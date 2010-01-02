<?php
/*
 $Rev: 258 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-08 21:59:28 -0600 (Wed, 08 Aug 2007) $
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

	//if you need to ovveride the phpbmsTable class make sure to include the modules file
	include("modules/[modulename]/include/[tablename].php");


	//If the addedit page will be accessd directly from a page other than the
	// basic search results page, you may want to grab and pass the previous URL
	//with the following code

	//===================================================
	if(!isset($_GET["backurl"]))
		$backurl = NULL;
	else{
		$backurl = $_GET["backurl"];
		if(isset($_GET["refid"]))
			$backurl .= "?refid=".$_GET["refid"];
	}
	//===================================================


	//Here you invoke the table class.  If you are going to use the standard phpbmsTable class
	// for updates and the such you would access it like this

	//		$thetable = new phpbmsTable($db,[table definition id]);

	// if you are going to overide the class you would instantiate
	// your overriden class like this

	// 		$thetable = new [tablename]($db,[table definition id]);

	//and if you are setting the backurl, make sure you pass that as well
	// like this:
	// 		$thetable = new [tablename]($db,[table definition id],$backurl);


	// Next we process the form (if submitted) and
	// return the current record as an array ($therecord)
	// or if this is a new record, it returns the defaults
	$therecord = $thetable->processAddEditPage();

	//make sure that we set the status message id the processing returned one
	// (e.g. "Record Updated")
	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle = "[tablename]";

	// Next, we set up to include any
	// additional css or javascript files we will be using
	//  This does nto include any field-type specific js (like datepicker)
	// as they are automatically icluded when you define the special fields you
	// will be using below.
	$phpbms->cssIncludes[] = "pages/[modulename]/[tablename].css";
	$phpbms->jsIncludes[] = "modules/[modulename]/javascript/[tablename].js";

	// DEPRECIATED:
	// if you need to define a body onlload function, do so with the phpbms property
	$phpbms->onload[] = "initializePage()";


	// Next we need to define any special fields that will be used in the form
	// A list of field objects (with documentation)is available in the /include/fields.php
	// file.

	// We need to define them here in the head
	// so that any necessay javascript is loaded appropriately.

		//Form Elements
		//==============================================================

		// Create the form
		$theform = new phpbmsForm();
		//if you need to set specific form vaiables (like enctype, or extra onsubmit
		// you can set those form properties here.

		// for each field we will use, create the field object and add it to
		// the forms list.
		$theinput = new inputDatePicker("orderdate", $therecord["orderdate"], "order date");
		$theform->addField($theinput);

		$theinput = new inputCheckBox("weborder",$therecord["weborder"],NULL, false, false);
		$theform->addField($theinput);

		$theinput = new inputField("accountnumber",$therecord["accountnumber"],  "account number" ,false, "integer", 20, 64);
		$theform->addField($theinput);


		// if you neeed to add additional attributes toa field, it's easy.
		$theinput = new inputBasicList("type",$therecord["type"],array("Quote"=>"Quote","Order"=>"Order","Invoice"=>"Invoice","VOID"=>"VOID"), $displayName = NULL, $displayLabel = true);
		$theinput->setAttribute("onchange","checkType(this)");
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		//If you have setup your table with the phpBMS custom fields structure
		//make sure to prep the custom fields here.
		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		// lastly, use the jsMerge method to create the final Javascript formatting
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");

?><div class="bodyline">
	<!--
		Next we start the form.  This also prints the H1 with title, and top save,cancel buttons
		If you need to have other buttons, or need a specific top, you will need to create your form manually.
	-->
	<?php $theform->startForm($pageTitle)?>

	<fieldset id="fsAttributes">
		<legend>attributes</legend>
		<p>
			<label for="id">id</label><br />
			<input name="id" id="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="readonly" class="uneditable" />
		</p>
		<p><?php $theform->showField("inactive");?></p>
	</fieldset>

	<div id="nameDiv">
		<fieldset >
			<legend>name / percentage</legend>

			<p><?php $theform->showField("name"); ?></p>

			<p><?php $theform->showField("percentage"); ?></p>

		</fieldset>

		<?php
			// if you are using the phpBMS custom fields format, make sure to
			// put this in to display administratively set custom fields.
			// If you are not, leaving this in will not affect your form
			$theform->showCustomFields($db, $thetable->customFieldsQueryResult)
		?>

	</div>
	<?php
		//Last, we show the create/modifiy with the bottom save and cancel buttons
		// and then close the form.
		$theform->showGeneralInfo($phpbms,$therecord);
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>
