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
require("../include/common_functions.php");


class updateAjax extends installUpdateBase{

	function coreDataUpdate(){

		if(!$this->db->connect())
			return $this->returnJSON(false, "Could not connect to database ".$this->db->getError());

		if(!$this->db->selectSchema())
			return $this->returnJSON(false, "Could not open database schema '".MYSQL_DATABASE."'");

		$updater = new installer($this->db);

		//get the current Version
		$currentVersion = $this->getCurrentVersion("base");
		include("../phpbmsversion.php");
		$newVersion = $modules["base"]["version"];

		//next we loop through ech upgrade process
		while($currentVersion != $newVersion){

			switch($currentVersion){

				// ================================================================================================
				case 0.8:

					$version = 0.9;
					//Processing Data Structure Changes
					$thereturn = $updater->processSQLfile("updatev".$version.".sql");
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					//Updating Module Table
					$thereturn = $this->updateModuleVersion("base", $version);
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					$currentVersion = $version;

					break;

				// ================================================================================================
				case 0.9:

					$version = 0.92;
					//Processing Data Structure Changes
					$thereturn = $updater->processSQLfile("updatev".$version.".sql");
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					//Updating Module Table
					$thereturn = $this->updateModuleVersion("base", $version);
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					$currentVersion = $version;

					break;

				// ================================================================================================
				case 0.92:

					$version = 0.94;
					//Processing Data Structure Changes
					$thereturn = $updater->processSQLfile("updatev".$version.".sql");
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					//Updating Module Table
					$thereturn = $this->updateModuleVersion("base", $version);
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					$currentVersion = $version;

					break;

				// ================================================================================================
				case 0.94:

					$version = 0.96;
					//Processing Data Structure Changes
					$thereturn = $updater->processSQLfile("updatev".$version.".sql");
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					//Updating Module Table
					$thereturn = $this->updateModuleVersion("base", $version);
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					$currentVersion = $version;

					break;

				// ================================================================================================
				case 0.96:

					$version = 0.98;
					//Processing Data Structure Changes
					$thereturn = $updater->processSQLfile("updatev".$version.".sql");
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

                                        $this->v098UpdateUUIDS("tabledefs", "tbld:", true);
                                        $this->v098UpdateUUIDS("users", "usr:", true);
                                        $this->v098UpdateUUIDS("roles", "role:", true);
                                        $this->v098UpdateUUIDS("reports", "rpt:");
                                        $this->v098UpdateUUIDS("scheduler", "schd:");
                                        $this->v098UpdateUUIDS("smartsearches", "smrt:");

                                        $this->v098UpdateNotesUUID();

					//Updating Module Table
					$thereturn = $this->updateModuleVersion("base", $version);
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					$currentVersion = $version;

					break;

			}//endswitch currentVersion

		}//endwhile currentversion/newversion

		return $this->returnJSON(true, "Update Successful.");

	}//end function coreDataUpdate


	function moduleUpdate($module){

		if(!$this->db->connect())
			return $this->returnJSON(false, "Could not connect to database ".$this->db->getError());

		if(!$this->db->selectSchema())
			return $this->returnJSON(false, "Could not open database schema '".MYSQL_DATABASE."'");

		$moduleFile = "../modules/".$module."/install/update.php";
		if(!file_exists($moduleFile))
			return $this->returnJSON(false, "Module update file '".$moduleFile."' does not exist.");

		$versionFile = "../modules/".$module."/version.php";
		if(!file_exists($versionFile))
			return $this->returnJSON(false, "Module version file '".$versionFile."' does not exist.");

		@ include($moduleFile);
		@ include($versionFile);

		$theModule->currentVersion = $this->getCurrentVersion($theModule->moduleName);
		return $theModule->update();

	}//end function coreDataUpdate


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

			case "coredataupdate":
				echo $this->coreDataUpdate();
				break;

			case "moduleupdate":
				if(!$extras)
					echo $this->returnJSON(false, "module not passed");
				echo $this->moduleUpdate($extras);
				break;

			default:
				echo $this->returnJSON(false, "command not recognized");
				break;

		}//endswitch

	}//endfunction process


	function getCurrentVersion($module){

		$querystatement = "
			SELECT
				version
			FROM
				modules
			WHERE
				name = '".$module."'";

		$queryresult = $this->db->query($querystatement);
		if($this->db->error)
			$error = new appError(-600,"Could not retrieve current version information for ".$module.": ".$this->db->error,"Cannot load module information",true,true,false);

		if($this->db->numRows($queryresult)){

			$therecord = $this->db->fetchArray($queryresult);

			return floatval($therecord["version"]);

		} else
			return 0;

	}//end function getCurrentVersion


	function updateModuleVersion($module, $version){

		$updatestatement = "
			UPDATE
				`modules`
			SET
				`version` ='".$version."'
			WHERE
				`name` = '".$module."'";

		$this->db->query($updatestatement);

		if($this->db->error)
			return $this->returnJSON(false, "Could not update module '".$version."' version to '".$version."': ".$this->db->error);
		else
			return true;

	}//end function updateModuleVersion



        // ==== v0.98 functions ================================================


        function v098UpdateNotesUUID(){

            $updatestatement = "
                UPDATE
                    notes
                SET
                    parentid = NULL
                WHERE
                    parentid = '0'";

            $this->db->query($updatestatement);

            $querystatement = "
                SELECT
                    id,
                    assignedtoid,
                    assignebyid
                FROM
                    notes";

            $queryresult = $this->db->query($querystatement);

            while($therecord = $this->db->fetchArray($queryresult)){

                $noteList[$therecord["id"]] = uuid("note:");

                $updatestatement = "
                    UPDATE
                        notes
                    SET
                        uuid = '".$noteList[$therecord["id"]]."',
                        assignedtoid = '".$this->uuids["users"][$therecord["assignedtoid"]]."',
                        assignedbyid = '".$this->uuids["users"][$therecord["assignedbyid"]]."',
                    WHERE
                        id =".$therecord["id"];

                $this->db->query($updatestatement);

            }//endwhile

            //next we need to update notes who have a parentid
            $querystatement = "
                SELECT
                    id,
                    parentid
                FROM
                    notes
                WHERE
                    parentid IS NOT NULL";

            $queryresult = $this->db->query($querystatement);

            while($therecord = $this->db->fetchArray($queryresult)){

                $updatestatement = "
                    UPDATE
                        notes
                    SET
                        parentid ='".$noteList[$therecord["parentid"]]."'
                    WHERE
                        id = ".$therecord["id"];

                $this->db->query($updatestatement);

            }//endwhile

        }//end function v098UpdateNotesUUID


        function v098UpdateUUIDS($tablename, $prefix, $populateList = false){

            $querystatement = "
                    SELECT
                        `id`
                    FROM
                        `".$tablename."`
                    WHERE
                        uuid = ''
                        OR uuid IS NULL";

            $queryresult = $this->db->query($querystatement);

            if($populateList)
                $uuidList[0] = "";

            while($therecord = $this->db->fetchArray($queryresult)){

                $uuid = uuid($prefix);
                if($populateList)
                    $uuidList[$therecord["id"]] = $uuid;
                $updatestatement = "
                    UPDATE
                            `".$tablename."`
                    SET
                            `uuid`='".$uuid."'
                    WHERE
                            `id` = '".$therecord["id"]."';
                    ";

                $this->db->query($updatestatement);

            }//end while

            if($populateList)
                $this->uuids[$tablename] = $uuidList;

        }//end function

}//end class updateAjax


class updateModuleAjax extends installUpdateBase{

	var $pathToModule = "";
	var $updateVersions = array();
	var $moduleName;
	var $newVersion = 0;
	var $currentVersion = 100000;


	function updateModuleAjax($db, $phpbmsSession, $moduleName, $pathToModule){

		$this->db = $db;
		$this->phpbmsSession = $phpbmsSession;
		$this->pathToModule = $pathToModule;
		$this->moduleName = $moduleName;

	}//end function


	function update(){

		if(!$this->db->connect())
			return $this->returnJSON(false, "Could not connect to database ".$this->db->getError());

		if(!$this->db->selectSchema())
			return $this->returnJSON(false, "Could not open database schema '".MYSQL_DATABASE."'");

		$updater = new installer($this->db);

		//next we loop through each upgrade process
		foreach($this->updateVersions as $version){

			if($this->currentVersion <= $version){

				$thereturn = $updater->processSQLfile($this->pathToModule."updatev".$version.".sql");
				if($thereturn !== true)
					return $this->returnJSON(false, $thereturn);

				//Updating Module Table
				$thereturn = $this->updateModuleVersion($this->moduleName, $version);
				if($thereturn !== true)
					return $this->returnJSON(false, $thereturn);

			}//endif

		}//endforeach

		return $this->returnJSON(true, "Module '".$this->moduleName."' Updated");

	}//end function update


	function updateModuleVersion($module, $version){

		$updatestatement = "
			UPDATE
				`modules`
			SET
				`version` ='".$version."'
			WHERE
				`name` = '".$module."'";

		$this->db->query($updatestatement);

		if($this->db->error)
			return $this->returnJSON(false, "Could not update module '".$version."' version to '".$version."': ".$this->db->error);
		else
			return true;

	}//end function updateModuleVersion

}//end class updateModuleAjax


// START PROCESSING
//==============================================================================

$updateAjax = new updateAjax();

if(!isset($_GET["command"]))
	echo $updateAjax->returnJSON("failure", "No command given.");

if(!isset($_GET["extras"]))
	$updateAjax->process($_GET["command"]);
else
	$updateAjax->process($_GET["command"], $_GET["extras"]);
