<?php
/*
 $Rev: 254 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-07 18:38:38 -0600 (Tue, 07 Aug 2007) $
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

class arAging{

	var $agingDate;
	var $printClientStatements = false;
	var $printSummary = false;
	var $serviceCharges = 0;

	function arAging($db, $userid = NULL){

		$this->db = $db;

		if($userid)
			$this->userid = $userid;
		else
			$this->userid = $_SESSION["userinfo"]["id"];

	}//end method

	function run($agingDate = NULL){

		if($agingDate)
			$this->agingDate = stringToDate($agingDate);
		else
			$this->agingDate = mktime(0,0,0);

		$querystatement = "
			SELECT
				*
			FROM
				aritems
			WHERE
				`status` = 'open'
				AND posted = 1
				AND `type` = 'invoice'";

		$queryresult = $this->db->query($querystatement);

		while($therecord = $this->db->fetchArray($queryresult)){

			$itemdate = stringToDate($therecord["itemdate"], "SQL");
			$termDate[1] = strtotime(TERM1_DAYS." days", $itemdate);
			$termDate[2] = strtotime(TERM2_DAYS." days", $itemdate);
			$termDate[3] = strtotime(TERM3_DAYS." days", $itemdate);

			for($i = 1; $i < 4; $i++){

				if($this->agingDate > $termDate[$i] && $therecord["aged".$i] == 0){
					if( $this->_createServiceCharge($therecord, constant("TERM".$i."_PERCENTAGE")) )
						$this->serviceCharges++;

					$this->_markCharged($therecord["id"], "aged".$i);

				}//endif

			}//endfor

		}//endwhile

	}//end method


	function _markCharged($aritemid, $termField){

		$updatestatement = "
			UPDATE
				aritems
			SET
				".$termField." = 1
			WHERE
				id = ".((int) $aritemid);

		$this->db->query($updatestatement);

	}//end method


	function _createServiceCharge($arrecord, $percentage){

		if($arrecord["amount"] - $arrecord["paid"] <= 0)
			return false;

		$newAmount = round( ($arrecord["amount"] - $arrecord["paid"]) * ($percentage/100), CURRENCY_ACCURACY);

		if($newAmount <=0)
			return false;

		if(!class_exists("phpbmsTable"))
			include("include/tables.php");

		$aritems = new phpbmsTable($this->db, "tbld:c595dbe7-6c77-1e02-5e81-c2e215736e9c");

		$newarrecord = array();
		$newarrecord["uuid"] = uuid($aritems->prefix.":");
		$newarrecord["type"] = "service charge";
		$newarrecord["status"] = "open";
		$newarrecord["posted"] = 1;
		$newarrecord["amount"] = $newAmount;
		$newarrecord["itemdate"] = dateToString($this->agingDate);
		$newarrecord["clientid"] = $arrecord["clientid"];
		$newarrecord["relatedid"] = $arrecord["relatedid"];

		$aritems->insertRecord($newarrecord, $this->userid);

		return true;

	}//end method


	function showResults(){

		include_once("include/fields.php");
		global $phpbms;
		$db = &$this->db;

		$phpbms->cssIncludes[] = "pages/aging.css";
		$phpbms->jsIncludes[] = "modules/bms/javascript/aritem_aging.js";
		$phpbms->showMenu = false;

		$formSubmit = str_replace("&","&amp;",$_SERVER['REQUEST_URI']);

		include("header.php");
		?>
		<div class="bodyline" id="dialog">
			<h1><span>AR Aging Results</span></h1>
			<form action="<?php echo $formSubmit ?>" id="record" method="post">

				<fieldset>
					<legend>details</legend>
					<p>
						<label for="agingdate">aging date</label><br />
						<input id="agingdate" class="uneditable" readonly="readonly" value="<?php echo dateToString($this->agingDate)?>"/>
					</p>

					<p>
						<label for="serviceCharges">service charge AR items created</label><br />
						<input id="serviceCharges" class="uneditable" readonly="readonly" value="<?php echo $this->serviceCharges?>"/>
					</p>
				</fieldset>

				<p class="notes">Any reports checked to be run should open in separate windows.</p>

				<p align="right">
					<input type="hidden" id="printClientStatements" value="<?php echo ((int) $this->printClientStatements)?>"/>
					<input type="hidden" id="printSummary" value="<?php echo ((int) $this->printSummary)?>"/>
					<input type="hidden" id="command" name="command" />
					<button type="button" class="Buttons" id="cancelButton">Done</button>
				</p>
			</form>
		</div>
		<?php
		include("footer.php");

	}//end method


	function showDialog(){

		include_once("include/fields.php");
		global $phpbms;
		$db = &$this->db;

		$phpbms->cssIncludes[] = "pages/aging.css";
		$phpbms->jsIncludes[] = "modules/bms/javascript/aritem_aging.js";
		$phpbms->showMenu = false;

		$formSubmit = str_replace("&","&amp;",$_SERVER['REQUEST_URI']);

		$theform = new phpbmsForm();

		$theinput = new inputDatePicker("agingdate", dateToString(mktime(0,0,0), "SQL"), "aging date", true);
		$theform->addField($theinput);

		$theform->jsMerge();

		include("header.php");

		?>
		<div class="bodyline" id="dialog">
			<h1><span>AR Aging</span></h1>
			<form action="<?php echo $formSubmit ?>" id="record" method="post">

				<fieldset>
					<legend>options</legend>
					<p><?php $theform->showField("agingdate")?></p>
				</fieldset>

				<fieldset>
					<legend>Current Items Needing Aging</legend>
					<?php $this->_showNeedingAgingTotals()?>

				</fieldset>

				<fieldset>
					<legend>Report Options</legend>

					<p>
						<input type="checkbox" value="1" id="printStatements" name="printStatements" class="radiochecks"/>
						<label for="printStatements">Print client statements for clients with open items.</label>
					</p>

					<p>
						<input type="checkbox" value="1" id="printSummary" name="printSummary" class="radiochecks"/>
						<label for="printSummary">Print a statement summary.</label>
					</p>

				</fieldset>
				<p class="notes">
					Aging should be run in a consistent timeframe.  Although
					running again will not result in duplicate service charges
					for an invoice in the same aging period, not running
					the aging on the same specified date may result in client
					statements showing the service charges too early or too late.
				</p>

				<p align="right">
					<input type="hidden" name="command" id="command" />
					<button type="button" class="Buttons" id="runButton">run aging</button>
					<button type="button" class="Buttons" id="cancelButton">cancel</button>
				</p>
			</form>
		</div>
		<?php

		include("footer.php");

	}//end method


	function _showNeedingAgingTotals(){

		$totals = array(
			"term1" => 0,
			"term2" => 0,
			"term3" => 0
		);

		$querystatement = "
			SELECT
				itemdate,
				aged1,
				aged2,
				aged3
			FROM
				aritems
			WHERE
				`status` = 'open'
				AND posted = 1
				AND `type` = 'invoice'";

		$queryresult = $this->db->query($querystatement);

		while($therecord = $this->db->fetchArray($queryresult)){

			$itemdate = stringToDate($therecord["itemdate"], "SQL");
			$term1Date = strtotime(TERM1_DAYS." days", $itemdate);
			$term2Date = strtotime(TERM2_DAYS." days", $itemdate);
			$term3Date = strtotime(TERM3_DAYS." days", $itemdate);
			$today = mktime(0,0,0);

			if($today > $term1Date && $therecord["aged1"] == 0)
				$totals["term1"]++;
			elseif($today > $term2Date && $therecord["aged2"] == 0)
				$totals["term2"]++;
			elseif($today > $term2Date && $therecord["aged2"] == 0)
				$totals["term3"]++;

		}//endwhile
		?>
			<p>
				<label for="term1"><?php echo (TERM1_DAYS+1)." - ".TERM2_DAYS." days" ?></label><br />
				<input id="term1" class="uneditable" readonly="readonly" value="<?php echo $totals["term1"] ?>"/>
			</p>

			<p>
				<label for="term2"><?php echo (TERM2_DAYS+1)." - ".TERM3_DAYS." days" ?></label><br />
				<input id="term2" class="uneditable" readonly="readonly" value="<?php echo $totals["term2"] ?>"/>
			</p>

			<p>
				<label for="term3"><?php echo (TERM1_DAYS+1)."+ days" ?></label><br />
				<input id="term3" class="uneditable" readonly="readonly" value="<?php echo $totals["term3"] ?>"/>
			</p>

		<?php

	}//end method

}//end class




//Processor
//===========================================================================
if(!isset($bypass)){

	include_once("../../include/session.php");

	$aging = new arAging($db);

	if(isset($_POST["command"])){

		switch($_POST["command"]){

			case "run":
				$aging->run($_POST["agingdate"]);

				if(isset($_POST["printStatements"]))
					$aging->printClientStatements = true;

				if(isset($_POST["printSummary"]))
					$aging->printSummary = true;

				$aging->showResults();

				break;

			case "cancel":
				goURL(APP_PATH."search.php?id=tbld%3Ac595dbe7-6c77-1e02-5e81-c2e215736e9c");

		}//endswitch

	} else
		$aging->showDialog();

}//end if
?>