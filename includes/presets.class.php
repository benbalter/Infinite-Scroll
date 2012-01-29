<?php
/*Infinite-Scroll Presets Class
Dependencies: Wordpress, infiniteScroll.*/
class infiniteScrollPresets extends infiniteScroll {
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
	if (($handle = @fopen(parent::$PresetDB, "r+")) !== FALSE) {
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
				fputcsv($handle,array($themename,$content,$nav,$post,$next),",");
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
	if (($handle = @fopen(parent::$PresetDB, "r")) !== FALSE) {
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
	if (($handle = @fopen(parent::$PresetDB, "r")) !== FALSE) {
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
	if (($handle = @fopen(parent::$PresetRev, "r")) !== FALSE) {
		//Discard first line
		fgets($handle, 4096);
		$currentrev = fgets($handle, 4096);
    	fclose($handle);
		}
	if(!isset($currentrev))
		$currentrev = 0;
	//Now check SubVersion
	$headresponse = get_headers(parent::$PresetRepo,1);
	if($headresponse[0]!='HTTP/1.1 404 Not Found'&&!empty($headresponse['ETag']))
		{
		$etag = trim($headresponse['ETag'],'"');
		$exetag = explode("//",$etag);
		$reporev = $exetag[0];
		if($reporev>$currentrev)
			{
			$newdb = file_get_contents(parent::$PresetRepo);
			if(file_put_contents(parent::$PresetDB,$newdb))
				{
				if(file_put_contents(parent::$PresetRev,"<?php /*Infinite-Scroll Preset DB Rev File. Contains Subversion Rev Info.\n$reporev\nEnd Preset DB Rev File*/ ?>"))
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
		return array("ERROR","Could not contact Wordpress repo. Are you behind a Firewall? Couldn't access: ".parent::$PresetRepo);
	}
public static function presetExport()
	{
	if(file_exists(parent::$PresetDB))
		{
		header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
    	header("Content-Description: Preset DB Export");
    	header("Content-Disposition: attachment; filename=PresetDB.csv.php");
   	 	header("Content-Type: text/csv");
		readfile(parent::$PresetDB);
		return true;
		}
	else
		return false;
	}
}