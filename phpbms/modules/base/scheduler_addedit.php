<?php
/*
 $Rev: 197 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-03-02 10:48:56 -0700 (Fri, 02 Mar 2007) $
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
	include("include/scheduler.php");

	$thetable = new schedulers($db,201);
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle="Scheduler";

	$currentpath=getcwd();

	$phpbms->cssIncludes[] = "pages/scheduler.css";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputCheckbox("inactive",$therecord["inactive"]);
		$theform->addField($theinput);

		$theinput = new inputField("name",$therecord["name"],NULL,true,NULL,32,64);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputField("job",$therecord["job"],"script",true,NULL,32,128,false);
		$theform->addField($theinput);

		$theinput = new inputDatePicker("startdate",$therecord["startdate"], "start date" ,true, 11, 15, false);
		$theform->addField($theinput);

		$theinput = new inputTimePicker("starttime",$therecord["starttime"], "start time" ,true,11, 15, false);
		//$theinput->setAttribute("onchange","checkEndDate();");
		$theform->addField($theinput);

		$theinput = new inputDatePicker("enddate",$therecord["enddate"], "end date" ,false, 11, 15, false);
		$theform->addField($theinput);

		$theinput = new inputTimePicker("endtime",$therecord["endtime"], "end time" ,false,11, 15, false);
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
			<label for="id">id</label><br />
			<input id="id" name="id" type="text" value="<?php echo htmlQuotes($therecord["id"]); ?>" size="10" maxlength="10" readonly="readonly" class="uneditable" />
		</p>

		<p><?php $theform->showField("inactive");?></p>

		<p>
			script last run<br />
			<strong><?php if($therecord["lastrun"]) echo $therecord["lastrun"]; else echo "never"?></strong>
		</p>
	</fieldset>

	<div id="leftSideDiv">
		<fieldset>
			<legend>Enabling the Scheduler</legend>
			<p>In order to enable the phpBMS scheduler, make sure you add the following line to your crontab:</p>
			<p class="mono important">*&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;
			cd <?php echo $currentpath;?>; php -f cron.php > /dev/null 2>&amp;1 </p>
		</fieldset>
		<fieldset>
			<legend>Scheduled Job</legend>

			<p><?php $theform->showField("name");?></p>

			<p>
				<label for="job">script</label> <span class="notes">(path relative to <?php echo $currentpath?>)</span><br />
				<?php $theform->showField("job");?>
			</p>
			<p>
				<label for="description">description</label><br />
				<textarea id="description" name="description" rows="4" cols="48"><?php echo htmlQuotes($therecord["description"]) ?></textarea>
			</p>
		</fieldset>

		<fieldset>
			<legend>Interval</legend>
				<p class="crontabnotation">
					<label for="min">min</label><br />
					<input name="min" id="min" maxlength="25" size="3" value="<?php echo $therecord["min"]?>" type="text" />
				</p>
				<p class="crontabnotation">
					<label for="hrs">hrs</label><br />
					<input name="hrs" id="hrs" maxlength="25" size="3" value="<?php echo $therecord["hrs"]?>" type="text" />
				</p>
				<p class="crontabnotation">
					<label for="date">date</label><br />
					<input name="date" id="date"  maxlength="25" size="3" value="<?php echo $therecord["date"]?>" type="text" />
				</p>
				<p class="crontabnotation">
					<label for="mo">mo</label><br />
					<input name="mo" id="mo" maxlength="25" size="3" value="<?php echo $therecord["mo"]?>" type="text" />
				</p>
				<p class="crontabnotation">
					<label for="day">day</label><br />
					<input name="day" id="day" maxlength="25" size="3" value="<?php echo $therecord["day"]?>" type="text" />
				</p>
			<p class="notes" id="standarNotationP">(Uses standard crontab notation.)</p>

		</fieldset>
		<fieldset>
			<legend>Dates</legend>
			<p>
				<label for="startdate">start</label><br />
				<?php $theform->showField("startdate");?> &nbsp; <?php $theform->showField("starttime");?>
			</p>
			<p>
				<label for="enddate">end</label><br />
				<?php $theform->showField("enddate");?> &nbsp; <?php $theform->showField("endtime");?>
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
