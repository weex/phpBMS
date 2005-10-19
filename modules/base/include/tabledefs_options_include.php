<?php
	if($_SESSION["userinfo"]["accesslevel"]<90) header("Location: ".$_SESSION["app_path"]."noaccess.html");
	function setOptionDefaults(){
		$therecord["id"]=NULL;		
		$therecord["name"]="";		
		$therecord["option"]="";		
		$therecord["othercommand"]=0;		

		return $therecord;		
	}
	
	function getOptions($tabledefid,$optionid=false){
		global $dblink;
		$querystatement="SELECT id, name, `option`, othercommand
		FROM tableoptions 
		WHERE tabledefid=".$tabledefid;
		if($optionid) $querystatement.=" AND id=".$optionid;
		$querystatement.=" ORDER BY othercommand, id";
		
		$thequery=mysql_query($querystatement) or $thequery=mysql_error($dblink)." -- ".$querystatement;		
		return $thequery;
	}// end function


	function addOption($variables,$tabledefid){
		global $dblink;
		$querystatement="INSERT INTO tableoptions (tabledefid, name, `option`, othercommand)
		values (";
		$querystatement.=$tabledefid.", ";
		$querystatement.="\"".$variables["name"]."\", ";
		$querystatement.="\"".$variables["option"]."\", ";
		$querystatement.="\"".$variables["othercommand"]."\") ";
		if(mysql_query($querystatement)) $thereturn ="Option Added"; else $thereturn=mysql_error($dblink)." -- ".$querystatement;
		
		return $thereturn;
	}// end function
	

	function updateOption($variables){
		global $dblink;
		$querystatement="UPDATE tableoptions set ";
		$querystatement.="name=\"".$variables["name"]."\", ";
		$querystatement.="`option`=\"".$variables["option"]."\", ";
		$querystatement.="othercommand=".$variables["othercommand"]." ";
		$querystatement.="WHERE id=".$variables["optionid"];
		if(mysql_query($querystatement)) $thereturn ="Option Updated"; else $thereturn=mysql_error($dblink)." -- ".$querystatement;
		
		return $thereturn;
	}

	function deleteOption($id){
		global $dblink;
		$querystatement="DELETE FROM tableoptions WHERE id=".$id;
		if(mysql_query($querystatement)) $thereturn ="Option Deleted"; else $thereturn=mysql_error($dblink)." -- ".$querystatement;
		
		return $thereturn;
	}

?>