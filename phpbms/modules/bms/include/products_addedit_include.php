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
function checkNumberCategories($dblink){
	$querystatement="SELECT count(id) AS thecount FROM productcategories";
	$queryresult=mysql_query($querystatement,$dblink);
	$therecord=mysql_fetch_array($queryresult);
	return $therecord["thecount"];
}

// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=4;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT id, partnumber, partname, description, status, categoryid,type,taxable,inactive,
				unitprice,unitcost,unitofmeasure,weight,isprepackaged,isoversized,packagesperitem,webenabled,
				keywords,thumbnailmime,picturemime,webdescription,memo,upc,

				createdby, creationdate, 
				modifiedby, modifieddate
				FROM products
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
	$therecord["categoryid"]=NULL;
	
	$therecord["inactive"]=0;
	$therecord["taxable"]=1;
	$therecord["type"]="Inventory";
	$therecord["status"]="In Stock";

	$therecord["partnumber"]="";
	$therecord["partname"]="";
	$therecord["upc"]="";
	$therecord["description"]="";

	$therecord["unitofmeasure"]=NULL;
	$therecord["unitprice"]=NULL;
	$therecord["unitcost"]=NULL;

	$therecord["weight"]=NULL;

	$therecord["webenabled"]=NULL;
	$therecord["isprepackaged"]=NULL;
	$therecord["isoversized"]=NULL;
	$therecord["packagesperitem"]=NULL;

	$therecord["keywords"]="";
	$therecord["thumbnailmime"]="";
	$therecord["picturemime"]="";
	$therecord["webdescription"]=NULL;

	$therecord["memo"]="";

	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;
		
	return $therecord;	
}//end function


function updateRecord($variables,$userid){
//========================================================================================
	global $dblink;
	
	$querystatement="UPDATE products SET ";
		if(hasRights(20)){
			$querystatement.="partnumber=\"".$variables["partnumber"]."\", "; 
			$querystatement.="partname=\"".$variables["partname"]."\", "; 
			$querystatement.="upc=\"".$variables["upc"]."\", "; 
			$querystatement.="description=\"".$variables["description"]."\", "; 

			if(isset($variables["inactive"])) $querystatement.="inactive=1, "; else $querystatement.="inactive=0, ";
			if(isset($variables["taxable"])) $querystatement.="taxable=1, "; else $querystatement.="taxable=0, ";
		
				$unitprice=ereg_replace("\\\$|,","",$variables["unitprice"]);
				$unitcost=ereg_replace("\\\$|,","",$variables["unitcost"]);
			
			$querystatement.="unitprice=".$unitprice.", "; 
			$querystatement.="unitcost=".$unitcost.", "; 
			$querystatement.="unitofmeasure=\"".$variables["unitofmeasure"]."\", "; 

			$querystatement.="type=\"".$variables["type"]."\", "; 
			$querystatement.="categoryid=\"".$variables["categoryid"]."\", "; 
		}			

		$querystatement.="memo=\"".$variables["memo"]."\", "; 
		$querystatement.="status=\"".$variables["status"]."\", "; 
		$querystatement.="weight=".$variables["weight"].", "; 
		if(isset($variables["isprepackaged"])) $querystatement.="isprepackaged=1, "; else $querystatement.="isprepackaged=0, ";
		if(isset($variables["isoversized"])) $querystatement.="isoversized=1, "; else $querystatement.="isoversized=0, ";
		if($variables["packagesperitem"])
			$variables["packagesperitem"]=1/$variables["packagesperitem"];
		$querystatement.="packagesperitem=".$variables["packagesperitem"].", "; 

		if(hasRights(20)){
			if(isset($variables["webenabled"])) $querystatement.="webenabled=1, "; else $querystatement.="webenabled=0, ";

			$querystatement.="keywords=\"".$variables["keywords"]."\", "; 
			$querystatement.="webdescription=\"".$variables["webdescription"]."\", "; 

			if($variables["thumbchange"]){
				if($variables["thumbchange"]=="upload"){
					if (function_exists('file_get_contents')) {
                    	$file = addslashes(file_get_contents($_FILES['thumbnailupload']['tmp_name']));
					} else {
	                    // If using PHP < 4.3.0 use the following:
    	                $file = addslashes(fread(fopen($_FILES['thumbnailupload']['tmp_name'], 'r'), filesize($_FILES['thumbnailupload']['tmp_name'])));
					}
					$querystatement.="thumbnail=\"".$file."\", ";
					$querystatement.="thumbnailmime=\"".$_FILES['thumbnailupload']['type']."\", ";
				} else {
					//delete
					$querystatement.="thumbnail=NULL, ";
					$querystatement.="thumbnailmime=NULL, ";
				}
			}
			if($variables["picturechange"]){
				if($variables["picturechange"]=="upload"){
					if (function_exists('file_get_contents')) {
                    	$file = addslashes(file_get_contents($_FILES['pictureupload']['tmp_name']));
					} else {
	                    // If using PHP < 4.3.0 use the following:
    	                $file = addslashes(fread(fopen($_FILES['pictureupload']['tmp_name'], 'r'), filesize($_FILES['pictureupload']['tmp_name'])));
					}
					$querystatement.="picture=\"".$file."\", ";
					$querystatement.="picturemime=\"".$_FILES['pictureupload']['type']."\", ";
				} else {
					//delete
					$querystatement.="picture=NULL, ";
					$querystatement.="picturemime=NULL, ";
				}
			}
		}//end access check

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$userid."\" "; 
	$querystatement.="WHERE id=".$variables["id"];
		
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) {
		$error=mysql_error($dblink);
		if(strpos($error,"Duplicate entry")===false)
			reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
		else
			return "Record not updated: Duplicate part number in another product found";
	}
	return "Record Updated";
}// end function


function insertRecord($variables,$userid){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO products ";
	
	$querystatement.="(partnumber,partname, upc, description, inactive,taxable,memo, unitprice,unitcost,
						unitofmeasure,type,status,categoryid, weight,isprepackaged,isoversized,
						packagesperitem,webenabled,keywords,webdescription,
						thumbnail,thumbnailmime,picture,picturemime,
						createdby,creationdate,modifiedby) VALUES (";
	
			$querystatement.="\"".$variables["partnumber"]."\", "; 
			$querystatement.="\"".$variables["partname"]."\", "; 
			$querystatement.="\"".$variables["upc"]."\", "; 
			$querystatement.="\"".$variables["description"]."\", "; 

			if(isset($variables["inactive"])) $querystatement.="1, "; else $querystatement.="0, ";
			if(isset($variables["taxable"])) $querystatement.="1, "; else $querystatement.="0, ";
			$querystatement.="\"".$variables["memo"]."\", "; 
		
				$unitprice=ereg_replace("\\\$|,","",$variables["unitprice"]);
				$unitcost=ereg_replace("\\\$|,","",$variables["unitcost"]);
			
			$querystatement.=$unitprice.", "; 
			$querystatement.=$unitcost.", "; 
			$querystatement.="\"".$variables["unitofmeasure"]."\", "; 

			$querystatement.="\"".$variables["type"]."\", "; 
			$querystatement.="\"".$variables["status"]."\", "; 
			$querystatement.="\"".$variables["categoryid"]."\", "; 

			$querystatement.=$variables["weight"].", "; 
			if(isset($variables["isprepackaged"])) $querystatement.="1, "; else $querystatement.="0, ";
			if(isset($variables["isoversized"])) $querystatement.="1, "; else $querystatement.="0, ";
			if($variables["packagesperitem"])
				$variables["packagesperitem"]=1/$variables["packagesperitem"];
			$querystatement.=$variables["packagesperitem"].", "; 

			if(isset($variables["webenabled"])) $querystatement.="1, "; else $querystatement.="0, ";
				
			$querystatement.="\"".$variables["keywords"]."\", "; 
			$querystatement.="\"".$variables["webdescription"]."\", "; 

			$file="NULL";
			$mime="NULL";
			if($variables["thumbchange"]){
				if($variables["thumbchange"]=="upload"){
					if (function_exists('file_get_contents')) {
                    	$file = addslashes(file_get_contents($_FILES['thumbnailupload']['tmp_name']));
					} else {
	                    // If using PHP < 4.3.0 use the following:
    	                $file = addslashes(fread(fopen($_FILES['thumbnailupload']['tmp_name'], 'r'), filesize($_FILES['thumbnailupload']['tmp_name'])));
					}
					$file="\"".$file."\"";
					$mime="\"".$_FILES['thumbnailupload']['type']."\"";
				} 
			}
			$querystatement.=$file.", ";
			$querystatement.=$mime.", ";
			
			$file="NULL";
			$mime="NULL";
			if($variables["picturechange"]){
				if($variables["picturechange"]=="upload"){
					if (function_exists('file_get_contents')) {
                    	$file = addslashes(file_get_contents($_FILES['pictureupload']['tmp_name']));
					} else {
	                    // If using PHP < 4.3.0 use the following:
    	                $file = addslashes(fread(fopen($_FILES['pictureupload']['tmp_name'], 'r'), filesize($_FILES['pictureupload']['tmp_name'])));
					}
					$file="\"".$file."\"";
					$mime="\"".$_FILES['pictureupload']['type']."\"";
				}
			}	
			$querystatement.=$file.", ";
			$querystatement.=$mime.", ";

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
				$statusmessage=updateRecord(addSlashesToArray($_POST),$_SESSION["userinfo"]["id"]);
				$theid=$_POST["id"];
				//get record
				$therecord=getRecords($theid);
				$createdby=getUserName($therecord["createdby"]);
				$modifiedby=getUserName($therecord["modifiedby"]);
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