<?PHP
function checkForInvoices($id){
	global $dblink;
	$querystatement="SELECT invoices.id FROM clients INNER JOIN invoices ON clients.id=invoices.clientid WHERE clients.id=".$id;
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Check For Invoices Failed: ".mysql_error($dblink)." -- ".$querystatement);	
	if(mysql_num_rows($queryresult))
		return true;
	else
		return false;
}

// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=2;
if(!isset($_GET["backurl"])) 
	$backurl=$_SESSION["app_path"]."search.php?id=".$tableid; 
else{ 
	$backurl=$_GET["backurl"];
	if(isset($_GET["refid"]))
		$backurl.="?refid=".$_GET["refid"];
}

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT id, firstname, lastname, company, city, state, postalcode, country, shiptocountry,
				address1, address2, type, inactive, leadsource, salesmanagerid, homephone, workphone,
				mobilephone, fax, otherphone, shiptoaddress1, shiptoaddress2, shiptocity,shiptostate,
				shiptopostalcode, email, webaddress, comments, paymentmethod, ccnumber, ccexpiration, 
				category, date_Format(becameclient,\"%c/%e/%Y\") as becameclient,

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
	$therecord["type"]="prospect";
	$therecord["category"]="";	
	$therecord["becameclient"]=NULL;
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


function updateRecord($variables,$userid){
//========================================================================================
	global $dblink;
	
	$querystatement="UPDATE clients SET ";
	
			$querystatement.="firstname=\"".$variables["firstname"]."\", "; 
			$querystatement.="lastname=\"".$variables["lastname"]."\", "; 
		
			$querystatement.="company=\"".$variables["company"]."\", "; 

			$querystatement.="homephone=\"".$variables["homephone"]."\", "; 
			$querystatement.="workphone=\"".$variables["workphone"]."\", "; 
			$querystatement.="mobilephone=\"".$variables["mobilephone"]."\", "; 
			$querystatement.="fax=\"".$variables["fax"]."\", "; 
			$querystatement.="otherphone=\"".$variables["otherphone"]."\", "; 

			$querystatement.="email=\"".$variables["email"]."\", "; 
			if ($variables["webaddress"]=="http://") $querystatement.="webaddress=\"\", "; 
			else $querystatement.="webaddress=\"".$variables["webaddress"]."\", "; 

			if(isset($variables["type"])) $querystatement.="type=\"".$variables["type"]."\", "; 
			if($variables["becameclient"]=="" || $variables["becameclient"]=="0/0/0000") $tempdate="NULL";
			else{
				$variables["becameclient"]="/".ereg_replace(",.","/",$variables["becameclient"]);
				$temparray=explode("/",$variables["becameclient"]);
				$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
			}
			$querystatement.="becameclient=".$tempdate.", "; 
			$querystatement.="category=\"".$variables["category"]."\", "; 
			if(isset($variables["inactive"])) $querystatement.="inactive=1, "; else $querystatement.="inactive=0, ";
			$querystatement.="salesmanagerid=\"".$variables["salesmanagerid"]."\", "; 
			$querystatement.="leadsource=\"".$variables["leadsource"]."\", "; 

			$querystatement.="address1=\"".$variables["address1"]."\", "; 
			$querystatement.="address2=\"".$variables["address2"]."\", "; 
			$querystatement.="city=\"".$variables["city"]."\", "; 
			$querystatement.="state=\"".$variables["state"]."\", "; 
			$querystatement.="postalcode=\"".$variables["postalcode"]."\", "; 
			$querystatement.="country=\"".$variables["country"]."\", "; 

			$querystatement.="shiptoaddress1=\"".$variables["shiptoaddress1"]."\", "; 
			$querystatement.="shiptoaddress2=\"".$variables["shiptoaddress2"]."\", "; 
			$querystatement.="shiptocity=\"".$variables["shiptocity"]."\", "; 
			$querystatement.="shiptostate=\"".$variables["shiptostate"]."\", "; 
			$querystatement.="shiptopostalcode=\"".$variables["shiptopostalcode"]."\", "; 
			$querystatement.="shiptocountry=\"".$variables["shiptocountry"]."\", "; 

			$querystatement.="paymentmethod=\"".$variables["paymentmethod"]."\", "; 
			$querystatement.="ccnumber=\"".$variables["ccnumber"]."\", "; 
			$querystatement.="ccexpiration=\"".$variables["ccexpiration"]."\", "; 

			$querystatement.="comments=\"".$variables["comments"]."\", "; 			

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$userid."\" "; 
	$querystatement.="WHERE id=".$variables["id"];
		
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
}// end function


function insertRecord($variables,$userid){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO clients ";
	
	$querystatement.="(firstname,lastname,company,homephone,workphone,mobilephone,fax,otherphone,email,webaddress,";
	$querystatement.="type,becameclient,category,inactive,salesmanagerid,leadsource,address1,address2,city,state,postalcode,country,shiptoaddress1,";
	$querystatement.="shiptoaddress2,shiptocity,shiptostate,shiptopostalcode,shiptocountry,";
	$querystatement.="paymentmethod,ccnumber,ccexpiration,comments,
						createdby,creationdate,modifiedby) VALUES (";
	
			$querystatement.="\"".$variables["firstname"]."\", "; 
			$querystatement.="\"".$variables["lastname"]."\", "; 
			$querystatement.="\"".$variables["company"]."\", "; 
		
			$querystatement.="\"".$variables["homephone"]."\", "; 
			$querystatement.="\"".$variables["workphone"]."\", "; 
			$querystatement.="\"".$variables["mobilephone"]."\", "; 
			$querystatement.="\"".$variables["fax"]."\", "; 
			$querystatement.="\"".$variables["otherphone"]."\", "; 

			$querystatement.="\"".$variables["email"]."\", "; 
			if ($variables["webaddress"]=="http://") $querystatement.="\"\", "; 
			else $querystatement.="\"".$variables["webaddress"]."\", "; 

			$querystatement.="\"".$variables["type"]."\", "; 
			if($variables["becameclient"]=="" || $variables["becameclient"]=="0/0/0000") $tempdate="NULL";
			else{
				$variables["becameclient"]="/".ereg_replace(",.","/",$variables["becameclient"]);
				$temparray=explode("/",$variables["becameclient"]);
				$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
			}
			$querystatement.=$tempdate.", "; 
			$querystatement.="\"".$variables["category"]."\", "; 
			if(isset($variables["inactive"])) $querystatement.="1, "; else $querystatement.="0, ";

			if(!$variables["salesmanagerid"]) $variables["salesmanagerid"]="NULL";
			$querystatement.=$variables["salesmanagerid"].", "; 
			$querystatement.="\"".$variables["leadsource"]."\", "; 

			$querystatement.="\"".$variables["address1"]."\", "; 
			$querystatement.="\"".$variables["address2"]."\", "; 
			$querystatement.="\"".$variables["city"]."\", "; 
			$querystatement.="\"".$variables["state"]."\", "; 
			$querystatement.="\"".$variables["postalcode"]."\", "; 
			$querystatement.="\"".$variables["country"]."\", "; 

			$querystatement.="\"".$variables["shiptoaddress1"]."\", "; 
			$querystatement.="\"".$variables["shiptoaddress2"]."\", "; 
			$querystatement.="\"".$variables["shiptocity"]."\", "; 
			$querystatement.="\"".$variables["shiptostate"]."\", "; 
			$querystatement.="\"".$variables["shiptopostalcode"]."\", "; 
			$querystatement.="\"".$variables["shiptocountry"]."\", "; 

			$querystatement.="\"".$variables["paymentmethod"]."\", "; 
			$querystatement.="\"".$variables["ccnumber"]."\", "; 
			$querystatement.="\"".$variables["ccexpiration"]."\", "; 

			$querystatement.="\"".$variables["comments"]."\", "; 
				
	//==== Almost all records should have this =========
	$querystatement.=$userid.", "; 
	$querystatement.="Now(), ";
	$querystatement.=$userid.")"; 
	
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"insert Failed: ".mysql_error($dblink)." -- ".$querystatement);
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