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
	include("include/menu.php");

	$thetable = new menus($db,19);
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle="Menu Item";

	$phpbms->cssIncludes[] = "pages/menus.css";
	$phpbms->jsIncludes[] = "modules/base/javascript/menu.js";

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

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	$phpbms->bottomJS[] = "showTypeDetails();";

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

	</fieldset>

	<div id="leftSideDiv">
		<fieldset>
			<legend><label for="name">name</label></legend>

			<p class="big"><?php $theform->showField("name"); ?></p>

		</fieldset>

		<fieldset>
			<legend>type</legend>
			<p class="typeP">
				<input type="radio" id="type1" value="cat" <?php if($therecord["link"]=="") echo "checked=\"checked\"" ?> name="radio" onclick="showTypeDetails();"  class="radiochecks" /><label for="type1">category</label><br />
				<img src="menu-example-category.png" width="220" height="167" class="typeImage" alt="category" />
			</p>

			<p class="typeP">
				<input type="radio" id="type2" value="search" <?php if(strpos($therecord["link"],"search.php?id=")!==false) echo "checked=\"checked\"" ?> name="radio" onclick="showTypeDetails();" class="radiochecks" /><label for="type2">table definition search</label><br />
				<img src="menu-example-tabledef.png" width="220" height="167" class="typeImage" alt="table definition search" />
			</p>

			<p>
				<input type="radio" id="type3" value="link" <?php if(strpos($therecord["link"],"search.php?id=")===false && $therecord["link"]!="") echo "checked=\"checked\"" ?> name="radio" onclick="showTypeDetails();" class="radiochecks" /><label for="type3">page link</label><br />
				<img src="menu-example-link.png" width="220" height="167" class="typeImage" alt="page link" />
			</p>
		</fieldset>
	</div>

	<div id="details">
		<fieldset>
			<legend>link / parent</legend>
			<p id="thelink">
				<label for="link">link</label> <span class="notes">(URL)</span><br />
				<input id="link" name="link" type="text" value="<?php if(substr($therecord["link"],0,10)!="search.php" ) echo htmlQuotes($therecord["link"])?>" size="64" maxlength="255" />
			</p>
			<p id="thetabledef">
				<label  for="linkdropdown">table definition</label><br />
				<?php $thetable->displayTableDropDown($therecord["link"]) ?>
			</p>
			<p>
				parent<br/>
				<?php  $thetable->displayParentDropDown($therecord["parentid"],$therecord["uuid"]) ?>
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
