<?PHP
function displayTables($fieldname,$selectedid){
	global $dblink;
	
	$querystatement="SELECT id, displayname FROM tabledefs ORDER BY displayname";
	$thequery=mysql_query($querystatement,$dblink);
	
		
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
	
	$querystatement="SELECT
				id,name,type,reportfile,tabledefid,description,displayorder,
				
				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				FROM reports
				WHERE id=".$id;		
	$thequery = mysql_query($querystatement,$dblink);
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
	
	$querystatement="UPDATE reports SET ";
	
			$querystatement.="name=\"".$_POST["name"]."\", "; 
			$querystatement.="type=\"".$_POST["type"]."\", "; 
			$querystatement.="tabledefid=".$_POST["tabledefid"].", "; 
			$querystatement.="reportfile=\"".$_POST["reportfile"]."\", "; 
			$querystatement.="description=\"".$_POST["description"]."\", "; 
			$querystatement.="displayorder=".$_POST["displayorder"].", "; 

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$querystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($querystatement,$dblink);
	if(!$thequery) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO reports ";
	
	$querystatement.="(name,type,tabledefid,reportfile,description,displayorder,
						createdby,creationdate,modifiedby) VALUES (";
	
			$querystatement.="\"".$_POST["name"]."\", "; 
			$querystatement.="\"".$_POST["type"]."\", "; 
			$querystatement.=$_POST["tabledefid"].", "; 
			$querystatement.="\"".$_POST["reportfile"]."\", "; 
			$querystatement.="\"".$_POST["description"]."\", "; 
			$querystatement.=$_POST["displayorder"].", "; 
				
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