<?PHP
// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=6;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$thequerystatement="SELECT id, name, percentage, 

				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				FROM tax
				WHERE id=".$id;		
	$thequery = mysql_query($thequerystatement,$dblink);
	$therecord = mysql_fetch_array($thequery);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	$therecord["id"]=NULL;
	$therecord["name"]=NULL;	
	$therecord["percentage"]=0;
	
	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;
	
	return $therecord;	
}//end function


function updateRecord(){
//========================================================================================
	global $dblink;
	
	$thequerystatement="UPDATE tax SET ";
	
			$thequerystatement.="name=\"".$_POST["name"]."\", "; 
			$thequerystatement.="percentage=\"".$_POST["percentage"]."\", "; 

	//==== Almost all records should have this =========
	$thequerystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$thequerystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($thequerystatement,$dblink);
	if(!$thequery) die ("Update Failed: ".mysql_error()." -- ".$thequerystatement);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

	$thequerystatement="INSERT INTO tax ";
	
	$thequerystatement.="(name,percentage,
						createdby,creationdate,modifiedby) VALUES (";
	
			$thequerystatement.="\"".$_POST["name"]."\", "; 
			$thequerystatement.="\"".$_POST["percentage"]."\", "; 
				
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
	
}
?>