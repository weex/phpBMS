<?php
/*
 $Rev: 311 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-10-02 19:51:27 -0600 (Tue, 02 Oct 2007) $
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
require_once("../../include/session.php");

require_once("include/fields.php");
require_once("include/post.php");
require_once("include/post_class.php");

if(!isset($_POST["startdate"]))
   $_POST["startdate"] = dateToString(mktime(0,0,0, date("m"), 1, date("y")));

if(!isset($_POST["enddate"]))
   $_POST["enddate"] = dateToString(mktime(0,0,0));

$poster = new poster($db, stringToDate($_POST["startdate"]), stringToDate($_POST["enddate"]));
$poster->getSections();

if(isset($_POST["cmd"]))
    $statusmessage = $poster->process($_POST);

$pageTitle="Post Records";

	$phpbms->cssIncludes[] = "pages/bms/post.css";
	$phpbms->jsIncludes[] = "modules/bms/javascript/post.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputDatePicker("startdate", dateToString($poster->startdate, "SQL"), "start date");
		$theform->addField($theinput);

		$theinput = new inputDatePicker("enddate", dateToString($poster->enddate, "SQL"), "end date");
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");

?><div class="bodyline" id="mainline">
	<form action="<?php echo htmlentities($_SERVER["PHP_SELF"])?>" method="post" name="record" id="record" onsubmit="return false">
	<input type="hidden" id="cmd" name="cmd" value=""/>

	<h1><span><?php echo $pageTitle ?></span></h1>

	<fieldset>
		<legend>posting date range</legend>

                <p class="dateranges"><?php $theform->showField("startdate")?></p>

                <p class="dateranges"><?php $theform->showField("enddate")?></p>

		<p>
                    <br />
                    <button id="changeDateRange" type="button" class="Buttons">find</button>
                </p>
	</fieldset>

	<fieldset>
		<legend>Post Transactions</legend>
                <?php echo $poster->showSections(); ?>
	</fieldset>

        <p id="bottomButtonsP">
            <button type="button" class="Buttons" id="postRecordsButton">post records</button>
        </p>

	</form>
</div>

<?php include("footer.php"); ?>
