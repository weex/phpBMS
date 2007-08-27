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

	
	function setDefaultQuickSearch(){
		$therecord["id"]=NULL;		
		$therecord["displayorder"]=NULL;		

		$therecord["name"]="";		
		$therecord["roleid"]=0;		
		$therecord["search"]="";		

		return $therecord;
	}
	
	function getQuicksearchs($db,$tabledefid,$quicksearchid=false){

		$querystatement="SELECT tablefindoptions.id, tablefindoptions.name, tablefindoptions.search, tablefindoptions.displayorder, roleid,
		roles.name as rolename
		FROM tablefindoptions LEFT JOIN roles ON tablefindoptions.roleid=roles.id
		WHERE tablefindoptions.tabledefid=".$tabledefid;
		if($quicksearchid) $querystatement.=" AND tablefindoptions.id=".$quicksearchid;
		$querystatement.=" ORDER BY tablefindoptions.displayorder";
		
		$thequery=$db->query($querystatement);
		
		return $thequery;
	}// end function


	function addQuicksearch($db,$variables,$tabledefid){
		$thereturn = false;
		
		$querystatement="INSERT INTO tablefindoptions (tabledefid, name, `search`, roleid, displayorder)
		values (";
		$querystatement.=$tabledefid.", ";
		$querystatement.="\"".$variables["name"]."\", ";
		$querystatement.="\"".$variables["search"]."\", ";
		$querystatement.="\"".$variables["roleid"]."\", ";
		$querystatement.="\"".$variables["displayorder"]."\")";		
		if($db->query($querystatement)) $thereturn ="Quick Search Item Added";
		
		return $thereturn;
	}// end function
	

	function updateQuicksearch($db,$variables){

		$querystatement="UPDATE tablefindoptions set ";
		$querystatement.="name=\"".$variables["name"]."\", ";
		$querystatement.="roleid=\"".$variables["roleid"]."\", ";
		$querystatement.="`search`=\"".$variables["search"]."\" ";
		$querystatement.="WHERE id=".$variables["quicksearchid"];
		if($db->query($querystatement)) $thereturn ="Quick Search Item Updated";
		
		return $thereturn;
	}

	function deleteQuicksearch($db,$id){

		$querystatement="DELETE FROM tablefindoptions WHERE id=".$id;
		if($db->query($querystatement)) $thereturn ="Quick Search Item Deleted";
		
		return $thereturn;
	}

	function moveQuicksearch($db,$id,$direction="up",$tabledefid){

		if($direction=="down") $increment="1"; else $increment="-1";

		$querystatement="select displayorder,tabledefid FROM tablefindoptions WHERE id=".$id;
		$thequery=$db->query($querystatement);
		$therecord=$db->fetchArray($thequery);

		$querystatement="select max(displayorder) as themax FROM tablefindoptions WHERE tabledefid=".$tabledefid;
		$thequery=$db->query($querystatement);
		$maxrecord=$db->fetchArray($thequery);
		
		if(!(($direction=="down" and $therecord["displayorder"]==$maxrecord["themax"]) or ($direction=="up" and $therecord["displayorder"]=="0"))){
			$querystatement="UPDATE tablefindoptions set displayorder=".$therecord["displayorder"]." 
								WHERE displayorder=".($increment+$therecord["displayorder"])." AND tabledefid=".$tabledefid;
			$thequery=$db->query($querystatement);

			$querystatement="UPDATE tablefindoptions set displayorder=displayorder+".$increment." WHERE id=".$id;
			$thequery=$db->query($querystatement);
		}// end if
		
		if(isset($thereturn)) return $thereturn; else return "Position Moved";
	}
?>