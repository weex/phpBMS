<?php
/*
 $Rev: 290 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-27 18:15:00 -0600 (Mon, 27 Aug 2007) $
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

if(!class_exists("phpbmsReport"))
    include("../../../report/report_class.php");

class invoicePDF extends phpbmsReport{

    var $showShipNameInShipTo = true;
    var $lineitemBoxHeight = 4.25;
    var $templateUUID = NULL;

    /**
      * $count
      * @var int The number of invoice records being displayed
      */
    var $count;


    function invoicePDF($db, $reportUUID, $tabledefUUID, $orientation='P', $unit='mm', $format='Letter'){

        parent::phpbmsReport($db, $reportUUID, $tabledefUUID);

        if(!class_exists("phpbmsPDFReport"))
            include("report/pdfreport_class.php");

        $this->pdf = new phpbmsPDFReport($db, $orientation, $unit, $format);

        $this->checkForDefaultSettings();
        $this->initialize();

    }//end method


    /**
     * function checkForDefaultSettings
     *
     * Checks to make sure loaded report Settings exist and are correct
     */
    function checkForDefaultSettings(){

        if(!isset($this->settings["reportTitle"]))
            $this->settings["reportTitle"] = "Invoice";

        if(!isset($this->settings["printLogo"]))
            $this->settings["printLogo"] = 1;

        if(!isset($this->settings["printCompanyInfo"]))
            $this->settings["printCompanyInfo"] = 1;

        if(!isset($this->settings["leftTopBox"]))
            $this->settings["leftTopBox"] = "billto";

        if(!isset($this->settings["leftTopBoxTitle"]))
            $this->settings["leftTopBoxTitle"] = "SOLD TO";

        if(!isset($this->settings["rightTopBox"]))
            $this->settings["rightTopBox"] = "shipto";

        if(!isset($this->settings["rightTopBoxTitle"]))
            $this->settings["rightTopBoxTitle"] = "SHIP TO";

        if(!isset($this->settings["templateFormatting"]))
            $this->settings["templateFormatting"] = 0;

        if(!isset($this->settings["templateUUID"]))
            $this->settings["templateUUID"] = "";

    }//end function checkForDefaultSettings


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
                    $this->whereClause = $whereclause;
            elseif(!$this->whereClause)
                    $this->whereClause = "invoices.id = -400";

            if($sortorder)
                    $this->sortOrder = $sortorder;
            elseif(!$this->sortOrder)
                    $this->sortOrder = "invoices.id";

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
                            invoices.*,
                            ".$paymentFields."

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
                            invoices INNER JOIN clients ON invoices.clientid = clients.uuid
                            INNER JOIN users ON invoices.modifiedby = users.id
                            LEFT JOIN shippingmethods ON invoices.shippingmethodid = shippingmethods.uuid
                            LEFT JOIN paymentmethods ON invoices.paymentmethodid = paymentmethods.uuid
                            LEFT JOIN tax ON invoices.taxareaid = tax.uuid";

            $querystatement = $this->assembleSQL($querystatement);

            $queryresult = $this->db->query($querystatement);

            $this->count = $this->db->numRows($queryresult);
            if($this->count == 0){

                    $this->showNoRecords();
                    exit;

            }//end if

            $pdf->logoInHeader = $this->settings["printLogo"];
            $pdf->companyInfoInHeader = $this->settings["printCompanyInfo"];

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

            if($this->settings["templateUUID"]){

                if(!isset($GLOBALS["pdfDoc"])){

                    $querystatement = "
                        SELECT
                            `file`
                        FROM
                            `files`
                        WHERE
                            `uuid` = '".$this->settings["templateUUID"]."'";

                    $queryresult = $this->db->query($querystatement);

                    $therecord = $this->db->fetchArray($queryresult);

                    $GLOBALS["pdfDoc"] = $therecord["file"];
                    $pdf->setSourceFile("global://pdfDoc");
                    $this->tplIdx = $pdf->importPage(1);

                }//endif

                $pdf->useTemplate($this->tplIdx);

            }//endif


            $this->page++;

            $nextY = $pdf->getY();

            //TITLE
            $title = "Statement";
            $titleWidth=2.375;
            $titleHeight=.25;
            $pdf->setStyle("title");
            $pdf->SetXY(-1*($titleWidth+$pdf->rightmargin), $pdf->topmargin);
            $pdf->Cell($titleWidth, $titleHeight, $this->settings["reportTitle"], $pdf->borderDebug,1,"R");

            $startY = $pdf->GetY() + 0.75;

            //page number?
            $pdf->setStyle("normal");
            $pageNoWidth = 1;
            $pdf->SetFontSize(8);
            $pdf->SetXY(-1*($pageNoWidth + $pdf->rightmargin), $pdf->topmargin + $titleHeight + 0.25);
            $pdf->Cell($pageNoWidth, 0.17, "page: ".$this->page, $pdf->borderDebug,1,"R");

            $boxHeight = 1.75;
            $boxWidth = ($pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin)/2 -0.0625;

            //Left Top Box
            $this->_addTopBox($this->settings["leftTopBox"], $this->settings["leftTopBoxTitle"], $pdf->leftmargin, $startY, $boxWidth, $boxHeight);

            //Right Top Box
            $this->_addTopBox($this->settings["rightTopBox"], $this->settings["rightTopBoxTitle"], $pdf->leftmargin + $boxWidth + 0.125, $startY, $boxWidth, $boxHeight);

            $pdf->setXY($pdf->leftmargin, $startY + $boxHeight + 0.125);

            $this->_topInvoiceInfo();


            $coords["x"] = $pdf->GetX();
            $coords["y"] = $pdf->GetY();

            if(!$this->settings["templateFormatting"]){

                //line item headings
                $pdf->setStyle("header");
                $pdf->SetLineWidth(0.02);

                foreach($this->lineitems as $column)
                    $pdf->Cell($column->size, 0.18, $column->title, 1, 0, $column->align, 1);

            }//endif

            return $coords;

    }//end method


    function _addTopBox($areaToPrint, $title, $x, $y, $boxWidth, $boxHeight){

        if($areaToPrint != "noshow"){

            $pdf = &$this->pdf;

            if(!$this->settings["templateFormatting"]){

                $pdf->setLineWidth(0.02);
                $pdf->Rect($x, $y, $boxWidth, $boxHeight);
                $pdf->setLineWidth(0.01);

                $pdf->setStyle("header");
                $pdf->setXY($x, $y);
                $pdf->Cell($boxWidth, 0.17, $title, $pdf->borderDebug, 2, "L", 1);
                $pdf->setStyle("normal");

            } else
                $pdf->SetXY($x ,$y + 0.17);

            $pdf->setXY($pdf->GetX(), $pdf->GetY() + 0.0625);

            $companyDisplay = "";

            if($this->invoicerecord["company"]){

                $companyDisplay .= $this->invoicerecord["company"];
                if($this->invoicerecord["firstname"])
                        $companyDisplay .= " (".$this->invoicerecord["firstname"]." ".$this->invoicerecord["lastname"].")";

            } else
                $companyDisplay .= $this->invoicerecord["firstname"]." ".$this->invoicerecord["lastname"];

            switch($areaToPrint){


                case "billto":

                    $pdf->SetXY($pdf->GetX() + 0.0625, $pdf->GetY() + 0.0625);
                    $pdf->SetFont("Arial", "B", 10);
                    $pdf->Cell($boxWidth - 0.125, 0.17, $companyDisplay, $pdf->borderDebug, 2, "L");

                    $billto = $this->_setBillTo();
                    $pdf->SetFont("Arial", "", 10);
                    $pdf->setXY($pdf->GetX(), $pdf->GetY() + 0.0625);
                    $pdf->MultiCell($boxWidth - 0.125,.17,$billto, $pdf->borderDebug);
                    break;

                case "shipto":

                    $pdf->SetXY($pdf->GetX() + 0.0625, $pdf->GetY() + 0.0625);
                    $pdf->SetFont("Arial", "B", 10);

                    $shipDisplay = (!$this->invoicerecord["shiptosameasbilling"] && $this->invoicerecord["shiptoname"])? $this->invoicerecord["shiptoname"] :$companyDisplay;
                    $pdf->Cell($boxWidth - 0.125, 0.17, $shipDisplay, $pdf->borderDebug, 2, "L");

                    $shipto = $this->_setShipTo();
                    $pdf->SetFont("Arial", "", 10);
                    $pdf->setXY($pdf->GetX(), $pdf->GetY() + 0.0625);
                    $pdf->MultiCell($boxWidth - 0.125,.17, $shipto, $pdf->borderDebug);
                    break;

                case "companyinfo":

                    $cname = COMPANY_NAME;
                    $caddress = COMPANY_ADDRESS."\n".COMPANY_CSZ."\n".COMPANY_PHONE;

                    $pdf->SetXY($pdf->GetX() + 0.0625, $pdf->GetY() + 0.0625);
                    $pdf->SetFont("Arial","B",10);
                    $pdf->Cell($boxWidth - 0.125, 0.17, $cname, $pdf->borderDebug, 2, "L");

                    //and last, company address
                    $pdf->setXY($pdf->GetX(), $pdf->GetY() + 0.0625);
                    $pdf->SetFont("Arial", "", 10);
                    $pdf->MultiCell($boxWidth - 0.125,.17 , $caddress, $pdf->borderDebug);
                    break;

                case "invoiceinfo":

                    $pdf->SetXY($pdf->GetX() + 0.0625, $pdf->GetY() + 0.0625);
                    $pdf->SetFont("Arial","B",14);
                    $pdf->Cell($boxWidth - 0.125, 0.25, $this->invoicerecord["id"], $pdf->borderDebug, 2, "R");


                    $details = "payment method\n".$this->invoicerecord["paymentname"];
                    $pdf->setXY($pdf->GetX(), $pdf->GetY() + 0.125);
                    $pdf->SetFont("Arial", "", 8);
                    $pdf->MultiCell($boxWidth - 0.125,.17, $details, $pdf->borderDebug, "R");

                    break;

            }//endswitch

        }//endif

    }//end function _addTopBox


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

            if(!$this->settings["templateFormatting"]){

                $pdf->setStyle("header");
                $pdf->SetLineWidth(0.02);

                foreach($this->topinfo as $column)
                        $pdf->Cell($column->size, 0.18, $column->title, 1, 0, $column->align, 1);

                $pdf->Rect($pdf->leftmargin, $pdf->GetY(), ($pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin), 0.39);

            }//endif

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

                        if(!$this->settings["templateFormatting"]){

                            $pdf->SetLineWidth(0.02);
                            $pdf->Rect($coords["x"], $coords["y"], $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, $this->lineitemBoxHeight);
                            $pdf->SetLineWidth(0.01);

                        }//endif

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

                if(!$this->settings["templateFormatting"]){

                    $pdf->SetXY($pdf->leftmargin, $pdf->GetY() + 0.0625);
                    $pdf->SetLineWidth(0.01);
                    $pdf->SetDrawColor(180,180,180);
                    $pdf->Line($pdf->leftmargin, $pdf->GetY(), $pdf->paperwidth - $pdf->rightmargin, $pdf->GetY());
                    $pdf->SetDrawColor(0,0,0);
                    $pdf->SetLineWidth(0.02);

                }//endif

                $pdf->SetXY($pdf->leftmargin, $pdf->GetY() + 0.0625);

            }//end while

            if(!$this->settings["templateFormatting"]){

                $pdf->SetLineWidth(0.02);
                $pdf->Rect($coords["x"], $coords["y"], $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, $this->lineitemBoxHeight);
                $pdf->SetLineWidth(0.01);

            }//endif

    }//end method


    function _getLineItems(){

            $querystatement = "
            SELECT
                    lineitems.*,
                    lineitems.quantity * lineitems.unitprice AS extended,
                    products.partname,
                    products.partnumber
            FROM
                    lineitems LEFT JOIN products ON lineitems.productid = products.uuid
            WHERE
                    lineitems.invoiceid ='".((int) $this->invoicerecord["id"])."'
            ORDER BY
                    displayorder";

            $queryresult = $this->db->query($querystatement);

            return $queryresult;

    }//end method


    function _addNotes(){

            $pdf = &$this->pdf;

            $height = 1;
            $nextPos = $pdf->GetY() + $height + 0.125;

            if(!$this->settings["templateFormatting"]){

                $pdf->Rect($pdf->GetX(), $pdf->GetY(), $pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, $height);
                $pdf->setStyle("header");
                $pdf->Cell($pdf->paperwidth - $pdf->leftmargin - $pdf->rightmargin, 0.18, "Notes/Instructions", 1, 2, "L", 1);
                $pdf->setStyle("normal");

            } else
                $pdf->SetY($pdf->GetY() + 0.18);

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

            if(!$this->settings["templateFormatting"]){

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

            }//endif

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
                            $userinfo = cleanFilename((string)$userinfo);
                            $this->pdf->Output($userinfo, 'D');
                            break;

                    case "email":

                            if(!$userinfo)
                                    $userinfo = $_SESSION["userinfo"];

                            if(!$userinfo["email"] || !$this->invoicerecord["email"])
                                    return false;

                            $to          = $this->invoicerecord["email"];
                            $toName      = $this->invoicerecord["firstname"]." ".$this->invoicerecord["lastname"];
                            $from        = $userinfo["email"];
                            $fromName    = $userinfo["firstname"]." ".$userinfo["lastname"];
                            $subject     = "Your ".$this->settings["reportTitle"]." (".$this->invoicerecord["id"].") from ".COMPANY_NAME;
                            $pdf         = $this->pdf->Output(NULL, "S");
                            $filename    = $this->settings["reportTitle"]."_".$this->invoicerecord["id"].".pdf";
                            $mailer      = $userinfo["mailer"];
                            $sendmail    = $userinfo["sendmail"];
                            $smtpauth    = $userinfo["smtpauth"];
                            $smtpsecure  = $userinfo["smtpsecure"];
                            $smtpport    = $userinfo["smtpport"];
                            $smtpuser    = $userinfo["smtpuser"];
                            $smtppass    = $userinfo["smtppass"];
                            $smtphost    = $userinfo["smtphost"];
                            $messageTXT  =  'Dear Client,\n\n'.
                                            'Attached is your '.$this->settings["reportTitle"].' from '.COMPANY_NAME.'\n\n'.
                                            'The attachment requires Adobe Acrobat Reader to view.\n'.
                                            'If you do not have Acrobat Reader, you can download it from http://www.adobe.com\n\n'.
                                            'Kind Regards,\n'.COMPANY_NAME;
                            $messageHTML =  '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'.
                                            '<html>'.
                                            '    <head>'.
                                            '        <title>'.$subject.'</title>'.
                                            '        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'.
                                            '    </head>'.
                                            '    <body>'.
                                            '        <p>'.
                                            '        Dear Client,</br>'.
                                            '        </br>'.
                                            '        Attached is your '.$this->settings["reportTitle"].' from '.COMPANY_NAME.'</br>'.
                                            '        </br>'.
                                            '        The attachment requires Adobe Acrobat Reader to view.</br>'.
                                            '        If you do not have Acrobat Reader, you can download it from <a href="http://www.adobe.com">Adobe</a></br>'.
                                            '        </br>'.
                                            '        Kind Regards,</br>'.
                                            '        '.COMPANY_NAME.'</br>'.
                                            '        </p>'.
                                            '    </body>'.
                                            '</html>';
 										
							// Catch Exceptions
							try {
								
								require_once("swift-mailer/lib/swift_required.php");
								
								// Create the Transport
								if ($mailer == "sendmail") {
									$transport = Swift_SendmailTransport::newInstance($sendmail); // Sendmail
								} elseif ($mailer == "smtp") {
									$transport = Swift_SmtpTransport::newInstance(); // SMTP
									$transport->setHost($smtphost);
									$transport->setPort($smtpport);
									if ($smtpauth) {
										$transport->setUsername($smtpuser);
										$transport->setPassword($smtppass);
									}
									if ($smtpsecure != "none") {
										$transport->setEncryption($smtpsecure);
									}
								} else {
									$transport = Swift_MailTransport::newInstance(); // Mail
								}
								
								// Create the Mailer using your created Transport
								$mailer = Swift_Mailer::newInstance($transport);
								
								// Create the message
								$message = Swift_Message::newInstance();
								$message->setSubject($subject); // Give the message a subject
								$message->setFrom(array($from => $fromName)); // Set the From address with an associative array
								$message->setTo(array($to => $toName)); // Set the To addresses with an associative array
								$message->setBody($messageHTML, 'text/html'); // Give it a body
								$message->addPart($messageTXT, 'text/plain'); // And optionally an alternative body
								
								// Create the attachment with the pdf data
								$attachment = Swift_Attachment::newInstance($pdf, $filename, 'application/pdf');
								$message->attach($attachment); // Attach it to the message
								
								// Send the message
								 $result = $mailer->send($message);
								
							// Handle Exceptions
							} catch (Exception $ex) {
							
								echo '<label style="padding:4px 6px 4px 6px;"><b>Exception captured for (<u>'.$this->invoicerecord["id"].'</u>) : </b>'.$ex->getMessage().'</label></br>';
								
								$result = FALSE;
								
							}
							
							return $result;
							
                            break;
							
            }//endswitch

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

        $settings[] = array(
            "name"=>"reportTitle",
            "defaultValue"=>"Invoice",
            "type"=>"string",
            "required"=>1,
            "description"=>"Title printed on reports"
        );

        $settings[] = array(
            "name"=>"printLogo",
            "defaultValue"=>1,
            "type"=>"bool",
            "required"=>1,
            "description"=>"Should the logo print (1 = yes, 0 = no)"
        );

        $settings[] = array(
            "name"=>"printCompanyInfo",
            "defaultValue"=>1,
            "type"=>"bool",
            "required"=>1,
            "description"=>"Should the top company information print (1 = yes, 0 = no)"
        );

        $settings[] = array(
            "name"=>"leftTopBox",
            "defaultValue"=>"billto",
            "type"=>"string",
            "required"=>1,
            "description"=>"Contents of Right Top Header Box (can be `billto`, `shipto`, `invoiceinfo`, `companyinfo`, `nowshow` or `blank`)"
        );

        $settings[] = array(
            "name"=>"leftTopBoxTitle",
            "defaultValue"=>"SOLD TO",
            "type"=>"string",
            "required"=>1,
            "description"=>"Title of Left Top Header Box"
        );

        $settings[] = array(
            "name"=>"rightTopBox",
            "defaultValue"=>"shipto",
            "type"=>"string",
            "required"=>1,
            "description"=>"Contents of Right Top Header Box (can be `billto`, `shipto`, `invoiceinfo`, `companyinfo`, `nowshow` or `blank`)"
        );

        $settings[] = array(
            "name"=>"rightTopBoxTitle",
            "defaultValue"=>"SHIP TO",
            "type"=>"string",
            "required"=>1,
            "description"=>"Title of Right Top Header Box"
        );

        $settings[] = array(
            "name"=>"templateFormatting",
            "defaultValue"=>"0",
            "type"=>"bool",
            "required"=>1,
            "description"=>"Should PDF remove lines and dark titles (1 = remove, 0 = keep)"
        );

        $settings[] = array(
            "name"=>"templateUUID",
            "defaultValue"=>"",
            "type"=>"string",
            "required"=>0,
            "description"=>"Optional UUID of file record for PDF to be used as background template"
        );

        return $settings;

    }//endfunction addingRecordDefaultSettings

}//end class



?>
