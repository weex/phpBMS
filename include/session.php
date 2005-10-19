<?php
//This function loads any variables written
// in settings.php into session variables.
//=========================================
function loadSettings() {
	
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

function reportError($id,$extras){
	echo $extras;
	die();
}

function xmlEncode($str){
	$str=str_replace("&","&amp;",$str);
	$str=str_replace("<","&lt;",$str);
	$str=str_replace(">","&gt;",$str);
	return $str;
}

// Start Code
//=================================================================================================================
	session_start();
	error_reporting(E_ALL);
	if (!isset($_SESSION["app_path"])) $mainpath=loadSettings();
	else $mainpath=$_SESSION["app_path"];
	
	if (!isset($_SESSION["userinfo"]) && basename($_SERVER["PHP_SELF"]) != "index.php") {
		if(isset($loginNoKick)){
			if(!isset($loginNoDisplayError))
				header("Location: ".$mainpath."noaccess.html");				
		} else{
			header("Location: ".$mainpath."index.php");
		}
	} else {

		// OPEN DATABASE IF NOT OPENED
		if(!isset($dblink)){
			$dblink = mysql_pconnect($_SESSION["mysql_server"],$_SESSION["mysql_user"],$_SESSION["mysql_userpass"]);		
			if (!$dblink) { session_destroy(); die("Error: No Link to MySQL possible"); }
			if (!mysql_select_db($_SESSION["mysql_database"])) {session_destroy(); die("Error: Couldn't open database: ".mysql_error($dblink)); }
		}	
	}//end if
	
?>