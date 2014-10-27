jQuery(function(){
    jQuery('.toggle-ctrl').change(function(e) {
        var $this = jQuery(this);
        toggleStateRecurse(jQuery($this.data('toggle')), $this.is(':checked'), $this.data('hide'));
        if ($this.data('other') != null) {
            toggleStateRecurse(jQuery($this.data('other')), !$this.is(':checked'), $this.data('hide'));
        }
    });

    function toggleStateRecurse($targets, state, hide) {
        if ('undefined'==typeof hide) {
            hide = false;
        }
        if(hide) {
            setVisible($targets, state);
        }
        $targets.each(function() {
            var $target = jQuery(this);
            if ($target.is('a')) {
                if (state) {
                    $target.removeClass('disabled').attr('href', $target.data('href'));
                }else{
                    $target.addClass('disabled').removeAttr('href');
                }
            }else if ($target.is('fieldset')) {
                $target.attr('disabled', !state);
                toggleStateRecurse(jQuery('input, select, button', $target), state, hide);
                toggleStateRecurse(jQuery('a', $target), state, hide);
            }else{
                $target.attr('disabled', !state);
            }
        });
    }

    function setVisible($el, state) {
        if(state) {
            $el.show();
        } else {
            $el.hide();
        }
    }

    var is_syncing = false;
    jQuery('#sync_start_go').click(function (e) {
        e.preventDefault();
        is_syncing = true;
        jQuery('#first-pass .progressbar').addClass('is-animated');
        jQuery('#sync_start').slideUp(animations_duration/2); jQuery('#sync_counter').slideDown(animations_duration/2);
        var sync_priority = jQuery('#sync_priority input:checked').val();

        syncStep(function(counter) {
            console.log('end@'+counter);
            is_syncing = false;
            setTimeout(function () {
                jQuery('#done_counter_count').text(counter);
                jQuery('#sync_counter').slideUp(animations_duration/2); jQuery('#sync_done').slideDown(animations_duration/2);
            }, animations_duration);
        }, {priority: sync_priority});
    });

    jQuery(window).bind('beforeunload', function() {
        if (is_syncing) {
            return _end_sync_trans;
        }
    })

    var animations_duration = 1000;
    var total_users = 0;
    var current_users = 0;
    var times = [];
    var last_response = null;

    function moy(data) {
        if (data.length == 0) {
            return 0;
        }
        var count=0;
        for (var i=data.length; i--;) {
            count+=data[i];
        }
        return count / data.length;
    }

    function syncStep(cb, last_recall) {
        if ('undefined'===typeof last_recall) {
            last_recall = {};
        }

        var xhr = jQuery.post(ajaxurl, jQuery.extend({action: 'mgrt_force_sync'}, last_recall), function (r) {
            try{
                r = JSON.parse(r);
            }catch(e){
                logFailure(xhr, String(e));
                return;
            }
            last_response = r;

            times = times.concat(r.times);
            console.log(moy(times) + 's per contact');

            current_users += r.count;
            prettyCounter(jQuery('#'+(r.last_recall.sequence == 0 ? 'first' : 'second')+'-pass span'), current_users);

            if (r.next_recall.mode != r.last_recall.mode && r['continue']) {
                total_users = current_users;
                current_users = 0;
                times = [];
                jQuery('#sync_counter .counter').toggleClass('is-disabled');
                jQuery('#sync_counter .counter .progressbar').toggleClass('is-animated');
                console.log('Next step');
            }

            if (r['continue']) {
                syncStep(cb, r.next_recall);
            }else{
                if ('function'===typeof cb) {
                    cb(current_users+total_users);
                }
            }
        }).fail(logFailure);
    }

    function logFailure(e, msg) {
        alert(_error_sync_trans);

        if (typeof msg == 'undefined') {
            msg = 'Request failed';
        }

        if (e == null) {
            e = {
                status: 'NaN',
                responseText: 'Parsing error'
            }
        }

        msg += '\n----\n';
        if (last_response != null) {
            msg += 'last_recall\n';
            msg += 'sequence: ' + (typeof last_response.last_recall.sequence == 'undefined' ? 'undefined' : last_response.last_recall.sequence) + '\n';
            msg += 'mode: ' + (typeof last_response.last_recall.mode == 'undefined' ? 'undefined' : last_response.last_recall.mode) + '\n';
            msg += 'next_recall\n';
            msg += 'sequence: ' + (typeof last_response.next_recall.sequence == 'undefined' ? 'undefined' : last_response.next_recall.sequence) + '\n';
            msg += 'mode: ' + (typeof last_response.next_recall.mode == 'undefined' ? 'undefined' : last_response.next_recall.mode) + '\n';
        }

        msg += e.status + ' : ' + e.responseText;
        jQuery('.sync-error .sync-error-detail').text(msg);
        jQuery('.sync-error').slideDown();
        jQuery('.sync-wizard').slideUp();

        is_syncing = false;
    }

    function prettyCounter($el, to) {
        if('undefined'!==typeof this.ani)
            this.ani.stop();
        var from = parseInt($el.text());
        this.ani = jQuery({counter: from}).animate({counter: to}, {
            duration: animations_duration,
            easing:'linear',
            step: function() {
                $el.text(Math.ceil(this.counter));
            }
        });
    }
})

var uuid = function() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
        return v.toString(16);
    });
};

var rand5 = function() {
    return (Math.random()+1).toString(36).substr(2, 5);
}
