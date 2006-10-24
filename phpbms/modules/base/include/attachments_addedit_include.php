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


// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=27;
if(!isset($_GET["backurl"])) 
	$backurl=$_SESSION["app_path"]."search.php?id=".$tableid; 
else{ 
	$backurl=$_GET["backurl"];
	if(isset($_GET["refid"]))
		$backurl.="?refid=".$_GET["refid"];
}

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT
				files.id,attachments.id as attachmentid,name,description,type,accesslevel,ISNULL(file) as nofile,
				
				attachments.createdby, attachments.creationdate, 
				attachments.modifiedby, attachments.modifieddate
				FROM attachments INNER JOIN files on attachments.fileid=files.id
				WHERE attachments.id=".$id;		
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Could not retrieve record: ".mysql_error($dblink)." ".$querystatement));
	$therecord = mysql_fetch_array($queryresult);
	if(!$therecord) reportError(300,"No record for id ".$id);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	$therecord["id"]=NULL;

	$therecord["attachmentid"]=NULL;
	$therecord["name"]="";
	$therecord["description"]="";
	$therecord["type"]="";
	$therecord["accesslevel"]=0;
	
	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;
	
	return $therecord;	
}//end function


function updateRecord($variables,$userid){
//========================================================================================
	global $dblink;
	
	$querystatement="UPDATE files SET ";

			$name=$variables["name"];
			$querystatement.="description=\"".$variables["description"]."\", "; 
			$querystatement.="accesslevel=".$variables["accesslevel"].", "; 
			if($_FILES['upload']["name"]){
				$name=$_FILES['upload']["name"];
				if (function_exists('file_get_contents')) {
					$file = addslashes(file_get_contents($_FILES['upload']['tmp_name']));
				} else {
					// If using PHP < 4.3.0 use the following:
					$file = addslashes(fread(fopen($_FILES['upload']['tmp_name'], 'r'), filesize($_FILES['thumbnailupload']['tmp_name'])));
				}
				$querystatement.="type=\"".$_FILES['upload']['type']."\", ";
				$querystatement.="file=\"".$file."\", ";
			}
			$querystatement.="name=\"".$name."\", "; 

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$userid."\" "; 
	$querystatement.="WHERE id=".$variables["id"];
		
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
}// end function


function insertRecord($variables,$userid){
//========================================================================================
	global $dblink;

	
	if($variables["newexisting"]=="new"){
		if(!$_FILES['upload']["name"])
			return -1;
		
		if (function_exists('file_get_contents')) {
			$file = addslashes(file_get_contents($_FILES['upload']['tmp_name']));
		} else {
			// If using PHP < 4.3.0 use the following:
			$file = addslashes(fread(fopen($_FILES['upload']['tmp_name'], 'r'), filesize($_FILES['thumbnailupload']['tmp_name'])));
		}

		$querystatement="INSERT INTO files ";
		
		$querystatement.="(name,description,accesslevel,type,file,
							createdby,creationdate,modifiedby) VALUES (";
		
				$querystatement.="\"".$_FILES['upload']["name"]."\", "; 
				$querystatement.="\"".$variables["description"]."\", "; 
				$querystatement.=$variables["accesslevel"].", "; 
				$querystatement.="\"".$_FILES['upload']['type']."\", ";
				$querystatement.="\"".$file."\", ";
					
		//==== Almost all records should have this =========
		$querystatement.=$userid.", "; 
		$querystatement.="Now(), ";
		$querystatement.=$userid.")"; 

		$queryresult = mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(300,"Insert Failed: ".mysql_error($dblink)." -- ".$querystatement);
		
		$variables["fileid"]=mysql_insert_id($dblink);
	}
	$querystatement="INSERT INTO attachments ";	
	$querystatement.="(fileid,tabledefid,recordid,
						createdby,creationdate,modifiedby) VALUES (";
	
		$querystatement.=$variables["fileid"].", "; 
		$querystatement.=$variables["tabledefid"].", "; 
		$querystatement.=$variables["recordid"].", "; 
				
	//==== Almost all records should have this =========
	$querystatement.=$userid.", "; 
	$querystatement.="Now(), ";
	$querystatement.=$userid.")"; 

	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Insert Failed: ".mysql_error($dblink)." -- ".$querystatement);
	
	return mysql_insert_id($dblink);
}




//==================================================================
// Process adding, editing, creating new, canceling or updating
//==================================================================
if(!isset($_POST["command"])){
	if(isset($_GET["id"]))
		$therecord=getRecords((integer) $_GET["id"]);
	else
		$therecord=setRecordDefaults();
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
				$theid=$_POST["attachmentid"];
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
				$statusmessage="Record Created";
			}
		break;
	}
	
}
?>