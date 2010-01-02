<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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
	include("include/tables.php");
	include("include/fields.php");
	include("include/relationships.php");

	$thetable = new relationships($db, "tbld:8d19c73c-42fb-d829-3681-d20b4dbe43b9");
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle="Table Relationship";

	$phpbms->cssIncludes[] = "pages/relationships.css";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputField("name",$therecord["name"],NULL,true,NULL,64,64);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputField("fromfield",$therecord["fromfield"],"from field",true,NULL,32,64);
		$theform->addField($theinput);

		$theinput = new inputField("tofield",$therecord["tofield"],"to field",true,NULL,32,64);
		$theform->addField($theinput);

		$theinput = new inputCheckbox("inherint",$therecord["inherint"],"inherent relationship");
		$theform->addField($theinput);

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");

?><div class="bodyline">
	<?php $theform->startForm($pageTitle)?>

	<fieldset>
		<legend><label for="name">name</label></legend>
		<p class="big">
			<?php $theform->showField("name");?>
		</p>
	</fieldset>

	<fieldset>
		<legend>From</legend>
		<p>
			<label for="fromtableid">from table definition</label><br />
			<?php $thetable->displayTables("fromtableid",$therecord["fromtableid"]) ?>
		</p>

		<p><?php $theform->showField("fromfield"); ?></p>

	</fieldset>

	<fieldset>
		<legend>to</legend>
		<p>
			<label for="totableid">table</label><br />
			<?php $thetable->displayTables("totableid",$therecord["totableid"]) ?>
		</p>

		<p><?php $theform->showField("tofield"); ?></p>

		<p><?php $theform->showField("inherint");?></p>

		<p class="notes">
			Note: Use "inherent relationship" if the "to table" is already included in the "from table" query table (see the
			"from table's" definition)
		</p>
	</fieldset>

	<?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

	<?php
		$theform->showGeneralInfo($phpbms,$therecord);
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>
