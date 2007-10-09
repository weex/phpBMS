<?php 
/*
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
	include("modules/bms/include/receipts.php");

	if(!isset($_GET["id"]))
		$_GET["id"] = 0;
	$_GET["id"] = (int) $_GET["id"];
	
	if(isset($_POST["referrer"]))
		$_SERVER['HTTP_REFERER'] = $_POST["referrer"];
	
	$thetable = new receipts($db,304);
	$therecord = $thetable->processAddEditPage();
	
	if($therecord["id"]){
		$items = new receiptitems($db);
		$itemsresult = $items->get($therecord["id"]);
	}//end if

	$pageTitle = "Receipt";	

	$phpbms->cssIncludes[] = "pages/receipts.css";
	$phpbms->jsIncludes[] = "modules/bms/javascript/receipt.js";
	$phpbms->jsIncludes[] = "modules/bms/javascript/paymentprocess.js";

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputDatePicker("receiptdate", $therecord["receiptdate"], "date", true);
		$theform->addField($theinput);

		$theinput = new inputBasicList("status",$therecord["status"],array("open"=>"open","collected"=>"collected"));
		$theform->addField($theinput);

		$theinput = new inputCheckBox("readytopost",$therecord["readytopost"],"ready to post");
		$theform->addField($theinput);

		$theinput = new inputCheckBox("posted", $therecord["posted"] ,"posted");
		$theinput->setAttribute("disabled","disabled");
		$theform->addField($theinput);

		$theinput = new inputAutofill($db, "clientid",$therecord["clientid"],2,"clients.id","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)",
										"if(clients.city!=\"\",concat(clients.city,\", \",clients.state),\"\")",
										"clients.inactive!=1 AND clients.type='client' AND clients.hascredit = 1", "client", true);					
		$theinput->setAttribute("size","51");
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);
		
		$theinput = new inputCurrency("amount", $therecord["amount"], "amount", true);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);
		
		$theinput = new inputField("ccnumber", $therecord["ccnumber"], "card number", false, false, 28,40);
		$theform->addField($theinput);
		
		$theinput = new inputField("ccexpiration", $therecord["ccexpiration"], "expiration", false, false, 8,10);
		$theform->addField($theinput);

		$theinput = new inputField("ccverification", $therecord["ccverification"], "verification/pin", false, false, 8,7);
		$theform->addField($theinput);

		$theinput = new inputField("bankname", $therecord["bankname"], "bank name", false, false, 30, 64);
		$theform->addField($theinput);

		$theinput = new inputField("checkno", $therecord["checkno"], "check number", false, false, 20 , 32);
		$theform->addField($theinput);

		$theinput = new inputField("accountnumber", $therecord["accountnumber"], "account number", false, "integer", 20, 64);
		$theform->addField($theinput);
		
		$theinput = new inputField("routingnumber", $therecord["routingnumber"], "routing number", false, "integer", 30, 64);
		$theform->addField($theinput);

		$theinput = new inputField("transactionid", $therecord["transactionid"], "transaction id", false, false, 32, 64);
		$theform->addField($theinput);

		$theinput = new inputChoiceList($db,"paymentother",$therecord["paymentother"],"receiptother", "reason");
		$theform->addField($theinput);

		$theinput = new inputTextarea("memo", $therecord["memo"], NULL, false, 3, 48, false);
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		
	include("header.php");
	
?><div class="bodyline">
<form action="<?php echo str_replace("&", "&amp;", $_SERVER["REQUEST_URI"]) ?>" method="post" name="record" id="record">

	<div id="topButtons"><?php showSaveCancel(1); ?></div>
	
	<h1 class="h1Title"><span><?php echo $pageTitle?></span></h1>
	
	<div id="rightSide">
		<fieldset>
			<legend>attributes</legend>
			
			<p>
				<label for="id">id</label><br />
				<input name="id" id="id" type="text" value="<?php echo $therecord["id"]; ?>" size="11" maxlength="11" readonly="readonly" class="uneditable"  />
			</p>
			
			<p><?php $theform->showField("receiptdate")?></p>
			
			<?php if($therecord["posted"]) { ?>
				<p><?php $theform->showField("posted")?></p>
			<?php }//endif ?>

			<p><?php $theform->showField("status")?></p>

			<p><?php $theform->showField("readytopost")?></p>
			
		</fieldset>
	</div>
	
	<div id="leftSide">
		
		<fieldset>
			<legend>Client / Amount</legend>
			
			<p><?php $theform->showField("clientid")?></p>
			
			<p><?php $theform->showField("amount")?></p>
			
		</fieldset>
		
		<fieldset>
			<legend>Receipt Type</legend>
			<p><?php $thetable->showPaymentOptions($therecord["paymentmethodid"])?></p>
			
			<div id="draft" class="paymentTypes">

				<p id="bankNameP"><?php $theform->showField("bankname")?></p>
			
				<p ><?php $theform->showField("checkno")?></p>

				<p id="routingNumberP" ><?php $theform->showField("routingnumber")?></p>
				
				<p><?php $theform->showField("accountnumber")?></p>
				
			</div>

			<div id="charge" class="paymentTypes">

				<p id="ccNumberP"><?php $theform->showField("ccnumber")?></p>
				
				<p><?php $theform->showField("ccexpiration")?></p>
				
				<p><?php $theform->showField("ccverification")?></p>
			
			</div>

			<div id="other" class="paymentTypes">

				<p><?php $theform->showField("paymentother")?></p>
								
			</div>

			<div id="transactionP">				
				
				<input type="hidden" id="processscript" />
				
				<p id="transactionidP"><?php $theform->showField("transactionid")?></p>
				
				<p>
					<br/>
					<button id="paymentProcessButton" type="button" class="graphicButtons buttonMoney" title="process payment online"><span>process payment online</span></button>
				</p>
				
			</div>

		</fieldset>
				
	</div>

	<fieldset>
		<legend>Distribution Items</legend>
		
		<div id="arRightButtons">
			<button type="button" class="smallButtons" id="loadOpenButton">load open AR items</button>
			<button type="button" class="smallButtons" id="autoApplyButton">auto-apply</button>
		</div>
		
		<div>
			<button type="button" id="addARItemButton" class="graphicButtons buttonPlus" title="add item"><span>+</span></button>
			&nbsp;distribution remaining: <input type="text" id="distributionRemaining" class="invisibleTextField" readonly="readonly"/>
		</div>
		
		<input type="hidden" id="itemschanged" name="itemschanged" />
		<input type="hidden" id="itemslist" name="itemslist" />
		<table class="querytable" border="0" cellpadding="0" cellspacing="0" id="itemsTable">

			<thead>
				<tr>
					<th align="left" nowrap="nowrap">doc ref</th>
					<th align="left">type</th>
					<th align="left" nowrap="nowrap">doc date</th>
					<th align="left" nowrap="nowrap">due date</th>
					<th align="right" width="100%" nowrap="nowrap">doc amount</th>
					<th align="right" nowrap="nowrap">doc due</th>
					<th align="right">applied</th>
					<th align="right">discount</th>
					<th align="right" nowrap="nowrap">tax adj.</th>
					<th>&nbsp;</th>
				</tr>
			</thead>

			<tfoot>
				<tr class="queryfooter">
					<td colspan="5" align="right">&nbsp;</td>
					<td><input id="totaldue" class="invisibleTextField currency" size="10" maxlength="12" readonly="readonly" value="$0.00"/></td>
					<td><input id="totalapplied" class="invisibleTextField currency" size="10" maxlength="12" readonly="readonly" value="$0.00"/></td>
					<td colspan="3">&nbsp;</td>
				</tr>
			</tfoot>
			
			<tbody id="itemsTbody">
				<?php 
					if($therecord["id"]){
						$items->show($itemsresult, $therecord["posted"], $therecord["id"]);
					}//end if
				?>
			</tbody>
		</table>
		
	</fieldset>
	
	<fieldset>
		<legend>notes</legend>

		<p><?php $theform->showField("memo")?></p>

	</fieldset>
			
	<?php $theform->showCreateModify($phpbms,$therecord) ?>
</form>
</div>
<?php include("footer.php")?>