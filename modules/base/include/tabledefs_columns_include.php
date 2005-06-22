<?php	
	function setColumnDefaults(){
		$therecord["id"]=NULL;		
		$therecord["displayorder"]=NULL;		
		$therecord["wrap"]=0;		

		$therecord["size"]="";		
		$therecord["name"]="";		
		$therecord["column"]="";		
		$therecord["align"]="left";		
		$therecord["footerquery"]="";		
		$therecord["sortorder"]="";		

		return $therecord;
	}
	
	function getColumns($tabledefid,$columnid=false){
		global $dblink;
		$thequerystatement="SELECT id, name, `column`, align, footerquery, sortorder, displayorder,wrap,size
		FROM tablecolumns 
		WHERE tabledefid=".$tabledefid;
		if($columnid) $thequerystatement.=" AND id=".$columnid;
		$thequerystatement.=" ORDER BY displayorder";
		
		$thequery=mysql_query($thequerystatement,$dblink) or $thequery=mysql_error()." -- ".$thequerystatement;		
		return $thequery;
	}// end function


	function addColumn(){
		global $dblink;
		$thequerystatement="INSERT INTO tablecolumns (tabledefid, name, `column`, align, footerquery, sortorder, displayorder,size,wrap)
		values (";
		$thequerystatement.=$_GET["id"].", ";
		$thequerystatement.="\"".$_POST["name"]."\", ";
		$thequerystatement.="\"".$_POST["column"]."\", ";
		$thequerystatement.="\"".$_POST["align"]."\", ";
		$thequerystatement.="\"".$_POST["footerquery"]."\", ";
		$thequerystatement.="\"".$_POST["sortorder"]."\", ";
		$thequerystatement.="\"".$_POST["displayorder"]."\", ";		
		$thequerystatement.="\"".$_POST["size"]."\", ";		
		if(!isset($_POST["wrap"])) $_POST["wrap"]=0;
		$thequerystatement.=" ".$_POST["wrap"]." )";		

		if(mysql_query($thequerystatement,$dblink)) $thereturn ="Column Added"; else $thereturn=mysql_error()." -- ".$thequerystatement;
		
		return $thereturn;
	}// end function
	

	function updateColumn(){
		global $dblink;
		$thequerystatement="UPDATE tablecolumns set ";
		$thequerystatement.="name=\"".$_POST["name"]."\", ";
		$thequerystatement.="`column`=\"".$_POST["column"]."\", ";
		$thequerystatement.="align=\"".$_POST["align"]."\", ";
		$thequerystatement.="sortorder=\"".$_POST["sortorder"]."\", ";
		$thequerystatement.="footerquery=\"".$_POST["footerquery"]."\", ";		
		$thequerystatement.="size=\"".$_POST["size"]."\", ";		
		if(!isset($_POST["wrap"])) $_POST["wrap"]=0;
		$thequerystatement.="wrap=".$_POST["wrap"]." ";		
		$thequerystatement.="WHERE id=".$_POST["columnid"];
		if(mysql_query($thequerystatement,$dblink)) $thereturn ="Column Updated"; else $thereturn=mysql_error()." -- ".$thequerystatement;
		
		return $thereturn;
	}

	function deleteColumn($id){
		global $dblink;
		
		$thequerystatement="SELECT tabledefid,displayorder FROM tablecolumns WHERE id=".$id;
		$theresult=mysql_query($thequerystatement,$dblink);
		$therecord=mysql_fetch_array($theresult);
				
		
		$thequerystatement="UPDATE tablecolumns SET displayorder=displayorder-1
							WHERE tabledefid=".$therecord["tabledefid"]." AND displayorder>".$therecord["displayorder"];
		if(!mysql_query($thequerystatement,$dblink)) $thereturn=mysql_error()." -- ".$thequerystatement; else {

			$thequerystatement="DELETE FROM tablecolumns WHERE id=".$id;
			if(mysql_query($thequerystatement,$dblink)) $thereturn ="Column Deleted"; else $thereturn=mysql_error()." -- ".$thequerystatement;
		}
				
		return $thereturn;
	}

	function moveColumn($id,$direction="up"){
		global $dblink;

		if($direction=="down") $increment="1"; else $increment="-1";

		$thequerystatement="select displayorder,tabledefid FROM tablecolumns WHERE id=".$id;
		$thequery=mysql_query($thequerystatement,$dblink) or $thereturn=mysql_error()." -1- ".$thequerystatement;
		$therecord=mysql_fetch_array($thequery);

		$thequerystatement="select max(displayorder) as themax FROM tablecolumns WHERE tabledefid=".$_GET["id"];
		$thequery=mysql_query($thequerystatement,$dblink) or $thereturn=mysql_error()." -2- ".$thequerystatement;
		$maxrecord=mysql_fetch_array($thequery);
		
		if(!(($direction=="down" and $therecord["displayorder"]==$maxrecord["themax"]) or ($direction=="up" and $therecord["displayorder"]=="0"))){
			$thequerystatement="UPDATE tablecolumns set displayorder=".$therecord["displayorder"]." WHERE displayorder=".($increment+$therecord["displayorder"])." AND tabledefid=".$therecord["tabledefid"];
			$thequery=mysql_query($thequerystatement,$dblink) or $thereturn=mysql_error()." -4- ".$thequerystatement;

			$thequerystatement="UPDATE tablecolumns set displayorder=displayorder+".$increment." WHERE id=".$id;
			$thequery=mysql_query($thequerystatement,$dblink) or $thereturn=mysql_error()." -3- ".$thequerystatement;
		}// end if
		
		if(isset($thereturn)) return $thereturn; else return "column moved";
	}
?>