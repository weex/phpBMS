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


	function setDefaultSearchField(){
		$therecord["id"]=NULL;		
		$therecord["displayorder"]=NULL;		
		$therecord["name"]="";		
		$therecord["type"]="field";		
		$therecord["field"]="";		

		return $therecord;	
	}
	
	function getSearchfields($db,$tabledefid,$searchfieldid=false){

		$querystatement="SELECT id, field, name, displayorder,type
		FROM tablesearchablefields 
		WHERE tabledefid=".$tabledefid;
		if($searchfieldid) $querystatement.=" AND id=".$searchfieldid;
		$querystatement.=" ORDER BY displayorder";
		
		$thequery=$db->query($querystatement);
		return $thequery;
	}// end function


	function addSearchfield($db,$variables,$tabledefid){

		$querystatement = "INSERT INTO tablesearchablefields (tabledefid, field, name, displayorder,type)
		values (";
		$querystatement.=$tabledefid.", ";
		$querystatement.="\"".$variables["field"]."\", ";
		$querystatement.="\"".$variables["name"]."\", ";
		$querystatement.="\"".$variables["displayorder"]."\", ";		
		$querystatement.="\"".$variables["type"]."\")";		
		if($db->query($querystatement)) $thereturn ="Search Field Added";
		
		return $thereturn;
	}// end function
	

	function updateSearchfield($db,$variables){

		$querystatement="UPDATE tablesearchablefields set ";
		$querystatement.="field=\"".$variables["field"]."\", ";
		$querystatement.="type=\"".$variables["type"]."\", ";
		$querystatement.="name=\"".$variables["name"]."\" ";
		$querystatement.="WHERE id=".$variables["searchfieldid"];
		if($db->query($querystatement)) $thereturn ="Search Field Updated";
		
		return $thereturn;
	}

	function deleteSearchfield($db,$id){

 		$querystatement="DELETE FROM tablesearchablefields WHERE id=".$id;

		if($db->query($querystatement)) $thereturn ="Search Field Deleted";
		
		return $thereturn;
	}

	function moveSearchfield($db,$id,$direction="up"){

		if($direction=="down") $increment="1"; else $increment="-1";

		$querystatement="select displayorder FROM tablesearchablefields WHERE id=".$id;
		$thequery = $db->query($querystatement);
		$therecord = $db->fetchArray($thequery);

		$querystatement="select max(displayorder) as themax FROM tablesearchablefields WHERE tabledefid=".$_GET["id"];
		$thequery=$db->query($querystatement);
		$maxrecord=$db->fetchArray($thequery);
		
		if(!(($direction=="down" and $therecord["displayorder"]==$maxrecord["themax"]) or ($direction=="up" and $therecord["displayorder"]=="0"))){
			$querystatement="UPDATE tablesearchablefields set displayorder=".$therecord["displayorder"]." 
								WHERE displayorder=".($increment+$therecord["displayorder"])." AND tabledefid=".$_GET["id"];
			$thequery=$db->query($querystatement);

			$querystatement="UPDATE tablesearchablefields set displayorder=displayorder+".$increment." WHERE id=".$id;
			$thequery=$db->query($querystatement);
		}// end if
		
		if(isset($thereturn)) return $thereturn; else return "Position Moved";
	}
?>