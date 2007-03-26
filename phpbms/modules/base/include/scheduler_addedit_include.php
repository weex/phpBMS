<?php
/*
 $Rev: 176 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2006-12-27 18:40:19 -0700 (Wed, 27 Dec 2006) $
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

// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=201;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT id, name, job, crontab, lastrun, startdatetime, enddatetime, 
				description, inactive, lastrun,
	
				createdby, creationdate, 
				modifiedby, modifieddate
				FROM scheduler
				WHERE id=".$id;		
	$thequery = mysql_query($querystatement,$dblink);
	$therecord = mysql_fetch_array($thequery);
	
	$datearray=explode(" ",$therecord["startdatetime"]);
	$therecord["startdate"]=$datearray[0];
	if(isset($datearray[1])) $therecord["starttime"]=$datearray[1]; else $therecord["starttime"]="";
	
	$datearray=explode(" ",$therecord["enddatetime"]);
	$therecord["enddate"]=$datearray[0];
	if(isset($datearray[1])) $therecord["endtime"]=$datearray[1]; else $therecord["endtime"]="";

	$cronarray=explode("::",$therecord["crontab"]);
	if(isset($cronarray[0])) $therecord["min"]=$cronarray[0]; else $therecord["min"]="*";
	if(isset($cronarray[1])) $therecord["hrs"]=$cronarray[1]; else $therecord["hrs"]="*";
	if(isset($cronarray[2])) $therecord["date"]=$cronarray[2]; else $therecord["date"]="*";
	if(isset($cronarray[3])) $therecord["mo"]=$cronarray[3]; else $therecord["mo"]="*";
	if(isset($cronarray[4])) $therecord["day"]=$cronarray[4]; else $therecord["day"]="*";
	
	$therecord["lastrun"]=formatFromSQLDatetime($therecord["lastrun"]);
	
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	$therecord["id"]=NULL;

	$therecord["name"]="";
	$therecord["job"]="";
	$therecord["description"]="";
	$therecord["crontab"]="*::*::*::*::*";
	$therecord["inactive"]=0;
	$therecord["lastrun"]="";


	$therecord["min"]="*";
	$therecord["hrs"]="*";
	$therecord["date"]="*";
	$therecord["mo"]="*";
	$therecord["day"]="*";

	$therecord["startdate"]=dateToString(mktime(),"SQL");
	$therecord["starttime"]="";

	$therecord["enddate"]="";
	$therecord["endtime"]="";

	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;

	return $therecord;	
}//end function


function updateRecord($variables,$userid){
//========================================================================================
	global $dblink;
	
	$querystatement="UPDATE scheduler SET ";
	
	//fields
	$querystatement.="name=\"".$variables["name"]."\", "; 
	$querystatement.="job=\"".$variables["job"]."\", "; 
	$querystatement.="description=\"".$variables["description"]."\", "; 	
	$querystatement.="inactive=";
	if(isset($variables["inactive"])) $querystatement.="1"; else $querystatement.="0";
	$querystatement.=",";

	$temparray[0]=$variables["min"];
	$temparray[1]=$variables["hrs"];
	$temparray[2]=$variables["date"];
	$temparray[3]=$variables["mo"];
	$temparray[4]=$variables["day"];
	$variables["crontab"]=implode("::",$temparray);
	$querystatement.="crontab=\"".$variables["crontab"]."\", "; 
	
	if($variables["startdate"]){
		$variables["startdate"]=sqlDateFromString($variables["startdate"]);
		if($variables["starttime"])
			$variables["startdate"].=" ".sqlTimeFromString($variables["starttime"]);
		$querystatement.="startdatetime=\"".$variables["startdate"]."\", "; 			
	}
	else
		$querystatement.="startdatetime=NULL, "; 

	if($variables["enddate"]){
		$variables["enddate"]=sqlDateFromString($variables["enddate"]);
		if($variables["endtime"])
			$variables["enddate"].=" ".sqlTimeFromString($variables["endtime"]);
		$querystatement.="enddatetime=\"".$variables["enddate"]."\", "; 			
	}
	else
		$querystatement.="enddatetime=NULL, "; 
	

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$userid."\" "; 
	$querystatement.="where id=".$variables["id"];
	
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);

}// end function


function insertRecord($variables,$userid){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO scheduler ";
	
	$querystatement.="(name,job,description,inactive,
	crontab,startdatetime,enddatetime,
	createdby,creationdate,modifiedby) VALUES (";
	
//fields
	$querystatement.="\"".$variables["name"]."\", "; 
	$querystatement.="\"".$variables["job"]."\", "; 
	$querystatement.="\"".$variables["description"]."\", "; 	
	if(isset($variables["inactive"])) $querystatement.="1"; else $querystatement.="0";
	$querystatement.=",";

	$temparray[0]=$variables["min"];
	$temparray[1]=$variables["hrs"];
	$temparray[2]=$variables["date"];
	$temparray[3]=$variables["mo"];
	$temparray[4]=$variables["day"];
	$variables["crontab"]=implode("::",$temparray);
	$querystatement.="\"".$variables["crontab"]."\", "; 
	
	if($variables["startdate"]){
		$variables["startdate"]=sqlDateFromString($variables["startdate"]);
		if($variables["starttime"])
			$variables["startdate"].=" ".sqlTimeFromString($variables["starttime"]);
		$querystatement.="\"".$variables["startdate"]."\", "; 			
	}
	else
		$querystatement.="NULL, "; 

	if($variables["enddate"]){
		$variables["enddate"]=sqlDateFromString($variables["enddate"]);
		if($variables["endtime"])
			$variables["enddate"].=" ".sqlTimeFromString($variables["endtime"]);
		$querystatement.="\"".$variables["enddate"]."\", "; 			
	}
	else
		$querystatement.="NULL, "; 

	//==== Almost all records should have this =========
	$querystatement.=$userid.", "; 
	$querystatement.="Now(), ";
	$querystatement.=$userid.")"; 

	$queryresult= mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Insert Failed: ".mysql_error($dblink)." -- ".$querystatement);
		
	$newid=mysql_insert_id($dblink);

	return $newid;
}



//==================================================================
// Process adding, editing, creating new, canceling or updating
//==================================================================
if(!isset($_POST["command"])){
	$querystatement="SELECT addroleid,editroleid FROM tabledefs WHERE id=".$tableid;
	$tableresult=mysql_query($querystatement,$dblink);
	if(!$tableresult) reportError(200,"Could Not retrieve Table Definition for id ".$tableid);
	$tablerecord=mysql_fetch_array($tableresult);
	
	if(isset($_GET["id"])){
		//editing
		if(!hasRights($tablerecord["editroleid"]))
			goURL($_SESSION["app_path"]."noaccess.php");
		$therecord=getRecords((integer) $_GET["id"]);
	} else {
		if(!hasRights($tablerecord["addroleid"]))
			goURL($_SESSION["app_path"]."noaccess.php");
		$therecord=setRecordDefaults();
	}
	$createdby=getUserName($therecord["createdby"]);
	$modifiedby=getUserName($therecord["modifiedby"]);
}
else
{
	switch($_POST["command"]){
		case "cancel":
			// if we needed to do any clean up (deleteing temp line items)
			if(!isset($_POST["id"])) $_POST["id"]=0;
			$theid=$_POST["id"];
			goURL("../../search.php?id=".$tableid."#".$theid);
		break;
		case "save":
			if($_POST["id"]) {
				updateRecord(addSlashesToArray($_POST),$_SESSION["userinfo"]["id"]);
				$theid=$_POST["id"];
				//get record
				$therecord=getRecords($theid);
				$createdby=getUserName($therecord["createdby"]);
				$modifiedby=getUserName($therecord["modifiedby"]);
				$statusmessage="Record Updated";
			}
			else {
				$theid=insertRecord(addSlashesToArray($_POST),$_SESSION["userinfo"]["id"]);
				//get record
				$therecord=getRecords($theid);
				$createdby=getUserName($therecord["createdby"]);
				$modifiedby=getUserName($therecord["modifiedby"]);
				$statusmessage="<div style=\"float:right;margin-top:-3px;\"><button type=\"button\" class=\"smallButtons\" onclick=\"document.location='".$_SERVER["REQUEST_URI"]."'\">add new</button></div>";
				$statusmessage.="Record Created";
			}
		break;
	}
	
}
?>