<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
 | All rights reserved.                                                    |
 +-------------------------------------------------------------------------+
 |                                                                         |
 | Redistribution and use in source and binary forms, with or without      |
 | modification, are permitted provided that the following conditions are  |
 | met:                                                                    |
 |                                                                         |
 | - Redistributions of source code must retain the above copyright        |
 |   notice, this list of conditions and the following disclaimer.         |
 |                                                                         |
 | - Redistributions in binary form must reproduce the above copyright     |
 |   notice, this list of conditions and the following disclaimer in the   |
 |   documentation and/or other materials provided with the distribution.  |
 |                                                                         |
 | - Neither the name of Kreotek LLC nor the names of its contributore may |
 |   be used to endorse or promote products derived from this software     |
 |   without specific prior written permission.                            |
 |                                                                         |
 | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS     |
 | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT       |
 | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A |
 | PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
 | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,   |
 | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT        |
 | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,   |
 | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY   |
 | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT     |
 | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE   |
 | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.    |
 |                                                                         |
 +-------------------------------------------------------------------------+
*/
function updateStatus($invoiceid,$statusid,$statusdate,$assignedtoid,$dblink){
	$querystatement="DELETE FROM invoicestatushistory WHERE invoiceid=".$invoiceid." AND invoicestatusid=".$statusid;
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Error Updating Status History: deleteing<br />".mysql_error($dblink));
	
	$querystatement="INSERT INTO invoicestatushistory (invoiceid,invoicestatusid,statusdate,assignedtoid) values(";
	$querystatement.=((int) $invoiceid).", ";
	$querystatement.=((int) $statusid).", ";

	if($statusdate=="" || $statusdate=="0/0/0000") $tempdate="NULL";
	else{
		$tempdate="\"".sqlDateFromString($statusdate)."\"";
	}
	$querystatement.=$tempdate.",";
	if($assignedtoid=="")
		$querystatement.="NULL";
	else
		$querystatement.=((int) $assignedtoid);

	$querystatement.=")";

	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Error Updating Status History: inserting<br />".mysql_error($dblink));
}

function showPaymentSelect($id,$paymentMethods){
	?><select name="paymentmethodid" id="paymentmethodid" onchange="showPaymentOptions()">
		<option value="0" <?php if($id==0) echo "selected=\"selected\""?>>&lt;none&gt;</option>
	<?php foreach($paymentMethods as $method){?>
		<option value="<?php echo $method["id"]?>" <?php if($id==$method["id"]) echo "selected=\"selected\""?>><?php echo $method["name"]?></option>	
	<?php } ?>
	</select>
	<?php 
}

function getPayments($dblink){
	$querystatement="SELECT id,name,type,onlineprocess,processscript FROM paymentmethods WHERE inactive=0 ORDER BY priority,name";
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Error Retreiving Payment Methods ".$querystatement." - ".mysql_error($dblink)));
	?><script language="javascript" type="text/javascript">
		paymentMethods=Array();
		<?php while($therecord=mysql_fetch_array($queryresult)) {
			$thereturn[$therecord["id"]]=$therecord;
		?>
			paymentMethods[<?php echo $therecord["id"]?>]=Array();
			paymentMethods[<?php echo $therecord["id"]?>]["name"]="<?php echo $therecord["name"]?>";
			paymentMethods[<?php echo $therecord["id"]?>]["type"]="<?php echo $therecord["type"]?>";
			paymentMethods[<?php echo $therecord["id"]?>]["onlineprocess"]=<?php echo $therecord["onlineprocess"]?>;
			paymentMethods[<?php echo $therecord["id"]?>]["processscript"]="<?php echo $therecord["processscript"]?>";
		<?php } ?>
	</script><?php 
	return $queryresult;
}

function showShippingSelect($id,$shippingMethods){
	?><select name="shippingmethodid" id="shippingmethodid" onchange="changeShipping(this)">
		<option value="0" <?php if($id==0) echo "selected=\"selected\""?>>&lt;none&gt;</option>
	<?php foreach($shippingMethods as $method){?>
		<option value="<?php echo $method["id"]?>" <?php if($id==$method["id"]) echo "selected=\"selected\""?>><?php echo $method["name"]?></option>	
	<?php } ?>
	</select>
	<?php 
}

function getShipping($dblink){
	$querystatement="SELECT id,name,canestimate,estimationscript FROM shippingmethods WHERE inactive=0 ORDER BY priority,name";
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Error Retreiving Shipping Methods ".$querystatement." - ".mysql_error($dblink)));
	?><script language="javascript" type="text/javascript">
		shippingMethods=Array();
		<?php while($therecord=mysql_fetch_array($queryresult)) {
			$thereturn[$therecord["id"]]=$therecord;
		?>
			shippingMethods[<?php echo $therecord["id"]?>]=Array();
			shippingMethods[<?php echo $therecord["id"]?>]["name"]="<?php echo $therecord["name"]?>";
			shippingMethods[<?php echo $therecord["id"]?>]["canestimate"]=<?php echo $therecord["canestimate"]?>;
			shippingMethods[<?php echo $therecord["id"]?>]["estimationscript"]="<?php echo $therecord["estimationscript"]?>";
		<?php } ?>
	</script><?php 
	return $queryresult;
}


function showTaxSelect($id,$dblink){
	$id=(int) $id;
	$querystatement="SELECT id,name,percentage FROM tax WHERE inactive=0 OR id=".$id." ORDER BY name";
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Error Retreiving Tax Areas ".$querystatement." - ".mysql_error($dblink)));
	?><select name="taxareaid" id="taxareaid" onchange="getPercentage()" size="5">
		<option value="0" <?php if($id==0) echo "selected=\"selected\""?>>&lt;none&gt;</option>
		<?php 
			while($therecord=mysql_fetch_array($queryresult)){
				?><option value="<?php echo $therecord["id"]?>" <?php if($id==$therecord["id"]) echo "selected=\"selected\""?>><?php echo $therecord["name"].": ".$therecord["percentage"]."%"?></option><?php
			}
		?>
	</select><?php
	
}

function showDiscountSelect($id,$dblink){
	$id=(int) $id;
	$querystatement="SELECT id,name,type,value FROM discounts WHERE inactive!=1 ORDER BY name";
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Error Retreiving Discounts ".$querystatement." - ".mysql_error($dblink)));
	?><select name="discountid" id="discountid" onchange="getDiscount()" size="8">
		<option value="0" <?php if($id==0) echo "selected=\"selected\""?>>&lt;none&gt;</option>
		<?php 
			while($therecord=mysql_fetch_array($queryresult)){
				if($therecord["type"]=="amount")
					$therecord["value"]=numberToCurrency($therecord["value"]);
				else
					$therecord["value"].="%";
				?><option value="<?php echo $therecord["id"]?>" <?php if($id==$therecord["id"]) echo "selected=\"selected\""?>><?php echo $therecord["name"].": ".$therecord["value"]?></option><?php
			}
		?>
	</select><?php
}

function getDiscount($id,$dblink){
	$therecord["name"]="";
	$therecord["value"]=0;
	
	if(((int) $id)!=0){		
		$querystatement="SELECT name,type,value FROM discounts WHERE id=".$id;
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(100,("Error Retreiving Discount".$querystatement." - ".mysql_error($dblink)));
		$therecord=mysql_fetch_array($queryresult);
		if($therecord["type"]!="amount"){
			$therecord["value"].="%";
			$therecord["name"].=": ".$therecord["value"];
		} else
			$therecord["name"].=": ".numberToCurrency($therecord["value"]);
	}
	$therecord["name"]=htmlQuotes($therecord["name"]);
	return $therecord;
}


function getLineItems($id){
	if(!$id) return false;
	global $dblink;
  	$querystatement="SELECT lineitems.id,lineitems.productid,
	
				products.partnumber as partnumber, products.partname as partname, lineitems.taxable,
				lineitems.quantity as quantity, lineitems.unitprice, 
				lineitems.unitprice as numprice, lineitems.unitcost as unitcost, lineitems.unitweight as unitweight, lineitems.memo as memo,
				lineitems.unitprice*lineitems.quantity as extended 
				FROM lineitems LEFT JOIN products on lineitems.productid=products.id 
				WHERE lineitems.invoiceid=".$id;
				
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Error Retreiving Line Items ".$querystatement." - ".mysql_error($dblink)));
	return $queryresult;	
}

function getTax($id,$dblink){
	$therecord["name"]="";
	
	if(((int) $id)!=0){
		
		$querystatement="SELECT name FROM tax WHERE id=".$id;
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(100,"Could Not Retrieve Tax Percentage");
		if(mysql_num_rows($queryresult))
			$therecord=mysql_fetch_array($queryresult);
	}
	
	$therecord["name"]=htmlQuotes($therecord["name"]);
	return $therecord;
}

function addLineItems($values,$invoiceid,$userid){
	global $dblink;
	
	$querystatement="DELETE FROM lineitems WHERE  invoiceid=".$invoiceid;
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,"Could Not remove lineitems");
		
	//var_dump($values);
	//exit;
	if($values){
	$lineitems= explode("{[]}",$values);		
	foreach($lineitems as $lineitem) {
		$fields=explode("[//]",$lineitem);
		$querystatement="INSERT INTO lineitems 
						(invoiceid, productid, quantity, unitcost, unitprice,
						unitweight, taxable, memo, createdby, creationdate, modifiedby) VALUES (";
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
		$querystatement.="\"".trim(html_entity_decode($fields[5]))."\",";
		$querystatement.=$userid.", NOW(), ".$userid." )";

		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(100,"Could Not Add Line Item: ".mysql_error($dblink)."<br /><br />".$querystatement);

	}//end foreach
	}//end if
}

function getDefaultStatus(){
	global $dblink;
		
	$querystatement="SELECT id FROM invoicestatuses WHERE invoicedefault=1";
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,"Could Not Retrieve Stauts Default: ".mysql_error($dblink)."<br /><br />".$querystatement);
	$therecord=mysql_fetch_array($queryresult);
	
	return $therecord["id"];
}

function displayStatusDropDown($statusid,$dblink){
	$querystatement="SELECT invoicestatuses.id,invoicestatuses.name,invoicestatuses.invoicedefault,users.firstname,users.lastname FROM 
					(invoicestatuses LEFT JOIN users ON invoicestatuses.defaultassignedtoid=users.id)WHERE invoicestatuses.inactive=0
					ORDER BY invoicestatuses.priority,invoicestatuses.name";
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Error Retrieving Statuses: ".mysql_error($dblink));	
	?><select id="statusid" name="statusid" onchange="updateAssignedTo();updateStatusDate()" class="important">
		<?php
		$options="";
		while($therecord=mysql_fetch_array($queryresult)){
			if($therecord["firstname"]!="" || $therecord["lastname"]!="")
				$options["s".$therecord["id"]]=trim($therecord["firstname"]." ".$therecord["lastname"]);
			?><option value="<?php echo $therecord["id"]?>" <?php if($statusid==$therecord["id"]) echo "selected=\"selected\""?>><?php echo $therecord["name"]?></option><?php
		}
		?>
	</select><script language="javascript" type="text/javascript">statusAssignedto=new Array;<?php 
		if($options!="")
			foreach($options as $key=>$value){
				echo "statusAssignedto[\"".$key."\"] = \"".$value."\";\n";
			}
?></script><?php
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
	
	$querystatement="SELECT id, clientid, statusid, assignedtoid, type, totalweight, totaltni, totalti, totalcost,
					leadsource, shippingmethodid, paymentmethodid, checkno, bankname, ccnumber, routingnumber, 
					accountnumber, transactionid,
					ccexpiration, specialinstructions, printedinstructions, tax, shipping,
					address1,address2,city,state,postalcode, country, amountpaid, 
					trackingno, taxareaid, taxpercentage, totalti-amountpaid as amountdue,
					weborder,webconfirmationno,ccverification, totaltaxable, discountamount,discountid,
					invoicedate,orderdate,statusdate,requireddate,
					ponumber,
					
				createdby, creationdate, 
				modifiedby, modifieddate
				FROM invoices
				WHERE invoices.id=".$id;		
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Could not retrieve record: ".mysql_error($dblink)." ".$querystatement));
	$therecord = mysql_fetch_array($queryresult);
	if(!$therecord) reportError(300,"No record for id ".$id);

	$discountinfo=getDiscount($therecord["discountid"],$dblink);
	$therecord["discountname"]=$discountinfo["name"];
	$therecord["discount"]=$discountinfo["value"];

	$taxinfo=getTax($therecord["taxareaid"],$dblink);
	$therecord["taxname"]=$taxinfo["name"];

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
	$therecord["statusid"]=getDefaultStatus();
	$therecord["orderdate"]=dateToString(mktime(),"SQL");
	$therecord["invoicedate"]=NULL;
	$therecord["statusdate"]=dateToString(mktime(),"SQL");
	$therecord["assignedtoid"]="";

	$therecord["leadsource"]="";
	$therecord["address1"]="";
	$therecord["address2"]="";
	$therecord["city"]="";
	$therecord["state"]="";
	$therecord["postalcode"]="";
	$therecord["country"]="";

	$therecord["taxareaid"]=0;
	$therecord["taxname"]=0;
	$therecord["tax"]=0;
	$therecord["taxpercentage"]=NULL;

	$therecord["shippingmethodid"]=0;
	$therecord["totalweight"]=0;
	
	$therecord["discountid"]=0;
	$therecord["discountamount"]=0;
	$therecord["discountname"]="";
	$therecord["discount"]=0;
		
	$therecord["subtotal"]=0;
	$therecord["totaltni"]=0;
	$therecord["totaltaxable"]=0;
	$therecord["totalti"]=0;
	$therecord["shipping"]=0;
	$therecord["totalcost"]=0;

	$therecord["weborder"]=0;
	$therecord["trackingno"]="";
	$therecord["webconfirmationno"]="";

	$therecord["specialinstructions"]="";

	$therecord["paymentmethodid"]=0;
	$therecord["routingnumber"]="";
	$therecord["accountnumber"]="";	
	$therecord["ccnumber"]="";
	$therecord["ccexpiration"]="";
	$therecord["ccverification"]="";
	$therecord["checkno"]="";
	$therecord["bankname"]="";
	$therecord["transactionid"]="";

	$therecord["amountpaid"]=0;
	$therecord["amountdue"]=0;

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

			$querystatement.="statusid=".((int)$variables["statusid"]).", ";
			$querystatement.="assignedtoid=".((int) $variables["assignedtoid"]).", ";
			if($variables["statusdate"]=="" || $variables["statusdate"]=="0/0/0000") $tempdate="NULL";
			else
				$tempdate="\"".sqlDateFromString($variables["statusdate"])."\"";
			$querystatement.="statusdate=".$tempdate.", ";

			
			if($variables["orderdate"]=="" || $variables["orderdate"]=="0/0/0000") $tempdate="NULL";
			else
				$tempdate="\"".sqlDateFromString($variables["orderdate"])."\"";
			$querystatement.="orderdate=".$tempdate.", "; 

			$querystatement.="discountamount=".currencyToNumber($variables["discountamount"]).", ";
			if($variables["discountid"]=="")$variables["discountid"]="NULL";
			$querystatement.="discountid=".$variables["discountid"].", "; 

				if($variables["invoicedate"]=="" || $variables["invoicedate"]=="0/0/0000") $tempdate="NULL";
				else
					$tempdate="\"".sqlDateFromString($variables["invoicedate"])."\"";

				//make sure there is an invoice date if the status is set to invoice!!!
				if($variables["type"]=="Invoice" && $tempdate=="NULL") $tempdate="Now()";
			$querystatement.="invoicedate=".$tempdate.", "; 
		
			$querystatement.="address1=\"".$variables["address1"]."\", "; 
			$querystatement.="address2=\"".$variables["address2"]."\", "; 
			$querystatement.="city=\"".$variables["city"]."\", "; 
			$querystatement.="state=\"".$variables["state"]."\", "; 
			$querystatement.="postalcode=\"".$variables["postalcode"]."\", "; 
			$querystatement.="country=\"".$variables["country"]."\", "; 

			$querystatement.="totaltni=".currencyToNumber($variables["totaltni"]).", "; 
			$querystatement.="totaltaxable=".currencyToNumber($variables["totaltaxable"]).", "; 
			$querystatement.="totalti=".currencyToNumber($variables["totalti"]).", "; 
			$querystatement.="shipping=".currencyToNumber($variables["shipping"]).", "; 
			$querystatement.="tax=".currencyToNumber($variables["tax"]).", "; 
			$querystatement.="amountpaid=".currencyToNumber($variables["amountpaid"]).", "; 

			$querystatement.="totalcost=".$variables["totalcost"].", "; 
			$querystatement.="totalweight=".$variables["totalweight"].", "; 

			$querystatement.="shippingmethodid=".$variables["shippingmethodid"].", "; 

			$querystatement.="trackingno=\"".$variables["trackingno"]."\", "; 

			if(hasRights(20)){
				$querystatement.="paymentmethodid=".$variables["paymentmethodid"].", "; 
				$querystatement.="checkno=\"".$variables["checkno"]."\", "; 
				$querystatement.="bankname=\"".$variables["bankname"]."\", "; 
				$querystatement.="ccnumber=\"".$variables["ccnumber"]."\", "; 
				$querystatement.="ccexpiration=\"".$variables["ccexpiration"]."\", "; 
				$querystatement.="ccverification=\"".$variables["ccverification"]."\", "; 
				
				if($variables["accountnumber"]=="")$variables["accountnumber"]="NULL";				
				$querystatement.="accountnumber=".$variables["accountnumber"].", "; 
				if($variables["routingnumber"]=="")$variables["routingnumber"]="NULL";
				$querystatement.="routingnumber=".$variables["routingnumber"].", "; 

				$querystatement.="transactionid=\"".$variables["transactionid"]."\", "; 
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
				else
					$tempdate="\"".sqlDateFromString($variables["requireddate"])."\"";

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

	if($variables["statuschanged"]==1)
		updateStatus($variables["id"],$variables["statusid"],$variables["statusdate"],$variables["assignedtoid"],$dblink);
		
	return "Record Updated";
}// end function


function insertRecord($variables,$userid){
//========================================================================================
	global $dblink;

	if($variables["type"]=="VOID"){
		$variables["totaltni"]=0;
		$variables["totalti"]=0;
		$variables["amountpaid"]=0;
	}
	$querystatement="INSERT INTO invoices 
			(clientid,leadsource,type,statusid,assignedtoid,statusdate,orderdate,discountamount,discountid,
			invoicedate,address1,address2,city,state,postalcode, country, totaltni, totaltaxable, totalti,shipping,tax,amountpaid,
			totalcost,totalweight,shippingmethodid,trackingno,paymentmethodid,accountnumber,routingnumber,transactionid,
			checkno,bankname,ccnumber,ccexpiration,ccverification,specialinstructions,printedinstructions,
			taxareaid, taxpercentage, weborder,webconfirmationno,ponumber,requireddate,
						createdby,creationdate,modifiedby) VALUES (";
	
			$querystatement.="\"".$variables["clientid"]."\", "; 
			$querystatement.="\"".$variables["leadsource"]."\", "; 

			$querystatement.="\"".$variables["type"]."\", "; 
			$querystatement.=((int)$variables["statusid"]).", ";
			$querystatement.=((int) $variables["assignedtoid"]).", ";
			if($variables["statusdate"]=="") $tempdate="NULL";
			else
				$tempdate="\"".sqlDateFromString($variables["statusdate"])."\"";
			$querystatement.=$tempdate.", ";
			
				if($variables["orderdate"]=="") $tempdate="NULL";
				else
					$tempdate="\"".sqlDateFromString($variables["orderdate"])."\"";
			$querystatement.=$tempdate.", "; 

			$querystatement.=currencyToNumber($variables["discountamount"]).", "; 				
			if($variables["discountid"]=="")$variables["discountid"]="NULL";
			$querystatement.=$variables["discountid"].", "; 

				if($variables["invoicedate"]=="") $tempdate="NULL";
				else
					$tempdate="\"".sqlDateFromString($variables["invoicedate"])."\"";
				
				//make sure there is an invoice date if the status is set to invoice!!!
				if($variables["type"]=="Invoice" && $tempdate=="NULL") $tempdate="Now()";
			$querystatement.=$tempdate.", "; 
		
			$querystatement.="\"".$variables["address1"]."\", "; 
			$querystatement.="\"".$variables["address2"]."\", "; 
			$querystatement.="\"".$variables["city"]."\", "; 
			$querystatement.="\"".$variables["state"]."\", "; 
			$querystatement.="\"".$variables["postalcode"]."\", "; 
			$querystatement.="\"".$variables["country"]."\", "; 

			$querystatement.=currencyToNumber($variables["totaltni"]).", "; 
			$querystatement.=currencyToNumber($variables["totaltaxable"]).", "; 
			$querystatement.=currencyToNumber($variables["totalti"]).", "; 
			$querystatement.=currencyToNumber($variables["shipping"]).", "; 
			$querystatement.=currencyToNumber($variables["tax"]).", "; 
			$querystatement.=currencyToNumber($variables["amountpaid"]).", "; 

			$querystatement.=$variables["totalcost"].", "; 
			$querystatement.=$variables["totalweight"].", "; 

			$querystatement.=$variables["shippingmethodid"].", "; 

			$querystatement.="\"".$variables["trackingno"]."\", "; 

			$querystatement.=$variables["paymentmethodid"].", "; 
			if($variables["accountnumber"]=="")$variables["accountnumber"]="NULL";				
			$querystatement.=$variables["accountnumber"].", "; 
			if($variables["routingnumber"]=="")$variables["routingnumber"]="NULL";
			$querystatement.=$variables["routingnumber"].", "; 
			$querystatement.="\"".$variables["transactionid"]."\", "; 			
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
			else
				$tempdate="\"".sqlDateFromString($variables["requireddate"])."\"";
			$querystatement.=$tempdate.", "; 
				
	//==== Almost all records should have this =========
	$querystatement.=$userid.", "; 
	$querystatement.="Now(), ";
	$querystatement.=$userid.")"; 
	
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Insert Failed: ".mysql_error($dblink)."\n<br />\n".$querystatement);

	$newid= mysql_insert_id($dblink);
	if($variables["lineitemschanged"]==1)
		addLineItems($variables["thelineitems"],$newid,$userid);

	if($variables["statuschanged"]==1)
		updateStatus($newid,$variables["statusid"],$variables["statusdate"],$variables["assignedtoid"],$dblink);
	
	return $newid;
}


//==================================================================
// Process adding, editing, creating new, canceling or updating
//==================================================================

if(!isset($_POST["command"])){
	$querystatement="SELECT addroleid,editroleid FROM tabledefs WHERE id=".$tableid;
	$tableresult=mysql_query($querystatement,$dblink);
	if(!$tableresult) reportError(200,"Could Not retrieve Table Definition for id ".$tableid);
	$tablerecord=mysql_fetch_array($tableresult);
	
	if(isset($_GET["id"])){
		//editing
		if(!hasRights($tablerecord["editroleid"]))
			goURL($_SESSION["app_path"]."noaccess.php");
		$therecord=getRecords((integer) $_GET["id"]);
	} else {
		if(!hasRights($tablerecord["addroleid"]))
			goURL($_SESSION["app_path"]."noaccess.php");
		$therecord=setRecordDefaults();
	}
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
			goURL($backurl."#".$theid);
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
				$statusmessage="<div style=\"float:right;margin-top:-3px;\"><button type=\"button\" class=\"smallButtons\" onclick=\"document.location='".$_SERVER["REQUEST_URI"]."'\">add new</button></div>";
				$statusmessage.="Record Created";
			}
		break;
	}
}
?>