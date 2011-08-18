<?php
/*
Plugin Name: Infinite Scroll
Version: 2.0b2.110723
Plugin URI: http://www.infinite-scroll.com
Description: Automatically loads the next page of posts into the bottom of the initial page. 
Author: Beaver6813, dirkhaim, Paul Irish
Author URI: http://www.infinite-scroll.com
License   : http://creativecommons.org/licenses/GPL/2.0/
*/
include("includes/infinite-scroll.class.php");
new infiniteScroll();
//Add Actions
add_action('template_redirect'	, create_function('', "infiniteScroll::addInfiniteScroll();"));
add_action('wp_head'			, create_function('', "infiniteScroll::addStyle();"));
add_action('admin_menu'			, 'add_wp_inf_scroll_options_page');
/*Because recently (3.0) WP doesn't always throw a 404 when posts aren't found.
Infinite-Scroll relies on 404 errors to terminate.. so we'll force them. */
add_action("wp"					, create_function('', "infiniteScroll::trigger404();"));

if ( get_option('infscr_state') == infscr_state_default && get_option(key_infscr_viewed_options) == false && !isset($_POST['submit']) )
	add_action('admin_notices', create_function('', "echo infiniteScroll::showSetupWarning();"));	

function add_wp_inf_scroll_options_page() 
	{
	$optionspage = add_options_page('Infinite Scroll Options', 'Infinite Scroll', 8, basename(__FILE__), 'wp_inf_scroll_options_page');
	add_action("load-".$optionspage, create_function('', "infiniteScroll::addjQuery();"));
	
	$loadhelp = str_replace("{INFSCROLL_VERSION}",infiniteScroll::$Version,file_get_contents(WP_PLUGIN_DIR."/infinite-scroll/includes/helpinfo.html"));
	add_contextual_help($optionspage,$loadhelp);
	}
	
function wp_inf_scroll_options_page()
	{
	check_admin_referer();
	include("includes/options.class.php");
	$plugin_dir 		= plugins_url('infinite-scroll');
	
	if(!empty($_GET['infpage'])&&((int) $_GET['infpage']) == $_GET['infpage']&&$_GET['infpage']>=0)
		$infscr_preset_page = $_GET['infpage'];	
	else
		$infscr_preset_page = 1;
	
	$pathParse			= array("options-general.php?page=".basename(__FILE__)."&infpage=","",$infscr_preset_page);
	$pathInfo			= base64_encode(serialize(array($pathParse,md5(NONCE_KEY.$pathParse[0]."infscr".$pathParse[1].					infiniteScroll::$Version.$pathParse[2]))));
	if(infiniteScrollOptions::pageActive("presets","tab")=="")
		$presetdefault = 1;
	else
		$presetdefault = 2;
	//If theres POST, update the options
	if (isset($_POST['info_update']))
		echo infiniteScrollOptions::updateOptions();
	//Check if user wants to add preset
	if(isset($_POST['preset_add']))
		{
		$addresult = infiniteScroll::presetAdd(strtolower(get_current_theme()),$_POST['infscr_content_selector'],$_POST['infscr_nav_selector'],$_POST['infscr_post_selector'],$_POST['infscr_next_selector'],$_POST['preset_overwrite']);	
		if($addresult[0]=='OK')
			{
			echo "<div class='updated'><p><strong>{$addresult[1]}</strong></p></div>";
			}
		else
			echo infiniteScroll::showError($addresult[1]);
		}
	//Update that they have viewed options
	update_option("infscr_viewed_options", true);
	//Check if user wants to update preset db
	if(isset($_GET['presetup'])&&$_GET['presetup']==1)
		{
		$updateresult = infiniteScroll::presetUpdate();
		if($updateresult[0]=='OK')
			{
			echo "<div class='updated'><p><strong>{$updateresult[1]}</strong></p></div>";
			}
		else
			echo infiniteScroll::showError($updateresult[1]);	
		}
	
	if (get_option('infscr_state') == 'disabled')
		echo infiniteScroll::showError("Infinite-Scroll plugin is <strong>disabled</strong>.");

	if (function_exists('wp_tiny_mce')) {
	  	add_filter('teeny_mce_buttons', create_function('$a', "
		return array('bold, italic, underline, separator, strikethrough, justifyleft, justifycenter, justifyright, code');"));
	  	add_filter('teeny_mce_before_init', create_function('$a', '
		$a["theme"] = "advanced";
		$a["skin"] = "wp_theme";
		$a["height"] = "50";
		$a["width"] = "240";
		$a["onpageload"] = "";
		$a["mode"] = "exact";
		$a["elements"] = "'.'infscr_donetext'.','.'infscr_text'.'";
		$a["editor_selector"] = "mceEditor";
		$a["plugins"] = "safari,inlinepopups,spellchecker";
		return $a;'));
	
	 	wp_tiny_mce(true);
		}
	echo infiniteScrollOptions::addOptStyle();
	echo "<script type=\"text/javascript\" src=\"$plugin_dir/infinitescroll.init.js.php?p=$pathInfo&a=$presetdefault\"></script>
	<script type=\"text/javascript\" src=\"$plugin_dir/js/admin_options.js\"></script>";
?>
	<div class="wrap">
    <form action="options-general.php?page=<?php echo basename(__FILE__); ?>" method="post" enctype="multipart/form-data">
    <h2>Infinite Scroll Options</h2>
    <h2 class="nav-tab-wrapper">
<a href="options-general.php?page=<?php echo basename(__FILE__); ?>&default=general" class="nav-tab<?php echo infiniteScrollOptions::pageActive("general","nav");?>" rel="general">General</a><a href="options-general.php?page=<?php echo basename(__FILE__); ?>&default=selectors" class="nav-tab<?php echo infiniteScrollOptions::pageActive("selectors","nav");?>" rel="selectors">Selectors</a><a href="options-general.php?page=<?php echo basename(__FILE__); ?>&default=presets" class="nav-tab<?php echo infiniteScrollOptions::pageActive("presets","nav");?>" rel="presets" style="float:right;">Preset Manager</a>
</h2>
<?php
	include("includes/general.tab.php");
	include("includes/selectors.tab.php");
    echo "</form>";
	include("includes/presets.tab.php");
	echo "</div>";
	}?>