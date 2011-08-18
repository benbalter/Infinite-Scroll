<div class="infscroll-tab infscroll-tab-general<?php echo infiniteScrollOptions::pageActive("general","tab");?>">
	  <p style="font-style:italic;">NOTE: If you haven't already, make sure you choose the correct selectors for your theme in the selectors tab above. This is needed for the plugin to work correctly. If you've tried and it still doesn't work then check out the Help menu in the top right!</p>

		<table class="editform infscroll-opttable" cellspacing="0" >
		  <tbody>
			<tr>
				<th width="20%" >
					<label for="<?php echo 'infscr_state'; ?>">Infinite Scroll state is:</label>
				</th>
				<td>
					<?php
						echo "<select name='".'infscr_state'."' id='".'infscr_state'."'>\n";
						echo "<option value='".'disabled'."'";
						if (get_option('infscr_state') == 'disabled')
							echo "selected='selected'";
						echo ">OFF</option>\n";
						
						echo "<option value='".'disabledforadmins'."'";
						if (get_option('infscr_state') == 'disabledforadmins')
							echo "selected='selected'";
						echo ">ON for Visitors Only</option>\n";
						
						echo "<option value='".'enabledforadmins'."'";
						if (get_option('infscr_state') == 'enabledforadmins')
							echo "selected='selected'";
						echo ">ON for Admins Only</option>\n";
						
						echo "<option value='".'enabled'."'";
						if (get_option('infscr_state') == 'enabled')
							echo "selected='selected'";
						echo ">ON</option>\n";
						
						echo "</select>";
					?>
				</td>
	      <td width="50%">
	        "ON for Admins Only" will enable the plugin code only for logged-in administrators&mdash;visitors will not be affected while you configure the plugin. "ON for Visitors Only" is useful for administrators when customizing the blog&mdash;infinite scroll will be disabled for them, but still enabled for any visitors. 
        </td>
			</tr>
<tr>
				<th width="30%" >
					<label for="<?php echo 'infscr_debug'; ?>">Debug Mode:</label>
				</th>
				<td>
					<?php
						echo "<select name='".'infscr_debug'."' id='".'infscr_debug'."'>\n";
						echo "<option value='0'";
						if (get_option('infscr_debug') == 0)
							echo "selected='selected'";
						echo ">OFF</option>\n";
						
						echo "<option value='1'";
						if (get_option('infscr_debug') == 1)
							echo "selected='selected'";
						echo ">ON</option>\n";
						
						echo "</select>";
					?>
				</td>
	      <td width="50%">
	        ON will turn on Debug mode. This will enable developer javascript console logging whilst in use. (Recommended: OFF, May break some browsers).
        </td>
			</tr> 
			<tr>
				<th>
					<label for="<?php echo 'infscr_js_calls'; ?>">Javascript to be called after the next posts are fetched:</label>
				</th>
				<td>
					<?php
						echo "<textarea name='".'infscr_js_calls'."' rows='2'  style='width: 95%;'>\n";
						echo stripslashes(get_option('infscr_js_calls'));
						echo "</textarea>\n";
					?>
				</td>
				<td>
				  <p>Any functions that are applied to the post contents on page load will need to be executed when the new content comes in.</p>
		    </td>
			</tr>

			<tr>
				<th>
					<label for="<?php echo 'infscr_image'; ?>">Loading image:</label><br />
                    <label for="<?php echo 'infscr_image_align'; ?>">Loading image align:</label>
				</th>
				<td>
					<?php
						echo "<input type='file' name='".'infscr_image'."' id='".'infscr_image'."' size='30' />\n<br/>";
						echo "<select name='".'infscr_image_align'."' id='".'infscr_image_align'."'>\n";
						echo "<option value='0'";
						if (get_option('infscr_image_align') == 0)
							echo "selected='selected'";
						echo ">Left</option>\n";
						
						echo "<option value='1'";
						if (get_option('infscr_image_align') == 1)
							echo "selected='selected'";
						echo ">Centre</option>\n";
						
						echo "<option value='2'";
						if (get_option('infscr_image_align') == 2)
							echo "selected='selected'";
						echo ">Right</option>\n";
						
						echo "</select>";
					?>
				</td>
                <td>Current Image:<br /><div style="text-align:center;margin-bottom:15px;"><img src="<?php echo stripslashes(get_option('infscr_image'));?>" alt="The Loading Image" /></div>
<p>URL of image that will be displayed while content is being loaded. Visit <a href="http://www.ajaxload.info" target="_blank">www.ajaxload.info</a> to customize your own loading spinner.</p>
              	</td>
  	          </tr>
  	  
  	  			<tr>
				<th>
					<label for="<?php echo 'infscr_text'; ?>">Loading text:</label>
				</th>
				<td>
					<?php
						echo "<textarea name='".'infscr_text'."' id='".'infscr_text'."' rows='2'  style='width: 95%;'>\n";
						echo stripslashes(get_option('infscr_text'));
						echo "</textarea>\n";
					?>
				</td>
                <td>
              	  <p>Text will be displayed while content is being loaded. <small><acronym>HTML</acrynom> allowed.</small></p>
              	</td>
  	          </tr>

	<tr>
				<th>
					<label for="<?php echo 'infscr_donetext'; ?>">"You've reached the end" text:</label>
				</th>
				<td>
					<?php
						echo "<textarea name='".'infscr_donetext'."' id='".'infscr_donetext'."' rows='2'  style='width: 95%;'>\n";
						echo stripslashes(get_option('infscr_donetext'));
						echo "</textarea>\n";
					?>
				</td>
                <td>
              	  <p>Text will be displayed when all entries have already been retrieved. The plugin will show this message, fade it out, and cease working. <small><acronym>HTML</acrynom> allowed.</small></p>
              	</td>
  	          </tr>

			</tbody>
		</table>
        	<p class="submit">
		<input type='submit' name='info_update' value='Update Options' />
	</p>
	</div>