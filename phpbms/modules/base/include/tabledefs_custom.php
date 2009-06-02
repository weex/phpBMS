<?php
/*
 $Rev: 498 $ | $LastChangedBy: nate $
 $LastChangedDate: 2009-04-16 13:00:58 -0600 (Thu, 16 Apr 2009) $
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

class customFields{

    var $db;
    var $tableinfo = array();
    var $settings;

    function customFields($db, $tabledefid){

        $this->db = $db;
        $this->tableinfo["id"] = $tabledefid;

        $this->_getTableInfo();
        $this->_getRecords();

    }//end function init


    function _getTableInfo(){

        $querystatement = "
            SELECT
                `id`,
                `uuid`,
                `displayname`,
                `hascustomfields`
            FROM
                `tabledefs`
            WHERE
                `id` = ".$this->tableinfo["id"];

        $queryresult = $this->db->query($querystatement);

        $this->tableinfo = $this->db->fetchArray($queryresult);

    }//end function _getTableInfo


    //get save custom fields that are setup for this table
    function _getRecords(){

        $querystatement = "
            SELECT
                *
            FROM
                `tablecustomfields`
            WHERE
                `tabledefid` = '".$this->tableinfo["uuid"]."'";

        $queryresult = $this->db->query($querystatement);

        $settings = array();

        //grab all the records
        while($therecord = $this->db->fetchArray($queryresult))
            $settings[$therecord["field"]] = $therecord;

        //loop through custom1 - custom8.  If there was not a record
        //in the database, get blank defaults
        for($i = 1; $i < 9; $i++){

            if(!isset($settings["custom".$i]))
               $settings["custom".$i] = $this->_getDefaults();

        }//endfor i

        $this->settings = $settings;

    }//end function _getRecords;


    //create the phpbmsForm, and
    //instantiate the fields for all 8 fields
    function prepFields(){

        $settings = $this->settings;

        $theform = new phpbmsForm();

        for($i = 1; $i < 9; $i++){

            $theinput = new inputField("custom".$i."name", $settings["custom".$i]["name"], "name");
            $theform->addField($theinput);

            $theinput = new inputRolesList($this->db, "custom".$i."roleid", $settings["custom".$i]["roleid"], "access (role)");
            $theform->addField($theinput);

            $theinput = new inputField("custom".$i."displayorder", $settings["custom".$i]["displayorder"], "display order", true, NULL, 10, 10);
            $theform->addField($theinput);

            switch($i){

                case 1:
                case 2:
                    $req = true;
                    $formatArray = array("integer" => "integer", "real" => "real", "currency" => "currency");
                    break;

                case 3:
                case 4:
                    $req = true;
                    $formatArray = array("date" => "date", "time" => "time");
                    break;

                case 5:
                case 6:
                    $req = true;
                    $formatArray = array("no formatting" => "", "phone number" => "phone", "e-mail address" => "email", "Web address (URL)" => "www", "modifiable drop down list" => "list");
                    break;

                case 7:
                case 8:
                    $req = false;
                    $formatArray = array("Not Applicable" => "");
                    break;

            }//endswitch

	    $theinput = new inputBasicList("custom".$i."format", $settings["custom".$i]["format"], $formatArray, "format");

            if(!$req){

                $theinput->setAttribute("readonly", "readonly");
                $theinput->setAttribute("class", "uneditable");

            }//endif

            $theform->addField($theinput);

            $theinput = new inputCheckbox("custom".$i."required", $settings["custom".$i]["required"], "required", !$req);
            $theform->addField($theinput);

            if($req){

                $theinput = new inputTextarea("custom".$i."generator", $settings["custom".$i]["generator"], "generation javascript", false, 2, 84);
                $theform->addField($theinput);

            }//endif

        }//endfor i

        return $theform;

    }//end function prepFields


    //shows the fieldset, and inputs for each fo the custom fields
    function showFields($theform){

        for($i = 1; $i < 9; $i++){

            switch($i){

                case 1:
                case 2:
                    $type = "number";
                    break;

                case 3:
                case 4:
                    $type = "date or time";
                    break;

                case 5:
                case 6:
                    $type = "string";
                    break;

                case 7:
                case 8:
                    $type = "boolean";
                    break;

            }//endswitch



            ?>
            <fieldset>
                <legend>Custom Field <?php echo $i ?></legend>

                <p>type: <strong><?php echo $type ?></strong></p>

                <p>active: <strong><?php echo ($this->settings["custom".$i]["name"]) ? "yes" : "no" ; ?></strong></p>

                <p class="items"><?php $theform->showField("custom".$i."name") ?></p>

                <p class="items formatting"><?php $theform->showField("custom".$i."format") ?></p>

                <p class="items"><br/><?php $theform->showField("custom".$i."required") ?></p>

                <p class="items"><?php $theform->showField("custom".$i."displayorder") ?></p>

                <p class="items"><?php $theform->showField("custom".$i."roleid") ?></p>

                <?php if($type != "boolean") {?>
                    <p class="generators"><?php $theform->showField("custom".$i."generator") ?></p>
                <?php }//endif ?>

            </fieldset>
            <?php

        }//endfor i

    }//end function showFields


    function _getDefaults(){

        $therecord["name"] = "";
        $therecord["field"] = "";
        $therecord["format"] = "";
        $therecord["required"] = 0;
        $therecord["displayorder"] = 0;
        $therecord["roleid"] = "";
        $therecord["generator"] = "";

        return $therecord;

    }//end function _getDefaults

    //process a posting of the form.
    function process($variables){

        $variables = addSlashesToArray($variables);

        $this->_deleteRecords();

        for($i = 1; $i < 9; $i++){

            $record = $this->_grabFieldInfo($i, $variables);

            $this->_insertRecord($record);

        }//endfor

        $this->_getRecords();

        return "Custom Fields Updated";

    }//end function process


    function _deleteRecords(){

        $deletestatement = "
            DELETE FROM
                `tablecustomfields`
            WHERE
                `tabledefid` = '".$this->tableinfo["uuid"]."'";

        $this->db->query($deletestatement);

    }//end function _deleteRecords()


    //extract an indvidual fields information from the post
    function _grabFieldInfo($i, $variables){

        $fieldArray = array("name", "format", "required", "displayorder", "roleid", "generator");

        $record["field"] = "custom".$i;
        $record["tabledefid"] = $this->tableinfo["uuid"];

        foreach($fieldArray as $field) {

            if(isset($variables["custom".$i.$field]))
                $record[$field] = $variables["custom".$i.$field];
            else{

                if($field != "name" || $field != "generator")
                    $record[$field] = 0;
                else
                    $record[$field] = "";

            }//endif

        }//endforeach

        return $record;

    }///end function _grabFieldInfo


    //Create indvidual custom field record (only if name is present)
    function _insertRecord($record){

        if($record["name"]) {

            $fieldnames = "";
            $values = "";

            foreach($record as $field=>$value){

                $fieldnames .= ", `".$field."`";
                $values .= ", '".$value."'";

            }//end foreach

            $fieldnames = substr($fieldnames, 1);
            $values = substr($values, 1);

            $insertstatement = "
                INSERT INTO
                    `tablecustomfields`
                    (".$fieldnames.")
                VALUES
                    (".$values.")";

            $this->db->query($insertstatement);

        }//endif

    }//end function

}//end class
?>
