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
//var_dump($_POST);
//exit;
	require_once("../../include/session.php");
	require_once("include/fields.php");
	require_once("include/tables.php");
	require_once("include/notes.php");

	if(isset($_GET["backurl"])){
		$backurl=$_GET["backurl"];
		if(isset($_GET["refid"]))
			$backurl.="?refid=".$_GET["refid"];
	} else
		$backurl = NULL;

	$thetable = new notes($db,12,$backurl);
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];


	$attachedtableinfo = $thetable->getAttachedTableDefInfo($therecord["attachedtabledefid"]);

	$pageTitle = "Note/Task/Event";

	$phpbms->cssIncludes[] = "pages/base/notes.css";
	$phpbms->jsIncludes[] = "modules/base/javascript/notes.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();
		$theform->onsubmit = "return submitForm(this)";
		$theform->id = "record";

		$temparray = array("Note"=>"NT","Task"=>"TS","Event"=>"EV","System Message"=>"SM");
		$theinput = new inputBasicList("thetype",$therecord["type"],$temparray,"type");
		$theinput->setAttribute("class","important");
		$theinput->setAttribute("onchange","changeType()");
		$theform->addField($theinput);

		$theinput = new inputField("subject",$therecord["subject"], "title" ,true);
		$theform->addField($theinput);

		$temparray = array("Highest"=>3,"High"=>2,"Medium"=>1,"Normal"=>0,"Low"=>-1,"Lowest"=>-2);
		$theinput = new inputBasicList("importance",$therecord["importance"],$temparray,"importance",false);
		$theform->addField($theinput);

		$theinput = new inputCheckbox("private",$therecord["private"]);
		$theform->addField($theinput);

		$theinput = new inputDatePicker("startdate",$therecord["startdate"], "start date" ,false, 11, 15, false);
		$theinput->setAttribute("onchange","checkEndDate();");
		$theform->addField($theinput);

		$theinput = new inputTimePicker("starttime",$therecord["starttime"], "start time" ,false,11, 15, false);
		$theinput->setAttribute("onchange","checkEndDate();");
		$theform->addField($theinput);

		$theinput = new inputDatePicker("enddate",$therecord["enddate"], "end date" ,false, 11, 15, false);
		$theform->addField($theinput);

		$theinput = new inputTimePicker("endtime",$therecord["endtime"], "end time" ,false,11, 15, false);
		$theform->addField($theinput);

		$theinput = new inputCheckbox("completed",$therecord["completed"],"completed",false,false);
		$theinput->setAttribute("onclick","completedCheck()");
		$theform->addField($theinput);

		$theinput = new inputDatePicker("completeddate",$therecord["completeddate"], "completed date" ,false, 11, 15, false);
		$theinput->setAttribute("readonly","readonly");
		$theform->addField($theinput);

		$theinput = new inputChoiceList($db, "status",$therecord["status"],"notestatus");
		$theform->addField($theinput);

		$theinput = new inputSmartSearch($db, "assignedtoid", "Pick Active User", $therecord["assignedtoid"], "assigned to", false, 18, 255, false);
		$theform->addField($theinput);

		$theinput = new inputDatePicker("assignedtodate",$therecord["assignedtodate"], "follow up date");
		$theform->addField($theinput);

		$theinput = new inputTimePicker("assignedtotime",$therecord["assignedtotime"], "follow up time" ,false,11, 15, false);
		$theform->addField($theinput);

		$theinput = new inputChoiceList($db, "category",$therecord["category"],"notecategories");
		$theform->addField($theinput);


		//repeat fields
		if($therecord["startdate"])
			$repeatBase = stringToDate($therecord["startdate"],"SQL");
		else
			$repeatBase = mktime();

		$theinput = new inputCheckbox("repeating",$therecord["repeating"],"repeat");
		$theinput->setAttribute("onchange","checkRepeat();");
		$theform->addField($theinput);

		$temparray = array("Daily"=>"Daily", "Weekly"=>"Weekly", "Monthly"=>"Monthly", "Yearly"=>"Yearly");
		$theinput = new inputBasiclist("repeattype",$therecord["repeattype"],$temparray,"frequency");
		$theinput->setAttribute("onchange","changeRepeatType();");
		$theform->addField($theinput);

		$theinput = new inputField("repeatevery",$therecord["repeatevery"],"frequency of repeating",false,"integer",2,4,false);
		$theform->addField($theinput);

		$theinput = new inputBasiclist("monthlyontheweek",$therecord["repeatontheweek"],$thetable->weekArray,"on the week of",false);
		$theinput2 = new inputBasiclist("yearlyontheweek",$therecord["repeatontheweek"],$thetable->weekArray,"on the week of",false);
		if(!$therecord["repeatontheday"]) {
			$theinput->setAttribute("disabled","disabled");
			$theinput2->setAttribute("disabled","disabled");

			$weekNumber = ceil(date("d",$repeatBase)/7);
			if($weekNumber > 4) $weekNumber = 5;

			$theinput->value = $weekNumber;
			$theinput2->value = $weekNumber;

		}
		$theform->addField($theinput);
		$theform->addField($theinput2);

		$temparray = array();
		for($i=1; $i<8; $i++)
			$temparray[nl_langinfo(constant("DAY_".$i))] = ($i==1)?(7):($i-1);
		$theinput = new inputBasiclist("monthlyontheday",$therecord["repeatontheday"],$temparray,"on the day",false);
		$theinput2 = new inputBasiclist("yearlyontheday",$therecord["repeatontheday"],$temparray,"on the day",false);
		if(!$therecord["repeatontheday"]){
			 $theinput->setAttribute("disabled","disabled");
			 $theinput2->setAttribute("disabled","disabled");
			 $theinput->value = strftime("%u",$repeatBase);
			 $theinput2->value = strftime("%u",$repeatBase);
		}
		$theform->addField($theinput);
		$theform->addField($theinput2);

		$temparray = array("never"=>"never", "after"=>"after", "on date"=>"on date");
		$thevalue = "never";
		if($therecord["id"]){
			if($therecord["repeattimes"])
				$thevalue = "after";
			elseif($therecord["repeatuntil"])
				$thevalue = "on date";
		}
		$theinput = new inputBasiclist("repeatend",$thevalue,$temparray,"end");
		$theinput->setAttribute("onchange","changeRepeatEnd();");
		$theform->addField($theinput);

		$theinput = new inputField("repeattimes",$therecord["repeattimes"],"repeat until number of times",false,"integer",3,5,false);
		$theform->addField($theinput);

		if(!$therecord["repeatuntil"])
			$therecord["repeatuntil"] = dateToString(mktime(),"SQL");

		$theinput = new inputDatePicker("repeatuntil", $therecord["repeatuntil"], "repeat until date" ,false, 10, 15, false);
		$theform->addField($theinput);
		//end repeat fields

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");

?><div class="bodyline">
	<?php $theform->startForm($pageTitle)?>

	<fieldset id="fsTop">
		<legend>Attributes</legend>
		<p>
			<?php $theform->showField("thetype")?>
			<input type="hidden" id="typeCheck" name="typeCheck" value="<?php echo $therecord["type"]?>" />
		</p>

		<p class="big"><?php $theform->showField("subject") ?></p>
	</fieldset>

	<div id="rightSideDiv">
		<fieldset>
			<legend><label for="importance">importance / privacy</label></legend>
			<p>
				<?php $theform->showField("importance")?>
				<?php $theform->showField("private")?>
			</p>
		</fieldset>

		<fieldset id="thedates">
			<legend>dates</legend>
			<p>
				<label for="startdate" id="starttext">start</label><br />
				<input name="dostart" id="startcheck" type="checkbox" value="1" <?php if($therecord["startdate"]) echo "checked=\"checked\"" ?> onclick="dateChecked('start')" class="radiochecks" />
				&nbsp;<?php $theform->showField("startdate");?>
				&nbsp;<?php $theform->showField("starttime");?>
			</p>
			<p>
				<label for="enddate" id="endtext">end</label><br />
				<input name="doend" id="endcheck" type="checkbox" value="1" <?php if($therecord["enddate"]) echo "checked=\"checked\"" ?> onclick="dateChecked('end')" class="radiochecks" />
				&nbsp;<?php $theform->showField("enddate");?>
				&nbsp;<?php $theform->showField("endtime");?>
			</p>
		</fieldset>

		<div id="thecompleted" class="fauxP">
			<p>
				<input type="hidden" name="completedChange" id="completedChange" value="<?php echo $therecord["completed"]?>" />
				<?php $theform->showField("completed")?><label for="completed" id="completedtext">completed</label>&nbsp;
				<?php $theform->showField("completeddate")?>
			</p>
			<p id="thestatus"><?php $theform->showField("status") ?></p>
		</div>

		<fieldset>
			<legend><label for="ds-assignedtoid">assigned to</label></legend>
			<div class="fauxP">
				<?php $theform->showField("assignedtoid");?>
				<input type="hidden" id="assignedtochange" name="assignedtochange" value="<?php echo $therecord["assignedtoid"] ?>" />
			</div>

			<?php if($therecord["assignedbyid"]!=0){ ?>
			<p>
				<label for="assignedbyid">assigned by</label><br />
				<input id="assignedbydisplay" value="<?php echo $phpbms->getUserName($therecord["assignedbyid"])?>" readonly="readonly" class="uneditable" />
				<input type="hidden" name="assignedbyid" id="assignedbyid" value="<?php echo $therecord["assignedbyid"]?>" />
			</p>
			<?php if($therecord["assignedbyid"] == $_SESSION["userinfo"]["id"]){?>
			<p>
				<button type="button" id="sendemailnotice" class="Buttons" onclick="sendEmailNotice()">send e-mail notice</button>
			</p>
			<?php } }?>

			<p><?php $theform->showField("assignedtodate");?> &nbsp; <?php $theform->showField("assignedtotime")?></p>
		</fieldset>

		<input id="attachedtabledefid" name="attachedtabledefid" type="hidden" value="<?php echo $therecord["attachedtabledefid"]?>" />
		<fieldset id="theassociated">
			<legend>associated with</legend>
				<p>
					<label for="assocarea">area</label><br />
					<input id="assocarea" type="text" readonly="readonly" class="uneditable" value="<?php echo $attachedtableinfo["displayname"];?>" />
				</p>

				<p>
					<label for="attachedid">record id</label><br />
					<input id="attachedid" name="attachedid" type="text" readonly="readonly" class="uneditable" value="<?php echo $therecord["attachedid"]?>" size="6" />&nbsp;
					<input name="link" type="button" class="Buttons" value=" go to record " onclick="document.location='<?php echo APP_PATH?><?php echo $attachedtableinfo["editfile"]."?id=".$therecord["attachedid"]; ?>'" />
				</p>
		</fieldset>

		<fieldset>
			<p>
				<label for="location">location</label><br />
				<input name="location" id="location" type="text" value="<?php echo $therecord["location"]?>"/>
			</p>

			<p><?php $theform->showField("category") ?></p>
		</fieldset>

	</div>

	<div id="leftSideDiv">
		<fieldset>
			<legend><label for="content">memo</label></legend>
			<p id="timeStampP">
				<button id="timeStampButton" type="button" class="graphicButtons buttonTimeStamp" accesskey="t" title="Add time stamp to memo (Access Key - t)">time stamp</button>
			</p>
			<p>
				<textarea name="content" cols="45" rows="23" id="content"><?php echo htmlQuotes($therecord["content"])?></textarea>
				<input id = "username" type="hidden" value="<?php echo formatVariable(trim($_SESSION["userinfo"]["firstname"]." ".$_SESSION["userinfo"]["lastname"]))?>" />
			</p>
		</fieldset>
	</div>

	<div id="repeatDiv">

		<div <?php if($therecord["parentid"]) echo 'style="display:none;"'?>>
			<input type="hidden" id="bypass" name="bypass" value=""/>
			<input type="hidden" id="eachlist" name="eachlist" value=""/>
			<input type="hidden" id="firstrepeat" name="firstrepeat" value="<?php echo $therecord["firstrepeat"]?>"/>
			<input type="hidden" id="lastrepeat" name="lastrepeat" value="<?php echo $therecord["lastrepeat"]?>"/>
			<input type="hidden" id="timesrepeated" name="timesrepeated" value="<?php echo $therecord["timesrepeated"]?>"/>
			<fieldset>
				<legend>repeat</legend>

				<p><?php $theform->showField("repeating")?></p>

				<div id="repeatOptions" <?php if(!$therecord["repeating"]) echo 'style="display:none"'?>>

					<p><?php $theform->showField("repeattype")?></p>

					<p>every <?php $theform->showField("repeatevery")?> <span id="repeatTypeText">day(s)</span></p>

					<div id="DailyDiv"></div>

					<div id="WeeklyDiv">
						<p><?php $thetable->showWeeklyOptions($therecord,$repeatBase)?></p>
					</div>

					<div id="MonthlyDiv">
						<p><input type="radio" id="monthlyEach" name="monthlyWhat" onchange="monthlyChange();" value="1" <?php if(!$therecord["repeatontheday"]) echo 'checked="checked"'?> /><label for="monthlyEach"> each</label></p>

						<p><?php $thetable->showMonthlyOptions($therecord,$repeatBase)?></p>

						<p><input type="radio" id="monthlyOnThe" name="monthlyWhat" onchange="monthlyChange();" value="2" <?php if($therecord["repeatontheday"]) echo 'checked="checked"'?> /><label for="monthlyOnThe"> on the</label></p>
						<p>
							<?php $theform->showField("monthlyontheweek");?>
							<?php $theform->showField("monthlyontheday");?>
						</p>
					</div>

					<div id="YearlyDiv">
						<p><?php $thetable->showYearlyOptions($therecord,$repeatBase)?></p>

						<p><input id="yearlyOnThe" type="checkbox" name="yearlyOnThe" onclick="yearlyOnTheChecked();" value="1" <?php if($therecord["repeattype"]=="Yearly" && $therecord["repeatontheday"]) echo 'checked="checked"'?>/><label for="yearlyOnThe"> on the</label></p>
						<p>
							<?php $theform->showField("yearlyontheweek");?>
							<?php $theform->showField("yearlyontheday");?>
						</p>

					</div>
				</div>
			</fieldset>

			<fieldset id="repeatEnding" <?php if(!$therecord["repeating"]) echo 'style="display:none"'?>>
				<legend>end</legend>
				<p>
					<?php $theform->showField("repeatend")?>
					<span id="repeatAfterSpan" style="display:none">
						<?php $theform->showField("repeattimes")?> <label for="repeattimes">time(s)</label>
					</span>
					<span id="repeatOndateSpan" style="display:none">
						<?php $theform->showField("repeatuntil")?>
					</span>
				</p>
			</fieldset>
		</div>
		<?php if($therecord["parentid"]){?>
		<fieldset>
			<legend>recurrence</legend>
			<p>This record was created from a repeated task/event.</p>
			<p>Click the <strong>Edit Repeating Options</strong> button to edit the options for the repeatable parent record.</p>
			<p class="notes">Any unsaved changes with the current record will be lost.</p>
			<p><input id="goparent" name="goparent" type="button" value="Edit Repeating Options..." onclick="goParent('<?php echo getAddEditFile($db,12) ?>')" class="Buttons" /></p>
		</fieldset>
		<?php }//endif ?>
	</div>

	<?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

	<?php
		$theform->showGeneralInfo($phpbms,$therecord);
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>
