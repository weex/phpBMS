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

	$thetable = new phpbmstable($db,204);
	$therecord = $thetable->processAddEditPage();


	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$phpbms->cssIncludes[] = "pages/base/smartsearches.css";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputField("id",$therecord["id"],NULL,false,NULL,9,64);
		$theinput->setAttribute("class","uneditable");
		$theinput->setAttribute("readonly","readonly");
		$theform->addField($theinput);

		$theinput = new inputField("name",$therecord["name"],NULL,true,NULL,50,255,false);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputDataTableList($db, "moduleid", $therecord["moduleid"], "modules", "id", "displayname",
								"", "", false, "module");
		$theform->addField($theinput);

		$theinput = new inputDataTableList($db, "tabledefid", $therecord["tabledefid"], "tabledefs", "id", "displayname",
								"", "", false, "table");
		$theform->addField($theinput);

		$theinput = new inputTextarea("fromclause",$therecord["fromclause"], "from clause" ,true, 3,80);
		$theform->addField($theinput);

		$theinput = new inputField("valuefield",$therecord["valuefield"],"value field",true,NULL,50,255);
		$theform->addField($theinput);

		$theinput = new inputTextarea("displayfield",$therecord["displayfield"], "display field" ,true, 3,80);
		$theform->addField($theinput);

		$theinput = new inputTextarea("secondaryfield",$therecord["secondaryfield"], "secondary field" ,true, 3,80);
		$theform->addField($theinput);

		$theinput = new inputTextarea("classfield",$therecord["classfield"], "class field" ,true, 3,80);
		$theform->addField($theinput);

		$theinput = new inputTextarea("rolefield",$therecord["rolefield"], "role field" ,false, 3,80);
		$theform->addField($theinput);

		$theinput = new inputTextarea("searchfields",$therecord["searchfields"], "search fields" ,true, 3,80);
		$theform->addField($theinput);

		$theinput = new inputTextarea("filterclause",$therecord["filterclause"], "filter clause" ,true, 3,80);
		$theform->addField($theinput);

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements


	$pageTitle="Smart Search";

	include("header.php");
?><div class="bodyline">
	<?php $theform->startForm($pageTitle)?>

	<div id="rightSideDiv">
		<fieldset>
			<legend>attribues</legend>
			<p><?php $theform->showField("id");?></p>
			<p><?php $theform->showField("moduleid");?></p>
			<p><?php $theform->showField("tabledefid");?></p>
		</fieldset>
	</div>

	<div id="leftSideDiv">

		<fieldset>
			<legend><label for="name">name</label></legend>
			<p><?php $theform->showField("name"); ?></p>
			<p class="notes">Name must be unique.</p>
		</fieldset>

		<fieldset >
			<legend>sql</legend>

			<p><?php $theform->showField("fromclause"); ?></p>

			<p><?php $theform->showField("valuefield"); ?></p>

			<p><?php $theform->showField("searchfields"); ?></p>

			<p><?php $theform->showField("displayfield"); ?></p>

			<p><?php $theform->showField("secondaryfield"); ?></p>

			<p><?php $theform->showField("classfield"); ?></p>

			<p><?php $theform->showField("rolefield"); ?></p>

			<p><?php $theform->showField("filterclause"); ?></p>

		</fieldset>

                <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

	</div>
	<?php
		$theform->showCreateModify($phpbms,$therecord);
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>
