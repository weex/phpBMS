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
		$thequerystatement="SELECT id, field, name, displayorder,type
		FROM tablesearchablefields 
		WHERE tabledefid=".$tabledefid;
		if($searchfieldid) $thequerystatement.=" AND id=".$searchfieldid;
		$thequerystatement.=" ORDER BY displayorder";
		
		$thequery=mysql_query($thequerystatement) or $thequery=mysql_error()." -- ".$thequerystatement;		
		return $thequery;
	}// end function


	function addSearchfield(){
		global $dblink;
		$thequerystatement="INSERT INTO tablesearchablefields (tabledefid, field, name, displayorder,type)
		values (";
		$thequerystatement.=$_GET["id"].", ";
		$thequerystatement.="\"".$_POST["field"]."\", ";
		$thequerystatement.="\"".$_POST["name"]."\", ";
		$thequerystatement.="\"".$_POST["displayorder"]."\", ";		
		$thequerystatement.="\"".$_POST["type"]."\")";		
		if(mysql_query($thequerystatement)) $thereturn ="Search Field Added"; else $thereturn=mysql_error()." -- ".$thequerystatement;
		
		return $thereturn;
	}// end function
	

	function updateSearchfield(){
		global $dblink;
		$thequerystatement="UPDATE tablesearchablefields set ";
		$thequerystatement.="field=\"".$_POST["field"]."\", ";
		$thequerystatement.="type=\"".$_POST["type"]."\", ";
		$thequerystatement.="name=\"".$_POST["name"]."\" ";
		$thequerystatement.="WHERE id=".$_POST["searchfieldid"];
		if(mysql_query($thequerystatement)) $thereturn ="Search Field Updated"; else $thereturn=mysql_error()." -- ".$thequerystatement;
		
		return $thereturn;
	}

	function deleteSearchfield($id){
		global $dblink;
		$thequerystatement="DELETE FROM tablesearchablefields WHERE id=".$id;
		if(mysql_query($thequerystatement)) $thereturn ="Search Field Deleted"; else $thereturn=mysql_error()." -- ".$thequerystatement;
		
		return $thereturn;
	}

	function moveSearchfield($id,$direction="up"){
		global $dblink;

		if($direction=="down") $increment="1"; else $increment="-1";

		$thequerystatement="select displayorder FROM tablesearchablefields WHERE id=".$id;
		$thequery=mysql_query($thequerystatement) or $thereturn=mysql_error()." -1- ".$thequerystatement;
		$therecord=mysql_fetch_array($thequery);

		$thequerystatement="select max(displayorder) as themax FROM tablesearchablefields WHERE tabledefid=".$_GET["id"];
		$thequery=mysql_query($thequerystatement) or $thereturn=mysql_error()." -2- ".$thequerystatement;
		$maxrecord=mysql_fetch_array($thequery);
		
		if(!(($direction=="down" and $therecord["displayorder"]==$maxrecord["themax"]) or ($direction=="up" and $therecord["displayorder"]=="0"))){
			$thequerystatement="UPDATE tablesearchablefields set displayorder=".$therecord["displayorder"]." 
								WHERE displayorder=".($increment+$therecord["displayorder"])." AND tabledefid=".$_GET["id"];
			$thequery=mysql_query($thequerystatement) or $thereturn=mysql_error()." -4- ".$thequerystatement;

			$thequerystatement="UPDATE tablesearchablefields set displayorder=displayorder+".$increment." WHERE id=".$id;
			$thequery=mysql_query($thequerystatement) or $thereturn=mysql_error()." -3- ".$thequerystatement;
		}// end if
		
		if(isset($thereturn)) return $thereturn; else return "Position Moved";
	}
?>