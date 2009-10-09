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

    class tableOptions {

        var $db;
        var $tabledefid;
        var $tabledefuuid;
        var $tablename;
        var $hasRelatedPushRecord = false;

        function tableOptions($db, $tabledefid){

            $this->db = $db;
            $this->tabledefid = ((int) $tabledefid);

            $querystatement = "
                SELECT
                    uuid,
                    displayname
                FROM
                    tabledefs
                WHERE
                    id=".$this->tabledefid;

            $queryresult = $this->db->query($querystatement);

            $therecord = $this->db->fetchArray($queryresult);

            $this->tabledefuuid = $therecord["uuid"];

            /**
              *  Check for push records
              */
            //if(isset($phpbms)){
            //    if(moduleExists("mod:b2d42220-443b-fe74-dbdb-ed2c0968c38c", $phpbms->modules)){
            //        $querystatement = "
            //            SELECT
            //                `id`
            //            FROM
            //                `pushrecords`
            //            WHERE
            //                `originuuid` = '".$this->tabledefuuid."'
            //        ";
            //
            //        $queryresult = $this->db->query($querystatement);
            //        if($this->db-numRows($queryresult))
            //            $this->hasRelatedPushRecord = true;
            //
            //    }
            //}

            $this->tablename = formatVariable($therecord["displayname"]);

        }//end function init


        function getDefaults(){

            $thereturn = array(
                "id" => NULL,
                "name" => "",
                "option" => "",
                "needselect" => 0,
                "othercommand" => 0,
                "roleid" => 0,
                "displayorder" => 0,
                "pushrecordid" => "",
                "type" => 0,
            );
            
            return $thereturn;

        }//end function getDefaults


        function get($id = NULL){

            $querystatement = "
                SELECT
                    tableoptions.id,
                    tableoptions.name,
                    tableoptions.name AS `displayname`,
                    IF(`tableoptions`.`othercommand`='0','1',IF(`tableoptions`.`name` LIKE '%:%','3','2')) AS `commandtype`,
                    tableoptions.option,
                    tableoptions.othercommand,
                    tableoptions.othercommand AS `type`,
                    tableoptions.displayorder,
                    tableoptions.roleid,
                    tableoptions.needselect,
                    roles.name AS rolename
                FROM
                    tableoptions LEFT JOIN roles ON tableoptions.roleid = roles.id
                WHERE";

            if($id)
                $querystatement .= "
                    tableoptions.id = ".((int) $id);
            else
                $querystatement .= "
                    tabledefid = '".$this->tabledefuuid."'";

            $querystatement .= "
                ORDER BY
                    `commandtype`,
                    tableoptions.displayorder,
                    tableoptions.name";

            $queryresult = $this->db->query($querystatement);
                
            $thereturn = array();
            while($therecord = $this->db->fetchArray($queryresult)){
                
                $therecord["pushrecordid"] = "";
                
                /**
                  *  Here, we check to see if the 'name' field is a pushrecord
                  *  uuid. If so, we need to set the pushrecordid.
                  */
                if($therecord["commandtype"] == 3){
                        
                    $therecord["pushrecordid"] = $therecord["name"];
                    
                    /**
                      *  Need name of the push record 
                      */                        
                    $pushquery = "
                        SELECT
                            `name`
                        FROM
                            `pushrecords`
                        WHERE
                            `uuid` = '".$therecord["name"]."'
                    ";

                    $pushresult = $this->db->query($pushquery);
                    
                    if($this->db->numRows($pushresult)){
                        $pushrecord = $this->db->fetchArray($pushresult);
                        $therecord["displayname"] = $pushrecord["name"];
                    }//end if
                        
                    $therecord["type"] = 2;
                    
                }//end if

                $thereturn[] = $therecord;

            }//end while

            return $thereturn;
            //return $this->db->query($querystatement);

        }//end method


        /**
          *  function showRecords
          *  @param array $recordArray
          */

        function showRecords($recordArray){

            global $phpbms;

            ?><table border="0" cellpadding="3" cellspacing="0" class="querytable">
                <thead>
                    <tr>
                        <th nowrap="nowrap"align="left" width="100%">name</th>
                        <th nowrap="nowrap"align="center">allowed</th>
                        <th nowrap="nowrap"align="center">need select</th>
                        <th nowrap="nowrap"align="left">function name</th>
                        <th nowrap="nowrap"align="left">access</th>
                        <th nowrap="nowrap"align="right">display order</th>
                        <th nowrap="nowrap">&nbsp;</th>
                    </tr>
                </thead>

                <tfoot>
                    <tr class="queryfooter">
                        <td colspan="7">&nbsp;</td>
                    </tr>
                </tfoot>

                <tbody>
                    <?php
                        if(count($recordArray)){

                            $row = 1;

                            $other = 4;

                            foreach($recordArray as $therecord){

                                $row = ($row == 1) ? 2 : 1;

                                    if($therecord["commandtype"] !== $other){
                                        
                                        switch($therecord["commandtype"]){
                                            
                                            case 1:
                                                $title = "Integrated Features";
                                                break;
                                            
                                            case 2:
                                                $title = "Additional Commands";
                                                break;
                                            
                                            case 3:
                                                $title = "Api Commands";
                                                break;
                                            
                                        }//end if

                                        ?><tr class="queryGroup"><td colspan="7"><?php echo $title;?></td></tr><?php

                                        $other = $therecord["commandtype"];

                                    }//end if ?>

                                <tr class="qr<?php echo $row?> noselects">

                                    <td nowrap="nowrap" class="important">
                                    <?php

                                    if($therecord["othercommand"])
                                        echo formatVariable($therecord["option"]);
                                    else
                                        echo formatVariable($therecord["name"]);

                                    ?>
                                    </td>

                                    <td nowrap="nowrap" align="center">
                                        <?php

                                        if($therecord["othercommand"])
                                            echo "&nbsp;";
                                        else
                                            echo formatVariable($therecord["option"], "boolean");

                                        ?>
                                    </td>

                                    <td nowrap="nowrap" align="center">
                                        <?php

                                            if(!$therecord["othercommand"])
                                                echo "&nbsp;";
                                            else
                                                echo formatVariable($therecord["needselect"], "boolean");

                                            ?>
                                    </td>

                                    <td nowrap="nowrap" align="center">
                                        <?php

                                            if($therecord["othercommand"])
                                                echo formatVariable($therecord["displayname"]);
                                            else
                                                echo "&nbsp;";

                                        ?>
                                    </td>

                                    <td nowrap="nowrap">
                                        <?php $phpbms->displayRights($therecord["roleid"], $therecord["rolename"])?>
                                    </td>

                                    <td nowrap="nowrap" align="right">
                                        <?php echo $therecord["displayorder"] ?>
                                    </td>

                                    <td nowrap="nowrap" valign="top">

                                        <button id="edt<?php echo $therecord["id"]?>" type="button" class="graphicButtons buttonEdit"><span>edit</span></button>
                                        <button id="del<?php echo $therecord["id"]?>" type="button" class="graphicButtons buttonDelete"><span>delete</span></button>

                                    </td>
                                </tr>
                        <?php

                            }//endwhile

                        } else {

                           ?><tr class="norecords"><td colspan="6">No Options Set</td></tr><?php

                        }//end if

                        ?>
                        </tbody>

                </table>
                <?php

        }//end function showRecords


        function add($variables){

            if(!isset($variables["ifOption"]))
                $variables["ifOption"] = 0;

            if(!isset($variables["needselect"]))
                $variables["needselect"] = 0;

            switch($variables["type"]){

                case 0:
                    $name = $variables["ifName"];
                    $option = $variables["ifOption"];
                    break;

                case 1:
                    $name = $variables["acName"];
                    $option = $variables["acOption"];
                    break;

                case 2:
                    $name = $variables["pushrecordid"];
                    $option = $variables["acOption"];
                    break;

            }//end switch

            $insertstatement = "
                INSERT INTO
                    tableoptions

                    (
                        tabledefid,
                        name,
                        `option`,
                        roleid,
                        displayorder,
                        othercommand,
                        needselect
                    ) VALUES (
                        '".$this->tabledefuuid."',
                        '".$name."',
                        '".$option."',
                        '".$variables["roleid"]."',
                        ".((int) $variables["displayorder"]).",
                        ".((int) $variables["type"]).",
                        ".((int) $variables["needselect"])."
                    )";

            $this->db->query($insertstatement);

        }//end function add


        function update($variables){

            if(!isset($variables["ifOption"]))
                $variables["ifOption"] = 0;

            if(!isset($variables["needselect"]))
                $variables["needselect"] = 0;

            if($variables["type"])
                $variables["othercommand"] = 1;
            else
                $variables["othercommand"] = 0;

            $updatestatement = "
                UPDATE
                    tableoptions
                SET
                    roleid = '".$variables["roleid"]."',
                    displayorder = ".((int) $variables["displayorder"]).",
                    othercommand = ".((int) $variables["othercommand"]).",
                    needselect = ".((int) $variables["needselect"]).",";

            switch($variables["type"]){

                case 0:
                    $updatestatement .= "
                        name = '".$variables["ifName"]."',
                        `option` = '".$variables["ifOption"]."'
                    ";
                    break;

                case 1:
                    $updatestatement .= "
                        name = '".$variables["acName"]."',
                        `option` = '".$variables["acOption"]."'
                    ";
                    break;

                case 2:
                    $updatestatement .= "
                        `name` = '".$variables["pushrecordid"]."',
                        `option` = '".$variables["acOption"]."'
                    ";
                    break;

            }//end switch

            $updatestatement .= "
                WHERE id =".((int) $variables["id"]);

            $this->db->query($updatestatement);

        }//end function update


        function delete($id){

            $deletestatement = "
                DELETE FROM
                    tableoptions
                WHERE
                    id = ".((int) $id);

            $this->db->query($deletestatement);

        }//end function delete


        function processForm($variables){

            switch($variables["command"]){

                case "add":
                    $this->add($variables);
                    $therecord = $this->getDefaults();
                    $therecord["statusmessage"] = "Option added";
                    break;

                case "edit":
                    $therecord = $this->get($variables["id"]);
                    $therecord = $therecord[0];
                    break;

                case "update":
                    $this->update($variables);
                    $therecord = $this->getDefaults();
                    $therecord["statusmessage"] = "Option updated";
                    break;

                case "delete":
                    $this->delete($variables["id"]);
                    $therecord = $this->getDefaults();
                    $therecord["statusmessage"] = "Option deleted";
                    break;

                case "cancel":
                    $therecord = $this->getDefaults();
                    break;

            }//endswitch

            return $therecord;

        }//end method

	}//end class
?>
