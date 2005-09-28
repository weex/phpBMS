<?PHP
function getTaxPercentage($taxid){
	if (!is_numeric($taxid)) return 0;
	
	global $dblink;
	
	$querystatement="SELECT percentage FROM tax WHERE id=".$taxid;
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,"Could Not Retrieve Tax Percentage");
	if(!mysql_num_rows($queryresult)) return 0;
	$therecord=mysql_fetch_array($queryresult);
	return $therecord["percentage"];
}

function createTempLineItems($invoiceid){
	global $dblink;

	//now construct temporary table
	$querystatement="delete from templineitems 
				WHERE (invoiceid=".$invoiceid." and sessionid=\"".session_id()."\") or created < Date_Add(Now(),INTERVAL -45 MINUTE);";
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("CTLI: Temp Line Items Delete Failed: ".mysql_error($dblink)." -- ".$querystatement));
		
	$querystatement="INSERT INTO templineitems (id,invoiceid,productid,unitprice,unitcost,quantity,unitweight,memo,sessionid)
					SELECT id,invoiceid,productid,unitprice,unitcost,quantity,unitweight,memo,\"".session_id()."\" as sessionid from lineitems
					WHERE invoiceid=".$invoiceid.";";
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("CTLI: Temp Line Items Creation Failed: ".mysql_error($dblink)." -- ".$querystatement));
}

function deleteTempLineItems($invoiceid){
		global $dblink;
		
		if(!$invoiceid) $invoiceid=-2;
	  	$querystatement="DELETE FROM templineitems WHERE invoiceid=".$invoiceid." and sessionid=\"".session_id()."\"";
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(100,("Temp Line Items Deleteion Failed: ".mysql_error($dblink)." -- ".$querystatement));

}

function updateLineItems($invoiceid){
	global $dblink;

	//Now need to delete all line items and re-add them back in		
	$querystatement="DELETE FROM lineitems WHERE invoiceid=".$invoiceid.";";
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Line Items Deleteion Failed: ".mysql_error($dblink)." -- ".$querystatement));

	$querystatement="INSERT INTO lineitems (invoiceid,productid,quantity,unitcost,unitprice,unitweight,memo,
		createdby,creationdate,modifiedby)
		SELECT \"".$invoiceid."\" as invoiceid, productid,quantity,unitcost,unitprice,unitweight,memo,
		\"".$_SESSION["userinfo"]["id"]."\" as createdby, Now() as creationdate,
		\"".$_SESSION["userinfo"]["id"]."\" as modifiedby from templineitems
		where templineitems.invoiceid=".$invoiceid." and sessionid=\"".session_id()."\"
		";
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Line Items Insertion Failed: ".mysql_error($dblink)." -- ".$querystatement));


	$querystatement="DELETE FROM templineitems WHERE invoiceid=".$invoiceid." and sessionid=\"".session_id()."\"";
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Temp Line Items Deleteion Failed: ".mysql_error($dblink)." -- ".$querystatement));
	
}//end function

function addLineItems($invoiceid){
	global $dblink;
	
	$querystatement="INSERT INTO lineitems (invoiceid,productid,quantity,unitcost,unitprice,unitweight,memo,
		createdby,creationdate,modifiedby)
		SELECT \"".$invoiceid."\" as invoiceid, productid,quantity,unitcost,unitprice,unitweight,memo,
		\"".$_SESSION["userinfo"]["id"]."\" as createdby, Now() as creationdate,
		\"".$_SESSION["userinfo"]["id"]."\" as modifiedby from templineitems
		where templineitems.invoiceid=-2 and sessionid=\"".session_id()."\"
		";
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Line Items Insertion Failed: ".mysql_error($dblink)." -- ".$querystatement));

	$querystatement="DELETE FROM templineitems WHERE invoiceid=-2 and sessionid=\"".session_id()."\"";
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Temp Line Items Deleteion Failed: ".mysql_error($dblink)." -- ".$querystatement));
}

// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=3;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT id, clientid, status, totalweight, totaltni, totalti, totalcost,
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
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Get Invoice Record Failed: ".mysql_error($dblink)." -- ".$querystatement));
	$therecord = mysql_fetch_array($queryresult);
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
	$querystatement="UPDATE invoices SET ";
	
			$querystatement.="clientid=\"".$_POST["clientid"]."\", "; 
			$querystatement.="leadsource=\"".$_POST["leadsource"]."\", "; 

			$querystatement.="status=\"".$_POST["status"]."\", "; 
			
				if($_POST["orderdate"]=="" || $_POST["orderdate"]=="0/0/0000") $tempdate="NULL";
				else{
					$_POST["orderdate"]="/".ereg_replace(",.","/",$_POST["orderdate"]);
					$temparray=explode("/",$_POST["orderdate"]);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$querystatement.="orderdate=".$tempdate.", "; 

				if($_POST["invoicedate"]=="" || $_POST["invoicedate"]=="0/0/0000") $tempdate="NULL";
				else{
					$_POST["invoicedate"]="/".ereg_replace(",.","/",$_POST["invoicedate"]);
					$temparray=explode("/",$_POST["invoicedate"]);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
				//make sure there is an invoice date if the status is set to invoice!!!
				if($_POST["status"]=="Invoice" && $tempdate=="NULL") $tempdate="Now()";
			$querystatement.="invoicedate=".$tempdate.", "; 
		
			$querystatement.="address1=\"".$_POST["address1"]."\", "; 
			$querystatement.="address2=\"".$_POST["address2"]."\", "; 
			$querystatement.="city=\"".$_POST["city"]."\", "; 
			$querystatement.="state=\"".$_POST["state"]."\", "; 
			$querystatement.="postalcode=\"".$_POST["postalcode"]."\", "; 
			$querystatement.="country=\"".$_POST["country"]."\", "; 

				$totaltni=ereg_replace("\\\$|,","",$_POST["totaltni"]);
				$totalti=ereg_replace("\\\$|,","",$_POST["totalti"]);
				$shipping=ereg_replace("\\\$|,","",$_POST["shipping"]);
				$tax=ereg_replace("\\\$|,","",$_POST["tax"]);
				$amountpaid=ereg_replace("\\\$|,","",$_POST["amountpaid"]);

			$querystatement.="totaltni=".$totaltni.", "; 
			$querystatement.="totalti=".$totalti.", "; 
			$querystatement.="shipping=".$shipping.", "; 
			$querystatement.="tax=".$tax.", "; 
			$querystatement.="amountpaid=".$amountpaid.", "; 

			$querystatement.="totalcost=".$_POST["totalcost"].", "; 
			$querystatement.="totalweight=".$_POST["totalweight"].", "; 

			$querystatement.="shippingmethod=\"".$_POST["shippingmethod"]."\", "; 

			if(isset($_POST["shipped"]) || ($_POST["shippeddate"]!="" && $_POST["shippeddate"]=="0/0/0000")) {$querystatement.="shipped=1, ";} 
				else $querystatement.="shipped=0, ";
			if($_POST["shippeddate"]=="" || $_POST["shippeddate"]=="0/0/0000") 
				{$tempdate="NULL";}
			else{
					$shippeddate="/".ereg_replace(",.","/",$_POST["shippeddate"]);
					$temparray=explode("/",$shippeddate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$querystatement.="shippeddate=".$tempdate.", "; 
			$querystatement.="trackingno=\"".$_POST["trackingno"]."\", "; 

			$querystatement.="paymentmethod=\"".$_POST["paymentmethod"]."\", "; 
			$querystatement.="checkno=\"".$_POST["checkno"]."\", "; 
			$querystatement.="bankname=\"".$_POST["bankname"]."\", "; 
			$querystatement.="ccnumber=\"".$_POST["ccnumber"]."\", "; 
			$querystatement.="ccexpiration=\"".$_POST["ccexpiration"]."\", "; 
			$querystatement.="ccverification=\"".$_POST["ccverification"]."\", "; 

			$querystatement.="specialinstructions=\"".$_POST["specialinstructions"]."\", "; 
			$querystatement.="printedinstructions=\"".$_POST["printedinstructions"]."\", "; 

			$querystatement.="taxareaid=\"".$_POST["taxareaid"]."\", "; 

			$querystatement.="ponumber=\"".$_POST["ponumber"]."\", "; 
				if($_POST["requireddate"]=="" || $_POST["requireddate"]=="0/0/0000") $tempdate="NULL";
				else{
					$requireddate="/".ereg_replace(",.","/",$_POST["requireddate"]);
					$temparray=explode("/",$requireddate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$querystatement.="requireddate=".$tempdate.", "; 

			if(!isset($_POST["weborder"])) $_POST["weborder"]=0; 
			$querystatement.="weborder=".$_POST["weborder"].", "; 
			$querystatement.="webconfirmationno=\"".$_POST["webconfirmationno"]."\", "; 

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$_SESSION["userinfo"]["id"]."\" "; 
	$querystatement.="WHERE id=".$_POST["id"];
		
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) die ("Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
	
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
	$querystatement="INSERT INTO invoices 
			(clientid,leadsource,status,orderdate,
			invoicedate,address1,address2,city,state,postalcode, country, totaltni,totalti,shipping,tax,amountpaid,
			totalcost,totalweight,shippingmethod,shipped,shippeddate,trackingno,paymentmethod,
			checkno,bankname,ccnumber,ccexpiration,ccverification,specialinstructions,printedinstructions,
			taxareaid,weborder,webconfirmationno,ponumber,requireddate,
						createdby,creationdate,modifiedby) VALUES (";
	
			$querystatement.="\"".$_POST["clientid"]."\", "; 
			$querystatement.="\"".$_POST["leadsource"]."\", "; 

			$querystatement.="\"".$_POST["status"]."\", "; 
			
				if($_POST["orderdate"]=="" || $_POST["orderdate"]=="0/0/0000") $tempdate="NULL";
				else{
					$orderdate="/".ereg_replace(",.","/",$_POST["orderdate"]);
					$temparray=explode("/",$orderdate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$querystatement.=$tempdate.", "; 

				if($_POST["invoicedate"]=="" || $_POST["invoicedate"]=="0/0/0000") $tempdate="NULL";
				else{
					$invoicedate="/".ereg_replace(",.","/",$_POST["invoicedate"]);
					$temparray=explode("/",$invoicedate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
				//make sure there is an invoice date if the status is set to invoice!!!
				if($_POST["status"]=="Invoice" && $tempdate=="NULL") $tempdate="Now()";
			$querystatement.=$tempdate.", "; 
		
			$querystatement.="\"".$_POST["address1"]."\", "; 
			$querystatement.="\"".$_POST["address2"]."\", "; 
			$querystatement.="\"".$_POST["city"]."\", "; 
			$querystatement.="\"".$_POST["state"]."\", "; 
			$querystatement.="\"".$_POST["postalcode"]."\", "; 
			$querystatement.="\"".$_POST["country"]."\", "; 

				$totaltni=ereg_replace("\\\$|,","",$_POST["totaltni"]);
				$totalti=ereg_replace("\\\$|,","",$_POST["totalti"]);
				$shipping=ereg_replace("\\\$|,","",$_POST["shipping"]);
				$tax=ereg_replace("\\\$|,","",$_POST["tax"]);
				$amountpaid=ereg_replace("\\\$|,","",$_POST["amountpaid"]);

			$querystatement.=$totaltni.", "; 
			$querystatement.=$totalti.", "; 
			$querystatement.=$shipping.", "; 
			$querystatement.=$tax.", "; 
			$querystatement.=$amountpaid.", "; 

			$querystatement.=$_POST["totalcost"].", "; 
			$querystatement.=$_POST["totalweight"].", "; 

			$querystatement.="\"".$_POST["shippingmethod"]."\", "; 

			if(isset($_POST["shipped"])) $querystatement.="1, "; else $querystatement.="0, ";
				if($_POST["shippeddate"]=="" || $_POST["shippeddate"]=="0/0/0000") $tempdate="NULL";
				else{
					$shippeddate="/".ereg_replace(",.","/",$_POST["shippeddate"]);
					$temparray=explode("/",$shippeddate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$querystatement.=$tempdate.", "; 
			$querystatement.="\"".$_POST["trackingno"]."\", "; 

			$querystatement.="\"".$_POST["paymentmethod"]."\", "; 
			$querystatement.="\"".$_POST["checkno"]."\", "; 
			$querystatement.="\"".$_POST["bankname"]."\", "; 
			$querystatement.="\"".$_POST["ccnumber"]."\", "; 
			$querystatement.="\"".$_POST["ccexpiration"]."\", "; 
			$querystatement.="\"".$_POST["ccverification"]."\", "; 

			$querystatement.="\"".$_POST["specialinstructions"]."\", "; 
			$querystatement.="\"".$_POST["printedinstructions"]."\", "; 

			$querystatement.="\"".$_POST["taxareaid"]."\", "; 

			if(!isset($_POST["weborder"])) $_POST["weborder"]=0; 
			$querystatement.=$_POST["weborder"].", "; 
			$querystatement.="\"".$_POST["webconfirmationno"]."\", "; 
			$querystatement.="\"".$_POST["ponumber"]."\", "; 
				if($_POST["requireddate"]=="" || $_POST["requireddate"]=="0/0/0000") $tempdate="NULL";
				else{
					$requireddate="/".ereg_replace(",.","/",$_POST["requireddate"]);
					$temparray=explode("/",$requireddate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$querystatement.=$tempdate.", "; 
				
	//==== Almost all records should have this =========
	$querystatement.=$_SESSION["userinfo"]["id"].", "; 
	$querystatement.="Now(), ";
	$querystatement.=$_SESSION["userinfo"]["id"].")"; 
	
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) die ("Insert Failed: ".mysql_error($dblink)." -- ".$querystatement);

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