<?php
/*
Plugin Name: Infinite Scroll
Version: 2.0b2.110716
Plugin URI: http://www.infinite-scroll.com
Description: Automatically loads the next page of posts into the bottom of the initial page. 
Author: Beaver6813, dirkhaim, Paul Irish
Author URI: http://www.infinite-scroll.com
License   : http://creativecommons.org/licenses/GPL/2.0/
*/
define('infscr_version'		,	'2.0b2.110716');

header('Content-Type: application/javascript');
require_once( '../../../wp-load.php' );
wp();

function outputLicense()
	{
	echo "/*
	--------------------------------
	Infinite Scroll Wordpress Plugin
	--------------------------------
	+ http://wordpress.org/extend/plugins/infinite-scroll/
	+ version ".infscr_version."
	+ Copyright 2011 Beaver6813, dirkhaim, Paul Irish
	+ Licensed under the GPLv2 License
	
	+ Documentation: http://infinite-scroll.com/
	
*/
";	
	}

//Get pathParse and validate it.
$error = false;
if($pathInfo = unserialize(base64_decode($_GET['p'])))
	{
	if(empty($pathInfo[0])||count($pathInfo[0])<2||empty($pathInfo[1])||$pathInfo[1]!=md5(NONCE_KEY.$pathInfo[0][0]."infscr".$pathInfo[0][1].infscr_version))
		$error = true;		
	}
else
	$error = true;

if($error)
	echo "//Missing/Invalid PathParse or Signature.";
else
	{
	//Lets setup settings!
	$plugin_dir 		= plugins_url('infinite-scroll');
	$current_page 		= (get_query_var('paged')) ? get_query_var('paged') : 1;
	$debug				= (stripslashes(get_option("infscr_debug"))==1) ? "true" : "false";
	
	$scheme 			= (is_ssl()) ? "https://" : "http://";
	$noscheme 			= parse_url(stripslashes(get_option("infscr_image")));
	$loading_image		= $scheme.$noscheme['host'].$noscheme['path'];	
	
	$loading_text		= stripslashes(get_option("infscr_text"));
	$donetext			= stripslashes(get_option("infscr_donetext"));
	$content_selector	= stripslashes(get_option("infscr_content_selector"));
	$navigation_selector= stripslashes(get_option("infscr_nav_selector"));
	$post_selector		= stripslashes(get_option("infscr_post_selector"));
	$next_selector		= stripslashes(get_option("infscr_next_selector"));
	$js_calls			= stripslashes(get_option("infscr_js_calls"));
	
	//Start Loading!
	//Load infinite-scroll
	outputLicense();
	if($debug=="true")
		{
		echo file_get_contents("js/jquery.infinitescroll.js");
		echo '//We leave a function outside the infinite-scroll area so that it works with older jQuery versions
			function infinite_scroll_callback() {
				'.$js_calls.'	
				}
			jQuery(document).ready(function($) {
			// Infinite Scroll jQuery+Wordpress plugin
			// Now we\'re inside, we should be able to use $ again
				$("'.$content_selector.'").infinitescroll({
					debug           : '.$debug.',
					loading			: {
						img			: "'.$loading_image.'",
						msgText		: "'.$loading_text.'",
						finishedMsg	: "'.$donetext.'"
						},
					state			: {
						currPage	: "'.$current_page.'"
						},
					nextSelector    : "'.$next_selector.'",
					navSelector     : "'.$navigation_selector.'",
					contentSelector : "'.$content_selector.'",
					itemSelector    : "'.$post_selector.'",
					pathParse		: ["'.$pathInfo[0][0].'", "'.$pathInfo[0][1].'"]
					}, function() { window.setTimeout(infinite_scroll_callback(), 1); } );
					});';
		}
	else
		{
		echo file_get_contents("js/jquery.infinitescroll.min.js");
		echo 'function infinite_scroll_callback(){'.$js_calls.'}
jQuery(document).ready(function($){$("'.$content_selector.'").infinitescroll({debug:'.$debug.',loading:{img:"'.$loading_image.'",msgText:"'.$loading_text.'",finishedMsg:"'.$donetext.'"},state:{currPage:"'.$current_page.'"},nextSelector:"'.$next_selector.'",navSelector:"'.$navigation_selector.'",contentSelector:"'.$content_selector.'",itemSelector:"'.$post_selector.'",pathParse:["'.$pathInfo[0][0].'","'.$pathInfo[0][1].'"]},function(){window.setTimeout(infinite_scroll_callback(),1);});});';
		}	
	}