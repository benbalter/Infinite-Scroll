<?php
require_once( '../../../wp-load.php' );
wp();
check_admin_referer();
include("includes/presets.class.php");
//What are we being asked for?
if($_GET['do']=='export')
	{
	if(!infiniteScrollPresets::presetExport())
		echo "Error exporting file. (Could not find the database to export). Please try again.";
	}
?>