<div class="infscroll-tab infscroll-tab-presets<?php echo infiniteScrollOptions::pageActive("presets","tab");?>">
    	  <p>For advanced users only. Generally you shouldn't need to use this.</p>
    <table class="editform infscroll-opttable" cellspacing="0" >
		  <tbody>
          <tr>
				<th width="20%" >
					<label for="themepresets">Add Preset:</label>
				</th>
				<td colspan="2">
                <form action="options-general.php?page=wp_infinite_scroll.php&default=presets" method="post">
                <table>
                 <tr valign="top"><th scope="row">Theme Name</th><td><input type="text" value="<?php if(isset($_POST['preset_themename'])) echo $_POST['preset_themename']; else echo strtolower(get_current_theme());?>" size="40" name="preset_themename" id="preset_themename"></td></tr>
                <tr valign="top"><th scope="row">Content Selector</th><td><input type="text" value="<?php if(isset($_POST['preset_content'])) echo $_POST['preset_content'];?>" size="40" name="preset_content" id="preset_content"></td></tr>
                <tr valign="top"><th scope="row">Posts Selector</th><td><input type="text" value="<?php if(isset($_POST['preset_posts'])) echo $_POST['preset_posts'];?>" size="40" name="preset_posts" id="preset_posts"></td></tr>
                <tr valign="top"><th scope="row">Navigation Selector</th><td><input type="text" value="<?php if(isset($_POST['preset_nav'])) echo $_POST['preset_nav'];?>" size="40" name="preset_nav" id="preset_next"></td></tr>
                <tr valign="top"><th scope="row">Next Page Selector</th><td><input type="text" value="<?php if(isset($_POST['preset_next'])) echo $_POST['preset_next'];?>" size="40" name="preset_next" id="preset_next"></td></tr>
                </table>
                <p class="submit" style="text-align:left;">
<input type='submit' name='preset_add' value='Add to Preset DB' /><br /><label for="preset_overwrite">Overwrite Existing Theme Preset: </label><input name="preset_overwrite" id="preset_overwrite" type="checkbox" value="1" />
	</p>
                </form>
  			</td>
			</tr>
          <tr>
				<th width="20%" >
					<label for="themepresets">Update Preset DB:</label>
				</th>
				<td>
                <a href="options-general.php?page=<?php echo $_GET['page']; ?>&default=presets&presetup=1" alt="Check for Updates" class="infscroll-preset-update button">Check for Updates</a>
  			</td>
  			<td width="50%">
  			  <p>Update your preset database to the newest version from our Wordpress repo. NOTE: This will remove any "custom" presets you may have added.</p>
			  </td>
			</tr>
          <tr>
				<th width="20%" >
					<label for="themepresets">Export Preset DB:</label>
				</th>
				<td>
                <a href="<?php echo plugins_url('infinite-scroll')."/presetdb.php?do=export&r=".mt_rand();?>" alt="Export Database" class="infscroll-preset-export button">Export Database</a>
  			</td>
  			<td width="50%">
  			  <p>Exporting the preset database might be useful if you ever want to share your custom presets with someone else.</p>
			  </td>
			</tr>
		  <tr style="border-top:2px solid black;">
				<th>
					<label for="<?php echo 'infscr_content_selector'; ?>">View Existing Presets:</label>
				</th>
				<td colspan="2">
                <table width="100%" border="0" cellspacing="0" cellpadding="1">
					  <tr>
					    <th width="20%" style="text-align:left;">Preset/Theme Name</th>
					    <th width="20%" style="text-align:left;">Content Selector</th>
					    <th width="20%" style="text-align:left;">Post Selector</th>
					    <th width="20%" style="text-align:left;">Navigation Selector</th>
					    <th width="20%" style="text-align:left;">Previous Posts Selector</th>
				      </tr>
                </table>
                <table width="100%" border="0" cellspacing="0" cellpadding="1" class="infscroll_preset_list">			
					<?php
					//Will be quite a memory hog when the database gets larger
					//TODO: Work on nosql style method of selecting ranges
					if(!empty($_GET['infpage'])&&((int) $_GET['infpage']) == $_GET['infpage']&&$_GET['infpage']>=0)
						$infscr_preset_page = $_GET['infpage'];	
					else
						$infscr_preset_page = 1;
					$finalpresets = infiniteScrollPresets::presetGetAll();
					if($finalpresets[0]=='ERROR')
						{
						echo "<tr>
							<td width=\"100%\" colspan=\"5\">{$finalpresets[1]}</td>
						  </tr>";	
						}
					else
						{
						$finalpresets = $finalpresets[1];
						//Determine length of results
						$length = count($finalpresets) - 1;
						
						$limit = 20;
						$nextpage = $infscr_preset_page + 1;
						$start = ($infscr_preset_page-1) * $limit;	
	
						if(($start+$limit)>$length)
							$nextpage = -1;	
						if($start<=$length)
							{
							//Extract what we want
							$finalpresets = array_slice($finalpresets, $start, $limit);
							foreach($finalpresets as $key=>$value)
								{
								if($key%2)
									$rowstyle = " style='background-color:#F7F7F7;'";
								else
									$rowstyle = "";
								echo "<tr$rowstyle>
								<td width=\"20%\">{$value['name']}</td>
								<td width=\"20%\">{$value['content']}</td>
								<td width=\"20%\">{$value['post']}</td>
								<td width=\"20%\">{$value['nav']}</td>
								<td width=\"20%\">{$value['next']}</td>
							  </tr>";
								}
							}
						else
							{
							echo "<tr>
							<td width=\"100%\" colspan=\"5\">No More Presets Available...</td>
						  </tr>";
							}
						}
					?>
            </table>
            <div class="infscroll_preset_nav">
            <?php if($nextpage!=-1) {?>
             <a href="options-general.php?page=<?php echo $_GET['page']; ?>&default=<?php echo infiniteScrollOptions::matchDefault($_GET['default']);?>&infpage=<?php echo $nextpage;?>" alt="More Results" class="button">More Results</a>
             <?php }?>
             </div>
  			</td>
			</tr>
			</tbody>
		</table>
    
    </div>