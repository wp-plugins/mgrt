var MgrtWizard = {
    init: function(e) {
        console.log(tinyMCEPopup);
        setTimeout(function() {
            tinyMCEPopup.resizeToInnerSize();
        }, 1000);
    },
    insert: function (code, text, options) {
        console.log(code, options);
        var output = '[';
        output += code + ' id="';
        output += rand5() + '"';
        for (var opt in options) {
            var value = options[opt];
            if(typeof options[opt] === 'object') {
                value = value.join();
            }
            if(value == '' || typeof value == 'undefined') {
                continue;
            }
            output += ' '+ opt + '="' + this.cleanup(value) + '"';
        }
        output += ']';

        if (text != '') {
            output += text
        }
        output += '[/' + code + ']';

        console.log(output);
        tinyMCEPopup.execCommand('mceReplaceContent', false, output);

        tinyMCEPopup.close();
    },
    cleanup: function (code) {
        return code
            .replace(/"/g, '&quot;');
    }
}
tinyMCEPopup.onInit.add(MgrtWizard.init, MgrtWizard);

jQuery(function() {
    jQuery('#cancel-all').click(function (e){
        e.preventDefault();
        tinyMCEPopup.close();
    });
    jQuery('#submit-all').click(function (e){
        e.preventDefault();

        var options = {};
        var code = jQuery('#selected-code input[type="radio"]:checked').first().val();

        // fast html encode
        var text = jQuery('<div/>').text(jQuery('#shortcode-text').val()).html();
        if (code == 'newsletter') {
            options.targets = [];
            jQuery('#selected-lists input[type="checkbox"]:checked').each(function () {
                options.targets.push(this.value);
            });
            options.fields = [];
            jQuery('#selected-fields input[type="checkbox"]:checked').each(function () {
                options.fields.push(this.value);
            });
        } else if (code == 'campaign') {
            options.count = jQuery('#campaign-count').val();
            if (options.count < 1 || options.count > 15) {
                options.count = 5;
            }
        } else {
            code = '';
        }
        if (code != '') {
            MgrtWizard.insert(code, text, options);
        }
    });
})
