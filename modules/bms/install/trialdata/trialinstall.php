<?php
function loadSettings() {
	$settingsfile = @ fopen("../../../../settings.php","r");
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



$thereturn ="++++++++++++++++++++++++\n";
$thereturn.=" INSTALLING TRIAL DATA\n";
$thereturn.="++++++++++++++++++++++++\n\n";
$vars=loadSettings();
if(!is_array($vars))
	$thereturn.=$vars;
else{
	$dblink = mysql_pconnect($vars["mysql_server"],$vars["mysql_user"],$vars["mysql_userpass"]);
	mysql_select_db($vars["mysql_database"],$dblink);
	
	
	$thereturn.=importData("clients");
	$thereturn.=importData("invoices");
	$thereturn.=importData("lineitems");
	$thereturn.=importData("productcategories");
	$thereturn.=importData("products");
	$thereturn.=importData("tax");

	$thereturn.="==================\nDone Importing Data\n==================";
	
}//end if

		
		
		header('Content-Type: text/xml');
		?><?php echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'; ?>
<response><?php echo $thereturn?></response>