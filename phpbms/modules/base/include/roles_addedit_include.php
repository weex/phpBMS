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

function assignUsers($id,$users,$dblink){
	$querystatement="DELETE FROM rolestousers WHERE roleid=".$id;
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Could not remove existing user roles: ".$querystatement);
	$newusers=explode(",",$users);
	foreach($newusers as $theuser)
		if($theuser!=""){
			$querystatement="INSERT INTO rolestousers (roleid,userid) VALUES(".$id.",".$theuser.")";
			$queryresult=mysql_query($querystatement,$dblink);
			if(!$queryresult) reportError(300,"Could not add new user role.");
		}
}

function displayUsers($id,$type,$dblink){
	$querystatement="SELECT users.id,concat(users.firstname,' ',users.lastname) as name
					FROM users INNER JOIN rolestousers ON rolestousers.userid=users.id 
					WHERE rolestousers.roleid=".$id;
	$assignedquery=mysql_query($querystatement,$dblink);
	if(!$assignedquery) reportError(300,"Could not retrieve assigned users ".mysql_query($dblink));
	$thelist=array();
	
	if($type=="available"){
		$excludelist=array();
		while($therecord=mysql_fetch_array($assignedquery))
			$excludelist[]=$therecord["id"];
			
		$querystatement="SELECT id,concat(users.firstname,' ',users.lastname) as name FROM users WHERE revoked=0 AND portalaccess=0";
		$availablequery=mysql_query($querystatement,$dblink);
		if(!$availablequery) reportError(300,"Could not retrieve available users");
		while($therecord=mysql_fetch_array($availablequery))
			if(!in_array($therecord["id"],$excludelist))
				$thelist[]=$therecord;		
	} else 
		while($therecord=mysql_fetch_array($assignedquery))
			$thelist[]=$therecord;
			
	foreach($thelist as $theoption){
		?>	<option value="<?php echo $theoption["id"]?>"><?php echo htmlQuotes($theoption["name"])?></option>
<?php 
	}
}

// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=200;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT id, name, description, inactive,
	
				createdby, creationdate, 
				modifiedby, modifieddate
				FROM roles
				WHERE id=".$id;		
	$thequery = mysql_query($querystatement,$dblink);
	$therecord = mysql_fetch_array($thequery);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	$therecord["id"]=NULL;

	$therecord["name"]="";
	$therecord["description"]="";
	$therecord["inactive"]=0;

	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;

	return $therecord;	
}//end function


function updateRecord($variables,$userid){
//========================================================================================
	global $dblink;
	
	$querystatement="UPDATE roles SET ";
	
	//fields
	$querystatement.="name=\"".$variables["name"]."\", "; 
	$querystatement.="description=\"".$variables["name"]."\", "; 	

	$querystatement.="inactive=";
	if(isset($variables["inactive"])) $querystatement.="1"; else $querystatement.="0";
	$querystatement.=",";

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$userid."\" "; 
	$querystatement.="where id=".$variables["id"];
	
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);

	if($variables["userschanged"]==1)
		assignUsers($variables["id"],$variables["newusers"],$dblink);

}// end function


function insertRecord($variables,$userid){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO roles ";
	
	$querystatement.="(name,description,inactive,
	createdby,creationdate,modifiedby) VALUES (";
	
	$querystatement.="\"".$variables["name"]."\", "; 
	$querystatement.="\"".$variables["description"]."\", "; 

	if(isset($variables["inactive"])) $querystatement.="1"; else $querystatement.="0";
	$querystatement.=",";
	
	//==== Almost all records should have this =========
	$querystatement.=$userid.", "; 
	$querystatement.="Now(), ";
	$querystatement.=$userid.")"; 
	
	$queryresult= mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Insert Failed: ".mysql_error($dblink)." -- ".$querystatement);
		
	$newid=mysql_insert_id($dblink);

	if($variables["userschanged"]==1)
		assignUsers($newid,$variables["newusers"],$dblink);

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