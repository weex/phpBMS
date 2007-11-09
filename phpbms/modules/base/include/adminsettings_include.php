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
// This function writes to the settings.php file
// any settings you want saved
//=========================================

class settings{
	var $db;
	
	function settings($db){
	
		$this->db = $db;
	
	}
	
	
	function getSettings(){
		$therecord = array();
		
		$querystatement = "SELECT `name`, `value` FROM `settings`";
		$queryresult = $this->db->query($querystatement);
		
		while($setting = $this->db->fetchArray($queryresult))
			$therecord[$setting["name"]] = $setting["value"];
			
		return $therecord;
	}
	
	
	function updateSettings($variables){
	
		global $phpbms;
		
		if(!isset($variables["persistent_login"])) $variables["persistent_login"]=0;
		
		//include any procesing that needs to be done by modules
		foreach($phpbms->modules as $module => $moduleinfo)
			if($module != "base")
				if(class_exists($module."Update")){
					$class = $module."Update";
					$extraUpdate = new $class($this->db);
					$variables = $extraUpdate->updateSettings($variables);
					
				}//end if
	
		// Update the settings records
		foreach($variables as $settingname => $settingvalue){
			if(defined(strtoupper($settingname))){
				if(constant(strtoupper($settingname)) != $settingvalue){
				
					$updatestatement = "
						UPDATE 
							settings 
						SET 
							value ='".$settingvalue."' 
						WHERE 
							name='".mysql_real_escape_string($settingname)."'";
							
					$updateresult = $this->db->query($updatestatement);
					
					if(!$this->db->affectedRows()){

						//check to see why the update did not work
						$querystatement = "
							SELECT 
								name
							FROM
								settings
							WHERE
								name = '".mysql_real_escape_string($settingname)."'";
						
						$queryresult = $this->db->query($querystatement);
						
						if(!$this->db->numRows($queryresult)){
						
							//insert the setting if need be
							$insertstatement ="
								INSERT INTO
									settings (
										`value`,
										`name`,
									) VALUES {
										'".$settingvalue."',
										'".mysql_real_escape_string($settingname)."'
									}";
									
							$this->db-query($insertstatement);
							
						}//end if
					
					}//end if
					
				}//end if
			}
		}//end foreach
		
		// deal with logo graphic.
		if(isset($_FILES["printedlogo"])){
			if($_FILES["printedlogo"]["type"] == "image/png" || $_FILES["printedlogo"]["type"] == "image/jpeg"){
				if (function_exists('file_get_contents')) {
					$file = mysql_real_escape_string(file_get_contents($_FILES['printedlogo']['tmp_name']));
				} else {
					// If using PHP < 4.3.0 use the following:
					$file = mysql_real_escape_string(fread(fopen($_FILES['printedlogo']['tmp_name'], 'r'), filesize($_FILES['printedlogo']['tmp_name'])));
				}
				if($_FILES["printedlogo"]["type"] == "image/jpeg")
					$name = "logo.jpg";
				else
					$name = "logo.png";
					
				$querystatement="UPDATE `files` SET `file` = '".$file."', `type` = '".$_FILES["printedlogo"]["type"]."', `name`='".$name."' WHERE id=1";
				$queryresult=$this->db->query($querystatement);
			}
		}		

		return true;
		
	}//end method
	

	function updateEncyptionSeed($newseed,$currpassword,$userid){
	
		$userid = (int) $userid;
	
		//first let's make sure the password matches
		$querystatement="SELECT id FROM users WHERE id=".$userid." AND password=ENCODE('".$currpassword."','".ENCRYPTION_SEED."')";
		$queryresult=$this->db->query($querystatement);

		if(!$this->db->numRows($queryresult))
			return "Encryption Seed not Updated: Invalid Current Password";
		
		//let's update the encryption seed then
		$querystatement="UPDATE settings SET value='".$newseed."' WHERE name='encryption_seed'";
		$queryresult=$this->db->query($querystatement);
			
		//last, reencode the current password
		$querystatement="UPDATE users SET password=ENCODE('".$currpassword."','".$newseed."') WHERE id=".$userid;
		$queryresult=$this->db->query($querystatement);

		//rencode all other passwords
		$querystatement="UPDATE users SET password = ENCODE(DECODE(password,'".ENCRYPTION_SEED."'),'".$newseed."') WHERE id !=".$userid;
		$queryresult=$this->db->query($querystatement);
			
		return "Encryption Seed Updated.";
	}	


	function processForm($variables){

		$variables = addSlashesToArray($variables);
		
		switch($variables["command"]){
			case "update settings":
				if($this->updateSettings($variables))
					$statusmessage="Settings Updated";
			break;
			
			case "update encryption seed":
				if(isset($variables["changeseed"]))
					$statusmessage = $this->updateEncyptionSeed($variables["encryption_seed"],$variables["currentpassword"],$_SESSION["userinfo"]["id"]);
			break;
		}

		return $statusmessage;

	}//end method
	
	
	function displayStylesheets($stylesheet){

		$thedir="../../common/stylesheet";
		$thedir_stream = @opendir($thedir);
		
		while($entry = @ readdir($thedir_stream)){
			if ($entry!="." and  $entry!=".." and is_dir($thedir."/".$entry)) {
				echo "<option value=\"".$entry."\"";
					if($entry == $stylesheet) echo " selected=\"selected\" ";
				echo ">".$entry."</option>";
			}
		}

	}
}//end class

?>