# Infinite-Scroll #
**Contributors:** beaver6813, dirkhaim, paul.irish, benbalter  
**Donate link:** http://www.infinite-scroll.com  
**Tags:** ajax, pagination, scrolling, scroll, endless, reading  
**Requires at least:** 3.2  
**Tested up to:** 3.4  
**Stable tag:** 2.0b  

Automatically append the next page of posts (via AJAX) to your page when a user scrolls to the bottom. 

## Description ##

Infinite Scroll adds the following functionality to your WordPress installation: When a user scrolls towards the bottom of the page, the next page of posts is automatically retrieved and appended. This means they never need to click "Next Page", which *dramatically increases stickiness*.  

Features:

* 	Fully embraces progressive enhancement: RSS readers and js-off folks will be happy.  
*   Fully customizable by text, css, and images.
*   Works on 80% of WordPress themes, with little or no configuration.
*   API hooks to modify the behavior.
*   Requires no (hopefully) template hacking, only a knowledge of CSS selectors.

Full information on [infinite-scroll.com](http://www.infinite-scroll.com)

## Installation ##

### Automatic Install ###
1. Login to your WordPress site as an Administrator
2. Navigate to Plugins->Add New from the menu on the left
3. Search for "Infinite Scroll"
4. Click "Install"
5. Click "Activate Now"

### Manual Install ###
1. Download the plugin from the link in the top left corner
2. Unzip the file, and upload the resulting "infinite-scroll" folder to your "/wp-content/plugins directory" as "/wp-content/plugins/infinite-scroll"
3. Log into your WordPress install as an administrator, and navigate to the plugins screen from the left-hand menu
4. Activate Infinite Scroll

## Frequently Asked Questions ##

### What exactly is infinite scrolling? ###

Essentially it is pre-fetching content from a subsequent page and adding it directly to the user’s current page. [More Information](http://www.infinite-scroll.com/the-interaction-design-pattern/)

### Can I change the number of posts loaded? ###

Yes. Go to Settings -> Reading.

### How do I change the alignment of the loading image? ###

Add the following to your theme's css: `#infscr-loading img { text-align: ALIGNMENT; }` where "ALIGNMENT" is either `left`, `right`, or `center`.  

### How can I pass additional arguments such as behavior or callbacks to the script? ###

Add a filter to `infinite_scroll_options` and adds additional options to the options array.

### Is the plugin available in my language? ###

If you enjoy the plugin and are interested in contributing a translation (it's super easy), please take a look at the [Translating WordPress](http://codex.wordpress.org/Translating_WordPress) page. 

### Is it SEO-Friendly? ###
Yes all enhancements are made via javascript only, so search spiders see no difference.

### Is it accessible? ###

Things won’t change for screen-readers. This technique degrades gracefully.

### Does it still keep going, even at the end of the blog? ###

Infinite scroll is configured to die when it hits a 404 Not Found status code, so when it goes through all your archives it should hit a /page/43/ (or something) that doesn’t exist, then show a message "Congrats, you’ve reached the end of the internet." Some WordPress themes don’t report not found pages with a 404. Try a different theme or contacting the theme author.

### Do I need to edit my theme to make this work? ###

Probably not, nope.

## Screenshots ##

###1. Loading the next set of posts###
![Loading the next set of posts](http://s.wordpress.org/extend/plugins/infinite-scroll/screenshot-1.png)


## Changelog ##

### 2.5 ###
* Plugin completely rewritten from the bottom up
* **Note: you will need to manually reactivate this plugin after upgrading**  
* Minimum WordPress version required now to **3.2**
* Added support for internationalization
* Options screens significantly simplified with additional help text
* Presets screen now uses standard WordPress administrative interface
* Changes to presets are now made inline; saved via AJAX without page reload
* Added additional API endpoints for developers to modify and customize plugin behavior
* Loading image now uses native WordPress uploader and can accept arbitrary URL or image from media gallery
* Loading and finished text now uses native WordPress TinyMCE editor
* If site administrator has not entered CSS selectors and preset is known, plugin will default to preset
* Any preset entered by site (or network) administrator will now override community contributed presets by default
* Community contributed CSS selector presets are now stored in the database allowing for plugin directory to be unwritable (security enhancement)
* Prompts users to default to CSS selector preset when available
* Community contributed CSS selected asynchronously update daily (performance enhancement)
* Site (or network) specific CSS selector presets are now stored in the database (security enhancement)
* CSS Preset updater now uses WP_HTTP class (compatibility enhancement)
* Javascript file now served directly to user (rather than proxied via PHP) to allow for browser caching and better integration with caching plugins (performance enhancement)
* Default loading and fished messages changed
* Removed prompt for option to activate infinite scrolling for only certain users (defaults to all)
* Removed prompt for option to toggle debug mode (defaults to `WP_DEBUG` or `SCRIPT_DEBUG`)
* Removed prompt for option to toggle scrolling behavior (defaults to automatic)
* Removed prompt for additional callbacks
* Removed prompt to select image alignment (defaults to left, can override via CSS)
* Significant code clean up, file reorganization, and in-line documentation to conform to WordPress coding and style standards
* Plugin file name changed to conform to standard WordPress naming conventions
* License (GPL) included with distribution
* Version numbering simplified

### 2.0b2.120111 ###
* Added infinite_scroll_load_override filter to manually force infinite-scroll to load on a page.
* Thanks to https://github.com/samargulies for the above patch.

### 2.0b2.111218 ###
* Updated preset DB with basic themes
* Made a few fixes in options/presets to prep for main release.
* Changed hook used by 404 detection to template_redirect from wp.

### 2.0b2.110822 ###
* Removed registration/enqueuing script in favor of just enqueueing
* Fixed bug/typo in compressed init script

### 2.0b2.110821 ###
* Converted options pages to use Settings API
* Added behavior selection (Manual triggering)

### 2.0b2.110818 ###
* Completely revamped admin panel adding more modular management
* Added ability to center loading image
* Added WYSIWYG editor to HTML allowed fields in admin panel
* Updated Javascript callback to pass DOM for new elements added
* Added Preset manager
* Added ability to auto-fill selector fields from theme preset
* Overhaul of underlying code, separating out into classes to cut down
on code processed (and hence load time) for the end-user.

### 2.0b2.110723 ###
* Improved escaping on settings to allow greater flexibility.
* Fixed issue with jQuery not loading if WP version is up to date.
* Fixed issue with plugin showing twice on plugin list.

### 2.0b2.110716 ###
* Moved init script from direct page insertion to separate script.
* Cleaned up unneeded declared constants.
* Combined init script and library into one minified script.
* This fixes an incompatibility with HeadJS plugin.

### 2.0b2.110713 ###
* Fixed multiple jQuery loaded conflicts.
* Now uses standard WordPress javascript insertion.
* Loads locally stored jQuery 1.6.2 if current version is < 1.6
  this can only be detected through WordPress now, hence the fix
  for multiple versions conflicting (some naughty themes don't
  use WordPress's javascript loading).

### 2.0b2.110709 ###
*** FIX:** Default options not being recursively copied.  
* Updated to new options layout.
* Callback fix.

### 2.0b2.110706 ###
* HTTPS loading image fix.
* Better debug support (switches between minified and non).

### 2.0b2.110629 ###
* Callback (custom javascript) fix.
* IE9 binding bug fix.

### 2.0b2.110628 ###
* Modified the method used to determine the link paths (bug fix)

### 2.0b2.110617 ###
* Updated core javascript to version 2.0b2.110617 (full revamp).
* General cleanup/tidy of plugin itself.
* Fixed bugs with numerical sub-domains (now uses site_url to determine path).
* Supports archives, tags, searches.
* Wider theme support.
* Simplified plugin options page, added loading image upload ability, added option for debug mode.
* Removed annoying reminder that showed on all admin pages.
* Forces 404 header when no more posts are available.

### 1.5.100504 ###
* New appending technique using document fragments.
* Callback receives the contentSelector elem as its first argument,
    and an array of the new elements as the second argument

### 1.4.100210 ###
Fixed small bug that jQuery 1.4 introduced.

### 1.4 ###
Proper WordPress method (enqueue_script) removed because it just breaks too often
JS updated with some custom method action.

### 1.3 ###
Use proper WordPress function to register the javascript
Use plugins_url to determine plugin url

### 1.2 ###
* 2009 August 4th
* `get_option('siteurl')` fix made.
* jQuery plugin version updated. many more options available.
* Release backwards compatible

### 1.1 ###
* 2008 September 25
* JavaScript rewritten as a proper jQuery plugin.
* Added animation

### 1.0 ###
* June 29 - 1.0 release.
