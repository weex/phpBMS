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

	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/invoices_functions.php");
	include("include/invoices_addedit_include.php");
	
	$pageTitle=$therecord["type"];
	
	$_SESSION["printing"]["tableid"]=3;
	$_SESSION["printing"]["theids"]=array($therecord["id"]);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("../../head.php")?>
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/invoice.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/choicelist.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js" type="text/javascript"></script>
<script language="JavaScript" src="javascript/invoice.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/datepicker.js" type="text/javascript"></script>
</head>
<body onLoad="initializePage()"><?php include("../../menu.php")?>
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="setLineItems();return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onClick="return false;" /></div>
<?php invoice_tabs("General",$therecord["id"]);?><div class="bodyline">
	<div id="topButtons">
		  <?php if($therecord["id"]){
		  		?><div id="printButton">
				<input name="doprint" type="button" value="print" accesskey="p" onClick="doPrint('<?php echo $_SESSION["app_path"]?>',<?php echo $therecord["id"]?>)" class="Buttons" />
			</div><?php 
			}//end if
			showSaveCancel(1); ?>
	</div>
	<h1 id="h1With<?php if(!$therecord["id"]) echo "out"?>Print"><?php echo $pageTitle ?></h1>
	
	 <fieldset id="fsAttributes">
		<legend>attributes</legend>
		
		<div id="attributesRight">
			<p>
				<label for="orderdate">order date</label><br />
				<?PHP field_datepicker("orderdate",$therecord["orderdate"],0,"Order date must be a valid date",Array("size"=>"11","maxlength"=>"11","tabindex"=>"10"),false);?>
			</p>
			<p>
				<label for="invoicedate">invoice date</label>
				<br />
				<?PHP field_datepicker("invoicedate",$therecord["invoicedate"],0,"Invoice date must be a valid date",Array("size"=>"11","maxlength"=>"11","tabindex"=>"11"),false);?>
			</p>
			
			<p>
				<label for="requireddate">required date</label>
				<br />
				<?PHP field_datepicker("requireddate",$therecord["requireddate"],0,"Required date must be a valid date",Array("size"=>"11","maxlength"=>"11","tabindex"=>"13"),false);?>
			</p>

			<p>
				<label for="shippeddate">shipped date</label>
				<br />
				<?PHP field_datepicker("shippeddate",$therecord["shippeddate"],0,"Shipped date must be a valid date.",Array("size"=>"11","maxlength"=>"11","tabindex"=>"14"),false); ?>			
			</p>
		</div>

		<div>
			<p>
				<label for="id">id</label>
				<br />
				<input name="id" id="id" type="text" value="<?php echo $therecord["id"]; ?>" size="11" maxlength="11" readonly="true" class="uneditable" tabindex="0">			
			</p>
			
			<p>
			<label for="type" class="important">type</label><br />
				<?PHP  if($therecord["type"]=="VOID" || $therecord["type"]=="Invoice") {?>
					<input id="type" name="type" type="text" value="<?php echo htmlQuotes($therecord["type"])?>" size="11" maxlength="11" readonly="true" class="uneditable important" tabindex=9 />
				<?php }else {
					$thechoices=array();
					$thechoices[]=array("name"=>"Quote","value"=>"Quote");
					$thechoices[]=array("name"=>"Order","value"=>"Order");
					if($_SESSION["userinfo"]["accesslevel"]>=30) $thechoices[]=array("name"=>"Invoice","value"=>"Invoice");
					$thechoices[]=array("name"=>"VOID","value"=>"VOID");
					basic_choicelist("type",$therecord["type"],$thechoices,array("onchange"=>"checkType(this)","class"=>"important","style"=>"width:90px","tabindex"=>"9"));
				}
				?><input type="hidden" id="oldType" name="oldType" value="<?php echo $therecord["type"]?>"/>
			</p>

			<p>
				<label for="ponumber">client PO#</label>
				<br />
				<input name="ponumber" id="ponumber" type="text" value="<?PHP echo htmlQuotes($therecord["ponumber"])?>" size="11" maxlength="64" tabindex=12 />
			</p>
		</div>
		
		<p>
			<strong>order status</strong><br />
			<input type="radio" name="status" id="statusOpen" value="Open" <?php if($therecord["status"]=="Open") echo "checked"?> class="radiochecks" align="baseline" tabindex="13"/><label for="statusOpen">open</label><br />
			<input type="radio" name="status" id="statusCommited" value="Committed" <?php if($therecord["status"]=="Committed") echo "checked"?> class="radiochecks" align="baseline" tabindex="13"/><label for="statusCommited">committed</label><br />
			<input type="radio" name="status" id="statusPacked" value="Packed" <?php if($therecord["status"]=="Packed") echo "checked"?> class="radiochecks" align="baseline" tabindex="13"/><label for="statusPacked">packed</label><br />
			<input type="radio" name="status" id="statusShipped" value="Shipped" <?php if($therecord["status"]=="Shipped") echo "checked"?> class="radiochecks" align="baseline" tabindex="13" onClick="setShipped()"/><label for="statusShipped">shipped/invoice</label>
		</p>
		
		<p>
			<label for="leadsource">lead source</label>
			<br />
			<?PHP choicelist("leadsource",$therecord["leadsource"],"leadsource",Array("tabindex"=>"14")); ?>			
		</p>
	</fieldset>
	
	<div id="fsTops">
		<fieldset >
			<legend><label for="ds-clientid">client</label></legend>
			<div class="important fauxP">
				  <?PHP autofill("clientid",$therecord["clientid"],2,"clients.id","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","if(clients.city!=\"\",concat(clients.city,\", \",clients.state),\"\")","clients.inactive!=1 AND clients.type=\"client\"",Array("size"=>"45","maxlength"=>"128","style"=>"","style"=>"font-weight:bold","tabindex"=>"1"),1,"The record must have a client chosen.") ?>
				  <script language="JavaScript" type="text/javascript">
					document.forms["record"]["clientid"].onchange=populateShipping;
				  </script>
				  <?php if($therecord["clientid"]){?>
				  <input name="viewclient" type="button" value="view client" onClick="viewClient('<?php echo getAddEditFile(2) ?>')" class="Buttons" tabindex="1" />
				  <?php }//end if?>
			</div>
		</fieldset>
		
		<fieldset>
			<legend><label for="address1">shipping address</label></legend>		
			<p>
				<input name="address1" id="address1" type="text" value="<?PHP echo htmlQuotes($therecord["address1"])?>" size="65" maxlength="128" tabindex=3 /><br />
				<input name="address2" id="address2" type="text"  value="<?PHP echo htmlQuotes($therecord["address2"])?>" size="65" maxlength="128" tabindex="4" />
			</p>
			<p class="cszP">
				<label for="city">city</label><br />
				<input name="city" type="text" id="city" value="<?php echo htmlQuotes($therecord["city"])?>" size="35" maxlength="64" tabindex="5" />			
			</p>
			<p class="cszP">
				<label for="state">state/prov</label><br />
				<input name="state" type="text" id="state" value="<?php echo htmlQuotes($therecord["state"])?>" size="2" maxlength="2" tabindex=6 />			
			</p>
			<p class="cszP">
				<label for="postalcode">zip/postal code</label><br />
				<input name="postalcode" type="text" id="postalcode" value="<?php echo htmlQuotes($therecord["postalcode"])?>" size="12" maxlength="15" tabindex=7 />
			</p>
			<p id="countryP">
				<label for="country">country</label><br />
				<input name="country" id="country" type="text" value="<?PHP echo htmlQuotes($therecord["country"])?>" size="45" maxlength="64" tabindex=8 />
			</p>
		</fieldset>

		<fieldset>
			<legend>web / confirmation number</legend>
			<p>
				<?PHP field_checkbox("weborder",$therecord["weborder"],0,array("tabindex"=>"14"));?>
				<input name="webconfirmationno" type="text" value="<?PHP echo $therecord["webconfirmationno"] ?>" size="41" maxlength="64" tabindex="14" />
			</p>
		</fieldset>
	</div>

<fieldset id="fsLineItems">
	<legend>line items</legend>
	
	<input type="hidden" name="thelineitems" id="thelineitems" value="" />
	<input id="lineitemschanged" name="lineitemschanged" type="hidden" value="0"/>
	<input id="unitcost" name="unitcost" type="hidden" value="0" />
	<input id="unitweight" name="unitweight" type="hidden" value="0"/>
	<input id="taxable" name="taxable" type="hidden" value="1"/>
	<input id="imgpath" name="imgpath" type="hidden" value="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image" />
	
	<table border="0" cellpadding="0" cellspacing="0" id="LITable">
		<tr id="LIHeader">
			<th nowrap class="queryheader" align="left">part number</td>
			<th nowrap class="queryheader" align="left">name</td>
			<th nowrap class="queryheader" align="left" width="90%">memo</td>
			<th align="right" nowrap class="queryheader">price</td>
			<th align="qty" nowrap class="queryheader">qty.</td>
			<th align="right" nowrap class="queryheader">extended</td>
			<th nowrap class="queryheader">&nbsp;</td>
		</tr>
		<?php if($therecord["type"]!="Invoice"){?>
		<tr id="LIAdd">
			<td nowrap>
			<?PHP autofill("partnumber","",4,"products.id","products.partnumber","products.partname","products.status=\"In Stock\" and products.inactive=0",Array("size"=>"16","maxlength"=>"32","tabindex"=>"15"),false,"") ?>
			<script language="JavaScript" type="text/javascript">
					document.forms["record"]["partnumber"].onchange=populateLineItem;
			</script>
			</td>
			<td nowrap>
			<?PHP autofill("partname","",4,"products.id","products.partname","products.partnumber","products.status=\"In Stock\" and products.inactive=0",Array("size"=>"20","maxlength"=>"128","tabindex"=>"16"),false,"") ?>
			<script language="JavaScript" type="text/javascript">
					document.forms["record"]["partname"].onchange=populateLineItem;
			</script>
			</td>
			<td width="100%"><input name="memo" type="text" id="memo" size="12" maxlength="255" tabindex="17" /></td>
			<td align="right" nowrap><input name="price" type="text" id="price" value="<?php echo currencyFormat(0)?>" size="10" maxlength="16" onchange="calculateExtended()" class="fieldCurrency"  tabindex="18"  /></td>
			<td align="center" nowrap><input name="qty" type="text" id="qty" value="1" size="5" maxlength="16" onchange="calculateExtended()" tabindex="19"  /></td>
			<td align="right" nowrap><input name="extended" type="text" id="extended" class="uneditable fieldCurrency" value="<?php echo currencyFormat(0)?>" size="12" maxlength="16" readonly="true" /></td>
			<td nowrap align="center"><button type="button" onclick="addLine(this.parentNode);" tabindex="20" class="graphicButtons buttonPlus" title="Add Line Item"><span>+</span></button></td>
		</tr><?PHP }//end if
  	$lineitemsresult=getLineItems($therecord["id"]);
		
	if($lineitemsresult) {
	?><tr id="LISep"><td colspan="7"></td></tr><?php 
	while($lineitem=mysql_fetch_array($lineitemsresult)){
  ?><tr class="lineitems" id="LIN<?php echo $lineitem["id"]?>">
			<td nowrap class="small" valign="top"><strong><?PHP if($lineitem["partnumber"]) echo htmlspecialchars($lineitem["partnumber"]); else echo "&nbsp;";?></strong></td>
			<td class="small" valign="top"><strong><?PHP if($lineitem["partname"]) echo htmlspecialchars($lineitem["partname"]); else echo "&nbsp;";?></strong></td>
			<td class="tiny" valign="top"><?PHP if($lineitem["memo"]) echo htmlspecialchars($lineitem["memo"]); else echo "&nbsp;"?></td>
			<td align="right" nowrap class="small" valign="top"><?PHP echo $lineitem["unitprice"]?></td>
			<td align="center" nowrap class="small" valign="top"><?PHP echo $lineitem["quantity"]?></td>
			<td align="right" nowrap class="small" valign="top"><?PHP echo $lineitem["extended"]?></td>
			<td valign="top"  align="center"><span class="LIRealInfo">
					<?PHP echo $lineitem["productid"]?>[//]
					<?PHP echo $lineitem["unitcost"]?>[//]
					<?PHP echo $lineitem["unitweight"]?>[//]
					<?PHP echo $lineitem["numprice"]?>[//]
					<?PHP echo $lineitem["quantity"]?>[//]
					<?PHP echo htmlspecialchars($lineitem["memo"])?>[//]
					<?PHP echo $lineitem["taxable"]?>
				</span>
				<?php if($therecord["type"]=="Invoice") echo "&nbsp;"; else {?><button type="button" class="graphicButtons buttonMinus" onClick="return deleteLine(this)" tabindex="21" title="Remove line item"><span>-</span></button><?php } ?>
			</td>
		</tr>
  <?PHP } } ?><tr id="LITotals">
		<td colspan="2" rowspan="5" valign="bottom">
			<div class="fauxP">
				<label for="ds-discountid">discount / promotion</label><br />
				<?PHP autofill("discountid",$therecord["discountid"],25,"discounts.id","discounts.name"," if(discounts.type+0=1,concat(discounts.value,\"%\"),format(discounts.value,2))","discounts.inactive=0",Array("size"=>"34","maxlength"=>"64","tabindex"=>"22"),0) ?>
				<input type="hidden" id="discount" name="discount" value="<?php getDiscount($therecord["discountid"])?>" />			
			</div>
						
			<div class="fauxP">
				<label for="ds-taxareaid">tax area</label><br />
				<?PHP autofill("taxareaid",$therecord["taxareaid"],6,"tax.id","tax.name","concat(tax.percentage,\"%\")","",Array("size"=>"20","maxlength"=>"64","tabindex"=>"22"),0) ?>
				<?PHP field_percentage("taxpercentage",$therecord["taxpercentage"],5,0,"Tax percentage must be a valid percentage.",Array("size"=>"7","maxlength"=>"9","onchange"=>"changeTaxPercentage()","tabindex"=>"22")); ?>
			</div>
		</td>
		<td>&nbsp;</td>
		<td colspan="2" class="invoiceTotalLabels"><label for="discountamount">discount</label><input type="hidden" id="totalBD" name="totalBD" value="<?php echo $therecord["totaltni"]+$therecord["discountamount"]?>" /></td>
		<td><input name="discountamount" id="discountamount" type="text" value="<?PHP echo $therecord["discountamount"]?>" size="12" maxlength="15" onchange="clearDiscount();calculateTotal();" class="fieldCurrency fieldTotal" tabindex="22"/></td>
		<td>&nbsp;</td>  	
  </tr><tr>
		<td>&nbsp;</td>
		<td colspan="2" class="invoiceTotalLabels"><label for="totaltni">subtotal</label></td>
		<td><input class="uneditable fieldCurrency fieldTotal" name="totaltni" id="totaltni" type="text" value="<?PHP echo $therecord["totaltni"]?>" size="12"  maxlength="15" readonly="true" onchange="calculateTotal();" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan="2" class="invoiceTotalLabels"><label for="tax">tax</label></td>
		<td><input name="tax" id="tax" type="text" value="<?PHP echo $therecord["tax"]?>" size="12" maxlength="15" onchange="changeTaxAmount()" class="fieldCurrency fieldTotal" tabindex="22" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td rowspan=2>
			<p>
				<label for="totalweight">total weight <em>(lbs)</em></label><br/>
				<input id="totalweight" name="totalweight" type="text" value="<?PHP echo $therecord["totalweight"]?>" size="12" maxlength="15" readonly="true" class="uneditable" />			
			</p>
		</td>
		<td colspan="2" class="invoiceTotalLabels"><label for="shipping">shipping</label></td>
		<td><input name="shipping" id="shipping" type="text" value="<?PHP echo $therecord["shipping"]?>" size="12" maxlength="15" onchange="calculateTotal();" class="fieldCurrency fieldTotal" tabindex="23" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" class="invoiceTotalLabels"><label for="totalti" class="important">total</label></td>
		<td>
			<input class="uneditable fieldCurrency important fieldTotal" name="totalti" id="totalti" type="text" value="<?PHP echo $therecord["totalti"]?>" size="12" maxlength="15" onchange="calculateTotal();"  readonly="true"/ >
			<input id="totalcost" name="totalcost" type="hidden" value="<?PHP echo $therecord["totalcost"] ?>" />
			<input id="totaltaxable" name="totaltaxable" type="hidden" value="<?PHP echo $therecord["totaltaxable"] ?>" />
		</td>
		<td>&nbsp;</td>
	</tr>
</table></fieldset>
	
<fieldset id="fsPayment">
	<legend>payment</legend>
	<p>
		<label for="amountdue" class="important">amount due</label><br />
		<input id="amountdue" name="amountdue" type="text" value="<?PHP echo $therecord["amountdue"] ?>" size="11" maxlength="11" onchange="calculatePaidDue();" class="important fieldCurrency" tabindex="24"/>
	</p>

	<p>
		<label for="amountpaid" class="important">amount paid</label><br />
		<input name="amountpaid" id="amountpaid" type="text" value="<?PHP echo $therecord["amountpaid"]?>" size="11" maxlength="11" onchange="calculatePaidDue();"  class="important fieldCurrency" tabindex="24"/>
		<input name="Button" type="button" class="Buttons" onClick="this.form['amountpaid'].value=this.form['totalti'].value;calculatePaidDue();" value="pay in full"  tabindex="24"/>
	</p>	
	<?php if($_SESSION["userinfo"]["accesslevel"]>=20){ ?>
	<p>
		<label for="paymentmethod">payment method</label><br />
		<?PHP choicelist("paymentmethod",$therecord["paymentmethod"],"paymentmethod",array("tabindex"=>"24")); ?>
		<script language="JavaScript" type="text/javascript">document.forms["record"]["paymentmethod"].onchange2=new Function("showPaymentOptions()");</script>
	</p>
	<div id="checkpaymentinfo">
		<p>
			<label for="checkno">check number</label><br />
			<input name="checkno" type="text" id="checkno" value="<?PHP echo htmlQuotes($therecord["checkno"])?>" size="24" maxlength="32" tabindex="25"/>
		</p>

		<p><label for="bankname">bank name</label><br />
			<input id="bankname" name="bankname" type="text" value="<?PHP echo htmlQuotes($therecord["bankname"]) ?>" size="24" maxlength="64" tabindex="26"/>
		</p>
	</div>	
	<div id="ccpaymentinfo">
		<p id="fieldCCNumber">
			<label for="ccnumber">card number</label><br />
			<input name="ccnumber" type="text" id="ccnumber" value="<?PHP echo htmlQuotes($therecord["ccnumber"]) ?>" size="28" maxlength="40" tabindex="27"/>			
		</p>
		<p id="fieldCCExpiration">
			<label for="ccexpiration">expiration</label><br />
			<input name="ccexpiration" id="ccexpiration" type="text"  value="<?PHP echo htmlQuotes($therecord["ccexpiration"]) ?>" size="8" maxlength="10" tabindex="28" />
		</p>
		<p>
			<label for="ccverification">verification/pin</label><br />
			<input id="ccverification" name="ccverification" type="text"  value="<?PHP echo htmlQuotes($therecord["ccverification"]) ?>" size="8" maxlength="7" tabindex="29" />		
		</p>				
	</div><?php }// end if accesslevel?>
</fieldset>
<div id="fsShipping">
<fieldset>
	<legend>shipping</legend>
	<p>
		<label for="ds-shippingmethod">ship via</label><br />
		<?PHP choicelist("shippingmethod",$therecord["shippingmethod"],"shippingmethod",array("tabindex"=>"30")); ?>&nbsp;
	    <input name="estimate" type="button" class="Buttons" id="estimate" value="calculate shipping" onClick="estimateShipping()" tabindex="30"/>
	</p>
	<p class="notes">
		<strong>Note:</strong> The "calculate shipping" button will retrieve shipping costs for UPS only.<br />
		Modifying the UPS entry names in the ship via will disable the shipping calculator.)
	</p>
	<p>
		<label for="trackingno">tracking number</label><br />
		<input id="trackingno" name="trackingno" type="text" value="<?PHP echo htmlQuotes($therecord["trackingno"]) ?>" size="50" maxlength="64" tabindex="30" />
	</p>
</fieldset>
</div>

<fieldset id="fsInstructions">
	<legend>instructions</legend>
	<p>
		<label for="specialinstructions">special instructions</label> 
		<span class="notes"><em>(will not print on invoice)</em></span><br />
		<textarea id="specialinstructions" name="specialinstructions" cols="45" rows="3"tabindex="37"><?PHP echo $therecord["specialinstructions"]?></textarea>	
	</p>
	<p>
		<label for="printedinstructions">printed instructions</label><br />
		<textarea id="printedinstructions" name="printedinstructions" cols="45" rows="3" tabindex="38"><?PHP echo $therecord["printedinstructions"]?></textarea>	
	</p>
</fieldset>		
<?php include("../../include/createmodifiedby.php"); ?>
<?PHP if($therecord["type"]=="VOID" || $therecord["type"]=="Invoice"){?>
<script language="JavaScript" type="text/javascript">disableSaves(document.forms["record"]);</script>
<?PHP }// end if ?>
</div><?php include("../../footer.php");?>
</form></body>
</html>