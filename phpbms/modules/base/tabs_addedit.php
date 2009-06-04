<?php
/*
 $Rev: 205 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-03-26 15:50:25 -0700 (Mon, 26 Mar 2007) $
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

	$thetable = new phpbmsTable($db, "tbld:7e75af48-6f70-d157-f440-69a8e7f59d38");
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle="Tab";
	$phpbms->cssIncludes[] = "pages/tabs.css";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputField("name",$therecord["name"],NULL,true,NULL,32,64);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputField("displayorder",$therecord["displayorder"],"display order",true,NULL,10,10);
		$theform->addField($theinput);

		$theinput = new inputRolesList($db,"roleid",$therecord["roleid"],"access (role)");
		$theform->addField($theinput);

		$theinput = new inputChoiceList($db,"tabgroup",$therecord["tabgroup"],"tabgroups", "tab group");
		$theform->addField($theinput);

		$theinput = new inputCheckbox("enableonnew",$therecord["enableonnew"],"enable on new");
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

		<p>
			<?php $theform->showField("displayorder"); ?><br />
			<span class="notes">Lower numbers are displayed first.</span>
		</p>

		<p><?php $theform->showField("roleid")?></p>

		<p><?php $theform->showField("enableonnew")?></p>

	</fieldset>

	<div id="leftSideDiv">
		<fieldset>
			<legend>details</legend>

			<p class="big"><?php $theform->showField("name"); ?></p>

			<p><?php $theform->showField("tabgroup"); ?></p>

			<p>
				<label for="location">location</label><br />
				<input id="location" name="location" value="<?php echo htmlQuotes($therecord["location"])?>" size="64" maxlength="128" /><br />
				<span class="notes">location should be relative to application root.</span>
			</p>

			<p>
				<label for="tooltip">tool tip</label><br />
				<input id="tooltip" name="tooltip" value="<?php echo htmlQuotes($therecord["tooltip"])?>" size="64" maxlength="128" />
			</p>
		</fieldset>
		<fieldset>
			<legend><label for="notificationsql">Notification SQL</label></legend>
			<span class="notes">SQL statement used to determine display of notification icon on tab.  SQL statment should return a single record, with field "theresult" - a number.  Use {{id]}} for id substitution.</span><br />
			<textarea id="notificationsql" name="notificationsql" cols="40" rows="5"><?php echo htmlQuotes($therecord["notificationsql"])?></textarea>
		</fieldset>

                <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

	</div>

	<?php
		$theform->showGeneralInfo($phpbms,$therecord);
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>
