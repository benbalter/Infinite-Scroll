<?php
/*Infinite-Scroll Options Class
Dependencies: Wordpress, infiniteScroll.*/
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
public static function selectorsText()
	{
	echo " <p>All CSS selectors are found with the jQuery javascript library. See the <a href=\"http://docs.jquery.com/Selectors\">jQuery CSS Selector documentation</a> for an overview of all possibilities. Single-quotes are not allowed&mdash;only double-quotes may be used.</p>
	    <table class=\"editform infscroll-opttable\" cellspacing=\"0\" >
		  <tbody>
          <tr>
				<th width=\"20%\" >
					<label for=\"themepresets\">Theme Presets:</label>
				</th>
				<td>";
	$presetinfo = infiniteScrollPresets::presetGet(strtolower(get_current_theme()));
	if($presetinfo[0]=='ERROR')
		{
		if($presetinfo[1]=='Could not find preset for theme.')
			echo "<img src=\"".site_url('/wp-includes/images/smilies/icon_cry.gif')."\" alt=\":-(\"/> We don't currently have a preset for your theme. You'll have to try and enter the right selectors manually using their description and default values.";	
		else
			echo $presetinfo[1];	
		}
	else
		{
		echo "We found a preset for your theme: ".get_current_theme();
		echo "<p class=\"submit\">
		<input type='button' name='auto_fill' value='Auto-Fill' />
		<input name='auto_fill_content' type='hidden' value='{$presetinfo[1]['content']}' />
		<input name='auto_fill_post' type='hidden' value='{$presetinfo[1]['post']}' />
		<input name='auto_fill_nav' type='hidden' value='{$presetinfo[1]['nav']}' />
		<input name='auto_fill_next' type='hidden' value='{$presetinfo[1]['next']}' />
	</p>";	
		}
  		echo "</td>
  			<td width=\"50%\">
  			  <p>To help new (or lazy) users, we have a new preset function. We've compiled a list of common themes and the selectors you should use on infinite-scroll for them.</p>
			  </td>
			</tr>
			</tbody>
		</table>";
	}
public static function generalText()
	{
	echo "<p style=\"font-style:italic;\">NOTE: If you haven't already, make sure you choose the correct selectors for your theme in the selectors tab above. This is needed for the plugin to work correctly. If you've tried and it still doesn't work then check out the Help menu in the top right!</p>";	
	}
//We use a little dirty hack to get around the stupid limitations put on the design of the settings pages
//FYI this is closing </td> and opening a new one for our descriptions
public static function addFieldtext($callbackarr)
	{
	$options = get_option('infscr_options');
	if(isset($options[$callbackarr[0]]))
		$value = $options[$callbackarr[0]];
	else
		$value = parent::$Defaults[$callbackarr[1]][$callbackarr[0]][0];
	echo "<input id='infscr_options[$callbackarr[0]]' name='infscr_options[$callbackarr[0]]' size='40' type='text' value='$value' /></td><td width='50%'>".self::getDescription($callbackarr[0])."</td>";	
	}
public static function addFielddropdown($callbackarr)
	{
	$options = get_option('infscr_options');
	if(isset($options[$callbackarr[0]]))
		$value = $options[$callbackarr[0]];
	else
		$value = parent::$Defaults[$callbackarr[1]][$callbackarr[0]][0];
	echo "<select name='infscr_options[$callbackarr[0]]' id='infscr_options[$callbackarr[0]]'>\n";
	foreach(parent::$Defaults[$callbackarr[1]][$callbackarr[0]][3] as $optkey => $optval)
		{
		echo "<option value='$optkey'";
		if ($value == $optkey)
			echo "selected='selected'";
		echo ">$optval</option>\n";	
		}						
	echo "</select></td><td width='50%'>".self::getDescription($callbackarr[0])."</td>";	
	}
public static function addFieldtextarea($callbackarr)
	{
	$options = get_option('infscr_options');
	if(isset($options[$callbackarr[0]]))
		$value = $options[$callbackarr[0]];
	else
		$value = parent::$Defaults[$callbackarr[1]][$callbackarr[0]][0];
	echo "<textarea name='infscr_options[$callbackarr[0]]' rows='2'  style='width: 95%;'>\n";
	echo stripslashes($value);
	echo "</textarea></td><td width='50%'>".self::getDescription($callbackarr[0])."</td>";
	}
public static function addFieldfileupload($callbackarr)
	{
	echo "<input type='file' name='infscr_options[$callbackarr[0]]' id='infscr_options[$callbackarr[0]]' size='30' /></td><td width='50%'>".self::getDescription($callbackarr[0])."</td>";	
	}

public static function loadSettings($settingsgrp, $id, $title, $callback, $page)
	{
	add_settings_section($id, $title, $callback, $page);
	foreach(parent::$Defaults[$settingsgrp] as $key=>$value)
		{
		add_settings_field($key, $value[1], array("infiniteScrollOptions","addField".$value[2]), $page, $id, array($key,$settingsgrp));
		}
	}
public static function validateOptions($input)
	{
	$options = get_option('infscr_options');
	if (isset($input['infscr_state']) && ($input['infscr_state'] == 'enabled' || $input['infscr_state'] == 'disabled' || $input['infscr_state'] == 'disabledforadmins' || $input['infscr_state'] == 'enabledforadmins'))
		$options['infscr_state'] = $input['infscr_state'];
	if (isset($input['infscr_debug']) && ($input['infscr_debug'] == 0 || $input['infscr_debug'] == 1))
		$options['infscr_debug'] = $input['infscr_debug'];
	if (isset($input['infscr_behavior']) && ($input['infscr_behavior'] == 'undefined' || $input['infscr_behavior'] == 'twitter'))
		$options['infscr_behavior'] = $input['infscr_behavior'];
	if (isset($input['infscr_js_calls']))
		$options['infscr_js_calls'] = $input['infscr_js_calls'];
	if (isset($input['infscr_image_align']) && ($input['infscr_image_align'] == 0 || $input['infscr_image_align'] == 1 || $input['infscr_image_align'] == 2))
		$options['infscr_image_align'] = $input['infscr_image_align'];
	if (isset($input['infscr_text']))
		$options['infscr_text'] = $input['infscr_text'];
	if (isset($input['infscr_donetext']))
		$options['infscr_donetext'] = $input['infscr_donetext'];
	if (isset($input['infscr_content_selector']))
		$options['infscr_content_selector'] = esc_js($input['infscr_content_selector']);
	if (isset($input['infscr_nav_selector']))
		$options['infscr_nav_selector'] = esc_js($input['infscr_nav_selector']);
	if (isset($input['infscr_post_selector']))
		$options['infscr_post_selector'] = esc_js($input['infscr_post_selector']);
	if (isset($input['infscr_next_selector']))
		$options['infscr_next_selector'] = esc_js($input['infscr_next_selector']);
	
	/* Handle Image Upload */
	//FYI I do know that the add_settings_error() function does work, but only as of 3.0. I'd rather keep compatability with 2.7.
	if(!empty($_FILES['infscr_options']['tmp_name']['infscr_image']))
		{
		$uploaddetails = wp_check_filetype($_FILES['infscr_options']['name']['infscr_image']);
		if(!empty($uploaddetails['ext']))
			{
			$uploadres = wp_upload_bits("inf-loading-".rand().".".$uploaddetails['ext'], null, file_get_contents($_FILES['infscr_options']['tmp_name']['infscr_image']));
			if(!$uploadres['error'])
				$options['infscr_image'] = $uploadres['url'];	

			}
		}
	return $options;
	}
public static function addOptStyle()
	{
	$plugin_dir 		= plugins_url('infinite-scroll');
	wp_enqueue_style( "infinite-scroll-admin-style" , "$plugin_dir/includes/admin.css", false, parent::$Version, false );
	}
public static function addOptJavascript()
	{
	parent::addjQuery();
	if(self::pageActive("presets","tab")=="")
		$presetdefault = 1;
	else
		$presetdefault = 2;
	if(!empty($_GET['infpage'])&&((int) $_GET['infpage']) == $_GET['infpage']&&$_GET['infpage']>=0)
		$infscr_preset_page = $_GET['infpage'];	
	else
		$infscr_preset_page = 1;
	$plugin_dir 		= plugins_url('infinite-scroll');
	$pathParse			= array("options-general.php?page=wp_infinite_scroll.php&infpage=","",$infscr_preset_page);
	$pathInfo			= base64_encode(serialize(array($pathParse,md5(NONCE_KEY.$pathParse[0]."infscr".$pathParse[1].					parent::$Version.$pathParse[2]))));	
	wp_enqueue_script( "infinite-scroll-init", "$plugin_dir/infinitescroll.init.js.php?p=$pathInfo&a=$presetdefault", array("jquery"), NULL, false );
	wp_enqueue_script( "infinite-scroll-admin", "$plugin_dir/js/admin_options.js", array("jquery", "infinite-scroll-init"), parent::$Version, false );		
	}
public static function addOptPage()
	{
	$plugin_dir 		= plugins_url('infinite-scroll');
	$currentopts		= get_option('infscr_options');

	//Check if user wants to add preset
	if(isset($_POST['preset_add']))
		{
		if(isset($_POST['preset_overwrite']))
			$overwrite = 1;
		else
			$overwrite = 0;
		$addresult = infiniteScrollPresets::presetAdd($_POST['preset_themename'],$_POST['preset_content'],$_POST['preset_nav'],$_POST['preset_posts'],$_POST['preset_next'],$overwrite);	
		if($addresult[0]=='OK')
			{
			echo "<div class='updated'><p><strong>{$addresult[1]}</strong></p></div>";
			}
		else
			echo parent::showError($addresult[1]);
		}
	//Update that they have viewed options
	if(!$currentopts['infscr_viewed_options'])
		{
		$currentopts['infscr_viewed_options'] = true;
		update_option("infscr_options", $currentopts);
		}
	//Check if user wants to update preset db
	if(isset($_GET['presetup'])&&$_GET['presetup']==1)
		{
		$updateresult = infiniteScrollPresets::presetUpdate();
		if($updateresult[0]=='OK')
			{
			echo "<div class='updated'><p><strong>{$updateresult[1]}</strong></p></div>";
			}
		else
			echo parent::showError($updateresult[1]);	
		}
	
	if($currentopts['infscr_state'] == 'disabled')
		echo parent::showError("Infinite-Scroll plugin is <strong>disabled</strong>.");

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
		$a["elements"] = "'.'infscr_options[infscr_text]'.','.'infscr_options[infscr_donetext]'.'";
		$a["editor_selector"] = "mceEditor";
		$a["plugins"] = "safari,inlinepopups,spellchecker";
		return $a;'));
	
	 	wp_tiny_mce(true);
		}
	echo '<div class="wrap">
    <form name="infinitescrollform" action="options.php" method="post" enctype="multipart/form-data">';
    settings_fields('infinitescroll');
    echo '<h2>Infinite Scroll Options</h2>
    <h2 class="nav-tab-wrapper">
<a href="options-general.php?page=wp_infinite_scroll.php&default=general" class="nav-tab'.self::pageActive("general","nav").'" rel="general">General</a><a href="options-general.php?page=wp_infinite_scroll.php&default=selectors" class="nav-tab'.self::pageActive("selectors","nav").'" rel="selectors">Selectors</a><a href="options-general.php?page=wp_infinite_scroll.php&default=presets" class="nav-tab'.self::pageActive("presets","nav").'" rel="presets" style="float:right;">Preset Manager</a>
</h2>';
	echo '<div class="infscroll-tab infscroll-tab-general'.infiniteScrollOptions::pageActive("general","tab").'">';
	do_settings_sections('infiniteScrollGeneral');
	echo '<p class="submit" style="text-align:center;">
			<input name="Submit" type="submit" class="button-primary" value="';
	esc_attr_e('Save Changes');
	echo '" />
	</p>
    </div>';
	echo '<div class="infscroll-tab infscroll-tab-selectors'.infiniteScrollOptions::pageActive("selectors","tab").'">';
    do_settings_sections('infiniteScrollSelectors');
	echo '<p class="submit" style="text-align:center;">
		<input name="Submit" type="submit" class="button-primary" value="';
	esc_attr_e('Save Changes');
	echo '" />
	</p>
    </div>';
    echo "</form>";
	include("presets.tab.php");
	echo "</div>";
	}
public static function getDescription($key)
	{
	switch($key)
		{
		case 'infscr_state':
			$description = '"ON for Admins Only" will enable the plugin code only for logged-in administrators&mdash;visitors will not be affected while you configure the plugin. "ON for Visitors Only" is useful for administrators when customizing the blog&mdash;infinite scroll will be disabled for them, but still enabled for any visitors.';	
		break;
		case 'infscr_debug':
			$description = 'ON will turn on Debug mode. This will enable developer javascript console logging whilst in use. (Recommended: OFF, May break some browsers).';	
		break;
		case 'infscr_behavior':
			$description = 'Automatic behavior is the default behavior used by infinite scroll, once the user reaches the end of the page, it\'ll load the next set of posts. Manual triggering turns off automatic loading, it won\'t use AJAX to load any more posts until the user clicks the link/button for more posts.';	
		break;
		case 'infscr_js_calls':
			$description = 'Any functions that are applied to the post contents on page load will need to be executed when the new content comes in.';	
		break;
		case 'infscr_image':
			$options = get_option('infscr_options');
			$description = 'Current Image:<br /><div style="text-align:center;margin-bottom:15px;"><img src="'.$options['infscr_image'].'" alt="The Loading Image" /></div>
<p>URL of image that will be displayed while content is being loaded. Visit <a href="http://www.ajaxload.info" target="_blank">www.ajaxload.info</a> to customize your own loading spinner.</p>';	
		break;
		case 'infscr_image_align':
			$description = '';	
		break;
		case 'infscr_text':
			$description = 'Text will be displayed while content is being loaded.';	
		break;
		case 'infscr_donetext':
			$description = 'Text will be displayed when all entries have already been retrieved. The plugin will show this message, fade it out, and cease working.';	
		break;
		case 'infscr_content_selector':
			$description = 'The selector of the content div on the main page.';	
		break;
		case 'infscr_post_selector':
			$description = '<p>The selector of the post block.</p>
				  <dl>
				    <dt>Examples:</dt>
				    <dd>#content &gt; *</dd>
				    <dd>#content div.post</dd>
				    <dd>div.primary div.entry</dd>
			    </dl>';	
		break;
		case 'infscr_nav_selector':
			$description = 'The selector of the navigation div (the one that includes the next and previous links).';	
		break;
		case 'infscr_next_selector':
			$description = '<p>The selector of the previous posts (next page) A tag.</p>
				  <dl>
				    <dt>Examples:</dt>
				    <dd>div.navigation a:first</dd>
				    <dd>div.navigation a:contains(Previous)</dd>
			    </dl>';	
		break;
		default:
			$description = '';
		break;
		}
	return $description;
	}
}