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
	include("../../include/common_functions.php");
	include("../../include/fields.php");
	include("include/scheduler_addedit_include.php");
	
	$pageTitle="Scheduler";
	
	$currentpath=getcwd();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/scheduler.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/datepicker.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/timepicker.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onsubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onclick="return false;" /></div>
<div class="bodyline">
	<div id="topButtons">
		<?php showSaveCancel(1); ?>
	</div>
	<h1 id="h1Title"><span><?php echo $pageTitle ?></span></h1>
	
	<fieldset id="fsAttributes">
		<legend>attributes</legend>
		<p>
			<label for="id">id</label><br />
			<input id="id" name="id" type="text" value="<?php echo htmlQuotes($therecord["id"]); ?>" size="10" maxlength="10" readonly="true" class="uneditable" />
		</p>
		
		<p><?php fieldCheckbox("inactive",$therecord["inactive"])?><label for="inactive">inactive</label></p>

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
			<p>
				<label for="name">name</label><br />
				<?php fieldText("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"32","maxlength"=>"64","class"=>"important")); ?>			
			</p>		
			<p>
				<label for="job">script</label> <span class="notes">(path relative to <?php echo $currentpath?>)</span><br />
				<?php fieldText("job",$therecord["job"],1,"Script cannot be blank.","",Array("size"=>"32","maxlength"=>"64",)); ?>			
			</p>
			<p>
				<label for="description">description</label><br />
				<textarea id="description" name="description"><?php echo htmlQuotes($therecord["description"]) ?></textarea>
			</p>
		</fieldset>
		
		<fieldset>
			<legend>Interval</legend>
				<p class="crontabnotation">
					<label for="mins">min</label><br />
					<input name="min" id="min" maxlength="25" size="3" value="<?php echo $therecord["min"]?>" type="text" />
				</p>
				<p class="crontabnotation">
					<label for="hrs">hrs</label><br />
					<input name="hrs" id="hrs" maxlength="25" size="3" value="<?php echo $therecord["hrs"]?>" type="text" />
				</p>
				<p class="crontabnotation">
					<label for="dayofmonth">date</label><br />
					<input name="date" id="date"  maxlength="25" size="3" value="<?php echo $therecord["date"]?>" type="text" />
				</p>
				<p class="crontabnotation">
					<label for="months">mo</label><br />
					<input name="mo" id="mo" maxlength="25" size="3" value="<?php echo $therecord["mo"]?>" type="text" />
				</p>
				<p class="crontabnotation">
					<label for="dayofweek">day</label><br />
					<input name="day" id="day" maxlength="25" size="3" value="<?php echo $therecord["day"]?>" type="text" />
				</p>
			<p class="notes" id="standarNotationP">(Uses standard crontab notation.)</p>
			
		</fieldset>
		<fieldset>
			<legend>Dates</legend>
			<p>
				<label for="startdate">start</label><br />
				<?php fieldDatePicker("startdate",$therecord["startdate"],1,"Must enter a valid start date.",Array("size"=>"11","maxlength"=>"15"));?>	
				&nbsp;<?php fieldTimePicker("starttime",$therecord["starttime"],0,"Must enter a valid start time.",Array("size"=>"11","maxlength"=>"15"));?>
			</p>
			<p>
				<label for="enddate">end</label><br />
				<?php fieldDatePicker("enddate",$therecord["enddate"],0,"Must enter a valid end date.",Array("size"=>"11","maxlength"=>"15"));?>			
				&nbsp;<?php fieldTimePicker("endtime",$therecord["endtime"],0,"Must enter a valid end time.",Array("size"=>"11","maxlength"=>"15"));?>			
			</p>
		</fieldset>
	</div>

	<?php include("../../include/createmodifiedby.php"); ?>
</div>
<?php include("../../footer.php"); ?>
</form>
</body>
</html>