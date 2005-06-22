<?PHP
function displayTables($fieldname,$selectedid){
	global $dblink;
	
	$thequerystatement="SELECT id, displayname FROM tabledefs ORDER BY displayname";
	$thequery=mysql_query($thequerystatement,$dblink);
	
		
	echo "<select name=\"".$fieldname."\">\n";

	echo "<option value=\"0\" ";
	if ($selectedid=="0") echo "selected";
	echo " style=\"font-weight:bold\">global</option>\n";

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
$tableid=16;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$thequerystatement="SELECT
				id,name,type,reportfile,tabledefid,description,displayorder,
				
				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				FROM reports
				WHERE id=".$id;		
	$thequery = mysql_query($thequerystatement,$dblink);
	$therecord = mysql_fetch_array($thequery);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	$therecord["id"]=NULL;

	$therecord["name"]="";
	$therecord["type"]="report";
	$therecord["tabledefid"]=0;
	$therecord["reportfile"]="";
	$therecord["displayorder"]=0;
	$therecord["description"]="";
	
	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;
	
	return $therecord;	
}//end function


function updateRecord(){
//========================================================================================
	global $dblink;
	
	$thequerystatement="UPDATE reports SET ";
	
			$thequerystatement.="name=\"".$_POST["name"]."\", "; 
			$thequerystatement.="type=\"".$_POST["type"]."\", "; 
			$thequerystatement.="tabledefid=".$_POST["tabledefid"].", "; 
			$thequerystatement.="reportfile=\"".$_POST["reportfile"]."\", "; 
			$thequerystatement.="description=\"".$_POST["description"]."\", "; 
			$thequerystatement.="displayorder=".$_POST["displayorder"].", "; 

	//==== Almost all records should have this =========
	$thequerystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$thequerystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($thequerystatement,$dblink);
	if(!$thequery) die ("Update Failed: ".mysql_error()." -- ".$thequerystatement);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

	$thequerystatement="INSERT INTO reports ";
	
	$thequerystatement.="(name,type,tabledefid,reportfile,description,displayorder,
						createdby,creationdate,modifiedby) VALUES (";
	
			$thequerystatement.="\"".$_POST["name"]."\", "; 
			$thequerystatement.="\"".$_POST["type"]."\", "; 
			$thequerystatement.=$_POST["tabledefid"].", "; 
			$thequerystatement.="\"".$_POST["reportfile"]."\", "; 
			$thequerystatement.="\"".$_POST["description"]."\", "; 
			$thequerystatement.=$_POST["displayorder"].", "; 
				
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