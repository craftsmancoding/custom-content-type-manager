(function() {

    tinymce.create('tinymce.plugins.summarize_posts', {

        init : function(ed, url){
            ed.addButton('summarize_posts', {
                title : 'Summarize Posts',
                onclick : function() {
                    ed.execCommand(
                        'mceInsertContent',
                        false,
                        show_summarize_posts() // <-- you must create this JS function!
                        );
                },
                image: url + "/../images/summarize_posts_icon.png"
            });
        },

        getInfo : function() {
            return {
                longname : 'Summarize Posts',
                author : 'Everett Griffiths',
                authorurl : 'http://craftsmancoding.com',
                infourl : '',
                version : "0.8"
            };
        }
    });

    tinymce.PluginManager.add('summarize_posts', tinymce.plugins.summarize_posts);
    
})();