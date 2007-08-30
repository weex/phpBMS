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
@ define("APP_DEBUG",false);
if(APP_DEBUG)
	error_reporting(E_ALL);

class appError{
 	var $number=0;
	var $title="";
	var $details="";
	var $stop=true;
	var $logerror=true;
	var $format="xhtml";
	
	function appError($number=0,$details="",$title="",$display=false,$stop=true,$logerror=true,$format="xhtml"){
		$this->title = $title;
		
		
		$this->details = $details;
		
		$this->stop = $stop;
				
		$this->logerror = $logerror;
		$this->format = $format;
		$this->number = $number;
				
		if($this->number<0){
			switch($number){
				case -400:
				case -410:
				case -420:
				case -430:
				case -440:
				case -450:
				case -460:
					$this->title="Database Error";
				break;
			}
		} 
		
		if($display || APP_DEBUG) $this->display($format);
		if($logerror) $this->logError();
		if($this->stop) exit;
	}
	
	function display($format=NULL){

		if($format==NULL)
			$format=$this->format;
	
		switch(strtolower($format)){
			case "json":

				echo "{\n";
				echo "\"error\" : { \"id\" : ".$this->number;
				if($this->title)
					echo ", \"title\" : \"".addslashes($this->title)."\"";
				if($this->details)
					echo ", \"details\" : \"".addslashes($this->details)."\"";
				echo "\n}";

			break;
			
			case "xhtml":

				$this->details = str_replace("\n","<br />", htmlspecialchars($this->details,ENT_COMPAT,"UTF-8"));

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

class phpbmsLog{
	
	var $db=NULL;
	var $type="ERROR";
	var $value="";
	var $userid=2;
	
	function phpbmsLog($value=NULL,$type=NULL,$userid=NULL,$db=NULL,$sendLog=true){

		//in most cases, it is prudent for the log object to have it's own DB object
		// so that it can properly supress errors without goofing things up.
		if($db){
			if(is_object($db)){
				$this->db=$db;
	
				$this->db->showError=false;
				$this->db->logError=false;
				$this->db->stopOnError=false;
			}
		}
		else{
			if(class_exists("db")){
				$this->db= new db(false);
	
				$this->db->showError=false;
				$this->db->logError=false;
				$this->db->stopOnError=false;
	
				$this->db->connect();
				$this->db->selectSchema();
			} else 
				return false;
		}
		
		if($value)
			$this->value=$value;
		if($type)
			$this->error=$type;
		if($userid)
			$this->userid=((int) $userid);
		
		if($sendLog)
			return $this->sendLog();
		else
			return true;
		
	}//end function
	
	function sendLog(){
			
		$ip=$_SERVER["REMOTE_ADDR"];
	
		$querystatement="INSERT INTO `log` (`type`,`value`,`userid`,`ip`) VALUES (";
		$querystatement.="\"".mysql_real_escape_string($this->type)."\", ";
		$querystatement.="\"".mysql_real_escape_string($this->value)."\", ";
		$querystatement.=$this->userid.", ";
		$querystatement.="\"".$ip."\")";
		
		$this->db->query($querystatement);
		
	}
}//end phpbmslog



class phpbmsSession{

	var $db=null;

	function loadDBSettings($reportError = true){
		// This functions looks for the settings.php file, and loads
		// the database variables as constants.  As an added benefit
		// it adds the phpBMS root as an included path.
		
		$path="";
		$count=1;

		//need to look for settings file... only go up a total of 10 directories
		$currdirectory= getcwd();
		
		while(!file_exists("settings.php") and ($count<10)){
			$path.="../";
			@ chdir("../");
			$count++;
		}
		
		$settingsfile =  @ fopen("settings.php","r");
		if($settingsfile){
			//loop through the settings file and load variables into the session 
			while( !feof($settingsfile)) {
				$line=NULL;
				$key=NULL;
				$value=NULL;
				$line=fscanf($settingsfile,"%[^=]=%[^[]]",$key,$value);
				if ($line){
					$key=trim($key);
					$value=trim($value);
					if($key!="" and !strpos($key,"]")){	
						$startpos=strpos($value,"\"");
						$endpos=strrpos($value,"\"");
						if($endpos!=false)
							$value=substr($value,$startpos+1,$endpos-$startpos-1);
						if(strpos($key,"mysql_")===0){
							define(strtoupper($key),$value);							
						}
					}
				}
			}
			
			@ fclose($settingsfile);

			//For legacy installations where pconnect is not set
			if(!defined("MYSQL_PCONNECT"))
				define("MYSQL_CONNECT",true);

			//this adds the phpbms root to the include path
			if ( ! defined( "PATH_SEPARATOR" ) ) {
			  if ( strpos( $_ENV[ "OS" ], "Win" ) !== false )
				define( "PATH_SEPARATOR", ";" );
			  else define( "PATH_SEPARATOR", ":" );
			}

			$pathToAdd=@ getcwd();
			ini_set("include_path",ini_get("include_path").PATH_SEPARATOR.$pathToAdd);
			
			//Now to set the path
			$pathrev = strrev($_SERVER["PHP_SELF"]);
			$choppos=0;

			for($x=0;$x<$count;$x++)
				$choppos = strpos($pathrev,"/",$choppos+1);
			define("APP_PATH",strrev(substr($pathrev,$choppos)));
			
			@ chdir ($currdirectory);
			return $path;
		} else {
			if($reportError)
				$error= new appError(-300,"You may need to run the install process, or set the permission on your settings file correctly.","Settings File Could Not Be Read",true,true,false);
			return false;
		}
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
			while($therecord=$this->db->fetchArray($queryresult))
				if(!defined(strtoupper($therecord["name"])))
					define(strtoupper($therecord["name"]),$therecord["value"]);

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
		}
	}


	function startSession(){
		// This is in a function in case we want to do sessions differently in the future

		session_name("phpBMS".str_replace(" ","",APPLICATION_NAME)."v08ID");
		session_start();
	}


	function verifyAPIlogin($user,$pass){
		$thereturn=false;
		$this->db->stopOnError = false;
		
		$querystatement = "SELECT id, firstname, lastname, email, phone, department, employeenumber, admin
						FROM users 
						WHERE login!=\"Scheduler\" AND login=\"".mysql_real_escape_string($user)."\" 
						AND password=ENCODE(\"".mysql_real_escape_string($pass)."\",\"".mysql_real_escape_string(ENCRYPTION_SEED)."\") 
						AND revoked=0 AND portalaccess=1";
		$queryresult = $this->db->query($querystatement);
		if(!$queryresult) {
			$error = new appError(-720,"","Error retrieving user record",true,true,true,"json");
			return false;
		}
		
		if($this->db->numRows($queryresult)){
			//We found a record that matches in the database
			// populate the session and go in
			$_SESSION["userinfo"]=$this->db->fetchArray($queryresult);
		
			$querystatement="UPDATE users SET modifieddate=modifieddate, lastlogin=Now() WHERE id = ".$_SESSION["userinfo"]["id"];
			$queryresult=@ $this->db->query($querystatement);
			if(!$queryresult) {
				$error = new appError(-730,"","Error Updaingt User Login Time",true,true,true,"json");
			} else
				$thereturn=true;
		}
		return $thereturn;	
	}
	
}//end loginSession class


// Start Code
//=================================================================================================================
//php <4.3.0 compatibility
if(!function_exists("mysql_real_escape_string")){
	function mysql_real_escape_string($string){
		return mysql_escape_string($string);
	}
	
   function utf8_replaceEntity($result){
	   $value = (int)$result[1];
	   $string = '';
	  
	   $len = round(pow($value,1/8));
	  
	   for($i=$len;$i>0;$i--){
		   $part = ($value & (255>>2)) | pow(2,7);
		   if ( $i == 1 ) $part |= 255<<(8-$len);
		  
		   $string = chr($part) . $string;
		  
		   $value >>= 6;
	   }
	  
	   return $string;
   }
  
   function html_entity_decode($string){
	   return preg_replace_callback(
		   '/&#([0-9]+);/u',
		   'utf8_replaceEntity',
		   $string
	   );
   }	
}// end PHP<4.3 compatibility


// Start Login verification Code
//=================================================================================================================
if(!isset($sqlEncoding))
	$sqlEncoding = "utf8";

if(!defined("noStartup")){
	$scriptname = basename($_SERVER["PHP_SELF"]);
	$phpbmsSession = new phpbmsSession;
	
	//Testing for API login
	if(strpos($scriptname,"api_")!==false){
		if(isset($_POST["phpbmsusername"]) && isset($_POST["phpbmspassword"])){
			$phpbmsSession->loadDBSettings();
			
			include_once("include/db.php");
			$db = new db();
			$phpbmsSession->db = $db;

			include_once("common_functions.php");			
			$phpbmsSession->loadSettings($sqlEncoding);
			$phpbms = new phpbms($db);
	
	
			if(!$phpbmsSession->verifyAPILogin($_POST["phpbmsusername"],$_POST["phpbmspassword"],ENCRYPTION_SEED))
				$error = new appError(-700,"","Login credentials incorrect",true,true,true,"json");
		} else
			$error= new appError(-710,"","No login credentials passed",true,true,true,"json");
	} else {
	
		$phpbmsSession->loadDBSettings($sqlEncoding);
	
	
		include_once("include/db.php");
		$db = new db();
		
		$phpbmsSession->db = $db;
		
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
			}
		}
		
	}
	
	$db->stopOnError=true;
}//end if

?>