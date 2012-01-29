<?php
/*Infinite-Scroll Main Class
Dependencies: Wordpress.*/
class infiniteScroll {
static $Version, $Defaults, $PresetRepo, $PresetDB, $PresetRev;
public function __construct()
	{
	self::$Version = '2.0b2.120111';
	self::$PresetRepo = 'http://plugins.svn.wordpress.org/infinite-scroll/branches/PresetDB/PresetDB.csv.php';
	self::$PresetDB = WP_PLUGIN_DIR."/infinite-scroll/PresetDB.csv.php";
	self::$PresetRev = WP_PLUGIN_DIR."/infinite-scroll/PresetDB.rev.php";
	//Load defaults
	self::$Defaults = array(
		'general'	=> array(
			'infscr_state' 				=> array('enabledforadmins',"Infinite Scroll State Is","dropdown",array("disabled"=>"OFF","disabledforadmins"=>"ON for Visitors Only","enabledforadmins"=>"ON for Admins Only","enabled"=>"ON")),
			'infscr_debug'				=> array('0',"Debug Mode","dropdown",array(0=>"OFF",1=>"ON")),
			'infscr_behavior'			=> array('undefined',"Scrolling Behavior","dropdown",array('undefined'=>"Automatic",'twitter'=>"Manual Triggering")),
			'infscr_js_calls' 			=> array('',"Javascript to be called after the next posts are fetched","textarea"),
			'infscr_image'				=> array(plugins_url('infinite-scroll/ajax-loader.gif'),"Loading Image","fileupload"),
			'infscr_image_align'		=> array(1,"Loading Image Align","dropdown",array(0=>"Left",1=>"Centre",2=>"Right")),
			'infscr_text'				=> array('<em>Loading the next set of posts...</em>',"Loading Text","textarea"),
			'infscr_donetext'			=> array('<em>Congratulations, you\'ve reached the end of the internet.</em>',"\"You've reached the end\" text","textarea")),
		'selectors'	=> array(
			'infscr_content_selector'	=> array('#content',"Content Selector","text"),
			'infscr_post_selector'		=> array('#content  div.post',"Posts Selector","text"),
			'infscr_nav_selector' 		=> array('div.navigation',"Navigation Selector","text"),
			'infscr_next_selector'		=> array('div.navigation a:first',"Next Page Selector","text")),
		'misc' 		=> array(
			'infscr_viewed_options'		=> array(false,false,false,false))
		);	
	}
public static function addDefaults()
	{
	$tmp = get_option('infscr_options');
    if(!is_array($tmp)) 
		{
		//infscr_options doesn't exist, check for legacy
		$stateopts = get_option('infscr_state');
		if(!empty($stateopts))
			{
			//We have legacy! Lets run an import
			$legacyarray = array();
			foreach(self::$Defaults as $key => $value)
				{
				foreach($value as $innerkey => $innerval)
					{
					$legacyarray[$innerkey] = get_option($innerkey);
					}
				}
			update_option('infscr_options', $legacyarray);
			//We'll now check that that was successful and if it was, remove the legacy variables
			//If it didn't work then we'll leave them for now
			$tmp = get_option('infscr_options');
			if(is_array($tmp)) {
				foreach(self::$Defaults as $key => $value)
					{
					foreach($value as $innerkey => $innerval)
						{
						delete_option($innerkey);
						}
					}
				}	
			}
		else
			{
			//If there are no legacy variables
			$newsettings = array();
			foreach(self::$Defaults as $key => $value)
				{
				foreach($value as $innerkey => $innerval)
					{
					$newsettings[$innerkey] = $innerval[0];
					}
				}
			update_option('infscr_options', $newsettings);
			}
		}		
	}
public static function addjQuery()
	{
	global $wp_scripts;
	//Now, some of the versions of jQuery bundled with Wordpress are monolithic.
	//Lets check how old they are!
	//Check if jQuery registered and its version. If not then we'll just pretend its
	//an old version.
	if(!empty($wp_scripts->registered['jquery']->ver))
		$versioncode = explode(".",$wp_scripts->registered['jquery']->ver);
	else
		$versioncode = array(1,1,1);
	//Lets check the main branch, we won't be *that* picky!
	if($versioncode[1]<6)
		{
		wp_deregister_script( 'jquery' );
		//IMPORTANT. Our versions of jQuery, like Wordpress, also append jQuery.noConflict();
		if((stripslashes(get_option('infscr_debug'))==1))
			wp_enqueue_script( 'jquery', plugins_url('infinite-scroll')."/js/jquery-1.6.2.js", array(), '1.6.2', false );
		else
			wp_enqueue_script( 'jquery', plugins_url('infinite-scroll')."/js/jquery-1.6.2.min.js", array(), '1.6.2', false );
		}
	return true;	
	}

public static function addStyle()
	{
	switch(get_option('infscr_image_align'))
		{
		default:
		case 0:
			$align = "left";
		break;
		case 1:
			$align = "center";
		break;
		case 2:
			$align = "right";
		break;
		}
	echo "<!-- Infinite-Scroll Style -->
	<style type=\"text/css\">
	 #infscr-loading img { text-align: $align; }
	</style>\r\n";
	return true;	
	}

public static function addInfiniteScroll()
	{
	global $user_level,$wp_query;
	$load_infinite_scroll = true;	
	/* Lets start our pre-flight checks */
	if(is_page() || is_single() || !have_posts())
		$load_infinite_scroll = false;
	
	$load_infinite_scroll = apply_filters('infinite_scroll_load_override', $load_infinite_scroll);
	
	if (get_option('infscr_state') == 'disabled' || (get_option('infscr_state') == 'disabledforadmins' && $user_level >= 8) || (get_option('infscr_state') == 'enabledforadmins' && $user_level <= 8))
		$load_infinite_scroll = false;
		
	/* Pre-flight checks complete. Are we good to fly? */	
	if($load_infinite_scroll)
		{
		/* Loading Infinite-Scroll */
		$plugin_dir 		= plugins_url('infinite-scroll');
		$current_page 		= (get_query_var('paged')) ? get_query_var('paged') : 1;
		
		$nextpage_no 		= intval($current_page) + 1;
		$max_page 			= $wp_query->max_num_pages;
		if ( !$max_page || $max_page >= $nextpage_no )
			{
			self::addjQuery();
			//We have to pass pathInfo to the script as the script can't determine the path itself. 
			//We have to introduce some form of validation, so we can validate/sign the pathInfo we create.
			$pathParse			= self::getPagenumLink();
			$pathParse[]		= $current_page;
			$pathInfo			= base64_encode(serialize(array($pathParse,md5(NONCE_KEY.$pathParse[0]."infscr".$pathParse[1].self::$Version.$pathParse[2]))));
			wp_enqueue_script( "infinite-scroll-init" , "$plugin_dir/infinitescroll.init.js.php?p=$pathInfo", array("jquery"), NULL, false );
			return true;
			}		
		}
	return false;		
	}

public static function trigger404()
	{
	$paged 	= (get_query_var('paged')) ? get_query_var('paged') : 1;
	if (($paged && $paged > 1) && !have_posts())
		{
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
		header("Status: 404 Not Found");
		}	
	}

public static function showError($message)
	{
	return "<div class=\"error\"><p>$message</p></div>\n";		
	}
	
public static function showSetupWarning()
	{
	return "<div id='infinitescroll-warning' class='updated fade'><p><strong>Infinite Scroll is almost ready</strong> Please <a href=\"options-general.php?page=wp_infinite_scroll.php\">review the configuration and set the state to ON</a></p></div>\n";	
	}

public static function showLicense()
	{
	return "/*
	--------------------------------
	Infinite Scroll Wordpress Plugin
	--------------------------------
	+ http://wordpress.org/extend/plugins/infinite-scroll/
	+ version ".self::$Version."
	+ Copyright 2011 Beaver6813, dirkhaim, Paul Irish
	+ Licensed under the GPLv2 License
	
	+ Documentation: http://infinite-scroll.com/
	
*/
";	
	}
public static function getPagenumLink()
	{
	global $wp_rewrite;

	$request = remove_query_arg( 'paged' );

	$home_root = parse_url(home_url());
	$home_root = ( isset($home_root['path']) ) ? $home_root['path'] : '';
	$home_root = preg_quote( trailingslashit( $home_root ), '|' );

	$request = preg_replace('|^'. $home_root . '|', '', $request);
	$request = preg_replace('|^/+|', '', $request);

	if ( !$wp_rewrite->using_permalinks() || is_admin() ) {
		$base = trailingslashit( get_bloginfo( 'url' ) );
		$result = add_query_arg( 'paged', "|||INF-SPLITHERE|||", $base . $request );
	} else {
		$qs_regex = '|\?.*?$|';
		preg_match( $qs_regex, $request, $qs_match );

		if ( !empty( $qs_match[0] ) ) {
			$query_string = $qs_match[0];
			$request = preg_replace( $qs_regex, '', $request );
		} else {
			$query_string = '';
		}

		$request = preg_replace( "|$wp_rewrite->pagination_base/\d+/?$|", '', $request);
		$request = preg_replace( '|^index\.php|', '', $request);
		$request = ltrim($request, '/');

		$base = trailingslashit( get_bloginfo( 'url' ) );

		if ( $wp_rewrite->using_index_permalinks() && '' != $request )
			$base .= 'index.php/';

		$request = ( ( !empty( $request ) ) ? trailingslashit( $request ) : $request ) . user_trailingslashit( $wp_rewrite->pagination_base . "/" . "|||INF-SPLITHERE|||", 'paged' );
		$result = $base . $request . $query_string;
	}
	$result = apply_filters('get_pagenum_link', $result);
	return explode("|||INF-SPLITHERE|||",$result);
	}
public static function initOptions()
	{
	include("options.class.php");
	include("presets.class.php");
	//Load Settings
	register_setting('infinitescroll', 'infscr_options', array('infiniteScrollOptions','validateOptions'));
	infiniteScrollOptions::loadSettings("general",'general_section', '', array('infiniteScrollOptions','generalText'), "infiniteScrollGeneral");
	infiniteScrollOptions::loadSettings("selectors",'selectors_section', '', array('infiniteScrollOptions','selectorsText'), "infiniteScrollSelectors");	
	//Add setup warning if required
	$currentopts		= get_option('infscr_options');
	if ( $currentopts['infscr_state'] == self::$Defaults['general']['infscr_state'][0] && $currentopts['infscr_viewed_options'] == false  && !isset($_POST['submit']) )
		add_action('admin_notices', array('infiniteScroll', 'showSetupWarning'));	
	}
public static function addOptPageLoader()
	{
	$optionspage = add_options_page('Infinite Scroll Options', 'Infinite Scroll', 'manage_options', 'wp_infinite_scroll.php', array('infiniteScrollOptions', 'addOptPage'));
	add_action("load-".$optionspage, array('infiniteScrollOptions', 'addOptJavascript'));
	add_action('admin_print_styles-'.$optionspage, array('infiniteScrollOptions', 'addOptStyle') );
	add_contextual_help($optionspage,str_replace("{INFSCROLL_VERSION}",self::$Version,file_get_contents(WP_PLUGIN_DIR."/infinite-scroll/includes/helpinfo.html")));	
	}
public static function slashOnlyDouble($text)
	{
	return addcslashes(stripslashes($text), '"');
	}

}