<?php	
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
 | All rights reserved.                                                    |
 +-------------------------------------------------------------------------+
 |                                                                         |
 | Redistribution and use in source and binary forms, with or without      |
 | modification, are permitted provided that the following conditions are  |
 | met:                                                                    |
 |                                                                         |
 | - Redistributions of source code must retain the above copyright        |
 |   notice, this list of conditions and the following disclaimer.         |
 |                                                                         |
 | - Redistributions in binary form must reproduce the above copyright     |
 |   notice, this list of conditions and the following disclaimer in the   |
 |   documentation and/or other materials provided with the distribution.  |
 |                                                                         |
 | - Neither the name of Kreotek LLC nor the names of its contributore may |
 |   be used to endorse or promote products derived from this software     |
 |   without specific prior written permission.                            |
 |                                                                         |
 | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS     |
 | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT       |
 | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A |
 | PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
 | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,   |
 | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT        |
 | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,   |
 | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY   |
 | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT     |
 | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE   |
 | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.    |
 |                                                                         |
 +-------------------------------------------------------------------------+
*/



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
		$therecord["format"]="";

		return $therecord;
	}
	
	function getColumns($tabledefid,$columnid=false){
		global $dblink;
		$querystatement="SELECT id, name, `column`, align, footerquery, sortorder, displayorder,wrap,size,format
		FROM tablecolumns 
		WHERE tabledefid=".$tabledefid;
		if($columnid) $querystatement.=" AND id=".$columnid;
		$querystatement.=" ORDER BY displayorder";
		
		$queryresult=mysql_query($querystatement,$dblink) or $queryresult=mysql_error($dblink)." -- ".$querystatement;		
		return $queryresult;
	}// end function


	function addColumn($variables,$tabledefid){
		global $dblink;
		$querystatement="INSERT INTO tablecolumns (tabledefid, name, `column`, align, footerquery, sortorder, displayorder,size,format,wrap)
		values (";
		$querystatement.=$tabledefid.", ";
		$querystatement.="\"".$variables["name"]."\", ";
		$querystatement.="\"".$variables["column"]."\", ";
		$querystatement.="\"".$variables["align"]."\", ";
		$querystatement.="\"".$variables["footerquery"]."\", ";
		$querystatement.="\"".$variables["sortorder"]."\", ";
		$querystatement.="\"".$variables["displayorder"]."\", ";		
		$querystatement.="\"".$variables["size"]."\", ";		
		if($variables["format"])
			$querystatement.="\"".$variables["format"]."\", ";
		else
			$querystatement.="NULL, ";
		if(!isset($variables["wrap"])) $variables["wrap"]=0;
		$querystatement.=" ".$variables["wrap"]." )";		

		if(mysql_query($querystatement,$dblink)) $thereturn ="Column Added"; else $thereturn=mysql_error($dblink)." <BR>".$querystatement;
		
		return $thereturn;
	}// end function
	

	function updateColumn($variables){
		global $dblink;
		$querystatement="UPDATE tablecolumns set ";
		$querystatement.="name=\"".$variables["name"]."\", ";
		$querystatement.="`column`=\"".$variables["column"]."\", ";
		$querystatement.="align=\"".$variables["align"]."\", ";
		$querystatement.="sortorder=\"".$variables["sortorder"]."\", ";
		$querystatement.="footerquery=\"".$variables["footerquery"]."\", ";		
		$querystatement.="size=\"".$variables["size"]."\", ";		
		if($variables["format"])
			$querystatement.="format=\"".$variables["format"]."\", ";
		else
			$querystatement.="format=NULL, ";
		if(!isset($variables["wrap"])) $variables["wrap"]=0;
		$querystatement.="wrap=".$variables["wrap"]." ";		
		$querystatement.="WHERE id=".$variables["columnid"];
		if(mysql_query($querystatement,$dblink)) $thereturn ="Column Updated"; else $thereturn=mysql_error($dblink)." <br>".$querystatement;
		
		return $thereturn;
	}

	function deleteColumn($id){
		global $dblink;
		
		$querystatement="SELECT tabledefid,displayorder FROM tablecolumns WHERE id=".$id;
		$theresult=mysql_query($querystatement,$dblink);
		$therecord=mysql_fetch_array($theresult);
				
		
		$querystatement="UPDATE tablecolumns SET displayorder=displayorder-1
							WHERE tabledefid=".$therecord["tabledefid"]." AND displayorder>".$therecord["displayorder"];
		if(!mysql_query($querystatement,$dblink)) $thereturn=mysql_error($dblink)." -- ".$querystatement; else {

			$querystatement="DELETE FROM tablecolumns WHERE id=".$id;
			if(mysql_query($querystatement,$dblink)) $thereturn ="Column Deleted"; else $thereturn=mysql_error($dblink)." -- ".$querystatement;
		}
				
		return $thereturn;
	}

	function moveColumn($id,$direction="up"){
		global $dblink;

		if($direction=="down") $increment="1"; else $increment="-1";

		$querystatement="select displayorder,tabledefid FROM tablecolumns WHERE id=".$id;
		$thequery=mysql_query($querystatement,$dblink) or $thereturn=mysql_error($dblink)." -1- ".$querystatement;
		$therecord=mysql_fetch_array($thequery);

		$querystatement="select max(displayorder) as themax FROM tablecolumns WHERE tabledefid=".$_GET["id"];
		$thequery=mysql_query($querystatement,$dblink) or $thereturn=mysql_error($dblink)." -2- ".$querystatement;
		$maxrecord=mysql_fetch_array($thequery);
		
		if(!(($direction=="down" and $therecord["displayorder"]==$maxrecord["themax"]) or ($direction=="up" and $therecord["displayorder"]=="0"))){
			$querystatement="UPDATE tablecolumns set displayorder=".$therecord["displayorder"]." WHERE displayorder=".($increment+$therecord["displayorder"])." AND tabledefid=".$therecord["tabledefid"];
			$thequery=mysql_query($querystatement,$dblink) or $thereturn=mysql_error($dblink)." -4- ".$querystatement;

			$querystatement="UPDATE tablecolumns set displayorder=displayorder+".$increment." WHERE id=".$id;
			$thequery=mysql_query($querystatement,$dblink) or $thereturn=mysql_error($dblink)." -3- ".$querystatement;
		}// end if
		
		if(isset($thereturn)) return $thereturn; else return "column moved";
	}
?>