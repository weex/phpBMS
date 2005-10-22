<?PHP
function getTotals($id=0){
	global $dblink;
	
	$returnArray["Invoice"]["total"]=0;
	$returnArray["Invoice"]["sum"]=0;
	$returnArray["Order"]["total"]=0;
	$returnArray["Order"]["sum"]=0;
	
	if($id>0){
		$querystatement="SELECT invoices.type,count(invoices.id) as total,sum(discountamount) as sum
						FROM discounts inner join invoices on discounts.id=invoices.discountid 
						WHERE discounts.id=".$id." and (invoices.type=\"Order\" or invoices.type=\"Invoice\") GROUP BY invoices.type";
		$queryresult = mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(300,"Stats Retrieval Failed: ".mysql_error($dblink)." -- ".$querystatement);
		while($therecord=mysql_fetch_array($queryresult)){
			$returnArray[$therecord["type"]]["total"]=$therecord["total"];
			$returnArray[$therecord["type"]]["sum"]=$therecord["sum"];
		}
		
	}
	return $returnArray;
}

// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=25;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT id, name, description, inactive, type, value,

				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				FROM discounts
				WHERE id=".$id;		
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Select Failed: ".mysql_error($dblink)." -- ".$querystatement);
	$therecord = mysql_fetch_array($queryresult );
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	$therecord["id"]=NULL;

	$therecord["name"]="";
	$therecord["type"]="percent";
	$therecord["description"]="";
	$therecord["value"]="0";
	$therecord["inactive"]="0";

	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;

	return $therecord;	
}//end function


function updateRecord($variables,$userid){
//========================================================================================
	global $dblink;
	$querystatement="UPDATE discounts SET ";
	
			$querystatement.="name=\"".$variables["name"]."\", "; 
			$querystatement.="description=\"".$variables["description"]."\", "; 
			
			if (!isset($variables["inactive"])) $variables["inactive"]=0;
			$querystatement.="inactive=".$variables["inactive"].", "; 
			
			$querystatement.="type=\"".$variables["type"]."\", "; 
			if($variables["type"]=="percent"){
				$variables["percentvalue"]=str_replace("%","",$variables["percentvalue"]);
				if($variables["percentvalue"]=="")
					$variables["percentvalue"]="0";
				$querystatement.="value=".$variables["percentvalue"].", "; 
			} else {
				$amountvalue=ereg_replace("\\\$|,","",$variables["amountvalue"]);
				$querystatement.="value=".$amountvalue.", "; 
			}

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$userid."\" "; 
	$querystatement.="WHERE id=".$variables["id"];
		
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
}// end function


function insertRecord($variables,$userid){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO discounts ";
	
	$querystatement.="(name,description, inactive, type, value,
						createdby,creationdate,modifiedby) VALUES (";
	
			$querystatement.="\"".$variables["name"]."\", "; 
			$querystatement.="\"".$variables["description"]."\", "; 
			
			if (!isset($variables["inactive"])) $variables["inactive"]=0;
			$querystatement.=$variables["inactive"].", "; 
			
			$querystatement.="\"".$variables["type"]."\", "; 
			if($variables["type"]=="percent"){
				$variables["percentvalue"]=str_replace("%","",$variables["percentage"]);
				if($variables["percentvalue"]=="")
					$variables["percentvalue"]="0";
				$querystatement.=$variables["percentvalue"].", "; 
			} else {
				$amountvalue=ereg_replace("\\\$|,","",$variables["amountvalue"]);
				$querystatement.=$amountvalue.", "; 
			}
				
	//==== Almost all records should have this =========
	$querystatement.=$userid.", "; 
	$querystatement.="Now(), ";
	$querystatement.=$userid.")"; 
	
	$queryresult = mysql_query($querystatement,$dblink);
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
	$stats=getTotals($therecord["id"]);
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
				$stats=getTotals($therecord["id"]);
				$createdby=getUserName($therecord["createdby"]);
				$modifiedby=getUserName($therecord["modifiedby"]);
				$statusmessage="Record Updated";
			}
			else {
				$theid=insertRecord(addSlashesToArray($_POST),$_SESSION["userinfo"]["id"]);
				//get record
				$therecord=getRecords($theid);
				$stats=getTotals($therecord["id"]);
				$createdby=getUserName($therecord["createdby"]);
				$modifiedby=getUserName($therecord["modifiedby"]);
				$statusmessage="Record Created";
			}
		break;
	}
	
}
?>