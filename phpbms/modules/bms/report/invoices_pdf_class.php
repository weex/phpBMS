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
		include("report/report_class.php");
		
	class invoicePDF extends phpbmsReport{
	
		var $title = "Invoice";
		var $showShipNameInShipTo = true;
		var $lineitemBoxHeight = 4.25;
			
		function invoicePDF($db, $orientation='P', $unit='mm', $format='Letter'){
		
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
			$topinfo[] = new pdfColumn("Order ID", "id", 0.75);
			$topinfo[] = new pdfColumn("Order Date", "orderdate", 1, "date");
			$topinfo[] = new pdfColumn("Client PO", "ponumber", 1);
			$topinfo[] = new pdfColumn("Processed By", "processedby", 0);
			$topinfo[] = new pdfColumn("Payment Method", "paymentname",2);
			
			$size = 0;
			foreach($topinfo as $column)
				$size += $column->size;
				
			$topinfo[3]->size = $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin - $size;

			$this->topinfo = $topinfo;
			
			
			$lineitems = array();
			$lineitems[] = new pdfColumn("Product / (Part Number)", "parts", 0);
			$lineitems[] = new pdfColumn("Tax", "taxable", 0.5, "boolean", "C");
			$lineitems[] = new pdfColumn("Unit Price", "unitprice", 0.75, "currency", "R");
			$lineitems[] = new pdfColumn("Qty", "quantity", 0.5, "real","R");
			$lineitems[] = new pdfColumn("Extended", "extended", 0.75, "currency", "R");
						
			$size = 0;
			foreach($lineitems as $column)
				$size += $column->size;
				
			$lineitems[0]->size = $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin - $size;
			
			$this->lineitems = $lineitems;
			
			$totalsinfo = array();
			$totalsinfo[] = new pdfColumn("Discount", "discountamount", 1, "currency", "R");
			$totalsinfo[] = new pdfColumn("Subtotal", "totaltni", 0, "currency", "R");
			$totalsinfo[] = new pdfColumn("Tax", "tax", 1, "currency", "R");
			$totalsinfo[] = new pdfColumn("Shipping", "shipping", 1, "currency", "R");
			$totalsinfo[] = new pdfColumn("Total", "totalti", 1, "currency", "R");
			$totalsinfo[] = new pdfColumn("Due", "amountdue", 1, "currency", "R");
									
			$this->totalsinfo = $totalsinfo;

		}//end method

		
		function generate($whereclause = NULL, $sortorder = "invoices.id"){
		
			$pdf = &$this->pdf;
			
			if($whereclause)
				$this->whereclause = $whereclause;
			elseif(!$this->whereclause)
				$this->whereclause = "invoices.id = -400";
			
			if($sortorder)
				$this->sortorder = $sortorder;
			elseif(!$this->sortorder)
				$this->sortorder = "invoices.id";
				
			$querystatement = "
				SELECT
					invoices.*,
					
					invoices.totalti - invoices.amountpaid AS amountdue,
					
					clients.firstname,
					clients.lastname,
					clients.company,
					clients.homephone,
					clients.workphone,
					clients.email,
					
					shippingmethods.name AS shippingname,
					
					paymentmethods.name AS paymentname,
					paymentmethods.type AS paymenttype,
					
					tax.name as taxname,
										
					users.firstname AS processorfirst,
					users.lastname AS processorlast
															
				FROM
					invoices INNER JOIN clients ON invoices.clientid = clients.id
					INNER JOIN users ON invoices.modifiedby = users.id
					LEFT JOIN shippingmethods ON invoices.shippingmethodid = shippingmethods.id
					LEFT JOIN paymentmethods ON invoices.paymentmethodid = paymentmethods.id
					LEFT JOIN tax ON invoices.taxareaid = tax.id";
					
			$querystatement = $this->assembleSQL($querystatement);
			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult) == 0){
			
				$this->_showNoRecords();
				exit;

			}//end if
			
			$pdf->hasComapnyHeader = true;
			$pdf->SetMargins();			
			
			//iterate through each invoice record
			while($invoicerecord = $this->db->fetchArray($queryresult)){
			
				$this->page = 0;
				
				$this->invoicerecord = $invoicerecord;
				
				//adds top info			
				$top = $this->_addPage();
			
				$this->_addLineItems($top);
			
				$pdf->SetXY($pdf->leftmargin, $top["y"] + $this->lineitemBoxHeight + 0.125);
				
				//Print any special/instructions and stuff							
				$this->_addNotes();
				
				//totals
				$this->_addTotals();
				
				//payment details
				$this->_addPaymentDetails();
				
			}//end while;
		
		
		}//end method
		
		
		function _addPage(){
		
			$pdf = &$this->pdf;
			
				
			$pdf->AddPage();	
			$this->page++;
			
			$nextY = $pdf->getY();
			
			//TITLE
			$title = "Statement";
			$titleWidth=2.375;
			$titleHeight=.25;
			$pdf->setStyle("title");
			$pdf->SetXY(-1*($titleWidth+$pdf->rightmargin), $pdf->topmargin);
			$pdf->Cell($titleWidth, $titleHeight,$this->title, $pdf->borderDebug,1,"R");
			
			$startY = $pdf->GetY() + 0.75;

			//page number?
			$pdf->setStyle("normal");
			$pageNoWidth = 1;
			$pdf->SetFontSize(8);
			$pdf->SetXY(-1*($pageNoWidth + $pdf->rightmargin), $pdf->topmargin + $titleHeight + 0.25);
			$pdf->Cell($pageNoWidth, 0.17, "page: ".$this->page, $pdf->borderDebug,1,"R");

			
			//SOLD TO
			$boxHeight = 1.75;
			$boxWidth = ($pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin)/2 -0.0625;

			$pdf->setLineWidth(0.02);
			$pdf->Rect($pdf->leftmargin, $startY, $boxWidth, $boxHeight);
			$pdf->setLineWidth(0.01);
			
			$pdf->setStyle("header");
			$pdf->setY($startY);
			$pdf->Cell($boxWidth, 0.17, "SOLD TO", $pdf->borderDebug, 2, "L", 1);
			$pdf->setStyle("normal");
						
			//Company Name
			$companyDisplay = "";
			if($this->invoicerecord["company"]){
				$companyDisplay .= $this->invoicerecord["company"];
				if($this->invoicerecord["firstname"])
					$companyDisplay .= " (".$this->invoicerecord["firstname"]." ".$this->invoicerecord["lastname"].")";			
			} else 
				$companyDisplay .= $this->invoicerecord["firstname"]." ".$this->invoicerecord["lastname"];			

			$pdf->SetXY($pdf->GetX() + 0.0625, $pdf->GetY() + 0.0625);
			$pdf->SetFont("Arial", "B", 10);
			$pdf->Cell($boxWidth - 0.125, 0.17, $companyDisplay, $pdf->borderDebug, 2, "L");

			$billto = $this->_setBillTo();
			$pdf->SetFont("Arial", "", 10);
			$pdf->setXY($pdf->GetX(), $pdf->GetY() + 0.0625);
			$pdf->MultiCell($boxWidth - 0.125,.17,$billto, $pdf->borderDebug);
			
			//SHIP TO
			$pdf->setLineWidth(0.02);
			$pdf->Rect($pdf->leftmargin + $boxWidth + 0.125, $startY, $boxWidth, $boxHeight);
			$pdf->setLineWidth(0.01);
			
			$pdf->setStyle("header");
			$pdf->setXY($pdf->leftmargin + $boxWidth + 0.125, $startY);
			$pdf->Cell($boxWidth, 0.17, "SHIP TO", $pdf->borderDebug, 2, "L", 1);
			$pdf->setStyle("normal");

			$pdf->SetXY($pdf->GetX() + 0.0625, $pdf->GetY() + 0.0625);
			$pdf->SetFont("Arial", "B", 10);
			
			$shipDisplay = (!$this->invoicerecord["shiptosameasbilling"] && $this->invoicerecord["shiptoname"])? $this->invoicerecord["shiptoname"] :$companyDisplay;
			$pdf->Cell($boxWidth - 0.125, 0.17, $shipDisplay, $pdf->borderDebug, 2, "L");
			
			$shipto = $this->_setShipTo();
			$pdf->SetFont("Arial", "", 10);
			$pdf->setXY($pdf->GetX(), $pdf->GetY() + 0.0625);
			$pdf->MultiCell($boxWidth - 0.125,.17, $shipto, $pdf->borderDebug);	
			
			$pdf->setXY($pdf->leftmargin, $startY + $boxHeight + 0.125);
			
			$this->_topInvoiceInfo();
			
			//line item headings
			$pdf->setStyle("header");
			$pdf->SetLineWidth(0.02);

			$coords["x"] = $pdf->GetX();
			$coords["y"] = $pdf->GetY();
			
			foreach($this->lineitems as $column)
				$pdf->Cell($column->size, 0.18, $column->title, 1, 0, $column->align, 1);
			
			return $coords;

		}//end method


		function _setBillTo(){

			$billto = $this->invoicerecord["address1"];
			
			if($this->invoicerecord["address2"])
				$billto .= "\n".$this->invoicerecord["address2"];
				
			$billto .="\n".$this->invoicerecord["city"].", ".$this->invoicerecord["state"]." ".$this->invoicerecord["postalcode"];
			
			if($this->invoicerecord["country"])
				$billto .=" ".$this->invoicerecord["country"];
				
			$phoneemail = "";
			if($this->invoicerecord["workphone"] || $this->invoicerecord["homephone"]){
			
				if($this->invoicerecord["workphone"])
					$phoneemail = $this->invoicerecord["workphone"]." (W)";
				else
					$phoneemail = $this->invoicerecord["homephone"]." (H)";
				
				$phoneemail.="\n";
				
			}//end if
			
			if($this->invoicerecord["email"])
				$phoneemail .= $this->invoicerecord["email"];
			
			if($phoneemail)
				$billto .= "\n\n".$phoneemail;
			
			return $billto;
		
		}//end method

		
		function _setShipTo(){
			
			$added = ($this->invoicerecord["shiptosameasbilling"])? "" : "shipto";
			
			$shipto = "";
							
			$shipto .= $this->invoicerecord[$added."address1"];
			
			if($this->invoicerecord[$added."address2"])
				$shipto .= "\n".$this->invoicerecord[$added."address2"];
				
			$shipto .="\n".$this->invoicerecord[$added."city"].", ".$this->invoicerecord[$added."state"]." ".$this->invoicerecord[$added."postalcode"];
			
			if($this->invoicerecord[$added."country"])
				$shipto .=" ".$this->invoicerecord[$added."country"];				
			
			if($this->showShipNameInShipTo)
				if($this->invoicerecord["shippingname"])
					$shipto .="\n\nShipping Method:\n".$this->invoicerecord["shippingname"];
			
			return $shipto;

		}//end method


		function _topInvoiceInfo(){
			
			$pdf = &$this->pdf;
			
			$pdf->setStyle("header");
			$pdf->SetLineWidth(0.02);

			foreach($this->topinfo as $column)
				$pdf->Cell($column->size, 0.18, $column->title, 1, 0, $column->align, 1);

			$pdf->Rect($pdf->leftmargin, $pdf->GetY(), ($pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin), 0.39);		
		
			$pdf->SetXY($pdf->leftmargin, $pdf->GetY() + .2);
			
			
			$this->invoicerecord["processedby"] = $this->invoicerecord["processorfirst"]." ".$this->invoicerecord["processorlast"];
			$pdf->setStyle("normal");
			
			foreach($this->topinfo as $column){
			
				if($column->format != "")
					$value = formatVariable($this->invoicerecord[$column->fieldname], $column->format);
				else
					$value = $this->invoicerecord[$column->fieldname];
				
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
				
				
			if($line["partname"] || $line["partnumber"] || $line["extended"]){
			
				if($pdf->GetY() + 0.17*3 > $coords["y"] + $this->lineitemBoxHeight){
					
					$pdf->SetLineWidth(0.02);
					$pdf->Rect($coords["x"], $coords["y"], $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, $this->lineitemBoxHeight);
					$pdf->SetLineWidth(0.01);
				
					$this->_addPage();
					
					$pdf->setStyle("normal");
					
					$pdf->SetY($pdf->GetY() + 0.18 + 0.0625);
					
				}//end if

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
				
				$pdf->SetX($pdf->leftmargin);				
				$pdf->Write(0.17, "(".$line["partnumber"].")");
				$pdf->Ln();
					
				}//endif

				if($line["memo"]){
				
					$pdf->SetX($pdf->leftmargin + 0.0625);
					$pdf->SetFont("Arial", "I", 8);
					$pdf->MultiCell($this->lineitems[0]->size - 0.0625, 0.16, $line["memo"], $pdf->borderDebug);
					$pdf->setStyle("normal");
					
				}//end if
				
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
				lineitems.*,
				lineitems.quantity * lineitems.unitprice AS extended,
				products.partname,
				products.partnumber
			FROM
				lineitems LEFT JOIN products ON lineitems.productid = products.id
			WHERE
				lineitems.invoiceid =".((int) $this->invoicerecord["id"])."
			ORDER BY
				displayorder";

			$queryresult = $this->db->query($querystatement);
			
			return $queryresult;
		
		}//end method


		function _addNotes(){
		
			$pdf = &$this->pdf;
		
			$height = 1;
			$nextPos = $pdf->GetY() + $height + 0.125;
		
			$pdf->Rect($pdf->GetX(), $pdf->GetY(), $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, $height);
			$pdf->setStyle("header");
			$pdf->Cell($pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, 0.18, "Notes/Instructions", 1, 2, "L", 1);
						
			$pdf->setStyle("normal");
			$pdf->SetXY($pdf->GetX() + .06125, $pdf->GetY() + .06125);
			$pdf->MultiCell($pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin - 0.125, 0.18, $this->invoicerecord["printedinstructions"]);
			
			$pdf->SetXY($pdf->leftmargin, $nextPos);
		
		}//end method
		
		
		function _addTotals(){

			$pdf = &$this->pdf;
		
			$size = 0;
			foreach($this->totalsinfo as $column)
				switch($column->fieldname){
					case "shipping":
					case "discountamount":
						if($this->invoicerecord[$column->fieldname])
							$size += $column->size;
						break;
					default:
						$size += $column->size;
				}//endswitch
			$this->totalsinfo[1]->size = $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin - $size;			
					
			$height = .5;
			$nextPos = $pdf->GetY() + $height + 0.125;
		
			$pdf->Rect($pdf->GetX(), $pdf->GetY(), $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, $height);
			
			$pdf->setStyle("header");
			foreach($this->totalsinfo as $column)
				switch($column->fieldname){
					case "shipping":
					case "discountamount":					
						if($this->invoicerecord[$column->fieldname])
							$pdf->Cell($column->size, 0.18, $column->title, 1, 0, $column->align, 1);
						break;
					default:
						$pdf->Cell($column->size, 0.18, $column->title, 1, 0, $column->align, 1);
				}//endswitch
			
			$pdf->setStyle("normal");
			$pdf->SetFont("Arial", "B", 10);
			$pdf->SetXY($pdf->leftmargin, $pdf->GetY() + 0.18 + 0.0625);
			
			foreach($this->totalsinfo as $column){
			
				if($column->format != "")
					$value = formatVariable($this->invoicerecord[$column->fieldname], $column->format);
				else
					$value = $this->invoicerecord[$column->fieldname];				
			
				switch($column->fieldname){
					case "shipping":
					case "discountamount":
						if($this->invoicerecord[$column->fieldname])
							$pdf->Cell($column->size, 0.18, $value, $pdf->borderDebug, 0, $column->align);
						break;
					default:
						$pdf->Cell($column->size, 0.18, $value, $pdf->borderDebug, 0, $column->align);
				}//endswitch
			}//end foreach
			$this->totalsinfo[1]->size = 0;			
			
			$pdf->SetXY($pdf->leftmargin, $nextPos);			

		}//end method
		
		
		function _addPaymentDetails(){
		}//end method
				

		function output($destination = "screen" , $userinfo = NULL){
	
			switch($destination){
				
				case "screen":
					$this->pdf->Output();
					break;
					
				case "email":
				
					if(!$userinfo)
						$userinfo = $_SESSION["userinfo"];
				
					if(!$userinfo["email"] || !$this->invoicerecord["email"])
						return false;
				
					$pdf = $this->pdf->Output(NULL, "S");
					
					$to = 		$this->invoicerecord["email"];
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
							 " name=\"".$this->title.$this->invoicerecord["id"].".pdf\"\n" . 
							 "Content-Disposition: attachment;\n" . 
							 " filename=\"".$this->title.$this->invoicerecord["id"].".pdf\"\n" . 
							 "Content-Transfer-Encoding: base64\n\n" . 
							 $pdf . "\n\n" . 
							 "--{$mime_boundary}--\n";
					
					return @ mail($to, $subject, $message, $headers);
					
					break;
				
			}//endswitch
		
		}//end method		
	
	}//end class
	


?>