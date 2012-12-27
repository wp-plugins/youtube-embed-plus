<?php
/*
  Plugin Name: YouTube Embed
  Plugin URI: http://www.embedplus.com
  Description: YouTube embed plugin for WordPress. Provides the basic features of YouTube for your blog, and convenient defaults.
  Version: 1.1
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
    public static $opt_auto_hd = 'youtubeprefs_auto_hd';
    public static $opt_autoplay = 'youtubeprefs_autoplay';
    public static $opt_cc_load_policy = 'youtubeprefs_cc_load_policy';
    public static $opt_iv_load_policy = 'youtubeprefs_iv_load_policy';
    public static $opt_loop = 'youtubeprefs_loop';
    public static $opt_modestbranding = 'youtubeprefs_modestbranding';
    public static $opt_rel = 'youtubeprefs_rel';
    public static $opt_showinfo = 'youtubeprefs_showinfo';
    public static $opt_theme = 'youtubeprefs_theme';

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
            add_option(self::$opt_auto_hd, 0);
            add_option(self::$opt_autoplay, 0);
            add_option(self::$opt_cc_load_policy, 0);
            add_option(self::$opt_iv_load_policy, 1);
            add_option(self::$opt_loop, 0);
            add_option(self::$opt_modestbranding, 0);
            add_option(self::$opt_rel, 1);
            add_option(self::$opt_showinfo, 1);
            add_option(self::$opt_theme, 'dark');

            update_option('embed_autourls', 1);
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

        $code1 = '<iframe width="' . self::$defaultwidth . '" height="' . self::$defaultheight .
                '" src="' . $linkscheme . '://www.youtube.com/embed/' . $linkparams['v'] . '?';
        $code2 = '" frameborder="0" allowfullscreen></iframe>';

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

        $finalparams = $linkparams + $prefparams;
        $finalsrc = '';

        if (count($finalparams) > 1)
        {
            foreach ($finalparams as $key => $value)
            {
                if ($key != 'v')
                {
                    $finalsrc .= htmlspecialchars($key) . '=' . htmlspecialchars($value) . '&';
                    if ($key == 'loop' && $value == 1)
                        $finalsrc .= 'playlist=' . $finalparams['v'] . '&';
                }
            }
        }

        $code = $code1 . $finalsrc . $code2;

        // reset static vals for next embed
        self::$optembedwidth = null;
        self::$optembedheight = null;
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
            self::$optembedwidth = intval(get_option('embed_size_w'));
            self::$optembedheight = intval(get_option('embed_size_h'));

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
        $auto_hd = get_option(self::$opt_auto_hd);
        $autoplay = get_option(self::$opt_autoplay, 0);
        $cc_load_policy = get_option(self::$opt_cc_load_policy, 0);
        $iv_load_policy = get_option(self::$opt_iv_load_policy, 1);
        $loop = get_option(self::$opt_loop, 0);
        $modestbranding = get_option(self::$opt_modestbranding, 0);
        $rel = get_option(self::$opt_rel, 1);
        $showinfo = get_option(self::$opt_showinfo, 1);
        $theme = get_option(self::$opt_theme, 'dark');

        // See if the user has posted us some information
        // If they did, this hidden field will be set to 'Y'
        if (isset($_POST[$ytprefs_submitted]) && $_POST[$ytprefs_submitted] == 'Y')
        {
            // Read their posted values
            $auto_hd = $_POST[self::$opt_auto_hd] == (true || 'on') ? 1 : 0;
            $autoplay = $_POST[self::$opt_autoplay] == (true || 'on') ? 1 : 0;
            $cc_load_policy = $_POST[self::$opt_cc_load_policy] == (true || 'on') ? 1 : 0;
            $iv_load_policy = $_POST[self::$opt_iv_load_policy] == (true || 'on') ? 1 : 3;
            $loop = $_POST[self::$opt_loop] == (true || 'on') ? 1 : 0;
            $modestbranding = $_POST[self::$opt_modestbranding] == (true || 'on') ? 1 : 0;
            $rel = $_POST[self::$opt_rel] == (true || 'on') ? 1 : 0;
            $showinfo = $_POST[self::$opt_showinfo] == (true || 'on') ? 1 : 0;
            $theme = $_POST[self::$opt_theme] == (true || 'on') ? 'dark' : 'light';


            // Save the posted value in the database
            update_option(self::$opt_auto_hd, $auto_hd);
            update_option(self::$opt_autoplay, $autoplay);
            update_option(self::$opt_cc_load_policy, $cc_load_policy);
            update_option(self::$opt_iv_load_policy, $iv_load_policy);
            update_option(self::$opt_loop, $loop);
            update_option(self::$opt_modestbranding, $modestbranding);
            update_option(self::$opt_rel, $rel);
            update_option(self::$opt_showinfo, $showinfo);
            update_option(self::$opt_theme, $theme);

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

                    <!--
                    <p>
                        <input name="<?php echo self::$opt_auto_hd; ?>" id="<?php echo self::$opt_auto_hd; ?>" <?php checked($auto_hd, 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_auto_hd; ?>"><?php _e('Automatically make all videos HD quality (when possible).') ?></label>
                    </p>
                    -->
                    <p>
                        <input name="<?php echo self::$opt_autoplay; ?>" id="<?php echo self::$opt_autoplay; ?>" <?php checked($autoplay, 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_autoplay; ?>"><?php _e('Automatically start playing your videos') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_cc_load_policy; ?>" id="<?php echo self::$opt_cc_load_policy; ?>" <?php checked($cc_load_policy, 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_cc_load_policy; ?>"><?php _e('Turn on closed captions by default') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_iv_load_policy; ?>" id="<?php echo self::$opt_iv_load_policy; ?>" <?php checked($iv_load_policy, 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_iv_load_policy; ?>"><?php _e('Show annotations by default') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_loop; ?>" id="<?php echo self::$opt_loop; ?>" <?php checked($loop, 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_loop; ?>"><?php _e('Loop all your videos') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_modestbranding; ?>" id="<?php echo self::$opt_modestbranding; ?>" <?php checked($modestbranding, 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_modestbranding; ?>"><?php _e('Modest branding - hide YouTube logo while playing') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_rel; ?>" id="<?php echo self::$opt_rel; ?>" <?php checked($rel, 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_rel; ?>"><?php _e('Show related videos at the end') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_showinfo; ?>" id="<?php echo self::$opt_showinfo; ?>" <?php checked($showinfo, 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_showinfo; ?>"><?php _e('Show the video title and other info') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_theme; ?>" id="<?php echo self::$opt_theme; ?>" <?php checked($theme, 'dark'); ?> type="checkbox" class="checkbox">
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
                _e("<p>Suppose you have a few videos that need to be different from the above defaults. You can add options to the end of a link as displayed below. Each option should begin with '&'. </p>");
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

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//class start
class Add_new_tinymce_btn
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

$youtubeplg = new YouTubePrefs();

/*
$youtubeprefsmce = new Add_new_tinymce_btn('|', 'youtubeprefswiz', plugins_url() . '/youtube/js/youtube_mce.js.php');

if (YouTubePrefs::wp_above_version('2.9'))
{
    add_action('admin_enqueue_scripts', 'youtubeprefs_admin_enqueue_scripts');
}
else
{
    wp_enqueue_style('youtubeprefswiz', plugins_url() . '/youtube/js/youtube_mce.css');
}

function youtubeprefs_admin_enqueue_scripts()
{
    wp_enqueue_style('youtubeprefswiz', plugins_url() . '/youtube/js/youtube_mce.css');
}
*/