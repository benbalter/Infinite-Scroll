<?php
/*Infinite-Scroll Main Class
Dependencies: Wordpress.*/
class infiniteScroll {
static $Version, $Defaults, $PresetRepo, $PresetDB, $PresetRev;
public function __construct()
	{
	self::$Version = '2.0b2.110818';
	self::$PresetRepo = 'http://plugins.svn.wordpress.org/infinite-scroll/branches/PresetDB/PresetDB.csv.php';
	self::$PresetDB = WP_PLUGIN_DIR."/infinite-scroll/PresetDB.csv.php";
	self::$PresetRev = WP_PLUGIN_DIR."/infinite-scroll/PresetDB.rev.php";
	//Load defaults
	self::$Defaults = array(
		'infscr_state' 				=> array('enabledforadmins',
												'If InfiniteScroll is turned on, off, or in maintenance'),
		'infscr_js_calls' 			=> array('',
												'Javascript to execute when new content loads in'),
		'infscr_image'				=> array(plugins_url('infinite-scroll/ajax-loader.gif'),
												'Loading image'),
		'infscr_text'				=> array('<em>Loading the next set of posts...</em>',
												'Loading text'),
		'infscr_donetext'			=> array('<em>Congratulations, you\'ve reached the end of the internet.</em>', 
												'Completed text'),
		'infscr_content_selector'	=> array('#content',
												'Content Div css selector'),
		'infscr_nav_selector' 		=> array('div.navigation',
												'Navigation Div css selector'),
		'infscr_post_selector'		=> array('#content  div.post',
												'Post Div css selector'),
		'infscr_next_selector'		=> array('div.navigation a:first',
												'Next page Anchor css selector'),
		'infscr_viewed_options'		=> array(false, 
												'Ever Viewed Options Page'),
		'infscr_debug'				=> array('0',
												'Debug Mode'),
		'infscr_image_align'		=> array(1,
												'Loading Image Alignment 0=Left, 1=Centre, 2=Right'));
	//Add to Wordpress
	foreach(self::$Defaults as $key=>$value)
		{
		add_option($key, $value[0], $value[1]);	
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
			wp_register_script( 'jquery', plugins_url('infinite-scroll')."/js/jquery-1.6.2.js", array(), '1.6.2', false );
		else
			wp_register_script( 'jquery', plugins_url('infinite-scroll')."/js/jquery-1.6.2.min.js", array(), '1.6.2', false );
		}
	wp_enqueue_script( 'jquery' );
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
	if (get_option('infscr_state') == 'disabled' || is_page() || is_single() || (get_option('infscr_state') == 'disabledforadmins' && $user_level >= 8) || (get_option('infscr_state') == 'enabledforadmins' && $user_level <= 8) || !have_posts())
		$load_infinite_scroll = false;
		
	/* Pre-flight checks complete. Are we good to fly? */	
	if($load_infinite_scroll)
		{
		/* Loading Infinite-Scroll */
		$plugin_dir 		= plugins_url('infinite-scroll');
		$current_page 		= (get_query_var('paged')) ? get_query_var('paged') : 1;
		
		$nextpage_no 		= intval($current_page) + 1;
		$max_page 			= $wp_query->max_num_pages;
		if ( !$max_page || $max_page >= $nextpage )
			{
			self::addjQuery();
			//We have to pass pathInfo to the script as the script can't determine the path itself. 
			//We have to introduce some form of validation, so we can validate/sign the pathInfo we create.
			$pathParse			= self::getPagenumLink();
			$pathParse[]		= $current_page;
			$pathInfo			= base64_encode(serialize(array($pathParse,md5(NONCE_KEY.$pathParse[0]."infscr".$pathParse[1].self::$Version.$pathParse[2]))));
			wp_register_script( "infinite-scroll-init", "$plugin_dir/infinitescroll.init.js.php?p=$pathInfo", array("jquery"), NULL, false );
			wp_enqueue_script( "infinite-scroll-init" );
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
		header("HTTP/1.1 404 Not Found");
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
//FYI We use this function for a certain part of the presetAdd as we don't want eol.
private static function presetToCSV($data)
    {
    $outstream = fopen("php://temp", 'r+');
    fputcsv($outstream, $data, ',', '"');
    rewind($outstream);
    $csv = fgets($outstream);
    fclose($outstream);
	$csv = substr($csv, 0, (0 - strlen(PHP_EOL)));
    return $csv;
    }
public static function presetAdd($themename,$content,$nav,$post,$next,$overwrite)
	{
	if (($handle = @fopen(self::$PresetDB, "r+")) !== FALSE) {
		//Discard first and second line
		fgets($handle, 4096);
		fgets($handle, 4096);
		$continueparse = true;
		$previouscursor = ftell($handle);
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $continueparse == true) {
			if(isset($data[0])&&$data[0]==$themename)
				{
				if($overwrite==1)
					{
					fseek($handle,$previouscursor);
					fwrite($handle, self::presetToCSV(array($themename,$content,$nav,$post,$next)));
					$continueparse = false;
					}
				else
					return array("ERROR","A preset for this theme already exists!");
				}
			elseif($data[0]=='End Preset DB*/ ?>')
				{
				fseek($handle,$previouscursor);
				fputcsv($handle,array($themename,$_POST['infscr_content_selector'],$_POST['infscr_nav_selector'],$_POST['infscr_post_selector'],$_POST['infscr_next_selector']),",");
				fwrite($handle,'End Preset DB*/ ?>');
				$continueparse = false;
				}
			$previouscursor = ftell($handle);
			}
		fclose($handle);
		//If we're still here then we presume it went okay...
		return array("OK","Preset Added Successfully.");
		}
	else
		return array("ERROR","Preset Database Doesn't Exist. Try Updating From Preset Manager.");
		
	}
public static function presetGet($themename)
	{
	if (($handle = @fopen(self::$PresetDB, "r")) !== FALSE) {
		//Discard first and second line
		fgets($handle, 4096);
		fgets($handle, 4096);
		$continueparse = true;
		$themeinfo = false;
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $continueparse == true) {
			if(isset($data[0])&&$data[0]==$themename)
				{
				$themeinfo = array("name"=>$data[0],"content"=>$data[1],"nav"=>$data[2],"post"=>$data[3],"next"=>$data[4]);
				$continueparse = false;	
				}
			}
		fclose($handle);
		if($themeinfo!=false)
			return array("OK",$themeinfo);
		else
			return array("ERROR","Could not find preset for theme.");
		}
	else
		return array("ERROR","Preset Database Doesn't Exist. Try Updating From Preset Manager.");
		
	}
public static function presetGetAll()
	{
	if (($handle = @fopen(self::$PresetDB, "r")) !== FALSE) {
		//Discard first and second line
		fgets($handle, 4096);
		fgets($handle, 4096);
		$presets = array();
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if(count($data)>2)
				{
				$presets[] = array("name"=>$data[0],"content"=>$data[1],"nav"=>$data[2],"post"=>$data[3],"next"=>$data[4]);
				}
			}
		fclose($handle);
		return array("OK",$presets);
		}
	else
		return array("ERROR","Preset Database Doesn't Exist. Try Updating.");
	}
public static function presetUpdate()
	{
	//First get current rev version
	if (($handle = @fopen(self::$PresetRev, "r")) !== FALSE) {
		//Discard first line
		fgets($handle, 4096);
		$currentrev = fgets($handle, 4096);
    	fclose($handle);
		}
	if(!isset($currentrev))
		$currentrev = 0;
	//Now check SubVersion
	$headresponse = get_headers(self::$PresetRepo,1);
	if($headresponse[0]!='HTTP/1.1 404 Not Found'&&!empty($headresponse['ETag']))
		{
		$etag = trim($headresponse['ETag'],'"');
		$exetag = explode("//",$etag);
		$reporev = $exetag[0];
		if($reporev>$currentrev)
			{
			$newdb = file_get_contents(self::$PresetRepo);
			if(file_put_contents(self::$PresetDB,$newdb))
				{
				if(file_put_contents(self::$PresetRev,"<?php /*Infinite-Scroll Preset DB Rev File. Contains Subversion Rev Info.\n$reporev\nEnd Preset DB Rev File*/ ?>"))
					return array("OK","Successfully Updated Preset Database To Latest Version");
				else
					return array("ERROR","Could not update revision file. Please check that ".WP_PLUGIN_DIR."/infinite-scroll/ is writable.");
				}
			else
				return array("ERROR","Could not update preset file. Please check that ".WP_PLUGIN_DIR."/infinite-scroll/ is writable.");
			}
		else
			{
			return array("OK","You already have the most current version!");	
			}
		}
	else
		return array("ERROR","Could not contact Wordpress repo. Are you behind a Firewall? Couldn't access: ".self::$PresetRepo);
	}
public static function presetExport()
	{
	if(file_exists(self::$PresetDB))
		{
    	header("Content-Description: Preset DB Export");
    	header("Content-Disposition: attachment; filename=PresetDB.csv.php");
   	 	header("Content-Type: text/csv");
		readfile(self::$PresetDB);
		return true;
		}
	else
		return false;
	}
public static function slashOnlyDouble($text)
	{
	return addcslashes(stripslashes($text), '"');
	}

}