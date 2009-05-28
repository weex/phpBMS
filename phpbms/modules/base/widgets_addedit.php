<?php
/*
 $Rev: 331 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-10-15 16:13:23 -0600 (Mon, 15 Oct 2007) $
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
	include("include/widgets.php");

	$thetable = new widgets($db, 205);
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle="Widget";

	$phpbms->cssIncludes[] = "pages/base/widgets.css";
	//$phpbms->jsIncludes[] = "modules/base/javascript/menu.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputField("title", $therecord["title"], NULL, true);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$temparray = array("Large Side"=>"big", "Smaller Side"=>"little");
		$theinput = new inputBasiclist("type", $therecord["type"], $temparray, "Area");
		$theform->addField($theinput);

		$theinput = new inputDataTableList($db, "moduleid", $therecord["moduleid"], "modules", "id", "displayname",
								"", "", false, "module");
		$theform->addField($theinput);

		$theinput = new inputCheckbox("default", $therecord["default"], NULL);
		$theform->addField($theinput);

		$theinput = new inputField("file", $therecord["file"], NULL, true);
		$theform->addField($theinput);

		$theinput = new inputRolesList($db,"roleid",$therecord["roleid"],"access (role)");
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

		<p><?php $theform->showField("type")?></p>

		<p><?php $theform->showField("default")?></p>

		<p><?php $theform->showField("moduleid")?></p>

		<p><?php $theform->showField("roleid")?></p>

	</fieldset>

	<div id="leftSideDiv">
		<fieldset>
			<legend>title / file</legend>

			<p class="big">
				<?php $theform->showField("title"); ?>
			</p>

			<p>
				<?php $theform->showField("file"); ?><br />
				<span class="notes">path relative to base directory of class that extends widget class</span>
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
