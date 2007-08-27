<?php	
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2007, Kreotek LLC                                  |
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
		$therecord["roleid"]=0;		

		$therecord["size"]="";		
		$therecord["name"]="";		
		$therecord["column"]="";		
		$therecord["align"]="left";		
		$therecord["footerquery"]="";		
		$therecord["sortorder"]="";
		$therecord["format"]="";

		return $therecord;
	}
	
	function getColumns($db,$tabledefid,$columnid=false){
		$querystatement="SELECT id, name, `column`, align, footerquery, sortorder, displayorder,wrap,size,format, roleid
		FROM tablecolumns 
		WHERE tabledefid=".((int) $tabledefid);
		if($columnid) $querystatement.=" AND id=".$columnid;
		$querystatement.=" ORDER BY displayorder";
		
		$queryresult=$db->query($querystatement);
		return $queryresult;
	}// end function


	function addColumn($db,$variables,$tabledefid){
		$thereturn = false;

		$querystatement="INSERT INTO tablecolumns (tabledefid, name, `column`, align, footerquery, sortorder, displayorder,size,format,wrap,roleid)
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
		$querystatement.=" ".$variables["wrap"].", ";		
		$querystatement.=" ".$variables["roleid"]." )";

		if($db->query($querystatement)) $thereturn ="Column Added";
		
		return $thereturn;
	}// end function
	

	function updateColumn($db, $variables){

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
		$querystatement.="wrap=".$variables["wrap"].", ";		
		$querystatement.="roleid=".$variables["roleid"]." ";		
		$querystatement.="WHERE id=".$variables["columnid"];
		if($db->query($querystatement)) $thereturn ="Column Updated";
		
		return $thereturn;
	}

	function deleteColumn($db,$id){
		
		$querystatement="SELECT tabledefid,displayorder FROM tablecolumns WHERE id=".$id;
		$theresult=$db->query($querystatement);
		$therecord=$db->fetchArray($theresult);
						
		$querystatement="UPDATE tablecolumns SET displayorder=displayorder-1
							WHERE tabledefid=".$therecord["tabledefid"]." AND displayorder>".$therecord["displayorder"];
		if($db->query($querystatement)) {

			$querystatement="DELETE FROM tablecolumns WHERE id=".$id;
			if($db->query($querystatement)) $thereturn ="Column Deleted"; 
		}
				
		return $thereturn;
	}

	function moveColumn($db,$id,$direction="up"){

		if($direction=="down") $increment="1"; else $increment="-1";

		$querystatement="select displayorder,tabledefid FROM tablecolumns WHERE id=".$id;
		$thequery=$db->query($querystatement);
		$therecord=$db->fetchArray($thequery);

		$querystatement="select max(displayorder) as themax FROM tablecolumns WHERE tabledefid=".$_GET["id"];
		$thequery=$db->query($querystatement);
		$maxrecord=$db->fetchArray($thequery);
		
		if(!(($direction=="down" and $therecord["displayorder"]==$maxrecord["themax"]) or ($direction=="up" and $therecord["displayorder"]=="0"))){
			$querystatement="UPDATE tablecolumns set displayorder=".$therecord["displayorder"]." WHERE displayorder=".($increment+$therecord["displayorder"])." AND tabledefid=".$therecord["tabledefid"];
			$thequery=$db->query($querystatement);

			$querystatement="UPDATE tablecolumns set displayorder=displayorder+".$increment." WHERE id=".$id;
			$thequery=$db->query($querystatement);
		}// end if
		
		if(isset($thereturn)) return $thereturn; else return "column moved";
	}
?>