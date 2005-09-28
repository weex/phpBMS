<?PHP
// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=6;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT id, name, percentage, 

				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				FROM tax
				WHERE id=".$id;		
	$thequery = mysql_query($querystatement,$dblink);
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
	
	$querystatement="UPDATE tax SET ";
	
			$querystatement.="name=\"".$_POST["name"]."\", "; 
			$querystatement.="percentage=\"".$_POST["percentage"]."\", "; 

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$querystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($querystatement,$dblink);
	if(!$thequery) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO tax ";
	
	$querystatement.="(name,percentage,
						createdby,creationdate,modifiedby) VALUES (";
	
			$querystatement.="\"".$_POST["name"]."\", "; 
			$querystatement.="\"".$_POST["percentage"]."\", "; 
				
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
	
}
?>