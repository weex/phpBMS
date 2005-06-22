<?PHP
// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=7;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$thequerystatement="SELECT id, name, description, webenabled, webdisplayname,

				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				FROM productcategories
				WHERE id=".$id;		
	$thequery = mysql_query($thequerystatement,$dblink);
	$therecord = mysql_fetch_array($thequery);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	$therecord["id"]=NULL;

	$therecord["name"]="";
	$therecord["webenabled"]=0;
	$therecord["description"]="";
	$therecord["webdisplayname"]="";

	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;

	return $therecord;	
}//end function


function updateRecord(){
//========================================================================================
	global $dblink;
	
	$thequerystatement="UPDATE productcategories SET ";
	
			$thequerystatement.="name=\"".$_POST["name"]."\", "; 
			$thequerystatement.="description=\"".$_POST["description"]."\", "; 
			
			if (!isset($_POST["webenabled"])) $_POST["webenabled"]=0;
			$thequerystatement.="webenabled=".$_POST["webenabled"].", "; 
			$thequerystatement.="webdisplayname=\"".$_POST["webdisplayname"]."\", "; 

	//==== Almost all records should have this =========
	$thequerystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$thequerystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($thequerystatement,$dblink);
	if(!$thequery) die ("Update Failed: ".mysql_error()." -- ".$thequerystatement);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

	$thequerystatement="INSERT INTO productcategories ";
	
	$thequerystatement.="(name,description, webenabled, webdisplayname,
						createdby,creationdate,modifiedby) VALUES (";
	
			$thequerystatement.="\"".$_POST["name"]."\", "; 
			$thequerystatement.="\"".$_POST["description"]."\", "; 

			if (!isset($_POST["webenabled"])) $_POST["webenabled"]=0;
			$thequerystatement.=$_POST["webenabled"].", "; 
			$thequerystatement.="\"".$_POST["webdisplayname"]."\", "; 
				
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