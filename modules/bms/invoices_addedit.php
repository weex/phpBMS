<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/invoices_functions.php");
	include("include/invoices_addedit_include.php");
	
	$pageTitle="Order/Invoice";
	
	$_SESSION["printing"]["tableid"]=3;
	$_SESSION["printing"]["theids"]=array($therecord["id"]);
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="../../common/javascript/choicelist.js"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js"></script>
<script language="JavaScript" src="javascript/invoice.js"></script>
<script language="JavaScript" src="../../common/javascript/datepicker.js"></script>
</head>
<body onLoad="initializePage()"><?php include("../../menu.php")?>
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="setLineItems();if(validateForm(this)) return checkShipping(); else return false;"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
<?php invoice_tabs("General",$therecord["id"]);?><div class="bodyline">
	<div style="float:right;width:225px;margin-bottom:0px;padding-bottom:0px;">
		  <?php if($therecord["id"]){?>
			<div align="right" style="float:left;padding-left:0px;padding-right:0px;">
				<input name="doprint" type="button" value="print" accesskey="p" onClick="doPrint(<?php echo $therecord["id"]?>)" class="Buttons" style="width:75px;"/>
			</div>
		  <?php }//end if?>
		<?php showSaveCancel(1); ?>
		  </script>
	</div>
	<div style="margin-right:<?php if($therecord["id"]) echo 230; else echo 160 ?>px;"><h1><?php echo $pageTitle ?></h1></div>		
	
	 <fieldset style="clear:both;float:right;width:220px;">
		<legend>attributes</legend>
		<div align="left" style="float:right;padding-top:0px;">
			<label for="orderdate">
				order date<br />
				<?PHP field_datepicker("orderdate",$therecord["orderdate"],0,"Order date must be a valid date",Array("size"=>"11","maxlength"=>"11","tabindex"=>"10"),false);?>
			</label>
			<label for="invoicedate">
				invoice date<br />
				<?PHP field_datepicker("invoicedate",$therecord["invoicedate"],0,"Invoice date must be a valid date",Array("size"=>"11","maxlength"=>"11","tabindex"=>"11"),false);?>
			</label>
			<label for="requireddate">
				required date<br />
				<?PHP field_datepicker("requireddate",$therecord["requireddate"],0,"Required date must be a valid date",Array("size"=>"11","maxlength"=>"11","tabindex"=>"13"),false);?>
			</label for="">
			<label for="shippeddate">
				shipped date<br />
				<?PHP field_datepicker("shippeddate",$therecord["shippeddate"],0,"Shipped date must be a valid date.",Array("size"=>"11","maxlength"=>"11","tabindex"=>"35"),false); ?>
			</label>
		</div>
		<div align="left" style="padding-top:0px;">
			<label for="id">
				id<br />
				<input name="id" id="id" type="text" value="<?php echo $therecord["id"]; ?>" size="11" maxlength="11" readonly="true" class="uneditable" style="" tabindex="-1">
			</label>
			<label for="type" class="important">
				type<br />
				<?PHP  if($therecord["type"]=="VOID" || $therecord["type"]=="Invoice") {?>
					<input name="status" type="text" value="<?php echo htmlQuotes($therecord["type"])?>" size="11" maxlength="11" readonly="true" class="uneditable" style="width:90px;font-weight:bold" tabindex=9 />
				<?php }else 
					basic_choicelist("type",$therecord["type"],Array(Array("name"=>"Quote","value"=>"Quote"),Array("name"=>"Order","value"=>"Order"),Array("name"=>"Invoice","value"=>"Invoice"),Array("name"=>"VOID","value"=>"VOID")),Array("onChange"=>"checkStatus(this)","class"=>"important","style"=>"width:90px","tabindex"=>"9"));
				?>
			</label>
			<label for="ponumber">
				client PO#<br />
				<input name="ponumber" id="ponumber" type="text" value="<?PHP echo htmlQuotes($therecord["ponumber"])?>" size="11" maxlength="64" tabindex=12 />
			</label>			
		</div>
		<div style="padding-top:0px;">
			<strong>order status</strong><br />
			<label for="statusOpen" style="padding:0px;padding-left:4px;"><input type="radio" name="status" id="statusOpen" value="Open" <?php if($therecord["status"]=="Open") echo "checked"?> class="radiochecks" align="baseline" tabindex="70"/> open</label>
			<label for="statusCommited" style="padding:0px;padding-left:4px;"><input type="radio" name="status" id="statusCommited" value="Committed" <?php if($therecord["status"]=="Committed") echo "checked"?> class="radiochecks" align="baseline" tabindex="70"/> committed</label>
			<label for="statusPacked" style="padding:0px;padding-left:4px;"><input type="radio" name="status" id="statusPacked" value="Packed" <?php if($therecord["status"]=="Packed") echo "checked"?> class="radiochecks" align="baseline" tabindex="70"/> packed</label>
			<label for="statusShipped" style="padding:0px;padding-left:4px;"><input type="radio" name="status" id="statusShipped" value="Shipped" <?php if($therecord["status"]=="Shipped") echo "checked"?> class="radiochecks" align="baseline" tabindex="70" onClick="setShipped()"/> shipped/invoice</label>
		</div>
		<div style="padding-bottom:4px;">
			<label for="leadsource" style="text-align:left">
			lead source<br />
			<?PHP choicelist("leadsource",$therecord["leadsource"],"leadsource",Array("style"=>"width:98%;","tabindex"=>"14")); ?>
			</label>
		</div>
	</fieldset>
	
	<fieldset>
		<legend><label for="ds-clientid">client</label></legend>
		<div class="important" style="margin-bottom:13px;">
			  <?PHP autofill("clientid",$therecord["clientid"],2,"clients.id","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","if(clients.city!=\"\",concat(clients.city,\", \",clients.state),\"\")","clients.inactive!=1 AND clients.type=\"client\"",Array("size"=>"45","maxlength"=>"128","style"=>"","style"=>"font-weight:bold","tabindex"=>"1"),1,"The record must have a client chosen.") ?>
			  <script language="JavaScript">
				document.forms["record"]["clientid"].onchange=populateShipping;
			  </script>
			  <?php if($therecord["clientid"]){?>
			  <input name="viewclient" type="button" value="view client" onClick="viewClient('<?php echo getAddEditFile(2) ?>')" class="Buttons" tabindex="2" />
			  <?php }//end if?>
		</div>
	</fieldset>
	<fieldset>
		<legend><label for="address1">shipping address</label></legend>		
		<div>
		<input name="address1" id="address1" type="text" style=";margin-bottom:2px;" value="<?PHP echo htmlQuotes($therecord["address1"])?>" size="70" maxlength="128" tabindex=3 />
		<br />
		<input name="address2" id="address2" type="text"  value="<?PHP echo htmlQuotes($therecord["address2"])?>" size="70" maxlength="128" tabindex="4" />
		</div>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td nowrap><label for="city">
						city<br />
						<input name="city" type="text" id="city" value="<?php echo htmlQuotes($therecord["city"])?>" size="35" maxlength="64" tabindex="5" />
					</label></td>
				<td nowrap ><label for="state">
						state/prov<br />
						<input name="state" type="text" id="state" value="<?php echo htmlQuotes($therecord["state"])?>" size="2" maxlength="2" tabindex=6 />
					</label></td>
				<td nowrap><label for="postalcode">
					zip/postal code<br />
						<input name="postalcode" type="text" id="postalcode" value="<?php echo htmlQuotes($therecord["postalcode"])?>" size="12" maxlength="15" tabindex=7 />
					</label>
				</td>
				</tr>
				<tr><td colspan="3"><label for="country">
				country<br />
				<input name="country" id="country" type="text" value="<?PHP echo htmlQuotes($therecord["country"])?>" size="45" maxlength="64" tabindex=8 />
				</label></td></tr>
			</table>
	</fieldset>
	<fieldset>
		<legend>web / confirmation number</legend>
		<div>
			<?PHP field_checkbox("weborder",$therecord["weborder"],0,array("tabindex"=>"31"));?>
			<input name="webconfirmationno" type="text" value="<?PHP echo $therecord["webconfirmationno"] ?>" size="35" maxlength="64" tabindex="36" />
		</div>
	</fieldset>
	
<fieldset style="clear:both;">
	<legend>line items / tax</legend><input type="hidden" name="thelineitems" id="thelineitems" value="" />
	<input id="lineitemschanged" name="lineitemschanged" type="hidden" value="0"/>
	<input id="unitcost" name="unitcost" type="hidden" value="0" />
	<input id="unitweight" name="unitweight" type="hidden" value="0"/>
	<input id="taxable" name="taxable" type="hidden" value="1"/>
	<input id="imgpath" name="imgpath" type="hidden" value="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>" />
	<table border="0" cellpadding="0" cellspacing="0"  style="border:0px;padding:0px;clear:both;">
		<tr id="LIHeader">
			<th nowrap class="queryheader" align="left">part number</td>
			<th nowrap class="queryheader" align="left">name</td>
			<th nowrap class="queryheader" align="left">memo</td>
			<th align="right" nowrap class="queryheader">price</td>
			<th align="center" nowrap class="queryheader">qty.</td>
			<th align="right" nowrap class="queryheader">extended</td>
			<th nowrap class="queryheader">&nbsp;</td>
		</tr>
		<tr id="LIAdd">
			<td nowrap class="lineitemsLeft">
			<?PHP autofill("partnumber","",4,"products.id","products.partnumber","products.partname","products.status=\"In Stock\" and products.inactive=0",Array("size"=>"11","maxlength"=>"32","tabindex"=>"15"),false,"") ?>
			<script language="JavaScript">
					document.forms["record"]["partnumber"].onchange=populateLineItem;
			</script>
			</td>
			<td nowrap>
			<?PHP autofill("partname","",4,"products.id","products.partname","products.partnumber","products.status=\"In Stock\" and products.inactive=0",Array("size"=>"20","maxlength"=>"64","style"=>"border-left-width:0px;","tabindex"=>"16"),false,"") ?>
			<script language="JavaScript">
					document.forms["record"]["partname"].onchange=populateLineItem;
			</script>
			</td>
			<td width="90%"><input name="memo" type="text" id="memo" size="12" maxlength="255" style="width:100%;border-left-width:0px;" tabindex="17" /></td>
			<td align="right" nowrap><input name="price" type="text" id="price" value="$0.00" size="8" maxlength="16" onChange="calculateExtended()" style="text-align:right;" tabindex="18"  /></td>
			<td align="center" nowrap><input name="qty" type="text" id="qty" value="1" size="2" maxlength="16" onChange="calculateExtended()" style="text-align:center; border-left:0px;border-right:0px;" tabindex="19"  /></td>
			<td align="right" nowrap><input name="extended" type="text" id="extended" class="uneditable" value="$0.00" size="8" maxlength="16" readonly="true" style="text-align:right;" /></td>
			<td nowrap align="center" class="lineitemsRight lineitemsBottom"><button type="button" class="invisibleButtons" onClick="addLine(this.parentNode);" tabindex="20" ><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-plus.png" align="middle" alt="+" width="16" height="16" border="0" /></button></td></tr><?PHP 
  	$lineitemsresult=getLineItems($therecord["id"]);
		
	if($lineitemsresult) {
	?><tr id="LISep"><td colspan="7" style="font-size:1px;padding:0px;" class="dottedline lineitemsRight lineitemsLeft">&nbsp;</td></tr><?php 
	while($lineitem=mysql_fetch_array($lineitemsresult)){
  ?><tr class="lineitems" id="LIN<?php echo $lineitem["id"]?>">
			<td nowrap class="small lineitemsLeft" valign="top"><strong><?PHP if($lineitem["partnumber"]) echo $lineitem["partnumber"]; else echo "&nbsp;";?></strong></td>
			<td class="small" valign="top"><strong><?PHP if($lineitem["partname"]) echo $lineitem["partname"]; else echo "&nbsp;";?></strong></td>
			<td class="tiny" valign="top"><?PHP if($lineitem["memo"]) echo $lineitem["memo"]; else echo "&nbsp;"?></td>
			<td align="right" nowrap class="small" valign="top"><?PHP echo $lineitem["unitprice"]?></td>
			<td align="center" nowrap class="small" valign="top"><?PHP echo $lineitem["quantity"]?></td>
			<td align="right" nowrap class="small" valign="top"><?PHP echo $lineitem["extended"]?></td>
			<td valign="top" style="padding:0px;" align="center"><span style="display:none;">
					<?PHP echo $lineitem["productid"]?>[//]
					<?PHP echo $lineitem["unitcost"]?>[//]
					<?PHP echo $lineitem["unitweight"]?>[//]
					<?PHP echo $lineitem["numprice"]?>[//]
					<?PHP echo $lineitem["quantity"]?>[//]
					<?PHP echo $lineitem["memo"]?>[//]
					<?PHP echo $lineitem["taxable"]?>
				</span>
				<button type="button" class="invisibleButtons" onClick="return deleteLine(this)" tabindex="21"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-minus.png" align="middle" alt="-" width="16" height="16" border="0" /></button>
			</td>
		</tr>
  <?PHP } } ?><tr id="LITotals">
		<td colspan="2">&nbsp;</td>
		<td >&nbsp;</td>
		<td colspan="2" class="invoiceTotalLabels"><label for="discountamount" style="margin:0px;padding:0px;">discount</label><input type="hidden" id="totalBD" name="totalBD" value="<?php echo $therecord["totaltni"]+$therecord["discountamount"]?>" /></td>
		<td><input name="discountamount" id="discountamount" type="text" value="<?PHP echo $therecord["discountamount"]?>" size="8" maxlength="15" onChange="calculateTotal();" style="text-align:right;" tabindex="22"/></td>
		<td>&nbsp;</td>  	
  </tr><tr>
		<td colspan="2" rowspan=4 valign="bottom">
			<label for="ds-taxareaid" style="padding-bottom:0px;">tax area</label>
			<div style="padding-top:0px;">
				<?PHP autofill("taxareaid",$therecord["taxareaid"],6,"tax.id","tax.name","concat(tax.percentage,\"%\")","",Array("size"=>"20","maxlength"=>"64","tabindex"=>"22"),0) ?>
				<?PHP field_percentage("taxpercentage",$therecord["taxpercentage"],5,0,"Tax percentage must be a valid percentage.",Array("size"=>"7","maxlength"=>"9","onChange"=>"changeTaxPercentage()","tabindex"=>"22")); ?>
				<script language="JavaScript">document.forms["record"]["taxareaid"].onchange=getPercentage;</script>
			</div>			
			<label for="totalweight" style="padding-bottom:0px;">total weight <em>(lbs)</em><br />
				<input id="totalweight" name="totalweight" type="text" value="<?PHP echo $therecord["totalweight"]?>" size="9" maxlength="15" readonly="true" class="uneditable" style="text-align:right;" />			
			</label>
		</td>
		<td>&nbsp;</td>
		<td colspan="2" class="invoiceTotalLabels"><label for="totaltni" style="margin:0px;padding:0px;">subtotal</label></td>
		<td><input class="uneditable" name="totaltni" id="totaltni" type="text" value="<?PHP echo $therecord["totaltni"]?>" size="8" maxlength="15" readonly="true" onChange="calculateTotal();" style="text-align:right;" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan="2" class="invoiceTotalLabels"><label for="tax" style="margin:0px;padding:0px;">tax</label></td>
		<td><input name="tax" id="tax" type="text" value="<?PHP echo $therecord["tax"]?>" size="8" maxlength="15" onChange="changeTaxAmount()" style="text-align:right;" tabindex="22" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan="2" class="invoiceTotalLabels"><label for="shipping" style="margin:0px;padding:0px;">shipping</label></td>
		<td><input name="shipping" id="shipping" type="text" value="<?PHP echo $therecord["shipping"]?>" size="8" maxlength="15" onChange="calculateTotal();" style="text-align:right;" tabindex="23" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan="2" class="invoiceTotalLabels"><label for="totalti" style="margin:0px;padding:0px;" class=important>total</label></td>
		<td>
			<input class="uneditable" name="totalti" id="totalti" type="text" value="<?PHP echo $therecord["totalti"]?>" size="8" maxlength="15" onChange="calculateTotal();"  readonly="true" style="text-align:right;font-weight:bold"/ >
			<input id="totalcost" name="totalcost" type="hidden" value="<?PHP echo $therecord["totalcost"] ?>" />
			<input id="totaltaxable" name="totaltaxable" type="hidden" value="<?PHP echo $therecord["totaltaxable"] ?>" />
		</td>
		<td>&nbsp;</td>
	</tr>
</table></fieldset>
	
<fieldset style="float:right;width:250px;margin-top:0px;padding-top:0px;">
	<legend>payment</legend>
	<label for="amountdue" class="important">amount due<br />
		<input id="amountdue" name="amountdue" type="text" value="<?PHP echo $therecord["amountdue"] ?>" size="11" maxlength="11" onChange="calculatePaidDue();" style="text-align:right;" class="important" tabindex="32"/>
	</label>
	<label for="amountpaid" class="important" style="padding-bottom:0px;margin-bottom:0px;">amount paid</label>
	<div style="padding-top:0px;margin-top:0px">
		<input name="amountpaid" id="amountpaid" type="text" value="<?PHP echo $therecord["amountpaid"]?>" size="11" maxlength="11" onChange="calculatePaidDue();" style="text-align:right;" class="important" tabindex="30"/>
		<input name="Button" type="button" class="Buttons" onClick="this.form['amountpaid'].value=this.form['totalti'].value;calculatePaidDue();" value="pay in full" style="margin-left:3px;" tabindex="31"/>
	</div>
	<label for="paymentmethod">
		payment method<br />
		<?PHP choicelist("paymentmethod",$therecord["paymentmethod"],"paymentmethod",array("tabindex"=>"24")); ?>
		  <script language="JavaScript">document.forms["record"]["paymentmethod"].onchange2=new Function("showPaymentOptions()");</script>
	</label>
	<div style="display:none;" id="checkpaymentinfo">
		<label for="checkno">check number<br />
			<input name="checkno" type="text" id="checkno" value="<?PHP echo htmlQuotes($therecord["checkno"])?>" size="24" maxlength="32" tabindex="25"/>
		</label>		
		<label for="bankname">bank name<br />
			<input name="bankname" type="text" value="<?PHP echo htmlQuotes($therecord["bankname"]) ?>" size="24" maxlength="64" style="width:98%" tabindex="26"/>
		</label>
	</div>	
	<div style="padding:0px;display:none;" id="ccpaymentinfo">
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<label for="ccnumber">
						card number<br />
						<input name="ccnumber" type="text" id="ccnumber" value="<?PHP echo htmlQuotes($therecord["ccnumber"]) ?>" size="28" maxlength="40" tabindex="27"/>
					</label>
				</td>
				<td>
					<label for="ccexpiration">
						expiration<br />
						<input name="ccexpiration" id="ccexpiration" type="text"  value="<?PHP echo htmlQuotes($therecord["ccexpiration"]) ?>" size="8" maxlength="10" tabindex="28" />
					</label>
				</td>
			</tr>
		</table>
		<label for="ccverification">verification/pin<br />
			<input id="ccverification" name="ccverification" type="text"  value="<?PHP echo htmlQuotes($therecord["ccverification"]) ?>" size="8" maxlength="7" tabindex="29" />
		</label>		
	</div>
</fieldset>
<fieldset>
	<legend>shipping</label></legend>
	<label for="ds-shippingmethod"style="padding-top:0px;">ship via<br />
	   <?PHP choicelist("shippingmethod",$therecord["shippingmethod"],"shippingmethod",array("tabindex"=>"26")); ?>&nbsp;
	   <input name="estimate" type="button" class="Buttons" id="estimate" value="calculate shipping" onClick="estimateShipping()" tabindex="33"/>
	</label>
	<div class="small"><em>
		(The "calculate shipping" button will retrieve shipping costs for UPS only.  Modifying the UPS entry names in the ship via
		will disable the shipping calculator.)
	</em></div>
	<label for="trackingno" style="margin-top:8px;">
		tracking number<br />
		<input id="trackingno" name="trackingno" type="text" value="<?PHP echo htmlQuotes($therecord["trackingno"]) ?>" size="50" maxlength="64" tabindex="35" />
	</label>
</fieldset>
	
<fieldset style="clear:both">
<legend>instructions</legend>
	<label for="specialinstructions">
		special instructions <em>(will not print on invoice)</em><br />
		<textarea id="specialinstructions" name="specialinstructions" cols="45" rows="3" style="width:99%" tabindex="37"><?PHP echo $therecord["specialinstructions"]?></textarea>	
	</label>
	<label for="printedinstructions">
		printed instructions<br />
		<textarea id="printedinstructions" name="printedinstructions" cols="45" rows="3"  style="width:99%" tabindex="38"><?PHP echo $therecord["printedinstructions"]?></textarea>	
	</label>
</fieldset>		
<?php include("../../include/createmodifiedby.php"); ?>
<?PHP if($therecord["type"]=="VOID" || $therecord["type"]=="Invoice"){?>
<script language="JavaScript">disableSaves(document.forms["record"]);</script>
<?PHP }// end if ?>
</div><?php include("../../footer.php");?>
</form></body>
</html>