<?php
header('Content-Type: application/javascript');
require_once( '../../../wp-load.php' );
wp();
	
//Get pathParse and validate it.
$error = false;
if($pathInfo = unserialize(base64_decode($_GET['p'])))
	{
	if(empty($pathInfo[0])||count($pathInfo[0])<2||empty($pathInfo[1])||$pathInfo[1]!=md5(NONCE_KEY.$pathInfo[0][0]."infscr".$pathInfo[0][1].infiniteScroll::$Version.$pathInfo[0][2]))
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
	$infscropts			= get_option("infscr_options");
	//Go through and check for defaults
	foreach(infiniteScroll::$Defaults as $key => $value)
		{
		foreach($value as $inkey => $invalue)
			{
			if(!isset($infscropts[$inkey]))
				$infscropts[$inkey] = $invalue[0];	
			}
		}
	$debug				= (stripslashes($infscropts['infscr_debug'])==1) ? "true" : "false";
	$scheme 			= (is_ssl()) ? "https://" : "http://";
	if(isset($_GET['a']) && $_GET['a']>0)
		{
		$noscheme 			= parse_url(stripslashes($plugin_dir."/ajax-loader.gif"));	
		$loading_text		= "Loading Additional Presets...";
		$donetext			= "No More Presets To Display!";
		$content_selector	= ".infscroll_preset_list";
		$navigation_selector= ".infscroll_preset_nav";
		$post_selector		= ".infscroll_preset_list tr";
		$next_selector		= ".infscroll_preset_nav a:first";
		$js_calls			= "if(jQuery('.infscroll_preset_list tr:last td:first').text()=='No More Presets Available...') { window.setTimeout(function() { jQuery(\".infscroll_preset_list\").infinitescroll(\"destroy\"); }, 10); };";
		$behavior			= 'undefined';
		}
	else
		{
		$noscheme 			= parse_url(stripslashes($infscropts['infscr_image']));
		$loading_text		= infiniteScroll::slashOnlyDouble($infscropts['infscr_text']);
		$donetext			= infiniteScroll::slashOnlyDouble($infscropts['infscr_donetext']);
		$content_selector	= stripslashes($infscropts['infscr_content_selector']);
		$navigation_selector= stripslashes($infscropts['infscr_nav_selector']);
		$post_selector		= stripslashes($infscropts['infscr_post_selector']);
		$next_selector		= stripslashes($infscropts['infscr_next_selector']);
		$js_calls			= stripslashes($infscropts['infscr_js_calls']);
		$behavior			= stripslashes($infscropts['infscr_behavior']);
		}
	$loading_image		= $scheme.$noscheme['host'].$noscheme['path'];	
	//Start Loading!
	//Load infinite-scroll
	echo infiniteScroll::showLicense();
	if($debug=="true")
		{
		echo file_get_contents("js/jquery.infinitescroll.js");
		if($behavior=='twitter')
			echo file_get_contents("js/behaviors/manual-trigger.js");
		echo '//We leave a function outside the infinite-scroll area so that it works with older jQuery versions
			function infinite_scroll_callback(newElements,data) {
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
						currPage	: "'.$pathInfo[0][2].'"
						},
					behavior		: "'.$behavior.'",
					nextSelector    : "'.$next_selector.'",
					navSelector     : "'.$navigation_selector.'",
					contentSelector : "'.$content_selector.'",
					itemSelector    : "'.$post_selector.'",
					pathParse		: ["'.$pathInfo[0][0].'", "'.$pathInfo[0][1].'"]
					}, function(newElements,data) { window.setTimeout(infinite_scroll_callback(newElements,data), 1); } );
					';
			//If its on the admin page and the tab is not active by default, pause it!
			if(isset($_GET['a']) && $_GET['a']==1)
				echo '$("'.$content_selector.'").infinitescroll("pause");';
			echo '});';
			
		}
	else
		{
		echo file_get_contents("js/jquery.infinitescroll.min.js");
		if($behavior=='twitter')
			echo file_get_contents("js/behaviors/manual-trigger.min.js");
		echo 'function infinite_scroll_callback(newElements,data){'.$js_calls.'}
jQuery(document).ready(function($){$("'.$content_selector.'").infinitescroll({debug:'.$debug.',loading:{img:"'.$loading_image.'",msgText:"'.$loading_text.'",finishedMsg:"'.$donetext.'"},state:{currPage:"'.$pathInfo[0][2].'"},behavior:"'.$behavior.'",nextSelector:"'.$next_selector.'",navSelector:"'.$navigation_selector.'",contentSelector:"'.$content_selector.'",itemSelector:"'.$post_selector.'",pathParse:["'.$pathInfo[0][0].'","'.$pathInfo[0][1].'"]},function(){window.setTimeout(infinite_scroll_callback(newElements,data),1);});';
		if(isset($_GET['a']) && $_GET['a']==1)
				echo '$("'.$content_selector.'").infinitescroll("pause");';
		echo '});';
		}	
	}?>