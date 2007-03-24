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
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/invoice.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/choicelist.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js" type="text/javascript"></script>
<script language="JavaScript" src="javascript/invoice.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/datepicker.js" type="text/javascript"></script>
<?php $shippingMethods=getShipping($dblink)?>
<?php $paymentMethods=getPayments($dblink)?>
</head>
<body onLoad="initializePage()"><?php include("../../menu.php")?>
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onsubmit="setLineItems();return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onClick="return false;" /></div>
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
	<div id="fsAttributes">
		<fieldset >
			<legend>attributes</legend>
			
			<div id="attributesRight">
				<p>
					<label for="orderdate">order date</label><br />
					<?php fieldDatePicker("orderdate",$therecord["orderdate"],0,"Order date must be a valid date",Array("size"=>"11","maxlength"=>"11","tabindex"=>"10"),false);?>
				</p>
				<p>
					<label for="invoicedate">invoice date</label>
					<br />
					<?php fieldDatePicker("invoicedate",$therecord["invoicedate"],0,"Invoice date must be a valid date",Array("size"=>"11","maxlength"=>"11","tabindex"=>"11"),false);?>
				</p>
				
				<p>
					<label for="requireddate">required date</label>
					<br />
					<?php fieldDatePicker("requireddate",$therecord["requireddate"],0,"Required date must be a valid date",Array("size"=>"11","maxlength"=>"11","tabindex"=>"13"),false);?>
				</p>
			</div>
		
			<div>
				<p>
					<label for="id">id</label>
					<br />
					<input name="id" id="id" type="text" value="<?php echo $therecord["id"]; ?>" size="11" maxlength="11" readonly="true" class="uneditable" tabindex="0"  />
				</p>
				
				<p>
				<label for="type" class="important">type</label><br />
					<?php  if($therecord["type"]=="VOID" || $therecord["type"]=="Invoice") {?>
						<input id="type" name="type" type="text" value="<?php echo htmlQuotes($therecord["type"])?>" size="11" maxlength="11" readonly="true" class="uneditable important" tabindex=9 />
					<?php }else {
						$thechoices=array();
						$thechoices[]=array("name"=>"Quote","value"=>"Quote");
						$thechoices[]=array("name"=>"Order","value"=>"Order");
						if(hasRights(30)) $thechoices[]=array("name"=>"Invoice","value"=>"Invoice");
						$thechoices[]=array("name"=>"VOID","value"=>"VOID");
						fieldBasicList("type",$therecord["type"],$thechoices,array("onchange"=>"checkType(this)","class"=>"important","style"=>"width:90px","tabindex"=>"9"));
					}
					?><input type="hidden" id="oldType" name="oldType" value="<?php echo $therecord["type"]?>"/>
				</p>
		
				<p>
					<label for="ponumber">client PO#</label>
					<br />
					<input name="ponumber" id="ponumber" type="text" value="<?php echo htmlQuotes($therecord["ponumber"])?>" size="11" maxlength="64" tabindex=12 />
				</p>
			</div>			
		</fieldset>
		
		<fieldset>
			<legend>Status</legend>
			<p>
				<label for="statusid" class="important">current status</label><br />
				<?php displayStatusDropDown($therecord["statusid"],$dblink)?>
				<input type="hidden" id="statuschanged" name="statuschanged" value="<?php if($therecord["id"]=="") echo "1"; else echo "0"?>" />
			</p>
			<p>
				<label for="statusdate">status date</label><br />
				<?PHP fieldDatePicker("statusdate",$therecord["statusdate"],0,"Status date must be a valid date",Array("size"=>"11","maxlength"=>"11","tabindex"=>"13","onchange"=>"updateStatusChange();"),false);?>
			</p>
			<p>
				<label for="assignedtoid">assigned to</label><br />
				<?php fieldAutofill("assignedtoid",$therecord["assignedtoid"],9,"users.id","concat(users.firstname,\" \",users.lastname)","\"\"","users.revoked!=1",Array("size"=>"30","maxlength"=>"128")) ?>
			</p>
		
		</fieldset>
	</div>
	
	<div id="fsTops">
		<fieldset >
			<legend><label for="ds-clientid">client</label></legend>
			<div class="important fauxP">
				  <?php fieldAutofill("clientid",$therecord["clientid"],2,"clients.id","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","if(clients.city!=\"\",concat(clients.city,\", \",clients.state),\"\")","clients.inactive!=1 AND clients.type=\"client\"",Array("size"=>"51","maxlength"=>"128","class"=>"important","tabindex"=>"1"),1,"The record must have a client chosen.") ?>
				  <script language="JavaScript" type="text/javascript">
					document.forms["record"]["clientid"].onchange=populateShipping;
				  </script>
				  <?php if($therecord["id"]){?>
				  <input name="viewclient" type="button" value="view client" onclick="viewClient('<?php echo getAddEditFile(2) ?>')" class="smallButtons" tabindex="1" />
				  <?php }//end if?>
			</div>
		</fieldset>
		
		<fieldset>
			<legend><label for="address1">shipping address</label></legend>		
			<p>
				<input name="address1" id="address1" type="text" value="<?php echo htmlQuotes($therecord["address1"])?>" size="65" maxlength="128" tabindex=3 /><br />
				<input name="address2" id="address2" type="text"  value="<?php echo htmlQuotes($therecord["address2"])?>" size="65" maxlength="128" tabindex="4" />
			</p>
			<p class="cszP">
				<label for="city">city</label><br />
				<input name="city" type="text" id="city" value="<?php echo htmlQuotes($therecord["city"])?>" size="35" maxlength="64" tabindex="5" />			
			</p>
			<p class="cszP">
				<label for="state">state/prov</label><br />
				<input name="state" type="text" id="state" value="<?php echo htmlQuotes($therecord["state"])?>" size="3" maxlength="5" tabindex=6 />			
			</p>
			<p class="cszP">
				<label for="postalcode">zip/postal code</label><br />
				<input name="postalcode" type="text" id="postalcode" value="<?php echo htmlQuotes($therecord["postalcode"])?>" size="12" maxlength="15" tabindex=7 />
			</p>
			<p id="countryP">
				<label for="country">country</label><br />
				<input name="country" id="country" type="text" value="<?php echo htmlQuotes($therecord["country"])?>" size="45" maxlength="64" tabindex=8 />
			</p>
		</fieldset>

		<fieldset>
			<legend>details</legend>
			<p>	<label for="weborder">web / confirmation Nnumber</label><br />
				<?php fieldCheckbox("weborder",$therecord["weborder"],0,array("tabindex"=>"14"));?>
				<input name="webconfirmationno" type="text" value="<?php echo $therecord["webconfirmationno"] ?>" size="64" maxlength="64" tabindex="14" />
			</p>
			<p>
				<label for="leadsource">lead source</label><br />
				<?php fieldChoiceList("leadsource",$therecord["leadsource"],"leadsource",Array("tabindex"=>"14")); ?>			
			</p>			
		</fieldset>
	</div>

<fieldset id="lineItemsFS">
	<legend>line items</legend>

	<input type="hidden" name="thelineitems" id="thelineitems" value="" />
	<input id="lineitemschanged" name="lineitemschanged" type="hidden" value="0"/>
	<input id="unitcost" name="unitcost" type="hidden" value="0" />
	<input id="unitweight" name="unitweight" type="hidden" value="0"/>
	<input id="taxable" name="taxable" type="hidden" value="1"/>
	<input id="imgpath" name="imgpath" type="hidden" value="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image" />
	
	<table border="0" cellpadding="0" cellspacing="0" id="LITable">
		<tr id="LIHeader">
			<th nowrap class="queryheader" align="left">part number</th>
			<th nowrap class="queryheader" align="left" id="partnameHeader"><div>name</div></th>
			<th nowrap class="queryheader" align="left" id="memoHeader">memo</th>
			<th align="right" nowrap class="queryheader">price</th>
			<th align="center" nowrap class="queryheader">qty.</th>
			<th align="right" nowrap class="queryheader">extended</th>
			<th nowrap class="queryheader">&nbsp;</th>
		</tr>
		<?php if($therecord["type"]!="Invoice"){?>
		<tr id="LIAdd">
			<td nowrap>
			<?php fieldAutofill("partnumber","",4,"products.id","products.partnumber","products.partname","products.status=\"In Stock\" and products.inactive=0",Array("size"=>"16","maxlength"=>"32","tabindex"=>"15"),false,"") ?>
			<script language="JavaScript" type="text/javascript">
					document.forms["record"]["partnumber"].onchange=populateLineItem;
			</script>
			</td>
			<td nowrap>
			<?php fieldAutofill("partname","",4,"products.id","products.partname","products.partnumber","products.status=\"In Stock\" and products.inactive=0",Array("size"=>"20","maxlength"=>"128","tabindex"=>"16"),false,"") ?>
			<script language="JavaScript" type="text/javascript">
					document.forms["record"]["partname"].onchange=populateLineItem;
			</script>
			</td>
			<td><input name="memo" type="text" id="memo" size="12" maxlength="255" tabindex="17" /></td>
			<td align="right" nowrap><input name="price" type="text" id="price" value="<?php echo htmlQuotes(numberToCurrency(0))?>" size="10" maxlength="16" onchange="calculateExtended()" class="fieldCurrency"  tabindex="18"  /></td>
			<td align="center" nowrap><input name="qty" type="text" id="qty" value="1" size="5" maxlength="16" onchange="calculateExtended()" tabindex="19"  /></td>
			<td align="right" nowrap><input name="extended" type="text" id="extended" class="uneditable fieldCurrency" value="<?php echo htmlQuotes(numberToCurrency(0))?>" size="12" maxlength="16" readonly="true" /></td>
			<td nowrap align="center"><button type="button" onclick="addLine(this.parentNode);" tabindex="20" class="graphicButtons buttonPlus" title="Add Line Item"><span>+</span></button></td>
		</tr><?php }//end if
  	$lineitemsresult=getLineItems($therecord["id"]);
		
	if($lineitemsresult) {
	?><tr id="LISep"><td colspan="7"></td></tr><?php 
	while($lineitem=mysql_fetch_array($lineitemsresult)){
  ?><tr class="lineitems" id="LIN<?php echo $lineitem["id"]?>">
			<td nowrap class="lineitemsLeft important"><?php if($lineitem["partnumber"]) echo htmlQuotes($lineitem["partnumber"]); else echo "&nbsp;";?></td>
			<td width="150"><strong><?php if($lineitem["partname"]) echo htmlQuotes($lineitem["partname"]); else echo "&nbsp;";?></strong></td>
			<td><?php if($lineitem["memo"]) echo htmlQuotes($lineitem["memo"]); else echo "&nbsp;"?></td>
			<td align="right" nowrap><?php echo htmlQuotes(numberToCurrency($lineitem["unitprice"]))?></td>
			<td align="center" nowrap><?php echo $lineitem["quantity"]?></td>
			<td align="right" nowrap><?php echo htmlQuotes(numbertoCurrency($lineitem["extended"]))?></td>
			<td align="center"><span class="LIRealInfo"><?php echo $lineitem["productid"]?>[//]<?php echo $lineitem["unitcost"]?>[//]<?php echo $lineitem["unitweight"]?>[//]<?php echo $lineitem["numprice"]?>[//]<?php echo $lineitem["quantity"]?>[//]<?php echo htmlQuotes($lineitem["memo"])?>[//]<?php echo $lineitem["taxable"]?></span>
				<?php if($therecord["type"]=="Invoice") echo "&nbsp;"; else {?><button type="button" class="graphicButtons buttonMinus" onClick="return deleteLine(this)" tabindex="21" title="Remove line item"><span>-</span></button><?php } ?>
			</td>
		</tr>
  <?php } } ?><tr id="LITotals">
		<td colspan="3" rowspan="8" align="right" valign="top">
			<div id="vContent1" class="vContent" onmouseover="window.clearTimeout(vTabTimeout);vTabTimeout=0" onmouseout="vTabTimeout=window.setTimeout('vTabOut()',1000)">
				<fieldset>
					<legend>Discount / Promotion</legend>
					<p id="pDiscount">
						<label for="discountid">discount</label><br />
						<?php showDiscountSelect($therecord["discountid"],$dblink)?>
					</p>
					<input type="hidden" id="discount" name="discount" value="<?php echo $therecord["discount"]?>" />
				</fieldset>
			</div>

			<div id="vContent2" class="vContent" onmouseover="window.clearTimeout(vTabTimeout);vTabTimeout=0" onmouseout="vTabTimeout=window.setTimeout('vTabOut()',1000)">
				<fieldset>
					<legend>Tax</legend>
					<p>
						<label for="taxareaid">tax area</label><br />
						<?php showTaxSelect($therecord["taxareaid"],$dblink)?>
					</p>
					<p>
						<label for="taxpercentage">tax percentage</label><br />
						<?php fieldPercentage("taxpercentage",$therecord["taxpercentage"],5,0,"Tax percentage must be a valid percentage.",Array("size"=>"9","maxlength"=>"9","onchange"=>"clearTaxareaid()","tabindex"=>"22")); ?>						
					</p>
				</fieldset>
			</div>

			<div id="vContent3" class="vContent" onmouseover="window.clearTimeout(vTabTimeout);vTabTimeout=0" onmouseout="vTabTimeout=window.setTimeout('vTabOut()',1000)">
				<fieldset>
					<legend>Shipping</legend>
					<p id="pTotalweight">
						<label for="totalweight">total wt.</label><br/>
						<input id="totalweight" name="totalweight" type="text" value="<?php echo $therecord["totalweight"]?>" size="5" maxlength="15" readonly="true" class="uneditable" />			
					</p>
					
					<p>
						<label for="shippingmethodid">ship via</label><br />
						<?php  showShippingSelect($therecord["shippingmethodid"],$shippingMethods);
							$shipButtonDisable="";
							if($therecord["shippingmethodid"]==0)
								$shipButtonDisable="Disabled";
							else
								if($shippingMethods[$therecord["shippingmethodid"]]["canestimate"]==0)
									$shipButtonDisable="Disabled";
						?>
						<button id="estimateShippingButton" type="button" onclick="startEstimateShipping()" class="graphicButtons buttonShip<?php echo $shipButtonDisable?>" <?php if($shipButtonDisable) echo "disabled=\"disabled\""?> title="Estimate Shipping"><span>Estimate Shipping</span></button>
					</p>
					<div id="shippingNotice">
						<p class="notes">
							The shipping estimate establishes an outside connection to a shipping provider
							in order to estimate the shipping for the order.
						</p>
						<p class="notes">
							Make sure the products have weight and shipping information entered, that the client
							has a valid shipping address, and that the company address information has been entered.
						</p>
						<p><label for="shippingNoticeResults">estimation results</label><br /><textarea readonly="readonly" id="shippingNoticeResults" rows="5" cols=""></textarea></p>
						<p align="right">
							<button type="button" class="Buttons" onclick="performShippingEstimate('<?php echo $_SESSION["app_path"] ?>')">estimate</button>
							<button type="button" class="Buttons" onclick="closeModal()">done</button>
						</p>
					</div>

					<p>
						<label for="trackingno">tracking number</label><br />
						<input id="trackingno" name="trackingno" type="text" value="<?php echo htmlQuotes($therecord["trackingno"]) ?>" size="50" maxlength="64" tabindex="30" />
					</p>					
				</fieldset>
			</div>

			<div id="vContent4" class="vContent"  onmouseover="window.clearTimeout(vTabTimeout);vTabTimeout=0" onmouseout="vTabTimeout=window.setTimeout('vTabOut()',1000)">
				<fieldset>
					<legend>Payment</legend>
					<?php if(hasRights(20)){ ?>
					<p>
						<label for="paymentmethodid">payment method</label><br />
						<?php showPaymentSelect($therecord["paymentmethodid"],$paymentMethods);
							$paymentButtonDisable="";
							if($therecord["paymentmethodid"]==0)
								$paymentButtonDisable="Disabled";
							else
								if($paymentMethods[$therecord["paymentmethodid"]]["onlineprocess"]==0)
									$paymentButtonDisable="Disabled";
						?>
						<button id="paymentProcessButton" type="button" onclick="startPaymentProcess()" class="graphicButtons buttonMoney<?php echo $paymentButtonDisable?>" <?php if($paymentButtonDisable) echo "disabled=\"disabled\""?> title="process payment online"><span>process payment online</span></button>
					</p>
					<div id="checkpaymentinfo">
						<p id="pCheckNumber">
							<label for="checkno">check number</label><br />
							<input name="checkno" type="text" id="checkno" value="<?php echo htmlQuotes($therecord["checkno"])?>" size="20" maxlength="32" tabindex="25"/>
						</p>				
						<p>
							<label for="bankname">bank name</label><br />
							<input id="bankname" name="bankname" type="text" value="<?php echo htmlQuotes($therecord["bankname"]) ?>" size="30" maxlength="64" tabindex="26"/>
						</p>
						<p id="pAccountNumber">
							<label for="accountnumber">account number</label><br />
							<?php fieldText("accountnumber",$therecord["accountnumber"],false,"Account number must be a valid integer","integer",Array("size"=>"20","maxlength"=>"64")); ?>
						</p>
						<p>
							<label for="routingnumber">routing number</label><br />
							<?php fieldText("routingnumber",$therecord["routingnumber"],false,"Routing number must be a valid integer","integer",Array("size"=>"30","maxlength"=>"64")); ?>
						</p>
					</div>	
					<div id="ccpaymentinfo">
						<p id="fieldCCNumber">
							<label for="ccnumber">card number</label><br />
							<input name="ccnumber" type="text" id="ccnumber" value="<?php echo htmlQuotes($therecord["ccnumber"]) ?>" size="28" maxlength="40" tabindex="27"/>			
						</p>
						<p id="fieldCCExpiration">
							<label for="ccexpiration">expiration</label><br />
							<input name="ccexpiration" id="ccexpiration" type="text"  value="<?php echo htmlQuotes($therecord["ccexpiration"]) ?>" size="8" maxlength="10" tabindex="28" />
						</p>
						<p>
							<label for="ccverification">verification/pin</label><br />
							<input id="ccverification" name="ccverification" type="text"  value="<?php echo htmlQuotes($therecord["ccverification"]) ?>" size="8" maxlength="7" tabindex="29" />		
						</p>				
					</div>
					<p id="pTransactionid">
						<label for="transactionid">transaction id</label><br />
						<input type="text" id="transactionid" name="transactionid" value="<?php echo htmlQuotes($therecord["transactionid"])?>" size="32" maxlength="64" />						
					</p>
					<div id="paymentNotice">
						<p class="notes">
							The payment processing establishes an outside connection to an online
							processing center.
						</p>
						<p><label for="paymentNotice Results">payment process results</label><br /><textarea readonly="readonly" id="paymentNoticeResults" rows="5" cols=""></textarea></p>
						<p align="right">
							<button type="button" class="Buttons" onclick="performPaymentProcess('<?php echo $_SESSION["app_path"] ?>')">process payment</button>
							<button type="button" class="Buttons" onclick="closeModal()">done</button>
						</p>
					</div>
					<?php } else {?>
					<p class="notes">You do not have rights to view payment details.</p>
					<?php } ?>
				</fieldset>
			</div>

			<div id="parenInfo">
				<div><span id="parenDiscount"><?php if($therecord["discountname"])  echo "(".$therecord["discountname"].")"; else echo "&nbsp;"; ?></span></div>
				<div>&nbsp;</div>
				<div><span id="parenTax"><?php 
					if($therecord["taxname"] || ((int) $therecord["taxpercentage"])!=0){
						echo "(";
						if($therecord["taxname"]) echo $therecord["taxname"].": ";
						echo $therecord["taxpercentage"]."%)";
					} else echo "&nbsp;";				
				?></span></div>
				<div><span id="parenShipping"><?php if($therecord["shippingmethodid"]!=0) echo "(".htmlQuotes($shippingMethods[$therecord["shippingmethodid"]]["name"]).")"; else echo "&nbsp;"?></span></div>
				<div>&nbsp;</div>
				<div id="parenSpacer"><span>&nbsp;</span></div>
				<div><span id="parenPayment"><?php if($therecord["paymentmethodid"]!=0) echo "(".htmlQuotes($paymentMethods[$therecord["paymentmethodid"]]["name"]).")"; else echo "&nbsp;"?></span></div>
			</div>
		</td>
		<td colspan="2" class="invoiceTotalLabels vTabs" id="vTab1" onmouseover="vTabOver(this)" onmouseout="vTabTimeout=window.setTimeout('vTabOut()',1000)"><div>discount<input type="hidden" id="totalBD" name="totalBD" value="<?php echo $therecord["totaltni"]+$therecord["discountamount"]?>" /></div></td>
		<td class="totalItems"><input name="discountamount" id="discountamount" type="text" value="<?php echo numberToCurrency($therecord["discountamount"])?>" size="12" maxlength="15" onchange="clearDiscount();calculateTotal();" class="fieldCurrency fieldTotal" tabindex="22"/></td>
		<td class="totalItems">&nbsp;</td>  	
  </tr><tr>
		<td colspan="2" class="invoiceTotalLabels"><div>subtotal</div></td>
		<td class="totalItems"><input class="uneditable fieldCurrency fieldTotal" name="totaltni" id="totaltni" type="text" value="<?php echo numberToCurrency($therecord["totaltni"])?>" size="12"  maxlength="15" readonly="true" onchange="calculateTotal();" /></td>
		<td class="totalItems">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" class="invoiceTotalLabels vTabs" id="vTab2" onmouseover="vTabOver(this)" onmouseout="vTabTimeout=window.setTimeout('vTabOut()',1000)"><div>tax</div></td>
		<td class="totalItems"><input name="tax" id="tax" type="text" value="<?php echo numberToCurrency($therecord["tax"])?>" size="12" maxlength="15" onchange="changeTaxAmount()" class="fieldCurrency fieldTotal" tabindex="22" /></td>
		<td class="totalItems">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" class="invoiceTotalLabels vTabs" id="vTab3" onmouseover="vTabOver(this)" onmouseout="vTabTimeout=window.setTimeout('vTabOut()',1000)"><div>shipping</div></td>
		<td class="totalItems"><input name="shipping" id="shipping" type="text" value="<?php echo numberToCurrency($therecord["shipping"])?>" size="12" maxlength="15" onchange="calculateTotal();" class="fieldCurrency fieldTotal" tabindex="23" /></td>
		<td class="totalItems">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" class="invoiceTotalLabels important"><div>total</div></td>
		<td class="totalItems">
			<input class="uneditable fieldCurrency important fieldTotal" name="totalti" id="totalti" type="text" value="<?php echo numberToCurrency($therecord["totalti"])?>" size="12" maxlength="15" onchange="calculateTotal();"  readonly="true" />
			<input id="totalcost" name="totalcost" type="hidden" value="<?php echo $therecord["totalcost"] ?>" />
			<input id="totaltaxable" name="totaltaxable" type="hidden" value="<?php echo $therecord["totaltaxable"] ?>" />
		</td>
		<td class="totalItems">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4" class="invoiceTotalLabels" id="totalSpacer"><div>&nbsp;</div></td>
	</tr>
	<tr>
		<td colspan="2" class="invoiceTotalLabels vTabs" id="vTab4" onmouseover="vTabOver(this)" onmouseout="vTabTimeout=window.setTimeout('vTabOut()',1000)"><div>payment</div></td>
		<td class="totalItems"><input name="amountpaid" id="amountpaid" type="text" value="<?php echo numberToCurrency($therecord["amountpaid"])?>" size="12" maxlength="15" onchange="calculatePaidDue();"  class="important fieldCurrency fieldTotal" tabindex="24"/></td>
		<td class="totalItems"><button type="button" onclick="payInFull()" tabindex="20" class="graphicButtons buttonCheck" title="Pay in full"><span>pay in full</span></button></td>
	</tr>
	<tr>
		<td colspan="2" class="invoiceTotalLabels"><div>amount due</div></td>
		<td class="totalItems"><input id="amountdue" name="amountdue" type="text" value="<?php echo numberToCurrency($therecord["amountdue"]) ?>" size="12" maxlength="15" onchange="calculatePaidDue();" class="important fieldCurrency fieldTotal" tabindex="24"/></td>
		<td class="totalItems">&nbsp;</td>
	</tr>
</table>
</fieldset>

<fieldset id="fsInstructions">
	<legend>instructions</legend>
	<p>
		<label for="specialinstructions">special instructions</label> 
		<span class="notes"><em>(will not print on invoice)</em></span><br />
		<textarea id="specialinstructions" name="specialinstructions" cols="45" rows="3"tabindex="37"><?php echo $therecord["specialinstructions"]?></textarea>	
	</p>
	<p>
		<label for="printedinstructions">printed instructions</label><br />
		<textarea id="printedinstructions" name="printedinstructions" cols="45" rows="3" tabindex="38"><?php echo $therecord["printedinstructions"]?></textarea>	
	</p>
</fieldset>		
<?php include("../../include/createmodifiedby.php"); ?>
<?php if($therecord["type"]=="VOID" || $therecord["type"]=="Invoice"){?>
<script language="JavaScript" type="text/javascript">disableSaves(document.forms["record"]);</script>
<?php }// end if ?>
</div><?php include("../../footer.php");?>
</form></body>
</html>