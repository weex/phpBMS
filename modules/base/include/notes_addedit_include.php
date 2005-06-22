<?PHP
// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=12;
// For notes, we need to track who called you, so that when the save/cancel, it takes them to the appropriate page
if(!isset($_GET["backurl"])) $backurl="../../search.php?id=".$tableid; else $backurl=$_GET["backurl"]."?refid=".$_GET["refid"];

function getAttachedTableDefInfo($id){
	global $dblink;
	
	$querystatement="SELECT displayname,editfile FROM tabledefs WHERE id =".$id;
	$queryresult=mysql_query($querystatement,$dblink) or reportError(100,(mysql_error($dblink)." ".$querystatement));
	$therecord=mysql_fetch_array($queryresult);
	
	return $therecord;	
}

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$thequerystatement="SELECT
				id, subject, assignedtoid, type, content, importance,
				date_Format(followup,\"%c/%e/%Y %T\") as followup, attachedtabledefid, attachedid, beenread,

				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				FROM notes
				WHERE id=".$id;		
	$thequery = mysql_query($thequerystatement,$dblink);
	$therecord = mysql_fetch_array($thequery);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	
	$therecord["subject"]="";
	$therecord["content"]="";
	$therecord["id"]=NULL;
	$therecord["beenread"]=false;
	$therecord["assignedtoid"]=NULL;
	$therecord["followup"]=NULL;
	
	$therecord["type"]=(isset($_GET["reftable"]))?"record":"personal";
	$therecord["attachedtabledefid"]=(isset($_GET["reftableid"]))?$_GET["reftableid"]:NULL;
	$therecord["attachedid"]=(isset($_GET["refid"]))?$_GET["refid"]:NULL;
	$therecord["importance"]="Normal";

	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;

	return $therecord;	
}//end function


function updateRecord(){
//========================================================================================
	global $dblink;
	
	$thequerystatement="UPDATE notes SET ";
	
			$thequerystatement.="importance=\"".$_POST["importance"]."\", "; 

			$thequerystatement.="subject=\"".$_POST["subject"]."\", "; 
			$thequerystatement.="content=\"".$_POST["content"]."\", "; 
		
			$thequerystatement.="type=\"".$_POST["type"]."\", "; 
			$thequerystatement.="assignedtoid=\"".$_POST["assignedtoid"]."\", "; 

			if(!isset($_POST["followup"]))$_POST["followup"]="";
				if($_POST["followup"]=="") $tempdate="NULL";
				else{
					$followup=ereg_replace(",.","/",$_POST["followup"]);
					$temparray=explode(" ",$followup);
					$temptimearray=explode(":",$temparray[1]);
					$tempdatearray=explode("/",$temparray[0]);
					if(strlen($tempdatearray[0])==1) $tempdatearray[0]="0".$tempdatearray[0];
					if(strlen($tempdatearray[1])==1) $tempdatearray[1]="0".$tempdatearray[1];
					$tempdate="\"".$tempdatearray[2].$tempdatearray[0].$tempdatearray[1].$temptimearray[0].$temptimearray[1].$temptimearray[2]."\"";
				}
			$thequerystatement.="followup=".$tempdate.", "; 

			if(isset($_POST["beenread"])) $thequerystatement.="beenread=1, "; else $thequerystatement.="beenread=0, ";

			$thequerystatement.="attachedtabledefid=".$_POST["attachedtabledefid"].", "; 
			$thequerystatement.="attachedid=\"".$_POST["attachedid"]."\", "; 

	//==== Almost all records should have this =========
	$thequerystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$thequerystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($thequerystatement,$dblink);
	if(!$thequery) die ("Update Failed: ".mysql_error()." -- ".$thequerystatement);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

	$thequerystatement="INSERT INTO notes ";
	
			$thequerystatement.="(importance,subject,content,type,assignedtoid,followup,beenread,attachedtabledefid,attachedid,";
			$thequerystatement.="createdby,creationdate,modifiedby) values (";

			$thequerystatement.="\"".$_POST["importance"]."\", "; 

			$thequerystatement.="\"".$_POST["subject"]."\", "; 
			$thequerystatement.="\"".$_POST["content"]."\", "; 
		
			$thequerystatement.="\"".$_POST["type"]."\", "; 
			$thequerystatement.="\"".$_POST["assignedtoid"]."\", "; 
			if(!isset($_POST["followup"]))$_POST["followup"]="";
				if($_POST["followup"]=="") $tempdate="NULL";
				else{
					$followup=ereg_replace(",.","/",$_POST["followup"]);
					$temparray=explode(" ",$followup);
					$temptimearray=explode(":",$temparray[1]);
					$tempdatearray=explode("/",$temparray[0]);
					if(strlen($tempdatearray[0])==1) $tempdatearray[0]="0".$tempdatearray[0];
					if(strlen($tempdatearray[1])==1) $tempdatearray[1]="0".$tempdatearray[1];
					$tempdate="\"".$tempdatearray[2].$tempdatearray[0].$tempdatearray[1].$temptimearray[0].$temptimearray[1].$temptimearray[2]."\"";
				}
			$thequerystatement.=$tempdate.", "; 

			if(isset($_POST["beenread"])) $thequerystatement.="1, "; else $thequerystatement.="0, ";

			$thequerystatement.="\"".$_POST["attachedtabledefid"]."\", "; 
			$thequerystatement.="\"".$_POST["attachedid"]."\", "; 
				
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
			header("Location: ".$backurl."#".$theid);
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