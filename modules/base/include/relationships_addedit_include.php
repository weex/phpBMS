<?PHP
function displayTables($fieldname,$selectedid){
	global $dblink;
	
	$thequerystatement="SELECT id, displayname FROM tabledefs WHERE type!=\"view\" ORDER BY displayname";
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
$tableid=10;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$thequerystatement="SELECT
				id,name,fromtableid,fromfield,totableid,tofield,inherint,

				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				FROM relationships
				WHERE id=".$id;		
	$thequery = mysql_query($thequerystatement,$dblink) or die($thequerystatement);
	$therecord = mysql_fetch_array($thequery);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	$therecord["id"]=NULL;

	$therecord["name"]="";

	$therecord["fromtableid"]=0;
	$therecord["totableid"]=0;
	$therecord["tofield"]="";
	$therecord["fromfield"]="";

	$therecord["inherint"]="";
	
	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;
	
	return $therecord;	
}//end function


function updateRecord(){
//========================================================================================
	global $dblink;
	
	$thequerystatement="UPDATE relationships SET ";
	
			$thequerystatement.="name=\"".$_POST["name"]."\", "; 
			if(isset($_POST["inherint"])) $thequerystatement.="inherint=1, "; else $thequerystatement.="inherint=0, ";

			$thequerystatement.="fromtableid=".$_POST["fromtableid"].", "; 
			$thequerystatement.="fromfield=\"".$_POST["fromfield"]."\", "; 

			$thequerystatement.="totableid=".$_POST["totableid"].", "; 
			$thequerystatement.="tofield=\"".$_POST["tofield"]."\", "; 		



	//==== Almost all records should have this =========
	$thequerystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$thequerystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($thequerystatement,$dblink);
	if(!$thequery) die ("Update Failed: ".mysql_error()." -- ".$thequerystatement);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

	$thequerystatement="INSERT INTO relationships ";
	
	$thequerystatement.="(name,inherint,fromtableid,fromfield,totableid,tofield,
						createdby,creationdate,modifiedby) VALUES (";
	
			$thequerystatement.="\"".$_POST["name"]."\", "; 
			if(isset($_POST["inherint"])) $thequerystatement.="1, "; else $thequerystatement.="0, ";

			$thequerystatement.=$_POST["fromtableid"].", "; 
			$thequerystatement.="\"".$_POST["fromfield"]."\", "; 

			$thequerystatement.=$_POST["totableid"].", "; 
			$thequerystatement.="\"".$_POST["tofield"]."\", "; 		
					
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