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


function createTables(){
	global $dblink;
	
	$thereturn="";	
	
	$createstatement="";
	$createfile = fopen("createtables.sql","r");
	if(!$createfile) 
		$thereturn="Could not open table creation file: createtables.sql";
	else{
		while(!feof($createfile)) {
			$createstatement.=fgets($createfile,1024);
			if(strpos($createstatement,";")){
				$theresult=mysql_query(trim($createstatement),$dblink); 
				if(!$theresult)
					$thereturn.=mysql_error($dblink)."\n";
				$createstatement="";
			}//end if;
		}//end while
		
	}//end if	
	return $thereturn;
}//end function


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



$thereturn ="++++++++++++++++++++++++++\n";
$thereturn.="   INSTALLING BUSINESS\n";
$thereturn.=" MANAGEMENT SYSTEM MODULE\n";
$thereturn.="++++++++++++++++++++++++++\n\n";
$vars=loadSettings();
if(!is_array($vars))
	$thereturn.=$vars;
else{
	$dblink = mysql_pconnect($vars["mysql_server"],$vars["mysql_user"],$vars["mysql_userpass"]);
	mysql_select_db($vars["mysql_database"],$dblink);
	
	$tempreturn=createTables();
	if(!$tempreturn)
		$thereturn="Done Creating Tables\n===========================\n";
	else
		$thereturn.=$tempreturn;
	
	$thereturn.="\n";
	$thereturn.=importData("choices");
	$thereturn.=importData("menu");
	$thereturn.=importData("modules");
	$thereturn.=importData("relationships");
	$thereturn.=importData("reports");
	$thereturn.=importData("tablecolumns");
	$thereturn.=importData("tabledefs");
	$thereturn.=importData("tablefindoptions");
	$thereturn.=importData("tableoptions");
	$thereturn.=importData("tablesearchablefields");
	$thereturn.=importData("usersearches");

	$thereturn.="Done Importing Data\n===========================\n";
	
}//end if

		
		
		header('Content-Type: text/xml');
		?><?php echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'; ?>
<response><?php echo $thereturn?></response>