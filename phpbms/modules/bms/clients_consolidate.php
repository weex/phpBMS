<?php
/*
 $Rev: 702 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2010-01-01 15:14:57 -0700 (Fri, 01 Jan 2010) $
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

class clientConsolidator{

    var $db;

    function clientConsolidator($db){

        $this->db = $db;

    }//end init


    function showPicker($idsArray, $useUuid = false){

        $querystatement = "
            SELECT
                clients.id,
                clients.uuid
            FROM
                `clients`
            WHERE
                clients.";

        if($useUuid){

            $querystatement .= "uuid IN (";
            $inStatement ="";

            foreach($idsArray as $uuid)
                $inStatement .= ", '".mysql_real_escape_string($uuid)."'";

            $querystatement .= substr($inStatement, 2).")";

        } else {

            $querystatement .= "id IN (";

            $inStatement ="";

            foreach($idsArray as $id)
                $inStatement .= ", ".((int) $id);

            $querystatement .= substr($inStatement, 2).")";

        }//endif

        $queryresult = $this->db->query($querystatement);

        global $phpbms;
        $db = $this->db;

	$pageTitle = "Consolidate Clients and Prospects";
	$phpbms->cssIncludes[] = "pages/bms/consolidateclients.css";
	include("header.php");

        ?>
        <form action="modules/bms/clients_consolidate.php" method="post" name="print_form">
            <div class="bodyline">

                <h1 id="topTitle"><span><?php echo formatVariable($pageTitle) ?></span></h1>

                <p class="notes">
                    Consolidation will relink all related records (sales orders, addresses, notes, etc...) from all the
                    selected client records to a single record selected below.  It will then delete the other records.
                    This is useful when duplicate records exist in the database that already have supporting records or
                    if you have two companies that have merged.
                </p>
                <p>
                    <label for="conolidateTo">consolidate <?php echo count($idsArray) ?> selected records to record</label><br />
                    <?php $uuidArray = $this->_showSelect($queryresult) ?>
                    <textarea name="uuidsArray" id="uuidsArray"><?php echo formatVariable(json_encode($uuidArray)) ?></textarea>
                </p>
                <p align="right">
                    <input name="command" type="submit" class="Buttons" id="print" value="consolidate" />
                    <input name="command" type="submit" class="Buttons" id="cancel" value="cancel"/>
                </p>

            </div>
        </form>
        <?php

        include("footer.php");


    }//end function showPicker


    function _showSelect($queryresult){

        ?><select name="consolidateTo"><?php

        $uuidArray = array();

        while($therecord = $this->db->fetchArray($queryresult)){

            $uuidArray[] = $therecord["uuid"];

            ?><option value="<?php echo formatVariable($therecord["uuid"])?>">UUID: <?php echo formatVariable($therecord["uuid"])?> ID: <?php echo formatVariable($therecord["id"])?></option><?php

        }//endwhile

        ?></select><?php

        return $uuidArray;

    }//end function _showSelect


    function consolidate($consolidateTo, $uuidsArray){

        $changeArray = array();
        $changeIn = "";
        $consolidateTo = mysql_real_escape_string($consolidateTo);

        foreach(json_decode(stripslashes($uuidsArray)) as $uuid){

            if($consolidateTo != $uuid) {

                $changeIn .= ", '".mysql_real_escape_string($uuid)."'";

                $changeArray[] = mysql_real_escape_string($uuid);

            }//endif

        }//endforeach

        $changeIn = substr($changeIn, 2);

        //update addresses
        $updatestatement = "
            UPDATE
                `addresstorecord`
            SET
                `recordid` = '".$consolidateTo."',
                `primary` = 0,
                `defaultshipto` = 0
            WHERE
                `tabledefid` = 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083'
                AND `recordid` IN (".$changeIn.")";

        $this->db->query($updatestatement);

        //change notes
        $updatestatement = "
            UPDATE
                `notes`
            SET
                `attachedid` = '".$consolidateTo."'
            WHERE
                `attachedtabledefid` = 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083'
                AND `attachedid` IN (".$changeIn.")";

        $this->db->query($updatestatement);

        //change attachments
        $updatestatement = "
            UPDATE
                `attachments`
            SET
                `recordid` = '".$consolidateTo."'
            WHERE
                `tabledefid` = 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083'
                AND `recordid` IN (".$changeIn.")";

        $this->db->query($updatestatement);

        //change sales orders
        $updatestatement = "
            UPDATE
                `invoices`
            SET
                `clientid` = '".$consolidateTo."'
            WHERE
                `clientid` IN (".$changeIn.")";

        $this->db->query($updatestatement);

        //change AR
        $updatestatement = "
            UPDATE
                `aritems`
            SET
                `clientid` = '".$consolidateTo."'
            WHERE
                `clientid` IN (".$changeIn.")";

        $this->db->query($updatestatement);

        //change receipts
        $updatestatement = "
            UPDATE
                `receipts`
            SET
                `clientid` = '".$consolidateTo."'
            WHERE
                `clientid` IN (".$changeIn.")";

        $this->db->query($updatestatement);

        //remove clients
        $deletestatement = "
            DELETE FROM
                `clients`
            WHERE
                `uuid` IN (".$changeIn.")";

        $this->db->query($deletestatement);

        global $phpbms;
        $db = $this->db;

	$pageTitle = "Consolidate Clients and Prospects";
	$phpbms->cssIncludes[] = "pages/bms/consolidateclients.css";

        $statusmessage = count($changeArray)." record(s) consolidated successfully";

	include("header.php");

        ?>
        <form action="clients_consolidate.php" method="post" name="print_form">
            <div class="bodyline">

                <h1 id="topTitle"><span><?php echo formatVariable($pageTitle) ?></span></h1>

                <p class="notes">
                    Consolidation has completed.  All related infomation has been transferred to a single client.  You may need
                    to remove any duplicate addresses that may be associated with the client record.
                </p>
                <p align="right">
                    <input name="command" type="submit" class="Buttons" id="done" value="done"/>
                </p>

            </div>
        </form>
        <?php

        include("footer.php");

    }//end function consolidate

}//end class clientConsolidator


/**
 * PROCESSING ==================================================================
 */
if(!isset($noOutput)){

    require_once("../../include/session.php");

    $consolidator = new clientConsolidator($db);

    $_POST = addSlashesToArray($_POST);

    if(!isset($_POST["command"]))
        $error = new appError(200, "passed parameters are not set");

    if($_POST["command"] === "cancel" || $_POST["command"] === "done")
        goURL("../../search.php?id=tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083");

    if(!isset($_POST["consolidateTo"]) || !isset($_POST["uuidsArray"]))
        $error = new appError(200, "passed parameters are not set");

    $consolidator->consolidate($_POST["consolidateTo"], $_POST["uuidsArray"]);

}//endif
?>
