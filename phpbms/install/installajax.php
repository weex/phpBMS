<?php
/*
 $Rev: 427 $ | $LastChangedBy: nate $
 $LastChangedDate: 2008-08-13 12:09:00 -0600 (Wed, 13 Aug 2008) $
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
define("APP_DEBUG",false);
define("noStartup",true);

require("install_include.php");
require("../include/session.php");



class installAjax extends installUpdateBase{

	function testConnection(){

		if($this->db->connect())
			return $this->returnJSON(true, "Connection to Database Established");
		else
			return $this->returnJSON(false, $this->db->getError());


	}//end function testConnection


	function createDatabase(){

		if(!$this->db->connect())
			return $this->returnJSON(false, "Could not connect to database ".$this->db->getError());

		$createstatement = "CREATE DATABASE ".MYSQL_DATABASE;
		$this->db->query($createstatement);

		if($this->db->error)
			return $this->returnJSON(false, $this->db->error);
		else
			return $this->returnJSON(true, "Database '".MYSQL_DATABASE."' created successfully");

	}//end function createDatabase


	function coreDataInstall($extras){

		$extras = explode("::", $extras);

		if(!$this->db->connect())
			return $this->returnJSON(false, "Could not connect to database ".$this->db->getError());

		if(!$this->db->selectSchema())
			return $this->returnJSON(false, "Could not open database schema '".MYSQL_DATABASE."'");

		$installer = new installer($this->db);

		//Run create table sql file ocreate the tables
		$tempReturn = $installer->createTables("createtables.sql");

		if($tempReturn !== true)
			return $this->returnJSON(false, $tempReturn);


		// Create an array of table names.  Each one has a correspondng
		// SQL file.  Each file consists of a series of insert statements
		// (one per line) that populates that table.
		$tables = array(
			"choices",
			"files",
			"menu",
			"tabs",
			"modules",
			"notes",
			"reports",
			"scheduler",
			"tablecolumns",
			"tabledefs",
			"tablefindoptions",
			"tablegroupings",
			"tableoptions",
			"tablesearchablefields",
			"smartsearches",
			"users",
			"settings",
			"widgets"
		);

		$thereturn = "";

		//now we run the import for each file
		foreach($tables as $table){

			$tempReturn = $installer->processSQLFile($table.".sql");

			if($tempReturn !== true)
				$thereturn .= $tempReturn;

		}//end foreach

		// next we need to generate the encrption seed and update the
		// setting in the table
		$newSeed = $this->generateRandomString(16);

		$updatestatement = "
			UPDATE
				`settings`
			SET
				`value` = '".$newSeed."'
			WHERE
				`name` = 'encryption_seed'";

		$this->db->query($updatestatement);
		if($this->db->error)
			return $this->returnJSON(false, "Error Updating Encryption Seed: ".$this->db->error);

		//finally we have to assign the admin password;

		$newPass = $this->generateRandomString(8);

		$updatestatement = "
			UPDATE
				`users`
			SET
				`email` = '".$extras[1]."',
				`password` = ENCODE('".$newPass."' , '".$newSeed."')
			WHERE
				`id` = 2";

		$this->db->query($updatestatement);
		if($this->db->error)
			return $this->returnJSON(false, "Error Updating Admin Password: ".$this->db->error);

		//Update Application Name
		$updatestatement = "
			UPDATE
				`settings`
			SET
				`value` = '".mysql_real_escape_string($extras["0"])."'
			WHERE
				`name` = 'application_name'";

		$this->db->query($updatestatement);
		if($this->db->error)
			return $this->returnJSON(false, "Error Updating Admin Password: ".$this->db->error);

		if($thereturn)
			return $this->returnJSON(false, $thereturn);
		else{

			//Let's also sen an e-mail to the administrator with his password
			if($extras[1]){

				$to = $extras[1];
				$from = "From: DoNotReply@phpbms.org";
				$subject = "Your phpBMS login information";
				$message ="phpBMS Core Program was installed successfully.  Your initial login information is:

	username: admin
	password: ".$newPass."

If you need further assistance, please use the community forum at http://www.phpbms.org/forum";

				//attempt to e-mail person
				@ mail($to, $subject, $message, $from);

			}//end if


			return $this->returnJSON(true, "Core Data Installed Successfully.", $newPass);

		}//endif thereturn

	}//end function coreDataInstall


	function moduleInstall($module){

		if(!$this->db->connect())
			return $this->returnJSON(false, "Could not connect to database ".$this->db->getError());

		if(!$this->db->selectSchema())
			return $this->returnJSON(false, "Could not open database schema '".MYSQL_DATABASE."'");

		$moduleFile = "../modules/".$module."/install/install.php";
		if(!file_exists($moduleFile))
			return $this->returnJSON(false, "Module install file '".$moduleFile."' does not exist.");

		@ include_once($moduleFile);

		return $theModule->install();

	}//end function $module


	function process($command, $extras = null){

		$this->phpbmsSession = new phpbmsSession;

		if($this->phpbmsSession->loadDBSettings(false)){

			@ include_once("include/db.php");

			$this->db = new db(false);
			$this->db->stopOnError = false;
			$this->db->showError = false;
			$this->db->logError = false;

		} else
			return $this->returnJSON(false, "Could not open session.php file");

		switch($command){

			case "testconnection":
				echo $this->testConnection();
				break;

			case "createdatabase":
				echo $this->createDatabase();
				break;

			case "coredatainstall":
				echo $this->coreDataInstall($extras);
				break;

			case "moduleinstall":
				if(!$extras)
					echo $this->returnJSON(false, "module not passed");
				echo $this->moduleInstall($extras);
				break;

			default:
				echo $this->returnJSON(false, "command not recognized");
				break;

		}//endswitch

	}//endfunction process


	function generateRandomString($length){

		$chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ2345689";
		$string = "";
		$modChars = strlen($chars) - 1;

		srand((double) microtime() * 1000000);

		while(strlen($string) < $length){

			$num = rand() % $modChars;
			$string .= substr($chars, $num, 1);

		}//endwhile

		return $string;

	}//endFunction generateRandomString

}//end class installAjax



class installModuleAjax extends installUpdateBase{

	var $createTablesSQL = "createtables.sql";
	var $tables = array();
	var $pathToModule = "";


	function installModuleAjax($db, $phpbmsSession, $pathToModule){

		$this->db = $db;
		$this->phpbmsSession = $phpbmsSession;
		$this->pathToModule = $pathToModule;

	}//end function


	function install(){

		if(!$this->db->connect())
			return $this->returnJSON(false, "Could not connect to database ".$this->db->getError());

		if(!$this->db->selectSchema())
			return $this->returnJSON(false, "Could not open database schema '".MYSQL_DATABASE."'");

		$installer = new installer($this->db);

		//Run create table sql file ocreate the tables
		$tempReturn = $installer->createTables($this->pathToModule.$this->createTablesSQL);

		if($tempReturn !== true)
			return $this->returnJSON(false, $tempReturn);

		$thereturn = "";

		//now we run the import for each file
		foreach($this->tables as $table){

			$tempReturn = $installer->processSQLFile($this->pathToModule.$table.".sql");

			if($tempReturn !== true)
				$thereturn .= $tempReturn;

		}//end foreach

		if($thereturn)
			return $this->returnJSON(false, $thereturn);
		else
			return $this->returnJSON(true, "Module Installed");


	}//end function install

}//end class installModuleAjax



// START PROCESSING
//==============================================================================

$installAjax = new installAjax();

if(!isset($_GET["command"]))
	echo $installAjax->returnJSON("failure", "No command given.");

if(!isset($_GET["extras"]))
	$installAjax->process($_GET["command"]);
else
	$installAjax->process($_GET["command"], $_GET["extras"]);
