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
	if($_SESSION["userinfo"]["accesslevel"]<90) header("Location: ".$_SESSION["app_path"]."noaccess.html");
	function setOptionDefaults(){
		$therecord["id"]=NULL;		
		$therecord["name"]="";		
		$therecord["option"]="";		
		$therecord["othercommand"]=0;		
		$therecord["accesslevel"]=0;		

		return $therecord;		
	}
	
	function getOptions($tabledefid,$optionid=false){
		global $dblink;

		$querystatement="SELECT id, name, `option`, othercommand, accesslevel
		FROM tableoptions 
		WHERE tabledefid=".$tabledefid;
		if($optionid) $querystatement.=" AND id=".$optionid;
		$querystatement.=" ORDER BY othercommand, id";
		
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(300,mysql_error($dblink)." -- ".$querystatement);
		
		return $queryresult;
	}// end function


	function addOption($variables,$tabledefid){
		global $dblink;
		$querystatement="INSERT INTO tableoptions (tabledefid, accesslevel, name, `option`, othercommand)
		values (";
		$querystatement.=$tabledefid.", ";
		$querystatement.=$variables["accesslevel"].", ";
		if($variables["othercommand"]==1) {
			$querystatement.="\"".$variables["name"]."\", ";
			$querystatement.="\"".$variables["option"]."\", ";
		} else {
			$querystatement.="\"".$variables["pdName"]."\", ";
			$querystatement.="\"".$variables["pdOption"]."\", ";
		}
		$querystatement.="\"".$variables["othercommand"]."\") ";
		if(mysql_query($querystatement)) $thereturn ="Option Added"; else $thereturn=mysql_error($dblink)." -- ".$querystatement;
		
		return $thereturn;
	}// end function
	

	function updateOption($variables){
		global $dblink;
		$querystatement="UPDATE tableoptions set ";
		$querystatement.="othercommand=".$variables["othercommand"].", ";		
		$querystatement.="accesslevel=".$variables["accesslevel"].", ";
		if($variables["othercommand"]==1) {
			$querystatement.="name=\"".$variables["name"]."\", ";
			$querystatement.="`option`=\"".$variables["option"]."\", ";
		} else {
			$querystatement.="name=\"".$variables["pdName"]."\", ";
			$querystatement.="`option`=\"".$variables["pdOption"]."\", ";
		}
		$querystatement.="othercommand=".$variables["othercommand"]." ";
		$querystatement.="WHERE id=".$variables["optionid"];
		if(mysql_query($querystatement)) $thereturn ="Option Updated"; else $thereturn=mysql_error($dblink)." -- ".$querystatement;
		
		return $thereturn;
	}

	function deleteOption($id){
		global $dblink;
		$querystatement="DELETE FROM tableoptions WHERE id=".$id;
		if(mysql_query($querystatement)) $thereturn ="Option Deleted"; else $thereturn=mysql_error($dblink)." -- ".$querystatement;
		
		return $thereturn;
	}

?>