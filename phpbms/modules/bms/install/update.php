<?php
/*
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
	$settingsfile = @ fopen("../../../settings.php","r");
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
	} else return "Cannot open setting.ini file";
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
	
	
	function verifyAdminLogin($user,$pass,$encryptionSeed,$dblink){
		$querystatement="SELECT id FROM users WHERE login=\"".$user."\" AND password=encode(\"".$pass."\",\"".$encryptionSeed."\") AND accesslevel>=90";
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) die("what the!");//return false;
		if (mysql_num_rows($queryresult)>0) return true;
		return false;
	}


	function doUpdate() {
		global $dblink;
		
		$thereturn.="Updating BMS\n===================\n";
		$vars=loadSettings();
		if(!is_array($vars)) {
			$thereturn="Error Loading settings.php file.\n\n";
			return $thereturn;
		}
		$dblink = mysql_pconnect($vars["mysql_server"],$vars["mysql_user"],$vars["mysql_userpass"]);
		mysql_select_db($vars["mysql_database"],$dblink);
		if(!verifyAdminLogin($_GET["u"],$_GET["p"],$vars["encryption_seed"],$dblink)){
			$thereturn="Update Requires Administrative Access.\n\n";
			return $thereturn;
		}
			
		$file =  @ fopen("./version.txt","r");
		$newVersion=fgets($file,1024);
		@ fclose($file);
		
		$querystatement="SELECT version FROM modules WHERE name=\"bms\"";
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) {
			$thereturn="Error Accessing module table in database.\n\n";
			return $thereturn;
		}
		$ver=mysql_fetch_array($queryresult);

		while($ver["version"]!=$newVersion){
			switch($ver["version"]){
				// ================================================================================================
				case "0.5":
					$thereturn.="Updating BMS Module to 0.51\n";
		
					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.51\" WHERE name=\"bms\";";
					$queryresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - modified bms record in modules table\n";
					
					$thereturn.="Update to 0.51 Finished\n\n";
			
					$ver["version"]="0.51";
				break;
				// ================================================================================================
				case "0.51":
					$thereturn.="Updating BMS Module to 0.6\n";
							
					$thereturn.=processSQLfile("updatev0.6.sql");
					
					$thereturn.=importData("choices");
					$thereturn.=importData("menu");
					$thereturn.=importData("reports");
					$thereturn.=importData("tablecolumns");
					$thereturn.=importData("tabledefs");
					$thereturn.=importData("tablefindoptions");
					$thereturn.=importData("tableoptions");
					$thereturn.=importData("tablesearchablefields");

					$querystatement="SELECT clients.id,DATE_FORMAT(clients.creationdate,\"%Y-%m-%d\") as creationdate,max(invoices.orderdate) as orderdate
									FROM `clients` LEFT JOIN invoices on clients.id=invoices.clientid 
									WHERE clients.type=\"client\" GROUP BY clients.id;";
					$queryresult=mysql_query($querystatement,$dblink);
					if(!$queryresult) return (mysql_error($dblink)." --".$querystatement);
					while($therecord=mysql_fetch_array($queryresult,$dblink)){
						$querystatement="UPDATE clients set becameclient=\"";
						if($therecord["orderdate"])
							$querystatement.=$therecord["orderdate"];
						else
							$querystatement.=$therecord["creationdate"];
						$querystatement.="\" WHERE id=".$therecord["id"];
						$updateresult=mysql_query($querystatement,$dblink);
					}
					$thereturn.=" - set intitial client becamclient field\n";
					

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.6\" WHERE name=\"bms\";";
					$updateresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - modified bms record in modules table\n";

					$thereturn.="Update to 0.6 Finished\n\n";
			
					$ver["version"]="0.6";
				break;
				// ================================================================================================
				case "0.6";
					$thereturn.="Updating BMS Module to 0.601\n";

					$querystatement="SELECT invoices.id,tax.percentage FROM invoices INNER JOIN tax on invoices.taxareaid=tax.id";
					$queryresult=mysql_query($querystatement,$dblink);
					if(!$queryresult) return (mysql_error($dblink)." --".$querystatement);
					while($therecord=mysql_fetch_array($queryresult,$dblink)){
						$querystatement="UPDATE invoices SET taxpercentage=".$therecord["percentage"]."WHERE id=".$therecord["id"];
						$updateresult=mysql_query($querystatement,$dblink);
					}
					$thereturn.=" - set taxpercentage on invoices\n";

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.601\" WHERE name=\"bms\";";
					$updateresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - modified bms record in modules table\n";

					$thereturn.="Update to 0.601 Finished\n\n";
			
					$ver["version"]="0.601";				
				break;
				// ================================================================================================
				case "0.601";
					$thereturn.="Updating BMS Module to 0.602\n";

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.602\" WHERE name=\"bms\";";
					$updateresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - modified bms record in modules table\n";

					$thereturn.="Update to 0.602 Finished\n\n";
			
					$ver["version"]="0.602";
				// ================================================================================================
				case "0.602";
					$thereturn.="Updating BMS Module to 0.61\n";

					$thereturn.=processSQLfile("updatev0.61.sql");

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.61\" WHERE name=\"bms\";";
					$updateresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - modified bms record in modules table\n";

					$thereturn.="Update to 0.61 Finished\n\n";
			
					$ver["version"]="0.61";
				break;
				// ================================================================================================
				case "0.61";
					$thereturn.="Updating BMS Module to 0.62\n";

					//$thereturn.=processSQLfile("updatev0.62.sql");

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.62\" WHERE name=\"bms\";";
					$updateresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - modified bms record in modules table\n";

					$thereturn.="Update to 0.62 Finished\n\n";
			
					$ver["version"]="0.62";
				break;
				// ================================================================================================
				case "0.61";
					$thereturn.="Updating BMS Module to 0.7\n";

					//$thereturn.=processSQLfile("updatev0.7.sql");

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.7\" WHERE name=\"bms\";";
					$updateresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - modified bms record in modules table\n";

					$thereturn.="Update to 0.7 Finished\n\n";
			
					$ver["version"]="0.7";
				break;
			}//end switch
		}//end while
		return $thereturn;

	}//end update		

		
		$thereturn=doUpdate();	
		header('Content-Type: text/xml');
		?><?php echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'; ?>
<response><?php echo $thereturn?></response>