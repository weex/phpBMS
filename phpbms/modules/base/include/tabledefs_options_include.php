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
	
	function setOptionDefaults(){
		$therecord["id"]=NULL;		
		$therecord["name"]="";		
		$therecord["option"]="";		
		$therecord["othercommand"]=0;		
		$therecord["roleid"]=0;		

		return $therecord;		
	}
	
	function getOptions($db,$tabledefid,$optionid=false){

		$querystatement="SELECT tableoptions.id, tableoptions.name, tableoptions.option, tableoptions.othercommand, tableoptions.roleid, roles.name as rolename
		FROM tableoptions LEFT JOIN roles ON tableoptions.roleid=roles.id
		WHERE tabledefid=".$tabledefid;
		if($optionid) $querystatement.=" AND tableoptions.id=".$optionid;
		$querystatement.=" ORDER BY othercommand, tableoptions.id";
		
		$queryresult=$db->query($querystatement);
		
		return $queryresult;
	}// end function


	function addOption($db,$variables,$tabledefid){

		$querystatement="INSERT INTO tableoptions (tabledefid, roleid, name, `option`, othercommand)
		values (";
		$querystatement.=$tabledefid.", ";
		$querystatement.=$variables["roleid"].", ";
		if($variables["othercommand"]==1) {
			$querystatement.="\"".$variables["name"]."\", ";
			$querystatement.="\"".$variables["option"]."\", ";
		} else {
			$querystatement.="\"".$variables["pdName"]."\", ";
			$querystatement.="\"".$variables["pdOption"]."\", ";
		}
		$querystatement.="\"".$variables["othercommand"]."\") ";
		if($db->query($querystatement)) $thereturn ="Option Added";
		
		return $thereturn;
	}// end function
	

	function updateOption($db,$variables){

		$querystatement="UPDATE tableoptions set ";
		$querystatement.="othercommand=".$variables["othercommand"].", ";		
		$querystatement.="roleid=".$variables["roleid"].", ";
		if($variables["othercommand"]==1) {
			$querystatement.="name=\"".$variables["name"]."\", ";
			$querystatement.="`option`=\"".$variables["option"]."\", ";
		} else {
			$querystatement.="name=\"".$variables["pdName"]."\", ";
			$querystatement.="`option`=\"".$variables["pdOption"]."\", ";
		}
		$querystatement.="othercommand=".$variables["othercommand"]." ";
		$querystatement.="WHERE id=".$variables["optionid"];
		if($db->query($querystatement)) $thereturn ="Option Updated";
		
		return $thereturn;
	}

	function deleteOption($db,$id){

		$querystatement="DELETE FROM tableoptions WHERE id=".$id;
		if($db->query($querystatement)) $thereturn ="Option Deleted";
		
		return $thereturn;
	}

?>