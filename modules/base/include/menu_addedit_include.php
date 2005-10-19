<?PHP
if($_SESSION["userinfo"]["accesslevel"]<90) header("Location: ".$_SESSION["app_path"]."noaccess.html");

function displayTableDropDown($selectedlink){
	global $dblink;
	$querystatement="select id, displayname from tabledefs order by displayname";
	$thequery=mysql_query($querystatement,$dblink);
	
	echo "<select id=\"linkdropdown\" name=\"linkdropdown\">\n";
	while($therecord=mysql_fetch_array($thequery)){
		echo "<option value=\"search.php?id=".$therecord["id"]."\" ";
		if ($selectedlink=="search.php?id=".$therecord["id"]) echo "selected";
		echo " >".$therecord["displayname"]."</option>\n";
	}
	echo "</select>\n";

}


function displayParentDropDown($selectedpid,$id=0){
	global $dblink;
	if($id=="")$id=0;
	$querystatement="SELECT id, name FROM menu WHERE id!=".$id." and parentid=0 and (link=\"\" or link is null) ORDER BY displayorder";
	$thequery=mysql_query($querystatement,$dblink);
	echo "<select name=\"parentid\" id=\"parentid\">\n";
	echo "<option value=\"0\" ";
	if ($selectedpid=="0") echo "selected";
	echo " >-- none --</option>\n";
	while($therecord=mysql_fetch_array($thequery)){
		echo "<option value=\"".$therecord["id"]."\" ";
		if ($selectedpid==$therecord["id"]) echo "selected";
		echo " >".$therecord["name"]."</option>\n";
	}
	echo "</select>\n";
}


// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=19;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT id, name, link, parentid, displayorder, accesslevel,
				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				FROM menu
				WHERE id=".$id;		
	$thequery = mysql_query($querystatement,$dblink);
	$therecord = mysql_fetch_array($thequery);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	$therecord["id"]=NULL;

	$therecord["name"]="";
	$therecord["link"]=NULL;
	$therecord["parentid"]=0;
	$therecord["displayorder"]=0;
	$therecord["accesslevel"]=0;

	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;

	return $therecord;	
}//end function


function updateRecord($variables,$userid){
//========================================================================================
	global $dblink;
	
	$querystatement="UPDATE menu SET ";
	
	//fields
	$querystatement.="name=\"".$variables["name"]."\", "; 
	
	switch($_POST["radio"]){
		case "cat":
			$querystatement.="link=\"\", "; 
			$querystatement.="parentid=0, "; 			
		break;
		case "search":
			$querystatement.="link=\"".$_POST["linkdropdown"]."\", "; 
			$querystatement.="parentid=".$variables["parentid"].", "; 			
		break;
		case "link":
			$querystatement.="link=\"".$variables["link"]."\", "; 
			$querystatement.="parentid=".$variables["parentid"].", "; 			
		break;
	}
	$querystatement.="displayorder=".$variables["displayorder"].", "; 
	$querystatement.="accesslevel=".$variables["accesslevel"].", "; 

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$userid."\" "; 
	$querystatement.="where id=".$variables["id"];
	
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
}// end function


function insertRecord($variables,$userid){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO menu ";
	
	$querystatement.="(name,link,parentid,displayorder,accesslevel,
	createdby,creationdate,modifiedby) VALUES (";
	
	$querystatement.="\"".$variables["name"]."\", "; 

	switch($_POST["radio"]){
		case "cat":
			$querystatement.="\"\", "; 
			$querystatement.="0, "; 			
		break;
		case "search":
			$querystatement.="\"".$_POST["linkdropdown"]."\", "; 
			$querystatement.=$variables["parentid"].", "; 			
		break;
		case "link":
			$querystatement.="\"".$variables["link"]."\", "; 
			$querystatement.=$variables["parentid"].", "; 			
		break;
	}
	$querystatement.=$variables["displayorder"].", "; 
	$querystatement.=$variables["accesslevel"].", "; 
	
	//==== Almost all records should have this =========
	$querystatement.=$userid.", "; 
	$querystatement.="Now(), ";
	$querystatement.=$userid.")"; 
	
	$queryresult= mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Insert Failed: ".mysql_error($dblink)." -- ".$querystatement);
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
	
}
?>