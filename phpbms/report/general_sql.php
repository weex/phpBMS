<?php
/*
 $Rev: 258 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-08 21:59:28 -0600 (Wed, 08 Aug 2007) $
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
 * Handles creation of SQL insert records
 *
 * This class handles the file creation of SQL Insert records that
 * correspond to the table definition main table that intializes a print of this
 * report
 */
class sqlExport extends phpbmsReport{

    /**
     * $maintable
     * @var string the SQL name of the main table to print.
     */
    var $maintable = "";

    /**
     * function generalTablePrint
     *
     * Initialization function
     *
     * @param object $db database object
     * @param string $reportUUID UUID of report record
     * @param string $tabledefUUID UUID of table definition intializing print
     */
    function sqlExport($db, $reportUUID, $tabledefUUID){

        parent::phpbmsReport($db, $reportUUID, $tabledefUUID);

        $therecord = $this->getTableDefInfo();

        $this->maintable = $therecord["maintable"];

    }//end function init


    /**
     * function generate
     *
     * Generates the SQL Insert statements for each record
     */
    function generate(){

        $querystatement = "
            SELECT
                *
            FROM
                ".$this->maintable;

        $querystatement = $this->assembleSQL($querystatement);

        $queryresult = $this->db->query($querystatement);

        $num_fields = $this->db->numFields($queryresult);

        $statementstart = "INSERT INTO `".$this->maintable."` (";

        for($i=0; $i<$num_fields ;$i++)
             $statementstart .= "`".$this->db->fieldName($queryresult,$i)."`, ";

        $statementstart = substr($statementstart,0,strlen($statementstart)-2).") VALUES (";

        while($therecord = $this->db->fetchArray($queryresult)){

            $insertstatement = $statementstart;

            foreach($therecord as $name => $field){

                if($field === NULL)
                     $addfield = "NULL, ";
                else
                     $addfield = "'".mysql_real_escape_string($field)."', ";

                //this is in temp for intallation exporting
                if(hasRights("Admin")){

                    switch($name){

                        case "createdby":
                        case "modifiedby":
                            $addfield = "1, ";
                            break;

                        case "creationdate":
                        case "modifieddate":
                            $addfield = "NOW(), ";
                            break;

                    }//end switch

                }//endif

                $insertstatement .= $addfield;

            }//endforeach

            $insertstatement = substr($insertstatement,0,strlen($insertstatement)-2).");\n";

            $this->reportOutput .= $insertstatement;

        }//endwhile

    }//end function generate

    /**
     * function show
     *
     * outputs the report to a .sql file
     */
    function show(){

        if(!isset($this->settings["filename"]))
           $this->settings["filename"] = $this->maintable."-export.sql";

        header("Content-type: text/plain");
        header('Content-Disposition: attachment; filename="'.$this->settings["filename"].'"');

        echo $this->reportOutput;

    }//end function show

}//end class sqlExport


/**
 * PROCESSING
 * =============================================================================
 */
if(!isset($noOutput)){

    session_cache_limiter('private');

    require_once("../include/session.php");

    checkForReportArguments();

    $report = new sqlExport($db, $_GET["rid"],$_GET["tid"]);
    $report->setupFromPrintScreen();
    $report->generate();
    $report->show();

}//end if

/**
 * When adding a new report record, the add/edit needs to know what the class
 * name is so that it can instantiate it, and grab it's default settings.
 */
if(isset($addingReportRecord))
    $reportClass ="pdfLabels";

?>
