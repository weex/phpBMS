<?PHP
// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=11;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT
				id,maintable,displayname,querytable,addfile,editfile,deletebutton,type,moduleid,
				defaultwhereclause,defaultsortorder,defaultsearchtype,defaultcriteriafindoptions,defaultcriteriaselection,

				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				FROM tabledefs
				WHERE id=".$id;		
	$thequery = mysql_query($querystatement,$dblink);
	$therecord = mysql_fetch_array($thequery);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	$therecord["id"]=NULL;
	
	$therecord["displayname"]="";
	$therecord["maintable"]="";
	$therecord["querytable"]="";
	
	$therecord["addfile"]="";
	$therecord["editfile"]="";
	$therecord["moduleid"]=1;

	$therecord["deletebutton"]="delete";
	$therecord["type"]="table";

	$therecord["defaultwhereclause"]="";
	$therecord["defaultcriteriafindoptions"]="";
	$therecord["defaultcriteriaselection"]="";
	$therecord["defaultsortorder"]="";

	$therecord["defaultsearchtype"]="search";

	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;

	return $therecord;	
}//end function


function updateRecord(){
//========================================================================================
	global $dblink;
	
	$querystatement="UPDATE tabledefs SET ";
	
			$querystatement.="maintable=\"".$_POST["maintable"]."\", "; 
			$querystatement.="querytable=\"".addslashes($_POST["querytable"])."\", "; 
			$querystatement.="displayname=\"".$_POST["displayname"]."\", "; 
			$querystatement.="type=\"".$_POST["type"]."\", "; 
			$querystatement.="moduleid=".$_POST["moduleid"].", "; 

			$querystatement.="addfile=\"".addslashes($_POST["addfile"])."\", "; 
			$querystatement.="editfile=\"".addslashes($_POST["editfile"])."\", "; 

			$querystatement.="deletebutton=\"".$_POST["deletebutton"]."\", "; 

			$querystatement.="defaultwhereclause=\"".addslashes($_POST["defaultwhereclause"])."\", "; 
			$querystatement.="defaultsortorder=\"".addslashes($_POST["defaultsortorder"])."\", "; 
			$querystatement.="defaultsearchtype=\"".$_POST["defaultsearchtype"]."\", "; 
			$querystatement.="defaultcriteriafindoptions=\"".addslashes($_POST["defaultcriteriafindoptions"])."\", "; 
			$querystatement.="defaultcriteriaselection=\"".addslashes($_POST["defaultcriteriaselection"])."\", "; 

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$querystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($querystatement,$dblink);
	if(!$thequery) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO tabledefs ";
	
	$querystatement.="(maintable,querytable,displayname,type,moduleid,addfile,editfile,deletebutton,
						defaultwhereclause,defaultsortorder,defaultsearchtype,defaultcriteriafindoptions,defaultcriteriaselection,
						createdby,creationdate,modifiedby) VALUES (";
	
			$querystatement.="\"".$_POST["maintable"]."\", "; 
			$querystatement.="\"".addslashes($_POST["querytable"])."\", "; 
			$querystatement.="\"".$_POST["displayname"]."\", "; 
			$querystatement.="\"".$_POST["type"]."\", "; 
			$querystatement.=$_POST["moduleid"].", "; 

			$querystatement.="\"".addslashes($_POST["addfile"])."\", "; 
			$querystatement.="\"".addslashes($_POST["editfile"])."\", "; 

			$querystatement.="\"".$_POST["deletebutton"]."\", "; 

			$querystatement.="\"".addslashes($_POST["defaultwhereclause"])."\", "; 
			$querystatement.="\"".addslashes($_POST["defaultsortorder"])."\", "; 
			$querystatement.="\"".$_POST["defaultsearchtype"]."\", "; 
			$querystatement.="\"".addslashes($_POST["defaultcriteriafindoptions"])."\", "; 
			$querystatement.="\"".addslashes($_POST["defaultcriteriaselection"])."\", "; 
				
	//==== Almost all records should have this =========
	$querystatement.=$_SESSION["userinfo"]["id"].", "; 
	$querystatement.="Now(), ";
	$querystatement.=$_SESSION["userinfo"]["id"].")"; 
	
	$thequery = mysql_query($querystatement,$dblink);
	if(!$thequery) die ("Insert Failed: ".mysql_error()." -- ".$querystatement);
	return mysql_insert_id($dblink);
}




//==================================================================
// Process adding, editing, creating new, canceling or updating
//==================================================================
if(!isset($_POST["command"])){
	if(isset($_GET["id"]))
		$therecord=getRecords($_GET["id"]);
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
				updateRecord();
				$theid=$_POST["id"];
				//get record
				$therecord=getRecords($theid);
				$createdby=getUserName($therecord["createdby"]);
				$modifiedby=getUserName($therecord["modifiedby"]);
				$statusmessage="Record Updated";
			}
			else {
				$theid=insertRecord();
				//get record
				$therecord=getRecords($theid);
				$createdby=getUserName($therecord["createdby"]);
				$modifiedby=getUserName($therecord["modifiedby"]);
				$statusmessage="Record Created";
			}
		break;
	}
	
}?>