<?php
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


	function verifyAdminLogin($user,$pass,$encryptionSeed,$dblink){
		$querystatement="SELECT id FROM users WHERE login=\"".$user."\" AND password=encode(\"".$pass."\",\"".$encryptionSeed."\") AND accesslevel>=90";
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) die("what the!");//return false;
		if (mysql_num_rows($queryresult)>0) return true;
		return false;
	}


	function doUpdate() {
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
		$newVersion=fgets($file);
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
					
					$thereturn.="Update to 0.51 Successful\n\n";
			
					$ver["version"]="0.51";
				break;
				// ================================================================================================
				case "0.51":
					$thereturn.="Updating BMS Module to 0.52\n";
		
					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.52\" WHERE name=\"bms\";";
					$queryresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - modified bms record in modules table\n";
					
					//Adding columns to invoice
					$querystatement="ALTER TABLE invoices ADD ponumber varchar(64) default '';";
					$queryresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - added client po field to invoices";

					//Adding columns to invoice
					$querystatement="ALTER TABLE invoices ADD requireddate date default NULL;";
					$queryresult=mysql_query($querystatement,$dblink);
					$thereturn.=" - added required date field to invoices";

					$thereturn.="Update to 0.52 Successful\n\n";
			
					$ver["version"]="0.52";
				break;
			}//end switch
		}//end while
		return $thereturn;

	}//end update		

		
		$thereturn=doUpdate();	
		header('Content-Type: text/xml');
		?><?php echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'; ?>
<response><?php echo $thereturn?></response>