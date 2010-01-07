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


class uniqueChecker{

    var $db;

    function uniqueChecker($db){

        $this->db = $db;
        $this->db->errorFormat = "json";

    }//end function init


    function check($tabledefuuid, $columname, $value, $excludeid = NULL){

        $querystatement = "
            SELECT
                `maintable`
            FROM
                `tabledefs`
            WHERE
                `uuid` = '".mysql_real_escape_string($tabledefuuid)."'";

        $queryresult = $this->db->query($querystatement);

        if($this->db->numRows($queryresult) === 0)
            return "error";

        $therecord = $this->db->fetchArray($queryresult);

        $table = $therecord["maintable"];

        $columname = mysql_real_escape_string(str_replace("`","", $columname));
        $value = mysql_real_escape_string($value);

        $querystatement = "
            SELECT
                count(id) AS thecount
            FROM
                `".$table."`
            WHERE
                `".$columname."` = '".$value."'";

        if($excludeid){

            $querystatement .= " AND `uuid` != '".mysql_real_escape_string($excludeid)."'";

        }//endif

        $queryresult = $this->db->query($querystatement);

        $therecord = $this->db->fetchArray($queryresult);

        return ($therecord["thecount"] == 0);

    }//end function check

}//end class


/**
 * PROCESSING ==================================================================
 */
if(!isset($noOutput)){

    require_once("include/session.php");

    if(!isset($_GET["tduuid"]) || !isset($_GET["cname"]) || !isset($_GET["value"]))
        $error = new appError(200, "passed parameters not set");

    if(!isset($_GET["xuuid"]))
        $_GET["xuuid"] = "";

    $checker = new uniqueChecker($db);

    echo json_encode($checker->check($_GET["tduuid"], $_GET["cname"], $_GET["value"], $_GET["xuuid"]));

}//endif
