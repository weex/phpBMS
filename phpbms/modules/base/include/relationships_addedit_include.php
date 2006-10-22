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

function displayTables($fieldname,$selectedid){
	global $dblink;
	
	$querystatement="SELECT id, displayname FROM tabledefs WHERE type!=\"view\" ORDER BY displayname";
	$thequery=mysql_query($querystatement,$dblink);
	
	echo "<select id=\"".$fieldname."\" name=\"".$fieldname."\">\n";
	while($therecord=mysql_fetch_array($thequery)){
		echo "	<option value=\"".$therecord["id"]."\"";
			if($selectedid==$therecord["id"]) echo " selected ";
		echo ">".$therecord["displayname"]."</option>\n";
	}

	echo "</select>\n";
}

// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=10;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT
				id,name,fromtableid,fromfield,totableid,tofield,inherint,

				createdby, creationdate, 
				modifiedby, modifieddate
				FROM relationships
				WHERE id=".$id;		
	$thequery = mysql_query($querystatement,$dblink) or die($querystatement);
	$therecord = mysql_fetch_array($thequery);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	$therecord["id"]=NULL;

	$therecord["name"]="";

	$therecord["fromtableid"]=0;
	$therecord["totableid"]=0;
	$therecord["tofield"]="";
	$therecord["fromfield"]="";

	$therecord["inherint"]="";
	
	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;
	
	return $therecord;	
}//end function


function updateRecord($variables,$userid){
//========================================================================================
	global $dblink;
	
	$querystatement="UPDATE relationships SET ";
	
			$querystatement.="name=\"".$variables["name"]."\", "; 
			if(isset($variables["inherint"])) $querystatement.="inherint=1, "; else $querystatement.="inherint=0, ";

			$querystatement.="fromtableid=".$variables["fromtableid"].", "; 
			$querystatement.="fromfield=\"".$variables["fromfield"]."\", "; 

			$querystatement.="totableid=".$variables["totableid"].", "; 
			$querystatement.="tofield=\"".$variables["tofield"]."\", "; 		



	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$userid."\" "; 
	$querystatement.="WHERE id=".$variables["id"];
		
	$thequery = mysql_query($querystatement,$dblink);
	if(!$thequery) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
}// end function


function insertRecord($variables,$userid){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO relationships ";
	
	$querystatement.="(name,inherint,fromtableid,fromfield,totableid,tofield,
						createdby,creationdate,modifiedby) VALUES (";
	
			$querystatement.="\"".$variables["name"]."\", "; 
			if(isset($variables["inherint"])) $querystatement.="1, "; else $querystatement.="0, ";

			$querystatement.=$variables["fromtableid"].", "; 
			$querystatement.="\"".$variables["fromfield"]."\", "; 

			$querystatement.=$variables["totableid"].", "; 
			$querystatement.="\"".$variables["tofield"]."\", "; 		
					
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
			header("Location: ../../search.php?id=".$tableid."#".$theid);
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