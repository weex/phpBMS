<?php
// This function writes to the settings.php file
// any settings you want saved
//=========================================
function write_settings($settings) {
	$settingsfile = fopen("../../settings.php","r") or die ("Couldn't open Settings File");
	//create an array of all lines
	while( !feof($settingsfile)) {
		$newfile[]=fgets($settingsfile,1024);
	}
	fclose($settingsfile);
	
	$newfile[]="\n";
	foreach($settings as $settingname=>$settingvalue) {
		$infile=false;
		//next loop through the file, and if the setting is their, replace it
		for($i=0;$i<count($newfile);$i++){
			if (strpos(("D".$newfile[$i]),$settingname)==1) {
				$tabnumber=intval(5-strlen($settingvalue)/8);
				$newfile[$i]=$settingname.str_repeat(chr(9),$tabnumber)."= \"".str_replace(chr(10),"\\n",$settingvalue)."\"\n";
				$infile=true;
				break;
			}
		}//
		if(!$infile) {
			$tabnumber=intval(5-strlen($settingname)/8);
			$newfile[]=$settingname.str_repeat(chr(9),$tabnumber)."= \"".str_replace(chr(10),"\\n",$settingvalue)."\"\n";
		}
	}
		
	if(end($newfile)=="\n") array_pop($newfile);
	//now write the new file
	$settingsfile = fopen("../../settings.php","w") or die ("Couldn't open Settings File");
	for($i=0;$i<count($newfile);$i++){
		 fwrite($settingsfile,$newfile[$i],1024);
	}
	fclose($settingsfile);	
}//end function

function processSettings($variables,$files){
	global $dblink;
	$writesettings=Array();
	foreach($variables as $key=>$value){
		if($key!="command" && $key!="printedlogo"){
			if($_SESSION[substr($key,1)]!=$value){
				$writesettings[substr($key,1)]=$value;
				$_SESSION[substr($key,1)]=$value;
			}
		}
	}

	$querystatement="SELECT name FROM modules WHERE name!=\"base\" ORDER BY name";
	$modulequery=mysql_query($querystatement,$dblink);
			
		while($modulerecord=mysql_fetch_array($modulequery)){
			@ include "../".$modulerecord["name"]."/include/adminsettings_include.php";
		}//end while 


	// if changes, process settings
	if(count($writesettings)>0) { write_settings($writesettings);}
	
	// deal with logo graphic.
	if(isset($files["printedlogo"]))
		if($files["printedlogo"]["type"]=="image/png")
			copy($files["printedlogo"]["tmp_name"],$_SERVER["DOCUMENT_ROOT"].$_SESSION["app_path"]."report/logo.png");
			
	return true;
}


//process commands
if (isset($_POST["command"])) {
	if(processSettings(addSlashesToArray($_POST),$_FILES))
		$statusmessage="Settings Updated";
}

?>