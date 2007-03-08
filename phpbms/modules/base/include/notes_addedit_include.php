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

function checkForNewerRepeats($parentid,$id){
	global $dblink;
	if ($parentid=="NULL")
		$parentid=$id;
		
	$querystatement="SELECT creationdate FROM notes WHERE id=".$id;
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,("Error retrieving creationdate lookup".mysql_error($dblink)." ".$querystatement));
	$therecord=mysql_fetch_array($queryresult);

	$querystatement="SELECT id FROM notes WHERE completed=0 AND parentid=".$parentid." AND creationdate > \"".$therecord["creationdate"]."\"";
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,("Error retrieving  last child record".mysql_error($dblink)." ".$querystatement));
	if(mysql_num_rows($queryresult)) return true; else return false;
}

function repeatTaskUpdate($parentid){
	global $dblink;
	
	$querystatement="DELETE FROM notes WHERE completed=0 AND parentid=".$parentid;
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,("Error deleteing uncompleted children".mysql_error($dblink)." ".$querystatement));
	
	$querystatement="SELECT id FROM notes WHERE completed=1 AND parentid=".$parentid." ORDER BY startdate DESC LIMIT 0,1";
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,("Error retrieving  last child record".mysql_error($dblink)." ".$querystatement));
	if(mysql_num_rows($queryresult)){
		$therecord=mysql_fetch_array($queryresult);
		repeatTask($therecord["id"]);
	} else {
		repeatTask($parentid);
	}
}


// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=12;
// For notes, we need to track who called you, so that when the save/cancel, it takes them to the appropriate page
if(!isset($_GET["backurl"])) 
	$backurl=$_SESSION["app_path"]."search.php?id=".$tableid; 
else{ 
	$backurl=$_GET["backurl"];
	if(isset($_GET["refid"]))
		$backurl.="?refid=".$_GET["refid"];
}


function getAttachedTableDefInfo($id){
	global $dblink;
	if($id){
		$querystatement="SELECT displayname,editfile FROM tabledefs WHERE id =".$id;
		$queryresult=mysql_query($querystatement,$dblink) or reportError(100,"Error Retrieving Table Definition: ".mysql_error($dblink)." -- ".$querystatement);
		$therecord=mysql_fetch_array($queryresult);
	}else{
		$therecord["displayname"]="";
		$therecord["editfile"]="";
	}
	
	return $therecord;	
}

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT
				id, subject, assignedtoid, `type`, content, importance, category,
				attachedtabledefid, attachedid, parentid, location, private, `status`,
				repeatfrequency, repeattype, repeatdays,repeattimes,`repeat`, 
				repeatuntildate,
				completed,
				completeddate,
				startdate,
				starttime,
				enddate, 
				endtime,
				assignedtoid,
				assignedtodate,
				assignedtotime,assignedbyid,

				createdby, creationdate, 
				modifiedby, modifieddate
				FROM notes
				WHERE id=".$id;		
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Could not retrieve record: ".mysql_error($dblink)." ".$querystatement));
	$therecord = mysql_fetch_array($queryresult);
	if(!$therecord) reportError(300,"No record for id ".$id);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	
	$therecord["id"]=NULL;
	$therecord["parentid"]=NULL;
	$therecord["importance"]=0;
	$therecord["type"]="NT";
	if(isset($_GET["ty"]))
		$therecord["type"]=$_GET["ty"];
	$therecord["location"]="";
	$therecord["subject"]="";
	$therecord["content"]="";
	$therecord["category"]="";
	$therecord["status"]="";

	$therecord["private"]=true;

	$therecord["startdate"]=NULL;
	$therecord["starttime"]=NULL;
	$therecord["enddate"]=NULL;
	$therecord["endtime"]=NULL;

	$therecord["completed"]=false;
	$therecord["completeddate"]=NULL;

	$therecord["assignedtoid"]=NULL;
	$therecord["assignedbyid"]=0;
	$therecord["assignedtodate"]=NULL;
	$therecord["assignedtotime"]=NULL;
	
	$therecord["attachedtabledefid"]=(isset($_GET["reftableid"]))?$_GET["reftableid"]:NULL;
	$therecord["attachedid"]=(isset($_GET["refid"]))?$_GET["refid"]:NULL;
	//form quickview
	if(isset($_GET["cid"])){
		$therecord["attachedtabledefid"]=2;
		$therecord["attachedid"]=$_GET["cid"];
	}

	$therecord["repeat"]=false;
	$therecord["repeatfrequency"]=1;
	$therecord["repeatdays"]=NULL;
	$therecord["repeattype"]="repeatDaily";
	$therecord["repeattimes"]=0;
	$therecord["repeatuntildate"]=NULL;


	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;
	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;

	return $therecord;	
}//end function


function updateRecord($variables,$userid){
//========================================================================================
	global $dblink;
	
	$querystatement="UPDATE notes SET ";
	
			$querystatement.="subject=\"".$variables["subject"]."\", "; 
			$querystatement.="content=\"".$variables["content"]."\", "; 
			$querystatement.="importance=".$variables["importance"].", "; 
			if(isset($variables["thetype"])) $querystatement.="type=\"".$variables["thetype"]."\", "; 
			$querystatement.="category=\"".$variables["category"]."\", "; 
			$querystatement.="location=\"".$variables["location"]."\", "; 
			if($variables["parentid"]=="") $variables["parentid"]="NULL";
			$querystatement.="parentid=".$variables["parentid"].", "; 
			if(isset($variables["private"])) $querystatement.="private=1,"; else $querystatement.="private=0,";

			if(isset($variables["completed"])) {
				$querystatement.="completed=1, "; 
				$querystatement.="completeddate=\"".sqlDateFromString($variables["completeddate"])."\", "; 
			}else {
				$querystatement.="completed=0, completeddate=NULL, ";
			}

			if($variables["enddate"]!="") {
				$querystatement.="enddate=\"".sqlDateFromString($variables["enddate"])."\", "; 
				$querystatement.="endtime=\"".sqlTimeFromString($variables["endtime"])."\", "; 
			} else {
				$querystatement.="enddate=NULL,endtime=NULL, ";
			}

			if($variables["startdate"]!="") {
				$querystatement.="startdate=\"".sqlDateFromString($variables["startdate"]).", "; 
				$querystatement.="starttime=\"".sqlTimeFromString($variables["starttime"]).", "; 
			} else {
				$querystatement.="startdate=NULL,starttime=NULL, ";
			}
			
			if(isset($variables["repeat"])) {
				$querystatement.="`repeat`=1, "; 
				$querystatement.="repeatfrequency=".$variables["repeatfrequency"].", "; 					
				$tempRepeatType="repeat".$variables["repeattype"];
				if($variables["repeattype"]=="Monthly")
					$tempRepeatType.=$variables["rpmo"];
				$querystatement.="repeattype=\"".$tempRepeatType."\",";
				
				$tempRepeatDays="";	
				if($variables["repeattype"]=="Weekly"){
					if(isset($variables["wosc"])) $tempRepeatDays.=$variables["wosc"];
					if(isset($variables["womc"])) $tempRepeatDays.=$variables["womc"];
					if(isset($variables["wotc"])) $tempRepeatDays.=$variables["wotc"];
					if(isset($variables["wowc"])) $tempRepeatDays.=$variables["wowc"];
					if(isset($variables["worc"])) $tempRepeatDays.=$variables["worc"];
					if(isset($variables["wofc"])) $tempRepeatDays.=$variables["wofc"];
					if(isset($variables["woac"])) $tempRepeatDays.=$variables["woac"];
				}
				$querystatement.="repeatdays=\"".$tempRepeatDays."\", ";
				if($variables["rpuntil"]<1) $variables["repeattimes"]=$variables["rpuntil"];
				$querystatement.="repeattimes=".$variables["repeattimes"].", ";
				if($variables["repeattimes"]==-1)
					$querystatement.="repeatuntildate=\"".sqlDateFromString($variables["repeatuntildate"])."\", "; 
				else
					$querystatement.="repeatuntildate=NULL,"; 
					
				$repeatChanges=$tempRepeatDays."*".$variables["repeatfrequency"]."*".$variables["repeattimes"]."*".$tempRepeatType."*".$variables["repeatuntildate"];
			}else {
				$querystatement.="`repeat`=0, repeattimes=0,repeatdays=\"\",repeatfrequency=1,repeattype=\"\",repeatuntildate=NULL, ";
			}

			if($variables["assignedtoid"]=="")$variables["assignedtoid"]="NULL";
			$querystatement.="assignedtoid=".$variables["assignedtoid"].", "; 
			if($variables["assignedtodate"]!="")
				$querystatement.="assignedtodate=\"".sqlDateFromString($variables["assignedtodate"])."\", "; 
			else
				$querystatement.="assignedtodate=NULL, "; 
						
			if($variables["assignedtotime"]!="")
				$querystatement.="assignedtotime=\"".sqlTimeFromString($variables["assignedtotime"])."\", "; 
			else
				$querystatement.="assignedtotime=NULL, "; 
			
			if($variables["assignedtoid"]!=$variables["assignedtochange"]){
				if($variables["assignedtoid"]!="NULL")
					$querystatement.="assignedbyid=".$userid.", "; 
				else
					$querystatement.="assignedbyid=0, "; 
			}
			
			if($variables["attachedtabledefid"]=="") $variables["attachedtabledefid"]=0;
			$querystatement.="attachedtabledefid=".$variables["attachedtabledefid"].", "; 
			if($variables["attachedid"]=="") $variables["attachedid"]=0;
			$querystatement.="attachedid=".$variables["attachedid"].", "; 

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$userid."\" "; 
	$querystatement.="WHERE id=".$variables["id"];
		
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
	
	// update repeat options where applicable.
	if($variables["typeCheck"]=="TS" && (isset($variables["repeat"]) && $variables["parentid"]=="NULL"))
		if(checkForNewerRepeats($variables["id"],$variables["id"]))
			repeatTaskUpdate($variables["id"]);
			
	//repeat task when completed (children only)
	if(isset($variables["completed"]) && $variables["completedChange"]!=1 && $variables["typeCheck"]=="TS" && (isset($variables["repeat"]) || $variables["parentid"]!="NULL")) {
		if(!checkForNewerRepeats($variables["parentid"],$variables["id"])){
			repeatTask($variables["id"]);
		}
	}
}// end function


function insertRecord($variables,$userid){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO notes ";
	
	$querystatement.="(subject,content,importance,type,category,location,parentid,private, ";
	$querystatement.="completed,completeddate, ";
	$querystatement.="enddate,endtime, ";
	$querystatement.="startdate,starttime, ";
	$querystatement.="`repeat`,repeatfrequency,repeattype,repeatdays,repeattimes,repeatuntildate, ";
	$querystatement.="assignedtoid,assignedtodate,assignedtotime,assignedbyid, ";
	$querystatement.="attachedtabledefid,attachedid,";
	$querystatement.="createdby,creationdate,modifiedby) values (";

	$querystatement.="\"".$variables["subject"]."\", "; 
	$querystatement.="\"".$variables["content"]."\", "; 
	$querystatement.=$variables["importance"].", "; 
	$querystatement.="\"".$variables["thetype"]."\", "; 
	$querystatement.="\"".$variables["category"]."\", "; 
	$querystatement.="\"".$variables["location"]."\", "; 
	if($variables["parentid"]=="") $variables["parentid"]="NULL";
	$querystatement.=$variables["parentid"].", "; 
	if(isset($variables["private"])) $querystatement.="1,"; else $querystatement.="0,";

	if(isset($variables["completed"])) {
		$querystatement.="1, "; 
		$querystatement.="\"".sqlDateFromString($variables["completeddate"])."\", "; 
	}else {
		$querystatement.="0, NULL, ";
	}

	if($variables["enddate"]!="") {
		$querystatement.="\"".sqlDateFromString($variables["enddate"])."\", "; 
		$querystatement.="\"".sqlTimeFromString($variables["endtime"])."\", "; 
	} else {
		$querystatement.="NULL,NULL, ";
	}
	if($variables["startdate"]!="") {
		$querystatement.="\"".sqlDateFromString($variables["startdate"])."\", "; 
		$querystatement.="\"".sqlTimeFromString($variables["starttime"])."\", "; 
	} else {
		$querystatement.="NULL,NULL, ";
	}
	
	if(isset($variables["repeat"])) {
		$querystatement.="1, "; 
		$querystatement.=$variables["repeatfrequency"].", "; 
		$tempRepeatType="repeat".$variables["repeattype"];
		if($variables["repeattype"]=="Monthly")
			$tempRepeatType.=$variables["rpmo"];
		$querystatement.="\"".$tempRepeatType."\",";
		
		$tempRepeatDays="";	
		if($variables["repeattype"]=="Weekly"){
			if(isset($variables["wosc"])) $tempRepeatDays.=$variables["wosc"];
			if(isset($variables["womc"])) $tempRepeatDays.=$variables["womc"];
			if(isset($variables["wotc"])) $tempRepeatDays.=$variables["wotc"];
			if(isset($variables["wowc"])) $tempRepeatDays.=$variables["wowc"];
			if(isset($variables["worc"])) $tempRepeatDays.=$variables["worc"];
			if(isset($variables["wofc"])) $tempRepeatDays.=$variables["wofc"];
			if(isset($variables["woac"])) $tempRepeatDays.=$variables["woac"];
		}
		$querystatement.="\"".$tempRepeatDays."\", ";
		if($variables["rpuntil"]<1) $variables["repeattimes"]=$variables["rpuntil"];
		$querystatement.=$variables["repeattimes"].", ";
		if($variables["repeattimes"]==-1)
			$querystatement.="\"".sqlDateFromString($variables["repeatuntildate"])."\", "; 
		else
			$querystatement.="NULL,"; 

	}else {
		$querystatement.="0, 1,\"\",\"\",0,NULL, ";
	}

	if($variables["assignedtoid"]=="")$variables["assignedtoid"]="NULL";
	$querystatement.=$variables["assignedtoid"].", "; 
	if($variables["assignedtodate"]!="")
		$querystatement.="\"".sqlDateFromString($variables["assignedtodate"])."\", "; 
	else
		$querystatement.="NULL, "; 
				
	if($variables["assignedtotime"]!="")
		$querystatement.="\"".sqlTimeFromString($variables["assignedtotime"])."\", "; 
	else
		$querystatement.="NULL, "; 
	$assignedby=0;
	if($variables["assignedtoid"]!=$variables["assignedtochange"])
		if($variables["assignedtoid"]!="NULL")
			$assignedby=$userid;
			
	$querystatement.=$assignedby.",";
	
	if($variables["attachedtabledefid"]=="") $variables["attachedtabledefid"]=0;
	if($variables["attachedid"]=="") $variables["attachedid"]=0;
	$querystatement.=$variables["attachedtabledefid"].", "; 
	$querystatement.=$variables["attachedid"].", "; 
				
	//==== Almost all records should have this =========
	$querystatement.=$userid.", "; 
	$querystatement.="Now(), ";
	$querystatement.=$userid.")"; 
	
	$thequery = mysql_query($querystatement,$dblink);
	if(!$thequery) die ("Insert Failed: ".mysql_error($dblink)." -- ".$querystatement);

	return mysql_insert_id($dblink);
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
			goURL($backurl."#".$theid);
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