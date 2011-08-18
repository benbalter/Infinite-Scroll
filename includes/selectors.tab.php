<div class="infscroll-tab infscroll-tab-selectors<?php echo infiniteScrollOptions::pageActive("selectors","tab");?>">
    	  <p>All CSS selectors are found with the jQuery javascript library. See the <a href="http://docs.jquery.com/Selectors">jQuery CSS Selector documentation</a> for an overview of all possibilities. Single-quotes are not allowed&mdash;only double-quotes may be used.</p>
    <table class="editform infscroll-opttable" cellspacing="0" >
		  <tbody>
          <tr>
				<th width="20%" >
					<label for="themepresets">Theme Presets:</label>
				</th>
				<td>
					<?php
					$presetinfo = infiniteScroll::presetGet(strtolower(get_current_theme()));
					if($presetinfo[0]=='Error')
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
					?>
  			</td>
  			<td width="50%">
  			  <p>To help new (or lazy) users, we have a new preset function. We've compiled a list of common themes and the selectors you should use on infinite-scroll for them.</p>
			  </td>
			</tr>
			<tr>
				<th>
					<label for="<?php echo 'infscr_content_selector'; ?>">Content CSS Selector:</label>
				</th>
				<td>
					<?php
						echo "<input name='".'infscr_content_selector'."' id='".'infscr_content_selector'."' value='".stripslashes(get_option('infscr_content_selector'))."' size='30' type='text'>\n";
					?>
  			</td>
  			<td>
  			  <p>The selector of the content div on the main page.</p>
			  </td>
			</tr>
			  
			<tr>
				<th >
					<label for="<?php echo 'infscr_post_selector'; ?>">Post CSS Selector:</label>
				</th>
				<td>
					<?php
						echo "<input name='".'infscr_post_selector'."' id='".'infscr_post_selector'."' value='".stripslashes(get_option('infscr_post_selector'))."' size='30' type='text'>\n";
					?>
				</td>
				<td>
				  <p>The selector of the post block.</p>
				  <dl>
				    <dt>Examples:</dt>
				    <dd>#content &gt; *</dd>
				    <dd>#content div.post</dd>
				    <dd>div.primary div.entry</dd>
			    </dl>
			  </td>
			</tr>
			  
			<tr>
				<th>
					<label for="<?php echo 'infscr_nav_selector'; ?>">Navigation Links CSS Selector:</label>
				</th>
				<td>
					<?php
						echo "<input name='".'infscr_nav_selector'."' id='".'infscr_nav_selector'."' value='".stripslashes(get_option('infscr_nav_selector'))."' size='30' type='text'>\n";
					?>
			
				</td>
				<td>
			  	<p>The selector of the navigation div (the one that includes the next and previous links).</p>
			  </td>
			</tr>			

			<tr>
				<th>
					<label for="<?php echo 'infscr_next_selector'; ?>">Previous posts CSS Selector:</label>
				</th>
				<td>
					<?php
						echo "<input name='".'infscr_next_selector'."' id='".'infscr_next_selector'."' value='".stripslashes(get_option('infscr_next_selector'))."' size='30' type='text'>\n";
					?>
				</td>
				<td>
				  <p>The selector of the previous posts (next page) A tag.</p>
				  <dl>
				    <dt>Examples:</dt>
				    <dd>div.navigation a:first</dd>
				    <dd>div.navigation a:contains(Previous)</dd>
			    </dl>
			  </td>
			</tr>
			</tbody>
		</table>
    	<p class="submit" style="text-align:center;">
		<input type='submit' name='info_update' value='Update Options' /><br /><br />OR<br /><br /><input type='submit' name='preset_add' value='Add to Preset DB' /><br /><label for="preset_overwrite">Overwrite Existing Theme Preset: </label><input name="preset_overwrite" id="preset_overwrite" type="checkbox" value="1" />
	</p>
    </div>