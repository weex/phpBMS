<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
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
	function loadSettings() {	
		$settingsfile =  fopen("../settings.php","r");
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
						$variables[$key]=$value;
					}
				}
			}
			if(!isset($variables["mysql_pconnect"]))
			$variables["mysql_pconnect"]="true";
			fclose($settingsfile);
			return $variables;
		} else return "Cannot open setting.php file";
	}

	function loadDBSettings($dblink,$vars){
		$querystatement="SELECT name,value FROM settings";
		$queryresult=mysql_query($querystatement,$dblink);
		while($therecord=mysql_fetch_array($queryresult))
			$vars[$therecord["name"]]=$therecord["value"];
		return $vars;
	}

	function importData($thetable){
		global $dblink;
		
		$tablefile = fopen($thetable.".sql","r");
		if(!$tablefile) {
			return "Could not open the file ".$thetable.".sql";
		}
		$thereturn="Importing records for '".$thetable."'\n";
			$counter=0;
			while(!feof($tablefile)) {
				$sqlstatement=trim(fgets($tablefile,1024));
				if(strrpos($sqlstatement,";")==strlen($sqlstatement)-1){
					$theresult=mysql_query($sqlstatement,$dblink);
					if(!$theresult)
						$thereturn.=mysql_error($dblink)."\n";
					else
						$counter++;
					$sqlstatement="";
				}//end if;
			}//end while
	
		$thereturn.="Import of ".$counter." record(s) for '".$thetable."' complete. \n\n";
		return $thereturn;
	}//end function
	
	function processSQLfile($filename){
		global $dblink;
				
		$thefile = fopen($filename,"r");
		if(!$thefile) {
			return "Could not open the file ".$filename.".";
		}
		$thereturn="Processing SQL from file '".$filename."'\n";
			while(!feof($thefile)) {
				$sqlstatement=trim(fgets($thefile,1024));
				if(strrpos($sqlstatement,";")==strlen($sqlstatement)-1){
					$theresult=mysql_query($sqlstatement,$dblink);
					if(!$theresult)
						$thereturn.=mysql_error($dblink)."\n";
					$sqlstatement="";
				}//end if;
			}//end while
	
		$thereturn.="Done processing SQL from file '".$filename."'. \n\n";
		return $thereturn;
	}//end function
		

	function write_settings($settings) {
		$settingsfile = fopen("../settings.php","r") or die ("Couldn't open Settings File");
		//create an array of all lines
		while( !feof($settingsfile)) {
			$newfile[]=fgets($settingsfile,1024);
		}
		fclose($settingsfile);
		
		$newfile[]="\n";
		foreach($settings as $settingname=>$settingvalue) {
			$infile=false;
			//next loop through the file, and if the setting is there, replace it
			for($i=0;$i<count($newfile);$i++){
				if (strpos(($newfile[$i]),$settingname)===0) {
					$tabnumber=intval(5-strlen($settingvalue)/8);
					$newfile[$i]=$settingname.str_repeat(chr(9),$tabnumber)."= \"".str_replace(chr(10),"\\n",$settingvalue)."\"\n";
					$infile=true;
					break;
				}
			}//
			if(!$infile) {
				$tabnumber=intval(5-strlen($settingname)/8);
				$newfile[]=$settingname.str_repeat(chr(9),$tabnumber)."= \"".str_replace(chr(10),"\\n",$settingvalue)."\"\n";
			}
		}
		if(end($newfile)=="\n") array_pop($newfile);
		//now write the new file
		$settingsfile = fopen("../settings.php","w") or die ("Couldn't open Settings File");
		for($i=0;$i<count($newfile);$i++){
			 fwrite($settingsfile,$newfile[$i],1024);
		}
		fclose($settingsfile);	
	}//end function	
	
	function verifyAdminLogin($user,$pass){
		global $dblink;
		global $vars;
		
		$querystatement="SELECT id FROM users WHERE login=\"".$user."\" AND password=encode(\"".$pass."\",\"".$vars["encryption_seed"]."\") AND admin=1";
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult){
			return false;
		}
		return (mysql_num_rows($queryresult)>0);
	}

	function getCurrentBaseVersion(){
		global $dblink;

		$querystatement="SELECT version FROM modules WHERE name=\"base\";";
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) return false;
		$ver=mysql_fetch_array($queryresult);
		return $ver["version"];			
	}
	
	function runUpdate($currentVersion,$newVersion){
		global $dblink;
		global $vars;
		
		$thereturn="";
		while($currentVersion!=$newVersion){
			switch($currentVersion){
				// ================================================================================================
				case "0.5":
					$thereturn.="Updating Base Module to 0.51\n";
					
					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.51\" WHERE name=\"base\";";
					$queryresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - modified base record in modules table\n";
					
					$thereturn.="Update to 0.51 Finished\n\n";
					$currentVersion="0.51";
				break;
				// ================================================================================================
				case "0.51":
					$thereturn.="Updating Base Module to 0.6\n";
										
					//Processing Data Structure Changes
					$thereturn.=processSQLfile("updatev0.6.sql");
					
					//Inputind new records for new structure
					$thereturn.=importData("choices");
					$thereturn.=importData("menu");
					$thereturn.=importData("reports");
					$thereturn.=importData("tablecolumns");
					$thereturn.=importData("tabledefs");
					$thereturn.=importData("tablefindoptions");
					$thereturn.=importData("tableoptions");
					$thereturn.=importData("tablesearchablefields");
					
					//Setting the new default load page
					$newSettings["default_load_page"]="modules/base/snapshot.php";
					write_settings($newSettings);
					$thereturn.=" - modified default start page in settings\n";					
										
					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.6\" WHERE name=\"base\";";
					$queryresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - modified base record in modules table\n";

					$thereturn.="Update to 0.6 Finished\n\n";
					
					$currentVersion="0.6";
				break;
				// ================================================================================================
				case "0.6":
					$thereturn.="Updating Base Module to 0.601\n";
					
					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.601\" WHERE name=\"base\";";
					$queryresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - modified base record in modules table\n";
					
					$thereturn.="Update to 0.601 Finished\n\n";
					$currentVersion="0.601";
				break;
				// ================================================================================================
				case "0.601":
					$thereturn.="Updating Base Module to 0.602\n";
					
					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.602\" WHERE name=\"base\";";
					$queryresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - modified base record in modules table\n";
					
					$thereturn.="Update to 0.602 Finished\n\n";
					$currentVersion="0.602";
				break;
				// ================================================================================================
				case "0.602":
					$thereturn.="Updating Base Module to 0.61\n";
					
					//Processing Data Structure Changes
					$thereturn.=processSQLfile("updatev0.61.sql");

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.61\" WHERE name=\"base\";";
					$queryresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - modified base record in modules table\n";
					
					foreach($vars as $key=>$value){
						if (strpos($key,"mysql_")!==0){
							$querystatement="INSERT INTO settings (name,value) VALUES (\"".$key."\",\"".$value."\")";
							$queryresult=mysql_query($querystatement,$dblink);
						}
					}
					$thereturn.="Moved non-mysql settings to new settings table.\n";
					
					$filename="../report/logo.png";
					if (function_exists('file_get_contents')) {
						@ $file = addslashes(file_get_contents($filename));
					} else {
						// If using PHP < 4.3.0 use the following:
						@ $file = addslashes(fread(fopen($filename, 'r'), filesize($filename)));
					}
					$querystatement="INSERT INTO files (id,name,description,type,accesslevel,file,createdby,creationdate,modifiedby) 
									VALUES (1,\"logo.png\",\"Company Logo Used in PDF reports\",\"image/png\",90,\"".$file."\",2,Now(),2)";
					$queryresult=mysql_query($querystatement,$dblink);
					if(!$queryresult)
						$thereturn.="Error moving logo to database.\n ";
					else
						$thereturn.="Moved logo to database.\n";

					
					$thereturn.="Update to 0.61 Finished\n\n";
					$currentVersion="0.61";
				break;
				// ================================================================================================
				case "0.61":
					$thereturn.="Updating Base Module to 0.62\n";
					
					//Processing Data Structure Changes
					$thereturn.=processSQLfile("updatev0.62.sql");

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.62\" WHERE name=\"base\";";
					$queryresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - modified base record in modules table\n";				

					$thereturn.="Update to 0.62 Finished\n\n";
					$currentVersion="0.62";
				break;
				// ================================================================================================
				case "0.62":
					$thereturn.="Updating Base Module to 0.7\n";
					
					//Processing Data Structure Changes
					$thereturn.=processSQLfile("updatev0.7.sql");
					
					$querystatement="SELECT id,accesslevel FROM users WHERE accesslevel!=0";
					$queryresult=mysql_query($querystatement,$dblink);
					while($therecord=mysql_fetch_array($queryresult)){
						while($therecord["accesslevel"]>1){
							if($therecord["accesslevel"]==40)
								$therecord["accesslevel"]=$therecord["accesslevel"]-10;
							$querystatement="INSERT INTO rolestousers (userid,roleid) VALUES (".$therecord["id"].",".$therecord["accesslevel"].")";
							$insertresult=mysql_query($querystatement,$dblink);
							$therecord["accesslevel"]=$therecord["accesslevel"]-10;							
						}
					}					
					$thereturn.=" - Connverted user access levels to roles.\n";				

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.7\" WHERE name=\"base\";";
					$queryresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - modified base record in modules table\n";				

					$thereturn.="Update to 0.7 Finished\n\n";
					$currentVersion="0.7";
				break;
				
			}//end switch
		}//end while
		return $thereturn;
	}//end function
	
	//==============================================================================================================================
		$thereturn="Error Processing: No Command Given";
		if(isset($_GET["command"])){
			$vars=loadSettings();
			if (!is_array($vars)) 
				$thereturn="Could Not load settings.php file.";
			else {
				if($vars["mysql_pconnect"]=="true")
					$dblink = @  mysql_pconnect($vars["mysql_server"],$vars["mysql_user"],$vars["mysql_userpass"]);
				else
					$dblink = @  mysql_connect($vars["mysql_server"],$vars["mysql_user"],$vars["mysql_userpass"]);
				mysql_select_db($vars["mysql_database"],$dblink);
				
				$vars=loadDBSettings($dblink,$vars);
								
				switch($_GET["command"]){
					case "verifyLogin":
						if (!verifyAdminLogin($_GET["u"],$_GET["p"]))
							$thereturn="DB Connection error or invlaid adminstrative error.\n";
						else
							$thereturn="DB Connected\nAdministrative user successfully verified\n";
					break;

					case "checkBaseUpdate":
						if (!verifyAdminLogin($_GET["u"],$_GET["p"]))
							$thereturn="DB Connection error or invlaid adminstrative error.\n";
						else {
							$currenVersion=getCurrentBaseVersion();
							if($currenVersion>=$_GET["v"])
								$thereturn="Update not needed\nCurrent Version: ".$currenVersion;
							else
								$thereturn="Update Possible\nCurrent Version: ".$currenVersion;							
						}
					break;
					case "updateBaseVersion":
						if (!verifyAdminLogin($_GET["u"],$_GET["p"]))
							$thereturn="DB Connection error or invlaid adminstrative error.\n";
						else {
							$currenVersion=getCurrentBaseVersion();
							if($currenVersion>=$_GET["v"])
								$thereturn="Update not needed\nCurrent Version: ".$currenVersion;
							else
								$thereturn=runUpdate($currenVersion,$_GET["v"]);
						}
					break;
				}//end switch
			}//end if
		}//end if
				
		
		header('Content-Type: text/xml');
		?><?php echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'; ?>
<response><?php echo $thereturn?></response>