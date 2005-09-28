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
		$querystatement="SELECT id, name, `search`, displayorder
		FROM tablefindoptions 
		WHERE tabledefid=".$tabledefid;
		if($quicksearchid) $querystatement.=" AND id=".$quicksearchid;
		$querystatement.=" ORDER BY displayorder";
		
		$thequery=mysql_query($querystatement) or $thequery=mysql_error()." -- ".$querystatement;		
		return $thequery;
	}// end function


	function addQuicksearch(){
		global $dblink;
		$querystatement="INSERT INTO tablefindoptions (tabledefid, name, `search`, displayorder)
		values (";
		$querystatement.=$_GET["id"].", ";
		$querystatement.="\"".$_POST["name"]."\", ";
		$querystatement.="\"".$_POST["search"]."\", ";
		$querystatement.="\"".$_POST["displayorder"]."\")";		
		if(mysql_query($querystatement)) $thereturn ="Quick Search Item Added"; else $thereturn=mysql_error()." -- ".$querystatement;
		
		return $thereturn;
	}// end function
	

	function updateQuicksearch(){
		global $dblink;
		$querystatement="UPDATE tablefindoptions set ";
		$querystatement.="name=\"".$_POST["name"]."\", ";
		$querystatement.="`search`=\"".$_POST["search"]."\" ";
		$querystatement.="WHERE id=".$_POST["quicksearchid"];
		if(mysql_query($querystatement)) $thereturn ="Quick Search Item Updated"; else $thereturn=mysql_error()." -- ".$querystatement;
		
		return $thereturn;
	}

	function deleteQuicksearch($id){
		global $dblink;
		$querystatement="DELETE FROM tablefindoptions WHERE id=".$id;
		if(mysql_query($querystatement)) $thereturn ="Quick Search Item Deleted"; else $thereturn=mysql_error()." -- ".$querystatement;
		
		return $thereturn;
	}

	function moveQuicksearch($id,$direction="up"){
		global $dblink;

		if($direction=="down") $increment="1"; else $increment="-1";

		$querystatement="select displayorder,tabledefid FROM tablefindoptions WHERE id=".$id;
		$thequery=mysql_query($querystatement) or $thereturn=mysql_error()." -1- ".$querystatement;
		$therecord=mysql_fetch_array($thequery);

		$querystatement="select max(displayorder) as themax FROM tablefindoptions WHERE tabledefid=".$_GET["id"];
		$thequery=mysql_query($querystatement) or $thereturn=mysql_error()." -2- ".$querystatement;
		$maxrecord=mysql_fetch_array($thequery);
		
		if(!(($direction=="down" and $therecord["displayorder"]==$maxrecord["themax"]) or ($direction=="up" and $therecord["displayorder"]=="0"))){
			$querystatement="UPDATE tablefindoptions set displayorder=".$therecord["displayorder"]." 
								WHERE displayorder=".($increment+$therecord["displayorder"])." AND tabledefid=".$_GET["id"];
			$thequery=mysql_query($querystatement) or $thereturn=mysql_error()." -4- ".$querystatement;

			$querystatement="UPDATE tablefindoptions set displayorder=displayorder+".$increment." WHERE id=".$id;
			$thequery=mysql_query($querystatement) or $thereturn=mysql_error()." -3- ".$querystatement;
		}// end if
		
		if(isset($thereturn)) return $thereturn; else return "Position Moved";
	}
?>