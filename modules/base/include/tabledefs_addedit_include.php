<?PHP
// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================
if($_SESSION["userinfo"]["accesslevel"]<90) header("Location: ".$_SESSION["app_path"]."noaccess.html");

//set table id
$tableid=11;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT
				id,maintable,displayname,querytable,addfile,editfile,deletebutton,type,moduleid,
				defaultwhereclause,defaultsortorder,defaultsearchtype,defaultcriteriafindoptions,defaultcriteriaselection,

				createdby, creationdate, 
				modifiedby, modifieddate
				FROM tabledefs
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


function updateRecord($variables,$userid){
//========================================================================================
	global $dblink;
	
	$querystatement="UPDATE tabledefs SET ";
	
			$querystatement.="maintable=\"".$variables["maintable"]."\", "; 
			$querystatement.="querytable=\"".$variables["querytable"]."\", "; 
			$querystatement.="displayname=\"".$variables["displayname"]."\", "; 
			$querystatement.="type=\"".$variables["type"]."\", "; 
			$querystatement.="moduleid=".$variables["moduleid"].", "; 

			$querystatement.="addfile=\"".$variables["addfile"]."\", "; 
			$querystatement.="editfile=\"".$variables["editfile"]."\", "; 

			$querystatement.="deletebutton=\"".$variables["deletebutton"]."\", "; 

			$querystatement.="defaultwhereclause=\"".$variables["defaultwhereclause"]."\", "; 
			$querystatement.="defaultsortorder=\"".$variables["defaultsortorder"]."\", "; 
			$querystatement.="defaultsearchtype=\"".$variables["defaultsearchtype"]."\", "; 
			$querystatement.="defaultcriteriafindoptions=\"".$variables["defaultcriteriafindoptions"]."\", "; 
			$querystatement.="defaultcriteriaselection=\"".$variables["defaultcriteriaselection"]."\", "; 

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$userid."\" "; 
	$querystatement.="WHERE id=".$variables["id"];
		
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
}// end function


function insertRecord($variables,$userid){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO tabledefs ";
	
	$querystatement.="(maintable,querytable,displayname,type,moduleid,addfile,editfile,deletebutton,
						defaultwhereclause,defaultsortorder,defaultsearchtype,defaultcriteriafindoptions,defaultcriteriaselection,
						createdby,creationdate,modifiedby) VALUES (";
	
			$querystatement.="\"".$variables["maintable"]."\", "; 
			$querystatement.="\"".$variables["querytable"]."\", "; 
			$querystatement.="\"".$variables["displayname"]."\", "; 
			$querystatement.="\"".$variables["type"]."\", "; 
			$querystatement.=$variables["moduleid"].", "; 

			$querystatement.="\"".$variables["addfile"]."\", "; 
			$querystatement.="\"".$variables["editfile"]."\", "; 

			$querystatement.="\"".$variables["deletebutton"]."\", "; 

			$querystatement.="\"".$variables["defaultwhereclause"]."\", "; 
			$querystatement.="\"".$variables["defaultsortorder"]."\", "; 
			$querystatement.="\"".$variables["defaultsearchtype"]."\", "; 
			$querystatement.="\"".$variables["defaultcriteriafindoptions"]."\", "; 
			$querystatement.="\"".$variables["defaultcriteriaselection"]."\", "; 
				
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
				$statusmessage="Record Created";
			}
		break;
	}
	
}?>