<?php
/*
 $Rev: 155 $ | $LastChangedBy: mipalmer $
 $LastChangedDate: 2006-10-22 15:09:20 -0600 (Sun, 22 Oct 2006) $
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

	$thetable = new phpbmstable($db,301);
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle="Payment Method";

	$phpbms->cssIncludes[] = "pages/paymentmethods.css";
	$phpbms->jsIncludes[] = "modules/bms/javascript/paymentmethods.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputCheckbox("inactive",$therecord["inactive"]);
		$theform->addField($theinput);

		$theinput = new inputField("priority",$therecord["priority"],NULL,false,"integer",8,8);
		$theform->addField($theinput);

		$theinput = new inputField("name",$therecord["name"],NULL,true,NULL,28,64);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputCheckbox("onlineprocess",$therecord["onlineprocess"],"online process");
		$theinput->setAttribute("onchange","checkScript(this);");
		$theform->addField($theinput);

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");
?>
	<div class="bodyline">
	<?php $theform->startForm($pageTitle)?>

	<fieldset id="fsAttributes">
		<legend>attribues</legend>

		<p><br /><?php $theform->showField("inactive")?></p>

		<p><?php $theform->showField("priority")?></p>

		<p class="notes">
			Lower priority numbered items are displayed first.
		</p>
	</fieldset>

	<div id="nameDiv">
		<fieldset >
			<legend>name / type</legend>
				<p class="big"><?php $theform->showField("name") ?></p>
				<p>
					<label for="type">type</label><br />
					<select id="type" name="type">
						<option value="" <?PHP if($therecord["type"]=="") echo "selected=\"selected\""; ?>>&lt;None&gt;</option>
						<option value="draft" <?PHP if($therecord["type"]=="draft") echo "selected=\"selected\""; ?>>Draft</option>
						<option value="charge"<?PHP if($therecord["type"]=="charge") echo "selected=\"selected\""; ?>>Charge</option>
						<option value="receivable"<?PHP if($therecord["type"]=="receivable") echo "selected=\"selected\""; ?>>Receivable</option>
					</select>
				</p>
				<p class="notes">Type determines which fields are shown on the invoice creation
				  screen. For example, if a payment method of Business Check is selected with
				  a type of draft then the check number, routing number and account number
				  fields will be shown. However, if VISA is selected with a type of charge
				  then the credit card number and expiration date fields will be shown.
				</p>
		</fieldset>
		<fieldset >
			<legend>processing</legend>
			<p>
				<?php $theform->showField("onlineprocess") ?>
			</p>
			<p id="pProcessscript" <?php if($therecord["onlineprocess"]==0) echo "style=\"display:none\" "?>>
				<label for="processscript">process script</label><br />
				<input id="processscript" name="processscript" type="text" value="<?php echo htmlQuotes($therecord["processscript"])?>" size="64" maxlength="128"/>
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
