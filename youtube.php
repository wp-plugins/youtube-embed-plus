<?php
/*
  Plugin Name: YouTube Embed
  Plugin URI: http://www.embedplus.com
  Description: YouTube embed plugin with basic features and convenient defaults. Upgrade now to add view tracking and access to your very own analytics dashboard.
  Version: 2.0
  Author: EmbedPlus Team
  Author URI: http://www.embedplus.com
 */

/*
  YouTube Embed
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
    public static $optembedwidth = null;
    public static $optembedheight = null;
    public static $defaultheight = null;
    public static $defaultwidth = null;
    public static $opt_center = 'centervid';
    public static $opt_auto_hd = 'auto_hd';
    public static $opt_autoplay = 'autoplay';
    public static $opt_cc_load_policy = 'cc_load_policy';
    public static $opt_iv_load_policy = 'iv_load_policy';
    public static $opt_loop = 'loop';
    public static $opt_modestbranding = 'modestbranding';
    public static $opt_rel = 'rel';
    public static $opt_showinfo = 'showinfo';
    public static $opt_theme = 'theme';
    public static $opt_alloptions = 'youtubeprefs_alloptions';
    public static $alloptions = null;
    public static $yt_options = array();

    /*
      color
      controls
      disablekb
      enablejsapi
      list
      listType
      playlist
     * 
     */
    public static $ytregex = '@^\s*https?://(?:www\.)?(?:youtube.com/watch\?|youtu.be/)([^\s"]+)\s*$@im';

    public function __construct()
    {

        $do_autoembeds = get_option('embed_autourls');
        if ($do_autoembeds == 0)
        {
            update_option('embed_autourls', 1);
        }

        self::$yt_options = array(
            self::$opt_autoplay,
            self::$opt_cc_load_policy,
            self::$opt_iv_load_policy,
            self::$opt_loop,
            self::$opt_modestbranding,
            self::$opt_rel,
            self::$opt_showinfo,
            self::$opt_theme
        );

        self::do_ytprefs();

        if (self::wp_above_version('2.9'))
        {
            add_action('admin_menu', 'YouTubePrefs::ytprefs_plugin_menu');
        }
//        if (!is_admin())
//        {
//            // allow shortcode in widgets
//            //add_filter('widget_text', 'do_shortcode', 11);
//        }
    }

    static function initoptions()
    {
        if (self::wp_above_version('2.9'))
        {
            if (get_option(self::$opt_alloptions) === false)
            {
                $_auto_hd = get_option('youtubeprefs_auto_hd', 0);
                $_autoplay = get_option('youtubeprefs_autoplay', 0);
                $_cc_load_policy = get_option('youtubeprefs_cc_load_policy', 0);
                $_iv_load_policy = get_option('youtubeprefs_iv_load_policy', 1);
                $_loop = get_option('youtubeprefs_loop', 0);
                $_modestbranding = get_option('youtubeprefs_modestbranding', 0);
                $_rel = get_option('youtubeprefs_rel', 1);
                $_showinfo = get_option('youtubeprefs_showinfo', 1);
                $_theme = get_option('youtubeprefs_theme', 'dark');

                $all = array(
                    self::$opt_center => 0,
                    self::$opt_auto_hd => $_auto_hd,
                    self::$opt_autoplay => $_autoplay,
                    self::$opt_cc_load_policy => $_cc_load_policy,
                    self::$opt_iv_load_policy => $_iv_load_policy,
                    self::$opt_loop => $_loop,
                    self::$opt_modestbranding => $_modestbranding,
                    self::$opt_rel => $_rel,
                    self::$opt_showinfo => $_showinfo,
                    self::$opt_theme => $_theme
                );

                add_option(self::$opt_alloptions, $all);

//                add_option(self::$opt_auto_hd, 0);
//                add_option(self::$opt_autoplay, 0);
//                add_option(self::$opt_cc_load_policy, 0);
//                add_option(self::$opt_iv_load_policy, 1);
//                add_option(self::$opt_loop, 0);
//                add_option(self::$opt_modestbranding, 0);
//                add_option(self::$opt_rel, 1);
//                add_option(self::$opt_showinfo, 1);
//                add_option(self::$opt_theme, 'dark');

                update_option('embed_autourls', 1);
            }
        }
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
        if (self::wp_above_version('2.9') && !is_admin())
        {
            add_filter('the_content', 'YouTubePrefs::apply_prefs', 1);
        }
    }

    public static function apply_prefs($content)
    {
        self::$optembedwidth = intval(get_option('embed_size_w'));
        self::$optembedheight = intval(get_option('embed_size_h'));
        self::$alloptions = get_option(self::$opt_alloptions);

        $content = preg_replace_callback(self::$ytregex, "YouTubePrefs::get_html", $content);
        return $content;
    }

    public static function get_html($m)
    {
        $link = trim(preg_replace('/&amp;/i', '&', $m[0]));
        $link = preg_replace('/\s/', '', $link);
        $linkparams = explode('?', $link);
        $linkparams = self::keyvalue($linkparams[1], true);
        self::init_dimensions($link, $linkparams);

        $linkscheme = parse_url($link, PHP_URL_SCHEME);

        $code1 = '<iframe id="_ytid_' . rand(10000, 99999) . '" width="' . self::$defaultwidth . '" height="' . self::$defaultheight .
                '" src="' . $linkscheme . '://www.youtube.com/embed/' . $linkparams['v'] . '?';
        $code2 = '" frameborder="0" allowfullscreen type="text/html" class="__youtube_prefs__"></iframe>';

        /*
          $prefparams = array();

          //get_option(self::$opt_auto_hd, 0);
          $autoplay = get_option(self::$opt_autoplay, 0);
          $cc_load_policy = get_option(self::$opt_cc_load_policy, 0) == 1 ? 1 : '';
          $iv_load_policy = get_option(self::$opt_iv_load_policy, 1);
          $loop = get_option(self::$opt_loop, 0);
          $modestbranding = get_option(self::$opt_modestbranding, 0) == 1 ? 1 : '';
          $rel = get_option(self::$opt_rel, 1);
          $showinfo = get_option(self::$opt_showinfo, 1);
          $theme = get_option(self::$opt_theme, 'dark');

          if ($autoplay == 1)
          $prefparams['autoplay'] = 1;
          if ($cc_load_policy == 1)
          $prefparams['cc_load_policy'] = 1;
          if ($iv_load_policy == 3)
          $prefparams['iv_load_policy'] = 3;
          if ($loop == 1)
          $prefparams['loop'] = 1;
          if ($modestbranding == 1)
          $prefparams['modestbranding'] = 1;
          if ($rel == 0)
          $prefparams['rel'] = 0;
          if ($showinfo == 0)
          $prefparams['showinfo'] = 0;
          if ($theme == 'light')
          $prefparams['theme'] = 'light';
         */

        $finalparams = $linkparams + self::$alloptions;
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
        $finalsrc = 'enablejsapi=1&' . $origin;

        if (count($finalparams) > 1)
        {
            foreach ($finalparams as $key => $value)
            {
                if (in_array($key, self::$yt_options))
                {
                    $finalsrc .= htmlspecialchars($key) . '=' . htmlspecialchars($value) . '&';
                    if ($key == 'loop' && $value == 1)
                        $finalsrc .= 'playlist=' . $finalparams['v'] . '&';
                }
            }
        }

        $code = $code1 . $finalsrc . $code2;

        if (self::$alloptions[self::$opt_center] == 1)
        {
            $code = '<div style="text-align: center; display: block;">' . $code . '</div>';
        }

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

    public static function init_dimensions($url, $urlkvp)
    {
        // get default dimensions; try embed size in settings, then try theme's content width, then just 480px
        if (self::$defaultwidth == null)
        {
            global $content_width;
            if (empty($content_width))
                $content_width = $GLOBALS['content_width'];

            self::$defaultwidth = $urlkvp['width'] ? $urlkvp['width'] : (self::$optembedwidth ? self::$optembedwidth : ($content_width ? $content_width : 480));
            self::$defaultheight = self::get_aspect_height($url);
        }
    }

    public static function get_aspect_height($url)
    {

        // attempt to get aspect ratio correct height from oEmbed
        $aspectheight = round((self::$defaultwidth * 9) / 16, 0);
        if ($url)
        {
            require_once( ABSPATH . WPINC . '/class-oembed.php' );
            $oembed = _wp_oembed_get_object();
            $args = array();
            $args['width'] = self::$defaultwidth;
            $args['height'] = self::$optembedheight;
            $args['discover'] = false;
            $odata = $oembed->fetch('http://www.youtube.com/oembed', $url, $args);

            if ($odata)
            {
                $aspectheight = $odata->height;
            }
        }

        //add 30 for YouTube's own bar
        return $aspectheight + 30;
    }

    public static function ytprefs_plugin_menu()
    {
        add_menu_page('YouTube Settings', 'YouTube', 'manage_options', 'youtube-my-preferences', 'YouTubePrefs::ytprefs_show_options', plugins_url('images/youtubeicon16.png', __FILE__), '10.00392854349');
        add_menu_page('YouTube Analytics Dashboard', 'My YouTube Performance', 'manage_options', 'youtube-ep-analytics-dashboard', 'YouTubePrefs::epstats_show_options', plugins_url('images/epstats16.png', __FILE__), '10.00492884349');
    }

    public static function epstats_show_options()
    {

        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // Now display the settings editing screen
        ?>
        <div class="wrap">
            <?php
            // header

            echo "<h2>" . '<img src="' . plugins_url('images/epstats16.png', __FILE__) . '" /> ' . __('YouTube Analytics Dashboard') . "</h2>";

            // settings form
            ?>
            <style type="text/css">
                .epicon { width: 20px; height: 20px; vertical-align: middle; padding-right: 5px;}
                .epindent {padding-left: 25px;}
            </style>
            <br>
            <iframe style="-webkit-box-shadow: 0px 0px 20px 0px #000000; box-shadow: 0px 0px 20px 0px #000000;" src="https://www.embedplus.com/dashboard/wordpress-video-analytics-seo.aspx?domain=<?php echo (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ""); ?>" width="1030" height="1600" scrolling="auto"/>
        </div>
        <?php
    }

    public static function ytprefs_show_options()
    {

        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // variables for the field and option names 
        $ytprefs_submitted = 'ytprefs_submitted';

        // Read in existing option values from database
        /*
          $auto_hd = get_option(self::$opt_auto_hd);
          $autoplay = get_option(self::$opt_autoplay, 0);
          $cc_load_policy = get_option(self::$opt_cc_load_policy, 0);
          $iv_load_policy = get_option(self::$opt_iv_load_policy, 1);
          $loop = get_option(self::$opt_loop, 0);
          $modestbranding = get_option(self::$opt_modestbranding, 0);
          $rel = get_option(self::$opt_rel, 1);
          $showinfo = get_option(self::$opt_showinfo, 1);
          $theme = get_option(self::$opt_theme, 'dark');
         */

        $all = get_option(self::$opt_alloptions);

        // See if the user has posted us some information
        // If they did, this hidden field will be set to 'Y'
        if (isset($_POST[$ytprefs_submitted]) && $_POST[$ytprefs_submitted] == 'Y')
        {
            // Read their posted values
//            $auto_hd = $_POST[self::$opt_auto_hd] == (true || 'on') ? 1 : 0;
//            $autoplay = $_POST[self::$opt_autoplay] == (true || 'on') ? 1 : 0;
//            $cc_load_policy = $_POST[self::$opt_cc_load_policy] == (true || 'on') ? 1 : 0;
//            $iv_load_policy = $_POST[self::$opt_iv_load_policy] == (true || 'on') ? 1 : 3;
//            $loop = $_POST[self::$opt_loop] == (true || 'on') ? 1 : 0;
//            $modestbranding = $_POST[self::$opt_modestbranding] == (true || 'on') ? 1 : 0;
//            $rel = $_POST[self::$opt_rel] == (true || 'on') ? 1 : 0;
//            $showinfo = $_POST[self::$opt_showinfo] == (true || 'on') ? 1 : 0;
//            $theme = $_POST[self::$opt_theme] == (true || 'on') ? 'dark' : 'light';

            $new_options = array();
            $new_options[self::$opt_center] = $_POST[self::$opt_center] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_auto_hd] = $_POST[self::$opt_auto_hd] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_autoplay] = $_POST[self::$opt_autoplay] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_cc_load_policy] = $_POST[self::$opt_cc_load_policy] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_iv_load_policy] = $_POST[self::$opt_iv_load_policy] == (true || 'on') ? 1 : 3;
            $new_options[self::$opt_loop] = $_POST[self::$opt_loop] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_modestbranding] = $_POST[self::$opt_modestbranding] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_rel] = $_POST[self::$opt_rel] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_showinfo] = $_POST[self::$opt_showinfo] == (true || 'on') ? 1 : 0;
            $new_options[self::$opt_theme] = $_POST[self::$opt_theme] == (true || 'on') ? 'dark' : 'light';

            $all = $new_options + $all;

            // Save the posted value in the database
//            update_option(self::$opt_auto_hd, $auto_hd);
//            update_option(self::$opt_autoplay, $autoplay);
//            update_option(self::$opt_cc_load_policy, $cc_load_policy);
//            update_option(self::$opt_iv_load_policy, $iv_load_policy);
//            update_option(self::$opt_loop, $loop);
//            update_option(self::$opt_modestbranding, $modestbranding);
//            update_option(self::$opt_rel, $rel);
//            update_option(self::$opt_showinfo, $showinfo);
//            update_option(self::$opt_theme, $theme);

            update_option(self::$opt_alloptions, $all);
            // Put a settings updated message on the screen
            ?>
            <div class="updated"><p><strong><?php _e('Settings saved.'); ?></strong></p></div>
            <?php
        }

        // Now display the settings editing screen

        echo '<div class="wrap">';

        // header

        echo "<h2>" . '<img src="' . plugins_url('images/youtubeicon16.png', __FILE__) . '" /> ' . __('YouTube Preferences') . "</h2>";

        echo '<em>Hear about major upcoming announcements and feature updates by <a target="_blank" href="http://eepurl.com/tpof9">signing up here</a>.</em>';
        
        echo '<a style="display: block; padding-top: 10px; text-decoration: none;" target="_blank" href="http://www.embedplus.com/dashboard/wordpress-video-analytics-seo.aspx"><img src="' . plugins_url('images/clickdashboard.png', __FILE__) . '" /></a>';
        
        // settings form
        ?>
            
        <style type="text/css">
            #ytform p { line-height: 20px; }
            #ytform ul li {margin-left: 30px; list-style: disc outside none;}
            .ytindent {padding: 0px 0px 0px 20px;}
            .shadow {-webkit-box-shadow: 0px 0px 20px 0px #000000; box-shadow: 0px 0px 20px 0px #000000;}
        </style>

        <div class="ytindent">
            <form name="form1" method="post" action="" id="ytform">
                <input type="hidden" name="<?php echo $ytprefs_submitted; ?>" value="Y">
                <h3>
                    <?php _e("How to Insert a YouTube Video") ?>
                </h3>
                <p>
                    All you have to do is paste the link to the YouTube video on its own line, as shown below (including the http:// part). Easy, eh?
                </p>
                <p>
                    <img class="shadow" src="<?php echo plugins_url('images/ownline.jpg', __FILE__) ?>" />
                </p>

                <p>
                    Note: Make sure the link is in plain text (not hyperlinked/blue).
                </p>

                <h3>
                    <?php _e("Default Options") ?>
                </h3>
                <p>
                    <?php _e("Below you can set the default options for all your videos. However, you can override them (and more) on a per-video basis. Directions on how to do that are in the next section.") ?>
                </p>

                <div class="ytindent">
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
                        <label for="<?php echo self::$opt_modestbranding; ?>"><?php _e('Modest branding - hide YouTube logo while playing') ?></label>
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
                    <p class="submit">
                        <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                    </p>
                </div>

                <h3>
                    <?php _e("How To Override Defaults / Other Options") ?>
                </h3>
                <?php
                _e("<p>Suppose you have a few videos that need to be different from the above defaults. You can add options to the end of a link as displayed below to override the above defaults. Each option should begin with '&'. </p>");
                _e('<ul>');
                _e("<li><strong>width</strong> - Sets the width of your player. If omitted, the default width will be the width of your theme's content.<em> Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&width=500</strong>&height=350</em></li>");
                _e("<li><strong>height</strong> - Sets the height of your player. If omitted, this will be calculated for you automatically. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA&width=500<strong>&height=350</strong></em> </li>");
                _e("<li><strong>autoplay</strong> - Set this to 1 to autoplay the video (or 0 to play the video once). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&autoplay=1</strong></em> </li>");
                _e("<li><strong>cc_load_policy</strong> - Set this to 1 to turn on closed captioning (or 0 to leave them off). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&cc_load_policy=1</strong></em> </li>");
                _e("<li><strong>iv_load_policy</strong> - Set this to 3 to turn off annotations (or 1 to show them). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&iv_load_policy=3</strong></em> </li>");
                _e("<li><strong>loop</strong> - Set this to 1 to loop the video (or 0 to not loop). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&loop=1</strong></em> </li>");
                _e("<li><strong>modestbranding</strong> - Set this to 1 to remove the YouTube logo while playing (or 0 to show the logo). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&modestbranding=1</strong></em> </li>");
                _e("<li><strong>rel</strong> - Set this to 0 to not show related videos at the end of playing (or 1 to show them). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&rel=0</strong></em> </li>");
                _e("<li><strong>showinfo</strong> - Set this to 0 to hide the video title and other info (or 1 to show it). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&showinfo=0</strong></em> </li>");
                _e("<li><strong>theme</strong> - Set this to 'light' to make the player have the light-colored theme (or 'dark' for the dark theme). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&theme=light</strong></em> </li>");
                _e('</ul>');

                _e("<p>You can also start and end each individual video at particular times. Like the above, each option should begin with '&'</p>");
                _e('<ul>');
                _e("<li><strong>start</strong> - Sets the time (in seconds) to start the video. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350<strong>&start=20</strong></em> </li>");
                _e("<li><strong>end</strong> - Sets the time (in seconds) to stop the video. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350<strong>&end=100</strong></em> </li>");
                _e('</ul>');
                ?>

            </form>

            <h3><?php _e('Other Notes') ?></h3>
            <P>
                <?php _e("Note that this is a no-frills plugin. We're just supporting the basic functions that people typically want when embedding YouTube videos, and providing an easy way to make defaults. If you want more, take a look at a more powerful one here: <a target=\"_blank\" href=\"http://wordpress.org/extend/plugins/embedplus-for-wordpress/\">Advanced YouTube Embed</a> by <a target=\"_blank\" href=\"http://www.embedplus.com\">EmbedPlus</a>."); ?>
            </p>
            <p>
                <?php echo '<em>Hear about major upcoming announcements and feature updates by <a target="_blank" href="http://eepurl.com/tpof9">signing up here</a>.</em>'; ?>
            </p>

        </div>
        <?php
    }

    public static function ytprefsscript()
    {
        wp_enqueue_script('__ytprefs__', plugins_url('scripts/ytprefs.min.js', __FILE__));
    }

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

register_activation_hook(__FILE__, array('YouTubePrefs', 'initoptions'));
add_action('wp_enqueue_scripts', array('YouTubePrefs', 'ytprefsscript'));

$youtubeplg = new YouTubePrefs();


