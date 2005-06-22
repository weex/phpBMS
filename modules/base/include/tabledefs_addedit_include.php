<?PHP
// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=11;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$thequerystatement="SELECT
				id,maintable,displayname,querytable,addfile,editfile,deletebutton,type,moduleid,
				defaultwhereclause,defaultsortorder,defaultsearchtype,defaultcriteriafindoptions,defaultcriteriaselection,

				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				FROM tabledefs
				WHERE id=".$id;		
	$thequery = mysql_query($thequerystatement,$dblink);
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
	
	$thequerystatement="UPDATE tabledefs SET ";
	
			$thequerystatement.="maintable=\"".$_POST["maintable"]."\", "; 
			$thequerystatement.="querytable=\"".$_POST["querytable"]."\", "; 
			$thequerystatement.="displayname=\"".$_POST["displayname"]."\", "; 
			$thequerystatement.="type=\"".$_POST["type"]."\", "; 
			$thequerystatement.="moduleid=".$_POST["moduleid"].", "; 

			$thequerystatement.="addfile=\"".$_POST["addfile"]."\", "; 
			$thequerystatement.="editfile=\"".$_POST["editfile"]."\", "; 

			$thequerystatement.="deletebutton=\"".$_POST["deletebutton"]."\", "; 

			$thequerystatement.="defaultwhereclause=\"".$_POST["defaultwhereclause"]."\", "; 
			$thequerystatement.="defaultsortorder=\"".$_POST["defaultsortorder"]."\", "; 
			$thequerystatement.="defaultsearchtype=\"".$_POST["defaultsearchtype"]."\", "; 
			$thequerystatement.="defaultcriteriafindoptions=\"".$_POST["defaultcriteriafindoptions"]."\", "; 
			$thequerystatement.="defaultcriteriaselection=\"".$_POST["defaultcriteriaselection"]."\", "; 

	//==== Almost all records should have this =========
	$thequerystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$thequerystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($thequerystatement,$dblink);
	if(!$thequery) die ("Update Failed: ".mysql_error()." -- ".$thequerystatement);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

	$thequerystatement="INSERT INTO tabledefs ";
	
	$thequerystatement.="(maintable,querytable,displayname,type,moduleid,addfile,editfile,deletebutton,
						defaultwhereclause,defaultsortorder,defaultsearchtype,defaultcriteriafindoptions,defaultcriteriaselection,
						createdby,creationdate,modifiedby) VALUES (";
	
			$thequerystatement.="\"".$_POST["maintable"]."\", "; 
			$thequerystatement.="\"".$_POST["querytable"]."\", "; 
			$thequerystatement.="\"".$_POST["displayname"]."\", "; 
			$thequerystatement.="\"".$_POST["type"]."\", "; 
			$thequerystatement.=$_POST["moduleid"].", "; 

			$thequerystatement.="\"".$_POST["addfile"]."\", "; 
			$thequerystatement.="\"".$_POST["editfile"]."\", "; 

			$thequerystatement.="\"".$_POST["deletebutton"]."\", "; 

			$thequerystatement.="\"".$_POST["defaultwhereclause"]."\", "; 
			$thequerystatement.="\"".$_POST["defaultsortorder"]."\", "; 
			$thequerystatement.="\"".$_POST["defaultsearchtype"]."\", "; 
			$thequerystatement.="\"".$_POST["defaultcriteriafindoptions"]."\", "; 
			$thequerystatement.="\"".$_POST["defaultcriteriaselection"]."\", "; 
				
	//==== Almost all records should have this =========
	$thequerystatement.=$_SESSION["userinfo"]["id"].", "; 
	$thequerystatement.="Now(), ";
	$thequerystatement.=$_SESSION["userinfo"]["id"].")"; 
	
	$thequery = mysql_query($thequerystatement,$dblink);
	if(!$thequery) die ("Insert Failed: ".mysql_error()." -- ".$thequerystatement);
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