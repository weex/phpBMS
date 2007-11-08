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

	if(!isset($_SESSION["userinfo"]["id"])){
	
		//IE needs caching to be set to private in order to display PDFS
		session_cache_limiter('private');
	
		//set encoding to latin1 (fpdf doesnt like utf8)
		$sqlEncoding = "latin1";	
		require_once("../../../include/session.php");
	
	}//end if
	
	if(!class_exists("invoicePDF"))
		include("invoices_pdf_class.php");
	
	class  packinglistPDF extends invoicePDF{
	
		var $title = "Packing List";
		var $showShipNameInShipTo = false;
				
		function packinglistPDF($db, $orientation='P', $unit='mm', $format='Letter'){
	
			$this->invoicePDF($db, $orientation, $unit, $format);
			
		}//end method
	

		function initialize(){
			//This function will set column headings, sizes and formatting

			$pdf = &$this->pdf;
			
			$topinfo = array();
			$topinfo[] = new pdfColumn("Order ID", "id", 0.75);
			$topinfo[] = new pdfColumn("Order Date", "orderdate", 1, "date");
			$topinfo[] = new pdfColumn("Client PO", "ponumber", 0);
			
			$size = 0;
			foreach($topinfo as $column)
				$size += $column->size;
				
			$topinfo[2]->size = $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin - $size;

			$this->topinfo = $topinfo;			
			
			$lineitems = array();
			$lineitems[] = new pdfColumn("Product / (Part Number)", "parts", 0);
			$lineitems[] = new pdfColumn("Prepackaged", "isprepackaged", 0.75, "boolean", "C");
			$lineitems[] = new pdfColumn("Oversized", "isoversized", 0.75, "boolean", "C");
			$lineitems[] = new pdfColumn("Unit Weight", "unitweight", 0.75, "real", "R");
			$lineitems[] = new pdfColumn("Qty", "quantity", 0.5, "real","R");
			$lineitems[] = new pdfColumn("Weight Ext.", "extended", 0.75, "real", "R");
						
			$size = 0;
			foreach($lineitems as $column)
				$size += $column->size;
				
			$lineitems[0]->size = $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin - $size;
			
			$this->lineitems = $lineitems;
			
			$totalsinfo = array();
			$totalsinfo[] = new pdfColumn("Shipping Method", "shippingname", 0);
			$totalsinfo[] = new pdfColumn("Estimated Boxes", "estimatedboxes", 1, NULL, "C");
			$totalsinfo[] = new pdfColumn("Total Weight", "totalweight", 1, "real", "R");
			$totalsinfo[] = new pdfColumn("Shipping", "shipping", 1, "currency", "R");
									
			$size = 0;
			foreach($totalsinfo as $column)
				$size += $column->size;
				
			$totalsinfo[0]->size = $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin - $size;

			$this->totalsinfo = $totalsinfo;

		}//end method

			
		function _addNotes(){
		
			$pdf = &$this->pdf;
		
			$height = 1;
			$nextPos = $pdf->GetY() + $height + 0.125;
		
			$pdf->Rect($pdf->GetX(), $pdf->GetY(), $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, $height);
			$pdf->setStyle("header");
			$pdf->Cell($pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, 0.18, "Special Instructions", 1, 2, "L", 1);
						
			$pdf->setStyle("normal");
			$pdf->SetXY($pdf->GetX() + .06125, $pdf->GetY() + .06125);
			$pdf->MultiCell($pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin - 0.125, 0.18, $this->invoicerecord["specialinstructions"]);
			
			$pdf->SetXY($pdf->leftmargin, $nextPos);
		
		}//end method
		
		
		function _getLineItems(){
		
			$querystatement = "
			SELECT
				lineitems.*,
				lineitems.quantity*lineitems.unitweight as extended,
				products.partname,
				products.partnumber,
				products.isoversized,
				products.isprepackaged,
				products.packagesperitem
			FROM
				lineitems LEFT JOIN products ON lineitems.productid = products.id
			WHERE
				lineitems.invoiceid =".((int) $this->invoicerecord["id"])."
			ORDER BY
				displayorder;

			$queryresult = $this->db->query($querystatement);
			
			//determine estimated total boxes
			$this->invoicerecord["estimatedboxes"] = 0;
			while($therecord = $this->db->fetchArray($queryresult)){
			
				if($therecord["isprepackaged"])
					$this->invoicerecord["estimatedboxes"] += $therecord["quantity"];
				else
					$this->invoicerecord["estimatedboxes"] += $therecord["quantity"] * $therecord["packagesperitem"];
				
			}//endwhile
			
			$this->db->seek($queryresult, 0);
			
			return $queryresult;
		
		}//end method
		
		function _addTotals(){

			$pdf = &$this->pdf;
							
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
					$value = formatVariable($this->invoicerecord[$column->fieldname], $column->format);
				else
					$value = $this->invoicerecord[$column->fieldname];				
			
				$pdf->Cell($column->size, 0.18, $value, $pdf->borderDebug, 0, $column->align);

			}//end foreach
					
		}//end method

	}//end class


	//PROCESSING
	//=============================================================================
	if(!isset($noOutput)){
			
		$report = new packinglistPDF($db, 'P', 'in', 'Letter');
		
		$report->setupFromPrintScreen();
		$report->generate();
		$report->output();
		
	}//end if
?>