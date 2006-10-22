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
// This function writes to the settings.php file
// any settings you want saved
//=========================================
function updateSettings($settings,$dblink) {	
	foreach($settings as $key=>$value){
		$querystatement="UPDATE settings set value=\"".$value."\" WHERE name=\"".$key."\"";
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(mysql_error($dblink)." - ".$querystatement);		
	}
}//end function

function processSettings($variables,$files,$dblink){
	$writesettings=Array();
	foreach($variables as $key=>$value){
		if($key!="command" && $key!="printedlogo" && strpos($key,"mysql_")!==0 && $key!="changeseed" && $key!="currentpassword" && $key!="sencryption_seed" && $key!="doencryptionupdate"){
			if($_SESSION[substr($key,1)]!=$value){
				$writesettings[substr($key,1)]=$value;
				$_SESSION[substr($key,1)]=stripslashes($value);
			}
		}
	}

	$querystatement="SELECT name FROM modules WHERE name!=\"base\" ORDER BY name";
	$modulequery=mysql_query($querystatement,$dblink);
			
		while($modulerecord=mysql_fetch_array($modulequery)){
			@ include "../".$modulerecord["name"]."/include/adminsettings_include.php";
		}//end while 


	// if changes, process settings
	if(count($writesettings)>0)
		updateSettings($writesettings,$dblink);
	
	// deal with logo graphic.
	if(isset($files["printedlogo"]))
		if($files["printedlogo"]["type"]=="image/png"){
			if (function_exists('file_get_contents')) {
				$file = addslashes(file_get_contents($_FILES['printedlogo']['tmp_name']));
			} else {
				// If using PHP < 4.3.0 use the following:
				$file = addslashes(fread(fopen($_FILES['printedlogo']['tmp_name'], 'r'), filesize($_FILES['printedlogo']['tmp_name'])));
			}
			$querystatement="UPDATE files SET file=\"".$file."\" WHERE id=1";
			$queryresult=mysql_query($querystatement,$dblink);
			if(!$queryresult) reportError(300,"Error Uploading Graphic File");
		}
			
	return true;
}

function updateEncyptionSeed($newseed,$currpassword,$userid,$currseed,$dblink){
	//first let's make sure the password matches
	$querystatement="SELECT id FROM users WHERE id=".$userid." AND password=ENCODE(\"".$currpassword."\",\"".$currseed."\")";
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult)
		return "Error retrieving current user information: ".mysql_error($dblink);
	if(!mysql_num_rows($queryresult))
		return "Invalid Password";
	
	//let's update the encryption seed then
	$querystatement="UPDATE settings SET value=\"".$newseed."\" WHERE name=\"encryption_seed\"";
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult)
		return "Error updateing seed: ".mysql_error($dblink);

	//update the current session information
	$_SESSION["encryption_seed"]=$newseed;
	
	//last, reencode the current password
	$querystatement="UPDATE users SET password=ENCODE(\"".$currpassword."\",\"".$newseed."\") WHERE id=".$userid;
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult)
		return "Error updateing user with new seed: ".mysql_error($dblink);
		
	return "Encryption Seed Updated. All other user passwords are now invalid.";
}

//process commands
if (isset($_POST["command"])) {
	switch($_POST["command"]){
		case "Update Settings":
			if(processSettings(addSlashesToArray($_POST),$_FILES,$dblink))
				$statusmessage="Settings Updated";				
		break;
		
		case "Update Encryption Seed":
			$_POST=addSlashesToArray($_POST);
			$statusmessage = updateEncyptionSeed($_POST["sencryption_seed"],$_POST["currentpassword"],$_SESSION["userinfo"]["id"],$_SESSION["encryption_seed"],$dblink);
		break;
	}
}

?>