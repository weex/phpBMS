<?PHP
function displayTableDropDown($selectedlink){
	global $dblink;
	$querystatement="select id, displayname from tabledefs order by displayname";
	$thequery=mysql_query($querystatement,$dblink);
	
	echo "<select name=\"linkdropdown\">\n";
	echo "<option value=\"0\" ";
	if (substr($selectedlink,0,10)!="search.php") echo "selected";
	echo " >-- typed --</option>\n";
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
	echo "<select name=\"parentid\">\n";
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


function updateRecord(){
//========================================================================================
	global $dblink;
	
	$querystatement="UPDATE menu SET ";
	
	//fields
	$querystatement.="name=\"".$_POST["name"]."\", "; 
	
	if($_POST["linkdropdown"]!="0") $_POST["link"]=$_POST["linkdropdown"];
	$querystatement.="link=\"".$_POST["link"]."\", "; 
	
	if(!$_POST["parentid"])$_POST["parentid"]=0;
	$querystatement.="parentid=".$_POST["parentid"].", "; 
	$querystatement.="displayorder=".$_POST["displayorder"].", "; 
	$querystatement.="accesslevel=".$_POST["accesslevel"].", "; 

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$querystatement.="where id=".$_POST["id"];
	
	$thequery = mysql_query($querystatement,$dblink);
	if(!$thequery) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO menu ";
	
	$querystatement.="(name,link,parentid,displayorder,accesslevel,
	createdby,creationdate,modifiedby) VALUES (";
	
	$querystatement.="\"".$_POST["name"]."\", "; 

	if($_POST["linkdropdown"]!="0") $_POST["link"]=$_POST["linkdropdown"];
	$querystatement.="\"".$_POST["link"]."\", "; 

	if(!$_POST["parentid"])$_POST["parentid"]=0;
	$querystatement.=$_POST["parentid"].", "; 
	$querystatement.=$_POST["displayorder"].", "; 
	$querystatement.=$_POST["accesslevel"].", "; 
	
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