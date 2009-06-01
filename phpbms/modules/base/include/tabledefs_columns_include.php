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

class tableColumns{

    var $db;
    var $uuid;

    function tableColumns($db, $tabledefid){

        $this->db = $db;

        $querystatement = "
            SELECT
                uuid
            FROM
                tabledefs
            WHERE
                id = ".((int) $tabledefid);

        $queryresult = $this->db->query($querystatement);

        $therecord = $this->db->fetchArray($queryresult);

        $this->tabledefuuid = $therecord["uuid"];

    }//end function init


    function getDefaults(){

        $therecord["id"] = NULL;
        $therecord["displayorder"] = NULL;
        $therecord["wrap"] = 0;
        $therecord["roleid"] = "";

        $therecord["size"] = "";
        $therecord["name"] = "";
        $therecord["column"] = "";
        $therecord["align"] = "left";
        $therecord["footerquery"] = "";
        $therecord["sortorder"] = "";
        $therecord["format"] = "";

        return $therecord;

    }//end function getDefaults


    function get($columnid = 0){

        $querystatement = "
            SELECT
                id,
                name,
                `column`,
                align,
                footerquery,
                sortorder,
                displayorder,
                wrap,
                size,
                format,
                roleid
            FROM
                tablecolumns
            WHERE
                tabledefid = '".$this->tabledefuuid."'";

        if($columnid)
            $querystatement .= "
                AND id = ".$columnid;

        $querystatement .= "
            ORDER BY
                displayorder";

        return $this->db->query($querystatement);

    }//end function get


    function add($variables){

        if($variables["format"])
            $variables["format"] = "'".$variables["format"]."'";
        else
            $variables["format"] = "NULL";

        if(!isset($variables["wrap"]))
            $variables["wrap"] = 0;

        $insertstatement = "
            INSERT INTO
                `tablecolumns`
            (
                tabledefid,
                name,
                `column`,
                align,
                footerquery,
                sortorder,
                displayorder,
                size,
                format,
                wrap,
                roleid
            ) VALUES (
                '".$this->uuid."',
                '".$variables["name"]."',
                '".$variables["column"]."',
                '".$variables["align"]."',
                '".$variables["footerquery"]."',
                '".$variables["sortorder"]."',
                '".$variables["displayorder"]."',
                '".$variables["size"]."',
                ".$variables["format"].",
                ".$variables["wrap"].",
                '".$variables["roleid"]."',
            )";

            if($db->query($insertstatement))
                return "Column Added";
            else
                return false;

    }//end function add


    function update($variables){

        if($variables["format"])
            $variables["format"] = "'".$variables["format"]."'";
        else
            $variables["format"] = "NULL";

        if(!isset($variables["wrap"]))
            $variables["wrap"] = 0;

        $updatestatement = "
            UPDATE
                tablecolumns
            SET
                `name` = '".$variables["name"]."',
                `column` = '".$variables["column"]."',
                `align` = '".$variables["align"]."',
                `sortorder` = '".$variables["sortorder"]."',
                `footerquery` = '".$variables["footerquery"]."',
                `size` = '".$variables["size"]."',
                `format` = ".$variables["format"].",
                `wrap` = ".$variables["wrap"].",
                `roleid` = '".$variables["roleid"]."',
            WHERE
                id = ".((int) $variables["columnid"]);

        if($db->query($updatestatement))
            return "Column Updated";
        else
            return false;

    }//end function update


    function delete($id){

        $querystatement = "
            SELECT
                displayorder
            FROM
                tablecolumns
            WHERE
                id = ".((int) $id);

        $queryresult = $this->db->query($querystatement);

        $therecord = $this->db->fetchArray($queryresult);

        $updatestatement = "
            UPDATE
                tablecolumns
            SET
                displayorder = displayorder -1
            WHERE
                tabledefid = '".$this->uuid."'
                AND displayorder > ".$therecord["displayorder"];

        if($this->db->query($queryresult)){

            $deletestatement = "
                DELETE FROM
                    tablecolumns
                WHERE
                    id = ".$id;

            if($this->db->query($deletestatement))
                return "Column Deleted";

        }//endif

        return false;

    }//end function delete


    function move($id, $direction = "up"){

        $increment = ($direction == "down")? "1": "-1";

        $querstatement = "
            SELECT
                displayorder
            FROM
                tablecolumns
            WHERE
                id = ".((int) $id);

        $queryresult = $this->db->query($querstatement);

        $therecord = $this->db->fetchArray($queryresult);

        $querstatement = "
            SELECT
                MAX(displayorder) AS themax
            FROM
                tablecolumns
            WHERE
                tabledefid = '".$this->uuid."'";

        $queryresult = $this->db->query($querstatement);

        $maxrecord = $this->db->fetchArray($queryresult);

        if(!(($direction=="down" && $therecord["displayorder"] == $maxrecord["themax"]) || ($direction == "up" and $therecord["displayorder"] == "0"))){

            $updatestatement = "
                UPDATE
                    tablecolumns
                SET
                    displayorder = ".$therecord["displayorder"]."
                WHERE
                    displayorder = ".($increment+$therecord["displayorder"])."
                    AND tabledefid = '".$this->uuid."'";

            $this->db->query($updatestatement);

            $updatestatement = "
                UPDATE
                    tablecolumns
                SET
                    displayorder = displayorder + ".$increment."
                WHERE
                    id = ".$id;

            $this->db->query($updatestatement);

            return "Column Moved";

        }//endif

        return false;

    }//end function move

}//end class tableColumns

?>
