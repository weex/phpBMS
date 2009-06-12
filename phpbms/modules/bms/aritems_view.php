<?php
/*
 $Rev: 285 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-27 14:05:27 -0600 (Mon, 27 Aug 2007) $
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
	include("include/fields.php");
	include("include/tables.php");
	include("modules/bms/include/aritems.php");

	$aritems = new phpbmstable($db,"tbld:c595dbe7-6c77-1e02-5e81-c2e215736e9c");
	$therecord = $aritems->processAddEditPage();

	$payments = new aritemPayments($db, $therecord);

	$client = new relatedClient($db, $therecord["clientid"]);
	$clientInfo = $client->getClientInfo();

    $clientName = $clientInfo["name"];

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$phpbms->cssIncludes[] = "pages/aritems.css";
	$phpbms->jsIncludes[] = "modules/bms/javascript/aritem.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputCheckbox("posted",$therecord["posted"]);
		$theinput->setAttribute("disabled","disabled");
		$theform->addField($theinput);

		$theinput = new inputField("client",$clientName,NULL,true,NULL,60,128);
		$theinput->setAttribute("class","uneditable");
		$theinput->setAttribute("readonly","readonly");
		$theform->addField($theinput);

		$theinput = new inputField("type",$therecord["type"],NULL,true,NULL,14,64);
		$theinput->setAttribute("class","uneditable");
		$theinput->setAttribute("readonly","readonly");
		$theform->addField($theinput);

		$theinput = new inputField("status",$therecord["status"],NULL,true,NULL,9,64);
		$theinput->setAttribute("class","uneditable");
		$theinput->setAttribute("readonly","readonly");
		$theform->addField($theinput);

		$theinput = new inputField("theid",$therecord["id"],"id",true,NULL,9,64);
		$theinput->setAttribute("class","uneditable");
		$theinput->setAttribute("readonly","readonly");
		$theform->addField($theinput);

		$theinput = new inputField("relatedid",$therecord["relatedid"],"related record id",true,NULL,14,64);
		$theinput->setAttribute("class","uneditable");
		$theinput->setAttribute("readonly","readonly");
		$theform->addField($theinput);

		$theinput = new inputCurrency("amount", $therecord["amount"]);
		$theinput->setAttribute("class","uneditable");
		$theinput->setAttribute("readonly","readonly");
		$theform->addField($theinput);

		$theinput = new inputCurrency("due", $therecord["amount"] - $therecord["paid"]);
		$theinput->setAttribute("class","uneditable");
		$theinput->setAttribute("readonly","readonly");
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements


	$pageTitle="AR Item";

	include("header.php");
?><div class="bodyline">
	<?php $theform->startForm($pageTitle)?>
	<input type="hidden" id="invoiceEdit" value="<?php echo getAddEditFile($db, "tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883") ?>" />
	<input type="hidden" id="receiptEdit" value="<?php echo getAddEditFile($db, "tbld:43678406-be25-909b-c715-7e2afc7db601") ?>" />
	<input type="hidden" id="clientEdit" value="<?php echo getAddEditFile($db, "tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083") ?>" />
	<input type="hidden" id="clientid" value="<?php echo $clientInfo["id"] ?>" />
	<div id="rightSideDiv">
		<fieldset id="fsAttributes">
			<legend>attribues</legend>

			<p><?php $theform->showField("theid")?></p>

			<p><?php $theform->showField("status")?></p>

			<p><?php $theform->showField("posted")?></p>

		</fieldset>
	</div>


	<div id="leftSideDiv">
		<fieldset >
			<legend>item</legend>

			<p><?php $theform->showField("client"); ?> <button type="button" title="view client" class="graphicButtons buttonInfo" id="viewClient"><span>View Record</span></button></p>

			<p><?php $theform->showField("type"); ?></p>

			<p><?php $theform->showField("relatedid"); ?> <button type="button" title="view related record" class="graphicButtons buttonInfo" id="viewRelatedButton"><span>View Record</span></button></p>

		</fieldset>

		<fieldset>
			<legend>amount</legend>

			<p><?php $theform->showField("amount")?></p>

			<p><?php $theform->showField("due")?></p>

		</fieldset>
	</div>

	<fieldset>
		<legend>payments</legend>

		<div class="fauxP"><?php $payments->show()?></div>

	</fieldset>

	<?php
		$theform->showGeneralInfo($phpbms,$therecord);
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>
