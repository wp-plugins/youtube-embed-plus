<?php
$installdir = explode("wp-content", __FILE__);
$installdir = $installdir[0];

require( $installdir . 'wp-load.php' );
?>

<?php if (false)
{
    ?>


    <script type="text/javascript">
<?php } ?>

    (function() {    
        tinymce.create('tinymce.plugins.Embedplusstats_youtubeprefs', {
            init : function(ed, url) {
                var plep = new Image();
                plep.src = url+'/btn_embedplusstats.png';
                ed.addButton('embedplusstats_youtubeprefs', {
                    title : 'What happens after you embed a YouTube video? Click here to start using this popular feature from EmbedPlus Labs »',
                    onclick : function(ev) {
                        window.open('http://www.embedplus.com/dashboard/wordpress-video-analytics-seo.aspx', '_blank');
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
                    version : "2.1"
                };
            }
        });
        tinymce.PluginManager.add('embedplusstats_youtubeprefs', tinymce.plugins.Embedplusstats_youtubeprefs);
    
    })();

<?php if (false)
{
    ?>
    </script>
<?php } ?>