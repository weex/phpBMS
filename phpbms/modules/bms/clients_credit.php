<?php
/*
 $Rev: 285 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-27 14:05:27 -0600 (Mon, 27 Aug 2007) $
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
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
	include("modules/bms/include/clients_credit.php");

	if(!isset($_GET["id"])) $error = new appError(300,"Passed variable not set (id)");

	$clientCredit = new clientCredit($db, $_GET["id"]);

	if(isset($_POST["creditlimit"])){
		if($clientCredit->update(addSlashesToArray($_POST) ))
			$statusmessage = "Credit Updated";
	}

	$therecord = $clientCredit->get();

	//setting page title
	$pageTitle="Credit: ";
	if($therecord["company"]=="")
		$pageTitle.=$therecord["firstname"]." ".$therecord["lastname"];
	else
		$pageTitle.=$therecord["company"];

	$phpbms->cssIncludes[] = "pages/clients_credit.css";
	$phpbms->jsIncludes[] = "modules/bms/javascript/clients_credit.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputCheckbox("hascredit",$therecord["hascredit"],"has credit");
		if($therecord["type"] == "prospect")
			$theinput->setAttribute("disabled","disabled");
		$theform->addField($theinput);

		$theinput = new inputCurrency("creditlimit", $therecord["creditlimit"], "credit limit");
		if($therecord["type"] == "prospect")
			$theinput->setAttribute("readonly","readonly");
		$theform->addField($theinput);

		$theinput = new inputCurrency("creditleft", ($therecord["creditlimit"]-$therecord["outstanding"]), "credit left");
		$theinput->setAttribute("readonly","readonly");
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements


	include("header.php");

	$phpbms->showTabs("clients entry", "tab:5a6ef814-2689-4e3b-2609-db43fb3cc001", ((int) $_GET["id"]));?><div class="bodyline">
	<form action="<?php echo htmlentities($_SERVER["REQUEST_URI"]) ?>"
	method="post" name="record" id="record">
		<div id="topButtons">
			<input type="button" class="Buttons" id="update1" name="update" value="save"/>
		</div>

		<h1 id="h1Title"><span><?php echo $pageTitle ?></span></h1>
		<input type="hidden" id="type" name="type" value="<?php echo $therecord["type"]?>" />

		<fieldset>
			<legend>Credit</legend>
			<?php if($therecord["type"] == "prospect") {?>
			<p class="notes">Credit can only be set for clients.</p>
			<?php }?>

			<p><?php $theform->showField("hascredit")?></p>

			<p><?php $theform->showField("creditlimit")?></p>


			<input type="hidden" id="outstanding" value="<?php echo $therecord["outstanding"]?>" />
			<p><?php $theform->showField("creditleft")?></p>
		</fieldset>

		<?php  if($therecord["hascredit"]) {?>

		<fieldset>
			<legend>open items</legend>
			<div class="fauxP">

				<?php $clientCredit->showHistory($_GET["id"])?>

			</div>
		</fieldset>

		<?php  } //end if?>

		<div align="right">
			<input type="button" class="Buttons" id="update2" name="update" value="save"/>
		</div>

	</form>
	</div>
<?php include("footer.php");?>
