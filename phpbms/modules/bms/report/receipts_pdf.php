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

	if(!isset($_SESSION["userinfo"])){

		//set encoding to latin1 (fpdf doesnt like utf8)
		$sqlEncoding = "latin1";
		require_once("../../../include/session.php");

	}

	if(!class_exists("phpbmsReport"))
		include("report/report_class.php");

	class receiptPDF extends phpbmsReport{

		var $title = "Receipt";
		var $lineitemBoxHeight = 4.25;

		function receiptPDF($db, $orientation='P', $unit='mm', $format='Letter'){

			$this->db = $db;

			if(!class_exists("phpbmsPDFReport"))
				include("report/pdfreport_class.php");

			$this->pdf = new phpbmsPDFReport($db, $orientation, $unit, $format);

			$this->initialize();

		}//end method


		function initialize(){
			//This function will set column headings, sizes and formatting

			$pdf = &$this->pdf;

			$topinfo = array();
			$topinfo[] = new pdfColumn("ID", "id", 0.75);
			$topinfo[] = new pdfColumn("Date", "receiptdate", 1, "date");
			$topinfo[] = new pdfColumn("Processed By", "processedby", 0);

			$size = 0;
			foreach($topinfo as $column)
				$size += $column->size;

			$topinfo[2]->size = $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin - $size;

			$this->topinfo = $topinfo;


			$lineitems = array();
			$lineitems[] = new pdfColumn("Doc Ref", "relatedid", 0.5);
			$lineitems[] = new pdfColumn("Type", "type", 0.75);
			$lineitems[] = new pdfColumn("Doc Date", "itemdate", 0.75, "date");
			$lineitems[] = new pdfColumn("Due Date", "duedate", 0.75, "date");

			$lineitems[] = new pdfColumn("Doc Amount", "amount", 0, "currency", "R");
			$lineitems[] = new pdfColumn("Applied", "applied", 0.75, "currency", "R");
			$lineitems[] = new pdfColumn("Discount", "discount", 0.75, "currency", "R");
			$lineitems[] = new pdfColumn("Tax Adj", "taxadjustment", 0.75, "currency", "R");

			$size = 0;
			foreach($lineitems as $column)
				$size += $column->size;

			$lineitems[4]->size = $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin - $size;

			$this->lineitems = $lineitems;

			$totalsinfo = array();
			$totalsinfo[] = new pdfColumn("Distribution Remaining", "remaining", 0, "currency", "R");
			$totalsinfo[] = new pdfColumn("Receipt Total", "amount", 0, "currency", "R");

			$this->totalsinfo = $totalsinfo;

		}//end method


		function generate($whereclause = NULL, $sortorder = "receipts.id"){

			$pdf = &$this->pdf;

			if($whereclause)
				$this->whereclause = $whereclause;
			elseif(!$this->whereclause)
				$this->whereclause = "receipts.id = -400";

			if($sortorder)
				$this->sortorder = $sortorder;
			elseif(!$this->sortorder)
				$this->sortorder = "receipts.id";

			$paymentFields = "";
			if(ENCRYPT_PAYMENT_FIELDS){

				$paymentFields = "
					".$this->db->decrypt("`ccnumber`")." AS `ccnumber`,
					".$this->db->decrypt("`ccverification`")." AS `ccverification`,
					".$this->db->decrypt("`ccexpiration`")." AS `ccexpiration`,
					".$this->db->decrypt("`routingnumber`")." AS `routingnumber`,
					".$this->db->decrypt("`accountnumber`")." AS `accountnumber`,
				";

			}//end if

			$querystatement = "
				SELECT
					receipts.*,
					".$paymentFields."

					clients.firstname,
					clients.lastname,
					clients.company,
					addresses.address1,
					addresses.address2,
					addresses.city,
					addresses.state,
					addresses.postalcode,
					addresses.country,
					clients.homephone,
					clients.workphone,
					clients.email,

					paymentmethods.name AS paymentname,
					paymentmethods.type AS paymenttype,

					users.firstname AS processorfirst,
					users.lastname AS processorlast

				FROM
					receipts INNER JOIN clients ON receipts.clientid = clients.uuid
					INNER JOIN users ON receipts.modifiedby = users.id INNER JOIN
					addresstorecord ON addresstorecord.tabledefid = 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083' AND addresstorecord.primary = 1
					AND addresstorecord.recordid = clients.uuid INNER JOIN addresses
					ON addresstorecord.addressid = addresses.uuid
					LEFT JOIN paymentmethods ON receipts.paymentmethodid = paymentmethods.uuid";

			$querystatement = $this->assembleSQL($querystatement);
			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult) == 0){

				$this->_showNoRecords();
				exit;

			}//end if

			$pdf->hasComapnyHeader = true;
			$pdf->SetMargins();

			//iterate through each invoice record
			while($receiptrecord = $this->db->fetchArray($queryresult)){

				$querystatement = "
					SELECT
						SUM(applied) AS thesum
					FROM
						receiptitems
					WHERE
						receiptid ='".$receiptrecord["uuid"]."'
				";

				$sumresult = $this->db->query($querystatement);
				$sumrecord = $this->db->fetchArray($sumresult);

				$receiptrecord["remaining"] = $receiptrecord["amount"] - $sumrecord["thesum"];

				$this->page = 0;

				$this->receiptrecord = $receiptrecord;

				//adds top info
				$top = $this->_addPage();

				$this->_addLineItems($top);

				$pdf->SetXY($pdf->leftmargin, $top["y"] + $this->lineitemBoxHeight + 0.125);

				//totals
				$this->_addTotals();

				//Print any special/instructions and stuff
				$this->_addNotes();

			}//end while;


		}//end method


		function _addPage(){

			$pdf = &$this->pdf;

			$pdf->AddPage();
			$this->page++;

			$nextY = $pdf->getY();

			//TITLE
			$titleWidth=2.375;
			$titleHeight=.25;
			$pdf->setStyle("title");
			$pdf->SetXY(-1*($titleWidth+$pdf->rightmargin), $pdf->topmargin);
			$pdf->Cell($titleWidth, $titleHeight,$this->title, $pdf->borderDebug,1,"R");

			//CLIENT
			$startY = $pdf->GetY() + 0.75;

			$boxHeight = 1.75;
			$boxWidth = ($pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin)/2 -0.0625;

			$pdf->setLineWidth(0.02);
			$pdf->Rect($pdf->leftmargin, $startY, $boxWidth, $boxHeight);
			$pdf->setLineWidth(0.01);

			$pdf->setStyle("header");
			$pdf->setY($startY);
			$pdf->Cell($boxWidth, 0.17, "CLIENT", $pdf->borderDebug, 2, "L", 1);
			$pdf->setStyle("normal");

			//Company Name
			$companyDisplay = "";
			if($this->receiptrecord["company"]){
				$companyDisplay .= $this->receiptrecord["company"];
				if($this->receiptrecord["firstname"])
					$companyDisplay .= " (".$this->receiptrecord["firstname"]." ".$this->receiptrecord["lastname"].")";
			} else
				$companyDisplay .= $this->receiptrecord["firstname"]." ".$this->receiptrecord["lastname"];

			$pdf->SetXY($pdf->GetX() + 0.0625, $pdf->GetY() + 0.0625);
			$pdf->SetFont("Arial", "B", 10);
			$pdf->Cell($boxWidth - 0.125, 0.17, $companyDisplay, $pdf->borderDebug, 2, "L");

			$client = $this->_setClientInfo();

			$pdf->SetFont("Arial", "", 10);
			$pdf->setXY($pdf->GetX(), $pdf->GetY() + 0.0625);
			$pdf->MultiCell($boxWidth - 0.125,.17,$client, $pdf->borderDebug);

			//PAYMENT
			$pdf->setLineWidth(0.02);
			$pdf->Rect($pdf->leftmargin + $boxWidth + 0.125, $startY, $boxWidth, $boxHeight);
			$pdf->setLineWidth(0.01);

			$pdf->setStyle("header");
			$pdf->setXY($pdf->leftmargin + $boxWidth + 0.125, $startY);
			$pdf->Cell($boxWidth, 0.17, "PAYMENT", $pdf->borderDebug, 2, "L", 1);
			$pdf->setStyle("normal");

			if(!$this->receiptrecord["paymentname"])
				$this->receiptrecord["paymentname"] = "Other";

			$pdf->SetXY($pdf->GetX() + 0.0625, $pdf->GetY() + 0.0625);
			$pdf->SetFont("Arial", "B", 10);
			$pdf->Cell($boxWidth - 0.125, 0.17, $this->receiptrecord["paymentname"], $pdf->borderDebug, 2, "L");


			$paymentInfo = $this->_getPaymentInfo();
			$pdf->SetFont("Arial", "", 10);
			$pdf->setXY($pdf->GetX(), $pdf->GetY() + 0.0625);
			$pdf->MultiCell($boxWidth - 0.125,.17, $paymentInfo, $pdf->borderDebug);

			$pdf->setXY($pdf->leftmargin, $startY + $boxHeight + 0.125);

			$this->_addTopInfo();

			//line item headings
			$pdf->setStyle("header");
			$pdf->SetLineWidth(0.02);

			$coords["x"] = $pdf->GetX();
			$coords["y"] = $pdf->GetY();

			foreach($this->lineitems as $column)
				$pdf->Cell($column->size, 0.18, $column->title, 1, 0, $column->align, 1);


			return $coords;

		}//end method


		function _setClientInfo(){

			$client = $this->receiptrecord["address1"];

			if($this->receiptrecord["address2"])
				$client .= "\n".$this->receiptrecord["address2"];

			$client .="\n".$this->receiptrecord["city"].", ".$this->receiptrecord["state"]." ".$this->receiptrecord["postalcode"];

			if($this->receiptrecord["country"])
				$client .=" ".$this->receiptrecord["country"];

			$phoneemail = "";
			if($this->receiptrecord["workphone"] || $this->receiptrecord["homephone"]){

				if($this->receiptrecord["workphone"])
					$phoneemail = $this->receiptrecord["workphone"]." (W)";
				else
					$phoneemail = $this->receiptrecord["homephone"]." (H)";

				$phoneemail.="\n";

			}//end if

			if($this->receiptrecord["email"])
				$phoneemail .= $this->receiptrecord["email"];

			if($phoneemail)
				$client .= "\n\n".$phoneemail;

			return $client;

		}//end method


		function _getPaymentInfo(){

			$info = "";

			switch($this->receiptrecord["paymenttype"]){

				case "charge":

					$info .= $this->receiptrecord["ccnumber"];
					$info .= "\n Expires: ".$this->receiptrecord["ccexpiration"];
					if($this->receiptrecord["ccverification"])
						$info .= "\n Verification/Pin: ".$this->receiptrecord["ccverification"];
					break;

				case "draft":

					$info .= $this->receiptrecord["bankname"];
					$info .= "\n Check No: ".$this->receiptrecord["checkno"];

					if($this->receiptrecord["accountnumber"] || $this->receiptrecord["routingnumber"])
						$info .= "\n";

					if($this->receiptrecord["accountnumber"])
						$info .="\n".$this->receiptrecord["accountnumber"];

					if($this->receiptrecord["routingnumber"])
						$info .="\n".$this->receiptrecord["routingnumber"];

					break;

				default:

					if(!$this->receiptrecord["paymentmethodid"])
						$info .= $this->receiptrecord["paymentother"];

			}//endswitch

			if($this->receiptrecord["transactionid"])
				$info .= "\nTransaction ID: ".$this->receiptrecord["transactionid"];

			return $info;

		}//end method


		function _addTopInfo(){

			$pdf = &$this->pdf;

			$pdf->setStyle("header");
			$pdf->SetLineWidth(0.02);

			foreach($this->topinfo as $column)
				$pdf->Cell($column->size, 0.18, $column->title, 1, 0, $column->align, 1);

			$pdf->Rect($pdf->leftmargin, $pdf->GetY(), ($pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin), 0.39);

			$pdf->SetXY($pdf->leftmargin, $pdf->GetY() + .2);


			$this->receiptrecord["processedby"] = $this->receiptrecord["processorfirst"]." ".$this->receiptrecord["processorlast"];
			$pdf->setStyle("normal");

			foreach($this->topinfo as $column){

				if($column->format != "")
					$value = formatVariable($this->receiptrecord[$column->fieldname], $column->format);
				else
					$value = $this->receiptrecord[$column->fieldname];

				$pdf->Cell($column->size, 0.18, $value, $pdf->borderDebug, 0, $column->align);

			}//end foreach

			$pdf->SetY($pdf->GetY() + 0.18 + 0.125);

		}//end method


		function _addLineItems($coords){

			$pdf = &$this->pdf;

			$lineitemresult = $this->_getLineItems();

			$pdf->setStyle("normal");

			$pdf->SetY($pdf->GetY() + 0.18 + 0.0625);

			while($line = $this->db->fetchArray($lineitemresult)){

				if($pdf->GetY() + 0.17*3 > $coords["y"] + $this->lineitemBoxHeight){

					$pdf->SetLineWidth(0.02);
					$pdf->Rect($coords["x"], $coords["y"], $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, $this->lineitemBoxHeight);
					$pdf->SetLineWidth(0.01);

					$this->_addPage();

				}//end if

				if($line["type"] == "invoice"){

					$tempDate = stringToDate($line["itemdate"], "SQL");
					$line["duedate"] = dateToString( strtotime(TERM1_DAYS." days", $tempDate), "SQL" );

				} else
					$line["duedate"] = "";

				if($line["type"] == "deposit" && $line["relatedid"] == $this->receiptrecord["id"]){
					$line["relatedid"] = "";
					$line["amount"] = 0;
					$line["aritemid"] = 0;
				}

				if($this->receiptrecord["posted"])
					$line["docdue"] = $line["amount"] - $line["paid"];
				elseif($line["relatedid"])
					$line["docdue"] = $line["amount"] - $line["paid"] - $line["applied"] - $line["discount"] - $line["taxadjustment"];
				else
					$line["docDue"] = 0;


				foreach($this->lineitems as $column){

					$ln = 0;


					switch($column->fieldname){

						case "parts":
							$pdf->SetFont("Arial", "B", 8);
							$pdf->Write(0.17, $line["partname"]);
							$pdf->setStyle("normal");
							$pdf->SetX($pdf->leftmargin + $column->size);
							break;

						default:
							if($column->format != "")
								$value = formatVariable($line[$column->fieldname], $column->format);
							else
								$value = $line[$column->fieldname];

							if($value == "&middot;")
								$value = " ";

							if($column->fieldname == $this->lineitems[count($this->lineitems)-1]->fieldname)
								$ln = 2;

							$pdf->Cell($column->size, 0.17, $value, $pdf->borderDebug, $ln, $column->align);
							break;

					}//end switch

				}//end foreach

				$pdf->SetXY($pdf->leftmargin, $pdf->GetY() + 0.0625);
				$pdf->SetLineWidth(0.01);
				$pdf->SetDrawColor(180,180,180);
				$pdf->Line($pdf->leftmargin, $pdf->GetY(), $pdf->paperwidth - $pdf->rightmargin, $pdf->GetY());
				$pdf->SetDrawColor(0,0,0);
				$pdf->SetLineWidth(0.02);
				$pdf->SetXY($pdf->leftmargin, $pdf->GetY() + 0.0625);

			}//end while

			$pdf->SetLineWidth(0.02);
			$pdf->Rect($coords["x"], $coords["y"], $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, $this->lineitemBoxHeight);

		}//end method


		function _getLineItems(){

			$querystatement = "
				SELECT
					receiptitems.aritemid,
					receiptitems.applied,
					receiptitems.discount,
					receiptitems.taxadjustment,
					aritems.type,
					IF(`invoices`.`id`,`invoices`.`id`,`receipts`.`id`) AS `relatedid`,
					aritems.itemdate,
					aritems.amount,
					aritems.paid
				FROM
					((receiptitems INNER JOIN aritems ON receiptitems.aritemid = aritems.uuid)LEFT JOIN `invoices` ON `aritems`.`relatedid` = `invoices`.`uuid`) LEFT JOIN `receipts` ON `aritems`.`relatedid` = `receipts`.`uuid`
				WHERE
					receiptitems.receiptid = '".mysql_real_escape_string($this->receiptrecord["uuid"])."'
				ORDER BY
					aritems.type,
					aritems.itemdate";

				return $this->db->query($querystatement);

		}//end method


		function _addNotes(){

			$pdf = &$this->pdf;

			$height = 1;
			$nextPos = $pdf->GetY() + $height + 0.125;

			$pdf->Rect($pdf->GetX(), $pdf->GetY(), $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, $height);
			$pdf->setStyle("header");
			$pdf->Cell($pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, 0.18, "Notes", 1, 2, "L", 1);

			$pdf->setStyle("normal");
			$pdf->SetXY($pdf->GetX() + .06125, $pdf->GetY() + .06125);
			$pdf->MultiCell($pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin - 0.125, 0.18, $this->receiptrecord["memo"]);

			$pdf->SetXY($pdf->leftmargin, $nextPos);

		}//end method


		function _addTotals(){

			$pdf = &$this->pdf;

			$size = $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin;

			$tempTotalsinfo = $this->totalsinfo;

			if($this->receiptrecord["remaining"]){

				$this->totalsinfo[1]->size = 1;
				$size -= 1;

			} else
				array_shift($this->totalsinfo);

			$this->totalsinfo[0]->size = $size;

			$height = .5;
			$nextPos = $pdf->GetY() + $height + 0.125;

			$pdf->Rect($pdf->GetX(), $pdf->GetY(), $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, $height);

			$pdf->setStyle("header");

			foreach($this->totalsinfo as $column)
				$pdf->Cell($column->size, 0.18, $column->title, 1, 0, $column->align, 1);

			$pdf->setStyle("normal");
			$pdf->SetFont("Arial", "B", 10);
			$pdf->SetXY($pdf->leftmargin, $pdf->GetY() + 0.18 + 0.0625);

			foreach($this->totalsinfo as $column){

				if($column->format != "")
					$value = formatVariable($this->receiptrecord[$column->fieldname], $column->format);
				else
					$value = $this->receiptrecord[$column->fieldname];

				$pdf->Cell($column->size, 0.18, $value, $pdf->borderDebug, 0, $column->align);

			}//end foreach

			if(isset($this->totalsinfo[1]))
				$this->totalsinfo[1]->size = 0;

			$pdf->SetXY($pdf->leftmargin, $nextPos);

			$this->totalsinfo = $tempTotalsinfo;

		}//end method



		function output($destination = "screen" , $userinfo = NULL){

			switch($destination){

				case "screen":
					$this->pdf->Output();
					break;

				case "email":

					if(!$userinfo)
						$userinfo = $_SESSION["userinfo"];

					if(!$userinfo["email"] || !$this->receiptrecord["email"])
						return false;

					$pdf = $this->pdf->Output(NULL, "S");

					$to = 		$this->receiptrecord["email"];
					$from = 	$userinfo["email"];
					$subject = 	"Your ".$this->title." from ".COMPANY_NAME;
					$message = 	"Attached is your ".$this->title." from ".COMPANY_NAME."\n\n" .
								"The attachment requires Adobe Acrobat Reader to view. \n If you do not " .
								"have Acrobat Reader, you can download it at http://www.adobe.com  \n\n" .
								COMPANY_NAME."\n".
								COMPANY_ADDRESS."\n".COMPANY_CSZ."\n".COMPANY_PHONE;

					$headers = "From: $from";

					$semi_rand = md5( time() );
					$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

					$headers .= "\nMIME-Version: 1.0\n" .
								"Content-Type: multipart/mixed;\n" .
								" boundary=\"{$mime_boundary}\"";

					$message = "This is a multi-part message in MIME format.\n\n" .
							"--{$mime_boundary}\n" .
							"Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
							"Content-Transfer-Encoding: 7bit\n\n" .
							$message . "\n\n";

					$pdf = chunk_split( base64_encode( $pdf ) );

					$message .= "--{$mime_boundary}\n" .
							 "Content-Type: {application/pdf};\n" .
							 " name=\"".$this->title.$this->receiptrecord["id"]."\"\n" .
							 "Content-Disposition: attachment;\n" .
							 " filename=\"".$this->title.$this->receiptrecord["id"].".pdf\"\n" .
							 "Content-Transfer-Encoding: base64\n\n" .
							 $pdf . "\n\n" .
							 "--{$mime_boundary}--\n";

					return @ mail($to, $subject, $message, $headers);

					break;

			}//endswitch

		}//end method

	}//end class


//PROCESSING
//=============================================================================
if(!isset($noOutput)){

	//IE needs caching to be set to private in order to display PDFS
	session_cache_limiter('private');



	$report = new receiptPDF($db, 'P', 'in', 'Letter');
	$report->setupFromPrintScreen();
	$report->generate();
	$report->output();

}//end if

?>