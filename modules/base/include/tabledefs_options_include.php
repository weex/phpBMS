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
		$thequerystatement="SELECT id, name, `option`, othercommand
		FROM tableoptions 
		WHERE tabledefid=".$tabledefid;
		if($optionid) $thequerystatement.=" AND id=".$optionid;
		$thequerystatement.=" ORDER BY othercommand, id";
		
		$thequery=mysql_query($thequerystatement) or $thequery=mysql_error()." -- ".$thequerystatement;		
		return $thequery;
	}// end function


	function addOption(){
		global $dblink;
		$thequerystatement="INSERT INTO tableoptions (tabledefid, name, `option`, othercommand)
		values (";
		$thequerystatement.=$_GET["id"].", ";
		$thequerystatement.="\"".$_POST["name"]."\", ";
		$thequerystatement.="\"".$_POST["option"]."\", ";
		$thequerystatement.="\"".$_POST["othercommand"]."\") ";
		if(mysql_query($thequerystatement)) $thereturn ="Option Added"; else $thereturn=mysql_error()." -- ".$thequerystatement;
		
		return $thereturn;
	}// end function
	

	function updateOption(){
		global $dblink;
		$thequerystatement="UPDATE tableoptions set ";
		$thequerystatement.="name=\"".$_POST["name"]."\", ";
		$thequerystatement.="`option`=\"".$_POST["option"]."\", ";
		$thequerystatement.="othercommand=".$_POST["othercommand"]." ";
		$thequerystatement.="WHERE id=".$_POST["optionid"];
		if(mysql_query($thequerystatement)) $thereturn ="Option Updated"; else $thereturn=mysql_error()." -- ".$thequerystatement;
		
		return $thereturn;
	}

	function deleteOption($id){
		global $dblink;
		$thequerystatement="DELETE FROM tableoptions WHERE id=".$id;
		if(mysql_query($thequerystatement)) $thereturn ="Option Deleted"; else $thereturn=mysql_error()." -- ".$thequerystatement;
		
		return $thereturn;
	}

?>