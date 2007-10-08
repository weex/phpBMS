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
	
		//set encoding to latin1 (fpdf doesnt like utf8)
		$sqlEncoding = "latin1";	
		require_once("../../../include/session.php");
	
	}//end if
	
	if(!class_exists("invoicePDF"))
		include("invoices_pdf_class.php");
	
	class  workorderPDF extends invoicePDF{
	
		var $title = "Work Order";
		var $lineitemBoxHeight = 3.75;
		
		function workorderPDF($db, $orientation='P', $unit='mm', $format='Letter'){
	
			$this->invoicePDF($db, $orientation, $unit, $format);
			
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
			$pdf->Cell($pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin - 0.125, 0.18, $this->invoicerecord["specialinstructions"]);
			
			$pdf->SetXY($pdf->leftmargin, $nextPos);
		
		}//end method
		
		function _addPaymentDetails(){

			$pdf = &$this->pdf;

			$columns = array();
			$columns[] = new pdfColumn("Payment Method", "paymentname", 0);

			switch($this->invoicerecord["paymenttype"]){
			
				case "draft":
					$columns[0]->size = 1.5;
					$columns[] = new pdfColumn("Check Number", "checkno", 1);
					$columns[] = new pdfColumn("Bank Name", "bankname", 2);
					break;

				case "charge":
					$columns[0]->size = 1.5;
					$columns[] = new pdfColumn("Number", "ccnumber", 1.5);
					$columns[] = new pdfColumn("Exp.", "ccexpiration", 1);
					$columns[] = new pdfColumn("Verification/Pin", "ccverification", 1);
					break;					
			
			}//end switch
						
			$size = 0;
			foreach($columns as $column)
				$size += $column->size;
							
			$i = count($columns) -1;
				
			$columns[$i]->size += $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin - $size;
			
			$height = 0.5;
			$nextPos = $pdf->GetY() + $height + 0.125;
		
			$pdf->Rect($pdf->GetX(), $pdf->GetY(), $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, $height);
			
			$pdf->setStyle("header");

			foreach($columns as $column)
				$pdf->Cell($column->size, 0.18, $column->title, 1, 0, $column->align, 1);
				
			$pdf->SetXY($pdf->leftmargin, $pdf->GetY() + 0.18 + 0.0625);
			
			$pdf->setStyle("normal");
			$pdf->SetFont("Arial", "B", 10);
			foreach($columns as $column)
				$pdf->Cell($column->size, 0.18, $this->invoicerecord[$column->fieldname], $pdf->borderDebug, 0, $column->align);

		}//end method
		
	}//end class


//PROCESSING
//=============================================================================
if(!isset($noOutput)){
		
	$report = new workorderPDF($db, 'P', 'in', 'Letter');
	
	$report->setupFromPrintScreen();
	$report->generate();
	$report->output();
	
}//end if

?>
