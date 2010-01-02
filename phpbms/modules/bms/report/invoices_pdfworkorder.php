<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
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
if(!class_exists("invoicePDF"))
    include("invoices_pdf_class.php");

class  workorderPDF extends invoicePDF{

    var $lineitemBoxHeight = 3.75;


    function workorderPDF($db, $reportUUID, $tabledefUUID, $orientation='P', $unit='mm', $format='Letter'){

        $this->invoicePDF($db, $reportUUID, $tabledefUUID, $orientation, $unit, $format);

    }//end method


    /**
     * function checkForDefaultSettings
     *
     * Checks to make sure loaded report Settings exist and are correct
     */
    function checkForDefaultSettings(){

        if(!isset($this->settings["reportTitle"]))
            $this->settings["reportTitle"] = "Work Order";

        parent::checkForDefaultSettings();

    }//end function checkForDefaultSettings


    function _addNotes(){

            $pdf = &$this->pdf;

            $height = 1;
            $nextPos = $pdf->GetY() + $height + 0.125;

            if(!$this->settings["templateFormatting"]){

                $pdf->Rect($pdf->GetX(), $pdf->GetY(), $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, $height);
                $pdf->setStyle("header");
                $pdf->Cell($pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, 0.18, "Special Instructions", 1, 2, "L", 1);

            }else
                $pdf->SetY($pdf->GetY() + 0.18);

            $pdf->setStyle("normal");
            $pdf->SetXY($pdf->GetX() + .06125, $pdf->GetY() + .06125);
            $pdf->MultiCell($pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin - 0.125, 0.18, $this->invoicerecord["specialinstructions"]);

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

            if(!$this->settings["templateFormatting"]){

                $pdf->Rect($pdf->GetX(), $pdf->GetY(), $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, $height);

                $pdf->setStyle("header");

                foreach($columns as $column)
                    $pdf->Cell($column->size, 0.18, $column->title, 1, 0, $column->align, 1);

            }//endif

            $pdf->SetXY($pdf->leftmargin, $pdf->GetY() + 0.18 + 0.0625);

            $pdf->setStyle("normal");
            $pdf->SetFont("Arial", "B", 10);
            foreach($columns as $column)
                    $pdf->Cell($column->size, 0.18, $this->invoicerecord[$column->fieldname], $pdf->borderDebug, 0, $column->align);

    }//end method

    /**
     * function addingRecordDefaultSettings
     *
     * Creates an array of settings associative arrays for use by the system when
     * a new report record is added that references the file containing this class
     *
     * @retrun array of settings. Each setting should itself be
     * an associative array containing the following
     * name: name of the setting
     * defaultvalue: default value for setting
     * type: (string, int, real, bool) type for value of setting
     * required: (0,1) whether the setting is required or not
     * description: brief description for what this setting is used for.
     */
    function addingRecordDefaultSettings(){

        $settings = parent::addingRecordDefaultSettings();

        for($i=0; $i< count($settings); $i++){

            switch($settings[$i]["name"]){

                case "reportTitle":
                    $settings[$i]["defaultValue"] = "Work Order";

            }//endswitch

        }//end foreach

        return $settings;

    }//endfunction addingRecordDefaultSettings

}//end class


/**
 * PROCESSING
 * =============================================================================
 */
if(!isset($noOutput)){

    //IE needs caching to be set to private in order to display PDFS
    session_cache_limiter('private');

    //set encoding to latin1 (fpdf doesnt like utf8)
    $sqlEncoding = "latin1";
    require_once("../../../include/session.php");

    checkForReportArguments();

    $report = new workorderPDF($db, $_GET["rid"], $_GET["tid"], 'P', 'in', 'Letter');

    $report->setupFromPrintScreen();
    $report->generate();

    $filename = 'Work_Order';

    if($report->count === 1){

        if($report->invoicerecord["company"])
            $filename .= "_".$report->invoicerecord["company"];

        $filename .= "_".$report->invoicerecord["id"];

    }elseif((int)$report->count)
        $filename .= "_Multiple";

    $filename .= ".pdf";

    $report->output('screen', $filename);

}//end if

/**
 * When adding a new report record, the add/edit needs to know what the class
 * name is so that it can instantiate it, and grab it's default settings.
 */
if(isset($addingReportRecord))
    $reportClass ="workorderPDF";
?>
