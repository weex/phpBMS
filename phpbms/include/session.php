<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
 | All rights reserved.                                                    |
 +-------------------------------------------------------------------------+
 |                                                                         |
 | Redistribution and use in source and binary forms, with or without      |
 | modification, are permitted provided that the following conditions are  |
 | met:                                                                    |
 |                                                                         |
 | - Redistributions of source code must retain the above copyright        |
 |   notice, this list of conditions and the following disclaimer.         |
 |                                                                         |
 | - Redistributions in binary form must reproduce the above copyright     |
 |   notice, this list of conditions and the following disclaimer in the   |
 |   documentation and/or other materials provided with the distribution.  |
 |                                                                         |
 | - Neither the name of Kreotek LLC nor the names of its contributore may |
 |   be used to endorse or promote products derived from this software     |
 |   without specific prior written permission.                            |
 |                                                                         |
 | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS     |
 | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT       |
 | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A |
 | PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
 | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,   |
 | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT        |
 | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,   |
 | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY   |
 | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT     |
 | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE   |
 | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.    |
 |                                                                         |
 +-------------------------------------------------------------------------+
*/
if(!isset($_SERVER['REQUEST_URI'])) {
	// This following code is for windows boxen, because they lack some server varables as well
	// formating options for the strftime function
	$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
	
	define("HOUR_FORMAT","%I");

	// Append the query string if it exists and isn't null
	if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
		$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
} else
	define("HOUR_FORMAT","%l");
	
function loadSettings(){
	global $dblink;
	
	$querystatement="SELECT name,value FROM settings";
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Could not retrieve settings. If you have not ran the update script for phpBMS, 
										please run it before logging in. Error Details:<br />".mysql_error($dblink)." ".$querystatement));
	while($therecord=mysql_fetch_array($queryresult))
		$_SESSION[$therecord["name"]]=$therecord["value"];
}

//This function loads any variables written
// in settings.php into session variables.
//=========================================
function loadMysqlSettings() {
	
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
					if(strpos($key,"mysql_")===0)
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

function reportError($id,$extras,$format=true,$path="",$die=true){
	if($path=="" && isset($_SESSION["app_path"]))
		$path=$_SESSION["app_path"];
	if($format) {?>	
		<link href="<?php echo $path ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css" />
		<div class="bodyline">
			<h1>phpBMS Error: <?php echo $id?></h1>
			<div class="box">
				<?php echo $extras ?>
			</div>
		</div>
	<?php }else
		echo $extras;
	if($die) die();
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
	if (!isset($_SESSION["app_path"])) $mainpath=loadMysqlSettings();
	else $mainpath=$_SESSION["app_path"];
	
	if (!isset($_SESSION["userinfo"]) && basename($_SERVER["PHP_SELF"]) != "index.php") {
		if(isset($loginNoKick)){
			if(!isset($loginNoDisplayError))
				exit();
			else
				$dblink = openDB($_SESSION["mysql_server"],$_SESSION["mysql_user"],$_SESSION["mysql_userpass"],$_SESSION["mysql_database"]); 
		} else{
			header("Location: ".$mainpath."index.php");
			exit();
		}
	} else {

		// OPEN DATABASE IF NOT OPENED
		if(!isset($dblink))		
			$dblink = openDB($_SESSION["mysql_server"],$_SESSION["mysql_user"],$_SESSION["mysql_userpass"],$_SESSION["mysql_database"]); 

		if(!isset($_SESSION["app_name"]))
			loadSettings();
	}//end if
	
	function openDB($dbserver,$dbuser,$dbpass,$dbname){
		$dblink = @ mysql_pconnect($dbserver,$dbuser,$dbpass);		
		if (!$dblink) 
			reportError(500,"Could not link to MySQL Server.  Please check your settings.",true,$mainpath);
		if (!mysql_select_db($dbname,$dblink)) 
			reportError(500,"Could not open database.  Please check your settings.",true,$mainpath);	
		return $dblink;
	}
?>