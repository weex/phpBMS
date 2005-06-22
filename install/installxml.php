<?php

function importData($thetable){
	global $dblink;
	
	$fcontents = @ file ($thetable.".txt"); 
	$thereturn="Starting import of records for '".$thetable."'\n";


  for($i=0; $i<sizeof($fcontents); $i++) { 
      $line = trim($fcontents[$i]); 
      $arr = explode("','", $line);  
	 
	  $arr[0]=substr($arr[0],1);
	  $arr[sizeof($arr)-1]=substr($arr[sizeof($arr)-1],0,strlen($arr[sizeof($arr)-1])-1);
	  for($x=0;$x<sizeof($arr);$x++)
	  	if($arr[$x]=="[NULL]")
			$arr[$x]="Null";
		else
		  	$arr[$x]="'".addslashes(str_replace("\\n","\n",$arr[$x]))."'";
	
      #if your data is comma separated
      # instead of tab separated, 
      # change the '\t' below to ',' 
     
      $sql = "replace into ".$thetable." values (".implode(",",$arr) .")"; 
	  
	  if($i>0) {
		  $queryresult=mysql_query($sql,$dblink);
		  if(!$queryresult){
			 $thereturn.=mysql_error($dblink)."\n";
		}
	  }
	}//end for
	$thereturn.="Done importing records for '".$thetable."'\n\n";
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



		$thereturn="Error Processing: No Command Given";
		if(isset($_GET["command"])){
			$vars=loadSettings();
			if (!is_array($vars)) 
				$thereturn=$vars; 
			else {

				switch($_GET["command"]){
					case "updatesettings":
						$thereturn=writeSettings($_GET["ms"],$_GET["mdb"],$_GET["mu"],$_GET["mup"]);
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
							$thereturn="Tables Successfully created\n===========================\n";
						else
							$thereturn.="\n\n";
							
						$thereturn.=importData("notes");
						$thereturn.=importData("menu");
						$thereturn.=importData("modules");
						$thereturn.=importData("reports");
						$thereturn.=importData("tablecolumns");
						$thereturn.=importData("tabledefs");
						$thereturn.=importData("tablefindoptions");
						$thereturn.=importData("tableoptions");
						$thereturn.=importData("tablesearchablefields");
						$thereturn.=importData("users");
						//$thereturn.=importData("usersearches");
						$thereturn.="==================\nDone Importing Data\n==================";
				
					break;
					
				}//end switch
			}//end if
		}//end if
		
		
		
		header('Content-Type: text/xml');
		?><?php echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'; ?>
<response><?php echo $thereturn?></response>