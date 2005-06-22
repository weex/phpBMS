<?PHP
// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=4;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$thequerystatement="SELECT id, partnumber, partname, description, status, categoryid,
				unitprice,unitcost,unitofmeasure,weight,isprepackaged,isoversized,packagesperitem,webenabled,

				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				FROM products
				WHERE id=".$id;		
	$thequery = mysql_query($thequerystatement,$dblink);
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
	
	$thequerystatement="UPDATE products SET ";
	
			$thequerystatement.="partnumber=\"".$_POST["partnumber"]."\", "; 
			$thequerystatement.="partname=\"".$_POST["partname"]."\", "; 
			$thequerystatement.="description=\"".$_POST["description"]."\", "; 
		
				$unitprice=ereg_replace("\\\$|,","",$_POST["unitprice"]);
				$unitcost=ereg_replace("\\\$|,","",$_POST["unitcost"]);
			
			$thequerystatement.="unitprice=".$unitprice.", "; 
			$thequerystatement.="unitcost=".$unitcost.", "; 
			$thequerystatement.="unitofmeasure=\"".$_POST["unitofmeasure"]."\", "; 

			$thequerystatement.="status=\"".$_POST["status"]."\", "; 
			$thequerystatement.="categoryid=\"".$_POST["categoryid"]."\", "; 

			$thequerystatement.="weight=".$_POST["weight"].", "; 
			if(isset($_POST["isprepackaged"])) $thequerystatement.="isprepackaged=1, "; else $thequerystatement.="isprepackaged=0, ";
			if(isset($_POST["isoversized"])) $thequerystatement.="isoversized=1, "; else $thequerystatement.="isoversized=0, ";
			if($_POST["packagesperitem"])
				$_POST["packagesperitem"]=1/$_POST["packagesperitem"];
			$thequerystatement.="packagesperitem=".$_POST["packagesperitem"].", "; 

			if(isset($_POST["webenabled"])) $thequerystatement.="webenabled=1, "; else $thequerystatement.="webenabled=0, ";

	//==== Almost all records should have this =========
	$thequerystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$thequerystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($thequerystatement,$dblink);
	if(!$thequery) die ("Update Failed: ".mysql_error()." -- ".$thequerystatement);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

	$thequerystatement="INSERT INTO products ";
	
	$thequerystatement.="(partnumber,partname, description, unitprice,unitcost,unitofmeasure,status,categoryid,
						weight,isprepackaged,isoversized,packagesperitem,webenabled,
						createdby,creationdate,modifiedby) VALUES (";
	
			$thequerystatement.="\"".$_POST["partnumber"]."\", "; 
			$thequerystatement.="\"".$_POST["partname"]."\", "; 
			$thequerystatement.="\"".$_POST["description"]."\", "; 
		
				$unitprice=ereg_replace("\\\$|,","",$_POST["unitprice"]);
				$unitcost=ereg_replace("\\\$|,","",$_POST["unitcost"]);
			
			$thequerystatement.=$unitprice.", "; 
			$thequerystatement.=$unitcost.", "; 
			$thequerystatement.="\"".$_POST["unitofmeasure"]."\", "; 

			$thequerystatement.="\"".$_POST["status"]."\", "; 
			$thequerystatement.="\"".$_POST["categoryid"]."\", "; 

			$thequerystatement.=$_POST["weight"].", "; 
			if(isset($_POST["isprepackaged"])) $thequerystatement.="1, "; else $thequerystatement.="0, ";
			if(isset($_POST["isoversized"])) $thequerystatement.="1, "; else $thequerystatement.="0, ";
			if($_POST["packagesperitem"])
				$_POST["packagesperitem"]=1/$_POST["packagesperitem"];
			$thequerystatement.=$_POST["packagesperitem"].", "; 

			if(isset($_POST["webenabled"])) $thequerystatement.="1, "; else $thequerystatement.="0, ";
				
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