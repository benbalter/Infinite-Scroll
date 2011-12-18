<?php
/*
Plugin Name: Infinite Scroll
Version: 2.0b2.111218
Plugin URI: http://www.infinite-scroll.com
Description: Automatically loads the next page of posts into the bottom of the initial page. 
Author: Beaver6813, dirkhaim, Paul Irish
Author URI: http://www.infinite-scroll.com
License   : http://creativecommons.org/licenses/GPL/2.0/
*/
include("includes/infinite-scroll.class.php");
new infiniteScroll();
//Add Generic Actions
add_action('init'					, array('infiniteScroll', 'addDefaults'));

//Add Main Blog Actions
add_action('template_redirect'		, array('infiniteScroll', 'addInfiniteScroll'));
add_action('wp_head'				, array('infiniteScroll', 'addStyle'));
/*Because recently (3.0) WP doesn't always throw a 404 when posts aren't found.
Infinite-Scroll relies on 404 errors to terminate.. so we'll force them. */
add_action("template_redirect"		, array('infiniteScroll', 'trigger404'));

//Add Admin Panel Actions
add_action('admin_init'				, array('infiniteScroll', 'initOptions'));
add_action('admin_menu'				, array('infiniteScroll', 'addOptPageLoader'));	
?>