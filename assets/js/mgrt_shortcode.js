(function() {
    "use strict";
    tinymce.PluginManager.add('mgrt_tinymce_buttons', function( editor, url ) {
        editor.addButton( 'mgrt_tinymce_buttons', {
            title: 'Newsletter',
            image: url + '/../images/shortcode-newsletter-icon.png',
            onclick: function() {
                console.log(url);
                editor.windowManager.open({
                    file : url + '/../tpl/wizard.php?base='+mgrt_wp_base,
                    width : 786 + parseInt(editor.getLang('example.delta_width', 0)), //jQuery(document).width() * 0.6, // 60%
                    height : 350 + parseInt(editor.getLang('example.delta_height', 0)),
                    inline : 1
                }, {
                    plugin_url : url
                });
            }
        });
    });
})();

