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
	include("./include/discounts.php");

	$thetable = new discounts($db,"tbld:455b8839-162b-3fcb-64b6-eeb946f873e1");
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$stats = $thetable->getTotals($therecord["id"]);

	$pageTitle="Discount / Promotion";

	$phpbms->cssIncludes[] = "pages/discounts.css";
	$phpbms->jsIncludes[] = "modules/bms/javascript/discount.js";
	$phpbms->onload[] = "init();";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputCheckbox("inactive",$therecord["inactive"]);
		$theform->addField($theinput);

		$theinput = new inputField("name",$therecord["name"],NULL,true,NULL,32,64);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputPercentage("percentvalue",$therecord["value"],"value",2,true);
		$theform->addField($theinput);

		$theinput = new inputCurrency("amountvalue",$therecord["value"], "value" ,true);
		$theform->addField($theinput);

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");

?><div class="bodyline">
	<?php $theform->startForm($pageTitle)?>

	<div id="fsAttributes">
		<fieldset>
			<legend>attributes</legend>
			<p>
				<?php $theform->showField("inactive")?>
			</p>
		</fieldset>

		<fieldset>
			<legend>statistics</legend>
			<p>
				<strong>Orders</strong> (<?php echo $stats["Order"]["total"]?>)<br/>
				total: <?php echo numberToCurrency($stats["Order"]["sum"])?>
			</p>
			<p>
				<strong>Invoices</strong> (<?php echo $stats["Invoice"]["total"]?>)<br />
				total: <?php echo numberToCurrency($stats["Invoice"]["sum"])?>
			</p>
		</fieldset>
	</div>

	<div id="nameDiv">
		<fieldset>
			<legend>name</legend>
			<p class="big">
				<?php $theform->showField("name") ?>
			</p>
		</fieldset>

		<fieldset>
			<legend>discount</legend>
			<p>
				<strong>type</strong><br />
				<input type="radio" class="radiochecks" id="typePercentage" name="type" value="percent" <?php if($therecord["type"]=="percent") echo "checked=\"checked\"" ?>  onclick="changeType()" /><label for="typePercentage">percentage</label>
				&nbsp;
				<input type="radio" class="radiochecks" id="typeAmount" name="type" value="amount" <?php if($therecord["type"]=="amount") echo "checked=\"checked\"" ?> onclick="changeType()" /><label for="typeAmount">amount</label>
			</p>
			<p id="pValue">
				<?php $theform->showField("percentvalue");?>
			</p>
			<p id="aValue">
				<?php $theform->showField("amountvalue");?>
			</p>
		</fieldset>

		<fieldset>
			<legend><label for="description">description</label></legend>
			<p>
				<span class="notes">(description is used on invoice reports)</span><br />
				<textarea id="description" name="description" cols="38" rows="4"><?php echo htmlQuotes($therecord["description"])?></textarea>
			</p>
		</fieldset>

                <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

        </div>

	<?php
		$theform->showGeneralInfo($phpbms,$therecord);
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>
