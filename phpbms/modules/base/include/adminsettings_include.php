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

/**
 * Handles saving of settings for phpBMS
 *
 * Processes and retrieves settings from the settings table.
 * @author Brian Rieb <brieb@kreotek.com>
 *
 */
class settings{

    /**
     * phpBMS DB object
     * @var object
     */
    var $db;


    /**
     * Constructor sets up {@link $db}
     */
    function settings($db){

	$this->db = $db;

    }//end function constructor


    /**
     * Retrieves settings from database and populates array with them
     *
     */
    function getSettings(){

	$therecord = array();

	$querystatement = "
	    SELECT
		`name`,
		`value`
	    FROM
		`settings`";

	$queryresult = $this->db->query($querystatement);

	while($setting = $this->db->fetchArray($queryresult))
		$therecord[$setting["name"]] = $setting["value"];

	return $therecord;

    }//end function getSettings


    /**
     * Updates phpBMS settings
     *
     * Updates the phpBMS settings, including the logo graphic
     *
     * @param array $variables variables passed from the form
     * @global object phpbms
     */
    function updateSettings($variables){

	global $phpbms;

	if(!isset($variables["persistent_login"]))
	    $variables["persistent_login"] = 0;

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
					) VALUES (
					    '".$settingvalue."',
					    '".mysql_real_escape_string($settingname)."'
					    )";

				$this->db-query($insertstatement);

			    }//end if

			}//end if

		}//end if

	    }//endif

	}//end foreach

	// deal with logo graphic.
	if(isset($_FILES["printedlogo"])){

	    $validFileTypes = array(
		"image/png",
		"image/x-png",
		"image/jpg",
		"image/jpeg",
		"imagep/jpeg",
	    );

	    if(in_array($_FILES["printedlogo"]["type"], $validFileTypes)){

		if (function_exists('file_get_contents')) {

		    $file = mysql_real_escape_string(file_get_contents($_FILES['printedlogo']['tmp_name']));

		} else {

			// If using PHP < 4.3.0 use the following:
			$file = mysql_real_escape_string(fread(fopen($_FILES['printedlogo']['tmp_name'], 'r'), filesize($_FILES['printedlogo']['tmp_name'])));

		}//endif

		if($_FILES["printedlogo"]["type"] == "image/jpeg")
		    $name = "logo.jpg";
		else
		    $name = "logo.png";

		$updatestatement = "
		    UPDATE
			`files`
		    SET
			`file` = '".$file."',
			`type` = '".$_FILES["printedlogo"]["type"]."',
			`name`='".$name."'
		    WHERE
			id = 1";

		$this->db->query($updatestatement);

	    }//endif file types

	}//endif file exists

	return true;

    }//end function updateSettings


    /**
     * Updates the password encryption seed and the password for the current user
     *
     * Updates the encryption seed, and alsoupdates the encryption seed for the
     * given user (current)
     *
     * @param string $newseed the new encrptions seed
     * @param string $currpassword the user's current password
     * @param integer $userid the user's id
     */
    function updateEncyptionSeed($newseed, $currpassword, $userid){

	$userid = (int) $userid;

	//first let's make sure the password matches
	$querystatement="
	    SELECT
		id
	    FROM
		users
	    WHERE
		id = ".$userid."
		AND password = ENCODE('".$currpassword."','".ENCRYPTION_SEED."')";

	$queryresult = $this->db->query($querystatement);

	if(!$this->db->numRows($queryresult))
	    return "Encryption Seed not Updated: Invalid Current Password";

	//let's update the encryption seed then
	$querystatement = "
	    UPDATE
		`settings`
	    SET
		`value` = '".$newseed."'
	    WHERE
		`name` = 'encryption_seed'";

	$queryresult = $this->db->query($querystatement);

	//last, reencode the current password
	$querystatement = "
	    UPDATE
		users
	    SET
		password = ENCODE('".$currpassword."','".$newseed."')
	    WHERE
	     id = ".$userid;

	$queryresult=$this->db->query($querystatement);

	//rencode all other passwords
	$querystatement = "
	    UPDATE
		users
	    SET
		password = ENCODE(DECODE(password,'".ENCRYPTION_SEED."'),'".$newseed."')
	    WHERE
		id !=".$userid;

	$queryresult=$this->db->query($querystatement);

	return "Encryption Seed Updated.";

    }//end function updateEncryptionSeed


    /**
    * processes settings form
    *
    * Processes the form that updates the settings, or the encryption seed
    *
    * @param array $variables variables array passed from the $_POST
    */
    function processForm($variables){

	$variables = addSlashesToArray($variables);

	switch($variables["command"]){

	    case "save":
		if($this->updateSettings($variables))
		    $statusmessage="Settings Updated";
		break;

	    case "encryption seed":
		if(isset($variables["changeseed"]))
		    $statusmessage = $this->updateEncyptionSeed($variables["encryption_seed"],$variables["currentpassword"],$_SESSION["userinfo"]["id"]);
		break;

	}//endswitch

	return $statusmessage;

    }//end function processForm


    /**
     * displays options values for stylesheet select
     *
     * Displays the option tags for the stylesheet select
     *
     * @param string $stylesheet the current stylesheet
     */
    function displayStylesheets($stylesheet){

	$thedir="../../common/stylesheet";
	$thedir_stream = @opendir($thedir);

	while($entry = @ readdir($thedir_stream)){

	    if ($entry!="." and  $entry!=".." and is_dir($thedir."/".$entry)) {

		?><option value="<?php echo $entry?>" <?php if($entry = $stylesheet) echo 'selected="selected"'; ?>><?php echo $entry?></option><?php

	    }//endif

	}//endwhile

    }//end function displayStyleSheets

    
    /**
     * Check to see if the scheduler has ever run
     */
    function checkForSchedulerRunning(){

	//first, if this is within the first day of the installation, we skip the check

	$querystatement = "SELECT creationdate FROM users WHERE id = 1";

	$queryresult = $this->db->query($querystatement);

	$therecord = $this->db->fetchArray($queryresult);

	if(stringToDate($therecord["creationdate"], "SQL") < strtotime("yesterday")){

	    $querystatement = "
		SELECT
		    MAX(lastrun) AS lastrun
		FROM
		    scheduler";

	    $queryresult = $this->db->query($querystatement);

	    $therecord = $this->db->fetchArray($queryresult);

	    if(!$therecord["lastrun"])
		return false;

	}//endif

	return true;

    }//end function checkForSchedulerRunning

}//end class settings
?>
