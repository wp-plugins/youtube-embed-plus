=== YouTube ===
Contributors: embedplus
Plugin Name: YouTube Embed
Tags: responsive, fluid, iphone, mobile, android, tablet, ipad, youtube playlist, playlists, playlist, wmode, hd, autohd, auto hd, seo, video analytics, basic analytics, google, google analytics, view count, vlog, vlogging, web videos, youtube analytics, youtube, youtube plugin, youtube shortcode, embed youtube, plugin, video, video shortcode, embed videos, youtube player, shortcode, simple, simple youtube, smart youtube playback, youtube embed, wordpress youtube embed, embedding youtube, youtube embedding, video plugin, https, ssl, secure, no-cookie, cookie, html5, youtube html5
Requires at least: 2.9
Tested up to: 3.6.1
Stable tag: 4.6
License: GPLv3 or later

YouTube embed plugin with basic features and convenient defaults. Upgrade now to add view tracking and access to your very own analytics dashboard.

== Description ==

**New:**

* Responsive, fluid video sizes to dynamically fit all screen sizes (smart phone, PC and tablet)
* Now supports playlists!
* Due to frequent requests, we've now enhanced the deleted video alerts to work with past YouTube videos that you embedded with many other YouTube plugins.

This YouTube embed plugin for WordPress keeps things simple, but it can be upgraded for enhanced performance, privacy and security for you and your visitors. It provides the basic features of the YouTube embedded player and will have you posting videos in seconds after installing it. **All you have to do is simply paste an unformatted YouTube link on its own line.**  Your YouTube embed will then show up when you preview or publish the post. The settings page has plenty of default options that you can automatically apply to all your embedded YouTube videos:

* Automatically center all your videos
* Automatically start playing your videos
* Turn on/off closed captions by default
* Show/hide annotations by default
* Loop your videos
* Modest branding - hide YouTube logo while playing
* Show/hide related videos at the end
* Show/hide the video title and other info
* Use the light theme
* Force HD quality when available
* Show/hide player controls
* Use "opaque" wmode
* Make your videos responsive so that they dynamically fit in all screen sizes (smart phone, PC and tablet)

Customizations can be also made to each YouTube embed by adding more to the link as shown below. Adding these will override the above global defaults that you set:

* width - Sets the width of your player. If omitted, the default width will be the width of your theme's content. Example: `http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350`
* height - Sets the height of your player. If omitted, this will be calculated for you automatically. Example: `http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350`
* vq - Set this to `hd720` or `hd1080` to force the video to play in HD quality. Example: `http://www.youtube.com/watch?v=quwebVjAEJA&vq=hd720`
* autoplay - Set this to 1 to autoplay the video (or 0 to play the video once). Example: `http://www.youtube.com/watch?v=quwebVjAEJA&autoplay=1`
* cc_load_policy - Set this to 1 to turn on closed captioning (or 0 to leave them off). Example: `http://www.youtube.com/watch?v=quwebVjAEJA&cc_load_policy=1`
* iv_load_policy - Set this to 3 to turn off annotations (or 1 to show them). Example: `http://www.youtube.com/watch?v=quwebVjAEJA&iv_load_policy=3`
* loop - Set this to 1 to loop the video (or 0 to not loop). Example: `http://www.youtube.com/watch?v=quwebVjAEJA&loop=1`
* modestbranding - Set this to 1 to remove the YouTube logo while playing (or 0 to show the logo). Example: `http://www.youtube.com/watch?v=quwebVjAEJA&modestbranding=1`
* rel - Set this to 0 to not show related videos at the end of playing (or 1 to show them). Example: `http://www.youtube.com/watch?v=quwebVjAEJA&rel=0`
* showinfo - Set this to 0 to hide the video title and other info (or 1 to show it). Example: `http://www.youtube.com/watch?v=quwebVjAEJA&showinfo=0`
* theme - Set this to 'light' to make the player have the light-colored theme (or 'dark' for the dark theme). Example: `http://www.youtube.com/watch?v=quwebVjAEJA&theme=light`

You can also start and end each individual video at particular times. Like the above, each option should begin with '&'

* start - Sets the time (in seconds) to start the video. Example: `http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350&start=20`
* end - Sets the time (in seconds) to stop the video. Example: `http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350&end=100`

Beyond the above features, you have the option of upgrading to PRO to add enhanced features like a visual embedding wizard (so you can avoid memorizing codes), priority support, and security/performance. As a PRO user, you also get access to our deleted video alerts to help avoid showing embedded videos that are later removed from YouTube.com. You even get an embedder-centric analytics dashboard that adds view tracking to each of your embeds so that you can answers questions like:

* How much are your visitors actually watching the videos you post?
* How does the view activity on your site compare to other sites like it?
* What and when are your best and worst performing YouTube embeds?
* How much do the producers of the YouTube videos you embed rely on **your site**, versus other sites and YouTube.com, for views?

See more details after installing. Enjoy!

== Installation ==

1. Use the WordPress plugin installer to install the plugin.  Alternatively, you can just extract the folder in our download package and upload it to your plugin directory.
1. Access the Plugins admin menu to activate the YouTube embed plugin.
1. Make your default settings after clicking the new YouTube menu item that shows up in your admin panel.
1. In your editor, start pasting the links with any desired additional codes needed for your YouTube embed. Make sure the link is on its own line.
1. To get an analytics dashboard, [sign up for one of the options here >>](https://www.embedplus.com/dashboard/pro-easy-video-analytics.aspx)

Always follow these rules when pasting a link:

* Make sure the url is really on its own line by itself.
* Make sure the url is not an active hyperlink (i.e., it should just be plain text). Otherwise, highlight the url and click the "unlink" button in your editor.
* Make sure you did **not** format or align the url in any way. If your url still appears in your actual post instead of a video, highlight it and click the "remove formatting" button (formatting can be invisible sometimes).
* Finally, there's a slight chance your custom theme is the issue, if you have one. To know for sure, we suggest temporarily switching to one of the default WordPress themes (e.g., "Twenty Thirteen") just to see if your video does appear. If it suddenly works, then your custom theme is the issue. You can switch back when done testing.

Additional codes (adding these will override the default settings in the admin):

* width - Sets the width of your player. If omitted, the default width will be the width of your theme's content. Example: `http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350`
* height - Sets the height of your player. If omitted, this will be calculated for you automatically. Example: `http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350`
* vq - Set this to `hd720` or `hd1080` to force the video to play in HD quality. Example: `http://www.youtube.com/watch?v=quwebVjAEJA&vq=hd720`
* autoplay - Set this to 1 to autoplay the video (or 0 to play the video once). Example: `http://www.youtube.com/watch?v=quwebVjAEJA&autoplay=1`
* cc_load_policy - Set this to 1 to turn on closed captioning (or 0 to leave them off). Example: `http://www.youtube.com/watch?v=quwebVjAEJA&cc_load_policy=1`
* iv_load_policy - Set this to 3 to turn off annotations (or 1 to show them). Example: `http://www.youtube.com/watch?v=quwebVjAEJA&iv_load_policy=3`
* loop - Set this to 1 to loop the video (or 0 to not loop). Example: `http://www.youtube.com/watch?v=quwebVjAEJA&loop=1`
* modestbranding - Set this to 1 to remove the YouTube logo while playing (or 0 to show the logo). Example: `http://www.youtube.com/watch?v=quwebVjAEJA&modestbranding=1`
* rel - Set this to 0 to not show related videos at the end of playing (or 1 to show them). Example: `http://www.youtube.com/watch?v=quwebVjAEJA&rel=0`
* showinfo - Set this to 0 to hide the video title and other info (or 1 to show it). Example: `http://www.youtube.com/watch?v=quwebVjAEJA&showinfo=0`
* theme - Set this to 'light' to make the player have the light-colored theme (or 'dark' for the dark theme). Example: `http://www.youtube.com/watch?v=quwebVjAEJA&theme=light`

You can also start and end each individual video at particular times. Like the above, each option should begin with '&'

* start - Sets the time (in seconds) to start the video. Example: `http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350&start=20`
* end - Sets the time (in seconds) to stop the video. Example: `http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350&end=100`

== Screenshots ==

1. YouTube Embed Screenshot 1: Paste a YouTube link on its own line and it will become a YouTube embed on your website.
2. YouTube Embed Screenshot 2: YouTube's admin settings.
3. YouTube Embed Screenshot 3: PRO Visual YouTube Wizard

== Changelog ==

= YouTube Embed 4.6 =
Added optional responsive video layout to fit all screen sizes. (smart phone, PC and tablet)

= YouTube Embed 4.5 =
Added support for playlists.
added support for wmode.

= YouTube Embed 4.1 =
Fixed spacing issue. Also added ability to fall back to old spacing format.

= YouTube Embed 4.0 =
New features for all users: lazy loading for the flash player by default and the ability to hide player controls for a cleaner look.

= YouTube Embed 3.7 =
Enhanced deleted video checker for PRO users

= YouTube Embed 3.5 =
Added ability to try to force HTML5 player to speed up page loading

= YouTube Embed 3.3 =
HTTPS: Added secure YouTube embedding for PRO users

= YouTube Embed 3.2 =
Ensures video-specific height overrides defaults properly

= YouTube Embed 3.1 =
Fixed obscure height problem

= YouTube Embed 3.0 =
Added Visual YouTube Wizard for PRO users
Added autologin to analytics for PRO users
Added priority support form for PRO users

= YouTube Embed 2.6 =
Compatible with WP 3.6

= YouTube Embed 2.4 =
Added auto HD support
Support for shorthand (i.e. `http://www.youtu.be`)
Fixed editor issue

= YouTube Embed 2.3 =
Start/end time shortcut bug fix

= YouTube Embed 2.2 =
Minor changes

= YouTube Embed 2.1 =
By request from several users, we've added easier access to the video analytics dashboard

= YouTube Embed 2.0 =

This upgrade specifically integrates a user-friendly YouTube Analytics Dashboard to this plugin so you can learn a lot more about the videos you post.  Download it if you would like it to use your site's YouTube-related activity to help answer questions like:
 
* How much are your visitors actually watching the videos you post?
* How does the view activity on your site compare to other sites like it?
* What and when are your best and worst performers?
* How much do the producers of the YouTube videos you embed rely on **your site** for views?

We think these are all interesting questions; however, note that there's no need to upgrade if you don't.  

= YouTube Embed 1.1 =
Fixed minor bug.

= YouTube Embed 1.0 =
First release uploaded to the plugin repository.

== Other Notes ==

This YouTube plugin includes [YouTube embed analytics](https://www.embedplus.com/dashboard/pro-easy-video-analytics.aspx) to help you learn a lot about the videos you post.