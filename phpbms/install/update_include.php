<?php

	


//=======================================================================================
//=======================================================================================
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

	
	function getNewVersion($dir="."){
		$file =  @ fopen($dir."/version.txt","r");
		$version=fgets($file,1024);
		@ fclose($file);
		return $version;
	}
	
	function showModules(){
		$vars=loadSettings();
		if(!is_array($vars)) {
			echo "<option>Could Not Open Settings File</option>";
			return false;
		}
		if($vars["mysql_pconnect"]=="true")
			$dblink = @  mysql_pconnect($vars["mysql_server"],$vars["mysql_user"],$vars["mysql_userpass"]);
		else
			$dblink = @  mysql_connect($vars["mysql_server"],$vars["mysql_user"],$vars["mysql_userpass"]);
		@ mysql_select_db($vars["mysql_database"],$dblink);
		
		$querystatement="SELECT name,version FROM modules WHERE name!=\"base\" ";
		$queryresult=$db->query($querystatement);
		
		while($modulerecord=$db->fetchArray($queryresult)){
			$newVersion=getNewVersion("../modules/".$modulerecord["name"]."/install");
			if($newVersion!=$modulerecord["version"])
				echo "<OPTION value=\"".$modulerecord["name"]."\">".$modulerecord["name"]." (".$modulerecord["version"]." -&gt; ".$newVersion.")</OPTION>\n";
		}
		
	}

	$version=getNewVersion();

?>