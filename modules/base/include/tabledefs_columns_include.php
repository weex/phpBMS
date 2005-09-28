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
		$querystatement="SELECT id, name, `column`, align, footerquery, sortorder, displayorder,wrap,size
		FROM tablecolumns 
		WHERE tabledefid=".$tabledefid;
		if($columnid) $querystatement.=" AND id=".$columnid;
		$querystatement.=" ORDER BY displayorder";
		
		$thequery=mysql_query($querystatement,$dblink) or $thequery=mysql_error()." -- ".$querystatement;		
		return $thequery;
	}// end function


	function addColumn(){
		global $dblink;
		$querystatement="INSERT INTO tablecolumns (tabledefid, name, `column`, align, footerquery, sortorder, displayorder,size,wrap)
		values (";
		$querystatement.=$_GET["id"].", ";
		$querystatement.="\"".$_POST["name"]."\", ";
		$querystatement.="\"".addslashes($_POST["column"])."\", ";
		$querystatement.="\"".addslashes($_POST["align"])."\", ";
		$querystatement.="\"".addslashes($_POST["footerquery"])."\", ";
		$querystatement.="\"".addslashes($_POST["sortorder"])."\", ";
		$querystatement.="\"".addslashes($_POST["displayorder"])."\", ";		
		$querystatement.="\"".addslashes($_POST["size"])."\", ";		
		if(!isset($_POST["wrap"])) $_POST["wrap"]=0;
		$querystatement.=" ".$_POST["wrap"]." )";		

		if(mysql_query($querystatement,$dblink)) $thereturn ="Column Added"; else $thereturn=mysql_error()." -- ".$querystatement;
		
		return $thereturn;
	}// end function
	

	function updateColumn(){
		global $dblink;
		$querystatement="UPDATE tablecolumns set ";
		$querystatement.="name=\"".$_POST["name"]."\", ";
		$querystatement.="`column`=\"".addslashes($_POST["column"])."\", ";
		$querystatement.="align=\"".addslashes($_POST["align"])."\", ";
		$querystatement.="sortorder=\"".addslashes($_POST["sortorder"])."\", ";
		$querystatement.="footerquery=\"".addslashes($_POST["footerquery"])."\", ";		
		$querystatement.="size=\"".addslashes($_POST["size"])."\", ";		
		if(!isset($_POST["wrap"])) $_POST["wrap"]=0;
		$querystatement.="wrap=".addslashes($_POST["wrap"])." ";		
		$querystatement.="WHERE id=".$_POST["columnid"];
		if(mysql_query($querystatement,$dblink)) $thereturn ="Column Updated"; else $thereturn=mysql_error()." -- ".$querystatement;
		
		return $thereturn;
	}

	function deleteColumn($id){
		global $dblink;
		
		$querystatement="SELECT tabledefid,displayorder FROM tablecolumns WHERE id=".$id;
		$theresult=mysql_query($querystatement,$dblink);
		$therecord=mysql_fetch_array($theresult);
				
		
		$querystatement="UPDATE tablecolumns SET displayorder=displayorder-1
							WHERE tabledefid=".$therecord["tabledefid"]." AND displayorder>".$therecord["displayorder"];
		if(!mysql_query($querystatement,$dblink)) $thereturn=mysql_error()." -- ".$querystatement; else {

			$querystatement="DELETE FROM tablecolumns WHERE id=".$id;
			if(mysql_query($querystatement,$dblink)) $thereturn ="Column Deleted"; else $thereturn=mysql_error()." -- ".$querystatement;
		}
				
		return $thereturn;
	}

	function moveColumn($id,$direction="up"){
		global $dblink;

		if($direction=="down") $increment="1"; else $increment="-1";

		$querystatement="select displayorder,tabledefid FROM tablecolumns WHERE id=".$id;
		$thequery=mysql_query($querystatement,$dblink) or $thereturn=mysql_error()." -1- ".$querystatement;
		$therecord=mysql_fetch_array($thequery);

		$querystatement="select max(displayorder) as themax FROM tablecolumns WHERE tabledefid=".$_GET["id"];
		$thequery=mysql_query($querystatement,$dblink) or $thereturn=mysql_error()." -2- ".$querystatement;
		$maxrecord=mysql_fetch_array($thequery);
		
		if(!(($direction=="down" and $therecord["displayorder"]==$maxrecord["themax"]) or ($direction=="up" and $therecord["displayorder"]=="0"))){
			$querystatement="UPDATE tablecolumns set displayorder=".$therecord["displayorder"]." WHERE displayorder=".($increment+$therecord["displayorder"])." AND tabledefid=".$therecord["tabledefid"];
			$thequery=mysql_query($querystatement,$dblink) or $thereturn=mysql_error()." -4- ".$querystatement;

			$querystatement="UPDATE tablecolumns set displayorder=displayorder+".$increment." WHERE id=".$id;
			$thequery=mysql_query($querystatement,$dblink) or $thereturn=mysql_error()." -3- ".$querystatement;
		}// end if
		
		if(isset($thereturn)) return $thereturn; else return "column moved";
	}
?>