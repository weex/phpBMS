<?PHP
function getDiscount($id){
	if(!$id) return "";
	
	global $dblink;
	
	$querystatement="SELECT if(discounts.type+0=1,concat(discounts.value,\"%\"),discounts.value) AS value FROM discounts WHERE id=".$id;
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Error Retreiving Discount".$querystatement." - ".mysql_error($dblink)));
	$therecord=mysql_fetch_array($queryresult);
	
	return $therecord["value"];
}

function getLineItems($id){
	if(!$id) return false;
	global $dblink;
  	$querystatement="SELECT lineitems.id,lineitems.productid,
	
				products.partnumber as partnumber, products.partname as partname, lineitems.taxable,
				lineitems.quantity as quantity, concat(\"\$\",format(lineitems.unitprice,2)) as unitprice, 
				lineitems.unitprice as numprice, lineitems.unitcost as unitcost, lineitems.unitweight as unitweight, lineitems.memo as memo,
				concat(\"\$\",format((lineitems.unitprice*lineitems.quantity),2)) as extended 
				FROM lineitems LEFT JOIN products on lineitems.productid=products.id 
				WHERE lineitems.invoiceid=".$id;
				
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Error Retreiving Line Items ".$querystatement." - ".mysql_error($dblink)));
	return $queryresult;	
}

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

function addLineItems($values,$invoiceid,$userid){
	global $dblink;
	
	$querystatement="DELETE FROM lineitems WHERE  invoiceid=".$invoiceid;
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,"Could Not remove lineitems");

	if($values){
	$lineitems= explode("{[]}",$values);		
	foreach($lineitems as $lineitem) {
		$fields=explode("[//]",$lineitem);
		$querystatement="INSERT INTO lineitems (invoiceid,productid,quantity,unitcost,unitprice,unitweight,taxable,memo,createdby,creationdate,modifiedby) VALUES (";
		$querystatement.=$invoiceid.", ";
		if(trim($fields[0])!="" and trim($fields[0])!="0"){
			$querystatement.=trim($fields[0]).", ";
			}
		else
			$querystatement.="NULL, ";
		if(trim($fields[4])!="" and trim($fields[4])!="0")
			$querystatement.=trim($fields[4]).", ";
		else
			$querystatement.="0, ";
		if(trim($fields[1])!="" and trim($fields[1])!="0")
			$querystatement.=trim($fields[1]).", ";
		else
			$querystatement.="0, ";
		if(trim($fields[3])!="" and trim($fields[3])!="0")
			$querystatement.=trim($fields[3]).", ";
		else
			$querystatement.="0, ";
		if(trim($fields[2])!="" and trim($fields[2])!="0")
			$querystatement.=trim($fields[2]).", ";
		else
			$querystatement.="0, ";
		if(trim($fields[6])!="" and trim($fields[6])!="0")
			$querystatement.=trim($fields[6]).", ";
		else
			$querystatement.="0, ";
		$querystatement.="\"".trim($fields[5])."\",";
		$querystatement.=$userid.", NOW(), ".$userid." )";

		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(100,"Could Not Add Line Item: ".mysql_error($dblink)."<br \><br \>".$querystatement);

	}//end foreach
	}//end if
}


// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=3;
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
	
	$querystatement="SELECT id, clientid, status, type, totalweight, totaltni, totalti, totalcost,
					leadsource, shippingmethod, paymentmethod, checkno, bankname, ccnumber,
					ccexpiration, specialinstructions, printedinstructions, tax, shipping,
					address1,address2,city,state,postalcode, country, amountpaid, 
					trackingno, taxareaid, taxpercentage, totalti-amountpaid as amountdue,
					weborder,webconfirmationno,ccverification, totaltaxable, discountamount,discountid,
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
	//from quickview
	if(isset($_GET["cid"]))
		$therecord["clientid"]=$_GET["cid"];
	$therecord["type"]="Order";
	$therecord["status"]="Open";

	$therecord["leadsource"]="";
	$therecord["address1"]="";
	$therecord["address2"]="";
	$therecord["city"]="";
	$therecord["state"]="";
	$therecord["postalcode"]="";
	$therecord["country"]="";

	$therecord["taxareaid"]=NULL;
	$therecord["taxpercentage"]=NULL;
	$therecord["shippingmethod"]=NULL;
	$therecord["tax"]=0;
	$therecord["totalweight"]=0;
	$therecord["discountid"]=0;
	$therecord["discountamount"]=0;
	$therecord["subtotal"]=0;
	$therecord["totaltni"]=0;
	$therecord["totaltaxable"]=0;
	$therecord["totalti"]=0;
	$therecord["shipping"]=0;
	$therecord["totalcost"]=0;

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

	$therecord["ponumber"]="";
	$therecord["requireddate"]=NULL;
	
	$therecord["createdby"]=$_SESSION["userinfo"]["id"];
	$therecord["modifiedby"]=NULL;

	$therecord["creationdate"]=NULL;
	$therecord["modifieddate"]=NULL;
	
	return $therecord;	
}//end function

function updateRecord($variables,$userid){
//========================================================================================	
	global $dblink;
	
	if($variables["oldType"]=="Invoice")
		return "Cannot save modifications to an invoice";
		
	if($variables["type"]=="VOID"){
		$variables["totaltni"]=0;
		$variables["totalti"]=0;
		$variables["amountpaid"]=0;
	}
	$querystatement="UPDATE invoices SET ";
	
			$querystatement.="clientid=\"".$variables["clientid"]."\", "; 
			$querystatement.="leadsource=\"".$variables["leadsource"]."\", "; 

			$querystatement.="type=\"".$variables["type"]."\", "; 
			$querystatement.="status=\"".$variables["status"]."\", "; 
			
				if($variables["orderdate"]=="" || $variables["orderdate"]=="0/0/0000") $tempdate="NULL";
				else{
					$variables["orderdate"]="/".ereg_replace(",.","/",$variables["orderdate"]);
					$temparray=explode("/",$variables["orderdate"]);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$querystatement.="orderdate=".$tempdate.", "; 

				$discountamount=ereg_replace("\\\$|,","",$variables["discountamount"]);
			$querystatement.="discountamount=".$discountamount.", "; 				
			$querystatement.="discountid=".$variables["discountid"].", "; 

				if($variables["invoicedate"]=="" || $variables["invoicedate"]=="0/0/0000") $tempdate="NULL";
				else{
					$variables["invoicedate"]="/".ereg_replace(",.","/",$variables["invoicedate"]);
					$temparray=explode("/",$variables["invoicedate"]);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
				//make sure there is an invoice date if the status is set to invoice!!!
				if($variables["status"]=="Invoice" && $tempdate=="NULL") $tempdate="Now()";
			$querystatement.="invoicedate=".$tempdate.", "; 
		
			$querystatement.="address1=\"".$variables["address1"]."\", "; 
			$querystatement.="address2=\"".$variables["address2"]."\", "; 
			$querystatement.="city=\"".$variables["city"]."\", "; 
			$querystatement.="state=\"".$variables["state"]."\", "; 
			$querystatement.="postalcode=\"".$variables["postalcode"]."\", "; 
			$querystatement.="country=\"".$variables["country"]."\", "; 

				$totaltni=ereg_replace("\\\$|,","",$variables["totaltni"]);
				$totaltaxable=ereg_replace("\\\$|,","",$variables["totaltaxable"]);
				$totalti=ereg_replace("\\\$|,","",$variables["totalti"]);
				$shipping=ereg_replace("\\\$|,","",$variables["shipping"]);
				$tax=ereg_replace("\\\$|,","",$variables["tax"]);
				$amountpaid=ereg_replace("\\\$|,","",$variables["amountpaid"]);

			$querystatement.="totaltni=".$totaltni.", "; 
			$querystatement.="totaltaxable=".$totaltaxable.", "; 
			$querystatement.="totalti=".$totalti.", "; 
			$querystatement.="shipping=".$shipping.", "; 
			$querystatement.="tax=".$tax.", "; 
			$querystatement.="amountpaid=".$amountpaid.", "; 

			$querystatement.="totalcost=".$variables["totalcost"].", "; 
			$querystatement.="totalweight=".$variables["totalweight"].", "; 

			$querystatement.="shippingmethod=\"".$variables["shippingmethod"]."\", "; 

			if($variables["shippeddate"]=="" || $variables["shippeddate"]=="0/0/0000") 
				{$tempdate="NULL";}
			else{
					$shippeddate="/".ereg_replace(",.","/",$variables["shippeddate"]);
					$temparray=explode("/",$shippeddate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$querystatement.="shippeddate=".$tempdate.", "; 
			$querystatement.="trackingno=\"".$variables["trackingno"]."\", "; 

			if($_SESSION["userinfo"]["accesslevel"]>=20){
				$querystatement.="paymentmethod=\"".$variables["paymentmethod"]."\", "; 
				$querystatement.="checkno=\"".$variables["checkno"]."\", "; 
				$querystatement.="bankname=\"".$variables["bankname"]."\", "; 
				$querystatement.="ccnumber=\"".$variables["ccnumber"]."\", "; 
				$querystatement.="ccexpiration=\"".$variables["ccexpiration"]."\", "; 
				$querystatement.="ccverification=\"".$variables["ccverification"]."\", "; 
			}

			$querystatement.="specialinstructions=\"".$variables["specialinstructions"]."\", "; 
			$querystatement.="printedinstructions=\"".$variables["printedinstructions"]."\", "; 

			$querystatement.="taxareaid=\"".$variables["taxareaid"]."\", "; 
			
			$variables["taxpercentage"]=str_replace("%","",$variables["taxpercentage"]);
			if($variables["taxpercentage"]=="" or  $variables["taxpercentage"]=="0" or $variables["taxpercentage"]=="0.0")
				$variables["taxpercentage"]="NULL";
			$querystatement.="taxpercentage=".$variables["taxpercentage"].", "; 

			$querystatement.="ponumber=\"".$variables["ponumber"]."\", "; 
				if($variables["requireddate"]=="" || $variables["requireddate"]=="0/0/0000") $tempdate="NULL";
				else{
					$requireddate="/".ereg_replace(",.","/",$variables["requireddate"]);
					$temparray=explode("/",$requireddate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$querystatement.="requireddate=".$tempdate.", "; 

			if(!isset($variables["weborder"])) $variables["weborder"]=0; 
			$querystatement.="weborder=".$variables["weborder"].", "; 
			$querystatement.="webconfirmationno=\"".$variables["webconfirmationno"]."\", "; 

	//==== Almost all records should have this =========
	$querystatement.="modifiedby=\"".$userid."\" "; 
	$querystatement.="WHERE id=".$variables["id"];
		
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) die ("Update Failed: ".mysql_error($dblink)." -- ".$querystatement);
	
	if($variables["lineitemschanged"]==1)
		addLineItems($variables["thelineitems"],$variables["id"],$userid);
		
	return "Record Updated";
}// end function


function insertRecord($variables,$userid){
//========================================================================================
	global $dblink;

	if($variables["status"]=="VOID"){
		$variables["totaltni"]=0;
		$variables["totalti"]=0;
		$variables["amountpaid"]=0;
	}
	$querystatement="INSERT INTO invoices 
			(clientid,leadsource,type,status,orderdate,discountamount,discountid,
			invoicedate,address1,address2,city,state,postalcode, country, totaltni, totaltaxable, totalti,shipping,tax,amountpaid,
			totalcost,totalweight,shippingmethod,shippeddate,trackingno,paymentmethod,
			checkno,bankname,ccnumber,ccexpiration,ccverification,specialinstructions,printedinstructions,
			taxareaid, taxpercentage, weborder,webconfirmationno,ponumber,requireddate,
						createdby,creationdate,modifiedby) VALUES (";
	
			$querystatement.="\"".$variables["clientid"]."\", "; 
			$querystatement.="\"".$variables["leadsource"]."\", "; 

			$querystatement.="\"".$variables["type"]."\", "; 
			$querystatement.="\"".$variables["status"]."\", "; 
			
				if($variables["orderdate"]=="" || $variables["orderdate"]=="0/0/0000") $tempdate="NULL";
				else{
					$orderdate="/".ereg_replace(",.","/",$variables["orderdate"]);
					$temparray=explode("/",$orderdate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$querystatement.=$tempdate.", "; 

				$discountamount=ereg_replace("\\\$|,","",$variables["discountamount"]);
			$querystatement.=$discountamount.", "; 				
			$querystatement.=$variables["discountid"].", "; 

				if($variables["invoicedate"]=="" || $variables["invoicedate"]=="0/0/0000") $tempdate="NULL";
				else{
					$invoicedate="/".ereg_replace(",.","/",$variables["invoicedate"]);
					$temparray=explode("/",$invoicedate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
				//make sure there is an invoice date if the status is set to invoice!!!
				if($variables["status"]=="Invoice" && $tempdate=="NULL") $tempdate="Now()";
			$querystatement.=$tempdate.", "; 
		
			$querystatement.="\"".$variables["address1"]."\", "; 
			$querystatement.="\"".$variables["address2"]."\", "; 
			$querystatement.="\"".$variables["city"]."\", "; 
			$querystatement.="\"".$variables["state"]."\", "; 
			$querystatement.="\"".$variables["postalcode"]."\", "; 
			$querystatement.="\"".$variables["country"]."\", "; 

				$totaltni=ereg_replace("\\\$|,","",$variables["totaltni"]);
				$totaltaxable=ereg_replace("\\\$|,","",$variables["totaltaxable"]);
				$totalti=ereg_replace("\\\$|,","",$variables["totalti"]);
				$shipping=ereg_replace("\\\$|,","",$variables["shipping"]);
				$tax=ereg_replace("\\\$|,","",$variables["tax"]);
				$amountpaid=ereg_replace("\\\$|,","",$variables["amountpaid"]);

			$querystatement.=$totaltni.", "; 
			$querystatement.=$totaltaxable.", "; 
			$querystatement.=$totalti.", "; 
			$querystatement.=$shipping.", "; 
			$querystatement.=$tax.", "; 
			$querystatement.=$amountpaid.", "; 

			$querystatement.=$variables["totalcost"].", "; 
			$querystatement.=$variables["totalweight"].", "; 

			$querystatement.="\"".$variables["shippingmethod"]."\", "; 

				if($variables["shippeddate"]=="" || $variables["shippeddate"]=="0/0/0000") $tempdate="NULL";
				else{
					$shippeddate="/".ereg_replace(",.","/",$variables["shippeddate"]);
					$temparray=explode("/",$shippeddate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$querystatement.=$tempdate.", "; 
			$querystatement.="\"".$variables["trackingno"]."\", "; 

			$querystatement.="\"".$variables["paymentmethod"]."\", "; 
			$querystatement.="\"".$variables["checkno"]."\", "; 
			$querystatement.="\"".$variables["bankname"]."\", "; 
			$querystatement.="\"".$variables["ccnumber"]."\", "; 
			$querystatement.="\"".$variables["ccexpiration"]."\", "; 
			$querystatement.="\"".$variables["ccverification"]."\", "; 

			$querystatement.="\"".$variables["specialinstructions"]."\", "; 
			$querystatement.="\"".$variables["printedinstructions"]."\", "; 

			$querystatement.="\"".$variables["taxareaid"]."\", "; 

			$variables["taxpercentage"]=str_replace("%","",$variables["taxpercentage"]);
			if($variables["taxpercentage"]=="" or  $variables["taxpercentage"]=="0" or $variables["taxpercentage"]=="0.0")
				$variables["taxpercentage"]="NULL";				
			$querystatement.=$variables["taxpercentage"].", "; 

			if(!isset($variables["weborder"])) $variables["weborder"]=0; 
			$querystatement.=$variables["weborder"].", "; 
			$querystatement.="\"".$variables["webconfirmationno"]."\", "; 
			$querystatement.="\"".$variables["ponumber"]."\", "; 
				if($variables["requireddate"]=="" || $variables["requireddate"]=="0/0/0000") $tempdate="NULL";
				else{
					$requireddate="/".ereg_replace(",.","/",$variables["requireddate"]);
					$temparray=explode("/",$requireddate);
					$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
				}
			$querystatement.=$tempdate.", "; 
				
	//==== Almost all records should have this =========
	$querystatement.=$userid.", "; 
	$querystatement.="Now(), ";
	$querystatement.=$userid.")"; 
	
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) die ("Insert Failed: ".mysql_error($dblink)." -- ".$querystatement);

	$newid= mysql_insert_id($dblink);
	if($variables["lineitemschanged"]==1)
		addLineItems($variables["thelineitems"],$newid,$userid);
	
	return $newid;
}


//==================================================================
// Process adding, editing, creating new, canceling or updating
//==================================================================

if(!isset($_POST["command"])){
	if(isset($_GET["id"])){
		$therecord=getRecords($_GET["id"]);
		//invoice specific
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
			$theid=$_POST["id"];
			header("Location: ".$backurl."#".$theid);
		break;
		case "save":
			if($_POST["id"]) {
				$statusmessage=updateRecord(addSlashesToArray($_POST),$_SESSION["userinfo"]["id"]);
				$theid=$_POST["id"];
				//get record
				$therecord=getRecords($theid);
				$createdby=getUserName($therecord["createdby"]);
				$modifiedby=getUserName($therecord["modifiedby"]);
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