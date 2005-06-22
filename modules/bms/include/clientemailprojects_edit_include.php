<?PHP
function getEmailInfo($id){
	global $dblink;
	
	$querystatement="SELECT concat(firstname,\" \",lastname,\" [\",email,\"]\") as name FROM users WHERE id=".$id;
	$queryresult=mysql_query($querystatement,$dblink);
	$therecord=mysql_fetch_array($queryresult);

	return $therecord["name"];
	
}

function shoSavedSearch($id){
	global $dblink;
	
	$querystatement="SELECT name FROM usersearches WHERE id=".$id;
	$queryresult=mysql_query($querystatement,$dblink);
	$therecord=mysql_fetch_array($queryresult);

	return $therecord["name"];
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
	
	$thequerystatement="SELECT id, name,userid,emailto,emailfrom,subject,body,DATE_FORMAT(lastrun,\"%m/%d/%Y %H:%i\")as lastrun
				FROM clientemailprojects
				WHERE id=".$id;		
	$thequery = mysql_query($thequerystatement,$dblink);
	$therecord = mysql_fetch_array($thequery);
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


function updateRecord(){
//========================================================================================
	global $dblink;
	
	$thequerystatement="UPDATE clientemailprojects SET ";
	
			if(isset($_POST["makeglobal"]))
				$thequerystatement.="userid=0, "; 
			$thequerystatement.="name=\"".$_POST["name"]."\" "; 

	//==== Almost all records should have this =========
	$thequerystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($thequerystatement,$dblink);
	if(!$thequery) die ("Update Failed: ".mysql_error()." -- ".$thequerystatement);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

}



//==================================================================
// Process adding, editing, creating new, canceling or updating
//==================================================================
if(!isset($_POST["command"])){
	if(isset($_GET["id"]))
		$therecord=getRecords($_GET["id"]);
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
				updateRecord();
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