<?PHP
// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=9;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="select id, login, firstname, lastname, accesslevel, 
				date_Format(lastlogin,\"%c/%e/%Y %T\") as lastlogin, revoked,
				email,phone,department,employeenumber,

				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				from users
				where id=".$id;		
	$thequery = mysql_query($querystatement,$dblink);
	$therecord = mysql_fetch_array($thequery);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	$therecord["id"]=NULL;
	
	$therecord["lastname"]="";
	$therecord["firstname"]="";
	$therecord["login"]="";
	
	$therecord["email"]="";
	$therecord["phone"]="";
	$therecord["department"]="";
	$therecord["employeenumber"]="";

	$therecord["revoked"]=0;
	$therecord["accesslevel"]=10;
	$therecord["lastlogin"]=NULL;
	
	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;
	
	return $therecord;	
}//end function


function updateRecord(){
//========================================================================================
	global $dblink;
	
	$querystatement="UPDATE users SET ";
	
	$querystatement.="firstname=\"".$_POST["firstname"]."\", "; 
	$querystatement.="lastname=\"".$_POST["lastname"]."\", "; 
	$querystatement.="login=\"".$_POST["login"]."\", "; 

	$querystatement.="email=\"".$_POST["email"]."\", "; 
	$querystatement.="phone=\"".$_POST["phone"]."\", "; 
	$querystatement.="department=\"".$_POST["department"]."\", "; 
	$querystatement.="employeenumber=\"".$_POST["employeenumber"]."\", "; 

	$querystatement.="accesslevel=".$_POST["accesslevel"].", "; 
	if(isset($_POST["revoked"])) $querystatement.="revoked=1, "; else $querystatement.="revoked=0, ";

	if($_POST["password"]) $querystatement.="password=encode(\"".$_POST["password"]."\",\"".$_SESSION["encryption_seed"]."\"), "; 

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$querystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($querystatement,$dblink);
	if(!$thequery) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO users ";
	
	$querystatement.="(login,lastname,firstname,email,phone,department,employeenumber,
						password,accesslevel,revoked,
	createdby,creationdate,modifiedby) VALUES (";
	
	$querystatement.="\"".$_POST["login"]."\", ";
	$querystatement.="\"".$_POST["lastname"]."\", ";
	$querystatement.="\"".$_POST["firstname"]."\", ";
	
	$querystatement.="\"".$_POST["email"]."\", "; 
	$querystatement.="\"".$_POST["phone"]."\", "; 
	$querystatement.="\"".$_POST["department"]."\", "; 
	$querystatement.="\"".$_POST["employeenumber"]."\", "; 	
	
	$querystatement.="encode(\"".$_POST["password"]."\",\"".$_SESSION["encryption_seed"]."\"), "; 
	$querystatement.=$_POST["accesslevel"].", ";
	if(isset($_POST["revoked"])) $querystatement.="1, "; else $querystatement.="0, ";
	
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