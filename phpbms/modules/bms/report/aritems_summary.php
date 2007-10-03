<?php
/*
 $Rev: 290 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-27 18:15:00 -0600 (Mon, 27 Aug 2007) $
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

if(!class_exists("phpbmsReport"))
	include("../../../report/report_class.php");

class aritemsSummary extends phpbmsReport{

	var $currentTotal = 0;
	var $term1Total = 0;
	var $term2Total = 0;
	var $term3Total = 0;
	var $showPayments = "new";
	var $showClosed = false;
	var $clientTotals = array();
	var $grandTotals = array(
		"docamount" => 0, 
		"current" => 0, 
		"term1" => 0, 
		"term2" => 0, 
		"term3" => 0
	);
	

	function aritemsSummary($db, $statementDate = NULL, $showPayments = "new", $showClosed = false){
	
		parent::phpbmsReport($db);

		if($statementDate)
			$this->statementDate = $statementDate;
		else
			$this->statementDate = mktime(0,0,0);
			
		$this->showPayments = $showPayments;			
		$this->showClosed = $showClosed;
			
	}//end method
	
	
	function generate($whereclause = ""){
	

		if($whereclause)
			$this->whereclause = $whereclause;
			
		if(!$this->whereclause)
			$this->whereclause = "
				aritems.status = 'open'
				AND aritems.posted = 1";


		//first we need to get the list of clients.				
		$querystatement = "
			SELECT DISTINCT 
				aritems.clientid,
				clients.firstname,
				clients.lastname,
				clients.company,
				clients.address1,
				clients.address2,
				clients.city,
				clients.postalcode,
				clients.state,
				clients.country,
				clients.creditlimit,
				clients.homephone,
				clients.workphone,
				clients.email,
				users.firstname AS salesfirstname,
				users.lastname AS saleslastname
			FROM
				(aritems INNER JOIN clients ON aritems.clientid = clients.id)
				LEFT JOIN users ON clients.salesmanagerid = users.id";
				
		$this->sortorder = 'if(clients.lastname!="",concat(clients.lastname,", ",clients.firstname,if(clients.company!="",concat(" (",clients.company,")"),"")),clients.company)';
		
		$querystatement = $this->assembleSQL($querystatement);

		$clientresult = $this->db->query($querystatement);

		ob_start();

		?>
		<div id="container">
		<h1>Accounts Receivable Summary</h1>
		<p>
			Statement Date: <?php echo dateToString($this->statementDate)?>
		</p>
		<table border="0" cellpadding="0" cellspacing="0" id="results">
			<thead>
				<tr>
					<th nowrap="nowrap" align="left">doc date</th>
					<th nowrap="nowrap" align="left">type</th>
					<th nowrap="nowrap" align="left">doc ref</th>
					<th nowrap="nowrap" align="right" class="currency">doc amount</th>
					<th nowrap="nowrap" align="right" class="currency">current</th>
					<th nowrap="nowrap" align="right" class="currency"><?php echo TERM1_DAYS?></th>
					<th nowrap="nowrap" align="right" class="currency"><?php echo TERM2_DAYS?></th>
					<th nowrap="nowrap" align="right" class="currency"><?php echo TERM3_DAYS?></th>
					<th nowrap="nowrap" align="right" class="currency" id="lastHeader">due</th>
				</tr>
			</thead>
			<tbody>
		<?php		
				
		while($clientrecord = $this->db->fetchArray($clientresult)){
		
			$this->clientrecord = $clientrecord;

			//Client info
			$displayName = "";
			if($this->clientrecord["company"]){
			
				$displayName .= $this->clientrecord["company"];
				
				if($this->clientrecord["lastname"])
					$displayName .= " (".$this->clientrecord["firstname"]." ".$this->clientrecord["lastname"].")";
					
			} else
				$displayName .= $this->clientrecord["firstname"]." ".$this->clientrecord["lastname"];						
			
			$phoneemail = "";
			if($this->clientrecord["workphone"] || $this->clientrecord["homephone"]){
	
				$phoneemail = $this->clientrecord["homephone"]." (H)";
	
				if($this->clientrecord["workphone"])
					$phoneemail = $this->clientrecord["workphone"]." (W)";
					
				$phoneemail .= "<br />";

			}//end if

			if($this->clientrecord["email"])
				$phoneemail .= $this->clientrecord["email"];

			?>
			
				<tr class="groupHeaders">
					<td colspan="9">
						<p class="name"><?php echo $displayName?></p>
				
						<?php if($phoneemail) {?>

							<p class="details"><?php echo $phoneemail?></p>

						<?php }?>
					</td>
				</tr>
				
			<?php
					
			$this->_addItems();
	
			?>
			
				<tr class="groupTotals">
					<td colspan="4" align="right"><?php echo formatVariable($this->clientTotals["docamount"], "currency")?></td>
					<td align="right"><?php echo formatVariable($this->clientTotals["current"], "currency")?></td>
					<td align="right"><?php echo formatVariable($this->clientTotals["term1"], "currency")?></td>
					<td align="right"><?php echo formatVariable($this->clientTotals["term2"], "currency")?></td>
					<td align="right"><?php echo formatVariable($this->clientTotals["term3"], "currency")?></td>
					<td align="right"><?php echo formatVariable($this->clientTotals["current"] + $this->clientTotals["term1"] + $this->clientTotals["term2"] + $this->clientTotals["term3"], "currency")?></td>
				</tr>
			
			<?php
			
			foreach($this->clientTotals as $key => $value)
				$this->grandTotals[$key] += $value;
	
		}//end while

			?>
			
				<tr id="grandTotals">
					<td colspan="4" align="right"><?php echo formatVariable($this->grandTotals["docamount"], "currency")?></td>
					<td align="right"><?php echo formatVariable($this->grandTotals["current"], "currency")?></td>
					<td align="right"><?php echo formatVariable($this->grandTotals["term1"], "currency")?></td>
					<td align="right"><?php echo formatVariable($this->grandTotals["term2"], "currency")?></td>
					<td align="right"><?php echo formatVariable($this->grandTotals["term3"], "currency")?></td>
					<td align="right"><?php echo formatVariable($this->grandTotals["current"] + $this->clientTotals["term1"] + $this->clientTotals["term2"] + $this->clientTotals["term3"], "currency")?></td>
				</tr>
			
			<?php
		
		
		?></tbody>
		</table>
		</div>
		<?php

		$this->contents = ob_get_contents();
		ob_end_clean();
		
	}//end method


	function _addItems(){
	
		$itemsArray = $this->_getItems();	
	
		foreach($itemsArray as $item){
			?>
			<tr>
				<td><?php echo $item["itemdate"]?></td>
				<td nowrap="nowrap"><?php echo $item["type"]?></td>
				<td><?php echo $item["relatedid"]?></td>
				<td align="right"><?php echo $item["amount"]?></td>
				<td align="right"><?php echo $item["current"]?></td>
				<td align="right"><?php echo $item["term1"]?></td>
				<td align="right"><?php echo $item["term2"]?></td>
				<td align="right"><?php echo $item["term3"]?></td>
				<td align="right"><?php echo $item["due"]?></td>
			</tr><?php
				
		}//end foreach
		
	}//end method


	function _getItems(){
		
		$querystatement = "
			SELECT
				id,
				`type`,
				relatedid,
				itemdate,
				amount,
				amount - paid AS due
			FROM
				aritems
			WHERE
				posted =1
				AND clientid = ".((int) $this->clientrecord["clientid"]);

		if(!$this->showClosed)
			$querystatement .= "
				AND `status` = 'open'";
			
		$querystatement .= "
				AND itemdate <= '".dateToString($this->statementDate,"SQL")."'
			ORDER BY
				itemdate,
				id";

		$queryresult = $this->db->query($querystatement);
		
		$returnArray = array();
		
		$this->clientTotals = array(
			"docamount" => 0, 
			"current" => 0, 
			"term1" => 0, 
			"term2" => 0, 
			"term3" => 0
		);
				
		while($therecord = $this->db->fetchArray($queryresult)){
		
			$itemdate = stringToDate($therecord["itemdate"],"SQL");
			
			$therecord["current"] = "&nbsp;";
			$therecord["term1"] = "&nbsp;";
			$therecord["term2"] = "&nbsp;";
			$therecord["term3"] = "&nbsp;";

			if($therecord["type"] != "invoice"){
			
				$this->clientTotals["current"] += $therecord["due"];
				$therecord["current"] = formatVariable($therecord["due"], "currency");
			
			} else {
			
				$daysover = floor(($this->statementDate - $itemdate)/86400);
				
				if($daysover > TERM3_DAYS){
				
					$this->clientTotals["term3"] += $therecord["due"];
					$therecord["term3"] = formatVariable($therecord["due"], "currency");
					
				} elseif($daysover > TERM2_DAYS){

					$this->clientTotals["term2"] += $therecord["due"];
					$therecord["term2"] = formatVariable($therecord["due"], "currency");

				}
				elseif($daysover > TERM1_DAYS ){

					$this->clientTotals["term1"] += $therecord["due"];
					$therecord["term1"] = formatVariable($therecord["due"], "currency");

				} else {

					$this->clientTotals["current"] += $therecord["due"];
					$therecord["current"] = formatVariable($therecord["due"], "currency");
					
				}
								
			}//endif
			
			$therecord["itemdate"] = formatFromSQLDate($therecord["itemdate"]);
			$this->clientTotals["docamount"] += $therecord["amount"];
			$therecord["amount"] = formatVariable($therecord["amount"], "currency");
			$therecord["due"] = formatVariable($therecord["due"], "currency");
						
			$returnArray[$therecord["itemdate"]."-".$therecord["id"]] = $therecord;
							
		}//endwhile

		if($this->showPayments != "none"){
		
			//add in receipts in laste term1_days
			
			$lastDays = dateToString( strtotime("-".TERM1_DAYS." days", $this->statementDate), "SQL");
			
			$querystatement = "SELECT
				id,
				receiptdate,
				amount
			FROM
				receipts
			WHERE
				clientid = ".((int) $this->clientrecord["clientid"]);

			if($this->showPayments == "new")
				$querystatement .= "
				AND receiptdate <= '".dateToString($this->statementDate, "SQL")."'
				AND receiptdate >= '".$lastDays."'";

			$querystatement .= "
				AND posted = 1";

			$receiptresult = $this->db->query($querystatement);
			
			while($receiptrecord = $this->db->fetchArray($receiptresult)){
			
				$rcptRecord["itemdate"] = formatFromSQLDate($receiptrecord["receiptdate"]);
				$rcptRecord["type"] = "payment";
				$rcptRecord["relatedid"] = "&nbsp;";
				
				$rcptRecord["amount"] = "(".formatVariable($receiptrecord["amount"], "currency").")";
				$rcptRecord["current"] = "&nbsp;";
				$rcptRecord["term1"] = "&nbsp;";
				$rcptRecord["term2"] = "&nbsp;";
				$rcptRecord["term3"] = "&nbsp;";
				$rcptRecord["due"] = "&nbsp;";
	
				$returnArray[$receiptrecord["receiptdate"]."-D".$receiptrecord["id"]] = $rcptRecord;

			}//endwhile
		
		}//end if
		
		ksort($returnArray);

		return $returnArray;

	}//end method
	
	
	function output($to = "screen"){

		global $phpbms;
		$db = &$this->db;
	
		$phpbms->cssIncludes[] = "reports.css";
		$phpbms->showMenu = false;
		$phpbms->showFooter = false;
		
		include("header.php");

		echo $this->contents;
		
		include("footer.php");

	}//end method


	function showOptions(){
	
		include("include/fields.php");
		global $phpbms;
		$db = &$this->db;
	
		$phpbms->cssIncludes[] = "pages/aritems_clientstatement.css";
		$phpbms->jsIncludes[] = "modules/bms/javascript/aritem_clientstatement.js";
		$phpbms->showMenu = false;
				
		$formSubmit = str_replace("&","&amp;",$_SERVER['REQUEST_URI']);		
		
		$theform = new phpbmsForm();
		
		$theinput = new inputDatePicker("statementdate", dateToString(mktime(0,0,0), "SQL"), "statement date", true);
		$theform->addField($theinput);
		
		$theinput = new inputCheckbox("showpayments",  true , "show new payments");
		$theform->addField($theinput);

		$theinput = new inputCheckbox("showclosed", false , "show closed items (history)");
		$theform->addField($theinput);

		$theform->jsMerge();

		include("header.php");
		
		?>
		
		<div class="bodyline" id="dialog">
			<h1><span>AR Summary Report</span></h1>
			<form action="<?php echo $formSubmit ?>" id="record" method="post">
			<input type="hidden" id="command" name="command" />
			
				<fieldset>
					<legend>options</legend>
					
					<p><?php $theform->showField("statementdate");?></p>

					<p><?php $theform->showField("showclosed");?></p>
					
					<p><?php $theform->showField("showpayments");?></p>
					
				</fieldset>
				
				<fieldset>
					<legend>payments</legend>
					
					<p>
						<input type="radio" name="payments" id="paymentsNew" value="new" class="radiochecks" checked="checked"/>
						<label for="paymentsNew">show new payments</label>
					</p>

					<p>
						<input type="radio" name="payments" id="paymentsAll" value="all" class="radiochecks"/>
						<label for="paymentsAll">show all payments</label>
					</p>

					<p>
						<input type="radio" name="payments" id="paymentsNone" value="none" class="radiochecks"/>
						<label for="paymentsNone">show no payments</label>
					</p>

				</fieldset>
			
				<fieldset>
					<legend>Records</legend>
					
					<p>
						<input type="radio" name="selrecords" id="allOpen" value="allOpen" class="radiochecks" checked="checked"/>
						<label for="allOpen">all clients with open items</label>
					</p>
					
					<p>
						<input type="radio" name="selrecords" id="allClients" value="allClients" class="radiochecks"/>
						<label for="allClients">all clients with any credit history</label>
					</p>

					<p>
						<input type="radio" name="selrecords" id="fromPrint" value="fromPrint" class="radiochecks"/>
						<label for="fromPrint">clients based on records from print screen</label>
					</p>
					
				</fieldset>

				<p align="right">
					<button type="button" class="Buttons" id="printButton">print</button>
					<button type="button" class="Buttons" id="cancelButton">cancel</button>
				</p>
			</form>
		</div>
		
		<?php
		include("footer.php");		
	
	}//end method


	function processDialog($variables){

		if(isset($variables["statementdate"]))
			$this->statementDate = stringToDate($variables["statementdate"]);

		$this->showClosed = isset($variables["showclosed"]);
		
		if(isset($variables["showpayments"]))
			$this->showPayments = $variables["showpayments"];
			
		if(!isset($variables["selrecords"]))
			$variables["selrecords"] = "allOpen";
			
		switch($variables["selrecords"]){
		
			case "allOpen":
				$this->generate();
				break;
				
			case "allClients":
				$this->generate("clients.hascredit = 1");	
				break;
			
			case "fromPrint":
				$this->setupFromPrintScreen();
				$this->generate();	
				break;
		
		}//endswitch
		
		$this->output();	

	}//end method
	
}//end class




//PROCESSING
//=============================================================================
if(!isset($noOutput)){

	if(isset($_GET["cmd"])){
	
		$_POST["command"] = $_GET["cmd"];
		$_POST["statementdate"] = $_GET["sd"];
		
	}//endif
	
	if(isset($_POST["command"])) {
	
		session_cache_limiter('private');
	
		//set encoding to latin1 (fpdf doesnt like utf8)
		$sqlEncoding = "latin1";	
		require_once("../../../include/session.php");
		
		$report = new aritemsSummary($db);

		switch($_POST["command"]){
			
			case "print":
				$report->processDialog($_POST);				
				break;
		
		}//endswitch
		
	} else {
	
		require_once("../../../include/session.php");
		
		$report = new aritemsSummary($db);
		
		$report->showOptions();		
	
	}//end if
	
}//end if
?>