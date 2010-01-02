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
class pdfColumn{

    var $title;
    var $fieldname;
    var $size = 1;
    var $format;
    var $align = "L";

    function pdfColumn($title, $fieldname, $size = 1, $format = "", $align = "L"){

        $this->title = $title;
        $this->fieldname = $fieldname;
        $this->size = ((real) $size);
        $this->format = $format;
        $this->align = $align;

    }//end method

}//end class


class pdfColor{

    var $r = 0;
    var $g = 0;
    var $b = 0;

    function pdfColor($r = 0,$g = 0,$b = 0){

        $this->r = $r;
        $this->g = $g;
        $this->b = $b;

    }//end method

}//end class


class pdfFont {

    var $family = "Arial";
    var $style = "";
    var $size = 8;

    function pdfFont($family = "Arial", $style ="", $size = 8){

        $this->family = $family;
        $this->style = $style;
        $this->size = $size;

    }//end method

}//end class


class pdfStyle{

    var $font = NULL;
    var $textColor = NULL;
    var $backgroundColor = NULL;

    function pdfStyle($font = NULL, $textColor = NULL, $backgroundColor = NULL){

        if($font) $this->font = $font;
        if($textColor) $this->textColor = $textColor;
        if($backgroundColor) $this->backgroundColor = $backgroundColor;

    }//end method

}//end class


if(!class_exists("FPDI")){

    require_once("fpdf/fpdf.php");
    require_once("fpdf/tpl_and_memimage.php");
    require_once("fpdf/fpdi.php");
    require_once('fpdf/fpdi_pdf_parser.php');

}//end if


class phpbmsPDFReport extends FPDI {

    var $borderDebug = 0;

    var $leftmargin = 0.5;
    var $rightmargin = 0.5;
    var $topmargin = 0.75;
    var $paperwidth = 8.5;
    var $paperlength = 11;

    var $logoInHeader = false;
    var $companyInfoInHeader = false;

    var $companyImageWidth = 1;

    var $styles = array();


    function phpbmsPDFReport($db, $orientation='P', $unit='mm', $format='Letter'){

            $this->db = $db;

            parent::FPDF_TPL($orientation, $unit, $format);

            $this->initStyles();
            $this->SetLineWidth(0.01);

    }//end method


    function initStyles(){

            //here we set the standard styles

            // NORMAL
            $font = new pdfFont("Arial", "", 8);
            $style = new pdfStyle($font);

            $this->styles["normal"] = $style;


            // TITLES
            $font = new pdfFont("Arial", "B", 16);
            $style = new pdfStyle($font);

            $this->styles["title"] = $style;


            // HEADER
            $font = new pdfFont("Arial", "B", 8);
            $bgC = new pdfColor(0,0,0);
            $txtC = new pdfColor(255,255,255);
            $style = new pdfStyle($font, $txtC, $bgC);

            $this->styles["header"] = $style;

    }//end method


    function defineStyle($name, $pdfStyleObj){

            if(get_class($pdfStyleObj) != "pdfStyle")
                    $error = new appError(1400,"defineStyle Method needs pdfStyle object as parameter 2","PDF Error",true,true,false);

            $this->styles[$name] = $pdfStyleObj;

    }//end if


    function setStyle($name){

            if(!isset($this->styles[$name]))
                    $name = "normal";

            $newStyle = $this->styles[$name];

            if(isset($newStyle->font))
                    $this->SetFont($newStyle->font->family, $newStyle->font->style, $newStyle->font->size);
            else
                    $this->SetFont("Arial", "", 8);

            if(isset($newStyle->textColor))
                    $this->SetTextColor($newStyle->textColor->r, $newStyle->textColor->g, $newStyle->textColor->b);
            else
                    $this->SetTextColor(0,0,0);

            if(isset($newStyle->backgroundColor))
                    $this->SetFillColor($newStyle->backgroundColor->r, $newStyle->backgroundColor->g, $newStyle->backgroundColor->b);
            else
                    $this->SetFillColor(255,255,255);

    }//end if


    function SetMargins(){

            parent::SetMargins($this->leftmargin, $this->topmargin, $this->rightmargin);

    }//end method


    function Header(){

        if($this->logoInHeader){

            $querystatement = "
                SELECT
                    `file`,
                    UPPER(`type`) AS `type`
                FROM
                    files
                WHERE
                    id=1";

            $pictureresult = $this->db->query($querystatement);

            $thepicture = $this->db->fetchArray($pictureresult);

            if($thepicture["type"]=="IMAGE/JPEG"){

                global $image;
                $image = $thepicture["file"];
                $this->Image('var://image', $this->leftmargin,$this->topmargin, $this->companyImageWidth, 0, "JPEG");

            } elseif($thepicture["type"]=="IMAGE/PNG")
                $this->MemImage($thepicture["file"], $this->leftmargin, $this->topmargin, $this->companyImageWidth);

        }//end if

        if($this->companyInfoInHeader){

            $cname = COMPANY_NAME;
            $caddress = COMPANY_ADDRESS."\n".COMPANY_CSZ."\n".COMPANY_PHONE;

            //company name
            $width = $this->leftmargin;
            if($this->logoInHeader)
                $width += $this->companyImageWidth;

            $this->SetXY($width, $this->topmargin);
            $this->SetFont("Times","B",12);
            $this->Cell(4, 0.25, $cname, $this->borderDebug, 2, "L");

            //and last, company address
            $this->SetFont("Times","",8);
            $this->MultiCell(4, .125 , $caddress, $this->borderDebug);

        }//end if

    }//end method

}//end class

?>
