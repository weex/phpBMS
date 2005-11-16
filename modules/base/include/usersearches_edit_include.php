<?PHP
/*
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
	
	$querystatement="SELECT id, displayname FROM tabledefs ORDER BY displayname";
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
$tableid=17;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT
 				id, name, userid, tabledefid, sqlclause, type, accesslevel 
				
 				FROM usersearches
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
	
	$therecord["name"]="";
	$therecord["userid"]=NULL;
	$therecord["tabledefid"]=NULL;
	$therecord["sqlclause"]="";
	$therecord["accesslevel"]=10;
	$therecord["type"]="";

	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;

	return $therecord;	
}//end function


function updateRecord($variables,$userid){
//========================================================================================
	global $dblink;
	$querystatement="UPDATE usersearches SET ";
	
			if(isset($variables["makeglobal"])) $querystatement.="userid=0, "; 
			$querystatement.="name=\"".$variables["name"]."\", "; 
			$querystatement.="tabledefid=\"".$variables["tabledefid"]."\", "; 
			if(isset($variables["accesslevel"]))$querystatement.="accesslevel=\"".$variables["accesslevel"]."\", "; 
			if(isset($variables["type"]))$querystatement.="type=\"".$variables["type"]."\", "; 
			$querystatement.="sqlclause=\"".$variables["sqlclause"]."\" "; 

	//==== Almost all records should have this =========
	$querystatement.="WHERE id=".$variables["id"];
		
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
}// end function




//==================================================================
// Process adding, editing, creating new, canceling or updating
//==================================================================
if(!isset($_POST["command"])){
	if(isset($_GET["id"]))
		$therecord=getRecords((integer) $_GET["id"]);
	else
		$therecord=setRecordDefaults();
	$username=getUserName($therecord["userid"]);
	if(!$username) $username="global";
	$createdby="";
	$modifiedby="";
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
				$username=getUserName($therecord["userid"]);
				if(!$username) $username="global";
				$createdby="";
				$modifiedby="";
				$statusmessage="Record Updated";
			}
			else {
				$theid=insertRecord();
				//get record
				$therecord=getRecords($theid);
				$username=getUserName($therecord["userid"]);
				if(!$username) $username="global";
				$createdby="";
				$modifiedby="";
				$statusmessage="Record Created";
			}
		break;
	}
	
}
?>