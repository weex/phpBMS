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
	
	$querystatement="SELECT id, firstname, lastname, company, city, state, postalcode, country, shiptocountry,
				address1, address2, type, inactive, leadsource, salesmanagerid, homephone, workphone,
				mobilephone, fax, otherphone, shiptoaddress1, shiptoaddress2, shiptocity,shiptostate,
				shiptopostalcode, email, webaddress, comments, paymentmethod, ccnumber, ccexpiration, 

				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				FROM clients
				WHERE id=".$id;		
	$thequery = mysql_query($querystatement,$dblink);
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
	
	$querystatement="UPDATE clients SET ";
	
			$querystatement.="firstname=\"".$_POST["firstname"]."\", "; 
			$querystatement.="lastname=\"".$_POST["lastname"]."\", "; 
		
			$querystatement.="company=\"".$_POST["company"]."\", "; 

			$querystatement.="homephone=\"".$_POST["homephone"]."\", "; 
			$querystatement.="workphone=\"".$_POST["workphone"]."\", "; 
			$querystatement.="mobilephone=\"".$_POST["mobilephone"]."\", "; 
			$querystatement.="fax=\"".$_POST["fax"]."\", "; 
			$querystatement.="otherphone=\"".$_POST["otherphone"]."\", "; 

			$querystatement.="email=\"".$_POST["email"]."\", "; 
			if ($_POST["webaddress"]=="http://") $querystatement.="webaddress=\"\", "; 
			else $querystatement.="webaddress=\"".$_POST["webaddress"]."\", "; 

			$querystatement.="type=\"".$_POST["type"]."\", "; 
			if(isset($_POST["inactive"])) $querystatement.="inactive=1, "; else $querystatement.="inactive=0, ";
			$querystatement.="salesmanagerid=\"".$_POST["salesmanagerid"]."\", "; 
			$querystatement.="leadsource=\"".$_POST["leadsource"]."\", "; 

			$querystatement.="address1=\"".$_POST["address1"]."\", "; 
			$querystatement.="address2=\"".$_POST["address2"]."\", "; 
			$querystatement.="city=\"".$_POST["city"]."\", "; 
			$querystatement.="state=\"".$_POST["state"]."\", "; 
			$querystatement.="postalcode=\"".$_POST["postalcode"]."\", "; 
			$querystatement.="country=\"".$_POST["country"]."\", "; 

			$querystatement.="shiptoaddress1=\"".$_POST["shiptoaddress1"]."\", "; 
			$querystatement.="shiptoaddress2=\"".$_POST["shiptoaddress2"]."\", "; 
			$querystatement.="shiptocity=\"".$_POST["shiptocity"]."\", "; 
			$querystatement.="shiptostate=\"".$_POST["shiptostate"]."\", "; 
			$querystatement.="shiptopostalcode=\"".$_POST["shiptopostalcode"]."\", "; 
			$querystatement.="shiptocountry=\"".$_POST["shiptocountry"]."\", "; 

			$querystatement.="paymentmethod=\"".$_POST["paymentmethod"]."\", "; 
			$querystatement.="ccnumber=\"".$_POST["ccnumber"]."\", "; 
			$querystatement.="ccexpiration=\"".$_POST["ccexpiration"]."\", "; 

			$querystatement.="comments=\"".$_POST["comments"]."\", "; 			

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$querystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($querystatement,$dblink);
	if(!$thequery) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO clients ";
	
	$querystatement.="(firstname,lastname,company,homephone,workphone,mobilephone,fax,otherphone,email,webaddress,";
	$querystatement.="type,inactive,salesmanagerid,leadsource,address1,address2,city,state,postalcode,country,shiptoaddress1,";
	$querystatement.="shiptoaddress2,shiptocity,shiptostate,shiptopostalcode,shiptocountry,";
	$querystatement.="paymentmethod,ccnumber,ccexpiration,comments,
						createdby,creationdate,modifiedby) VALUES (";
	
			$querystatement.="\"".$_POST["firstname"]."\", "; 
			$querystatement.="\"".$_POST["lastname"]."\", "; 
			$querystatement.="\"".$_POST["company"]."\", "; 
		
			$querystatement.="\"".$_POST["homephone"]."\", "; 
			$querystatement.="\"".$_POST["workphone"]."\", "; 
			$querystatement.="\"".$_POST["mobilephone"]."\", "; 
			$querystatement.="\"".$_POST["fax"]."\", "; 
			$querystatement.="\"".$_POST["otherphone"]."\", "; 

			$querystatement.="\"".$_POST["email"]."\", "; 
			if ($_POST["webaddress"]=="http://") $querystatement.="\"\", "; 
			else $querystatement.="\"".$_POST["webaddress"]."\", "; 

			$querystatement.="\"".$_POST["type"]."\", "; 
			if(isset($_POST["inactive"])) $querystatement.="1, "; else $querystatement.="0, ";

			if(!$_POST["salesmanagerid"]) $_POST["salesmanagerid"]="NULL";
			$querystatement.=$_POST["salesmanagerid"].", "; 
			$querystatement.="\"".$_POST["leadsource"]."\", "; 

			$querystatement.="\"".$_POST["address1"]."\", "; 
			$querystatement.="\"".$_POST["address2"]."\", "; 
			$querystatement.="\"".$_POST["city"]."\", "; 
			$querystatement.="\"".$_POST["state"]."\", "; 
			$querystatement.="\"".$_POST["postalcode"]."\", "; 
			$querystatement.="\"".$_POST["country"]."\", "; 

			$querystatement.="\"".$_POST["shiptoaddress1"]."\", "; 
			$querystatement.="\"".$_POST["shiptoaddress2"]."\", "; 
			$querystatement.="\"".$_POST["shiptocity"]."\", "; 
			$querystatement.="\"".$_POST["shiptostate"]."\", "; 
			$querystatement.="\"".$_POST["shiptopostalcode"]."\", "; 
			$querystatement.="\"".$_POST["shiptocountry"]."\", "; 

			$querystatement.="\"".$_POST["paymentmethod"]."\", "; 
			$querystatement.="\"".$_POST["ccnumber"]."\", "; 
			$querystatement.="\"".$_POST["ccexpiration"]."\", "; 

			$querystatement.="\"".$_POST["comments"]."\", "; 
				
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