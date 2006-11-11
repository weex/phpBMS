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
$tableid=9;

function assignRoles($id,$roles,$dblink){
	$querystatement="DELETE FROM rolestousers WHERE userid=".$id;
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Could not remove existing user roles: ".$querystatement);
	$newroles=explode(",",$roles);
	foreach($newroles as $therole)
		if($therole!=""){
			$querystatement="INSERT INTO rolestousers (userid,roleid) VALUES(".$id.",".$therole.")";
			$queryresult=mysql_query($querystatement,$dblink);
			if(!$queryresult) reportError(300,"Could not add new role.");
		}
}

function displayRoles($id,$type,$dblink){
	$querystatement="SELECT roles.id,roles.name
					FROM roles INNER JOIN rolestousers ON rolestousers.roleid=roles.id 
					WHERE rolestousers.userid=".$id;
	$assignedquery=mysql_query($querystatement,$dblink);
	if(!$assignedquery) reportError(300,"Could not retrieve assigned roles ".mysql_query($dblink));
	$thelist=array();
	
	if($type=="available"){
		$excludelist=array();
		while($therecord=mysql_fetch_array($assignedquery))
			$excludelist[]=$therecord["id"];
			
		$querystatement="SELECT id,name FROM roles WHERE inactive=0";
		$availablequery=mysql_query($querystatement,$dblink);
		if(!$availablequery) reportError(300,"Could not retrieve available roles");
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

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="select id, login, firstname, lastname, admin,portalaccess,
				date_Format(lastlogin,\"%c/%e/%Y %T\") as lastlogin, revoked,
				email,phone,department,employeenumber,

				createdby, creationdate, 
				modifiedby, modifieddate
				from users
				where id=".$id;		
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Could not retrieve record: ".mysql_error($dblink)." ".$querystatement));
	$therecord = mysql_fetch_array($queryresult);
	if(!$therecord) reportError(300,"No record for id ".$id);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	$therecord["id"]=NULL;
	
	$therecord["lastname"]="";
	$therecord["firstname"]="";
	$therecord["login"]="";
	
	$therecord["email"]="";
	$therecord["phone"]="";
	$therecord["department"]="";
	$therecord["employeenumber"]="";

	$therecord["revoked"]=0;
	$therecord["admin"]=0;
	$therecord["portalaccess"]=0;
	$therecord["lastlogin"]=NULL;
	
	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;
	
	return $therecord;	
}//end function


function updateRecord($variables,$userid){
//========================================================================================
	global $dblink;
	
	$querystatement="UPDATE users SET ";
	
	$querystatement.="firstname=\"".$variables["firstname"]."\", "; 
	$querystatement.="lastname=\"".$variables["lastname"]."\", "; 
	$querystatement.="login=\"".$variables["login"]."\", "; 

	$querystatement.="email=\"".$variables["email"]."\", "; 
	$querystatement.="phone=\"".$variables["phone"]."\", "; 
	$querystatement.="department=\"".$variables["department"]."\", "; 
	$querystatement.="employeenumber=\"".$variables["employeenumber"]."\", "; 

	if($variables["password"]) $querystatement.="password=encode(\"".$variables["password"]."\",\"".$_SESSION["encryption_seed"]."\"), "; 

	if(isset($variables["revoked"])) $querystatement.="revoked=1, "; else $querystatement.="revoked=0, ";
	if(isset($variables["admin"])) $querystatement.="admin=1, "; else $querystatement.="admin=0, ";
	if(isset($variables["portalaccess"])) $querystatement.="portalaccess=1, "; else $querystatement.="portalaccess=0, ";

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$userid."\" "; 
	$querystatement.="WHERE id=".$variables["id"];
		
	$thequery = mysql_query($querystatement,$dblink);
	if(!$thequery) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
	
	if($variables["roleschanged"]==1)
		assignRoles($variables["id"],$variables["newroles"],$dblink);
}// end function


function insertRecord($variables,$userid){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO users ";
	
	$querystatement.="(login,lastname,firstname,email,phone,department,employeenumber,
						password,revoked,admin,portalaccess,
	createdby,creationdate,modifiedby) VALUES (";
	
	$querystatement.="\"".$variables["login"]."\", ";
	$querystatement.="\"".$variables["lastname"]."\", ";
	$querystatement.="\"".$variables["firstname"]."\", ";
	
	$querystatement.="\"".$variables["email"]."\", "; 
	$querystatement.="\"".$variables["phone"]."\", "; 
	$querystatement.="\"".$variables["department"]."\", "; 
	$querystatement.="\"".$variables["employeenumber"]."\", "; 	
	
	$querystatement.="encode(\"".$variables["password"]."\",\"".$_SESSION["encryption_seed"]."\"), "; 
	if(isset($variables["revoked"])) $querystatement.="1, "; else $querystatement.="0, ";
	if(isset($variables["admin"])) $querystatement.="1, "; else $querystatement.="0, ";
	if(isset($variables["portalaccess"])) $querystatement.="1, "; else $querystatement.="0, ";
	
	//==== Almost all records should have this =========
	$querystatement.=$userid.", "; 
	$querystatement.="Now(), ";
	$querystatement.=$userid.")"; 
	
	$thequery = mysql_query($querystatement,$dblink);
	if(!$thequery) die ("Insert Failed: ".mysql_error($dblink)." -- ".$querystatement);

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