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
	include("include/roles.php");

	$thetable = new roles($db,200);
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle="Role";

	$phpbms->cssIncludes[] = "pages/roles.css";
	$phpbms->jsIncludes[] = "modules/base/javascript/roles.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();
		$theform->onsubmit="return submitForm(this);";

		$theinput = new inputCheckbox("inactive",$therecord["inactive"]);
		$theform->addField($theinput);

		$theinput = new inputField("name",$therecord["name"],NULL,true,NULL,28,64);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");

?><div class="bodyline">
	<?php $theform->startForm($pageTitle)?>

	<fieldset id="fsAttributes">
		<legend>attributes</legend>

		<p><?php $theform->showField("inactive")?></p>

	</fieldset>

	<div id="leftSideDiv">
		<fieldset>
			<legend>name</legend>
			<p class="big"><?php $theform->showField("name") ?></p>
		</fieldset>

		<fieldset>
			<legend><label for="description">description</label></legend>
			<p>
				<textarea name="description" cols="45" rows="5" id="description"><?php echo htmlQuotes($therecord["description"])?></textarea>
			</p>
		</fieldset>

                <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

		<?php if($therecord["id"]){?>
		<fieldset>
			<legend>users</legend>
			<input type="hidden" name="userschanged" id="userschanged" value="0" />
			<input type="hidden" name="newusers" id="newusers" value="" />
			<div class="fauxP">
			<div id="assignedusersdiv">
				users assigned to group<br />
				<select id="assignedusers" size="10" multiple>
					<?php $thetable->displayUsers($therecord["id"],"assigned")?>
				</select>
			</div>
			<div id="usersbuttonsdiv">
				<p>
					<button type="button" class="Buttons" onclick="moveUser('availableusers','assignedusers')">&lt; add user</button>
				</p>
				<p>
					<button type="button" class="Buttons" onclick="moveUser('assignedusers','availableusers')">remove user &gt;</button>
				</p>
			</div>
			<div id="availableusersdiv">
				available users<br />
				<select id="availableusers" size="10" multiple>
					<?php $thetable->displayUsers($therecord["id"],"available")?>
				</select>
			</div>
			</div>
		</fieldset>
		<?php }?>

	</div>

	<?php
		$theform->showGeneralInfo($phpbms,$therecord);
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>
