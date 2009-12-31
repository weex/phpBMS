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

if(!class_exists("phpbmsReport"))
    include("report_class.php");

/**
 * Record Reporting in HTML table format
 *
 * This class implements a report used for printing out general table information
 * in HTML table format
 */
class generalTablePrint extends phpbmsReport {

    /**
     * $maintable
     * @var string the SQL name of the main table to print.
     */
    var $maintable = "";

    /**
     * $displayname
     * @var string display name of table definition
     */
    var $displayname = "";


    /**
     * function generalTablePrint
     *
     * Initialization function
     *
     * @param object $db database object
     * @param string $reportUUID UUID of report record
     * @param string $tabledefUUID UUID of table definition intializing print
     */
    function generalTablePrint($db, $reportUUID, $tabledefUUID){

        parent::phpbmsReport($db, $reportUUID, $tabledefUUID);

        $therecord = $this->getTableDefInfo();

        $this->maintable = $therecord["maintable"];
        $this->displayname = $therecord["displayname"];

    }//end function init


    /**
     * function generate
     *
     * Creates the main part of the report (table)
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
            $columns = array_reverse($columns);

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


        if(!isset($this->settings["reportTitle"]))
            $this->settings["reportTitle"] = $this->displayname;

        ob_start();

        ?>
        <div id="container">
            <h1><?php echo formatVariable($this->settings["reportTitle"])?></h1>
            <table id="results">
                <thead>
                    <tr>
        <?php

        for($i=0;$i<$num_fields;$i++){

                ?>
                    <th <?php if($i == $num_fields-1) echo 'id="lastHeader"' ?>><?php echo $this->db->fieldName($queryresult, $i); ?></th>
                <?php

        }//end for

        ?>
                    </tr>
                </thead>

                <tbody>
        <?php

        while($therecord = $this->db->fetchArray($queryresult)){

            ?><tr><?php

            foreach($therecord as $value){

                ?><td><?php echo formatVariable($value)?></td><?php

            }//end foreach

            ?></tr><?php

        }//endwhile

        ?>
                </tbody>
            </table>
        </div>
        <?php

        $this->reportOutput = ob_get_contents();
        ob_end_clean();

    }//end function generate


    /**
     * function show
     *
     * outputs HTML report
     */
    function show(){

        global $phpbms;
        $db = &$this->db;

        $phpbms->cssIncludes[] = "reports.css";
        $phpbms->cssIncludes[] = "pages/generaltableprint.css";

        $phpbms->showMenu = false;
        $phpbms->showFooter = false;

        include("header.php");

        echo $this->reportOutput;

        include("footer.php");

    }//end method

}//end class


/**
 * PROCESSING
 * =============================================================================
 */
if(!isset($noOutput)){

    session_cache_limiter('private');

    require_once("../include/session.php");

    checkForReportArguments();

    $report = new generalTablePrint($db, $_GET["rid"], $_GET["tid"]);
    $report->setupFromPrintScreen();
    $report->generate();
    $report->show();

}//end if

/**
 * When adding a new report record, the add/edit needs to know what the class
 * name is so that it can instantiate it, and grab it's default settings.
 */
if(isset($addingReportRecord))
    $reportClass ="generalTablePrint";

?>
