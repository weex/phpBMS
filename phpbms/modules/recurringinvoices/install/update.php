<?php
/*
 $Rev: 265 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-13 21:33:23 -0600 (Mon, 13 Aug 2007) $
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

include("../../../install/install_include.php");
include("../../../include/session.php");

	function doUpdate($db) {
	
		$module = array(
			"title" => "Recurring Invoices",
			"name" => "recurringinvoices"
		);
	
		$thereturn="Updating ".$module["title"]." Module\n";
		
		if(!verifyAdminLogin($db,$_GET["u"],$_GET["p"])){
		
			$thereturn="Update Requires Administrative Access.\n\n";
			return $thereturn;
			
		}//end if
				
		$newVersion = $_GET["v"];
		
		$querystatement="SELECT version FROM modules WHERE name = '".$module["name"]."'";
		$queryresult=$db->query($querystatement);

		if($db->numRows($queryresult)) {
			
			$ver=$db->fetchArray($queryresult);
			while($ver["version"] != $newVersion){
			
				switch($ver["version"]){
				
					// ================================================================================================
					case "1.0":
						$nextVersion = "1.01";
						
						$thereturn.="Updating ".$module["title"]." Module to ".$nextVersion."\n";
			
						$thereturn.= processSQLfile($db,"updatev1.01.sql");
	
						//Updating Module Table
						$querystatement="
							UPDATE 
								modules 
							SET 
								version='".$nextVersion."'
							WHERE 
								name = '".$module["name"]."'";
								
						$queryresult = $db->query($querystatement);
						
						$thereturn.=" - Updated module record with new version \n";
						
						$thereturn.="Update of ".$module["name"]." to ".$nextVersion." Finished\n\n";
				
						$ver["version"] = $nextVersion;
		
						break;
						
					// ================================================================================================
					case "1.01":
						$nextVersion = "1.02";
						
						$thereturn.="Updating ".$module["title"]." Module to ".$nextVersion."\n";
			
						//$thereturn.= processSQLfile($db,"updatev1.01.sql");
	
						//Updating Module Table
						$querystatement="
							UPDATE 
								modules 
							SET 
								version='".$nextVersion."'
							WHERE 
								name = '".$module["name"]."'";
								
						$queryresult = $db->query($querystatement);
						
						$thereturn.=" - Updated module record with new version \n";
						
						$thereturn.="Update of ".$module["name"]." to ".$nextVersion." Finished\n\n";
				
						$ver["version"] = $nextVersion;
		
						break;
				}//end switch
				
			}//end while
			
		} else 
			$thereturn = "Cannot update module ".$module["title"].": Module not installed.";

		return $thereturn;

	}//end update		

	//PROCESSING
	// ===================================================================================================
	
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
				
				$thereturn = doUpdate($db);	

			}//endif
			
		}//end if
		
	} else
		$thereturn = "Could not access settings.php";	
				
		header('Content-Type: text/xml');
		?><?php echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'; ?>
<response><?php echo $thereturn?></response>