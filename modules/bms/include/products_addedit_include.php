<?PHP
// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=4;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT id, partnumber, partname, description, status, categoryid,
				unitprice,unitcost,unitofmeasure,weight,isprepackaged,isoversized,packagesperitem,webenabled,

				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				FROM products
				WHERE id=".$id;		
	$thequery = mysql_query($querystatement,$dblink);
	$therecord = mysql_fetch_array($thequery);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	$therecord["id"]=NULL;
	$therecord["categoryid"]=NULL;
	
	$therecord["status"]="In Stock";

	$therecord["partnumber"]="";
	$therecord["partname"]="";
	$therecord["description"]="";

	$therecord["unitofmeasure"]=NULL;
	$therecord["unitprice"]=NULL;
	$therecord["unitcost"]=NULL;

	$therecord["weight"]=NULL;

	$therecord["webenabled"]=NULL;
	$therecord["isprepackaged"]=NULL;
	$therecord["isoversized"]=NULL;
	$therecord["packagesperitem"]=NULL;

	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;
	
	return $therecord;	
}//end function


function updateRecord(){
//========================================================================================
	global $dblink;
	
	$querystatement="UPDATE products SET ";
	
			$querystatement.="partnumber=\"".$_POST["partnumber"]."\", "; 
			$querystatement.="partname=\"".$_POST["partname"]."\", "; 
			$querystatement.="description=\"".$_POST["description"]."\", "; 
		
				$unitprice=ereg_replace("\\\$|,","",$_POST["unitprice"]);
				$unitcost=ereg_replace("\\\$|,","",$_POST["unitcost"]);
			
			$querystatement.="unitprice=".$unitprice.", "; 
			$querystatement.="unitcost=".$unitcost.", "; 
			$querystatement.="unitofmeasure=\"".$_POST["unitofmeasure"]."\", "; 

			$querystatement.="status=\"".$_POST["status"]."\", "; 
			$querystatement.="categoryid=\"".$_POST["categoryid"]."\", "; 

			$querystatement.="weight=".$_POST["weight"].", "; 
			if(isset($_POST["isprepackaged"])) $querystatement.="isprepackaged=1, "; else $querystatement.="isprepackaged=0, ";
			if(isset($_POST["isoversized"])) $querystatement.="isoversized=1, "; else $querystatement.="isoversized=0, ";
			if($_POST["packagesperitem"])
				$_POST["packagesperitem"]=1/$_POST["packagesperitem"];
			$querystatement.="packagesperitem=".$_POST["packagesperitem"].", "; 

			if(isset($_POST["webenabled"])) $querystatement.="webenabled=1, "; else $querystatement.="webenabled=0, ";

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$querystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($querystatement,$dblink);
	if(!$thequery) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

	$querystatement="INSERT INTO products ";
	
	$querystatement.="(partnumber,partname, description, unitprice,unitcost,unitofmeasure,status,categoryid,
						weight,isprepackaged,isoversized,packagesperitem,webenabled,
						createdby,creationdate,modifiedby) VALUES (";
	
			$querystatement.="\"".$_POST["partnumber"]."\", "; 
			$querystatement.="\"".$_POST["partname"]."\", "; 
			$querystatement.="\"".$_POST["description"]."\", "; 
		
				$unitprice=ereg_replace("\\\$|,","",$_POST["unitprice"]);
				$unitcost=ereg_replace("\\\$|,","",$_POST["unitcost"]);
			
			$querystatement.=$unitprice.", "; 
			$querystatement.=$unitcost.", "; 
			$querystatement.="\"".$_POST["unitofmeasure"]."\", "; 

			$querystatement.="\"".$_POST["status"]."\", "; 
			$querystatement.="\"".$_POST["categoryid"]."\", "; 

			$querystatement.=$_POST["weight"].", "; 
			if(isset($_POST["isprepackaged"])) $querystatement.="1, "; else $querystatement.="0, ";
			if(isset($_POST["isoversized"])) $querystatement.="1, "; else $querystatement.="0, ";
			if($_POST["packagesperitem"])
				$_POST["packagesperitem"]=1/$_POST["packagesperitem"];
			$querystatement.=$_POST["packagesperitem"].", "; 

			if(isset($_POST["webenabled"])) $querystatement.="1, "; else $querystatement.="0, ";
				
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