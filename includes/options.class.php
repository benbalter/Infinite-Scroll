<?php
/*Infinite-Scroll Options Class
Dependencies: Wordpress.*/
class infiniteScrollOptions extends infiniteScroll {

public static function pageActive($tab,$mode)
	{
	if(empty($_GET['default']))
		$default = "general";
	else
		$default = $_GET['default'];
	if($default==$tab)
		{
		if($mode=="nav")
			return " nav-tab-active";
		else
			return " infscroll-tab-active";
		}
	return "";
	}

public static function matchDefault($default)
	{
	switch($default)
		{
		case 'general':
		case 'selectors':
		case 'presets':
		return $default;
		break;
		
		default:
		return "";
		break;
		}
	}

public static function updateOptions()
	{	
	// update state
	$infscr_state = $_POST['infscr_state'];
	if ($infscr_state != 'enabled' && $infscr_state != 'disabled' && $infscr_state != 'disabledforadmins' && $infscr_state != 'enabledforadmins')
		$infscr_state = parent::$Defaults['infscr_state'][0];
	update_option('infscr_state', esc_js($infscr_state));
	// update debug
	$infscr_debug = $_POST['infscr_debug'];
	update_option('infscr_debug', esc_js($infscr_debug));
	// update js calls field
	$infscr_js_calls = $_POST['infscr_js_calls'];
	update_option('infscr_js_calls', $infscr_js_calls);
	// update image
	/* Handle Image Upload */
	if(!empty($_FILES['infscr_image']['tmp_name']))
		{
		$infscr_image = $_FILES['infscr_image'];
		$uploaddetails = wp_check_filetype($infscr_image["name"]);
		if(!empty($uploaddetails['ext']))
			{
			$uploadres = wp_upload_bits("inf-loading-".rand().".".$uploaddetails['ext'], null, file_get_contents($infscr_image["tmp_name"]));
			if(!$uploadres['error'])
				update_option('infscr_image', $uploadres['url']);	
			else
				return parent::showError("Error Saving Loading Bar: {$uploadres['error']}");	
			}
		else
			{
			return parent::showError("Could Not Determine File Extension. Supported Files Are: .jpg, .jpeg. gif. .png");
			}
		}
	// update image alignment
	$infscr_image_align = $_POST['infscr_image_align'];
	if ($infscr_image_align != 0 && $infscr_image_align != 1 && $infscr_image_align != 2)
		$infscr_image_align = 1;
	update_option('infscr_image_align', esc_js($infscr_image_align));
	// update text 
	$infscr_text = $_POST['infscr_text'];
	update_option('infscr_text', $infscr_text);
	// update done text 
	$infscr_donetext = $_POST['infscr_donetext'];
	update_option('infscr_donetext', $infscr_donetext);
	// update content selector
	$content_selector = $_POST['infscr_content_selector'];
	update_option('infscr_content_selector', esc_js($content_selector));
	// update the navigation selector
	$navigation_selector = $_POST['infscr_nav_selector'];
	update_option('infscr_nav_selector', esc_js($navigation_selector));
	// update the post selector
	$post_selector = $_POST['infscr_post_selector'];
	update_option('infscr_post_selector', esc_js($post_selector));
	// update the next selector
	$next_selector = $_POST['infscr_next_selector'];
	update_option('infscr_next_selector', esc_js($next_selector));
	// update notification
	return "<div class='updated'><p><strong>Infinite Scroll options updated</strong></p></div>";		
	}

public static function addOptStyle()
	{
	return "<style type=\"text/css\">
table.infscroll-opttable { width: 100%;}
table.infscroll-opttable td, table.infscroll-opttable th { vertical-align: top; padding: 9px 4px;  }
table.infscroll-opttable th { padding-top: 9px; text-align: right;}
table.infscroll-opttable td p { margin: 0;}
table.infscroll-opttable dl { font-size: 90%; color: #666; margin-top: 5px; }
table.infscroll-opttable dd { margin-bottom: 0 }
.infscroll-tab { display:none; }
.infscroll-tab-active { display:block; }
.infscroll_preset_nav { text-align: right; padding-top: 25px;}
</style>";	
	}
}