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


				// ENTER YOUR MODULE NAME HERE
				$thereturn  = "Recurring Invoice Installation\n";
				$thereturn .= "______________________________\n";
				
				//MAKE SURE THERE IS A createtables.sql FILE
				//which houses all your SQL create statements
				// for any tables you will create.
				$tempreturn = createTables($db,"createtables.sql");
				
				if($tempreturn === true){
					
					$thereturn.= "Done Creating Tables \n";
					
					
					// NEXT, DEFINE AN ARRAY OF TABLE NAMES
					// for any tables that need initial data populated. (including existing tables)
					// for each table, make sure there
					// is a corresponding SQL file
					// with insert statements for all the records needing to be imported
					
					/*
						Need to add entries for tabledefs, tablecolumns, tablefindoptions, tableoptions, and
						tablesearchablefields
					*/
					$tables = array(
						"menu",
						"modules",
						"roles",
						"scheduler",
						"tabledefs",
						"tablecolumns",
						"tableoptions",
						"tablefindoptions",
						"tablesearchablefields",
						"tabs",
						"users",
					);
					foreach($tables as $table){
						
						$failure = false;
						$tempreturn = importData($db,$table);
						$thereturn .= $tempreturn;
						if(!strpos($tempreturn,"complete.") === false){
							$failed = true;
						}
						
					}//end foreach
					
					
					//MAKE SURE TO set the done message for your module.
					if(!$failure){
						$thereturn .= "________________________\n";			
						$thereturn .= "Done Installing BMS Data\n";
					}
				} else
					$thereturn = $tempreturn;				
			}		
		}
	} else
		$thereturn = "Could not access settings.php";
		
		
		header('Content-Type: text/xml');
		?><?php echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'; ?>
<response><?php echo $thereturn?></response>