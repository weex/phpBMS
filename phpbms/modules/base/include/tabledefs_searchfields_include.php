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

class tableSearchFields{

    var $db;
    var $uuid;

    function tableSearchFields($db, $id){

        $this->db = $db;

        $querystatement = "
            SELECT
                uuid
            FROM
                tabledefs
            WHERE
                id = ".((int) $id);

        $queryresult = $this->db->query($querystatement);

        $therecord = $this->db->fetchArray($queryresult);

        $this->uuid = $therecord["uuid"];

    }//end function init


    function getDefaults(){

        $therecord["id"]=NULL;
        $therecord["displayorder"]=NULL;
        $therecord["name"]="";
        $therecord["type"]="field";
        $therecord["field"]="";

        return $therecord;

    }//end function getDefaults


    function get($id = 0){

        $querystatement = "
            SELECT
                `id`,
                `field`,
                `name`,
                `displayorder`,
                `type`
            FROM
                `tablesearchablefields`
            WHERE
                tabledefid = '".$this->uuid."'";

        if($id)
            $querystatement .= "
                AND
                    `id` = ".$id;

        $querystatement .= "
            ORDER BY
                `displayorder`";

        return $this->db->query($querystatement);

    }//end function get


    function add($variables){

        $insertstatement = "
            INSERT INTO
                `tablesearchablefields`
            (
                `tabledefid`,
                `field`,
                `name`,
                `displayorder`,
                `type`
            ) VALUES (
                '".$this->uuid."',
                '".$variables["field"]."',
                '".$variables["name"]."',
                '".$variables["displayorder"]."',
                '".$variables["type"]."'
            )";

        if($this->db->query($insertstatement))
            return "Search Field Added";
        else
            return false;

    }//end function add


    function update($variables){

        $updatestatement = "
            UPDATE
                `tablesearchablefields`
            SET
                `field` = '".$variables["field"]."',
                `type` = '".$variables["type"]."',
                `name` = '".$variables["name"]."'
            WHERE
                `id` = ".$variables["searchfieldid"];

        if($this->db->query($updatestatement))
            return "Search Field Updated";
        else
            return false;

    }//end function update


    function delete($id){

        $deletestatement = "
            DELETE FROM
                `tablesearchablefields`
            WHERE
                `id` =".((int) $id);

        if($this->db->query($deletestatement))
            return "Search Field Deleted";
        else
            return false;

    }//end function delete


    function move($id, $direction = "up"){

        if($direction == "down")
            $increment = "1";
        else
            $increment="-1";

        $querystatement = "
            SELECT
                `displayorder`
            FROM
                `tablesearchablefields`
            WHERE
                id = ".((int) $id);

        $queryresult = $this->db->query($querystatement);

        $therecord = $this->db->fetchArray($queryresult);

        $querystatement = "
            SELECT
                MAX(`displayorder`) AS themax
            FROM
                `tablesearchablefields`
            WHERE
                `tabledefid` = '".$this->uuid."'";

        $queryresult = $this->db->query($querystatement);

        $maxrecord = $this->db->fetchArray($queryresult);

        if(!(($direction == "down" && $therecord["displayorder"] == $maxrecord["themax"]) || ($direction=="up" && $therecord["displayorder"]=="0"))){

            $updatestatement = "
                UPDATE
                    `tablesearchablefields`
                SET
                    `displayorder` = ".$therecord["displayorder"]."
                WHERE
                    displayorder = ".($increment+$therecord["displayorder"])."
                    AND tabledefid='".$this->uuid."'";

            $this->db->query($updatestatement);

            $updatestatement = "
                UPDATE
                    `tablesearchablefields`
                SET
                    displayorder = displayorder + ".$increment."
                WHERE
                    id=".((int) $id);

            $this->db->query($querystatement);

            return "Position Moved";

        }//endif

        return false;

    }//end function move

}//end class tableSearchFields
?>
