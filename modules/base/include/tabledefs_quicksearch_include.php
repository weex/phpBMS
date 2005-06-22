<?php
	function setDefaultQuickSearch(){
		$therecord["id"]=NULL;		
		$therecord["displayorder"]=NULL;		

		$therecord["name"]="";		
		$therecord["search"]="";		

		return $therecord;
	}
	
	function getQuicksearchs($tabledefid,$quicksearchid=false){
		global $dblink;
		$thequerystatement="SELECT id, name, `search`, displayorder
		FROM tablefindoptions 
		WHERE tabledefid=".$tabledefid;
		if($quicksearchid) $thequerystatement.=" AND id=".$quicksearchid;
		$thequerystatement.=" ORDER BY displayorder";
		
		$thequery=mysql_query($thequerystatement) or $thequery=mysql_error()." -- ".$thequerystatement;		
		return $thequery;
	}// end function


	function addQuicksearch(){
		global $dblink;
		$thequerystatement="INSERT INTO tablefindoptions (tabledefid, name, `search`, displayorder)
		values (";
		$thequerystatement.=$_GET["id"].", ";
		$thequerystatement.="\"".$_POST["name"]."\", ";
		$thequerystatement.="\"".$_POST["search"]."\", ";
		$thequerystatement.="\"".$_POST["displayorder"]."\")";		
		if(mysql_query($thequerystatement)) $thereturn ="Quick Search Item Added"; else $thereturn=mysql_error()." -- ".$thequerystatement;
		
		return $thereturn;
	}// end function
	

	function updateQuicksearch(){
		global $dblink;
		$thequerystatement="UPDATE tablefindoptions set ";
		$thequerystatement.="name=\"".$_POST["name"]."\", ";
		$thequerystatement.="`search`=\"".$_POST["search"]."\" ";
		$thequerystatement.="WHERE id=".$_POST["quicksearchid"];
		if(mysql_query($thequerystatement)) $thereturn ="Quick Search Item Updated"; else $thereturn=mysql_error()." -- ".$thequerystatement;
		
		return $thereturn;
	}

	function deleteQuicksearch($id){
		global $dblink;
		$thequerystatement="DELETE FROM tablefindoptions WHERE id=".$id;
		if(mysql_query($thequerystatement)) $thereturn ="Quick Search Item Deleted"; else $thereturn=mysql_error()." -- ".$thequerystatement;
		
		return $thereturn;
	}

	function moveQuicksearch($id,$direction="up"){
		global $dblink;

		if($direction=="down") $increment="1"; else $increment="-1";

		$thequerystatement="select displayorder,tabledefid FROM tablefindoptions WHERE id=".$id;
		$thequery=mysql_query($thequerystatement) or $thereturn=mysql_error()." -1- ".$thequerystatement;
		$therecord=mysql_fetch_array($thequery);

		$thequerystatement="select max(displayorder) as themax FROM tablefindoptions WHERE tabledefid=".$_GET["id"];
		$thequery=mysql_query($thequerystatement) or $thereturn=mysql_error()." -2- ".$thequerystatement;
		$maxrecord=mysql_fetch_array($thequery);
		
		if(!(($direction=="down" and $therecord["displayorder"]==$maxrecord["themax"]) or ($direction=="up" and $therecord["displayorder"]=="0"))){
			$thequerystatement="UPDATE tablefindoptions set displayorder=".$therecord["displayorder"]." 
								WHERE displayorder=".($increment+$therecord["displayorder"])." AND tabledefid=".$_GET["id"];
			$thequery=mysql_query($thequerystatement) or $thereturn=mysql_error()." -4- ".$thequerystatement;

			$thequerystatement="UPDATE tablefindoptions set displayorder=displayorder+".$increment." WHERE id=".$id;
			$thequery=mysql_query($thequerystatement) or $thereturn=mysql_error()." -3- ".$thequerystatement;
		}// end if
		
		if(isset($thereturn)) return $thereturn; else return "Position Moved";
	}
?>