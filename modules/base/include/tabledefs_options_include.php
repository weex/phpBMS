<?php
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
		
		$thequery=mysql_query($querystatement) or $thequery=mysql_error()." -- ".$querystatement;		
		return $thequery;
	}// end function


	function addOption(){
		global $dblink;
		$querystatement="INSERT INTO tableoptions (tabledefid, name, `option`, othercommand)
		values (";
		$querystatement.=$_GET["id"].", ";
		$querystatement.="\"".$_POST["name"]."\", ";
		$querystatement.="\"".$_POST["option"]."\", ";
		$querystatement.="\"".$_POST["othercommand"]."\") ";
		if(mysql_query($querystatement)) $thereturn ="Option Added"; else $thereturn=mysql_error()." -- ".$querystatement;
		
		return $thereturn;
	}// end function
	

	function updateOption(){
		global $dblink;
		$querystatement="UPDATE tableoptions set ";
		$querystatement.="name=\"".$_POST["name"]."\", ";
		$querystatement.="`option`=\"".$_POST["option"]."\", ";
		$querystatement.="othercommand=".$_POST["othercommand"]." ";
		$querystatement.="WHERE id=".$_POST["optionid"];
		if(mysql_query($querystatement)) $thereturn ="Option Updated"; else $thereturn=mysql_error()." -- ".$querystatement;
		
		return $thereturn;
	}

	function deleteOption($id){
		global $dblink;
		$querystatement="DELETE FROM tableoptions WHERE id=".$id;
		if(mysql_query($querystatement)) $thereturn ="Option Deleted"; else $thereturn=mysql_error()." -- ".$querystatement;
		
		return $thereturn;
	}

?>