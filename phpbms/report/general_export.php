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
 * Basic CSV output report
 *
 * This class implements a report used for creating a CSV file through reporting
 */
class generalExport extends phpbmsReport {

    /**
     * $maintable
     * @var string the SQL name of the main table to print.
     */
    var $maintable = "";


    /**
     * function generalExport
     *
     * Initialization function
     *
     * @param object $db database object
     * @param string $reportUUID UUID of report record
     * @param string $tabledefUUID UUID of table definition intializing print
     */
    function generalExport($db, $reportUUID, $tabledefUUID){

        parent::phpbmsReport($db, $reportUUID, $tabledefUUID);

        $this->checkForDefaultSettings();

        $therecord = $this->getTableDefInfo();

        $this->maintable = $therecord["maintable"];

    }//end function init


    /**
     * function checkForDefaultSettings
     *
     * Checks to make sure loaded report Settings exist and are correct
     */
    function checkForDefaultSettings(){

        if(!isset($this->settings["showHeader"]))
            $this->settings["showHeader"] = 1;

        if(!isset($this->settings["fieldDelimiter"]))
            $this->settings["fieldDelimiter"] = ",";

        if(!isset($this->settings["recordDelimiter"]))
            $this->settings["recordDelimiter"] = "\n";

        if(!isset($this->settings["fieldEncapsulation"]))
            $this->settings["fieldEncapsulation"] = "\"";

    }//end function checkForDefaultSettings


    /**
     * function generate
     *
     * Creates body of the file.
     */
    function generate(){

        $fromFields = "*";

        $columns = array();
        foreach($this->settings as $key=>$value){

            if(strpos($key, "column") === 0){

                $pos = substr($key,6)-1;
                $columns[$pos]["field"] = $value;

                if(isset($this->settings["titleColumn".($pos + 1)]))
                    $columns[$pos]["title"] = $this->settings["titleColumn".($pos +1)];
                else
                    $columns[$pos]["title"] = "";

            }//endif

        }//endforeach

        if(count($columns)){

            ksort($columns);

            $fromFields = "";
            foreach($columns as $column){

                $fromFields .= ", ".$column["field"];
                if($column["title"])
                    $fromFields .= " AS `".$column["title"]."`";

                $fromFields.="\n";

            }//endforeach

            $fromFields = substr($fromFields, 1);

        }//endif

        if(isset($this->settings["fromTable"]))
            $querytable = $this->settings["fromTable"];
        else
            $querytable = $this->maintable;

        $querystatement = "
            SELECT
                ".$fromFields."
            FROM
                ".$querytable;

        $querystatement = $this->assembleSQL($querystatement);

        $queryresult = $this->db->query($querystatement);

        $num_fields = $this->db->numFields($queryresult);

        /**
         * generating column names.  First row is just field names
         */
        if($this->settings["showHeader"])
            for($i=0;$i<$num_fields;$i++)
                $this->reportOutput .= $this->settings["fieldDelimiter"].$this->db->fieldName($queryresult, $i);

        $this->reportOutput = substr($this->reportOutput, strlen($this->settings["fieldDelimiter"])).$this->settings["recordDelimiter"];

        /**
         * Looping through retrieved records.
         */
        while($therecord = $this->db->fetchArray($queryresult)){

            $line = "";

            foreach($therecord as $value)
               $line .= $this->settings["fieldDelimiter"].$this->settings["fieldEncapsulation"].mysql_real_escape_string($value).$this->settings["fieldEncapsulation"];

            $line = substr($line, strlen($this->settings["fieldDelimiter"])).$this->settings["recordDelimiter"];

            $this->reportOutput .= $line;

        }//endwhile

        /**
         * removing last \n
         */
        $this->reportOutput = substr($this->reportOutput, 0, strlen($this->reportOutput) - strlen($this->settings["recordDelimiter"]));

    }//end function generate


    /**
     * function show
     *
     * Outputing generated data
     */
    function show(){

        if(!isset($this->settings["filename"]))
           $this->settings["filename"] = $this->maintable."-export.txt";
echo "<pre>";
        //header("Content-type: text/plain");
        //header('Content-Disposition: attachment; filename="'.$this->settings["filename"].'"');

        echo $this->reportOutput;

    }//end function show

}//end class


/**
 * PROCESSING
 * =============================================================================
 */
if(!isset($noOutput)){

    session_cache_limiter('private');

    require_once("../include/session.php");

    checkForReportArguments();

    $report = new generalExport($db, $_GET["rid"],$_GET["tid"]);
    $report->setupFromPrintScreen();
    $report->generate();
    $report->show();

}//end if

/**
 * When adding a new report record, the add/edit needs to know what the class
 * name is so that it can instantiate it, and grab it's default settings.
 */
if(isset($addingReportRecord))
    $reportClass ="generalExport";
?>
