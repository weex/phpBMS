<?php
/*
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
	include("modules/recurringinvoices/include/recurringinvoices.php");

	if(!isset($_GET["id"]))
		$_GET["id"] = 0;
	$_GET["id"] = (int) $_GET["id"];

	if(isset($_POST["referrer"]))
		$_SERVER['HTTP_REFERER'] = $_POST["referrer"];

	$thetable = new recurringinvoice($db,$_GET["id"]);
	$therecord = $thetable->process();

	//set the page title
	$refquery="SELECT
			   invoices.id, if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as name,
			   invoices.type,
			   invoices.invoicedate
			   FROM invoices INNER JOIN clients ON invoices.clientid=clients.id
			   WHERE invoices.id=".$_GET["id"];
	$refquery=$db->query($refquery);
	$refrecord=$db->fetchArray($refquery);

	$invoiceDate = stringToDate($refrecord["invoicedate"],"SQL");

	$pageTitle="Invoice Recurrence: ".$refrecord["id"].": ".$refrecord["name"];
	$phpbms->cssIncludes[] = "pages/recurringinvoices.css";
	$phpbms->jsIncludes[] = "modules/recurringinvoices/javascript/recurringinvoices.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputCheckbox("recurr","","recurr invoice",($invoiceDate == NULL));
		$theform->addField($theinput);

		$temparray = array("Daily"=>"Daily", "Weekly"=>"Weekly", "Monthly"=>"Monthly", "Yearly"=>"Yearly");
		$theinput = new inputBasiclist("type",$therecord["type"],$temparray,"frequency");
		$theinput->setAttribute("onchange","changeType();");
		$theform->addField($theinput);

		$theinput = new inputField("every",$therecord["every"],"frequency of repeating",false,"integer",2,4,false);
		$theform->addField($theinput);

		$theinput = new inputBasiclist("monthlyontheweek",$therecord["ontheweek"],$thetable->weekArray,"on the week of",false);
		$theinput2 = new inputBasiclist("yearlyontheweek",$therecord["ontheweek"],$thetable->weekArray,"on the week of",false);
		if(!$therecord["ontheday"]) {
			$theinput->setAttribute("disabled","disabled");
			$theinput2->setAttribute("disabled","disabled");

			$weekNumber = ceil(date("d",$invoiceDate)/7);
			if($weekNumber > 4) $weekNumber = 5;

			$theinput->value = $weekNumber;
			$theinput2->value = $weekNumber;

		}
		$theform->addField($theinput);
		$theform->addField($theinput2);

		$temparray = array();
		for($i=1; $i<8; $i++)
			$temparray[nl_langinfo(constant("DAY_".$i))] = ($i==1)?(7):($i-1);
		$theinput = new inputBasiclist("monthlyontheday",$therecord["ontheday"],$temparray,"on the day",false);
		$theinput2 = new inputBasiclist("yearlyontheday",$therecord["ontheday"],$temparray,"on the day",false);
		if(!$therecord["ontheday"]){
			 $theinput->setAttribute("disabled","disabled");
			 $theinput2->setAttribute("disabled","disabled");
			 $theinput->value = strftime("%u",$invoiceDate);
			 $theinput2->value = strftime("%u",$invoiceDate);
		}
		$theform->addField($theinput);
		$theform->addField($theinput2);

		$temparray = array("never"=>"never", "after"=>"after", "on date"=>"on date");
		$thevalue = "never";
		if($therecord["id"]){
			if($therecord["times"])
				$thevalue = "after";
			elseif($therecord["until"])
				$thevalue = "on date";
		}
		$theinput = new inputBasiclist("end",$thevalue,$temparray,"end");
		$theinput->setAttribute("onchange","changeEnd();");
		$theform->addField($theinput);

		$theinput = new inputField("times",$therecord["times"],"repeat until number of times",false,"integer",3,5,false);
		$theform->addField($theinput);

		$theinput = new inputDatePicker("until", $therecord["until"], "repeat until date" ,false, 10, 15, false);
		$theform->addField($theinput);

		$theinput = new inputCheckbox("includepaymenttype",$therecord["includepaymenttype"], "include payment type from original invoice");
		$theinput->setAttribute("onchange","switchInclude();");
		$theform->addField($theinput);

		if($therecord["includepaymenttype"])
			$tempdisabled = false;
		else
			$tempdisabled = true;
		$theinput = new inputCheckbox("includepaymentdetails",$therecord["includepaymentdetails"], "include payment details from original invoice",$tempdisabled);
		$theform->addField($theinput);


		$theinput = new inputSmartSearch($db, "assignedtoid", "Pick Active User", $therecord["assignedtoid"], "assigned to", false, 36);
		$theinput->setAttribute("size","30");
		$theform->addField($theinput);

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================

	include("header.php");

	$phpbms->showTabs("invoices entry","tab:d303321e-7ff5-fe4b-29ec-fe3eb0305576",$_GET["id"]);
?><div class="bodyline">
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>"
	method="post" name="record" id="record"
	onsubmit="return false;">
	<div id="topButtons">
		<input type="button" class="Buttons" id="update1" name="update" value="save" onclick="submitForm('update');"/>
		<?php if(strpos($_SERVER['HTTP_REFERER'],"search.php") != false){?>
		<input type="button" class="Buttons" id="cancel1" name="cancel" value="cancel" onclick="submitForm('cancel');"/>
		<?php }?>
	</div>
	<h1 id="h1Title"><span><?php echo $pageTitle ?></span></h1>
	<input type="hidden" id="id" name="id" value="<?php echo $therecord["id"]?>"/>
	<input type="hidden" id="eachlist" name="eachlist" value=""/>
	<input type="hidden" id="command" name="command" />
	<input type="hidden" id="referrer" name="referrer" value="<?php echo str_replace("&","&amp;",$_SERVER['HTTP_REFERER'])?>" />
	<fieldset>
		<legend>Invoice Recurrence</legend>

		<p><?php $theform->showField("recurr")?></p>

		<?php if($invoiceDate == NULL){?>
			<p class="notes">The invoice cannot be repeated until an invoice date has been set.</p>
		<?php }?>
	</fieldset>

	<div id="recurrDetails">
		<div id="rightSideDiv">
			<fieldset>
				<legend>Repeat Statistics</legend>
				<p>
					first recurred<br />
					<input readonly="readonly" class="uneditable" type="text" size="12" value="<?php if($therecord["lastrepeat"]) echo formatFromSQLDate($therecord["firstrepeat"])?>"/>
				</p>
				<p>
					last recurred<br />
					<input readonly="readonly" class="uneditable" type="text" size="12" value="<?php if($therecord["lastrepeat"]) echo formatFromSQLDate($therecord["lastrepeat"])?>"/>
				</p>
				<p>
					number of recurrences to date<br/>
					<input readonly="readonly" class="uneditable" type="text" size="4" value="<?php echo $therecord["timesrepeated"]?>"/>
				</p>
			</fieldset>
		</div>

		<div id="leftSideDiv">
			<fieldset>
				<legend>recurrence options</legend>

				<p>
					invoice date<br/>
					<input type="text" size="12" class="uneditable" readonly="readonly" value="<?php echo dateToString($invoiceDate) ?>" />
				</p>

				<p><?php $theform->showField("type")?></p>

				<p>every <?php $theform->showField("every")?> <span id="typeText">day(s)</span></p>

				<div id="DailyDiv"></div>

				<div id="WeeklyDiv">
					<p><?php $thetable->showWeeklyOptions($therecord,$invoiceDate)?></p>
				</div>

				<div id="MonthlyDiv">
					<p><input type="radio" id="monthlyEach" name="monthlyWhat" onchange="monthlyChange();" value="1" <?php if(!$therecord["ontheday"]) echo 'checked="checked"'?> /><label for="monthlyEach"> each</label></p>

					<p><?php $thetable->showMonthlyOptions($therecord,$invoiceDate)?></p>

					<p><input type="radio" id="monthlyOnThe" name="monthlyWhat" onchange="monthlyChange();" value="2" <?php if($therecord["ontheday"]) echo 'checked="checked"'?> /><label for="monthlyOnThe"> on the</label></p>
					<p>
						<?php $theform->showField("monthlyontheweek");?>
						<?php $theform->showField("monthlyontheday");?>
					</p>
				</div>

				<div id="YearlyDiv">
					<p><?php $thetable->showYearlyOptions($therecord,$invoiceDate)?></p>

					<p><input id="yearlyOnThe" type="checkbox" name="yearlyOnThe" onclick="yearlyOnTheChecked();" value="1" <?php if($therecord["type"]=="Yearly" && $therecord["ontheday"]) echo 'checked="checked"'?>/><label for="yearlyOnThe"> on the</label></p>
					<p>
						<?php $theform->showField("yearlyontheweek");?>
						<?php $theform->showField("yearlyontheday");?>
					</p>

				</div>
			</fieldset>

			<fieldset>
				<legend>end</legend>
				<p>
					<?php $theform->showField("end")?>
					<span id="afterSpan" style="display:none">
						<?php $theform->showField("times")?> <label for="times">time(s)</label>
					</span>
					<span id="ondateSpan" style="display:none">
						<?php $theform->showField("until")?>
					</span>
				</p>
			</fieldset>

			<fieldset>
				<legend>New Order Options</legend>

				<p><?php $theform->showfield("includepaymenttype");?></p>

				<p><?php $theform->showfield("includepaymentdetails");?></p>

				<p><?php $thetable->showStatusDropDown($therecord["statusid"]);?></p>

				<div class="fauxP"><?php $theform->showField("assignedtoid");?></div>


				<p><?php $thetable->showRolesDropDown($therecord["notificationroleid"]);?></p>

			</fieldset>

			<p class="notes">
				Note: Recurring invoices utilizes the scheduler function of phpBMS, which relies
				on an external scheduler program (like cron).  Make sure these functions are enabled or
				the invoices will not repeat.
			</p>

		</div>
	</div>


	<div align="right">
		<input type="button" class="Buttons" id="update2" name="update" value="save" onclick="submitForm('update');"/>
		<?php if(strpos($_SERVER['HTTP_REFERER'],"search.php") != false){?>
		<input type="button" class="Buttons" id="cancel2" name="cancel" value="cancel" onclick="submitForm('cancel');"/>
		<?php }?>
	</div>
</form>
</div>
<?php include("footer.php")?>
