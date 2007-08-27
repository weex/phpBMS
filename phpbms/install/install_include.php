<?php
function processSQLfile($db,$filename){
	global $dblink;
			
	$thefile = @ fopen($filename,"r");
	if(!$thefile) 
		return "Could not open the file ".$filename.".\n";

	$thereturn="Processing SQL from file '".$filename."'\n";
		while(!feof($thefile)) {
			$sqlstatement=trim(fgets($thefile,1024));
			if(strrpos($sqlstatement,";")==strlen($sqlstatement)-1){
				$theresult=$db->query($sqlstatement);
				if($db->error)
					$thereturn = "Error processing SQL file  ".$filename.": ".$db->error."\n".$sqlstatement;
				$sqlstatement="";
			}//end if;
		}//end while

	$thereturn.="Done processing SQL from file '".$filename."'. \n\n";
	return $thereturn;
}//end function


function verifyAdminLogin($db,$user,$pass){

	if((real) getCurrentVersion($db,"base")>=.7)
		$querystatement="SELECT id FROM users WHERE login=\"".mysql_real_escape_string($user)."\" AND password=encode(\"".mysql_real_escape_string($pass)."\",\"".ENCRYPTION_SEED."\") AND admin=1";
	else
		$querystatement="SELECT id FROM users WHERE login=\"".mysql_real_escape_string($user)."\" AND password=encode(\"".mysql_real_escape_string($pass)."\",\"".ENCRYPTION_SEED."\") AND accesslevel>=90";
	
	$queryresult=$db->query($querystatement);
			
	if(!$queryresult)
		return false;
	return ($db->numRows($queryresult)>0);
}


function getCurrentVersion($db,$module){

	$querystatement="SELECT version FROM modules WHERE name=\"".mysql_real_escape_string($module)."\";";
	$queryresult=$db->query($querystatement);

	$ver=$db->fetchArray($queryresult);
	return $ver["version"];			
}


function loadModules($type){
	$currdirectory = @getcwd();

	$thedir= @ opendir("../modules/");
	
	echo 'modules = Array();'."\n";
	
	$modules = array();
	while($entry=readdir($thedir)){
		if($entry != "." && $entry != ".." && $entry != "base" && $entry != "sample" && is_dir("../modules/".$entry)){
			if(file_exists("../modules/".$entry."/install/".$type.".php") && file_exists("../modules/".$entry."/install/version.php")){
				include("../modules/".$entry."/install/version.php");
			}					
		}
	}		
	
	foreach($modules as $name=>$module)
		if(is_array($module)){
			echo 'modules["'.$name.'"] = Array()'."\n";
			foreach($module as $key=>$value)
				echo 'modules["'.$name.'"]["'.$key.'"] = "'.$value.'";'."\n";
		}
	
	@ chdir ($currdirectory);
	
	return $modules;
}//end function

	
function showModules($modules){
	if(is_array($modules)){
		foreach($modules as $name => $module)
			if($name != "base")
				if(is_array($module)){
					?><option value="<?php echo $name ?>"><?php echo $module["name"]?></option><?php
				}
	}
}


function createTables($db,$sqlfile){

	$sqlstatement="";
	$thereturn = "";
	
	$createfile = @ fopen("createtables.sql","r");
	if(!$createfile) 
		return "Could not open SQL file: ".sqlfile;
	else{
		while(!feof($createfile)) {
			$sqlstatement.= @ fgets($createfile,1024);
			if(strpos($sqlstatement,";")){
			
				$theresult = $db->query(trim($sqlstatement)); 
				
				if($db->error){
					return "Error creating tables: ".$db->error."\n\n".trim($sqlstatement);
				}
				$sqlstatement="";
			}//end if;
		}//end while
		
	}//end if	

	return true;
}


function importData($db,$tablename){
	
	$tablefile = @ fopen($tablename.".sql","rb");
	if(!$tablefile) 
		return "Could not open the file ".$tablename.".sql\n";

	$thereturn="";
	$counter=0;
	$failure = false;
	
	while(!feof($tablefile)) {
		$sqlstatement=trim(fgets($tablefile,8184));
		if(strrpos($sqlstatement,";")==strlen($sqlstatement)-1){

			$theresult=$db->query($sqlstatement);

			if($db->error){
				$failure = true;
				$thereturn .= "Error importing record into ".$tablename.": ".$db->error."\n";
			}
			else
				$counter++;

			$sqlstatement="";
		}//end if;
	}//end while
	
	if($failure)
		$threturn = "Importing of some records in ".$tablename." occured.\n\n";
	else
		$thereturn.="Import of ".$counter." record(s) for '".$tablename."' complete.\n";
	
	return $thereturn;
}//end function

?>