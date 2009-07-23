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

	$thetable = new invoices($db,"tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883",$backurl);
	$therecord = $thetable->processAddEditPage();

	$lineitems = new lineitems($db, $therecord["id"], $therecord["type"]);

	$phpbms->cssIncludes[] = "pages/invoice.css";
	$phpbms->jsIncludes[] = "modules/bms/javascript/invoice.js";
	$phpbms->jsIncludes[] = "modules/bms/javascript/invoiceaddress.js";
	$phpbms->jsIncludes[] = "modules/bms/javascript/paymentprocess.js";

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];


		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputField("theid", $therecord["id"], "id", false, NULL, 11);
                $theinput->setAttribute("readonly", "readonly");
                $theinput->setAttribute("class", "uneditable");
		$theform->addField($theinput);

		$theinput = new inputSmartSearch($db, "clientid", "Pick Sales Order Client", $therecord["clientid"], "client", true, 68, 255, false);
		$theform->addField($theinput);

		$theinput = new inputDatePicker("orderdate", $therecord["orderdate"], "order date");
		$theform->addField($theinput);

		$theinput = new inputDatePicker("invoicedate", $therecord["invoicedate"], "invoice date");
		$theform->addField($theinput);

		$theinput = new inputDatePicker("requireddate", $therecord["requireddate"], "required date");
		$theform->addField($theinput);

		$theinput = new inputBasicList("type",$therecord["type"],array("Quote"=>"Quote","Order"=>"Order"), NULL, true);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$checkDisabled = false;
		if($therecord["type"] == "VOID" || $therecord["type"] == "Invoice")
			$checkDisabled = true;

		$theinput = new inputCheckbox("iscreditmemo", $therecord["iscreditmemo"], "credit memo", $checkDisabled);
		$theform->addField($theinput);

		$theinput = new inputField("cmuuid", $therecord["cmuuid"], "related invoice id", false, NULL, 11);
		        $theinput->setAttribute("readonly", "readonly");
                $theinput->setAttribute("class", "uneditable");
		$theform->addField($theinput);

		$theinput = new inputDatePicker("statusdate", $therecord["statusdate"], "status date");
		$theform->addField($theinput);

		$theinput = new inputSmartSearch($db, "assignedtoid", "Pick Active User", $therecord["assignedtoid"], "assigned to", false, 36);
		$theform->addField($theinput);

		$theinput = new inputCheckBox("readytopost",$therecord["readytopost"],"ready to post");
		$theform->addField($theinput);

		$theinput = new inputCheckBox("weborder",$therecord["weborder"],NULL, false, false);
		$theform->addField($theinput);

		$theinput = new inputChoiceList($db,"leadsource",$therecord["leadsource"],"leadsource", "lead source");
		$theform->addField($theinput);

		if($therecord["type"]!="VOID" && $therecord["type"]!="Invoice"){

			$theinput = new inputSmartSearch($db, "productid", "Pick Product", "", NULL, false, 36, 255, false);
			$theform->addField($theinput);

		}//endif = ORDER

		$theinput = new inputField("address1",$therecord["address1"],"address",false,NULL,71,128,false);
		$theform->addField($theinput);

		$theinput = new inputField("address2",$therecord["address2"],NULL,false,NULL,71,128, false);
		$theform->addField($theinput);

		$theinput = new inputField("city",$therecord["city"],NULL,false,NULL,35,64);
		$theform->addField($theinput);

		$theinput = new inputField("state",$therecord["state"],"state/province",false,NULL,10,20);
		$theform->addField($theinput);

		$theinput = new inputField("postalcode",$therecord["postalcode"],"zip/postal code",false,NULL,12,15);
		$theform->addField($theinput);

		$theinput = new inputField("country",$therecord["country"],NULL,false,NULL,44,128);
		$theform->addField($theinput);

		$theinput = new inputCheckBox("shiptosameasbilling",$therecord["shiptosameasbilling"],"shipping address same as billing");
		$theform->addField($theinput);

		$saveOptionList = array(
			"sales order only" => "orderOnly",
			"update client address" => "updateAddress",
			"create new client address" => "createAddress"
		);
		$theinput = new inputBasicList("billingsaveoptions","orderOnly",$saveOptionList, "saving");
		$theform->addField($theinput);


		$theinput = new inputField("shiptoname",$therecord["shiptoname"],"ship to name",false,NULL,71,128);
		$theform->addField($theinput);

		$theinput = new inputField("shiptoaddress1",$therecord["shiptoaddress1"],"address",false,NULL,71,128, false);
		$theform->addField($theinput);

		$theinput = new inputField("shiptoaddress2",$therecord["shiptoaddress2"],NULL,false,NULL,71,128, false);
		$theform->addField($theinput);

		$theinput = new inputField("shiptocity",$therecord["shiptocity"],"city",false,NULL,35,64);
		$theform->addField($theinput);

		$theinput = new inputField("shiptostate",$therecord["shiptostate"],"state/province",false,NULL,10,20);
		$theform->addField($theinput);

		$theinput = new inputField("shiptopostalcode",$therecord["shiptopostalcode"],"zip/postal code",false,NULL,12,15);
		$theform->addField($theinput);

		$theinput = new inputField("shiptocountry",$therecord["shiptocountry"],"country",false,NULL,44,128);
		$theform->addField($theinput);

		$theinput = new inputBasicList("shiptosaveoptions","orderOnly",$saveOptionList, "address save options");
		$theform->addField($theinput);


		$theinput = new inputPercentage("taxpercentage",$therecord["taxpercentage"], "tax percentage" , 5);
		$theinput->setAttribute("onchange","clearTaxareaid()");
		$theform->addField($theinput);

		$theinput = new inputCurrency("creditlimit", $therecord["creditlimit"], "credit limit" );
		$theinput->setAttribute("readonly","readonly");
		$theform->addField($theinput);

		$theinput = new inputCurrency("creditleft", $therecord["creditleft"], "credit left (before order)" );
		$theinput->setAttribute("readonly","readonly");
		$theform->addField($theinput);

		//=====Payment Info====================


		$theinput = new inputComparisonField("accountnumber",$therecord["accountnumber"],  "account number" ,false, NULL, 20, 64);
		$theform->addField($theinput);

		$theinput = new inputComparisonField("routingnumber",$therecord["routingnumber"],  "routing number" ,false, NULL, 30, 64);
		$theform->addField($theinput);

		$theinput = new inputComparisonField("ccnumber", $therecord["ccnumber"], "card number", false, NULL, 28, 40);
		$theform->addField($theinput);

		$theinput = new inputComparisonField("ccexpiration", $therecord["ccexpiration"], "expiration", false, NULL, 8, 10);
		$theform->addField($theinput);

		$theinput = new inputComparisonField("ccverification", $therecord["ccverification"], "verification", false, NULL, 8, 7);
		$theform->addField($theinput);

		//======================================

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements


	$pageTitle = "Sales Order";
	if($therecord["iscreditmemo"])
		$pageTitle = "Credit Memo";

	$_SESSION["printing"]["tableid"]="tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883";
	$_SESSION["printing"]["theids"]=array($therecord["id"]);

	$shippingMethods = $thetable->getShipping($therecord["shippingmethodid"]);
	$paymentMethods = $thetable->getPayments($therecord["paymentmethodid"]);
	$statuses = $thetable->getStatuses($therecord["statusid"]);

	if($therecord["type"]=="VOID" || $therecord["type"]=="Invoice")
		$phpbms->bottomJS[] = 'disableSaves(document.forms["record"]);';

	include("header.php");


?><form action="<?php echo str_replace("&","&amp;",$_SERVER["REQUEST_URI"]) ?>"
	method="post" name="record" id="record"><div id="dontSubmit"><input type="submit" value=" " onclick="return false;" /></div>
<?php $phpbms->showTabs("invoices entry","tab:20276b44-9cfa-403e-4c2a-ac6f0987ae20",$therecord["id"]);?><div class="bodyline">
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
				<p><?php $theform->showField("orderdate"); ?></p>

				<p><?php $theform->showField("invoicedate"); ?></p>

				<p><?php $theform->showField("requireddate"); ?></p>
			</div>

			<div>
				<p><?php $theform->showField("theid")?></p>

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
					<input name="ponumber" id="ponumber" type="text" value="<?php echo htmlQuotes($therecord["ponumber"])?>" size="11" maxlength="64"/>
				</p>

				<p><?php $theform->showfield("iscreditmemo"); ?></p>

				<?php if($therecord["iscreditmemo"] && $therecord["cmuuid"]){ ?>

					<p><?php $theform->showfield("cmuuid"); ?></p>

				<?php }//end if ?>
			</div>

			<p>
				<?php $theform->fields["leadsource"]->display() ?>
			</p>

		</fieldset>

		<fieldset>
			<legend>Status</legend>
			<p>
				<label for="statusid" class="important">current status</label><br />
				<?php $thetable->showStatusDropDown($therecord["statusid"],$statuses)?>
				<input type="hidden" id="statuschanged" name="statuschanged" value="<?php if($therecord["id"]=="") echo "1"; else echo "0"?>" />
			</p>
			<p>
				<?PHP $theform->fields["statusdate"]->display();?>
			</p>
			<div class="fauxP">
				<?php $theform->fields["assignedtoid"]->display(); ?>
			</div>
			<p>
				<?php $theform->showField("readytopost"); ?>
			</p>
		</fieldset>
	</div>

	<div id="fsTops">
		<fieldset>
			<legend><label for="ds-clientid">client</label></legend>
			<div class="fauxP big">
				<input type="hidden" id="clientAddEditFile" value="<?php echo getAddEditFile($db, "tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083") ?>"/>
                                <input type="hidden" id="clientRealId" value="<?php echo $therecord["clientrealid"]?>"/>
				<?php $thetable->showClientType($therecord["clientid"])?>

                                <?php $theform->showField("clientid")?>

                                <button id="viewClientButton" type="button" title="view client" class="graphicButtons buttonInfo"><span>view client</span></button>
			</div>
		</fieldset>

		<fieldset id="fsAddresses">
			<legend>addresses</legend>
			<ul class="tabs">
				<li class="tabsSel"><a href="#" id="tabBilling">billing address</a></li>
				<li><a href="#" id="tabShipTo">shipping address</a></li>
			</ul>
			<div id="billingAddressDiv">
				<input type="hidden" id="billingaddressid" name="billingaddressid" value="<?php echo $therecord["billingaddressid"]?>" />
				<p>
					<label for="address1">address</label>
					<button type="button" class="graphicButtons buttonMap" id="buttonMapBilling" title="show map"><span>map</span></button>
					<br />
					<?php $theform->showField("address1");?><br />
					<?php $theform->showField("address2");?>
				</p>

				<p class="cityState"><?php $theform->showField("city");?></p>

				<p class="cityState"><?php $theform->showField("state");?></p>

				<p><?php $theform->showField("postalcode");?></p>

				<p>
					<?php $theform->showField("country");?>
				</p>

				<p>
					<?php $theform->showField("shiptosameasbilling")?>
				</p>

				<p><button id="buttonAddressBilling" class="graphicButtons buttonDown showoptions" type="button"><span>more options</span></button></p>

				<div id="addressOptionsDivBilling">
					<fieldset>
						<legend>address</legend>
						<p><label>replace</label><br />
							<button type="button" class="<?php if(!$therecord["clientid"]) {?>disabled<?php }//endif - clientid?>Buttons addressButtons" id="addressLoadButtonBilling">load...</button>
							<button type="button" class="Buttons addressButtons" id="addressClearButtonBilling">clear</button>
						</p>
						<p><?php $theform->showField("billingsaveoptions")?></p>
					</fieldset>
				</div>

			</div>

			<div id="shiptoAddressDiv">
				<input type="hidden" id="shiptoaddressid" name="shiptoaddressid" value="<?php echo $therecord["shiptoaddressid"]?>" />
				<p>
					<?php $theform->showField("shiptoname");?>
					<br /><span class="notes">(if different from client)</span>
				</p>
				<p>
					<label for="shiptoaddress1">address</label>
					<button type="button" class="graphicButtons buttonMap" id="buttonMapShipping" title="show map"><span>map</span></button>
					<br />
					<?php $theform->showField("shiptoaddress1");?><br />
					<?php $theform->showField("shiptoaddress2");?>
				</p>
				<p class="cityState">
					<?php $theform->showField("shiptocity");?>
				</p>
				<p class="cityState">
					<?php $theform->showField("shiptostate");?>
				</p>
				<p>
					<?php $theform->showField("shiptopostalcode");?>
				</p>
				<p>
					<?php $theform->showField("shiptocountry");?>
				</p>

				<p><button id="buttonAddressShipTo" class="graphicButtons buttonDown showoptions" type="button"><span>more options</span></button></p>

				<div id="addressOptionsDivShipTo">
					<fieldset>
						<legend>address</legend>
						<p><label>replace</label><br />
							<button type="button" class="<?php if(!$therecord["clientid"]) {?>disabled<?php }//endif - clientid?>Buttons addressButtons" id="addressLoadButtonShipping">load...</button>
							<button type="button" class="Buttons addressButtons" id="addressClearButtonShipping">clear</button>
						</p>
						<p><?php $theform->showField("shiptosaveoptions")?></p>
					</fieldset>
				</div>

			</div>
		</fieldset>

		<fieldset>
			<legend>web</legend>
			<p>	<label for="weborder">web / confirmation number</label><br />
				<?php $theform->fields["weborder"]->display();?>
				<input name="webconfirmationno" type="text" value="<?php echo htmlQuotes($therecord["webconfirmationno"]) ?>" size="64" maxlength="64"/>
			</p>
		</fieldset>
	</div>

<fieldset id="lineItemsFS">
	<legend>line items</legend>

	<input type="hidden" name="thelineitems" id="thelineitems" />
	<input type="hidden" id="lineitemschanged" name="lineitemschanged" />

	<input id="partnumber" name="partnumber" type="hidden" value="" />
	<input id="partname" name="partname" type="hidden" value="" />
	<input id="unitcost" name="unitcost" type="hidden" value="0" />
	<input id="unitweight" name="unitweight" type="hidden" value="0"/>
	<input id="taxable" name="taxable" type="hidden" value="1"/>
	<input id="imgpath" name="imgpath" type="hidden" value="<?php echo APP_PATH ?>common/stylesheet/<?php echo STYLESHEET ?>/image" />

	<table border="0" cellpadding="0" cellspacing="0" id="LITable">
		<thead>
			<tr id="LIHeader">
				<th nowrap="nowrap" class="queryheader" align="left" colspan="2">product</th>
				<th nowrap="nowrap" class="queryheader" align="left" id="memoHeader">memo</th>
				<th align="right" nowrap="nowrap" class="queryheader">price</th>
				<th align="center" nowrap="nowrap" class="queryheader">qty.</th>
				<th align="right" nowrap="nowrap" class="queryheader">extended</th>
				<th nowrap="nowrap" class="queryheader">&nbsp;</th>
			</tr>
		</thead>

		<tbody>
		<?php if($therecord["type"]!="Invoice" && $therecord["type"]!="VOID"){?>
		<tr id="LIAdd">
			<td colspan="2"><?php $theform->showField("productid")?></td>
			<td><input name="memo" type="text" id="memo" size="12" maxlength="255" /></td>
			<td align="right" nowrap="nowrap"><input name="price" type="text" id="price" value="<?php echo htmlQuotes(numberToCurrency(0))?>" size="10" maxlength="16" class="fieldCurrency" /></td>
			<td align="center" nowrap="nowrap"><input name="qty" type="text" id="qty" value="1" size="5" maxlength="16" /></td>
			<td align="right" nowrap="nowrap"><input name="extended" type="text" id="extended" class="uneditable fieldCurrency" value="<?php echo htmlQuotes(numberToCurrency(0))?>" size="12" maxlength="16" readonly="readonly" /></td>
			<td nowrap="nowrap" align="left"><button type="button" id="lineitemAddButton" class="graphicButtons buttonPlus" title="Add Line Item"><span>+</span></button></td>
		</tr><?php }//end if

		$lineitems->show();

		?><tr id="LITotals">
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
							if($therecord["shippingmethodid"]=="")
								$shipButtonDisable="Disabled";
							else{
								if($shippingMethods[$therecord["shippingmethodid"]]["canestimate"]==0)
									$shipButtonDisable="Disabled";
							}
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
						<input id="trackingno" name="trackingno" type="text" value="<?php echo htmlQuotes($therecord["trackingno"]) ?>" size="50" maxlength="64" />
					</p>
				</fieldset>
			</div>

			<div id="vContent4" class="vContent">
				<fieldset>
					<legend>Payment</legend>
					<?php if(hasRights("role:de7e6679-8bb2-29ee-4883-2fcd756fb120")){ ?>
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
							<input name="checkno" type="text" id="checkno" value="<?php echo htmlQuotes($therecord["checkno"])?>" size="20" maxlength="32" />
						</p>
						<p>
							<label for="bankname">bank name</label><br />
							<input id="bankname" name="bankname" type="text" value="<?php echo htmlQuotes($therecord["bankname"]) ?>" size="30" maxlength="64" />
						</p>
						<p id="pAccountNumber">
							<?php $theform->showfield("accountnumber"); ?>
						</p>
						<p>
							<?php $theform->showfield("routingnumber"); ?>
						</p>
					</div>

					<div id="ccpaymentinfo">
						<p id="fieldCCNumber">
							<?php $theform->showfield("ccnumber"); ?>
						</p>
						<p id="fieldCCExpiration">
							<?php $theform->showfield("ccexpiration"); ?>
						</p>
						<p>
							<?php $theform->showfield("ccverification"); ?>
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

			<table border="0" cellpadding="0" cellspacing="0" id="parenInfo">
				<tbody>
					<tr><td id="parenDiscount"><?php if($therecord["discountname"])  echo "(".htmlQuotes($therecord["discountname"]).")"; else echo "&nbsp;"; ?></td></tr>
					<tr><td class="blanks">&nbsp;</td></tr>
					<tr><td id="parenTax">
						<?php
							if($therecord["taxname"] || ((int) $therecord["taxpercentage"])!=0){
								echo "(";
								if($therecord["taxname"]) echo formatVariable($therecord["taxname"]).": ";
								echo $therecord["taxpercentage"]."%)";
							} else echo "&nbsp;";
						?>
					</td></tr>
					<tr><td id="parenShipping">
						<?php
							if($therecord["shippingmethodid"]){
								echo "(".htmlQuotes($shippingMethods[$therecord["shippingmethodid"]]["name"]).")";
							}else{
								echo "&nbsp;";
							}
							?>
					</td></tr>
					<tr><td class="blanks">&nbsp;</td></tr>
					<tr><td id="parenSpacer" class="blanks">&nbsp;</td></tr>
					<tr><td id="parenPayment"><?php if($therecord["paymentmethodid"]!="") echo "(".htmlQuotes($paymentMethods[$therecord["paymentmethodid"]]["name"]).")"; else echo "&nbsp;"?></td></tr>
				</tbody>
			</table>

		</td>
		<td colspan="2" class="invoiceTotalLabels vTabs" id="vTab1"><div>discount<input type="hidden" id="totalBD" name="totalBD" value="<?php echo $therecord["totaltni"]+$therecord["discountamount"]?>" /></div></td>
		<td class="totalItems"><input name="discountamount" id="discountamount" type="text" value="<?php echo numberToCurrency($therecord["discountamount"])?>" size="12" maxlength="15" onchange="clearDiscount();calculateTotal();" class="fieldCurrency fieldTotal"/></td>
		<td class="totalItems">&nbsp;</td>
  </tr><tr>
		<td colspan="2" class="invoiceTotalLabels"><div>subtotal</div></td>
		<td class="totalItems"><input class="uneditable fieldCurrency fieldTotal" name="totaltni" id="totaltni" type="text" value="<?php echo numberToCurrency($therecord["totaltni"])?>" size="12"  maxlength="15" readonly="readonly" onchange="calculateTotal();" /></td>
		<td class="totalItems">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" class="invoiceTotalLabels vTabs" id="vTab2"><div>tax</div></td>
		<td class="totalItems"><input name="tax" id="tax" type="text" value="<?php echo numberToCurrency($therecord["tax"])?>" size="12" maxlength="15" onchange="changeTaxAmount()" class="fieldCurrency fieldTotal" /></td>
		<td class="totalItems">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" class="invoiceTotalLabels vTabs" id="vTab3"><div>shipping</div></td>
		<td class="totalItems"><input name="shipping" id="shipping" type="text" value="<?php echo numberToCurrency($therecord["shipping"])?>" size="12" maxlength="15" onchange="calculateTotal();" class="fieldCurrency fieldTotal" /></td>
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
		<td class="totalItems"><input name="amountpaid" id="amountpaid" type="text" value="<?php echo numberToCurrency($therecord["amountpaid"])?>" size="12" maxlength="15" onchange="calculatePaidDue();"  class="important fieldCurrency fieldTotal" /></td>
		<td class="totalItems"><button id="payinfull" type="button" onclick="payInFull()" class="graphicButtons buttonCheck" title="Pay in full"><span>pay in full</span></button></td>
	</tr>
	<tr>
		<td colspan="2" class="invoiceTotalLabels" nowrap="nowrap"><div>amount due</div></td>
		<td class="totalItems"><input id="amountdue" name="amountdue" type="text" value="<?php echo numberToCurrency($therecord["amountdue"]) ?>" size="12" maxlength="15" onchange="calculatePaidDue();" class="important fieldCurrency fieldTotal" /></td>
		<td class="totalItems">&nbsp;</td>
	</tr>
	</tbody>
</table>
</fieldset>

<fieldset id="fsInstructions">
	<legend>instructions</legend>
	<p>
		<label for="specialinstructions">special instructions</label>
		<span class="notes"><em>(will not print on invoice)</em></span><br />
		<textarea id="specialinstructions" name="specialinstructions" cols="45" rows="3"><?php echo $therecord["specialinstructions"]?></textarea>
	</p>
	<p>
		<label for="printedinstructions">printed instructions</label><br />
		<textarea id="printedinstructions" name="printedinstructions" cols="45" rows="3"><?php echo $therecord["printedinstructions"]?></textarea>
	</p>

</fieldset>

<?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

<?php $theform->showGeneralInfo($phpbms,$therecord) ?>
</div>
</form>
<?php include("footer.php");?>
