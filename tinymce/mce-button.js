(function() {
    tinymce.create('tinymce.plugins.SPIG', {
        init : function(ed, url) {
			ed.addButton('mybutton', {
                title : 'SPIGallery',
                cmd : 'mybutton',
                image : url + '/mce.png'
            });
			ed.addCommand('mybutton', function() {
                var return_text = '[SPIGallery]';
                ed.execCommand('mceInsertContent', 0, return_text);
            });
        },
    });
 
    // Register plugin
    tinymce.PluginManager.add( 'SPIG', tinymce.plugins.SPIG );
})();