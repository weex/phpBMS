<?PHP
if($_SESSION["userinfo"]["accesslevel"]<90) header("Location: ".$_SESSION["app_path"]."noaccess.html");
function getEmailInfo($id){
	global $dblink;
	
	$querystatement="SELECT concat(firstname,\" \",lastname,\" [\",email,\"]\") as name FROM users WHERE id=".$id;
	$queryresult=mysql_query($querystatement,$dblink);
	$therecord=mysql_fetch_array($queryresult);

	return $therecord["name"];
	
}

function showSavedSearch($id){
	global $dblink;
	
	$querystatement="SELECT name FROM usersearches WHERE id=".$id;
	$queryresult=mysql_query($querystatement,$dblink);
	if(mysql_num_rows($queryresult)){
		$therecord=mysql_fetch_array($queryresult);
		return $therecord["name"];
	} else
	return "Saved Search Deleted";
}

// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=22;
if(!isset($_GET["backurl"])) $backurl="../../search.php?id=".$tableid; else $backurl=$_GET["backurl"]."?refid=".$_GET["refid"];

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT id, name,userid,emailto,emailfrom,subject,body,DATE_FORMAT(lastrun,\"%m/%d/%Y %H:%i\")as lastrun
				FROM clientemailprojects
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
	$therecord["userid"]=0;

	$therecord["name"]="";
	$therecord["subject"]="";
	$therecord["emailto"]="selected";
	$therecord["emailfrom"]="";
	$therecord["body"]="";
	$therecord["lastrun"]=NULL;
	
	return $therecord;	
}//end function


function updateRecord($variables,$userid){
//========================================================================================
	global $dblink;
	
	$querystatement="UPDATE clientemailprojects SET ";
	
			if(isset($variables["makeglobal"]))
				$querystatement.="userid=0, "; 
			$querystatement.="name=\"".$_POST["name"]."\" "; 

	//==== Almost all records should have this =========
	$querystatement.="WHERE id=".$variables["id"];
		
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
}// end function


function insertRecord($variables,$userid){
//========================================================================================
	global $dblink;

}



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
				$theid=insertRecord(addSlashesToArray($_POST),$_SESSION["userinfo"]["id"]);
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