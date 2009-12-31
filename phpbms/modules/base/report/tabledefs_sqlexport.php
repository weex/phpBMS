<?php
/*
 $Rev: 258 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-08 21:59:28 -0600 (Wed, 08 Aug 2007) $
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
    include("../../../report/report_class.php");

class tabledefSQLExport extends phpbmsReport{

    function tabledefSQLExport($db, $reportUUID, $tabledefUUID){

        parent::phpbmsReport($db, $reportUUID, $tabledefUUID);

    }//end method


    function generate(){

            $this->reportOutput .= $this->_addTableInfo("tablecolumns");
            $this->reportOutput .= $this->_addTableInfo("tablefindoptions");
            $this->reportOutput .= $this->_addTableInfo("tablegroupings");
            $this->reportOutput .= $this->_addTableInfo("tableoptions");
            $this->reportOutput .= $this->_addTableInfo("tablesearchablefields");

    }//end method


    function _addTableInfo($tablename){

        $querystatement = "
            SELECT
                    `uuid`
            FROM
                    `tabledefs`
            WHERE
                    ".$this->whereClause;

        $queryresult = $this->db->query($querystatement);

        $whereclause = "";

        while($therecord = $this->db->fetchArray($queryresult))
            $whereclause .= " OR `tabledefid` = '".mysql_real_escape_string($therecord["uuid"])."'";

        $whereclause = substr($whereclause, 4);

        $output = 	"/* Begin ".$tablename." */\n".
                        "/* ====================================================================== */\n";

        $querystatement = "
                SELECT
                        *
                FROM
                        `".$tablename."`
                WHERE
                        ".$whereclause."
                ORDER BY
                        ".$tablename.".tabledefid";

        $queryresult = $this->db->query($querystatement);

        $num_fields = $this->db->numFields($queryresult);

        $statementstart = "INSERT INTO `".$tablename."` (";

        for($i=0; $i<$num_fields ;$i++){

            $fieldname = $this->db->fieldName($queryresult,$i);

            if($fieldname != "id")
                $statementstart .= "`".$fieldname."`, ";

        }//endfor

        $statementstart = substr($statementstart,0,strlen($statementstart)-2).") VALUES (";

        while($therecord = $this->db->fetchArray($queryresult)){

            $insertstatement = $statementstart;

            foreach($therecord as $name => $field){

                if($field === NULL)
                    $addfield = "NULL, ";
                else
                    $addfield = "'".mysql_real_escape_string($field)."', ";

                if($name != "id")
                    $insertstatement .= $addfield;

            }//endforeach

            $insertstatement = substr($insertstatement,0,strlen($insertstatement)-2).");\n";

            $output .= $insertstatement;

        }//endwhile


        $output .= 	"/* ====================================================================== */\n".
                        "/* END ".$tablename." - record count: ".$this->db->numRows($queryresult)."*/\n\n";

        return $output;

    }//end method


    function show(){

        header("Content-type: text/plain");
        header('Content-Disposition: attachment; filename="tableInfoSQL.sql"');

        echo $this->reportOutput;

    }//end method


}//end class

/**
 * PROCESSING
 * =============================================================================
 */
if(!isset($noOutput)){

    session_cache_limiter('private');

    require_once("../../../include/session.php");

    checkForReportArguments();

    $report = new tabledefSQLExport($db, $_GET["rid"],$_GET["tid"]);
    $report->setupFromPrintScreen();
    $report->generate();
    $report->show();

}//end if

/**
 * When adding a new report record, the add/edit needs to know what the class
 * name is so that it can instantiate it, and grab it's default settings.
 */
if(isset($addingReportRecord))
    $reportClass ="tabledefSQLExport";
?>
