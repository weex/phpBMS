<?php
error_reporting(E_ALL);

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


function loadSettings() {
	$settingsfile = @ fopen("../settings.php","r");
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

function writeSettings($server,$database,$user,$userpass){
	$settings["mysql_server"]=$server;
	$settings["mysql_database"]=$database;	
	$settings["mysql_user"]=$user;	
	$settings["mysql_userpass"]=$userpass;	

	$settingsfile = @ fopen("../settings.php","r");
	if(!$settingsfile) return "Could not open settings.php file for reading"; 

	//create an array of all lines
	while( !feof($settingsfile)) {
		$newfile[]=fgets($settingsfile,1024);
	}
	fclose($settingsfile);
	
	$newfile[]="\n";
	foreach($settings as $settingname=>$settingvalue) {
		$infile=false;
		//next loop through the file, and if the setting is their, replace it
		for($i=0;$i<count($newfile);$i++){
			if (strpos(("D".$newfile[$i]),$settingname)==1) {
				$tabnumber=intval(5-strlen($settingname)/8);
				$newfile[$i]=$settingname.str_repeat(chr(9),$tabnumber)."= \"".str_replace(chr(10),"\\n",$settingvalue)."\"\n";
				$infile=true;
				break;
			}
		}
	}
	if(end($newfile)=="\n") array_pop($newfile);
	//now write the new file
	$settingsfile=NULL;
	$settingsfile = @ fopen("../settings.php","w");
	if(!$settingsfile) return "Could not open settings file for writing.";
	for($i=0;$i<count($newfile);$i++){
		 fwrite($settingsfile,$newfile[$i],1024);
	}
	//fclose($settingsfile);
	
	return "settings.php file updated successfully";
}//end function

function createDefaultFiles(){
	$thereturn="Copying Default Files Successful\n";
	if(!copy("../defaultsettings.php","../settings.php"))
		$thereturn="Error Copying Default Settings File.";
	if(!copy("../report/defaultlogo.png","../report/logo.png"))
		$thereturn="Error Copying Default Logo Picture File.";
	return $thereturn;
}

		$thereturn="Error Processing: No Command Given";
		if(isset($_GET["command"])){
			$vars=loadSettings();

			switch($_GET["command"]){
				case "updatesettings":
					$thereturn=createDefaultFiles();
					$thereturn.=writeSettings($_GET["ms"],$_GET["mdb"],$_GET["mu"],$_GET["mup"]);
				break;


				case "testconnection":
					$dblink = @  mysql_pconnect($vars["mysql_server"],$vars["mysql_user"],$vars["mysql_userpass"]);		
					if(!$dblink) 
						$thereturn="Could Not Establish Connection To MySQL Server: Check server, username and password"; 
					else
						$thereturn="Connection to MySQL Established";
				break;
				
				
				case "createdatabase":
					$thereturn="";
					$dblink = mysql_pconnect($vars["mysql_server"],$vars["mysql_user"],$vars["mysql_userpass"]);		
					if (!mysql_select_db($vars["mysql_database"])){
						$queryresult=mysql_query("create database `".$vars["mysql_database"]."`",$dblink);							
						if(!$queryresult)
							$thereturn=mysql_error($dblink)."\n";
					}
					
					if (!mysql_select_db($vars["mysql_database"])) 
						$thereturn.="Could not connect to database: '".$vars["mysql_database"]."'.";
					else
						$thereturn.="Connection to database '".$vars["mysql_database"]."' established";
				break;
				
	
				case "populatedata":
					$dblink = mysql_pconnect($vars["mysql_server"],$vars["mysql_user"],$vars["mysql_userpass"]);		
					mysql_select_db($vars["mysql_database"]);
					
					$thereturn=createTables();
					if(!$thereturn)
						$thereturn="Done Creating Tables \n===========================\n";
					else
						$thereturn.="\n\n";
						
					$thereturn.=importData("choices");
					$thereturn.=importData("menu");
					$thereturn.=importData("modules");
					$thereturn.=importData("notes");
					$thereturn.=importData("reports");
					$thereturn.=importData("tablecolumns");
					$thereturn.=importData("tabledefs");
					$thereturn.=importData("tablefindoptions");
					$thereturn.=importData("tableoptions");
					$thereturn.=importData("tablesearchablefields");
					$thereturn.=importData("users");
					$thereturn.="\nDone Importing Data\n===========================\n";
			
				break;
				
			}//end switch
		}//end if
		
		
		
		header('Content-Type: text/xml');
		?><?php echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'; ?>
<response><?php echo $thereturn?></response>