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

//=====================================================================================
// START PROCESSING
//======================================================================================

		$phpbmsSession = new phpbmsSession;
		$thereturn="Error Processing: No Command Given";
		if(isset($_GET["command"])){

			$success = $phpbmsSession->loadDBSettings(false);
			include_once("include/db.php");
			$db = new db(false);
			$db->stopOnError = false;
			$db->showError = false;
			$db->logError = false;

			if($success !== false){
				switch($_GET["command"]){
	
					case "testconnection":
					//======================================================================================
					
						if($db->connect())
							$thereturn = "Connection to MySQL Established";
						else 
							$thereturn = "Could Not Establish Connection To MySQL Server: Check server, username and password"; 
						
					break;
					
					
					case "createdatabase":
					//======================================================================================
	
						if(!$db->connect())
							$thereturn = "Could Not Establish Connection To MySQL Server: Check server, username and password"; 
						else{
							$querystatement = "CREATE DATABASE ".MYSQL_DATABASE;
							$queryreult = $db->query($querystatement);
							
							if($db->error)
								$thereturn = "Could not Create Database: ".$db->error;
							else
								$thereturn = "Database ".MYSQL_DATABASE." created";
						}
							
					break;
					
		
					case "populatedata":
	
						if(!$db->connect())
							$thereturn = "Could Not Establish Connection To MySQL Server: Check server, username and password"; 
						else{						
							if($db->selectSchema()){
								$thereturn = "";
								$tempreturn = createTables($db,"createtables.sql");
								
								if($tempreturn === true){
									$thereturn.= "Done Creating Tables \n";
									
									$tables = array(
										"choices",
										"menu",
										"tabs",
										"modules",
										"notes",
										"reports",
										"tablecolumns",
										"tabledefs",
										"tablefindoptions",
										"tablegroupings",
										"tableoptions",
										"tablesearchablefields",
										"smartsearches",
										"users",
										"settings",
										"files",
									);
									foreach($tables as $table){
										
										$failure = false;
										$tempreturn = importData($db,$table);
										$thereturn .= $tempreturn;
										if(!strpos($tempreturn,"complete.") === false){
											$failed = true;
										}
										
									}//end foreach
									
									if(!$failure){
										$thereturn .= "_________________________\n";			
										$thereturn .= "Done Installing Core Data\n";
									}
								
								} else
									$thereturn = $tempreturn;
							
							} else
								$thereturn = "Database (schema) ".MYSQL_DATABASE." could not be selected";
						}//end if
				
					break;
					
				}//end switch
			} else
				$thereturn = "Could not access settings.php";
			
		}//end if
		
		
		
		header('Content-Type: text/xml');
		?><?php echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'; ?>
<response><?php echo $thereturn?></response>