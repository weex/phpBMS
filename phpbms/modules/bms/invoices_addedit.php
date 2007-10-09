<?php 
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2007, Kreotek LLC                                  |
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
	include("include/tables.php");
	include("include/fields.php");
	include("include/invoices.php");

	if(!isset($_GET["backurl"])) 
		$backurl = NULL; 
	else{ 
		$backurl = $_GET["backurl"];
		if(isset($_GET["refid"]))
			$backurl .= "?refid=".$_GET["refid"];
	}

	$thetable = new invoices($db,3,$backurl);
	$therecord = $thetable->processAddEditPage();

	$phpbms->cssIncludes[] = "pages/invoice.css";
	$phpbms->jsIncludes[] = "modules/bms/javascript/invoice.js";
	$phpbms->jsIncludes[] = "modules/bms/javascript/paymentprocess.js";
	
	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];


		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();
		
		$theinput = new inputDatePicker("orderdate", $therecord["orderdate"], "order date");
		$theform->addField($theinput);
	
		$theinput = new inputDatePicker("invoicedate", $therecord["invoicedate"], "invoice date");
		$theform->addField($theinput);

		$theinput = new inputDatePicker("requireddate", $therecord["requireddate"], "required date");
		$theform->addField($theinput);

		$theinput = new inputBasicList("type",$therecord["type"],array("Quote"=>"Quote","Order"=>"Order"), NULL, true);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputDatePicker("statusdate", $therecord["statusdate"], "status date");
		$theform->addField($theinput);		
		
		$theinput = new inputAutofill($db, "assignedtoid",$therecord["assignedtoid"],9,"users.id","concat(users.firstname,\" \",users.lastname)",
										"\"\"","users.revoked!=1", "assigned to");					
		$theinput->setAttribute("size","30");
		$theform->addField($theinput);

		$theinput = new inputCheckBox("readytopost",$therecord["readytopost"],"ready to post");
		$theform->addField($theinput);

		$theinput = new inputAutofill($db, "clientid",$therecord["clientid"],2,"clients.id","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)",
										"if(clients.city!=\"\",concat(clients.city,\", \",clients.state),\"\")","clients.inactive!=1 AND clients.type=\"client\"", "client", true,true,false);					
		$theinput->setAttribute("size","51");
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputCheckBox("weborder",$therecord["weborder"],NULL, false, false);
		$theform->addField($theinput);
		
		$theinput = new inputChoiceList($db,"leadsource",$therecord["leadsource"],"leadsource", "lead source");
		$theform->addField($theinput);
		
		if($therecord["type"]!="VOID" && $therecord["type"]!="Invoice"){
			$theinput = new inputAutofill($db, "partnumber","",4,"products.id","products.partnumber",
											"products.partname","products.status=\"In Stock\" and products.inactive=0","partnumber", false,true,false);					
			$theinput->setAttribute("size","16");
			$theinput->setAttribute("maxlength","32");
			$theform->addField($theinput);
			$phpbms->bottomJS[] = 'document.forms["record"]["partnumber"].onchange=populateLineItem;';
				
			$theinput = new inputAutofill($db, "partname","",4,"products.id","products.partname",
											"products.partnumber","products.status=\"In Stock\" and products.inactive=0","part name", false,true,false);
			$theinput->setAttribute("size","20");
			$theinput->setAttribute("maxlength","128");
			$theform->addField($theinput);
			$phpbms->bottomJS[] = 'document.forms["record"]["partname"].onchange=populateLineItem;';
		}
		
		$theinput = new inputPercentage("taxpercentage",$therecord["taxpercentage"], "tax percentage" , 5);
		$theinput->setAttribute("onchange","clearTaxareaid()");
		$theform->addField($theinput);
		
		
		$theinput = new inputField("accountnumber",$therecord["accountnumber"],  "account number" ,false, "integer", 20, 64);
		$theform->addField($theinput);
		
		$theinput = new inputField("routingnumber",$therecord["routingnumber"],  "routing number" ,false, "integer", 30, 64);
		$theform->addField($theinput);

		$theinput = new inputCurrency("creditlimit", $therecord["creditlimit"], "credit limit" );
		$theinput->setAttribute("readonly","readonly");
		$theform->addField($theinput);

		$theinput = new inputCurrency("creditleft", $therecord["creditleft"], "credit left (before order)" );
		$theinput->setAttribute("readonly","readonly");
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	
	$pageTitle=ucwords($therecord["type"]);
	
	$_SESSION["printing"]["tableid"]=3;
	$_SESSION["printing"]["theids"]=array($therecord["id"]);
	
	$shippingMethods = $thetable->getShipping($therecord["shippingmethodid"]);
	$paymentMethods = $thetable->getPayments($therecord["paymentmethodid"]);
	$statuses = $thetable->getStatuses($therecord["statusid"]);

	if($therecord["type"]=="VOID" || $therecord["type"]=="Invoice")
		$phpbms->bottomJS[] = 'disableSaves(document.forms["record"]);';
	
	include("header.php");	


?><form action="<?php echo str_replace("&","&amp;",$_SERVER["REQUEST_URI"]) ?>" 
	method="post" name="record" id="record"><div id="dontSubmit"><input type="submit" value=" " onclick="return false;" /></div>
<?php $phpbms->showTabs("invoices entry",15,$therecord["id"]);?><div class="bodyline">
	<div id="topButtons">
		  <?php if($therecord["id"]){
		  		?><div id="printButton">
				<input name="doprint" type="button" value="print" accesskey="p" onclick="doPrint('<?php echo APP_PATH?>',<?php echo $therecord["id"]?>)" class="Buttons" />
			</div><?php 
			}//end if
			showSaveCancel(1); ?>
	</div>
	<h1 id="h1With<?php if(!$therecord["id"]) echo "out"?>Print"><?php echo $pageTitle ?></h1>
	<div id="fsAttributes">
		<fieldset >
			<legend>attributes</legend>
			
			<div id="attributesRight">
				<p><?php $theform->fields["orderdate"]->display(); ?></p>
				
				<p><?php $theform->fields["invoicedate"]->display(); ?></p>				
				
				<p><?php $theform->fields["requireddate"]->display(); ?></p>
			</div>
		
			<div>
				<p>
					<label for="id">id</label>
					<br />
					<input name="id" id="id" type="text" value="<?php echo $therecord["id"]; ?>" size="11" maxlength="11" readonly="readonly" class="uneditable"  />
				</p>
				
				<p>
					<?php  if($therecord["type"]=="VOID" || $therecord["type"]=="Invoice") {?>
						<label for="type" class="important">type</label><br />
						<input id="type" name="type" type="text" value="<?php echo htmlQuotes($therecord["type"])?>" size="11" maxlength="11" readonly="readonly" class="uneditable important" />
					<?php }else {
						$theform->fields["type"]->display();
					}?><input type="hidden" id="oldType" name="oldType" value="<?php echo $therecord["type"]?>"/>
				</p>
		
				<p>
					<label for="ponumber">client PO#</label>
					<br />
					<input name="ponumber" id="ponumber" type="text" value="<?php echo htmlQuotes($therecord["ponumber"])?>" size="11" maxlength="64" tabindex="12" />
				</p>
			</div>			
		</fieldset>
		
		<fieldset>
			<legend>Status</legend>
			<p>
				<label for="statusid" class="important">current status</label><br />
				<?php $thetable->displayStatusDropDown($therecord["statusid"],$statuses)?>
				<input type="hidden" id="statuschanged" name="statuschanged" value="<?php if($therecord["id"]=="") echo "1"; else echo "0"?>" />
			</p>
			<p>
				<?PHP $theform->fields["statusdate"]->display();?>
			</p>
			<p>
				<?php $theform->fields["assignedtoid"]->display(); ?>
			</p>
			<p>
				<?php $theform->fields["readytopost"]->display(); ?>				
			</p>
		</fieldset>
	</div>
	
	<div id="fsTops">
		<fieldset >
			<legend><label for="ds-clientid">client</label></legend>
			<div class="important fauxP">
				  <?php $theform->fields["clientid"]->display(); 
				  if($therecord["id"]){?>
				  <button type="button" title="view client" class="graphicButtons buttonInfo" onclick="viewClient('<?php echo getAddEditFile($db,2) ?>')"><span>view client</span></button>
				  <?php }//end if?>
			</div>
		</fieldset>
		
		<fieldset>
			<legend><label for="address1">shipping address</label></legend>		
			<p>
				<input name="address1" id="address1" type="text" value="<?php echo htmlQuotes($therecord["address1"])?>" size="65" maxlength="128" tabindex="3" /><br />
				<input name="address2" id="address2" type="text"  value="<?php echo htmlQuotes($therecord["address2"])?>" size="65" maxlength="128" tabindex="4" />
			</p>
			<p class="cszP">
				<label for="city">city</label><br />
				<input name="city" type="text" id="city" value="<?php echo htmlQuotes($therecord["city"])?>" size="35" maxlength="64" tabindex="5" />			
			</p>
			<p class="cszP">
				<label for="state">state/prov</label><br />
				<input name="state" type="text" id="state" value="<?php echo htmlQuotes($therecord["state"])?>" size="10" maxlength="20" tabindex="6" />
			</p>
			<p class="cszP">
				<label for="postalcode">zip/postal code</label><br />
				<input name="postalcode" type="text" id="postalcode" value="<?php echo htmlQuotes($therecord["postalcode"])?>" size="12" maxlength="15" tabindex="7" />
			</p>
			<p id="countryP">
				<label for="country">country</label><br />
				<input name="country" id="country" type="text" value="<?php echo htmlQuotes($therecord["country"])?>" size="45" maxlength="64" tabindex="8" />
			</p>
		</fieldset>

		<fieldset>
			<legend>details</legend>
			<p>	<label for="weborder">web / confirmation number</label><br />
				<?php $theform->fields["weborder"]->display();?>
				<input name="webconfirmationno" type="text" value="<?php echo htmlQuotes($therecord["webconfirmationno"]) ?>" size="64" maxlength="64" tabindex="14" />
			</p>
			<p>
				<?php $theform->fields["leadsource"]->display() ?>			
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
	<input id="imgpath" name="imgpath" type="hidden" value="<?php echo APP_PATH ?>common/stylesheet/<?php echo STYLESHEET ?>/image" />
	
	<table border="0" cellpadding="0" cellspacing="0" id="LITable">
		<tr id="LIHeader">
			<th nowrap="nowrap" class="queryheader" align="left">part number</th>
			<th nowrap="nowrap" class="queryheader" align="left" id="partnameHeader"><div>name</div></th>
			<th nowrap="nowrap" class="queryheader" align="left" id="memoHeader">memo</th>
			<th align="right" nowrap="nowrap" class="queryheader">price</th>
			<th align="center" nowrap="nowrap" class="queryheader">qty.</th>
			<th align="right" nowrap="nowrap" class="queryheader">extended</th>
			<th nowrap="nowrap" class="queryheader">&nbsp;</th>
		</tr>
		<?php if($therecord["type"]!="Invoice" && $therecord["type"]!="VOID"){?>
		<tr id="LIAdd">
			<td nowrap="nowrap"><?php $theform->fields["partnumber"]->display();?></td>
			<td nowrap="nowrap"><?php $theform->fields["partname"]->display();?></td>
			<td><input name="memo" type="text" id="memo" size="12" maxlength="255" tabindex="17" /></td>
			<td align="right" nowrap="nowrap"><input name="price" type="text" id="price" value="<?php echo htmlQuotes(numberToCurrency(0))?>" size="10" maxlength="16" onchange="calculateExtended()" class="fieldCurrency"  tabindex="18"  /></td>
			<td align="center" nowrap="nowrap"><input name="qty" type="text" id="qty" value="1" size="5" maxlength="16" onchange="calculateExtended()" tabindex="19"  /></td>
			<td align="right" nowrap="nowrap"><input name="extended" type="text" id="extended" class="uneditable fieldCurrency" value="<?php echo htmlQuotes(numberToCurrency(0))?>" size="12" maxlength="16" readonly="readonly" /></td>
			<td nowrap="nowrap" align="center"><button type="button" onclick="addLine(this.parentNode);" tabindex="20" class="graphicButtons buttonPlus" title="Add Line Item"><span>+</span></button></td>
		</tr><?php }//end if
  	$lineitemsresult = $thetable->getLineItems($therecord["id"]);
		
	if($lineitemsresult) {
	?><tr id="LISep"><td colspan="7"></td></tr><?php 
	while($lineitem=$db->fetchArray($lineitemsresult)){
  ?><tr class="lineitems" id="LIN<?php echo $lineitem["id"]?>">
			<td nowrap="nowrap" class="lineitemsLeft important"><?php if($lineitem["partnumber"]) echo htmlQuotes($lineitem["partnumber"]); else echo "&nbsp;";?></td>
			<td width="150"><strong><?php if($lineitem["partname"]) echo htmlQuotes($lineitem["partname"]); else echo "&nbsp;";?></strong></td>
			<td><?php if($lineitem["memo"]) echo htmlQuotes($lineitem["memo"]); else echo "&nbsp;"?></td>
			<td align="right" nowrap="nowrap"><?php echo htmlQuotes(numberToCurrency($lineitem["unitprice"]))?></td>
			<td align="center" nowrap="nowrap"><?php echo $lineitem["quantity"]?></td>
			<td align="right" nowrap="nowrap"><?php echo htmlQuotes(numbertoCurrency($lineitem["extended"]))?></td>
			<td align="center"><span class="LIRealInfo"><?php echo $lineitem["productid"]?>[//]<?php echo $lineitem["unitcost"]?>[//]<?php echo $lineitem["unitweight"]?>[//]<?php echo $lineitem["numprice"]?>[//]<?php echo $lineitem["quantity"]?>[//]<?php echo htmlQuotes($lineitem["memo"])?>[//]<?php echo $lineitem["taxable"]?></span>
				<?php if($therecord["type"]=="Invoice") echo "&nbsp;"; else {?><button type="button" class="graphicButtons buttonMinus" onclick="return deleteLine(this)" tabindex="21" title="Remove line item"><span>-</span></button><?php } ?>
			</td>
		</tr>
  <?php } } ?><tr id="LITotals">
		<td colspan="3" rowspan="8" align="right" valign="top">

			<div id="vContent1" class="vContent">
				<fieldset>
					<legend>Discount / Promotion</legend>
					<p id="pDiscount">
						<label for="discountid">discount</label><br />
						<?php $thetable->showDiscountSelect($therecord["discountid"])?>
					</p>
					<input type="hidden" id="discount" name="discount" value="<?php echo $therecord["discount"]?>" />
				</fieldset>
			</div>

			<div id="vContent2" class="vContent">
				<fieldset>
					<legend>Tax</legend>
					<p>
						<label for="taxareaid">tax area</label><br />
						<?php $thetable->showTaxSelect($therecord["taxareaid"])?>
					</p>
					<p>
						<?php $theform->fields["taxpercentage"]->display();?>						
					</p>
				</fieldset>
			</div>

			<div id="vContent3" class="vContent">
				<fieldset>
					<legend>Shipping</legend>
					<p id="pTotalweight">
						<label for="totalweight">total wt.</label><br/>
						<input id="totalweight" name="totalweight" type="text" value="<?php echo $therecord["totalweight"]?>" size="5" maxlength="15" readonly="readonly" class="uneditable" />			
					</p>
					
					<p>
						<label for="shippingmethodid">ship via</label><br />
						<?php  $thetable->showShippingSelect($therecord["shippingmethodid"],$shippingMethods);
							$shipButtonDisable="";
							if($therecord["shippingmethodid"]==0)
								$shipButtonDisable="Disabled";
							else
								if($shippingMethods[$therecord["shippingmethodid"]]["canestimate"]==0)
									$shipButtonDisable="Disabled";
						?>
						<button id="estimateShippingButton" type="button" onclick="startEstimateShipping()" class="graphicButtons buttonShip<?php echo $shipButtonDisable?>" title="Estimate Shipping"><span>Estimate Shipping</span></button>
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
							<button type="button" class="Buttons" onclick="performShippingEstimate('<?php echo APP_PATH ?>')">estimate</button>
							<button type="button" class="Buttons" onclick="closeModal()">done</button>
						</p>
					</div>

					<p>
						<label for="trackingno">tracking number</label><br />
						<input id="trackingno" name="trackingno" type="text" value="<?php echo htmlQuotes($therecord["trackingno"]) ?>" size="50" maxlength="64" tabindex="30" />
					</p>					
				</fieldset>
			</div>

			<div id="vContent4" class="vContent">
				<fieldset>
					<legend>Payment</legend>
					<?php if(hasRights(20)){ ?>
					<p>
						<label for="paymentmethodid">payment method</label><br />
						<?php $thetable->showPaymentSelect($therecord["paymentmethodid"],$paymentMethods);
							$paymentButtonDisable="";
							if($therecord["paymentmethodid"]==0)
								$paymentButtonDisable="Disabled";
							else
								if($paymentMethods[$therecord["paymentmethodid"]]["onlineprocess"]==0)
									$paymentButtonDisable="Disabled";
						?>
						<button id="paymentProcessButton" type="button" class="graphicButtons buttonMoney<?php echo $paymentButtonDisable?>" title="process payment online"><span>process payment online</span></button>
						<input type="hidden" id="processscript"/>
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
							<?php $theform->fields["accountnumber"]->display();?>
						</p>
						<p>
							<?php $theform->fields["routingnumber"]->display();?>
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
					
					<div id="receivableinfo">
						
						<input type="hidden" id="hascredit" value="<?php echo $therecord["hascredit"]?>"/>

						<p><?php $theform->showField("creditlimit")?></p>
					
						<p><?php $theform->showField("creditleft")?></p>

						<p class="notes">
							Payments made to accounts receivable invoices are done through
							the disbursements area.
						</p>
					</div>
					
					<p id="pTransactionid">
						<label for="transactionid">transaction id</label><br />
						<input type="text" id="transactionid" name="transactionid" value="<?php echo htmlQuotes($therecord["transactionid"])?>" size="32" maxlength="64" />						
					</p>
					
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
		<td colspan="2" class="invoiceTotalLabels vTabs" id="vTab1"><div>discount<input type="hidden" id="totalBD" name="totalBD" value="<?php echo $therecord["totaltni"]+$therecord["discountamount"]?>" /></div></td>
		<td class="totalItems"><input name="discountamount" id="discountamount" type="text" value="<?php echo numberToCurrency($therecord["discountamount"])?>" size="12" maxlength="15" onchange="clearDiscount();calculateTotal();" class="fieldCurrency fieldTotal" tabindex="22"/></td>
		<td class="totalItems">&nbsp;</td>  	
  </tr><tr>
		<td colspan="2" class="invoiceTotalLabels"><div>subtotal</div></td>
		<td class="totalItems"><input class="uneditable fieldCurrency fieldTotal" name="totaltni" id="totaltni" type="text" value="<?php echo numberToCurrency($therecord["totaltni"])?>" size="12"  maxlength="15" readonly="readonly" onchange="calculateTotal();" /></td>
		<td class="totalItems">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" class="invoiceTotalLabels vTabs" id="vTab2"><div>tax</div></td>
		<td class="totalItems"><input name="tax" id="tax" type="text" value="<?php echo numberToCurrency($therecord["tax"])?>" size="12" maxlength="15" onchange="changeTaxAmount()" class="fieldCurrency fieldTotal" tabindex="22" /></td>
		<td class="totalItems">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" class="invoiceTotalLabels vTabs" id="vTab3"><div>shipping</div></td>
		<td class="totalItems"><input name="shipping" id="shipping" type="text" value="<?php echo numberToCurrency($therecord["shipping"])?>" size="12" maxlength="15" onchange="calculateTotal();" class="fieldCurrency fieldTotal" tabindex="23" /></td>
		<td class="totalItems">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" class="invoiceTotalLabels important"><div>total</div></td>
		<td class="totalItems">
			<input class="uneditable fieldCurrency important fieldTotal" name="totalti" id="totalti" type="text" value="<?php echo numberToCurrency($therecord["totalti"])?>" size="12" maxlength="15" onchange="calculateTotal();"  readonly="readonly" />
			<input id="totalcost" name="totalcost" type="hidden" value="<?php echo $therecord["totalcost"] ?>" />
			<input id="totaltaxable" name="totaltaxable" type="hidden" value="<?php echo $therecord["totaltaxable"] ?>" />
		</td>
		<td class="totalItems">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4" class="invoiceTotalLabels" id="totalSpacer"><div>&nbsp;</div></td>
	</tr>
	<tr>
		<td colspan="2" class="invoiceTotalLabels vTabs" id="vTab4"><div>payment</div></td>
		<td class="totalItems"><input name="amountpaid" id="amountpaid" type="text" value="<?php echo numberToCurrency($therecord["amountpaid"])?>" size="12" maxlength="15" onchange="calculatePaidDue();"  class="important fieldCurrency fieldTotal" tabindex="24"/></td>
		<td class="totalItems"><button id="payinfull" type="button" onclick="payInFull()" tabindex="20" class="graphicButtons buttonCheck" title="Pay in full"><span>pay in full</span></button></td>
	</tr>
	<tr>
		<td colspan="2" class="invoiceTotalLabels" nowrap="nowrap"><div>amount due</div></td>
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
<?php $theform->showCreateModify($phpbms,$therecord) ?>
</div>
</form>
<?php include("footer.php");?>
