<?php
/*
  Plugin Name: YouTube
  Plugin URI: http://www.embedplus.com/dashboard/pro-easy-video-analytics.aspx
  Description: YouTube embed plugin with basic features and convenient defaults. Upgrade now to add tracking, instant video SEO tags, and much more!
  Version: 6.1
  Author: EmbedPlus Team
  Author URI: http://www.embedplus.com
 */

/*
  YouTube
  Copyright (C) 2013 EmbedPlus.com

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program. If not, see <http://www.gnu.org/licenses/>.

 */

//define('WP_DEBUG', true);

class YouTubePrefs
{

    public static $version = '6.1';
    public static $opt_version = 'version';
    public static $optembedwidth = null;
    public static $optembedheight = null;
    public static $defaultheight = null;
    public static $defaultwidth = null;
    public static $opt_center = 'centervid';
    public static $opt_autoplay = 'autoplay';
    public static $opt_cc_load_policy = 'cc_load_policy';
    public static $opt_iv_load_policy = 'iv_load_policy';
    public static $opt_loop = 'loop';
    public static $opt_modestbranding = 'modestbranding';
    public static $opt_rel = 'rel';
    public static $opt_showinfo = 'showinfo';
    public static $opt_controls = 'controls';
    public static $opt_theme = 'theme';
    public static $opt_wmode = 'wmode';
    public static $opt_vq = 'vq';
    public static $opt_html5 = 'html5';
    public static $opt_ssl = 'ssl';
    public static $opt_nocookie = 'nocookie';
    public static $opt_pro = 'pro';
    public static $opt_oldspacing = 'oldspacing';
    public static $opt_responsive = 'responsive';
    public static $opt_defaultdims = 'defaultdims';
    public static $opt_defaultwidth = 'width';
    public static $opt_defaultheight = 'height';
    public static $opt_schemaorg = 'schemaorg';
    public static $opt_alloptions = 'youtubeprefs_alloptions';
    public static $alloptions = null;
    public static $yt_options = array();
    //public static $epbase = 'http://localhost:2346';
    public static $epbase = '//www.embedplus.com';
    public static $double_plugin = false;
    public static $scriptsprinted = 0;
    /*
      color
      autohide
     */
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    public static $oldytregex = '@^\s*https?://(?:www\.)?(?:(?:youtube.com/watch\?)|(?:youtu.be/))([^\s"]+)\s*$@im';
    //public static $ytregex = '@^[\r\n]{0,1}[[:blank:]]*https?://(?:www\.)?(?:(?:youtube.com/watch\?)|(?:youtu.be/))([^\s"]+)[[:blank:]]*[\r\n]{0,1}$@im';
    public static $ytregex = '@^[\r\t ]*https?://(?:www\.)?(?:(?:youtube.com/watch\?)|(?:youtu.be/))([^\s"]+)[\r\t ]*$@im';

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        add_action('admin_init', array("YouTubePrefs", 'check_double_plugin_warning'));

        self::$alloptions = get_option(self::$opt_alloptions);
        if (self::$alloptions == false || version_compare(self::$alloptions[self::$opt_version], self::$version, '<'))
        {
            self::initoptions();
        }

        if (self::$alloptions[self::$opt_oldspacing] == 1)
        {
            self::$ytregex = self::$oldytregex;
        }

        self::$optembedwidth = intval(get_option('embed_size_w'));
        self::$optembedheight = intval(get_option('embed_size_h'));

        self::$yt_options = array(
            self::$opt_autoplay,
            self::$opt_cc_load_policy,
            self::$opt_iv_load_policy,
            self::$opt_loop,
            self::$opt_modestbranding,
            self::$opt_rel,
            self::$opt_showinfo,
            self::$opt_controls,
            self::$opt_html5,
            self::$opt_theme,
            self::$opt_wmode,
            self::$opt_vq,
            'list',
            'start',
            'end'
        );

        add_action('media_buttons', 'YouTubePrefs::media_button_wizard', 11);
        //$embedplusmce_wiz = new Add_new_tinymce_btn_Youtubeprefs('|', 'embedplus_youtubeprefs_wiz', plugins_url() . '/youtube-embed-plus/scripts/embedplus_mce_wiz.js');
        //$embedplusmce_prefs = new Add_new_tinymce_btn_Youtubeprefs('|', 'embedplus_youtubeprefs', plugins_url() . '/youtube-embed-plus/scripts/embedplus_mce_prefs.js');
        //$epstatsmce_youtubeprefs = new Add_new_tinymce_btn_Youtubeprefs('|', 'embedplusstats_youtubeprefs', plugins_url() . '/youtube-embed-plus/scripts/embedplusstats_mce.js');

        self::do_ytprefs();
        add_action('admin_menu', 'YouTubePrefs::ytprefs_plugin_menu');
        if (!is_admin())
        {
            add_action('wp_print_scripts', array('YouTubePrefs', 'jsvars'));
            add_action('wp_enqueue_scripts', array('YouTubePrefs', 'fitvids'));
        }
    }

    static function media_button_wizard()
    {
        add_thickbox();

        $wizhref = self::$epbase . '/wpembedcode-simple-search.aspx?pluginversion=' . YouTubePrefs::$version .
                '&wpversion=' . get_bloginfo('version') .
                '&settingsurl=' . urlencode(admin_url('admin.php?page=youtube-my-preferences#jumpdefaults')) .
                '&dashurl=' . urlencode(admin_url('admin.php?page=youtube-ep-analytics-dashboard')) .
                '&blogwidth=' . YouTubePrefs::get_blogwidth() .
                '&domain=' . urlencode(site_url()) .
                '&prokey=' . urlencode(YouTubePrefs::$alloptions[YouTubePrefs::$opt_pro]) .
                '&myytdefaults=' . urlencode(http_build_query(YouTubePrefs::$alloptions)) .
                '&random=' . rand(1, 1000) .
                '&TB_iframe=true&width=950&height=800';
        ?>
        <script type="text/javascript">
            function widen_ytprefs_wiz() {
                setTimeout(function(){
                    jQuery("#TB_window").animate({marginLeft: '-' + parseInt((950 / 2), 10) + 'px', width: '950px'}, 300);
                    jQuery("#TB_window iframe").animate({width: '950px'}, 300);
                }, 15);
            }
            jQuery(document).ready(function() {
                jQuery("#ytprefs_wiz_button").click(widen_ytprefs_wiz);
                jQuery(window).resize(widen_ytprefs_wiz);
            });
        </script>
        <a href="<?php echo $wizhref; ?>" class="thickbox button ytprefs_media_link" id="ytprefs_wiz_button" title="Visual YouTube Search Tool and Wizard - An easier embedding option"><span></span> YouTube</a>
        <?php
        //<?php if (version_compare(get_bloginfo('version'), '3.0', '<')) {echo '_old';}
    }

    static function check_double_plugin_warning()
    {
        if (is_plugin_active('embedplus-for-wordpress/embedplus.php'))
        {
            self::$double_plugin = true;
        }
    }

    static function double_plugin_warning()
    {
        ?>
        <style type="text/css">
            .embedpluswarning img
            {
                vertical-align: text-bottom;
            }
        </style>
        <div class="error">
            <p class="embedpluswarning">Seems like you have two different YouTube plugins by the EmbedPlus Team installed: <b><img src="<?php echo plugins_url('images/youtubeicon16.png', __FILE__) ?>" /> YouTube</b> and <b><img src="<?php echo plugins_url('images/btn_embedpluswiz.png', __FILE__) ?>" /> Advanced YouTube Embed.</b> We strongly suggest keeping only the one you prefer, so that they don't conflict with each other while trying to create your embeds.</p>
        </div>
        <?php
    }

    static function jsvars()
    {
        $responsiveselector = '["iframe.__youtube_prefs_widget__"]';
        if (self::$alloptions[self::$opt_responsive] == 1)
        {
            $responsiveselector = '["iframe[src*=\'youtube.com\']","iframe[src*=\'youtube-nocookie.com\']"]';
        }
        ?>
        <script type="text/javascript">
            var eppathtoscripts = "<?php echo plugins_url('scripts/', __FILE__); ?>";
            var epresponsiveselector = <?php echo $responsiveselector; ?>;
        </script>
        <?php
    }

    static function fitvids()
    {
        wp_enqueue_script('__ytprefsfitvids__', plugins_url('scripts/fitvids.min.js', __FILE__), false, false, true);
    }

    static function initoptions()
    {
        //vanilla defaults
        $_center = 0;
        $_autoplay = get_option('youtubeprefs_autoplay', 0);
        $_cc_load_policy = get_option('youtubeprefs_cc_load_policy', 0);
        $_iv_load_policy = get_option('youtubeprefs_iv_load_policy', 1);
        $_loop = get_option('youtubeprefs_loop', 0);
        $_modestbranding = get_option('youtubeprefs_modestbranding', 0);
        $_rel = get_option('youtubeprefs_rel', 1);
        $_showinfo = get_option('youtubeprefs_showinfo', 1);
        $_html5 = get_option('youtubeprefs_html5', 0);
        $_theme = get_option('youtubeprefs_theme', 'dark');
        $_vq = get_option('youtubeprefs_vq', '');
        $_pro = '';
        $_ssl = 0;
        $_nocookie = 0;
        $_controls = 2;
        $_oldspacing = 1;
        $_responsive = 0;
        $_schemaorg = 0;
        $_wmode = 'opaque';
        $_defaultdims = 0;
        $_defaultwidth = '';
        $_defaultheight = '';

        $arroptions = get_option(self::$opt_alloptions);

        //update vanilla to previous settings if exists
        if ($arroptions !== false)
        {
            $_center = self::tryget($arroptions, self::$opt_center, 0);
            $_autoplay = self::tryget($arroptions, self::$opt_autoplay, 0);
            $_cc_load_policy = self::tryget($arroptions, self::$opt_cc_load_policy, 0);
            $_iv_load_policy = self::tryget($arroptions, self::$opt_iv_load_policy, 1);
            $_loop = self::tryget($arroptions, self::$opt_loop, 0);
            $_modestbranding = self::tryget($arroptions, self::$opt_modestbranding, 0);
            $_rel = self::tryget($arroptions, self::$opt_rel, 1);
            $_showinfo = self::tryget($arroptions, self::$opt_showinfo, 1);
            $_html5 = self::tryget($arroptions, self::$opt_html5, 0);
            $_theme = self::tryget($arroptions, self::$opt_theme, 'dark');
            $_wmode = self::tryget($arroptions, self::$opt_wmode, 'opaque');
            $_vq = self::tryget($arroptions, self::$opt_vq, '');
            $_pro = self::tryget($arroptions, self::$opt_pro, '');
            $_ssl = self::tryget($arroptions, self::$opt_ssl, 0);
            $_nocookie = self::tryget($arroptions, self::$opt_nocookie, 0);
            $_controls = self::tryget($arroptions, self::$opt_controls, 2);
            $_oldspacing = self::tryget($arroptions, self::$opt_oldspacing, 1);
            $_responsive = self::tryget($arroptions, self::$opt_responsive, 0);
            $_schemaorg = self::tryget($arroptions, self::$opt_schemaorg, 0);
            $_defaultdims = self::tryget($arroptions, self::$opt_defaultdims, 0);
            $_defaultwidth = self::tryget($arroptions, self::$opt_defaultwidth, '');
            $_defaultheight = self::tryget($arroptions, self::$opt_defaultheight, '');
        }
        else
        {
            $_oldspacing = 0;
        }

        $all = array(
            self::$opt_version => self::$version,
            self::$opt_center => $_center,
            self::$opt_autoplay => $_autoplay,
            self::$opt_cc_load_policy => $_cc_load_policy,
            self::$opt_iv_load_policy => $_iv_load_policy,
            self::$opt_loop => $_loop,
            self::$opt_modestbranding => $_modestbranding,
            self::$opt_rel => $_rel,
            self::$opt_showinfo => $_showinfo,
            self::$opt_html5 => $_html5,
            self::$opt_theme => $_theme,
            self::$opt_wmode => $_wmode,
            self::$opt_vq => $_vq,
            self::$opt_pro => $_pro,
            self::$opt_ssl => $_ssl,
            self::$opt_nocookie => $_nocookie,
            self::$opt_controls => $_controls,
            self::$opt_oldspacing => $_oldspacing,
            self::$opt_responsive => $_responsive,
            self::$opt_schemaorg => $_schemaorg,
            self::$opt_defaultdims => $_defaultdims,
            self::$opt_defaultwidth => $_defaultwidth,
            self::$opt_defaultheight => $_defaultheight
        );

        update_option(self::$opt_alloptions, $all);
        update_option('embed_autourls', 1);
        self::$alloptions = get_option(self::$opt_alloptions);
    }

    public static function tryget($array, $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    public static function wp_above_version($ver)
    {
        global $wp_version;
        if (version_compare($wp_version, $ver, '>='))
        {
            return true;
        }
        return false;
    }

    public static function do_ytprefs()
    {
        if (!is_admin())
        {
            add_filter('the_content', 'YouTubePrefs::apply_prefs_content', 1);
            add_filter('widget_text', 'YouTubePrefs::apply_prefs_widget', 1);
        }
    }

    public static function apply_prefs_content($content)
    {
        $content = preg_replace_callback(self::$ytregex, "YouTubePrefs::get_html_content", $content);
        return $content;
    }

    public static function apply_prefs_widget($content)
    {
        $content = preg_replace_callback(self::$ytregex, "YouTubePrefs::get_html_widget", $content);
        return $content;
    }

    public static function get_html_content($m)
    {
        return self::get_html($m, true);
    }

    public static function get_html_widget($m)
    {
        return self::get_html($m, false);
    }

    public static function get_html($m, $iscontent)
    {
        $link = trim(preg_replace('/&amp;/i', '&', $m[0]));
        $link = preg_replace('/\s/', '', $link);
        $linkparamstemp = explode('?', $link);
        $linkparams = self::keyvalue($linkparamstemp[1], true);
        if (strpos($linkparamstemp[0], 'youtu.be') !== false && !$linkparams['v'])
        {
            $vtemp = explode('/', $linkparamstemp[0]);
            $linkparams['v'] = array_pop($vtemp);
        }

        self::init_dimensions($link, $linkparams);

        $linkscheme = 'http';
        $youtubebaseurl = 'youtube';
        $schemaorgoutput = '';

        $finalparams = $linkparams + self::$alloptions;

        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0)
        {
            if (self::$alloptions[self::$opt_ssl] == 1)
            {
                $linkscheme = 'https';
            }

            if (self::$alloptions[self::$opt_nocookie] == 1)
            {
                $youtubebaseurl = 'youtube-nocookie';
            }

            if (isset($finalparams[self::$opt_html5]) && $finalparams[self::$opt_html5] == 0)
            {
                unset($finalparams[self::$opt_html5]);
            }

            if (self::$alloptions[self::$opt_schemaorg] == 1)
            {
                $schemaorgoutput = self::getschemaorgoutput($finalparams['v']);
            }
        }
        else
        {
            if (isset($finalparams[self::$opt_html5]))
            {
                unset($finalparams[self::$opt_html5]);
            }
        }

        $centercode = '';
        if ($finalparams[self::$opt_center] == 1)
        {
            $centercode = ' style="display: block; margin: 0px auto;" ';
        }

        $code1 = $schemaorgoutput . '<iframe ' . $centercode . ' id="_ytid_' . rand(10000, 99999) . '" width="' . self::$defaultwidth . '" height="' . self::$defaultheight .
                '" src="' . $linkscheme . '://www.' . $youtubebaseurl . '.com/embed/' . $linkparams['v'] . '?';
        $code2 = '" frameborder="0" allowfullscreen type="text/html" class="__youtube_prefs__' . ($iscontent ? '' : ' __youtube_prefs_widget__') . '"></iframe>';

        $origin = '';

        try
        {
            if (!empty($_SERVER["HTTP_HOST"]))
            {
                $origin = 'origin=' .
                        ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://') . $_SERVER["HTTP_HOST"] . '&';
            }
        }
        catch (Exception $e)
        {
            
        }
        $finalsrc = 'enablejsapi=1&'; // . $origin;

        if (count($finalparams) > 1)
        {
            foreach ($finalparams as $key => $value)
            {
                if (in_array($key, self::$yt_options))
                {
                    $finalsrc .= htmlspecialchars($key) . '=' . htmlspecialchars($value) . '&';
                    if ($key == 'loop' && $value == 1 && !isset($finalparams['list']))
                    {
                        $finalsrc .= 'playlist=' . $finalparams['v'] . '&';
                    }
                }
            }
        }

        $code = $code1 . $finalsrc . $code2; //. '<!--' . $m[0] . '-->';
        // reset static vals for next embed
        self::$defaultheight = null;
        self::$defaultwidth = null;

        return $code;
    }

    public static function keyvalue($qry, $includev)
    {
        $ytvars = explode('&', $qry);
        $ytkvp = array();
        foreach ($ytvars as $k => $v)
        {
            $kvp = explode('=', $v);
            if (count($kvp) == 2 && ($includev || strtolower($kvp[0]) != 'v'))
            {
                $ytkvp[$kvp[0]] = $kvp[1];
            }
        }

        return $ytkvp;
    }

    public static function getschemaorgoutput($vidid)
    {
        $schemaorgcode = '';
        try
        {
            $ytapilink = 'https://gdata.youtube.com/feeds/api/videos/' . $vidid . '?v=2&alt=json&fields=id,published,title,content,media:group(media:description,yt:duration)';
            $apidata = wp_remote_get($ytapilink);
            if (!is_wp_error($apidata))
            {
                $raw = wp_remote_retrieve_body($apidata);
                if (!empty($raw))
                {
                    $json = json_decode($raw, true);
                    if (is_array($json))
                    {
                        $_name = esc_attr(sanitize_text_field($json['entry']['title']['$t']));
                        $_description = esc_attr(sanitize_text_field($json['entry']['media$group']['media$description']['$t']));
                        $_thumbnailUrl = esc_url("http://i.ytimg.com/vi/" . $vidid . "/0.jpg");
                        $_duration = self::formatDuration(self::secondsToDuration(intval($json['entry']['media$group']['yt$duration']['seconds'])));
                        $_uploadDate = sanitize_text_field($json['entry']['published']['$t']);

                        $schemaorgcode = '<div style="display:none;" itemprop="video" itemscope itemtype="http://schema.org/VideoObject">';
                        $schemaorgcode .= '<meta itemprop="embedURL" content="http://www.youtube.com/embed/' . $vidid . '">';
                        $schemaorgcode .= '<meta itemprop="name" content="' . $_name . '">';
                        $schemaorgcode .= '<meta itemprop="description" content="' . $_description . '">';
                        $schemaorgcode .= '<meta itemprop="thumbnailUrl" content="' . $_thumbnailUrl . '">';
                        $schemaorgcode .= '<meta itemprop="duration" content="' . $_duration . '">';
                        $schemaorgcode .= '<meta itemprop="uploadDate" content="' . $_uploadDate . '">';
                        $schemaorgcode .= '</div>';
                    }
                }
            }
        }
        catch (Exception $ex)
        {
            
        }
        return $schemaorgcode;
    }

    public static function secondsToDuration($seconds)
    {
        $remaining = $seconds;
        $parts = array();
        $multipliers = array(
            'hours' => 3600,
            'minutes' => 60,
            'seconds' => 1
        );

        foreach ($multipliers as $type => $m)
        {
            $parts[$type] = (int) ($remaining / $m);
            $remaining -= ($parts[$type] * $m);
        }

        return $parts;
    }

    public static function formatDuration($parts)
    {
        $default = array(
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0
        );

        extract(array_merge($default, $parts));

        return "T{$hours}H{$minutes}M{$seconds}S";
    }

    public static function init_dimensions($url, $urlkvp)
    {
        // get default dimensions; try embed size in settings, then try theme's content width, then just 480px
        if (self::$defaultwidth == null)
        {
            global $content_width;
            if (empty($content_width))
                $content_width = $GLOBALS['content_width'];

            self::$defaultwidth = $urlkvp['width'] ? $urlkvp['width'] : (self::$alloptions[self::$opt_defaultwidth] ? self::$alloptions[self::$opt_defaultwidth] : (self::$optembedwidth ? self::$optembedwidth : ($content_width ? $content_width : 480)));
            //self::$defaultheight = $urlkvp['height'] ? $urlkvp['height'] + 28 : self::get_aspect_height($url, $urlkvp);
            self::$defaultheight = $urlkvp['height'] ? $urlkvp['height'] : (self::$alloptions[self::$opt_defaultheight] ? self::$alloptions[self::$opt_defaultheight] : self::get_aspect_height($url, $urlkvp));
        }
    }

    public static function get_aspect_height($url, $urlkvp)
    {

        // attempt to get aspect ratio correct height from oEmbed
        $aspectheight = round((self::$defaultwidth * 9) / 16, 0);
        if ($url)
        {
            require_once( ABSPATH . WPINC . '/class-oembed.php' );
            $oembed = _wp_oembed_get_object();
            $args = array();
            $args['width'] = self::$defaultwidth;
            $args['height'] = self::$defaultwidth; //square to get biggest height from width // self::$optembedheight;
            $args['discover'] = false;
            $odata = $oembed->fetch('https://www.youtube.com/oembed', $url, $args);

            if ($odata)
            {
                $aspectheight = $odata->height;
            }
        }

        //add 28 for YouTube's own bar
        return $aspectheight + 28;
    }

    public static function ytprefs_plugin_menu()
    {
        //add_menu_page('YouTube Settings', 'YouTube', 'manage_options', 'youtube-my-preferences', 'YouTubePrefs::ytprefs_show_options', plugins_url('images/youtubeicon16.png', __FILE__), '10.00392854349');

        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0)
        {
            add_menu_page('YouTube Settings', 'YouTube PRO', 'manage_options', 'youtube-my-preferences', 'YouTubePrefs::ytprefs_show_options', plugins_url('images/youtubeicon16.png', __FILE__), '10.000392854349');
            add_menu_page('YouTube Analytics Dashboard', 'PRO Analytics', 'manage_options', 'youtube-ep-analytics-dashboard', 'YouTubePrefs::epstats_show_options', plugins_url('images/epstats16.png', __FILE__), '10.000492884349');
        }
        else
        {
            add_menu_page('YouTube Settings', 'YouTube Free', 'manage_options', 'youtube-my-preferences', 'YouTubePrefs::ytprefs_show_options', plugins_url('images/youtubeicon16.png', __FILE__), '10.000392854349');
            add_submenu_page('youtube-my-preferences', '', '', 'manage_options', 'youtube-my-preferences', 'YouTubePrefs::ytprefs_show_options');
            add_submenu_page('youtube-my-preferences', 'YouTube PRO', 'YouTube PRO', 'manage_options', 'youtube-ep-analytics-dashboard', 'YouTubePrefs::epstats_show_options');
        }
    }

    public static function epstats_show_options()
    {

        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if (self::$double_plugin)
        {
            //add_action('admin_notices', array("YouTubePrefs", "double_plugin_warning"));
            self::double_plugin_warning();
        }


        // Now display the settings editing screen
        ?>
        <div class="wrap">
            <style type="text/css">
                .wrap {font-family: Arial;}
                .epicon { width: 20px; height: 20px; vertical-align: middle; padding-right: 5px;}
                .epindent {padding-left: 25px;}
                iframe.shadow {-webkit-box-shadow: 0px 0px 20px 0px #000000; box-shadow: 0px 0px 20px 0px #000000;}
                .bold {font-weight: bold;}
                .orange {color: #f85d00;}
            </style>
            <br>
            <?php
            $thishost = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "");
            $thiskey = self::$alloptions[self::$opt_pro];
            if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0)
            {
                //// header
                echo "<h2>" . '<img src="' . plugins_url('images/epstats16.png', __FILE__) . '" /> ' . __('YouTube Analytics Dashboard') . "</h2>";
                echo '<p><i>Logging you in...</i></p>';
            }
            else
            {
                //// header
                echo "<h2>" . '<img src="' . plugins_url('images/epstats16.png', __FILE__) . '" /> ' . __('YouTube Plugin PRO') . "</h2><p class='bold orange'>This tab is here to provide direct access to analytics. Graphs and other data about your site will show below after you activate PRO.</p><br>";
            }
            ?>
            <iframe class="shadow" src="<?php echo self::$epbase ?>/dashboard/pro-easy-video-analytics.aspx?ref=protab&domain=<?php echo $thishost; ?>&prokey=<?php echo $thiskey; ?>" width="1060" height="2700" scrolling="auto"/>
        </div>
        <?php
    }

    public static function my_embedplus_pro_record()
    {
        $result = array();
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            $tmppro = preg_replace('/[^A-Za-z0-9-]/i', '', $_REQUEST[self::$opt_pro]);
            $new_options = array();
            $new_options[self::$opt_pro] = $tmppro;
            $all = get_option(self::$opt_alloptions);
            $all = $new_options + $all;
            update_option(self::$opt_alloptions, $all);

            if (strlen($tmppro) > 0)
            {
                $result['type'] = 'success';
            }
            else
            {
                $result['type'] = 'error';
            }
            echo json_encode($result);
        }
        else
        {
            $result['type'] = 'error';
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
        die();
    }

    public static function ytprefs_show_options()
    {

        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if (self::$double_plugin)
        {
            //add_action('admin_notices', array("YouTubePrefs", "double_plugin_warning"));
            self::double_plugin_warning();
        }


        // variables for the field and option names 
        $ytprefs_submitted = 'ytprefs_submitted';

        // Read in existing option values from database

        $all = get_option(self::$opt_alloptions);

        // See if the user has posted us some information
        // If they did, this hidden field will be set to 'Y'
        if (isset($_POST[$ytprefs_submitted]) && $_POST[$ytprefs_submitted] == 'Y')
        {
            // Read their posted values

            $new_options = array();
            $new_options[self::$opt_center] = $_POST[self::$opt_center] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_autoplay] = $_POST[self::$opt_autoplay] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_cc_load_policy] = $_POST[self::$opt_cc_load_policy] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_iv_load_policy] = $_POST[self::$opt_iv_load_policy] == (true || 'on') ? 1 : 3;
            $new_options[self::$opt_loop] = $_POST[self::$opt_loop] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_modestbranding] = $_POST[self::$opt_modestbranding] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_rel] = $_POST[self::$opt_rel] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_showinfo] = $_POST[self::$opt_showinfo] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_controls] = $_POST[self::$opt_controls] == (true || 'on') ? 2 : 0;
            $new_options[self::$opt_html5] = $_POST[self::$opt_html5] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_theme] = $_POST[self::$opt_theme] == (true || 'on') ? 'dark' : 'light';
            $new_options[self::$opt_wmode] = $_POST[self::$opt_wmode] == (true || 'on') ? 'opaque' : 'transparent';
            $new_options[self::$opt_vq] = $_POST[self::$opt_vq] == (true || 'on') ? 'hd720' : '';
            $new_options[self::$opt_ssl] = isset($_POST[self::$opt_ssl]) && $_POST[self::$opt_ssl] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_nocookie] = isset($_POST[self::$opt_nocookie]) && $_POST[self::$opt_nocookie] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_oldspacing] = isset($_POST[self::$opt_oldspacing]) && $_POST[self::$opt_oldspacing] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_responsive] = isset($_POST[self::$opt_responsive]) && $_POST[self::$opt_responsive] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_schemaorg] = isset($_POST[self::$opt_schemaorg]) && $_POST[self::$opt_schemaorg] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_defaultdims] = isset($_POST[self::$opt_defaultdims]) && $_POST[self::$opt_defaultdims] == (true || 'on') ? 1 : 0;

            $_defaultwidth = '';
            try
            {
                $_defaultwidth = is_numeric(trim($_POST[self::$opt_defaultwidth])) ? intval(trim($_POST[self::$opt_defaultwidth])) : $_defaultwidth;
            }
            catch (Exception $ex)
            {
                
            }
            $new_options[self::$opt_defaultwidth] = $_defaultwidth;

            $_defaultheight = '';
            try
            {
                $_defaultheight = is_numeric(trim($_POST[self::$opt_defaultheight])) ? intval(trim($_POST[self::$opt_defaultheight])) : $_defaultheight;
            }
            catch (Exception $ex)
            {
                
            }
            $new_options[self::$opt_defaultheight] = $_defaultheight;

            $all = $new_options + $all;

            // Save the posted value in the database

            update_option(self::$opt_alloptions, $all);
            // Put a settings updated message on the screen

            
            ?>
            <div class="updated"><p><strong><?php _e('Settings saved.'); ?></strong></p></div>
            <?php
        }

        $haspro = ($all[self::$opt_pro] && strlen(trim($all[self::$opt_pro])) > 0);
        // Now display the settings editing screen

        echo '<div class="wrap" style="max-width: 1000px;">';

        // header

        echo "<h2>" . '<img src="' . plugins_url('images/youtubeicon16.png', __FILE__) . '" /> ' . __('YouTube Settings') . "</h2>";

        // settings form
        ?>

        <style type="text/css">
            .wrap {font-family: Arial;}
            #ytform p { line-height: 20px; }
            #ytform ul li {margin-left: 30px; list-style: disc outside none;}
            .ytindent {padding: 0px 0px 0px 20px; font-size: 11px;}
            .ytindent ul, .ytindent p {font-size: 11px;}
            .shadow {-webkit-box-shadow: 0px 0px 20px 0px #000000; box-shadow: 0px 0px 20px 0px #000000;}
            .gopro {margin: 0px;}
            .gopro img {vertical-align: middle;
                        width: 19px;
                        height: 19px;
                        padding-bottom: 4px;}
            .gopro li {margin-bottom: 0px;}
            .orange {color: #f85d00;}
            .bold {font-weight: bold;}
            .grey{color: #888888;}
            #goprobox {border-radius: 15px; padding: 10px 15px 15px 15px; margin-top: 15px; border: 3px solid #CCE5EC; width: 825px; position: relative;}
            #salenote {position: absolute; right: 10px; top: 10px; width: 75px; height: 30px;}
            #nonprosupport {border-radius: 15px; padding: 5px 10px 10px 10px;  border: 3px solid #ff6655; width: 800px;}
            .pronon {font-weight: bold; color: #f85d00;}
            ul.reglist li {margin: 0px 0px 0px 30px; list-style: disc outside none;}
            .procol {width: 465px; float: left;}
            .smallnote {font-style: italic; font-size: 10px;}
            .italic {font-style: italic;}
            .ytindent h3 {font-size: 15px; line-height: 22px; margin: 5px 0px 10px 0px;}
            #wizleftlink {float: left; display: block; width: 240px; font-style: italic; text-align: center; text-decoration: none;}
            .button-primary {font-weight: bold; white-space: nowrap;}
            #opt_pro {box-shadow: 0px 0px 5px 0px #1870D5; width: 270px;vertical-align: top;}
            #goprobox h3 {font-size: 13px;}
            .chx p {margin: 0px 0px 5px 0px;}
            .cuz {background-image: linear-gradient(to bottom,#4983FF,#0C5597) !important; color: #ffffff;}
            #boxdefaultdims {font-weight: bold; padding: 0px 10px; <?php echo $all[self::$opt_defaultdims] ? '' : 'display: none;' ?>}
            .textinput {border-width: 2px !important;}
            h3.sect {border-radius: 10px; background-color: #D9E9F7; padding: 5px 5px 5px 10px;}
            h3.sect a {text-decoration: none; color: #E20000;}
            #ytnav {margin-bottom: 15px;}
            #ytnav a {font-weight: bold; display: inline-block; padding: 5px 10px; margin: 0px 20px 0px 0px; border: 1px solid #cccccc; border-radius: 6px;
                      text-decoration: none;}
            .jumper {height: 25px;}
        </style>

        <div class="ytindent">
            <br>
            <div id="jumphowto"></div>
            <div id="ytnav">
                <a href="#jumphowto">How To Embed</a>
                <a href="#jumpwiz">Visual YouTube Wizard</a>
                <a href="#jumpdefaults">Set Defaults</a>
                <a href="#jumpoverride">How To Override Defaults</a>
                <a href="#jumppro">Go PRO!</a>
                <a href="#jumpsupport">Support</a>
            </div>

            <form name="form1" method="post" action="" id="ytform">
                <input type="hidden" name="<?php echo $ytprefs_submitted; ?>" value="Y">

                <h3 class="sect">
                    <?php _e("How to Insert a YouTube Video or Playlist") ?> <!--<span class="pronon">(For Free and <a href="<?php echo self::$epbase ?>/dashboard/pro-easy-video-analytics.aspx" target="_blank">PRO Users &raquo;</a>)</span>-->
                </h3>
                <p>
                    Do you already have a URL to the video you want to embed? All you have to do is paste it on its own line, as shown below (including the http:// part). Easy, eh?<br>
                    For playlists: Go to the page for the playlist that lists all of its videos (<a target="_blank" href="http://www.youtube.com/playlist?list=PL70DEC2B0568B5469">Example &raquo;</a>). Click on the video that you want the playlist to start with. Copy and paste that browser URL into your blog on its own line.
                </p>
                <p>
                    Always follow these rules for any URL:
                </p>
                <ul class="reglist">
                    <li>Make sure the URL is really on its own line by itself</li>
                    <li>Make sure the URL is <strong>not</strong> an active hyperlink (i.e., it should just be plain text). Otherwise, highlight the URL and click the "unlink" button in your editor: <img src="<?php echo plugins_url('images/unlink.png', __FILE__) ?>"/></li>
                    <li>Make sure you did <strong>not</strong> format or align the URL in any way. If your URL still appears in your actual post instead of a video, highlight it and click the "remove formatting" button (formatting can be invisible sometimes): <img src="<?php echo plugins_url('images/erase.png', __FILE__) ?>"/></li>
                </ul>       
                <p>
                    <img style="width: 400px; height: auto;" class="shadow" src="<?php echo plugins_url('images/howto.png', __FILE__) ?>" />
                </p>

                <div class="jumper" id="jumpwiz"></div>
                <h3 class="sect">Visual YouTube Wizard</h3>

                <p>
                    Let's say you don't know the exact URL of the video you wish to embed.  Well, we've made the ability to directly search YouTube and insert videos right from your editor tab as a free feature to all users.  
                    Simply click the <img style="vertical-align: text-bottom;" src="<?php echo plugins_url('images/wizbuttonbig.png', __FILE__) ?>"> wizard button found above 
                    your editor to start the wizard (see image above to locate this button).  There, you'll be given the option to enter your search terms.  
                    Click the "Search" button to view the results.  Each result will have an <span class="button-primary cuz">&#9660; Insert Into Editor</span> button that 
                    you can click to directly embed the desired video link to your post without having to copy and paste it.             
                </p>
                <p>
                    The ability to read the latest Internet discussions about the videos you want to embed is now free to all users.
                </p>
                <p>
                    <b class="orange">Even more options are available to PRO users!</b> Simply click the <span class="button-primary cuz">&#9658; Customize</span> button on the wizard to further personalize your embeds without having to enter special codes yourself. No memorization needed!
                    <br>
                    <br>
                    <img src="<?php echo plugins_url('images/ssprowizard.jpg', __FILE__) ?>" >
                </p>
                <div class="jumper" id="jumpdefaults"></div>
                <h3 class="sect">
                    <?php _e("Default YouTube Options") ?> 
                </h3>
                <p>
                    <?php _e("Below you can set the default options for all your videos (click \"Save Changes\" when finished). However, you can override them (and more) on a per-video basis. Directions on how to do that are in the next section.") ?>
                </p>

                <div class="ytindent chx">
                    <p>
                        <input name="<?php echo self::$opt_oldspacing; ?>" id="<?php echo self::$opt_oldspacing; ?>" <?php checked($all[self::$opt_oldspacing], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_oldspacing; ?>">
                            Continue the spacing style from version 4.0 and older. Those versions required you to manually add spacing above and below your video. Unchecking this will automatically add the spacing for you.
                        </label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_center; ?>" id="<?php echo self::$opt_center; ?>" <?php checked($all[self::$opt_center], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_center; ?>"><?php _e('Automatically center all your videos (not necessary if all you\'re videos span the whole width of your blog)') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_autoplay; ?>" id="<?php echo self::$opt_autoplay; ?>" <?php checked($all[self::$opt_autoplay], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_autoplay; ?>"><?php _e('Automatically start playing your videos') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_cc_load_policy; ?>" id="<?php echo self::$opt_cc_load_policy; ?>" <?php checked($all[self::$opt_cc_load_policy], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_cc_load_policy; ?>"><?php _e('Turn on closed captions by default') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_iv_load_policy; ?>" id="<?php echo self::$opt_iv_load_policy; ?>" <?php checked($all[self::$opt_iv_load_policy], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_iv_load_policy; ?>"><?php _e('Show annotations by default') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_loop; ?>" id="<?php echo self::$opt_loop; ?>" <?php checked($all[self::$opt_loop], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_loop; ?>"><?php _e('Loop all your videos') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_modestbranding; ?>" id="<?php echo self::$opt_modestbranding; ?>" <?php checked($all[self::$opt_modestbranding], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_modestbranding; ?>"><?php _e('Modest branding - hide YouTube logo from control bar while playing') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_rel; ?>" id="<?php echo self::$opt_rel; ?>" <?php checked($all[self::$opt_rel], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_rel; ?>"><?php _e('Show related videos at the end') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_showinfo; ?>" id="<?php echo self::$opt_showinfo; ?>" <?php checked($all[self::$opt_showinfo], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_showinfo; ?>"><?php _e('Show the video title and other info') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_theme; ?>" id="<?php echo self::$opt_theme; ?>" <?php checked($all[self::$opt_theme], 'dark'); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_theme; ?>"><?php _e('Use the dark theme (uncheck to use light theme)') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_vq; ?>" id="<?php echo self::$opt_vq; ?>" <?php checked($all[self::$opt_vq], 'hd720'); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_vq; ?>"><?php _e('Force HD quality when available') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_controls; ?>" id="<?php echo self::$opt_controls; ?>" <?php checked($all[self::$opt_controls], 2); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_controls; ?>"><?php _e('Show player controls. Checking this also speeds up page loading (the Flash player will "lazy load," which means it will load the player after clicking play). Uncheck this to hide the player controls for a cleaner look.') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_wmode; ?>" id="<?php echo self::$opt_wmode; ?>" <?php checked($all[self::$opt_wmode], 'opaque'); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_wmode; ?>"><?php _e('Use "opaque" wmode (uncheck to use "transparent"). Opaque may have higher performance.') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_responsive; ?>" id="<?php echo self::$opt_responsive; ?>" <?php checked($all[self::$opt_responsive], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_responsive; ?>"><?php _e('Make my videos responsive so that they dynamically fit in all screen sizes. (smart phone, PC and tablet)') ?></label>
                    </p>

                    <p>
                        <input name="<?php echo self::$opt_defaultdims; ?>" id="<?php echo self::$opt_defaultdims; ?>" <?php checked($all[self::$opt_defaultdims], 1); ?> type="checkbox" class="checkbox">                        
                        <span id="boxdefaultdims">
                            Width: <input type="text" name="<?php echo self::$opt_defaultwidth; ?>" id="<?php echo self::$opt_defaultwidth; ?>" value="<?php echo trim($all[self::$opt_defaultwidth]); ?>" class="textinput" style="width: 50px;"> &nbsp;
                            Height: <input type="text" name="<?php echo self::$opt_defaultheight; ?>" id="<?php echo self::$opt_defaultheight; ?>" value="<?php echo trim($all[self::$opt_defaultheight]); ?>" class="textinput" style="width: 50px;">
                        </span>

                        <label for="<?php echo self::$opt_defaultdims; ?>"><?php _e('Make my videos have a default size (NOTE: Checking the responsive option will override this size setting) ') ?></label>
                    </p>

                    <p class="smallnote orange">Below are PRO features for enhanced SEO, performance, privacy, and security (works for even past embed links):</p>
                    <?php
                    if ($haspro)
                    {
                        ?>
                        <p>
                            <input name="<?php echo self::$opt_ssl; ?>" id="<?php echo self::$opt_ssl; ?>" <?php checked($all[self::$opt_ssl], 1); ?> type="checkbox" class="checkbox">
                            <label for="<?php echo self::$opt_ssl; ?>">
                                <b>(PRO)</b> Use the secure YouTube player for all of your visitors and videos you embed. This will go back and also secure your past embeds as they are loaded on their pages.
                            </label>
                        </p>
                        <p>
                            <input name="<?php echo self::$opt_nocookie; ?>" id="<?php echo self::$opt_nocookie; ?>" <?php checked($all[self::$opt_nocookie], 1); ?> type="checkbox" class="checkbox">
                            <label for="<?php echo self::$opt_nocookie; ?>">
                                <b>(PRO)</b> Prevent YouTube from leaving tracking cookies on your visitors browsers unless they actual play the videos. This is coded to apply this behavior on links in your past post as well.
                            </label>
                        </p>
                        <p>
                            <input name="<?php echo self::$opt_html5; ?>" id="<?php echo self::$opt_html5; ?>" <?php checked($all[self::$opt_html5], 1); ?> type="checkbox" class="checkbox">
                            <label for="<?php echo self::$opt_html5; ?>">
                                <b>(PRO)</b> Speed up your pages containing YouTube videos by using YouTube's HTML5 player instead of the Flash player when available.  It's been noted that using the HTML5 player offers visibly lower page load times than Flash.  Our own internal tests along with data from some beta testers suggest the same thing. In fact, some experiments show that pages (with multiple embeds) can have over four times less size with HTML5 than Flash. <br><span class="italic">So what does this all mean?</span>  Well given that site speed may have an effect on search engine rankings, we suggest checking this option if you typically embed videos in your posts as it may have a site-wide benefit. Our code will even go back and load your older posted videos as HTML5 instead of Flash, where possible.
                            </label>
                        </p>
                        <p>
                            <input name="<?php echo self::$opt_schemaorg; ?>" id="<?php echo self::$opt_schemaorg; ?>" <?php checked($all[self::$opt_schemaorg], 1); ?> type="checkbox" class="checkbox">
                            <label for="<?php echo self::$opt_schemaorg; ?>">
                                <b>(PRO)</b> Automatically add Google, Bing, and Yahoo friendly markup so that your pages with video embeds can be indexed to have a greater chance of showing up in search engine results for those particular videos, even if you aren't the owner. Just check the PRO setting and we'll handle the SEO.
                            </label>
                        </p>
                        <?php
                    }
                    else
                    {
                        ?>
                        <p>
                            <input disabled type="checkbox" class="checkbox">
                            <label>
                                <span class="pronon">(PRO Users)</span> Use the secure YouTube player for all of your visitors and videos you embed. This will go back and also secure your past embeds as they are loaded on their pages.
                            </label>
                        </p>
                        <p>
                            <input disabled type="checkbox" class="checkbox">
                            <label>
                                <span class="pronon">(PRO Users)</span> Prevent YouTube from leaving tracking cookies on your visitors browsers unless they actual play the videos. This is coded to apply this behavior on links in your past post as well.
                            </label>
                        </p>
                        <p>
                            <input disabled type="checkbox" class="checkbox">
                            <label>
                                <span class="pronon">(PRO Users)</span> Speed up your pages containing YouTube videos by using YouTube's HTML5 player instead of the Flash player when available.  It's been noted that using the HTML5 player offers visibly lower page load times than Flash.  Our own internal tests along with data from some beta testers suggest the same thing. <b>In fact, some experiments show that pages (with multiple embeds) can have over four times less size with HTML5 than Flash.</b> <br><span class="italic">So what does this all mean?</span>  Well given that site speed may have an effect on search engine rankings, we suggest checking this option if you typically embed videos in your posts. Our code will even go back and load your older posted videos as HTML5 instead of Flash, where possible.
                            </label>
                        </p>
                        <p>
                            <input disabled type="checkbox" class="checkbox">
                            <label>
                                <span class="pronon">(NEW: PRO Users)</span> Automatically add Google, Bing, and Yahoo friendly markup so that your pages with video embeds can be indexed to have a greater chance of showing up in search engine results for those particular videos, <b>even if you aren't the owner</b>. Just check the PRO setting and we'll handle the SEO.
                            </label>
                        </p>

                        <?php
                    }
                    ?>

                    <p class="submit">
                        <input type="submit" onclick="return savevalidate();" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                    </p>
                    <?php
                    if ($haspro)
                    {
                        ?>
                        <p>
                            <b>(PRO)</b> We'll soon be promoting this plugin right from our <a target="_blank" href="<?php echo self::$epbase; ?>">embedplus.com</a> homepage. For this, we'll be linking to examples of sites using this plugin. It will also give you a chance to promote your quality site.  <a id="showcase-validate" class="button-primary" target="_blank">Click this button</a> to automatically include your site for evaluation.  
                        </p>
                        <?php
                    }
                    else
                    {
                        ?>

                        <p>
                            <span class="pronon">(PRO Users)</span> We'll soon be promoting this plugin right from our <a target="_blank" href="<?php echo self::$epbase; ?>">embedplus.com</a> homepage. For this, we'll be linking to examples of sites using this plugin. It will also give you a chance to promote your quality site.   <a class="button-primary" disabled>Click this button</a> to automatically include your site for evaluation.
                        </p>
                    <?php } ?>
                </div>
                <div class="jumper" id="jumpoverride"></div>

                <h3 class="sect">
                    <?php _e("How To Override Defaults / Other Options") ?> 
                </h3>
                <p>Suppose you have a few videos that need to be different from the above defaults. You can add options to the end of a link as displayed below to override the above defaults. Each option should begin with '&'.
                    <br><span class="pronon">PRO users: You can use the <span class="button-primary cuz">&#9658; Customize</span> button in the wizard instead of memorizing the following.</span>
                    <?php
                    _e('<ul>');
                    _e("<li><strong>width</strong> - Sets the width of your player. If omitted, the default width will be the width of your theme's content.<em> Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&width=500</strong>&height=350</em></li>");
                    _e("<li><strong>height</strong> - Sets the height of your player. We do not recommend setting height because best-height will be calculated for you automatically, based on the above height. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA&width=500<strong>&height=350</strong></em> </li>");
                    _e("<li><strong>autoplay</strong> - Set this to 1 to autoplay the video (or 0 to play the video once). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&autoplay=1</strong></em> </li>");
                    _e("<li><strong>cc_load_policy</strong> - Set this to 1 to turn on closed captioning (or 0 to leave them off). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&cc_load_policy=1</strong></em> </li>");
                    _e("<li><strong>iv_load_policy</strong> - Set this to 3 to turn off annotations (or 1 to show them). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&iv_load_policy=3</strong></em> </li>");
                    _e("<li><strong>loop</strong> - Set this to 1 to loop the video (or 0 to not loop). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&loop=1</strong></em> </li>");
                    _e("<li><strong>modestbranding</strong> - Set this to 1 to remove the YouTube logo while playing (or 0 to show the logo). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&modestbranding=1</strong></em> </li>");
                    _e("<li><strong>rel</strong> - Set this to 0 to not show related videos at the end of playing (or 1 to show them). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&rel=0</strong></em> </li>");
                    _e("<li><strong>showinfo</strong> - Set this to 0 to hide the video title and other info (or 1 to show it). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&showinfo=0</strong></em> </li>");
                    _e("<li><strong>theme</strong> - Set this to 'light' to make the player have the light-colored theme (or 'dark' for the dark theme). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&theme=light</strong></em> </li>");
                    _e("<li><strong>vq</strong> - Set this to 'hd720' or 'hd1080' to force the video to have HD quality. Leave blank to let YouTube decide. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&vq=hd720</strong></em> </li>");
                    _e("<li><strong>controls</strong> - Set this to 0 to hide the video controls (or 2 to show it). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&controls=0</strong></em> </li>");
                    _e('</ul>');

                    _e("<p>You can also start and end each individual video at particular times. Like the above, each option should begin with '&'</p>");
                    _e('<ul>');
                    _e("<li><strong>start</strong> - Sets the time (in seconds) to start the video. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350<strong>&start=20</strong></em> </li>");
                    _e("<li><strong>end</strong> - Sets the time (in seconds) to stop the video. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350<strong>&end=100</strong></em> </li>");
                    _e('</ul>');
                    ?>

            </form>
            <div class="jumper" id="jumppro"></div>
            <div id="goprobox">
                <?php
                if ($haspro)
                {
                    echo "<h3>" . __('Thank you for going PRO.');
                    echo ' &nbsp;<input type="submit" name="showkey" class="button-primary" style="vertical-align: 15%;" id="showprokey" value="View my PRO key" />';
                    echo "</h3>";
                    ?>
                    <?php
                }
                else
                {
                    ?>

                    <h3 class="sect">
                        <a href="<?php echo self::$epbase ?>/dashboard/pro-easy-video-analytics.aspx" class="button-primary" target="_blank">Want to go PRO?</a> PRO users help to keep this plugin running and frequently updated. They get perks like:
                    </h3>
                    <div class="procol">
                        <ul class="gopro">
                            <li>
                                <img src="<?php echo plugins_url('images/iconwizard.png', __FILE__) ?>">
                                Full Visual Embedding Wizard (Easily customize embeds without memorizing codes)
                            </li>
                            <li>
                                <img src="<?php echo plugins_url('images/vseo.png', __FILE__) ?>">
                                One-Click Video SEO Tags (markup that can help drive more traffic)
                            </li>
                            <li>
                                <img src="<?php echo plugins_url('images/html5.png', __FILE__) ?>">
                                HTML5-first to speedup page loads (will even work for your old embeds)
                            </li>
                            <li>
                                <img src="<?php echo plugins_url('images/deletechecker.png', __FILE__) ?>">
                                Deleted Video Checker (alerts you if YouTube deletes videos you embedded)
                            </li>
                            <!--
                            <li>
                                <img src="<?php echo plugins_url('images/icondiscuss.png', __FILE__) ?>">
                                Read the latest Internet discussions about the videos you want to embed 
                            </li>                            
                            -->
                            <li>
                                <img src="<?php echo plugins_url('images/lock.png', __FILE__) ?>">
                                Secure YouTube player (will even work for your old embeds)
                            </li>
                        </ul>
                    </div>
                    <div class="procol" style="width: 340px;">
                        <ul class="gopro">
                            <li>
                                <img src="<?php echo plugins_url('images/prioritysupport.png', __FILE__) ?>">
                                Priority support (Puts your request in front)
                            </li>
                            <li>
                                <img src="<?php echo plugins_url('images/bulletgraph45.png', __FILE__) ?>">
                                User-friendly video analytics dashboard
                            </li>
                            <li>
                                <img src="<?php echo plugins_url('images/infinity.png', __FILE__) ?>">
                                Unlimited PRO upgrades and downloads
                            </li>
                            <li>
                                <img src="<?php echo plugins_url('images/showcase.png', __FILE__) ?>">
                                A chance to showcase your site right from our homepage
                            </li>

                            <li>
                                <img src="<?php echo plugins_url('images/questionsale.png', __FILE__) ?>">
                                What else? You tell us!                                
                            </li>                            
                        </ul>
                    </div>
                    <br>
                    <br>
                    <div style="clear: both;"></div>
                    <h3>Enter and save your PRO key (emailed to you):</h3>
                <?php } ?>
                <form name="form2" method="post" action="" id="epform2" class="submitpro" <?php if ($haspro) echo 'style="display: none;"' ?>>
                    <input type="hidden" name="<?php echo $pro_submitted; ?>" value="Y">

                    <input name="<?php echo self::$opt_pro; ?>" id="opt_pro" value="<?php echo $all[self::$opt_pro]; ?>" type="text">
                    <input type="submit" name="Submit" class="button-primary" id="prokeysubmit" value="<?php _e('Save Key') ?>" />
                    <?php
                    if (!$haspro)
                    {
                        ?>                    
                        &nbsp; &nbsp; &nbsp; <span style="font-size: 25px; color: #cccccc;">|</span> &nbsp; &nbsp; &nbsp; <a href="<?php echo self::$epbase ?>/dashboard/pro-easy-video-analytics.aspx" class="button-primary" target="_blank">Click here to go PRO &raquo;</a>
                        <?php
                    }
                    ?>
                    <br>
                    <span style="display: none;" id="prokeyloading" class="orange bold">Verifying...</span>
                    <span  class="orange bold" style="display: none;" id="prokeysuccess">Success! Please refresh this page.</span>
                    <span class="orange bold" style="display: none;" id="prokeyfailed">Sorry, that seems to be an invalid key.</span>

                </form>

            </div>
            <div class="smallnote">
                <!--&nbsp; *Upcoming: We've started developing a feature that will recommend YouTube embeds that you might want to include in a post while you're actually<br>
                writing/editing. It will apply some experimental artificial intelligence techniques on your post content for these recommendations, all at the click of a button.
                -->
            </div>
            <div class="jumper" id="jumpsupport"></div>
            <div id="nonprosupport">
                <h3>Support tips for non-PRO users</h3>
                We've found that a common support request has been from users that are pasting video links on single lines, as required, but are not seeing the video embed show up. One of these suggestions is usually the fix:
                <ul class="reglist">
                    <li>Make sure the URL is really on its own line by itself</li>
                    <li>Make sure the URL is not an active hyperlink (i.e., it should just be plain text). Otherwise, highlight the URL and click the "unlink" button in your editor: <img src="<?php echo plugins_url('images/unlink.png', __FILE__) ?>"/>.</li>
                    <li>Make sure you did <strong>not</strong> format or align the URL in any way. If your URL still appears in your actual post instead of a video, highlight it and click the "remove formatting" button (formatting can be invisible sometimes): <img src="<?php echo plugins_url('images/erase.png', __FILE__) ?>"/></li>
                    <li>Finally, there's a slight chance your custom theme is the issue, if you have one. To know for sure, we suggest temporarily switching to one of the default WordPress themes (e.g., "Twenty Thirteen") just to see if your video does appear. If it suddenly works, then your custom theme is the issue. You can switch back when done testing.</li>
                    <li>If none of the above work, you can contact us here if you still have issues: ext@embedplus.com. PRO users should use the priority form below instead.</li>                        
                </ul>                
                </p>
            </div>
            <br>
            <h3 class="sect">
                Priority Support <span class="pronon">(<a href="<?php echo self::$epbase ?>/dashboard/pro-easy-video-analytics.aspx" target="_blank">PRO Users &raquo;</a>)</span>
            </h3>
            <p>
                <strong>PRO users:</strong> Below, We've enabled the ability to have priority support with our team.  Use this to get one-on-one help with any issues you might have or to send us suggestions for future features.  We typically respond within minutes during normal work hours.  
            </p>


            <iframe src="<?php echo self::$epbase ?>/dashboard/prosupport.aspx?simple=1&prokey=<?php echo $all[self::$opt_pro]; ?>&domain=<?php echo site_url(); ?>" width="500" height="500"></iframe>

            <p class="bold">Keep us going!
                <br>
                <iframe src="<?php echo self::$epbase ?>/wplikeus.aspx" width="350" height="100"></iframe>
            </p>
            <!--
            <h3 class="orange">What's next for us? Take this survey.</h3>
            <div id="surveyMonkeyInfo" style="width:700px;font-size:10px;color:#666;border:1px solid #ccc;padding:4px;"><div><iframe id="sm_e_s" src="http://www.surveymonkey.com/jsEmbed.aspx?sm=uYXvJKm2UNLkrpXHzLJ57Q_3d_3d" width="700" height="800" style="border:0px;padding-bottom:4px;" frameborder="0" allowtransparency="true" ></iframe></div></div>
            -->

            <script type="text/javascript">
                                                                                                                                                                
                function savevalidate()
                {
                    var valid = true;
                                                                                                                                                                    
                    if (jQuery("#<?php echo self::$opt_defaultdims; ?>").is(":checked"))
                    {
                        if (!(jQuery.isNumeric(jQuery.trim(jQuery("#<?php echo self::$opt_defaultwidth; ?>").val())) && 
                            jQuery.isNumeric(jQuery.trim(jQuery("#<?php echo self::$opt_defaultheight; ?>").val()))))
                        {
                            alert("Please enter valid numbers for default height and width, or uncheck the option.");
                            jQuery("#boxdefaultdims input").css("background-color", "#ffcccc").css("border", "2px solid #000000");
                            valid = false;
                        }
                    }
                                                                                                                                                                    
                                                                                                                                                                    
                    return valid;
                }
                                                                                                                                                                
                var prokeyval;
                var mydomain = escape("http://" + window.location.host.toString());

                jQuery(document).ready(function($) {
                    jQuery('#<?php echo self::$opt_defaultdims; ?>').change(function()
                    {
                        if(jQuery(this).is(":checked"))
                        {
                            jQuery("#boxdefaultdims").show(500);
                        }
                        else
                        {
                            jQuery("#boxdefaultdims").hide(500);
                        }
                                                                                                                                                                                                                
                    });
                                                                                                                                                                                                                                                                                                                                                                                                        
                    jQuery("#showcase-validate").click(function() {
                        window.open("<?php echo self::$epbase . "/showcase-validate.aspx?prokey=" . self::$alloptions[self::$opt_pro] ?>" + "&domain=" + mydomain);
                    });
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                    jQuery('#showprokey').click(function(){
                        jQuery('.submitpro').show(500);
                        return false;
                    });
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     
                    jQuery('#prokeysubmit').click(function(){
                        jQuery(this).attr('disabled', 'disabled');
                        jQuery('#prokeyfailed').hide();
                        jQuery('#prokeysuccess').hide();
                        jQuery('#prokeyloading').show();
                        prokeyval = jQuery('#opt_pro').val();
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
                        var tempscript=document.createElement("script");
                        tempscript.src="//www.embedplus.com/dashboard/wordpress-pro-validatejp.aspx?simple=1&prokey=" + prokeyval + "&domain=" + mydomain;
                        var n=document.getElementsByTagName("head")[0].appendChild(tempscript);
                        setTimeout(function(){
                            n.parentNode.removeChild(n)
                        },500);
                        return false;
                    });
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                    window.embedplus_record_prokey = function(good){
                                                                                                                                                                                                
                        var wpajaxurl = "<?php echo admin_url('admin-ajax.php') ?>";
                        if (window.location.toString().indexOf('https://') == 0)
                        {
                            wpajaxurl = wpajaxurl.replace("http://", "https://");
                        }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
                        jQuery.ajax({
                            type : "post",
                            dataType : "json",
                            timeout: 30000,
                            url : wpajaxurl,
                            data : { action: 'my_embedplus_pro_record', <?php echo self::$opt_pro; ?>:  (good? prokeyval : "")},
                            success: function(response) {
                                if(response.type == "success") {
                                    jQuery("#prokeysuccess").show();
                                }
                                else {
                                    jQuery("#prokeyfailed").show();
                                }
                            },
                            error: function(xhr, ajaxOptions, thrownError){
                                jQuery('#prokeyfailed').show();
                            },
                            complete: function() {
                                jQuery('#prokeyloading').hide();
                                jQuery('#prokeysubmit').removeAttr('disabled');
                            }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                        });
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
                    };
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                });
            </script>
            <?php
            if (function_exists('add_thickbox'))
            {
                add_thickbox();
            }
            ?>

            <?php
        }

        public static function ytprefsscript()
        {
            wp_enqueue_script('__ytprefs__', plugins_url('scripts/ytprefs.min.js', __FILE__));
        }

        public static function get_blogwidth()
        {
            $blogwidth = null;
            try
            {
                $embed_size_w = intval(get_option('embed_size_w'));

                global $content_width;
                if (empty($content_width))
                    $content_width = $GLOBALS['content_width'];
                if (empty($content_width))
                    $content_width = $_GLOBALS['content_width'];

                $blogwidth = $embed_size_w ? $embed_size_w : ($content_width ? $content_width : 450);
            }
            catch (Exception $ex)
            {
                
            }

            return $blogwidth;
        }

    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//class start
    class Add_new_tinymce_btn_Youtubeprefs
    {

        public $btn_arr;
        public $js_file;

        /*
         * call the constructor and set class variables
         * From the constructor call the functions via wordpress action/filter
         */

        function __construct($seperator, $btn_name, $javascrip_location)
        {
            $this->btn_arr = array("Seperator" => $seperator, "Name" => $btn_name);
            $this->js_file = $javascrip_location;
            add_action('init', array($this, 'add_tinymce_button'));
            add_filter('tiny_mce_version', array($this, 'refresh_mce_version'));
        }

        /*
         * create the buttons only if the user has editing privs.
         * If so we create the button and add it to the tinymce button array
         */

        function add_tinymce_button()
        {
            if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
                return;
            if (get_user_option('rich_editing') == 'true')
            {
                //the function that adds the javascript
                add_filter('mce_external_plugins', array($this, 'add_new_tinymce_plugin'));
                //adds the button to the tinymce button array
                add_filter('mce_buttons', array($this, 'register_new_button'));
            }
        }

        /*
         * add the new button to the tinymce array
         */

        function register_new_button($buttons)
        {
            array_push($buttons, $this->btn_arr["Seperator"], $this->btn_arr["Name"]);
            return $buttons;
        }

        /*
         * Call the javascript file that loads the
         * instructions for the new button
         */

        function add_new_tinymce_plugin($plugin_array)
        {
            $plugin_array[$this->btn_arr['Name']] = $this->js_file;
            return $plugin_array;
        }

        /*
         * This function tricks tinymce in thinking
         * it needs to refresh the buttons
         */

        function refresh_mce_version($ver)
        {
            $ver += 3;
            return $ver;
        }

    }

//class end


    register_activation_hook(__FILE__, array('YouTubePrefs', 'initoptions'));
    add_action('wp_enqueue_scripts', array('YouTubePrefs', 'ytprefsscript'));
    add_action("wp_ajax_my_embedplus_pro_record", array('YouTubePrefs', 'my_embedplus_pro_record'));


    $youtubeplg = new YouTubePrefs();

    add_action('admin_enqueue_scripts', 'youtubeprefs_admin_enqueue_scripts');

    function youtubeprefs_admin_enqueue_scripts()
    {
        wp_enqueue_style('embedplusyoutube', plugins_url() . '/youtube-embed-plus/scripts/embedplus_mce.css');
        add_action('wp_print_scripts', 'youtubeprefs_output_scriptvars');
    }

    function youtubeprefs_output_scriptvars()
    {
        YouTubePrefs::$scriptsprinted++;
        if (YouTubePrefs::$scriptsprinted == 1)
        {
            $blogwidth = YouTubePrefs::get_blogwidth();
            $epprokey = YouTubePrefs::$alloptions[YouTubePrefs::$opt_pro];
            $myytdefaults = http_build_query(YouTubePrefs::$alloptions);
            ?>
            <script type="text/javascript">            
                var epblogwidth = <?php echo $blogwidth; ?>;
                var epprokey = '<?php echo $epprokey; ?>';
                var epbasesite = '<?php echo YouTubePrefs::$epbase; ?>';
                var epversion = '<?php echo YouTubePrefs::$version; ?>';
                var myytdefaults = '<?php echo $myytdefaults; ?>';
                var eppluginadminurl = '<?php echo admin_url('admin.php?page=youtube-my-preferences'); ?>';

                // Create IE + others compatible event handler
                var epeventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
                var epeventer = window[epeventMethod];
                var epmessageEvent = epeventMethod == "attachEvent" ? "onmessage" : "message";

                // Listen to message from child window
                epeventer(epmessageEvent,function(e)
                {
                    var embedcode = "";
                    try
                    {
                        if (e.data.indexOf("youtubeembedplus") == 0)
                        {
                            embedcode = "<p>" + e.data.split("|")[1] + "</p>";
                                                                                                                                                                                                                                                                        
                            //                        window.tinyMCE.execInstanceCommand(
                            //                        window.tinyMCE.activeEditor.id,
                            //                        'restoreSelection',
                            //                        false,
                            //                        null);
                                                                                                                                                                                                                                                                        
                            window.tinyMCE.execInstanceCommand(
                            window.tinyMCE.activeEditor.id,
                            'mceInsertContent',
                            false,
                            embedcode);

                            tb_remove();

                            //                            var $mceclose = jQuery(".mceClose");                                                                                                                               
                            //                            $mceclose.get(0).click();
                            //                            //wintoclose = window.tinyMCE.activeEditor.windowManager;
                            //                            jQuery(".mceClose, #mceModalBlocker").mousedown().click();
                        }
                    }
                    catch (err)
                    {
                        if (typeof console != 'undefined') console.log(err.message);
                    }
                                                                                                                                                                                                                                                                                                    
                                                                                                                                                                                                                                                                                
                },false);
                                                                                                                                                                                                                                                                                                        
            </script>
            <?php
        }
    }

    