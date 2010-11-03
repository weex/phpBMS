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


class choiceList{

    var $db;

    function choiceList($db){

                $this->db = $db;

    }//end function init


    function deleteList($listname){

        $deletestatement = "
            DELETE FROM
                `choices`
            WHERE
                `listname` = '".mysql_real_escape_string($listname)."' ";
            $queryresult=$this->db->query($deletestatement);

            echo "ok";
    }//end function deleteList


    function addToList($listname, $value){

        $insertstatement = "
            INSERT INTO
                `choices`(
                `listname`,
                `thevalue`
            ) VALUES (
                '".mysql_real_escape_string($listname)."',
                '".mysql_real_escape_string($value)."'
            )";

        $this->db->query($insertstatement);

        echo "ok";

    }//end function addToList


    function displayList($queryresult, $blankvalue){

        while($therecord = $this->db->fetchArray($queryresult)){

            $display = $therecord["thevalue"];
            $theclass = "";

            if($therecord["thevalue"]==""){

                $display = "&lt;".$blankvalue."&gt;";
                $theclass = ' class="choiceListBlank" ';

            }

            ?><option value="<?php echo $therecord["thevalue"]?>" <?php echo $theclass?>><?php echo $display?></option><?php

        }//end while

    }//end function displayList


    function displayBox($listname, $blankvalue, $listid){

        $blankvalue = str_replace("<","",$blankvalue);
        $blankvalue = str_replace(">","",$blankvalue);

        $querystatement = "
            SELECT
                thevalue
            FROM
                choices
            WHERE
                listname='".mysql_real_escape_string($listname)."'
            ORDER BY
                thevalue";

        $queryresult = $this->db->query($querystatement);

        ?>
        <p id="MLListP">
                <select id="MLlist" name="MLList" size="12" onchange="updateML(this)">
                        <?php $this->displayList($queryresult, $blankvalue)?>
                </select>
        </p>
        <p id="MLAddDelP">
                <input type="button" id="MLDelete" name="MLDelete" value="delete" class="Buttons" disabled onclick="delML()" /><br/>
                <input type="button" id="MLInsert" name="MLInsert" value="insert" class="Buttons" onclick="insertML()"/>
        </p>
        <p id="MLAddTextP">
                <input name="MLaddedit" id="MLaddedit" type="text"/>
                <input name="MLblankvalue" id="MLblankvalue" type="hidden" value="<?php echo $blankvalue?>"/>
        </p>
        <p id="MLAddP">
                <input type="button" id="MLaddeditbutton" name="MLaddeditbutton" value="add" class="Buttons" onclick="addeditML('<?php echo $blankvalue?>')" />
        </p>
        <p id="MLStatus" class="small">&nbsp;</p>
        <div align="right">
                <input type="button" id="MLok" name="MLok" value="ok" class="Buttons" style="width:75px;" onclick="clickOK('<?php echo APP_PATH?>','<?php echo $listid?>','<?php echo $listname?>')"/>
                <input type="button" id="MLcancel" name="MLcancel" value="cancel" class="Buttons" style="width:75px;" onclick="closeBox('<?php echo $listid?>');"/>&nbsp;
        </div>
        <?php

    }//end function

}//end class





if(!isset($noOutput)){

    require_once("include/session.php");
    $thelist = new choiceList($db);

    if(!isset($_GET["cm"]))
        $error = new appError(200, "passed parameters not set");

    switch($_GET["cm"]){

        case "shw":

            if(!isset($_GET["ln"]))
                    $_GET["ln"]="shippingmethod";

            if(!isset($_GET["bv"]))
                $_GET["bv"]="none";

            if(!isset($_GET["lid"]))
                $_GET["lid"]=NULL;

            $thelist->displayBox($_GET["ln"],$_GET["bv"],$_GET["lid"]);
            break;

        case "del":

            if(!isset($_GET["ln"]))
                $error = new appError(200, "passed parameters not set");

            $thelist->deleteList($_GET["ln"]);
            break;

        case "add":

            if(!isset($_GET["ln"]) || !isset($_GET["val"]))
                $error = new appError(200, "passed parameters not set");

            $thelist->addToList($_GET["ln"], $_GET["val"]);
            break;

    }//endswitch

}//endif


?>
