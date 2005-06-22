<?php
function loadSettings() {
	$_SESSION["isloaded"]=true;
	
	$path="";
	$count=1;
	//need to look for settings file... only go up a total of 5 directorieds
	while(!file_exists("settings.php") and ($count<5)){
		$path.="../";
		@ chdir("../");
		$count++;
	}
	
	$settingsfile =  @ fopen("settings.php","r");
	if($settingsfile){
		//loop through the settings file and load variables into the session 
		while( !feof($settingsfile)) {
			$line=fscanf($settingsfile,"%[^=]=%[^[]]",$key,$value);
			if ($line){
				$key=trim($key);
				$value=trim($value);
				if($key!="" and !strpos($key,"]")){	
					$startpos=strpos($value,"\"");
					$endpos=strrpos($value,"\"");
					if($endpos!=false)
						$value=substr($value,$startpos+1,$endpos-$startpos-1);
					$_SESSION[$key]=$value;
				}
			}
			$line=NULL;
			$key=NULL;
			$value=NULL;
		}
		fclose($settingsfile);
		return $path;
	} else reportError(500,"Settings file could not be opened");
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>phpBMS - TEST</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="common/stylesheet/grays/base.css" rel="stylesheet" type="text/css">
</head>
<body><?php loadSettings();?>
</body>
</html>