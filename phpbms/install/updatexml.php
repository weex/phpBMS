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
error_reporting(E_ALL);
define("APP_DEBUG",false);
define("noStartup",true);

include("install_include.php");

include("../include/session.php");

		
	function runUpdate($db, $currentVersion,$newVersion){
		
		$thereturn="";
		while($currentVersion!=$newVersion){
			switch($currentVersion){
				// ================================================================================================
				case "0.5":
					$thereturn.="Updating Base Module to 0.51\n";
					
					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.51\" WHERE name=\"base\";";
					$queryresult=$db->query($querystatement);
					$thereturn.=" - modified base record in modules table\n";
					
					$thereturn.="Update to 0.51 Finished\n\n";
					$currentVersion="0.51";
				break;
				// ================================================================================================
				case "0.51":
					$thereturn.="Updating Base Module to 0.6\n";
										
					//Processing Data Structure Changes
					$thereturn.=processSQLfile($db,"updatev0.6.sql");
					
					//Inputind new records for new structure
					$thereturn.=importData($db,"choices");
					$thereturn.=importData($db,"menu");
					$thereturn.=importData($db,"reports");
					$thereturn.=importData($db,"tablecolumns");
					$thereturn.=importData($db,"tabledefs");
					$thereturn.=importData($db,"tablefindoptions");
					$thereturn.=importData($db,"tableoptions");
					$thereturn.=importData($db,"tablesearchablefields");
					
					/*
					//Setting the new default load page
					$newSettings["default_load_page"]="modules/base/snapshot.php";
					write_settings($newSettings);
					$thereturn.=" - modified default start page in settings\n";					
					
					depreciated in newer versions
					*/
										
					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.6\" WHERE name=\"base\";";
					$queryresult=$db->query($querystatement);
					$thereturn.=" - modified base record in modules table\n";

					$thereturn.="Update to 0.6 Finished\n\n";
					
					$currentVersion="0.6";
				break;
				// ================================================================================================
				case "0.6":
					$thereturn.="Updating Base Module to 0.601\n";
					
					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.601\" WHERE name=\"base\";";
					$queryresult=$db->query($querystatement);
					$thereturn.=" - modified base record in modules table\n";
					
					$thereturn.="Update to 0.601 Finished\n\n";
					$currentVersion="0.601";
				break;
				// ================================================================================================
				case "0.601":
					$thereturn.="Updating Base Module to 0.602\n";
					
					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.602\" WHERE name=\"base\";";
					$queryresult=$db->query($querystatement);
					$thereturn.=" - modified base record in modules table\n";
					
					$thereturn.="Update to 0.602 Finished\n\n";
					$currentVersion="0.602";
				break;
				// ================================================================================================
				case "0.602":
					$thereturn.="Updating Base Module to 0.61\n";
					
					//Processing Data Structure Changes
					$thereturn.=processSQLfile($db,"updatev0.61.sql");

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.61\" WHERE name=\"base\";";
					$queryresult=$db->query($querystatement);
					$thereturn.=" - modified base record in modules table\n";
					
					/*foreach($vars as $key=>$value){
						if (strpos($key,"mysql_")!==0){
							$querystatement="INSERT INTO settings (name,value) VALUES (\"".$key."\",\"".$value."\")";
							$queryresult=$db->query($querystatement);
						}
					}
					$thereturn.="Moved non-mysql settings to new settings table.\n";
					
					DEPRECIATED
					*/
					
					$filename="../report/logo.png";
					if (function_exists('file_get_contents')) {
						@ $file = addslashes(file_get_contents($filename));
					} else {
						// If using PHP < 4.3.0 use the following:
						@ $file = addslashes(fread(fopen($filename, 'r'), filesize($filename)));
					}
					$querystatement="INSERT INTO files (id,name,description,type,accesslevel,file,createdby,creationdate,modifiedby) 
									VALUES (1,\"logo.png\",\"Company Logo Used in PDF reports\",\"image/png\",90,\"".$file."\",2,Now(),2)";
					$queryresult=$db->query($querystatement);
					$thereturn.="Moved logo to database.\n";

					
					$thereturn.="Update to 0.61 Finished\n\n";
					$currentVersion="0.61";
				break;
				// ================================================================================================
				case "0.61":
					$thereturn.="Updating Base Module to 0.62\n";
					
					//Processing Data Structure Changes
					$thereturn.=processSQLfile($db,"updatev0.62.sql");

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.62\" WHERE name=\"base\";";
					$queryresult=$db->query($querystatement);
					$thereturn.=" - modified base record in modules table\n";				

					$thereturn.="Update to 0.62 Finished\n\n";
					$currentVersion="0.62";
				break;
				// ================================================================================================
				case "0.62":
					$thereturn.="Updating Base Module to 0.7\n";
					
					//Processing Data Structure Changes
					$thereturn.=processSQLfile($db,"updatev0.7.sql");
					
					$querystatement="SELECT id,accesslevel FROM users WHERE accesslevel!=0";
					$queryresult=$db->query($querystatement);
					while($therecord=$db->fetchArray($queryresult)){
						while($therecord["accesslevel"]>1){
							if($therecord["accesslevel"]==40)
								$therecord["accesslevel"]=$therecord["accesslevel"]-10;
							$querystatement="INSERT INTO rolestousers (userid,roleid) VALUES (".$therecord["id"].",".$therecord["accesslevel"].")";
							$insertresult=$db->query($querystatement);
							$therecord["accesslevel"]=$therecord["accesslevel"]-10;							
						}
					}					
					$querystatement="ALTER TABLE `users` DROP COLUMN `accesslevel`";
					$queryresult=$db->query($querystatement);

					$thereturn.=" - Connverted user access levels to roles.\n";				

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.7\" WHERE name=\"base\";";
					$queryresult=$db->query($querystatement);
					$thereturn.=" - modified base record in modules table\n";				

					$thereturn.="Update to 0.7 Finished\n\n";
					$currentVersion="0.7";
				break;

				// ================================================================================================
				case "0.7":				
					$thereturn.="Updating phpBMS Core to 0.8\n";
					
					//Processing Data Structure Changes
					$thereturn.=processSQLfile($db,"updatev0.8.sql");
				
					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.8\" WHERE name=\"base\";";
					$queryresult=$db->query($querystatement);

					$thereturn.="______________________\n\n";
					$thereturn.="Update to 0.8 Finished\n\n";
					$currentVersion="0.8";
				break;

				// ================================================================================================
				case "0.8":				
					$thereturn .= "Updating phpBMS Core to 0.9\n";
					
					//Processing Data Structure Changes
					$thereturn .= processSQLfile($db,"updatev0.9.sql");
				
					//Updating Module Table
					$querystatement = "
						UPDATE 
							modules 
						SET 
							version='0.9'
						WHERE
							name='base'";
							
					$queryresult = $db->query($querystatement);

					$thereturn .= "______________________\n\n";
					$thereturn .= "Update to 0.9 Finished\n\n";
					
					$currentVersion = "0.9";
					
					break;

			}//end switch
		}//end while
		return $thereturn;
	}//end function
	


	//PROCESSING
	//==============================================================================================================================
	$thereturn="Error Processing: No Command Given";
	if(isset($_GET["command"])){
	
		$phpbmsSession = new phpbmsSession;
		$success = $phpbmsSession->loadDBSettings(false);

		include_once("include/db.php");
		$db = new db(false);
		$db->stopOnError = false;
		$db->showError = false;
		$db->logError = false;
	
		if($success !== false){
			
			if(!$db->connect())
				$thereturn = "Could Not Establish Connection To MySQL Server: Check server, user name, and password."; 			
			else {
				if(!$db->selectSchema())
					$thereturn = "Database (schema) ".MYSQL_DATABASE." could not be selected";			
				else {
	
					$phpbmsSession->db = $db;
					$phpbmsSession->loadSettings();

					switch($_GET["command"]){
						case "verifyLogin":
						//========================================================================================
							if (!verifyAdminLogin($db,$_GET["u"],$_GET["p"],$_GET["v"]))
								$thereturn="Invalid Administrative Login.\n";
							else
								$thereturn="Administrative Login Verified.\n";
						break;
	
						case "checkBaseUpdate":
						//========================================================================================
							if (!verifyAdminLogin($db,$_GET["u"],$_GET["p"]))
								$thereturn="Invalid Administrative Login.\n";
							else {
								$currenVersion = getCurrentVersion($db,"base");

								if( ((real) $currenVersion) >= ((real) $_GET["v"]) )
									$thereturn="No Update Needed for phpBMS Core.";
								else
									$thereturn="phpBMS Core Update Possible\n Current Data Version: ".$currenVersion;							
							}
						break;

						case "checkModuleUpdate":
						//========================================================================================
							if (!verifyAdminLogin($db,$_GET["u"],$_GET["p"]))
								$thereturn="Invalid Administrative Login.\n";
							else {
								$currenVersion = getCurrentVersion($db,$_GET["m"]);

								if( ((real) $currenVersion) >= ((real) $_GET["mv"]) )
									$thereturn="No Update Needed for Current Module.";
								else
									$thereturn="Module Update Possible\n Current Data Version: ".$currenVersion;							
							}
						break;

						case "updateBaseVersion":
						//========================================================================================
							if (!verifyAdminLogin($db,$_GET["u"],$_GET["p"]))
								$thereturn="Invalid Administrative Login.\n";
							else {
								$currenVersion = getCurrentVersion($db,"base");

								if( ((real) $currenVersion) >= ((real) $_GET["v"]) )
									$thereturn="No Update Needed for phpBMS Core.";
								else
									$thereturn = runUpdate($db, $currenVersion, $_GET["v"]);							
							}
						break;
					}//end switch					
	
	
				}		
			}
		} else
			$thereturn = "Could not access settings.php";	
	}
				
		
		header('Content-Type: text/xml');
		?><?php echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'; ?>
<response><?php echo $thereturn?></response>