<?PHP
function getTaxPercentage($taxid){
	if (!is_numeric($taxid)) return 0;
	
	global $dblink;
	
	$querystatement="SELECT percentage FROM tax WHERE id=".$taxid;
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reporError(100,"Could Not Retrieve Tax Percentage");
	if(!mysql_num_rows($queryresult)) return 0;
	$therecord=mysql_fetch_array($queryresult);
	return $therecord["percentage"];
}

function createTempLineItems($invoiceid){
	global $dblink;

	//now construct temporary table
	$thequery="delete from templineitems 
				WHERE (invoiceid=".$invoiceid." and sessionid=\"".session_id()."\") or created < Date_Add(Now(),INTERVAL -45 MINUTE);";
	$thequery = mysql_query($thequery,$dblink);
		
	$thequery="INSERT INTO templineitems (id,invoiceid,productid,unitprice,unitcost,quantity,unitweight,memo,sessionid)
					SELECT id,invoiceid,productid,unitprice,unitcost,quantity,unitweight,memo,\"".session_id()."\" as sessionid from lineitems
					WHERE invoiceid=".$invoiceid.";";
	$thequery = mysql_query($thequery,$dblink);
}

function deleteTempLineItems($invoiceid){
		global $dblink;
		
		if(!$invoiceid) $invoiceid=-2;
	  	$thequerystatement="DELETE FROM templineitems WHERE invoiceid=".$invoiceid." and sessionid=\"".session_id()."\"";
		$thequery=mysql_query($thequerystatement,$dblink);
}

function updateLineItems($invoiceid){
	global $dblink;

	//Now need to delete all line items and re-add them back in		
	$thequerystatement="DELETE FROM lineitems WHERE invoiceid=".$invoiceid.";";
	$thequery = mysql_query($thequerystatement,$dblink);

	$thequerystatement="INSERT INTO lineitems (invoiceid,productid,quantity,unitcost,unitprice,unitweight,memo,
		createdby,creationdate,modifiedby)
		SELECT \"".$invoiceid."\" as invoiceid, productid,quantity,unitcost,unitprice,unitweight,memo,
		\"".$_SESSION["userinfo"]["id"]."\" as createdby, Now() as creationdate,
		\"".$_SESSION["userinfo"]["id"]."\" as modifiedby from templineitems
		where templineitems.invoiceid=".$invoiceid." and sessionid=\"".session_id()."\"
		";
	$thequery = mysql_query($thequerystatement,$dblink);


	$thequerystatement="DELETE FROM templineitems WHERE invoiceid=".$invoiceid." and sessionid=\"".session_id()."\"";
	$theresult=mysql_query($thequerystatement,$dblink);
}//end function

function addLineItems($invoiceid){
	global $dblink;
	
	$thequerystatement="INSERT INTO lineitems (invoiceid,productid,quantity,unitcost,unitprice,unitweight,memo,
		createdby,creationdate,modifiedby)
		SELECT \"".$invoiceid."\" as invoiceid, productid,quantity,unitcost,unitprice,unitweight,memo,
		\"".$_SESSION["userinfo"]["id"]."\" as createdby, Now() as creationdate,
		\"".$_SESSION["userinfo"]["id"]."\" as modifiedby from templineitems
		where templineitems.invoiceid=-2 and sessionid=\"".session_id()."\"
		";
	$thequery = mysql_query($thequerystatement,$dblink);

	$thequerystatement="DELETE FROM templineitems WHERE invoiceid=-2 and sessionid=\"".session_id()."\"";
	$theresult=mysql_query($thequerystatement,$dblink);
}

// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=3;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$thequerystatement="SELECT id, clientid, status, totalweight, totaltni, totalti, totalcost,
					leadsource, shippingmethod, paymentmethod, checkno, bankname, ccnumber,
					ccexpiration, specialinstructions, printedinstructions, tax, shipping,
					address1,address2,city,state,postalcode, country, amountpaid, shipped, trackingno, taxareaid,
					weborder,webconfirmationno,ccverification,
					date_Format(invoicedate,\"%c/%e/%Y\") as invoicedate,
					date_Format(orderdate,\"%c/%e/%Y\") as orderdate,
					date_Format(shippeddate,\"%c/%e/%Y\") as shippeddate,
					ponumber,
					date_Format(requireddate,\"%c/%e/%Y\") as requireddate,
					
				createdby, date_Format(creationdate,\"%c/%e/%Y %T\") as creationdate, 
				modifiedby, date_Format(modifieddate,\"%c/%e/%Y %T\") as modifieddate
				FROM invoices
				WHERE id=".$id;		
	$thequery = mysql_query($thequerystatement,$dblink);
	$therecord = mysql_fetch_array($thequery);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	$therecord["id"]=NULL;
	$therecord["clientid"]=NULL;
	$therecord["status"]="Order";

	$therecord["leadsource"]="";
	$therecord["address1"]="";
	$therecord["address2"]="";
	$therecord["city"]="";
	$therecord["state"]="";
	$therecord["postalcode"]="";
	$therecord["country"]="";

	$therecord["taxareaid"]=NULL;
	$therecord["shippingmethod"]=NULL;
	$therecord["tax"]=0;
	$therecord["taxpercentage"]=0;
	$therecord["totalweight"]=0;
	$therecord["subtotal"]=0;
	$therecord["totaltni"]=0;
	$therecord["totalti"]=0;
	$therecord["shipping"]=0;
	$therecord["totalcost"]=0;

	$therecord["shipped"]=0;
	$therecord["weborder"]=0;
	$therecord["shippeddate"]=NULL;
	$therecord["trackingno"]="";
	$therecord["webconfirmationno"]="";

	$therecord["specialinstructions"]="";

	$therecord["paymentmethod"]="";
	$therecord["ccnumber"]="";
	$therecord["ccexpiration"]="";
	$therecord["ccverification"]="";
	$therecord["checkno"]="";
	$therecord["bankname"]="";

	$therecord["amountpaid"]=0;
	$therecord["amountdue"]=0;

	$therecord["orderdate"]=date("n/d/Y");
	$therecord["invoicedate"]=NULL;
	$therecord["printedinstructions"]=$_SESSION["invoice_default_printinstruc"];
	
	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;
	
	$therecord["taxpercentage"]=0;

	$therecord["ponumber"]="";
	$therecord["requireddate"]=NULL;

	return $therecord;	
}//end function


function updateRecord(){
//========================================================================================
	global $dblink;
	
	if($_POST["status"]=="VOID"){
		$_POST["totaltni"]=0;
		$_POST["totalti"]=0;
		$_POST["amountpaid"]=0;
	}
	$thequerystatement="UPDATE invoices SET ";
	
			$thequerystatement.="clientid=\"".$_POST["clientid"]."\", "; 
			$thequerystatement.="leadsource=\"".$_POST["leadsource"]."\", "; 

			$thequerystatement.="status=\"".$_POST["status"]."\", "; 
			
				if($_POST["orderdate"]=="" || $_POST["orderdate"]=="0/0/0000") $tempdate="NULL";
				else{
					$_POST["orderdate"]="/".ereg_replace(",.","/",$_POST["orderdate"]);
					$temparray=explode("/",$_POST["orderdate"]);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$thequerystatement.="orderdate=".$tempdate.", "; 

				if($_POST["invoicedate"]=="" || $_POST["invoicedate"]=="0/0/0000") $tempdate="NULL";
				else{
					$_POST["invoicedate"]="/".ereg_replace(",.","/",$_POST["invoicedate"]);
					$temparray=explode("/",$_POST["invoicedate"]);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
				//make sure there is an invoice date if the status is set to invoice!!!
				if($_POST["status"]=="Invoice" && $tempdate=="NULL") $tempdate="Now()";
			$thequerystatement.="invoicedate=".$tempdate.", "; 
		
			$thequerystatement.="address1=\"".$_POST["address1"]."\", "; 
			$thequerystatement.="address2=\"".$_POST["address2"]."\", "; 
			$thequerystatement.="city=\"".$_POST["city"]."\", "; 
			$thequerystatement.="state=\"".$_POST["state"]."\", "; 
			$thequerystatement.="postalcode=\"".$_POST["postalcode"]."\", "; 
			$thequerystatement.="country=\"".$_POST["country"]."\", "; 

				$totaltni=ereg_replace("\\\$|,","",$_POST["totaltni"]);
				$totalti=ereg_replace("\\\$|,","",$_POST["totalti"]);
				$shipping=ereg_replace("\\\$|,","",$_POST["shipping"]);
				$tax=ereg_replace("\\\$|,","",$_POST["tax"]);
				$amountpaid=ereg_replace("\\\$|,","",$_POST["amountpaid"]);

			$thequerystatement.="totaltni=".$totaltni.", "; 
			$thequerystatement.="totalti=".$totalti.", "; 
			$thequerystatement.="shipping=".$shipping.", "; 
			$thequerystatement.="tax=".$tax.", "; 
			$thequerystatement.="amountpaid=".$amountpaid.", "; 

			$thequerystatement.="totalcost=".$_POST["totalcost"].", "; 
			$thequerystatement.="totalweight=".$_POST["totalweight"].", "; 

			$thequerystatement.="shippingmethod=\"".$_POST["shippingmethod"]."\", "; 

			if(isset($_POST["shipped"])) {$thequerystatement.="shipped=1, ";} 
				else $thequerystatement.="shipped=0, ";
			if($_POST["shippeddate"]=="" || $_POST["shippeddate"]=="0/0/0000") 
				{$tempdate="NULL";}
			else{
					$shippeddate="/".ereg_replace(",.","/",$_POST["shippeddate"]);
					$temparray=explode("/",$shippeddate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$thequerystatement.="shippeddate=".$tempdate.", "; 
			$thequerystatement.="trackingno=\"".$_POST["trackingno"]."\", "; 

			$thequerystatement.="paymentmethod=\"".$_POST["paymentmethod"]."\", "; 
			$thequerystatement.="checkno=\"".$_POST["checkno"]."\", "; 
			$thequerystatement.="bankname=\"".$_POST["bankname"]."\", "; 
			$thequerystatement.="ccnumber=\"".$_POST["ccnumber"]."\", "; 
			$thequerystatement.="ccexpiration=\"".$_POST["ccexpiration"]."\", "; 
			$thequerystatement.="ccverification=\"".$_POST["ccverification"]."\", "; 

			$thequerystatement.="specialinstructions=\"".$_POST["specialinstructions"]."\", "; 
			$thequerystatement.="printedinstructions=\"".$_POST["printedinstructions"]."\", "; 

			$thequerystatement.="taxareaid=\"".$_POST["taxareaid"]."\", "; 

			$thequerystatement.="ponumber=\"".$_POST["ponumber"]."\", "; 
				if($_POST["requireddate"]=="" || $_POST["requireddate"]=="0/0/0000") $tempdate="NULL";
				else{
					$requireddate="/".ereg_replace(",.","/",$_POST["requireddate"]);
					$temparray=explode("/",$requireddate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$thequerystatement.="requireddate=".$tempdate.", "; 

			if(!isset($_POST["weborder"])) $_POST["weborder"]=0; 
			$thequerystatement.="weborder=".$_POST["weborder"].", "; 
			$thequerystatement.="webconfirmationno=\"".$_POST["webconfirmationno"]."\", "; 

	//==== Almost all records should have this =========
	$thequerystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$thequerystatement.="WHERE id=".$_POST["id"];
		
	$thequery = mysql_query($thequerystatement,$dblink);
	if(!$thequery) die ("Update Failed: ".mysql_error()." -- ".$thequerystatement);
	
	updateLineItems($_POST["id"]);
}// end function


function insertRecord(){
//========================================================================================
	global $dblink;

	if($_POST["status"]=="VOID"){
		$_POST["totaltni"]=0;
		$_POST["totalti"]=0;
		$_POST["amountpaid"]=0;
	}
	$thequerystatement="INSERT INTO invoices 
			(clientid,leadsource,status,orderdate,
			invoicedate,address1,address2,city,state,postalcode, country, totaltni,totalti,shipping,tax,amountpaid,
			totalcost,totalweight,shippingmethod,shipped,shippeddate,trackingno,paymentmethod,
			checkno,bankname,ccnumber,ccexpiration,ccverification,specialinstructions,printedinstructions,
			taxareaid,weborder,webconfirmationno,ponumber,requireddate,
						createdby,creationdate,modifiedby) VALUES (";
	
			$thequerystatement.="\"".$_POST["clientid"]."\", "; 
			$thequerystatement.="\"".$_POST["leadsource"]."\", "; 

			$thequerystatement.="\"".$_POST["status"]."\", "; 
			
				if($_POST["orderdate"]=="" || $_POST["orderdate"]=="0/0/0000") $tempdate="NULL";
				else{
					$orderdate="/".ereg_replace(",.","/",$_POST["orderdate"]);
					$temparray=explode("/",$orderdate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$thequerystatement.=$tempdate.", "; 

				if($_POST["invoicedate"]=="" || $_POST["invoicedate"]=="0/0/0000") $tempdate="NULL";
				else{
					$invoicedate="/".ereg_replace(",.","/",$_POST["invoicedate"]);
					$temparray=explode("/",$invoicedate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
				//make sure there is an invoice date if the status is set to invoice!!!
				if($_POST["status"]=="Invoice" && $tempdate=="NULL") $tempdate="Now()";
			$thequerystatement.=$tempdate.", "; 
		
			$thequerystatement.="\"".$_POST["address1"]."\", "; 
			$thequerystatement.="\"".$_POST["address2"]."\", "; 
			$thequerystatement.="\"".$_POST["city"]."\", "; 
			$thequerystatement.="\"".$_POST["state"]."\", "; 
			$thequerystatement.="\"".$_POST["postalcode"]."\", "; 
			$thequerystatement.="\"".$_POST["country"]."\", "; 

				$totaltni=ereg_replace("\\\$|,","",$_POST["totaltni"]);
				$totalti=ereg_replace("\\\$|,","",$_POST["totalti"]);
				$shipping=ereg_replace("\\\$|,","",$_POST["shipping"]);
				$tax=ereg_replace("\\\$|,","",$_POST["tax"]);
				$amountpaid=ereg_replace("\\\$|,","",$_POST["amountpaid"]);

			$thequerystatement.=$totaltni.", "; 
			$thequerystatement.=$totalti.", "; 
			$thequerystatement.=$shipping.", "; 
			$thequerystatement.=$tax.", "; 
			$thequerystatement.=$amountpaid.", "; 

			$thequerystatement.=$_POST["totalcost"].", "; 
			$thequerystatement.=$_POST["totalweight"].", "; 

			$thequerystatement.="\"".$_POST["shippingmethod"]."\", "; 

			if(isset($_POST["shipped"])) $thequerystatement.="1, "; else $thequerystatement.="0, ";
				if($_POST["shippeddate"]=="" || $_POST["shippeddate"]=="0/0/0000") $tempdate="NULL";
				else{
					$shippeddate="/".ereg_replace(",.","/",$_POST["shippeddate"]);
					$temparray=explode("/",$shippeddate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$thequerystatement.=$tempdate.", "; 
			$thequerystatement.="\"".$_POST["trackingno"]."\", "; 

			$thequerystatement.="\"".$_POST["paymentmethod"]."\", "; 
			$thequerystatement.="\"".$_POST["checkno"]."\", "; 
			$thequerystatement.="\"".$_POST["bankname"]."\", "; 
			$thequerystatement.="\"".$_POST["ccnumber"]."\", "; 
			$thequerystatement.="\"".$_POST["ccexpiration"]."\", "; 
			$thequerystatement.="\"".$_POST["ccverification"]."\", "; 

			$thequerystatement.="\"".$_POST["specialinstructions"]."\", "; 
			$thequerystatement.="\"".$_POST["printedinstructions"]."\", "; 

			$thequerystatement.="\"".$_POST["taxareaid"]."\", "; 

			if(!isset($_POST["weborder"])) $_POST["weborder"]=0; 
			$thequerystatement.=$_POST["weborder"].", "; 
			$thequerystatement.="\"".$_POST["webconfirmationno"]."\", "; 
			$thequerystatement.="\"".$_POST["ponumber"]."\", "; 
				if($_POST["requireddate"]=="" || $_POST["requireddate"]=="0/0/0000") $tempdate="NULL";
				else{
					$requireddate="/".ereg_replace(",.","/",$_POST["requireddate"]);
					$temparray=explode("/",$requireddate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$thequerystatement.=$tempdate.", "; 
				
	//==== Almost all records should have this =========
	$thequerystatement.=$_SESSION["userinfo"]["id"].", "; 
	$thequerystatement.="Now(), ";
	$thequerystatement.=$_SESSION["userinfo"]["id"].")"; 
	
	$thequery = mysql_query($thequerystatement,$dblink);
	if(!$thequery) die ("Insert Failed: ".mysql_error()." -- ".$thequerystatement);

	$newid= mysql_insert_id($dblink);
	addLineItems($newid);
	
	return $newid;
}


//==================================================================
// Process adding, editing, creating new, canceling or updating
//==================================================================

if(!isset($_POST["command"])){
	if(isset($_GET["id"])){
		$therecord=getRecords($_GET["id"]);
		//invoice specific, create temp line items
		createTempLineItems($_GET["id"]);
		$therecord["taxpercentage"]=getTaxPercentage($therecord["taxareaid"]);
	}
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
			deleteTempLineItems($_POST["id"]);
			$theid=$_POST["id"];
			header("Location: ../../search.php?id=".$tableid."#".$theid);
		break;
		case "save":
			if($_POST["id"]) {
				updateRecord();
				$theid=$_POST["id"];
				//get record
				$therecord=getRecords($theid);
				createTempLineItems($theid);
				$therecord["taxpercentage"]=getTaxPercentage($therecord["taxareaid"]);
				$createdby=getUserName($therecord["createdby"]);
				$modifiedby=getUserName($therecord["modifiedby"]);
				$statusmessage="Record Updated";
			}
			else {
				$theid=insertRecord();
				//get record
				$therecord=getRecords($theid);
				createTempLineItems($theid);
				$therecord["taxpercentage"]=getTaxPercentage($therecord["taxareaid"]);
				$createdby=getUserName($therecord["createdby"]);
				$modifiedby=getUserName($therecord["modifiedby"]);
				$statusmessage="Record Created";
			}
		break;
	}
}
?>