=== Plugin Name ===
Contributors: paul.irish, dirkhaim, candrews
Donate link: http://www.infinite-scroll.com
Tags: ajax, pagination, scrolling, scroll, endless, reading
Requires at least: 2.3
Tested up to: 2.8.3 
Stable tag: 1.3.1

Automatically append the next page of posts (via AJAX) to your page when a user scrolls to the bottom. 

== Description ==

Infinite Scroll adds the following functionality to your wordpress installation: **When a user scrolls towards the bottom of the page, the next page of posts is automatically retrieved and appended**. This means they never need to click "Next Page", which *dramatically increases stickiness*.

Features:

*   Fully embraces progressive enhancement: RSS readers and js-off folks will be happy.
*   Fully customizable by text, css, and images.
*   Works on 80% of wordpress themes, with little or no configuration.
*   Hackable source code to modify the behavior.
*   Tested back to 2.3, but probably works on earlier versions.
*   Requires no (hopefully) template hacking, only a knowledge of CSS selectors.

Full information on [infinite-scroll.com](http://www.infinite-scroll.com)


== Installation ==

1. Download the plugin.
1. Install it to your /wp-content/plugins/ directory
1. Activate the plugin in your Wordpress Admin UI.
1. Visit the Settings / Infinite Scroll page to [set up the css selectors](http://www.infinite-scroll.com/installation/).
1. The plugin will now work for a logged in Admin, but will be disabled for all other users; you can change this.


== Frequently Asked Questions ==

= Can I change the number of posts loaded? = 

Yup. But that's a Wordpress thing. Go to Settings / Reading 

= Why is this FAQ so short? = 

Because it is. Go to [infinite-scroll.com](http://www.infinite-scroll.com) for more.

== Screenshots ==

1. Loading the next set of posts

== Changelog ==

= 1.3 =
Use proper Wordpress function to register the javascript
Use plugins_url to determine plugin url

= 1.2 = 
* 2009 August 4th
* getoption(’siteurl’) fix made.
* jQuery plugin version updated. many more options available.
* Release backwards compatible

= 1.1 =
* 2008 September 25
* JavaScript rewritten as a proper jQuery plugin.
* Added animation

= 1.0 =
* June 29 - 1.0 release.

