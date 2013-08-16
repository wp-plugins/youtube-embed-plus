    (function() {    
        tinymce.create('tinymce.plugins.Embedplusstats_youtubeprefs', {
            init : function(ed, url) {
                var plep = new Image();
                plep.src = url+'/btn_embedplusstats.png';
                ed.addButton('embedplusstats_youtubeprefs', {
                    title : 'How much are your visitors actually watching the videos you post? Click here to start using this popular feature from EmbedPlus Labs »',
                    onclick : function(ev) {
                        window.open('https://www.embedplus.com/dashboard/easy-video-analytics-seo.aspx', '_blank');
                    }
                });
                       
            },
            createControl : function(n, cm) {
                return null;
            },
            getInfo : function() {
                return {
                    longname : "Embedplus Video Analytics Dashboard",
                    author : 'EmbedPlus',
                    authorurl : 'http://www.embedplus.com/',
                    infourl : 'http://www.embedplus.com/',
                    version : "2.4"
                };
            }
        });
        tinymce.PluginManager.add('embedplusstats_youtubeprefs', tinymce.plugins.Embedplusstats_youtubeprefs);
    
    })();
