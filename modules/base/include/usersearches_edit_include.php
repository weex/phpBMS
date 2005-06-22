<?PHP
function displayTables($fieldname,$selectedid){
	global $dblink;
	
	$thequerystatement="SELECT id, displayname FROM tabledefs ORDER BY displayname";
	$thequery=mysql_query($thequerystatement,$dblink);
	
	echo "<select name=\"".$fieldname."\">\n";
	while($therecord=mysql_fetch_array($thequery)){
		echo "	<option value=\"".$therecord["id"]."\"";
			if($selectedid==$therecord["id"]) echo " selected ";
		echo ">".$therecord["displayname"]."</option>\n";
	}

	echo "</select>\n";
}
// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=17;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$thequerystatement="SELECT
 				id, name, userid, tabledefid, sqlclause, type 
				
 				FROM usersearches
				WHERE id=".$id;		
	$thequery = mysql_query($thequerystatement,$dblink);
	$therecord = mysql_fetch_array($thequery);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	$therecord["id"]=NULL;
	
	$therecord["name"]="";
	$therecord["userid"]=NULL;
	$therecord["tabledefid"]=NULL;
	$therecord["sqlclause"]="";
	$therecord["type"]="";

	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;

	return $therecord;	
}//end function


function updateRecord(){
//========================================================================================
	global $dblink;
	$thequerystatement="UPDATE usersearches SET ";
	
			if(isset($_POST["makeglobal"])) $thequerystatement.="userid=0, "; 
			$thequerystatement.="name=\"".$_POST["name"]."\", "; 
			$thequerystatement.="tabledefid=\"".$_POST["tabledefid"]."\", "; 
			if(isset($_POST["type"]))$thequerystatement.="type=\"".$_POST["type"]."\", "; 
			$thequerystatement.="sqlclause=\"".$_POST["sqlclause"]."\" "; 

	//==== Almost all records should have this =========
	$thequerystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($thequerystatement,$dblink);
	if(!$thequery) die ("Update Failed: ".mysql_error()." -- ".$thequerystatement);
}// end function




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