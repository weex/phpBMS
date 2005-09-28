<?PHP
function displayTables($fieldname,$selectedid){
	global $dblink;
	
	$querystatement="SELECT id, displayname FROM tabledefs WHERE type!=\"view\" ORDER BY displayname";
	$thequery=mysql_query($querystatement,$dblink);
	
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
	
	$querystatement="SELECT
				id,name,fromtableid,fromfield,totableid,tofield,inherint,

				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				FROM relationships
				WHERE id=".$id;		
	$thequery = mysql_query($querystatement,$dblink) or die($querystatement);
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
	
	$querystatement="UPDATE relationships SET ";
	
			$querystatement.="name=\"".$_POST["name"]."\", "; 
			if(isset($_POST["inherint"])) $querystatement.="inherint=1, "; else $querystatement.="inherint=0, ";

			$querystatement.="fromtableid=".$_POST["fromtableid"].", "; 
			$querystatement.="fromfield=\"".$_POST["fromfield"]."\", "; 

			$querystatement.="totableid=".$_POST["totableid"].", "; 
			$querystatement.="tofield=\"".$_POST["tofield"]."\", "; 		



	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$querystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($querystatement,$dblink);
	if(!$thequery) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO relationships ";
	
	$querystatement.="(name,inherint,fromtableid,fromfield,totableid,tofield,
						createdby,creationdate,modifiedby) VALUES (";
	
			$querystatement.="\"".$_POST["name"]."\", "; 
			if(isset($_POST["inherint"])) $querystatement.="1, "; else $querystatement.="0, ";

			$querystatement.=$_POST["fromtableid"].", "; 
			$querystatement.="\"".$_POST["fromfield"]."\", "; 

			$querystatement.=$_POST["totableid"].", "; 
			$querystatement.="\"".$_POST["tofield"]."\", "; 		
					
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