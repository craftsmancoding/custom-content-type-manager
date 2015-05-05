(function() {

    tinymce.create('tinymce.plugins.custom_fields', {

        init : function(ed, url){
            ed.addButton('custom_fields', {
                title : 'Custom Fields',
                onclick : function() {
                    ed.execCommand(
                        'mceInsertContent',
                        false,
                        show_custom_fields() // <-- you must create this JS function!
                        );
                },
                image: url + "/../../images/wrench.png"
            });
        },

        getInfo : function() {
            return {
                longname : 'Custom Fields',
                author : 'Everett Griffiths',
                authorurl : 'http://craftsmancoding.com',
                infourl : '',
                version : "0.1"
            };
        }
    });

    tinymce.PluginManager.add('custom_fields', tinymce.plugins.custom_fields);
    
})();