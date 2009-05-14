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

// Turn on/or off debugging
@ define("APP_DEBUG", true);
if(APP_DEBUG)
	error_reporting(E_ALL);


// Error Class - This class reports errors.  It can also log these errors
// to the phpBMS log table in some cases.
class appError{

 	var $number=0;
	var $title="";
	var $details="";
	var $stop=true;
	var $logerror=true;
	var $format="xhtml";

	//init
	function appError($number=0,$details="",$title="",$display=false,$stop=true,$logerror=true,$format="xhtml"){

		$this->title = $title;
		$this->details = $details;
		$this->stop = $stop;

		$this->logerror = $logerror;
		$this->format = $format;
		$this->number = $number;

		// find a predetermined title;
		if($this->number<0){

			switch($number){
				case -300:
					$this->title = "settings.php file not readable";
					$this->details =
					'If this is the initial installation of the program,
					you may wan to run the installer.  Use your web browser to navigate to:<br /><br />

					<a href="'.APP_PATH.'install">phpBMS Installation</a><br /><br />

					If your application already has a settings.php file in the main phpbms directory, you may need to give your web server rights to read the file.';
					break;
				case -400:
				case -410:
				case -420:
				case -430:
				case -440:
				case -450:
				case -460:
					$this->title="Database Error";
					break;
			}//end case;

		}//endif this->number

		if($display || APP_DEBUG) $this->display($format);
		if($logerror) $this->logError();
		if($this->stop) exit;

	}//eend function init


	//This function outputs the error to screen either in
	// XHTML, plain text, or JSON format
	function display($format = NULL){

		if($format == NULL)
			$format = $this->format;

		switch(strtolower($format)){

			case "json":

				$return["id"] = $this->number;
				$return["title"] = $this->title;
				$return["details"] = $this->details;

				echo json_encode($return);

				break;

			case "xhtml":

				// Unsure if this line is needed, as it limits what we can do with detail print out
				//$this->details = str_replace("\n","<br />", htmlspecialchars($this->details,ENT_COMPAT,"UTF-8"));

				if(defined("APP_PATH")){

					if(!defined("STYLESHEET"))
						define("STYLESHEET","mozilla");

					?><link href="<?php echo APP_PATH ?>common/stylesheet/<?php echo STYLESHEET ?>/base.css" rel="stylesheet" type="text/css" /><?php

				} else {

					//if the app_path is not defined, we can try including the mozilla stylesheet, relative to
					// the assumed phpbms root
					?><link href="common/stylesheet/mozilla/base.css" rel="stylesheet" type="text/css" /><?php

				}//end if

				?><div class="bodyline">
					<h1><span>phpBMS Error: <?php echo $this->number; if($this->title) echo " ".$this->title?></span></h1>
					<?php if($this->details) {?>
					<div class="box">
						<?php echo $this->details?>
					</div>
					<?php  } //end if?>
				</div><?php

				break;

			default:

				echo "phpBMS Error: ".$this->number;
				if($this->title) echo ": ".$this->title;
				if($this->details) echo  " - ".$this->details;

				break;
		}//end switch
	}// end dispaly function


	// this function logs the error in the phpBMS log table
	function logError(){

		$message = $_SERVER["REQUEST_URI"]."\n";
		$message .= $this->number;

		if($this->title)
			$message.=": ".$this->title;

		if($this->details)
			$message.="\n\n".$this->details;

		$log = new phpbmsLog($message,"ERROR");

	}//end logError

}//end appError class


// This is the class for logging items tot the phpBMS
// log table;
class phpbmsLog{

	var $db = NULL;
	var $type = "ERROR";
	var $value = "";
	var $userid = 2;

	function phpbmsLog($value=NULL,$type=NULL,$userid=NULL,$db=NULL,$sendLog=true){

		//in most cases, it is prudent for the log object to have it's own DB object
		// so that it can properly supress errors without goofing things up.
		if($db){
			if(is_object($db)){

				$this->db = $db;

				$this->db->showError=false;
				$this->db->logError=false;
				$this->db->stopOnError=false;

			}//endif object

		} else {

			if(class_exists("db")){

				$this->db= new db(false);

				$this->db->showError=false;
				$this->db->logError=false;
				$this->db->stopOnError=false;

				$this->db->connect();
				$this->db->selectSchema();

			} else
				return false;

		}//endif db

		if($value)
			$this->value = $value;

		if($type)
			$this->type = $type;

		if($userid)
			$this->userid = ((int) $userid);

		if($sendLog)
			return $this->sendLog();
		else
			return true;

	}//end function init


	// inserts record into log table
	function sendLog(){

		$ip = $_SERVER["REMOTE_ADDR"];

		$insertstatement = "
			INSERT INTO
				`log`
			(`type`, `value`, `userid`, `ip`) VALUES (
				'".mysql_real_escape_string($this->type)."',
				'".mysql_real_escape_string($this->value)."',
				".$this->userid.",
				'".$ip."'
			)";

		$this->db->query($insertstatement);

	}//end function sendLog

}//end class phpbmslog


// This class handles the loading of the database, session and application
// variables, as well as verifying API level logins
class phpbmsSession{

	var $db = null;

	function loadDBSettings($reportError = true){

		// This functions looks for the settings.php file, and loads
		// the database variables as constants.  As an added benefit
		// it adds the phpBMS root as an included path.


		//need to look for settings file... only go up a total of 10 directories
		$currdirectory = getcwd();

		//Prep the setting of the application path;
		$currentURL = explode("/",$_SERVER["PHP_SELF"]);
		array_pop($currentURL);

		$count = 0;
		$path = "";

		//We need to find the applications root
		while(!file_exists("phpbmsversion.php") && $count < 9){

			$path.="../";
			@ chdir("../");
			$count++;

		}//end while

		//Now set the Web location (APP_PATH)
		$appPath = "/";
		for($i = 0; $i < count($currentURL) - $count; $i++)
			if($currentURL[$i])
				$appPath .= $currentURL[$i]."/";

		define("APP_PATH", $appPath);

		$settingsfile =  @ fopen("settings.php","r");

		if($settingsfile){

			//loop through the settings file and load variables into the session
			while( !feof($settingsfile)) {

				$line = NULL;
				$key = NULL;
				$value = NULL;
				$line = @ fscanf($settingsfile,"%[^=]=%[^[]]",$key,$value);

				if ($line){

					$key=trim($key);
					$value=trim($value);

					if($key!="" and !strpos($key,"]")){

						$startpos=strpos($value,"\"");
						$endpos=strrpos($value,"\"");

						if($endpos!=false)
							$value=substr($value,$startpos+1,$endpos-$startpos-1);

						if(strpos($key,"mysql_")===0)
							define(strtoupper($key),$value);

					}//endif key

				}//endif line

			}//endwhile

			@ fclose($settingsfile);

			//For legacy installations where pconnect is not set
			if(!defined("MYSQL_PCONNECT"))
				define("MYSQL_CONNECT",true);

			//this adds the phpbms root to the include path
			if ( !defined( "PATH_SEPARATOR" ) ) {

				//if we cannot determin the OS, we will assume its unix
				if(!isset($_ENV["OS"]))
					$_ENV["OS"] = "unix";

				if ( strpos( $_ENV["OS"], "Win" ) !== false )
					define( "PATH_SEPARATOR", ";" );
				else
					define( "PATH_SEPARATOR", ":" );

			}//end if

			$pathToAdd = @ getcwd();

			//Now we include the root application path to php's include path
			if(ini_set("include_path", ini_get("include_path").PATH_SEPARATOR.$pathToAdd) === false && $reportError)
				$error = new appError(-310, "Your implementation of PHP does not allow changing of the include path. You may need to modify your PHP settings to allow phpBMS to modify this php ini setting. If you are using a web hosting company, you may need to contact them to allow this.", "Cannot add to include path", true, true, false);


			//return directory to current directory
			@ chdir ($currdirectory);

			return $path;

		} else {

			if($reportError)
				$error = new appError(-300,"","",true,true,false);

			return false;

		}//endif settingsfile

	}//end function


	function loadSettings($encoding = "utf8"){

		// We are going to make sure that we are using utf8
		// but it works only in mySQL 5, so we supress errors
		// when trying it.
		if($this->db==NULL)
			$error=new appError(-310,"","Database not loaded");

		$this->db->logError = false;
		$this->db->stopOnError = false;

		$this->db->setEncoding($encoding);

		$this->db->logError = true;

		$querystatement = "SELECT name,value FROM settings";

		$queryresult = $this->db->query($querystatement);

		if(!$queryresult){

			$error= new appError(-310,"If you have not ran the update script for phpBMS, please run it before logging in.","Could Not Retrieve Settings From Database");
			return false;

		} else {

			while($therecord=$this->db->fetchArray($queryresult)){

				//old versions used a reserved constant in certain php versions
				if($therecord["name"] == "currency_symbol")
					$therecord["name"] = "currency_sym";

				if(!defined(strtoupper($therecord["name"])))
					define(strtoupper($therecord["name"]),$therecord["value"]);
			}//end while

			// This following code is for windows boxen, because they lack some server varables as well
			// formating options for the strftime function
			if(!isset($_SERVER['REQUEST_URI'])) {
				$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];

				if(!defined("HOUR_FORMAT"))
					define("HOUR_FORMAT","%I");

				// Append the query string if it exists and isn't null
				if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
					$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
			} else
				if(!defined("HOUR_FORMAT"))
					define("HOUR_FORMAT","%l");

			return true;

		}//endif queryresult

	}//end function


	// This is in a function in case we want to do sessions differently in the future
	function startSession(){

		session_name("phpBMS".preg_replace('/\W/',"",APPLICATION_NAME)."v096ID");
		session_start();

	}//end function startSesion


	function verifyAPIlogin($user, $pass, $format = "json"){

		$thereturn = false;
		$this->db->stopOnError = false;

		$querystatement = "
			SELECT
				id,
				firstname,
				lastname,
				email,
				phone,
				department,
				employeenumber,
				admin
			FROM
				users
			WHERE
				login != 'Scheduler'
				AND login = '".mysql_real_escape_string($user)."'
				AND password = ENCODE('".mysql_real_escape_string($pass)."', '".mysql_real_escape_string(ENCRYPTION_SEED)."')
				AND revoked = 0
				AND portalaccess = 1";

		$queryresult = $this->db->query($querystatement);

		if(!$queryresult) {

			$error = new appError(-720,"","Error retrieving user record",true,true,true,$format);
			return false;

		}//endif

		if($this->db->numRows($queryresult)){

			//We found a record that matches in the database
			// populate the session and go in
			$_SESSION["userinfo"] = $this->db->fetchArray($queryresult);

			$querystatement = "
				UPDATE
					users
				SET
					modifieddate=modifieddate,
					lastlogin=Now()
				WHERE
					id = ".$_SESSION["userinfo"]["id"];

			$queryresult = @ $this->db->query($querystatement);

			if(!$queryresult)
				$error = new appError(-730,"","Error Updating User Login Time",true,true,true,$format);
			else
				$thereturn = true;

		}//endif numrows

		return $thereturn;

	}//end function verifyAPIlogin


	//Check to see if install folders are present.  If so, do not continue.
	function checkForInstallDirs($errorFormat = "xhtml"){

		//first lets check for the main programs install folder
		if(file_exists("install") && is_dir("install"))
			$error = new appError(-353,"You must remove the install directory and all modules' install directories before phpBMS can run.","Main Install Directory Present",true,true,true,$errorFormat);

		if(file_exists("modules") && is_dir("modules")){

			$thedir = @ opendir("modules");

			while($entry = readdir($thedir)){

				if($entry != "." && $entry != ".." && $entry != "base" && $entry != "sample" && is_dir("modules/".$entry)){

					if(file_exists("modules/".$entry."/install") && is_dir("modules/".$entry."/install")){

						$error = new appError(-354,"You must remove the install directory and all modules' install directories before phpBMS can run.","Module '".$entry."' Install Directory Present",true,true,true,$errorFormat);

					}//endif

				}//endif

			}//end while

		}//end if

	}//end function checkForInstallDirs

}//end phpbmsSession class



// Start Login verification Code
//==============================================================================
if(!isset($sqlEncoding))
	$sqlEncoding = "utf8";

if(!defined("noStartup")){

	$scriptname = basename($_SERVER["PHP_SELF"]);
	$phpbmsSession = new phpbmsSession;

	//Testing for API login
	if(strpos($scriptname,"api_")!==false){

                if(!isset($_POST["phpbmsformat"]))
                    $_POST["phpbmsformat"] = "json";

		if(isset($_POST["phpbmsusername"]) && isset($_POST["phpbmspassword"])){

			$phpbmsSession->loadDBSettings(APP_DEBUG);

			if(!APP_DEBUG)
				$phpbmsSession->checkForInstallDirs("json");

			include_once("include/db.php");
			$db = new db();
			$phpbmsSession->db = $db;

			include_once("common_functions.php");
			$phpbmsSession->loadSettings($sqlEncoding);
			$phpbms = new phpbms($db);


			if(!$phpbmsSession->verifyAPILogin($_POST["phpbmsusername"],$_POST["phpbmspassword"], $_POST["phpbmsformat"]))
				$error = new appError(-700,"","Login credentials incorrect",true,true,true,"json");

		} else
		    $error= new appError(-710, "", "No login credentials passed", true, true, true, $_POST["phpbmsformat"]);

	} else {

		$phpbmsSession->loadDBSettings(APP_DEBUG);

		if(!APP_DEBUG)
			$phpbmsSession->checkForInstallDirs();

		//start database
		include_once("include/db.php");
		$db = new db();

		$phpbmsSession->db = $db;

		//load application settings from table
		$phpbmsSession->loadSettings($sqlEncoding);

		include_once("common_functions.php");
		$phpbms = new phpbms($db);

		if(!isset($noSession))
			$phpbmsSession->startSession();

		if (!isset($_SESSION["userinfo"]) && $scriptname != "index.php") {

			if(isset($loginNoKick)){

				if(!isset($loginNoDisplayError))
					exit();

			} else{

				goURL(APP_PATH."index.php");

			}//endif

		}//endif iseet userinfo

	}//endif

	$db->stopOnError = true;

}//end if

?>
