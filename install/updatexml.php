<?php
	function loadSettings() {	
		$settingsfile =  fopen("../settings.php","r");
		if($settingsfile){
			//loop through the settings file and load variables into the session 
			while( !feof($settingsfile)) {
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
				$line=NULL;
				$key=NULL;
				$value=NULL;
			}
			fclose($settingsfile);
			return $variables;
		} else return "Cannot open setting.php file";
	}

	function verifyAdminLogin($user,$pass){
		global $dblink;
		global $vars;
		
		$querystatement="SELECT id FROM users WHERE login=\"".$user."\" AND password=encode(\"".$pass."\",\"".$vars["encryption_seed"]."\") AND accesslevel>90";
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) return false;
		if (mysql_num_rows($queryresult)>0) return true;
		return false;
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
					
					$thereturn.="Update to 0.51 Successful\n\n";
					$currentVersion="0.51";
				break;
				// ================================================================================================
				case "0.51":
					$thereturn.="Updating Base Module to 0.52\n";
					
					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.52\" WHERE name=\"base\";";
					$queryresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - modified base record in modules table\n";
					
					//Dropping selected field in choices table
					$querystatement="ALTER TABLE choices DROP selected";
					$queryresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - dropped selected field from choices table\n";

					$thereturn.="Update to 0.52 Successful\n\n";
					$currentVersion="0.52";
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
				$thereturn="DB Connection Information not loaded";
			else {
				$dblink = mysql_pconnect($vars["mysql_server"],$vars["mysql_user"],$vars["mysql_userpass"]);		
				mysql_select_db($vars["mysql_database"],$dblink);

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