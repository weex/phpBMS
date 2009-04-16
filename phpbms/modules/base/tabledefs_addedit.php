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
	include("include/tabledefs.php");

	$thetable = new tableDefinitions($db,11);
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];


	$pageTitle="Table Definition";

	$phpbms->cssIncludes[] = "pages/tabledefs.css";
	$phpbms->jsIncludes[] = "modules/base/javascript/tabledefs.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputField("id",$therecord["id"],NULL,false,NULL,9,64);
		$theinput->setAttribute("class","uneditable");
		$theinput->setAttribute("readonly","readonly");
		$theform->addField($theinput);

		$theinput = new inputBasicList ("type",$therecord["type"],$list = array("table"=>"table","view"=>"view","system"=>"system"));
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		if(!$therecord["id"]){

			$theinput = new inputField("newid","","new id",false,"integer",9);
			$theform->addField($theinput);

		}//endif - id

		$theinput = new inputDataTableList($db, "moduleid", $therecord["moduleid"], "modules", "id", "displayname",
								"", "", false, "module");
		$theform->addField($theinput);

		$theinput = new inputField("displayname",$therecord["displayname"],"display name",true,NULL,50,64,false);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputField("maintable",$therecord["maintable"],"primary table name",true,NULL,50,64);
		$theform->addField($theinput);

		$theinput = new inputField("importfile",$therecord["importfile"],"import records file",false,NULL,100,128);
		$theform->addField($theinput);

		$theinput = new inputRolesList($db,"importroleid",$therecord["importroleid"],"import access (role)");
		$theform->addField($theinput);

		$theinput = new inputField("addfile",$therecord["addfile"],"add new record file",true,NULL,100,128);
		$theform->addField($theinput);

		$theinput = new inputRolesList($db,"addroleid",$therecord["addroleid"],"add access (role)");
		$theform->addField($theinput);

		$theinput = new inputField("editfile",$therecord["editfile"],"edit record file",true,NULL,100,128);
		$theform->addField($theinput);

		$theinput = new inputRolesList($db,"editroleid",$therecord["editroleid"],"edit access (role)");
		$theform->addField($theinput);

		$theinput = new inputRolesList($db,"searchroleid",$therecord["searchroleid"],"search access (role)");
		$theform->addField($theinput);

		$theinput = new inputRolesList($db,"advsearchroleid",$therecord["advsearchroleid"],"advanced search access (role)");
		$theform->addField($theinput);

		$theinput = new inputRolesList($db,"viewsqlroleid",$therecord["viewsqlroleid"],"view SQL statement access (role)");
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	$phpbms->topJS[] = "requiredArray[requiredArray.length]=new Array('querytable','Search/Display SQL FROM clause cannot be blank.');";
	$phpbms->topJS[] = "requiredArray[requiredArray.length]=new Array('defaultwhereclause','default search cannot be blank.');";
	$phpbms->topJS[] = "requiredArray[requiredArray.length]=new Array('defaultsortorder','default sort order cannot be blank.');";

	include("header.php");

	$phpbms->showTabs("tabledefs entry",1,$therecord["id"]);
?><div class="bodyline">
	<?php $theform->startForm($pageTitle)?>

	<fieldset id="fsAttributes">
		<legend>attributes</legend>

		<p><?php $theform->showField("id"); ?></p>

		<?php if(!$therecord["id"]){?>
		<p><?php $theform->showField("newid"); ?><br />
			<span class="notes">Optionally, you can
			specify the id to be used.  Make sure that
			the id is not already in use.  Base module
			ids run in the 200s, bms in the 300s, recurring
			invoices in the 400s.
			</span>
		</p>
		<?php }?>

		<p><?php $theform->showField("type"); ?></p>

		<p><?php $theform->showField("moduleid");?></p>

		<p>
			<label for="deletebutton">delete record display name</label><br />
			<input id="deletebutton" name="deletebutton" type="text" value="<?php echo htmlQuotes($therecord["deletebutton"])?>" size="20" maxlength="20" /><br />
		</p>
	</fieldset>

	<div id="leftSideDiv">
		<fieldset>
			<legend><label for="displayname">name</label></legend>
			<p><?php $theform->showField("displayname"); ?></p>
		</fieldset>

		<fieldset>
			<legend>SQL table</legend>

			<p><?php $theform->showField("maintable") ?></p>

			<p>
				<label for="querytable">search/display SQL FROM clause</label><br />
				<textarea id="querytable" name="querytable" rows="2" cols="48"><?php echo htmlQuotes($therecord["querytable"])?></textarea><br />
			</p>

			<p class="notes">
				<strong>Note:</strong> For simple tables, entering the same information as the primary table name is sufficient.
				For complex data views that invlolve multiple tables, you will want to enter the SQL's FROM clause.
			</p>
			<p class="notes">
				For example, for invoices, you want to show both the invoice information and the client's name, so you would enter:<br /><br />
				invoices INNER JOIN clients ON invoices.clientid=clients.id
			</p>
		</fieldset>

		<fieldset>
			<legend>Adding Records</legend>
			<p>
				<?php $theform->showField("addfile");?><br />
				<span class="notes">file name, including path from application root, that is used for creating new records.</span>
			</p>

			<p><?php $theform->showField("addroleid");?></p>
		</fieldset>

		<fieldset>
			<legend>Editing Records</legend>

			<p>
				<?php $theform->showField("editfile");?><br />
				<span class="notes">file name, including path from application root, that is used for editing existing records.</span>
			</p>

			<p><?php $theform->showField("editroleid");?></p>
		</fieldset>

		<fieldset>
			<legend>Importing Records</legend>
			<p>
				<?php $theform->showField("importfile") ?><br />
				<span class="notes">file name, including path from application root, that is used for importing records.If none
				is specfied, the general import for the table def will be used.  This may not always result in accurate imports for
				the more complicated table definitions.</span>
			</p>

			<p><?php $theform->showField("importroleid") ?></p>
		</fieldset>

		<fieldset>
			<legend>search screen access</legend>

			<p><?php $theform->showField("searchroleid")?></p>

			<p><?php $theform->showField("advsearchroleid")?></p>

			<p><?php $theform->showField("viewsqlroleid")?></p>
		</fieldset>

		<fieldset>
			<legend>search screen defaults</legend>
			<p>
				<label for="defaultwhereclause">default search</label> <span class="notes">(SQL WHERE clause)</span><br />
				<textarea id="defaultwhereclause" name="defaultwhereclause" cols="32" rows="4"><?php echo htmlQuotes($therecord["defaultwhereclause"])?></textarea>
			</p>

			<p>
				<label for="defaultsortorder">default sort order</label> <span class="notes">(SQL ORDER BY clause)</span><br />
				<textarea id="defaultsortorder" name="defaultsortorder" cols="32" rows="4"><?php echo htmlQuotes($therecord["defaultsortorder"])?></textarea>
			</p>
			<p>
				Does the default search (above) correspond to a quick search (find drop down) item?<br />
				<input type="radio" id="defaultsearchtypeNone" name="defaultsearchtype" class="radiochecks" value="" <?php if($therecord["defaultsearchtype"]=="") echo "checked=\"checked\""?> onchange="toggleDefaultSearch()" />
				<label for="defaultsearchtypeNone">no</label>&nbsp;

				<input type="radio" id="defaultsearchtypeSearch" name="defaultsearchtype" class="radiochecks" value="search" <?php if($therecord["defaultsearchtype"]=="search") echo "checked=\"checked\""?>  onchange="toggleDefaultSearch()" />
				<label for="defaultsearchtypeNone">yes</label>&nbsp;
			</p>
			<div id="defaultQuickSearch" <?php if($therecord["defaultsearchtype"]=="") echo "style=\"display:none;\""?>>
				<p>
					<label for="defaultcriteriafindoptions">critera: selected find option</label> <span class="notes">(quick search)</span><br/>
					<textarea id="defaultcriteriafindoptions" name="defaultcriteriafindoptions" cols="32" rows="2"><?php echo htmlQuotes($therecord["defaultcriteriafindoptions"])?></textarea>

				</p>
				<p>
					<label for="defaultcriteriaselection">criteria: selected search field</label><br />
					<textarea id="defaultcriteriaselection" name="defaultcriteriaselection" cols="32" rows="2" ><?php echo htmlQuotes($therecord["defaultcriteriaselection"])?></textarea>
				</p>
			</div>
		</fieldset>
	</div>

	<?php
		$theform->showCreateModify($phpbms,$therecord);
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>
