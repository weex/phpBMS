<?php	
	function setDefaultSearchField(){
		$therecord["id"]=NULL;		
		$therecord["displayorder"]=NULL;		
		$therecord["name"]="";		
		$therecord["type"]="field";		
		$therecord["field"]="";		

		return $therecord;	
	}
	
	function getSearchfields($tabledefid,$searchfieldid=false){
		global $dblink;
		$querystatement="SELECT id, field, name, displayorder,type
		FROM tablesearchablefields 
		WHERE tabledefid=".$tabledefid;
		if($searchfieldid) $querystatement.=" AND id=".$searchfieldid;
		$querystatement.=" ORDER BY displayorder";
		
		$thequery=mysql_query($querystatement) or $thequery=mysql_error()." -- ".$querystatement;		
		return $thequery;
	}// end function


	function addSearchfield(){
		global $dblink;
		$querystatement="INSERT INTO tablesearchablefields (tabledefid, field, name, displayorder,type)
		values (";
		$querystatement.=$_GET["id"].", ";
		$querystatement.="\"".addslashes($_POST["field"])."\", ";
		$querystatement.="\"".addslashes($_POST["name"])."\", ";
		$querystatement.="\"".$_POST["displayorder"]."\", ";		
		$querystatement.="\"".$_POST["type"]."\")";		
		if(mysql_query($querystatement)) $thereturn ="Search Field Added"; else $thereturn=mysql_error()." -- ".$querystatement;
		
		return $thereturn;
	}// end function
	

	function updateSearchfield(){
		global $dblink;
		$querystatement="UPDATE tablesearchablefields set ";
		$querystatement.="field=\"".addslashes($_POST["field"])."\", ";
		$querystatement.="type=\"".$_POST["type"]."\", ";
		$querystatement.="name=\"".addslashes($_POST["name"])."\" ";
		$querystatement.="WHERE id=".$_POST["searchfieldid"];
		if(mysql_query($querystatement)) $thereturn ="Search Field Updated"; else $thereturn=mysql_error()." -- ".$querystatement;
		
		return $thereturn;
	}

	function deleteSearchfield($id){
		global $dblink;
		$querystatement="DELETE FROM tablesearchablefields WHERE id=".$id;
		if(mysql_query($querystatement)) $thereturn ="Search Field Deleted"; else $thereturn=mysql_error()." -- ".$querystatement;
		
		return $thereturn;
	}

	function moveSearchfield($id,$direction="up"){
		global $dblink;

		if($direction=="down") $increment="1"; else $increment="-1";

		$querystatement="select displayorder FROM tablesearchablefields WHERE id=".$id;
		$thequery=mysql_query($querystatement) or $thereturn=mysql_error()." -1- ".$querystatement;
		$therecord=mysql_fetch_array($thequery);

		$querystatement="select max(displayorder) as themax FROM tablesearchablefields WHERE tabledefid=".$_GET["id"];
		$thequery=mysql_query($querystatement) or $thereturn=mysql_error()." -2- ".$querystatement;
		$maxrecord=mysql_fetch_array($thequery);
		
		if(!(($direction=="down" and $therecord["displayorder"]==$maxrecord["themax"]) or ($direction=="up" and $therecord["displayorder"]=="0"))){
			$querystatement="UPDATE tablesearchablefields set displayorder=".$therecord["displayorder"]." 
								WHERE displayorder=".($increment+$therecord["displayorder"])." AND tabledefid=".$_GET["id"];
			$thequery=mysql_query($querystatement) or $thereturn=mysql_error()." -4- ".$querystatement;

			$querystatement="UPDATE tablesearchablefields set displayorder=displayorder+".$increment." WHERE id=".$id;
			$thequery=mysql_query($querystatement) or $thereturn=mysql_error()." -3- ".$querystatement;
		}// end if
		
		if(isset($thereturn)) return $thereturn; else return "Position Moved";
	}
?>