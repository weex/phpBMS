<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2007, Kreotek LLC                                  |
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
	
function loadSettings($encoding="utf8"){
	global $dblink;
	
	// this only works for MySQL 5
	@ mysql_query("SET NAMES ".$encoding,$dblink);
	
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
	$currdirectory= getcwd();
	
	while(!file_exists("settings.php") and ($count<5)){
		$path.="../";
		@ chdir("../");
		$count++;
	}
	
	$settingsfile =  @ fopen("settings.php","r");
	if($settingsfile){
		//loop through the settings file and load variables into the session 
		while( !feof($settingsfile)) {
			$line=NULL;
			$key=NULL;
			$value=NULL;
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
		}
		if(!isset($_SESSION["mysql_pconnect"]))
			$_SESSION["mysql_pconnect"]="true";
		fclose($settingsfile);
		@ chdir ($currdirectory);
		return $path;
	} else reportError(500,"Settings file could not be opened");
}

function reportError($id,$extras,$format=true,$path="",$die=true,$log=true){
	if($path=="" && isset($_SESSION["app_path"]))
		$path=$_SESSION["app_path"];
	if($format===true)	
		$format="xhtml";
		
	switch($format){
		case "JSON":
		case "json":
			echo "{
				\"error\" : { \"number\" : ".$id.", \"extras\" : \"".str_replace("\n","\\n",addslashes($extras))."\"}
			}";
		break;
		case "xhtml":
		case "XHTML":
			?><link href="<?php echo $path ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css" />
			<div class="bodyline">
				<h1>phpBMS Error: <?php echo $id?></h1>
				<div class="box">
					<?php echo $extras ?>
				</div>
			</div><?php 
		break;
		default:
			echo $id;
			if($extras) echo " - ".$extras;
	}
	if($format) {}else
		echo $extras;
		
	if($log && function_exists("sendLog")){
		global $dblink;
		if(!isset($_SESSION["userinfo"]["id"]))
			$_SESSION["userinfo"]["id"]="NULL";
		sendLog($dblink,"ERROR",$id.": ".$_SERVER["REQUEST_URI"]." -- ".$extras,$_SESSION["userinfo"]["id"]);
	}
	if($die) die();
}

function xmlEncode($str){
	$str=str_replace("&","&amp;",$str);
	$str=str_replace("<","&lt;",$str);
	$str=str_replace(">","&gt;",$str);
	return $str;
}

function openDB($dbserver,$dbuser,$dbpass,$dbname,$pconnect){
	if($pconnect=="true")
		$dblink = @ mysql_pconnect($dbserver,$dbuser,$dbpass);
	else
		$dblink = @ mysql_connect($dbserver,$dbuser,$dbpass);
	if (!$dblink) 
		reportError(500,"Could not link to MySQL Server.  Please check your settings.",true);
	if (!mysql_select_db($dbname,$dblink)) 
		reportError(500,"Could not open database.  Please check your settings.",true);	
	return $dblink;
}

function verifyAPILogin($user,$pass,$seed,$dblink){
	$thereturn=false;
	
	$querystatement="SELECT id, firstname, lastname, email, phone, department, employeenumber, admin
					FROM users 
					WHERE login!=\"Scheduler\" AND login=\"".mysql_real_escape_string($user)."\" and password=ENCODE(\"".mysql_real_escape_string($pass)."\",\"".mysql_real_escape_string($seed)."\") and revoked=0 and portalaccess=1";
	$queryresult=@ mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(1441,"Error verifing user record.","json");
	
	if(mysql_num_rows($queryresult)){
		//We found a record that matches in the database
		// populate the session and go in
		$_SESSION["userinfo"]=mysql_fetch_array($queryresult);

		// set application location (web, not physical)
		$pathrev=strrev($_SERVER["PHP_SELF"]);
		$_SESSION["app_path"]=strrev(substr($pathrev,(strpos($pathrev,"/"))));

		$querystatement="UPDATE users SET modifieddate=modifieddate, lastlogin=Now() WHERE id = ".$_SESSION["userinfo"]["id"];
		$queryresult=@ mysql_query($querystatement,$dblink);
		if(!$queryresult)
			reportError(300,"Error updating user login time.","json");
		$thereturn=true;
	}
	return $thereturn;
}

function reportJSONError(){
}

// Start Code
//=================================================================================================================

	//php <4.3.0 compatibility
	if(!function_exists("mysql_real_escape_string")){
		function mysql_real_escape_string($string){
			return mysql_escape_string($string);
		}
		
	   function utf8_replaceEntity($result){
		   $value = (int)$result[1];
		   $string = '';
		  
		   $len = round(pow($value,1/8));
		  
		   for($i=$len;$i>0;$i--){
			   $part = ($value & (255>>2)) | pow(2,7);
			   if ( $i == 1 ) $part |= 255<<(8-$len);
			  
			   $string = chr($part) . $string;
			  
			   $value >>= 6;
		   }
		  
		   return $string;
	   }
	  
	   function html_entity_decode($string){
		   return preg_replace_callback(
			   '/&#([0-9]+);/u',
			   'utf8_replaceEntity',
			   $string
		   );
	   }	
	}
	
	// This following code is for windows boxen, because they lack some server varables as well
	// formating options for the strftime function
	if(!isset($_SERVER['REQUEST_URI'])) {
		$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
		
		define("HOUR_FORMAT","%I");
	
		// Append the query string if it exists and isn't null
		if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
			$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
	} else
		define("HOUR_FORMAT","%l");


	//Testing for API login
	if(strpos(basename($_SERVER["PHP_SELF"]),"api_")!==false){
		if(isset($_POST["phpbmsusername"]) && isset($_POST["phpbmspassword"])){
			$mainpath=loadMysqlSettings();
			$dblink = openDB($_SESSION["mysql_server"],$_SESSION["mysql_user"],$_SESSION["mysql_userpass"],$_SESSION["mysql_database"],$_SESSION["mysql_pconnect"]);
			loadSettings();
			if(!verifyAPILogin($_POST["phpbmsusername"],$_POST["phpbmspassword"],$_SESSION["encryption_seed"],$dblink))
				reportError(1440,"Login credentials incorrect","json");
		} else
			reportError(1441,"No login credentials passed","json");
	} else {	
		if(!isset($noSession)){
			session_name("phpBMSv08ID");
			session_start();
		}
		
		if (!isset($_SESSION["app_path"]))
			$mainpath=loadMysqlSettings();
		else 
			$mainpath=$_SESSION["app_path"];
				
		if (!isset($_SESSION["userinfo"]) && basename($_SERVER["PHP_SELF"]) != "index.php") {
		
			if(isset($loginNoKick)){
				if(!isset($loginNoDisplayError))
					exit();
				else
					$dblink = openDB($_SESSION["mysql_server"],$_SESSION["mysql_user"],$_SESSION["mysql_userpass"],$_SESSION["mysql_database"],$_SESSION["mysql_pconnect"]); 
			} else{
				header("Location: ".$mainpath."index.php");
				exit();
			}
		} else {
	
			// OPEN DATABASE IF NOT OPENED
			if(!isset($dblink))		
				$dblink = openDB($_SESSION["mysql_server"],$_SESSION["mysql_user"],$_SESSION["mysql_userpass"],$_SESSION["mysql_database"],$_SESSION["mysql_pconnect"]); 
	
			//load from settings table
			if(!isset($_SESSION["app_name"])) loadSettings();
		}//end if
	}
?>