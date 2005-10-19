<?php
	if($_SESSION["userinfo"]["accesslevel"]<90) header("Location: ".$_SESSION["app_path"]."noaccess.html");
	function setDefaultQuickSearch(){
		$therecord["id"]=NULL;		
		$therecord["displayorder"]=NULL;		

		$therecord["name"]="";		
		$therecord["accesslevel"]=11;		
		$therecord["search"]="";		

		return $therecord;
	}
	
	function getQuicksearchs($tabledefid,$quicksearchid=false){
		global $dblink;
		$querystatement="SELECT id, name, `search`, displayorder, accesslevel
		FROM tablefindoptions 
		WHERE tabledefid=".$tabledefid;
		if($quicksearchid) $querystatement.=" AND id=".$quicksearchid;
		$querystatement.=" ORDER BY displayorder";
		
		$thequery=mysql_query($querystatement) or $thequery=mysql_error($dblink)." -- ".$querystatement;		
		return $thequery;
	}// end function


	function addQuicksearch($variables,$tabledefid){
		global $dblink;
		$querystatement="INSERT INTO tablefindoptions (tabledefid, name, `search`, accesslevel, displayorder)
		values (";
		$querystatement.=$tabledefid.", ";
		$querystatement.="\"".$variables["name"]."\", ";
		$querystatement.="\"".$variables["search"]."\", ";
		$querystatement.="\"".$variables["accesslevel"]."\", ";
		$querystatement.="\"".$variables["displayorder"]."\")";		
		if(mysql_query($querystatement)) $thereturn ="Quick Search Item Added"; else $thereturn=mysql_error($dblink)." -- ".$querystatement;
		
		return $thereturn;
	}// end function
	

	function updateQuicksearch($variables){
		global $dblink;
		$querystatement="UPDATE tablefindoptions set ";
		$querystatement.="name=\"".$variables["name"]."\", ";
		$querystatement.="accesslevel=\"".$variables["accesslevel"]."\", ";
		$querystatement.="`search`=\"".$variables["search"]."\" ";
		$querystatement.="WHERE id=".$variables["quicksearchid"];
		if(mysql_query($querystatement)) $thereturn ="Quick Search Item Updated"; else $thereturn=mysql_error($dblink)." -- ".$querystatement;
		
		return $thereturn;
	}

	function deleteQuicksearch($id){
		global $dblink;
		$querystatement="DELETE FROM tablefindoptions WHERE id=".$id;
		if(mysql_query($querystatement)) $thereturn ="Quick Search Item Deleted"; else $thereturn=mysql_error($dblink)." -- ".$querystatement;
		
		return $thereturn;
	}

	function moveQuicksearch($id,$direction="up",$tabledefid){
		global $dblink;

		if($direction=="down") $increment="1"; else $increment="-1";

		$querystatement="select displayorder,tabledefid FROM tablefindoptions WHERE id=".$id;
		$thequery=mysql_query($querystatement) or $thereturn=mysql_error($dblink)." -1- ".$querystatement;
		$therecord=mysql_fetch_array($thequery);

		$querystatement="select max(displayorder) as themax FROM tablefindoptions WHERE tabledefid=".$tabledefid;
		$thequery=mysql_query($querystatement) or $thereturn=mysql_error($dblink)." -2- ".$querystatement;
		$maxrecord=mysql_fetch_array($thequery);
		
		if(!(($direction=="down" and $therecord["displayorder"]==$maxrecord["themax"]) or ($direction=="up" and $therecord["displayorder"]=="0"))){
			$querystatement="UPDATE tablefindoptions set displayorder=".$therecord["displayorder"]." 
								WHERE displayorder=".($increment+$therecord["displayorder"])." AND tabledefid=".$tabledefid;
			$thequery=mysql_query($querystatement) or $thereturn=mysql_error($dblink)." -4- ".$querystatement;

			$querystatement="UPDATE tablefindoptions set displayorder=displayorder+".$increment." WHERE id=".$id;
			$thequery=mysql_query($querystatement) or $thereturn=mysql_error($dblink)." -3- ".$querystatement;
		}// end if
		
		if(isset($thereturn)) return $thereturn; else return "Position Moved";
	}
?>