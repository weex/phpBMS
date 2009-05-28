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

	include("include/invoicestatuses.php");

	$thetable = new invoiceStatus($db,302);
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle="Invoice Status";
	$phpbms->cssIncludes[] = "pages/invoicestatuses.css";

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

		$theinput = new inputCheckbox("invoicedefault",$therecord["invoicedefault"],"new order default");
		$theform->addField($theinput);

		$theinput = new inputCheckbox("setreadytopost",$therecord["setreadytopost"],"set ready to post when status selected");
		$theform->addField($theinput);

		$theinput = new inputSmartSearch($db, "defaultassignedtoid", "Pick Active User", $therecord["defaultassignedtoid"], "assigned to", false, 42);
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
		<p>
			<label for="id">id</label><br />
			<input name="id" id="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="readonly" class="uneditable" />
		</p>

		<p><?php $theform->showField("inactive")?></p>

		<p><?php $theform->showField("priority")?></p>

		<p class="notes">Lower priority numbered items are displayed first.</p>

	</fieldset>

	<div id="nameDiv">
		<fieldset >
			<legend>name</legend>

			<p><?php $theform->showField("name");?></p>

		</fieldset>
		<fieldset>
			<legend>Defaults</legend>
			<p>
				<?php $theform->showField("invoicedefault")?>
			</p>
			<p>
				<?php $theform->showField("setreadytopost")?>
			</p>
			<p>
				<?php $theform->showField("defaultassignedtoid")?>
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
