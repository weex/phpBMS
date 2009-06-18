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

class aritemsClientStatements extends phpbmsReport{

	var $currentTotal = 0;
	var $term1Total = 0;
	var $term2Total = 0;
	var $term3Total = 0;
	var $showPayments = true;
	var $showClosed = false;

	function aritemsClientStatements($db, $statementDate = NULL, $showPayments = true, $showClosed = false){

		parent::phpbmsReport($db);

		if($statementDate)
			$this->statementDate = $statementDate;
		else
			$this->statementDate = mktime(0,0,0);

		$this->showPayments = $showPayments;
		$this->showClosed = $showClosed;

	}//end method aritemsClientStatements


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
				clients.id,
				clients.firstname,
				clients.lastname,
				clients.company,
				addresses.address1,
				addresses.address2,
				addresses.city,
				addresses.postalcode,
				addresses.state,
				addresses.country,
				clients.creditlimit,
				clients.homephone,
				clients.workphone,
				clients.email,
				users.firstname AS salesfirstname,
				users.lastname AS saleslastname
			FROM
				(aritems INNER JOIN clients
					ON aritems.clientid = clients.uuid
				INNER JOIN addresstorecord
					ON addresstorecord.tabledefid = 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083' AND addresstorecord.primary = 1
					AND addresstorecord.recordid = clients.uuid
				INNER JOIN addresses
					ON addresstorecord.addressid = addresses.uuid
				)LEFT JOIN users
					ON clients.salesmanagerid = users.uuid";

		$this->sortorder = 'if(clients.lastname!="",concat(clients.lastname,", ",clients.firstname,if(clients.company!="",concat(" (",clients.company,")"),"")),clients.company)';

		$querystatement = $this->assembleSQL($querystatement);

		$clientresult = $this->db->query($querystatement);

		if(!class_exists("phpbmsPDFReport"))
			include("report/pdfreport_class.php");

		$this->pdf = new phpbmsPDFReport($this->db, "P","in","Letter");

		$pdf = &$this->pdf;

		$this->itemColumns = array(
			array("field" => "itemdate", "title" => "date", "size" => 1, "align" => "L", "nl" => 0),
			array("field" => "type", "title" => "type", "size" => 1, "align" => "L", "nl" => 0),
			array("field" => "relatedid", "title" => "doc ref", "size" => 1, "align" => "L", "nl" => 0),
			array("field" => "dayspd", "title" => "days past due", "size" => 1, "align" => "L", "nl" => 0),
			array("field" => "amount", "title" => "doc amount", "size" => 0, "align" => "R", "nl" => 0),
			array("field" => "due", "title" => "due", "size" => 1, "align" => "R", "nl" => 2),
		);

		$this->itemColumns[4]["size"] = $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin - 5;

		//uncomment the following line to help troubleshoot formatting
		//$pdf->borderDebug = 1;
		$pdf->hasComapnyHeader = true;
		$pdf->SetMargins();

		while($clientrecord = $this->db->fetchArray($clientresult)){

			$this->page = 1;

			$this->clientrecord = $clientrecord;

			$this->_addPage();

			$this->_addItems();

			//footer
			$pdf->setXY($pdf->leftmargin, 8.5 + $pdf->topmargin);

			$pdf->setStyle("header");
			$pdf->Cell(1 - 0.01, 0.17, "Current", 1, 0, "R", 1);
			$pdf->SetX($pdf->GetX()+.02);
			$pdf->Cell(1 - 0.02, 0.17, (TERM1_DAYS+1)." - ".TERM2_DAYS, 1, 0, "R", 1);
			$pdf->SetX($pdf->GetX()+.02);
			$pdf->Cell(1 - 0.02, 0.17, (TERM2_DAYS+1)." - ".TERM3_DAYS, 1, 0, "R", 1);
			$pdf->SetX($pdf->GetX()+.02);
			$pdf->Cell(1 - 0.02, 0.17, "+".TERM3_DAYS, 1, 0, "R", 1);
			$pdf->SetX($pdf->GetX()+.02);
			$pdf->Cell(1 - 0.02, 0.17, "Total Due", 1, 2, "R", 1);

			$pdf->SetX($pdf->leftmargin);

			$pdf->setStyle("normal");
			$pdf->SetFont("Arial", "B");
			$pdf->Cell(1, 0.17, formatVariable($this->currentTotal, "currency"), 1, 0, "R", 1);
			$pdf->Cell(1, 0.17, formatVariable($this->term1Total, "currency"), 1, 0, "R", 1);
			$pdf->Cell(1, 0.17, formatVariable($this->term2Total, "currency"), 1, 0, "R", 1);
			$pdf->Cell(1, 0.17, formatVariable($this->term3Total, "currency"), 1, 0, "R", 1);

			$totalDue = $this->currentTotal + $this->term1Total + $this->term2Total + $this->term3Total;

			$pdf->Cell(1 - 0.01, 0.17, formatVariable($totalDue, "currency"), 1, 0, "R", 1);

			$this->currentTotal = 0;
			$this->term1Total = 0;
			$this->term2Total = 0;
			$this->term3Total = 0;

		}//end while

	}//end method


	function _addPage(){

		$pdf = &$this->pdf;

		$pdf->AddPage();

		$nextY = $pdf->getY();

		//title/info
		$title = "Statement";
		$titleWidth=2.5;
		$titleHeight=.25;
		$pdf->setStyle("title");
		$pdf->SetXY(-1*($titleWidth+$pdf->rightmargin), $pdf->topmargin);
		$pdf->Cell($titleWidth, $titleHeight,$title, $pdf->borderDebug,1,"R");

		$sideWidth = 1.25;
		$pdf->SetXY( -1 * ($sideWidth + $pdf->rightmargin), $nextY + 0.125);
		$pdf->setStyle("header");
		$pdf->Cell($sideWidth, .17, "date", 1, 2, "L", 1);
		$pdf->setStyle("normal");
		$pdf->Cell($sideWidth, .17, dateToString($this->statementDate), 1, 2, "L");

		$pdf->setStyle("header");
		$pdf->Cell($sideWidth, .17, "Account No.", 1, 2, "L", 1);
		$pdf->setStyle("normal");
		$pdf->Cell($sideWidth, .17, $this->clientrecord["id"], 1, 2, "L");

		$pdf->setStyle("header");
		$pdf->Cell($sideWidth, .17, "Sales Rep.", 1, 2, "L", 1);
		$pdf->setStyle("normal");
		$pdf->SetFontSize(6);
		$pdf->Cell($sideWidth, .17, $this->clientrecord["salesfirstname"]." ".$this->clientrecord["saleslastname"], 1, 2, "L");

		$pdf->SetFontSize(8);
		$pdf->Cell($sideWidth, .17, "page: ".$this->page, $pdf->borderDebug, 2, "L");

		//Client info
		$displayName = "";
		if($this->clientrecord["company"]){

			$displayName .= $this->clientrecord["company"];

			if($this->clientrecord["lastname"])
				$displayName .= " (".$this->clientrecord["firstname"]." ".$this->clientrecord["lastname"].")";

		} else
			$displayName .= $this->clientrecord["firstname"]." ".$this->clientrecord["lastname"];

		$address = $this->clientrecord["address1"];

		if ($this->clientrecord["address2"])
			$address.="\n".$this->clientrecord["address2"];

		$address .= "\n".$this->clientrecord["city"].", ".$this->clientrecord["state"]."  ".$this->clientrecord["postalcode"];

		if($this->clientrecord["country"])
			$address .= " ".$this->clientrecord["country"];

		$phoneemail = "";
		if($this->clientrecord["workphone"] || $this->clientrecord["homephone"]){

			$phoneemail = $this->clientrecord["homephone"]." (H)";

			if($this->clientrecord["workphone"])
				$phoneemail = $this->clientrecord["workphone"]." (W)";

			$phoneemail .= "\n";

		}//end if

		if($this->clientrecord["email"])
			$phoneemail .= $this->clientrecord["email"];

		if($phoneemail)
			$address .= "\n\n".$phoneemail;

		$clientWidth = ($pdf->paperwidth - $pdf->leftmargin - $pdf-> rightmargin)/2;
		$pdf->setXY($pdf->leftmargin, $nextY + 0.375);
		$pdf->SetFont("Arial","B",10);
		$pdf->Cell($clientWidth,.16,$displayName, $pdf->borderDebug,2,"L");
		$pdf->SetFont("Arial","B",9);
		$pdf->MultiCell($clientWidth,.13,$address, $pdf->borderDebug);


		//now for item headers
		$pdf->SetXY($pdf->leftmargin, 2.375 + $pdf->topmargin);
		$pdf->setStyle("header");

		$x = $pdf->GetX();
		$y = $pdf->GetY() + 0.17;
		$this->itmeAreaHeight = 5.5;
		$pdf->Line($x, $y, $x, $y + $this->itmeAreaHeight);

		foreach($this->itemColumns as $column){

			if($column["nl"] == 0){

				$x += $column["size"];
				$pdf->SetDrawColor(120,120,120);
				$pdf->Line($x -0.01, $y, $x -0.01, $y + $this->itmeAreaHeight);
				$pdf->SetDrawColor(0,0,0);

				$column["size"] -= 0.02;

			}//endif

			$pdf->Cell($column["size"], 0.17, $column["title"], 1, $column["nl"], $column["align"], 1);

			if($column["nl"] == 0)
				$pdf->SetX($pdf->GetX() + 0.02);

		}//endforeach
		$x = $pdf->paperwidth - $pdf->rightmargin;
		$pdf->Line($x, $y, $x, $y + $this->itmeAreaHeight);
		$pdf->Line($pdf->leftmargin, $y + $this->itmeAreaHeight, $x, $y + $this->itmeAreaHeight);

		$pdf->setStyle("normal");

	}//end method


	function _addItems(){

		$pdf = &$this->pdf;

		$itemsArray = $this->_getItems();

		$itemHeight = 0.25;
		$currHeight = 0;


		foreach($itemsArray as $item){

			$pdf->SetX($pdf->leftmargin);

			if($currHeight >= $this->itmeAreaHeight){

				//too many items requires multiple pages
				$this->page++;
				$this->_addPage();

			}//end if

			foreach($this->itemColumns as $column){

				$pdf->Cell($column["size"], $itemHeight, $item[$column["field"]], $pdf->borderDebug, $column["nl"], $column["align"]);

			}//endforeach

			$currHeight += $itemHeight;

		}//end foreach

	}//end method


	function _getItems(){

		$querystatement = "
			SELECT
				`aritems`.`id`,
				`aritems`.`type`,
				IF(`invoices`.`id`,`invoices`.`id`,`receipts`.`id`) AS `relatedid`,
				`aritems`.`itemdate`,
				`aritems`.`amount`,
				`aritems`.`amount` - `paid` AS due
			FROM
				(`aritems` LEFT JOIN `invoices` ON `aritems`.`relatedid` = `invoices`.`uuid`) LEFT JOIN `receipts` ON `aritems`.`relatedid` = `receipts`.`uuid`
			WHERE
				`aritems`.posted = 1
				AND `aritems`.clientid = '".mysql_real_escape_string($this->clientrecord["clientid"])."'
		";

		if(!$this->showClosed)
			$querystatement .= "
				AND `aritems`.`status` = 'open'";

		$querystatement .= "
				AND `aritems`.itemdate <= '".dateToString($this->statementDate,"SQL")."'
			ORDER BY
				`aritems`.itemdate,
				`aritems`.id";

		$queryresult = $this->db->query($querystatement);

		$returnArray = array();

		$serviceChargeAmount = 0;

		while($therecord = $this->db->fetchArray($queryresult)){

			if($therecord["type"] == "service charge"){

				$serviceChargeAmount += $therecord["due"];

			} else {

				$itemdate = stringToDate($therecord["itemdate"],"SQL");

				if($therecord["type"] != "invoice")
					$therecord["dayspd"]="";
				else {

					$daysover = floor(($this->statementDate - $itemdate)/86400) - TERM1_DAYS;

					if($daysover + TERM1_DAYS > TERM3_DAYS)
						$this->term3Total += $therecord["due"];
					elseif($daysover + TERM1_DAYS > TERM2_DAYS)
						$this->term2Total += $therecord["due"];
					elseif($daysover > 0)
						$this->term1Total += $therecord["due"];
					else{

						$this->currentTotal += $therecord["due"];
						$daysover = "";

					}//end if

					if($therecord["due"] > 0)
						$therecord["dayspd"] = $daysover;
					else
						$therecord["dayspd"] = "(paid in full)";

				}//endif

				$keydate = $therecord["itemdate"];
				$therecord["itemdate"] = formatFromSQLDate($therecord["itemdate"]);
				$therecord["amount"] = formatVariable($therecord["amount"], "currency");

				$therecord["due"] = formatVariable($therecord["due"], "currency");

				$returnArray[$keydate."-".$therecord["id"]] = $therecord;

			}//end if

		}//endwhile

		if($serviceChargeAmount){

			$serviceRecord["itemdate"] = dateToString($this->statementDate);
			$serviceRecord["dayspd"] = "";
			$serviceRecord["type"] = "service charges";
			$serviceRecord["relatedid"] = "";
			$serviceRecord["amount"] = formatVariable($serviceChargeAmount, "currency");;
			$serviceRecord["due"] = formatVariable($serviceChargeAmount, "currency");

			$this->currentTotal += $serviceChargeAmount;

			$returnArray[dateToString($this->statementDate, "SQL")."-XXX"] = $serviceRecord;

		}//endif

		if($this->showPayments){

			//add in receipts in laste term1_days

			$lastDays = dateToString( strtotime("-".TERM1_DAYS." days", $this->statementDate), "SQL");

			$querystatement = "SELECT
				id,
				receiptdate,
				amount
			FROM
				receipts
			WHERE
				clientid = '".mysql_real_escape_string($this->clientrecord["clientid"])."'
				AND receiptdate <= '".dateToString($this->statementDate, "SQL")."'
				AND receiptdate >= '".$lastDays."'
				AND posted = 1";

			$receiptresult = $this->db->query($querystatement);

			while($receiptrecord = $this->db->fetchArray($receiptresult)){

				$rcptRecord["itemdate"] = formatFromSQLDate($receiptrecord["receiptdate"]);
				$rcptRecord["dayspd"] = "";
				$rcptRecord["type"] = "payment";
				$rcptRecord["relatedid"] = "";
				$rcptRecord["amount"] = "(".formatVariable($receiptrecord["amount"], "currency").")";
				$rcptRecord["due"] = "";

				$returnArray[$receiptrecord["receiptdate"]."-D".$receiptrecord["id"]] = $rcptRecord;

			}//endwhile

		}//end if

		ksort($returnArray);

		return $returnArray;

	}//end method


	function output($to = "screen"){

		$this->pdf->Output();

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
			<h1><span>Client AR Statement</span></h1>
			<form action="<?php echo $formSubmit ?>" id="record" method="post">
			<input type="hidden" id="command" name="command" />

				<fieldset>
					<legend>options</legend>

					<p><?php $theform->showField("statementdate");?></p>

					<p><?php $theform->showField("showclosed");?></p>

					<p><?php $theform->showField("showpayments");?></p>

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

		$this->showPayments = isset($variables["showpayments"]);

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

		$report = new aritemsClientStatements($db);

		switch($_POST["command"]){

			case "print":
				$report->processDialog($_POST);
				break;

		}//endswitch

	} else {

		require_once("../../../include/session.php");

		$report = new aritemsClientStatements($db);

		$report->showOptions();

	}//end if

}//end if
?>