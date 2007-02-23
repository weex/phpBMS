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

function getTotals($id=0){
	global $dblink;
	
	$returnArray["Invoice"]["total"]=0;
	$returnArray["Invoice"]["sum"]=0;
	$returnArray["Order"]["total"]=0;
	$returnArray["Order"]["sum"]=0;
	
	if($id>0){
		$querystatement="SELECT invoices.type,count(invoices.id) as total,sum(discountamount) as sum
						FROM discounts inner join invoices on discounts.id=invoices.discountid 
						WHERE discounts.id=".$id." and (invoices.type=\"Order\" or invoices.type=\"Invoice\") GROUP BY invoices.type";
		$queryresult = mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(300,"Stats Retrieval Failed: ".mysql_error($dblink)." -- ".$querystatement);
		while($therecord=mysql_fetch_array($queryresult)){
			$returnArray[$therecord["type"]]["total"]=$therecord["total"];
			$returnArray[$therecord["type"]]["sum"]=$therecord["sum"];
		}
		
	}
	return $returnArray;
}

// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=25;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT id, name, description, inactive, type, value,

				createdby, creationdate, 
				modifiedby, modifieddate
				FROM discounts
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
	$therecord["type"]="percent";
	$therecord["description"]="";
	$therecord["value"]="0";
	$therecord["inactive"]="0";

	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;

	return $therecord;	
}//end function


function updateRecord($variables,$userid){
//========================================================================================
	global $dblink;
	$querystatement="UPDATE discounts SET ";
	
			$querystatement.="name=\"".$variables["name"]."\", "; 
			$querystatement.="description=\"".$variables["description"]."\", "; 
			
			if (!isset($variables["inactive"])) $variables["inactive"]=0;
			$querystatement.="inactive=".$variables["inactive"].", "; 
			
			$querystatement.="type=\"".$variables["type"]."\", "; 
			if($variables["type"]=="percent"){
				$variables["percentvalue"]=str_replace("%","",$variables["percentvalue"]);
				if($variables["percentvalue"]=="")
					$variables["percentvalue"]="0";
				$querystatement.="value=".$variables["percentvalue"].", "; 
			} else 
				$querystatement.="value=".currencyToNumber($variables["amountvalue"]).", "; 

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$userid."\" "; 
	$querystatement.="WHERE id=".$variables["id"];
		
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
}// end function


function insertRecord($variables,$userid){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO discounts ";
	
	$querystatement.="(name,description, inactive, type, value,
						createdby,creationdate,modifiedby) VALUES (";
	
			$querystatement.="\"".$variables["name"]."\", "; 
			$querystatement.="\"".$variables["description"]."\", "; 
			
			if (!isset($variables["inactive"])) $variables["inactive"]=0;
			$querystatement.=$variables["inactive"].", "; 
			
			$querystatement.="\"".$variables["type"]."\", "; 
			if($variables["type"]=="percent"){
				$variables["percentvalue"]=str_replace("%","",$variables["percentvalue"]);
				if($variables["percentvalue"]=="")
					$variables["percentvalue"]="0";
				$querystatement.=$variables["percentvalue"].", "; 
			} else 
				$querystatement.=currencyToNumber($variables["amountvalue"]).", "; 
				
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
	$stats=getTotals($therecord["id"]);
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
				$stats=getTotals($therecord["id"]);
				$createdby=getUserName($therecord["createdby"]);
				$modifiedby=getUserName($therecord["modifiedby"]);
				$statusmessage="Record Updated";
			}
			else {
				$theid=insertRecord(addSlashesToArray($_POST),$_SESSION["userinfo"]["id"]);
				//get record
				$therecord=getRecords($theid);
				$stats=getTotals($therecord["id"]);
				$createdby=getUserName($therecord["createdby"]);
				$modifiedby=getUserName($therecord["modifiedby"]);
				$statusmessage="<div style=\"float:right;margin-top:-3px;\"><button type=\"button\" class=\"smallButtons\" onclick=\"document.location='".$_SERVER["REQUEST_URI"]."'\">add new</button></div>";
				$statusmessage.="Record Created";
			}
		break;
	}
	
}
?>