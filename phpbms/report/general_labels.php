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

if(!class_exists("phpbmsReport"))
    include("report_class.php");

/**
 * Handles Label Printing
 */
class pdfLabels extends phpbmsReport{

    /**
     * $maintable
     * @var string the SQL name of the main table to print.
     */
    var $maintable = "";

    /**
     * $skipLabels
     * @var int number of lables to skip before printing.
     */
    var $skipLabels = 0;


    /**
     * function pdfLabels
     *
     * initialization function
     */
    function pdfLabels($db, $reportUUID, $tabledefUUID){

        parent::phpbmsReport($db, $reportUUID, $tabledefUUID);

        $therecord = $this->getTableDefInfo();

        $this->maintable = $therecord["maintable"];

        $this->checkForDefaultSettings();

    }//end function init


    /**
     * function checkForDefaultSettings
     *
     * Checks to make sure loaded report Settings exist and are correct
     */
    function checkForDefaultSettings(){

        if(!isset($this->settings["maxRows"]))
            $this->settings["maxRows"] = 10;

        if(!isset($this->settings["maxColumns"]))
            $this->settings["maxColumns"] = 3;

        if(!isset($this->settings["columnMargin"]))
            $this->settings["columnMargin"] = 1/8;

        if(!isset($this->settings["labelHeight"]))
            $this->settings["labelHeight"] = 1;

        if(!isset($this->settings["labelWidth"]))
            $this->settings["labelWidth"] = 2 + (5/8);

        if(!isset($this->settings["startTop"]))
            $this->settings["startTop"] = 1/2;

        if(!isset($this->settings["startLeft"]))
            $this->settings["startLeft"] = 3/16;

        if(!isset($this->settings["borderDebug"]))
            $this->settings["borderDebug"] = 0;

        if(!isset($this->settings["queryStatement"]))
            $this->settings["queryStatement"] = 'SELECT "no data in first row" AS `rowText1`,  "no data in second row" AS `rowText2` FROM `'.$this->maintable.'`';

        if(!isset($this->settings["defaultSortOrder"]))
            $this->settings["defaultSortOrder"] = '';

        if(!isset($this->settings["fileName"]))
            $this->settings["fileName"] = $this->maintable.'-labels.pdf';

        if(!isset($this->settings["labelMarginTop"]))
            $this->settings["labelMarginTop"] = 1/8;

        if(!isset($this->settings["labelMarginLeft"]))
            $this->settings["labelMarginLeft"] = 1/16;

        if(!isset($this->settings["rowText1Font"]))
            $this->settings["rowText1Font"] = "Arial,B,9";

    }//end function checkForDefaultSettings


    /**
     * function siplaySkipLabels
     *
     * Displays dialog so that people can skip labels when printing
     */
    function displaySkipLabels(){

        global $phpbms;

	$pageTitle = "Label Options";
	$phpbms->showMenu = false;
	$phpbms->cssIncludes[] = "pages/historyreports.css";
	include("header.php");

        ?>
        <form action="<?php echo htmlentities($_SERVER["REQUEST_URI"])?>" method="post" name="print_form">
            <div class="bodyline" id="reportOptions">

                <h1 id="topTitle"><span>Label Options</span></h1>

                <p>
                    <label for="skipLabels">skip first labels</label><br />
                    <input name="skipLabels" id="skipLabels" value="0" size="3" maxlength="3" />
                </p>
                <p align="right">
                    <input name="command" type="submit" class="Buttons" id="print" value="print" />
                    <input name="cancel" type="button" class="Buttons" id="cancel" value="cancel" onclick="window.close();" />
                </p>

            </div>
        </form>
        <?php

        include("footer.php");

    }//end function displaySkipLabels


    /**
     * function generate
     *
     * Generates the PDF report
     */
    function generate(){

        if($this->skipLabels >= $this->settings["maxRows"] * $this->settings["maxColumns"])
            $this->skipLabels = 0;

        if($this->settings["defaultSortOrder"] && !$this->sortOrder)
            $this->sortOrder = $this->settings["defaultSortOrder"];

        $querystatement = $this->assembleSQL($this->settings["queryStatement"]);

        $queryresult = $this->db->query($querystatement);

        if(!class_exists("phpbmsPDFReport"))
            include("pdfreport_class.php");

        $pdf = new phpbmsPDFReport($this->db, "P", "in");

        $pdf->Open();
        $pdf->SetMargins(0,0);

        $pdf->AddPage();

        $thex = $this->settings["startLeft"];
        $they = $this->settings["startTop"];
        $rowcount = 1;
        $totalcount = 1;
        $column = 1;
        $textRows = 0;

        /**
         * skipping labels
         */
        while($totalcount <= $this->skipLabels){

            if($rowcount > $this->settings["maxRows"]){

                $column++;
                $they = $this->settings["startTop"];
                $thex += $this->settings["labelWidth"] + $this->settings["columnMargin"];
                $rowcount = 1;

            }//endif

            $they += $this->settings["labelHeight"];
            $rowcount++;
            $totalcount++;

        }//endwhile

        $thisCount = $this->db->numRows($queryresult);

        while($therecord = $this->db->fetchArray($queryresult)){

            //initialize amount of text rows
            if(!$textRows){

                $textRows = 0;

                foreach($therecord as $key=>$value)
                    if(strpos($key, "rowText") === 0)
                        $textRows++;

            }//endif

            if($rowcount > $this->settings["maxRows"]){

                $column++;
                $they = $this->settings["startTop"];
                $thex += $this->settings["labelWidth"] + $this->settings["columnMargin"];
                $rowcount = 1;

            }//endif

            if($column > $this->settings["maxColumns"]){

                $pdf->AddPage();
                $thex = $this->settings["startLeft"];
                $they = $this->settings["startTop"];
                $rowcount = 1;
                $column = 1;

            }//endif

            $pdf = $this->printLabel($pdf, $therecord, $thex, $they, $textRows);

            $they += $this->settings["labelHeight"];
            $rowcount++;


        }//endwhile $therecord

        $this->reportOutput = $pdf;

    }//end function generate


    /**
     * function printLabel
     *
     * generates the contents on an individual label, and adds them to the PDF
     *
     * @param object $pdf the PDF object
     * @param array $therecord the data record
     * @param int $thex x corrdinate of current PDF
     * @param int $they y coordinate of current PDF
     * @param int $textRows number of text rows that will be printed
     *
     * @return object returns the modified PDF object
     */
    function printLabel($pdf, $therecord, $thex, $they, $textRows){

        $pdf->SetXY($thex + $this->settings["labelMarginLeft"], $they + $this->settings["labelMarginTop"]);


        $textHeight = 0.135;

        for($i=1; $i<= $textRows; $i++){

            if(isset($this->settings["rowText".$i."Font"]))
                $pdf = $this->setFont($pdf, $this->settings["rowText".$i."Font"]);

            if(isset($this->settings["rowText".$i."Height"]))
                $textHeight = $this->settings["rowText".$i."Height"];

            if($therecord["rowText".$i])
                $pdf->Cell($this->settings["labelWidth"] - $this->settings["labelMarginLeft"], $textHeight, $therecord["rowText".$i], $this->settings["borderDebug"], 2, "L");

            //$pdf->SetX($thex + $this->settings["labelMarginLeft"]);

        }//endfor

        return $pdf;

    }//end function printLabel


    /**
     * function setFont
     *
     * Sets the current PDF font stle based on the passed setting
     *
     * @param object $pdf PDF object
     * @param string $setting comma separated list of FPDF font paramaters
     *
     * @return object PDF object with font set appropriately
     */
    function setFont($pdf, $setting){

        $settings = explode(",", $setting);

        if(!isset($settings[1]))
            $settings[1] = "";

        if(!isset($settings[2]))
            $settings[2] ="";

        $pdf->SetFont($settings[0], $settings[1], $settings[2]);

        return $pdf;

    }//end function setfont


    /**
     * function output
     *
     * sends the generated PDF through the browser
     */
     function output(){

        $filename = cleanFilename($this->settings["fileName"]);
        $this->reportOutput->Output($filename, "D");

     }//end function output


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
            "name"=>"maxRows",
            "defaultValue"=>10,
            "type"=>"int",
            "required"=>1,
            "description"=>"Number of label rows per page"
        );

        $settings[] = array(
            "name"=>"maxColumns",
            "defaultValue"=>3,
            "type"=>"int",
            "required"=>1,
            "description"=>"Number of label columns per page"
        );

        $settings[] = array(
            "name"=>"startTop",
            "defaultValue"=>1/2,
            "type"=>"real",
            "required"=>1,
            "description"=>"Top Margin of page"
        );

        $settings[] = array(
            "name"=>"startLeft",
            "defaultValue"=>3/16,
            "type"=>"real",
            "required"=>1,
            "description"=>"Left Margin of page"
        );

        $settings[] = array(
            "name"=>"columnMargin",
            "defaultValue"=>1/8,
            "type"=>"real",
            "required"=>1,
            "description"=>"Distance between columns"
        );

        $settings[] = array(
            "name"=>"labelHeight",
            "defaultValue"=>1,
            "type"=>"real",
            "required"=>1,
            "description"=>"Height of a single label"
        );

        $settings[] = array(
            "name"=>"labelHeight",
            "defaultValue"=>2 + 5/8,
            "type"=>"real",
            "required"=>1,
            "description"=>"Width of a single label"
        );

        $settings[] = array(
            "name"=>"labelMarginLeft",
            "defaultValue"=>1/16,
            "type"=>"real",
            "required"=>1,
            "description"=>"Distance from left between the start of an individual to the text being put on it"
        );

        $settings[] = array(
            "name"=>"labelMarginTop",
            "defaultValue"=>1/8,
            "type"=>"real",
            "required"=>1,
            "description"=>"Distance from top between start of an individual to the text being put on it"
        );

        $settings[] = array(
            "name"=>"queryStatement",
            "defaultValue"=>'SELECT "no data in first row" AS `rowText1`,  "no data in second row" AS `rowText2` FROM `'.$this->maintable.'`',
            "type"=>"text",
            "required"=>1,
            "description"=>"SQL SELECT and FROM clauses defining the data to be retrieved.  Each line printed should be selected as a column in the format `rowText(X)`, where (X) is the line number it will be printed on"
        );

        $settings[] = array(
            "name"=>"rowText1Font",
            "defaultValue"=>"Arial,B,9",
            "type"=>"string",
            "required"=>0,
            "description"=>"Comma separated list of FPDF font parameters defining font settings for the label text.  You can change the font on subsequent lines by adding an additional setting rowText(x)Font where (x) is the line number the font change occurs."
        );

        return $settings;

    }//endfunction addingRecordDefaultSettings

}//end class pdfLabels


/**
 * PROCESSING
 * =============================================================================
 */
if(!isset($noOutput)){

    //IE needs caching to be set to private in order to display PDFS
    session_cache_limiter('private');

    //set encoding to latin1 (fpdf doesnt like utf8)
    $sqlEncoding = "latin1";
    require_once("../include/session.php");

    checkForReportArguments();

    $report = new pdfLabels($db, $_GET["rid"], $_GET["tid"]);

    if(!isset($_POST["skipLabels"]))
        $report->displaySkipLabels();
    else{

        $report->skipLabels = (int) $_POST["skipLabels"];

        $report->setupFromPrintScreen();
        $report->generate();
        $report->output();

    }//endif


}//end if

/**
 * When adding a new report record, the add/edit needs to know what the class
 * name is so that it can instantiate it, and grab it's default settings.
 */
if(isset($addingReportRecord))
    $reportClass ="pdfLabels";

?>
