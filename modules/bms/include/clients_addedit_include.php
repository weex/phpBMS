<?PHP
// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=2;
if(!isset($_GET["backurl"])) $backurl="../../search.php?id=".$tableid; else $backurl=$_GET["backurl"]."?refid=".$_GET["refid"];

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$thequerystatement="SELECT id, firstname, lastname, company, city, state, postalcode, country, shiptocountry,
				address1, address2, type, inactive, leadsource, salesmanagerid, homephone, workphone,
				mobilephone, fax, otherphone, shiptoaddress1, shiptoaddress2, shiptocity,shiptostate,
				shiptopostalcode, email, webaddress, comments, paymentmethod, ccnumber, ccexpiration, 

				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				FROM clients
				WHERE id=".$id;		
	$thequery = mysql_query($thequerystatement,$dblink);
	$therecord = mysql_fetch_array($thequery);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	$therecord["id"]=NULL;
	$therecord["type"]="prospects";
	$therecord["inactive"]=0;

	$therecord["lastname"]="";
	$therecord["firstname"]="";
	$therecord["company"]="";

	$therecord["workphone"]="";
	$therecord["homephone"]="";
	$therecord["mobilephone"]="";
	$therecord["fax"]="";
	$therecord["otherphone"]="";
	$therecord["email"]="";
	$therecord["webaddress"]="http://";

	$therecord["comments"]="";

	$therecord["address1"]="";
	$therecord["address2"]="";
	$therecord["city"]="";
	$therecord["state"]="";
	$therecord["postalcode"]="";
	$therecord["country"]="";

	$therecord["shiptoaddress1"]="";
	$therecord["shiptoaddress2"]="";
	$therecord["shiptocity"]="";
	$therecord["shiptostate"]="";
	$therecord["shiptopostalcode"]="";
	$therecord["shiptocountry"]="";
	
	$therecord["salesmanagerid"]=NULL;
	$therecord["leadsource"]="";

	$therecord["paymentmethod"]="";
	$therecord["ccnumber"]="";
	$therecord["ccexpiration"]="";


	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;
	
	return $therecord;	
}//end function


function updateRecord(){
//========================================================================================
	global $dblink;
	
	$thequerystatement="UPDATE clients SET ";
	
			$thequerystatement.="firstname=\"".$_POST["firstname"]."\", "; 
			$thequerystatement.="lastname=\"".$_POST["lastname"]."\", "; 
		
			$thequerystatement.="company=\"".$_POST["company"]."\", "; 

			$thequerystatement.="homephone=\"".$_POST["homephone"]."\", "; 
			$thequerystatement.="workphone=\"".$_POST["workphone"]."\", "; 
			$thequerystatement.="mobilephone=\"".$_POST["mobilephone"]."\", "; 
			$thequerystatement.="fax=\"".$_POST["fax"]."\", "; 
			$thequerystatement.="otherphone=\"".$_POST["otherphone"]."\", "; 

			$thequerystatement.="email=\"".$_POST["email"]."\", "; 
			if ($_POST["webaddress"]=="http://") $thequerystatement.="webaddress=\"\", "; 
			else $thequerystatement.="webaddress=\"".$_POST["webaddress"]."\", "; 

			$thequerystatement.="type=\"".$_POST["type"]."\", "; 
			if(isset($_POST["inactive"])) $thequerystatement.="inactive=1, "; else $thequerystatement.="inactive=0, ";
			$thequerystatement.="salesmanagerid=\"".$_POST["salesmanagerid"]."\", "; 
			$thequerystatement.="leadsource=\"".$_POST["leadsource"]."\", "; 

			$thequerystatement.="address1=\"".$_POST["address1"]."\", "; 
			$thequerystatement.="address2=\"".$_POST["address2"]."\", "; 
			$thequerystatement.="city=\"".$_POST["city"]."\", "; 
			$thequerystatement.="state=\"".$_POST["state"]."\", "; 
			$thequerystatement.="postalcode=\"".$_POST["postalcode"]."\", "; 
			$thequerystatement.="country=\"".$_POST["country"]."\", "; 

			$thequerystatement.="shiptoaddress1=\"".$_POST["shiptoaddress1"]."\", "; 
			$thequerystatement.="shiptoaddress2=\"".$_POST["shiptoaddress2"]."\", "; 
			$thequerystatement.="shiptocity=\"".$_POST["shiptocity"]."\", "; 
			$thequerystatement.="shiptostate=\"".$_POST["shiptostate"]."\", "; 
			$thequerystatement.="shiptopostalcode=\"".$_POST["shiptopostalcode"]."\", "; 
			$thequerystatement.="shiptocountry=\"".$_POST["shiptocountry"]."\", "; 

			$thequerystatement.="paymentmethod=\"".$_POST["paymentmethod"]."\", "; 
			$thequerystatement.="ccnumber=\"".$_POST["ccnumber"]."\", "; 
			$thequerystatement.="ccexpiration=\"".$_POST["ccexpiration"]."\", "; 

			$thequerystatement.="comments=\"".$_POST["comments"]."\", "; 			

	//==== Almost all records should have this =========
	$thequerystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$thequerystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($thequerystatement,$dblink);
	if(!$thequery) die ("Update Failed: ".mysql_error()." -- ".$thequerystatement);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

	$thequerystatement="INSERT INTO clients ";
	
	$thequerystatement.="(firstname,lastname,company,homephone,workphone,mobilephone,fax,otherphone,email,webaddress,";
	$thequerystatement.="type,inactive,salesmanagerid,leadsource,address1,address2,city,state,postalcode,country,shiptoaddress1,";
	$thequerystatement.="shiptoaddress2,shiptocity,shiptostate,shiptopostalcode,shiptocountry,";
	$thequerystatement.="paymentmethod,ccnumber,ccexpiration,comments,
						createdby,creationdate,modifiedby) VALUES (";
	
			$thequerystatement.="\"".$_POST["firstname"]."\", "; 
			$thequerystatement.="\"".$_POST["lastname"]."\", "; 
			$thequerystatement.="\"".$_POST["company"]."\", "; 
		
			$thequerystatement.="\"".$_POST["homephone"]."\", "; 
			$thequerystatement.="\"".$_POST["workphone"]."\", "; 
			$thequerystatement.="\"".$_POST["mobilephone"]."\", "; 
			$thequerystatement.="\"".$_POST["fax"]."\", "; 
			$thequerystatement.="\"".$_POST["otherphone"]."\", "; 

			$thequerystatement.="\"".$_POST["email"]."\", "; 
			if ($_POST["webaddress"]=="http://") $thequerystatement.="\"\", "; 
			else $thequerystatement.="\"".$_POST["webaddress"]."\", "; 

			$thequerystatement.="\"".$_POST["type"]."\", "; 
			if(isset($_POST["inactive"])) $thequerystatement.="1, "; else $thequerystatement.="0, ";

			if(!$_POST["salesmanagerid"]) $_POST["salesmanagerid"]="NULL";
			$thequerystatement.=$_POST["salesmanagerid"].", "; 
			$thequerystatement.="\"".$_POST["leadsource"]."\", "; 

			$thequerystatement.="\"".$_POST["address1"]."\", "; 
			$thequerystatement.="\"".$_POST["address2"]."\", "; 
			$thequerystatement.="\"".$_POST["city"]."\", "; 
			$thequerystatement.="\"".$_POST["state"]."\", "; 
			$thequerystatement.="\"".$_POST["postalcode"]."\", "; 
			$thequerystatement.="\"".$_POST["country"]."\", "; 

			$thequerystatement.="\"".$_POST["shiptoaddress1"]."\", "; 
			$thequerystatement.="\"".$_POST["shiptoaddress2"]."\", "; 
			$thequerystatement.="\"".$_POST["shiptocity"]."\", "; 
			$thequerystatement.="\"".$_POST["shiptostate"]."\", "; 
			$thequerystatement.="\"".$_POST["shiptopostalcode"]."\", "; 
			$thequerystatement.="\"".$_POST["shiptocountry"]."\", "; 

			$thequerystatement.="\"".$_POST["paymentmethod"]."\", "; 
			$thequerystatement.="\"".$_POST["ccnumber"]."\", "; 
			$thequerystatement.="\"".$_POST["ccexpiration"]."\", "; 

			$thequerystatement.="\"".$_POST["comments"]."\", "; 
				
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