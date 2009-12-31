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
if(class_exists("phpbmsTable")) {
    class reports extends phpbmsTable{

        var $_availableTabledefUUIDs = NULL;
        var $_availableRoleUUIDs = NULL;

        function getDefaults(){

            $therecord = parent::getDefaults();

            $therecord["type"] = "report";

            return $therecord;

        }//end function getDefaults


        function verifyVariables($variables){

            //cannot be table default ("")
            if(isset($variables["reportfile"])){
                if($variables["reportfile"] === "" || $variables["reportfile"] === NULL)
                    $this->verifyErrors[] = "The `reportfile` field must not be blank.";
            }else
                $this->verifyErrors[] = "The `reportfile` field must be set.";

            //Table default (NULL) OK
            if(isset($variables["type"]))
                if($variables["type"] !== "")//don't care if it's ""
                    switch($variables["type"]){

                            case "report":
                            case "PDF Report":
                            case "export":
                                break;

                            default:
                                $this->verifyErrors[] = "The `type` field is not an accepted value.  It must be 'report', 'PDF Report', or 'export.";
                            break;

                    }//end switch

            //Table Default ('') ok becuase it means report is globally available to any table
            if(isset($variables["tabledefid"])){

                if($this->_availableTabledefUUIDs === NULL){

                    $this->_availableTabledefUUIDs = $this->_loadUUIDList("tabledefs");

                    //add the global option
                    $this->_availableTabledefUUIDs[] = "";

                }//end if

                if( !in_array((string)$variables["tabledefid"], $this->_availableTabledefUUIDs) )
                    $this->verifyErrors[] = "The `tabledefid` field does not give an existing/acceptable table definition uuid.";

            }//end if

            //Table Default ('') ok becuase it means report is globally available to any user
            if(isset($variables["roleid"])){

                if($this->_availableRoleUUIDs === NULL){

                    $this->_availableRoleUUIDs = $this->_loadUUIDList("roles");
                    $this->_availableRoleUUIDs[] = ""; // for no role restrictions
                    $this->_availableRoleUUIDs[] = "Admin"; //for the Admin restriction

                }//end if

                if( !in_array((string)$variables["roleid"], $this->_availableRoleUUIDs) )
                    $this->verifyErrors[] = "The `roleid` field does not give an existing/acceptable to role id number.";

            }//end if

            return parent::verifyVariables($variables);

        }//end method


        function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false, $useUuid = false){

            $newid = parent::insertRecord($variables, $createdby, $overrideID, $replace, $useUuid);

            $reportSettings = new reportSettings($this->db, $variables["uuid"]);
            $reportSettings->createFromDefaults($variables["reportfile"]);

            return $newid;

        }//end method


        function updateRecord($variables, $modifiedby = NULL, $useUuid = false){

            parent::updateRecord($variables, $modifiedby, $useUuid);

            $reportSettings = new reportSettings($this->db, $variables["uuid"]);
            $reportSettings->save($variables["rsDelList"], $variables["rsUpdates"], $variables["rsAdds"]);

        }//end method updateRecord


        function displayTables($fieldname,$selectedid){

            $querystatement="SELECT uuid, displayname FROM tabledefs ORDER BY displayname";
            $thequery=$this->db->query($querystatement);

            echo "<select id=\"".$fieldname."\" name=\"".$fieldname."\">\n";

            echo "<option value=\"\" ";
            if ($selectedid=="") echo "selected=\"selected\"";
            echo " style=\"font-weight:bold\">global</option>\n";

            while($therecord=$this->db->fetchArray($thequery)){
                    echo "	<option value=\"".$therecord["uuid"]."\"";
                            if($selectedid==$therecord["uuid"]) echo " selected=\"selected\"";
                    echo ">".$therecord["displayname"]."</option>\n";
            }

            echo "</select>\n";

        }//end method


        /**
         * function displayFiles
         *
         * Displays a list of possible report filenames.  This includes
         * all PHP files in the main report folder, as well as in the report
         * folder of any loaded module
         */
        function displayRerportFiles(){

            $files = array();

            $curDir = getcwd();

            chdir("../..");

            /**
             * Load core reports
             */
            if(file_exists("report") && is_dir("report")){

                $thedir = @ opendir("report");

                    while($entry = @ readdir($thedir))
                        if(@ strtolower(substr($entry, -4)) == ".php")
                            $files[] = "report/".$entry;

            }//endif

            chdir("modules");

            /**
             * Get loaded modules
             */
            $querystatement = "
                SELECT
                    `name`
                FROM
                    `modules`";

            $queryresult = $this->db->query($querystatement);

            while($therecord = $this->db->fetchArray($queryresult)){

                chdir($therecord["name"]);

                $thedir = @ opendir("report");

                    while($entry = @ readdir($thedir))
                        if(@ strtolower(substr($entry, -4)) == ".php")
                            $files[] = "modules/".$therecord["name"]."/report/".$entry;

                chdir("..");

            }//endwhile

            chdir($curDir);

            ?><label for="reportfile">report file</label><br />
            <select id="reportfile" name="reportfile">
                <?php foreach($files as $filename){?>
                    <option value="<?php echo $filename; ?>"><?php echo $filename; ?></option>
                <?php }//endforeach?>
            </select><?php

        }//end function displayReportFiles

    }//end class



    /**
     * handles retrieval, display and saving of report settings records
     */
    class reportSettings{

        /**
         * $db
         * @var object the database object
         */
        var $db;

        /**
         * $reportUUID
         * @var string report UUID
         */
        var $reportUUID;

        /**
         * $settingsQueryResult
         * @var int query result reference
         */
        var $settingsQueryResult;


        /**
         * function reportSettings
         *
         * Class initializer
         *
         * @param object $db database object
         * @param string $reportUUID associated report's UUID
         */
        function reportSettings($db, $reportUUID){

            $this->db = $db;
            $this->reportUUID = $reportUUID;

        }//end function reportSettings (init)


        /**
         * function get
         *
         * retrieves query result for all report settings asociated with report
         */
        function get(){

            $querystatement = "
                SELECT
                    `id`,
                    `name`,
                    `value`,
                    `type`,
                    `required`,
                    `description`
                FROM
                    `reportsettings`
                WHERE
                    `reportuuid` = '".$this->reportUUID."'";

            $this->settingsQueryResult = $this->db->query($querystatement);

        }//end function get


        /**
         * function display
         *
         * Display all settings records one TR for each
         */
        function display(){

            if($this->db->numRows($this->settingsQueryResult) == 0){

                ?><tr class="norecords" id="noSettings"><td colspan="5">No Settings</td></tr><?php

                return;

            }//endif

            $row = 1;

            while($therecord = $this->db->fetchArray($this->settingsQueryResult)){

                ?>
                <tr class="qr<?php echo $row; ?> rsRows" id="rsExRow<?php echo $therecord["id"] ?>">
                    <td align="right">
                        <strong><?php echo formatVariable($therecord["name"]) ?></strong>
                        <input class="rsNames" id="rsName<?php echo $therecord["id"]?>" type="hidden" value="<?php echo formatVariable($therecord["name"]) ?>" />
                    </td>
                    <td>
                        <?php if($therecord["type"] != "text") {?>
                        <input class="rsValues" id="rsValue<?php echo $therecord["id"] ?>" type="text" size="32" value="<?php echo formatVariable(addcslashes($therecord["value"],"\\\n\t\r")) ?>" />
                        <?php } else {
                            ?>
                            <textarea class="rsValues" id="rsValue<?php echo $therecord["id"] ?>" rows="2" cols="29"><?php echo formatVariable(addcslashes($therecord["value"],"\\\n\t\r")) ?></textarea>
                            <?php
                        }?>
                    </td>
                    <td><?php echo formatVariable($therecord["type"]) ?></td>
                    <td><?php echo formatVariable($therecord["description"]) ?></td>
                    <td><?php
                        if($therecord["required"] != 1){

                            ?><button type="button" id="rsDelButton<?php echo $therecord["id"]?>" class="graphicButtons buttonMinus rsDelButtons" title="Remove Setting"><span>-</span></button><?php

                        }//endif
                    ?></td>
                </tr>
                <?php

                $row = ($row==1) ? 2 : 1;

            }//endwhile

        }//end function display


        /**
         * function save
         *
         * saves report settings changes
         *
         * @param string $delList JSON string of ids to be deleted
         * @param string $updateList JSON string of updates to be made
         * @param string $addList JSON string of name/value pairs to be added
         */
        function save($delList, $updateList, $addList){

            $delList = json_decode(stripslashes($delList));
            if(count($delList)){

                $inClause = "";

                foreach($delList as $id){

                    $inClause .= ", ".$id;

                }//endforeach

                $deletestatement = "
                    DELETE FROM
                        `reportsettings`
                    WHERE
                        `id` IN (".substr($inClause, 1).")";

                $this->db->query($deletestatement);

            }//endif

            $updateList = str_replace("\n", "\\\\n", $updateList);
            $updateList = str_replace("\r", "", $updateList);
            $updateList = json_decode(stripslashes($updateList));

            foreach($updateList as $updateObj){

                $updatestatement = '
                    UPDATE
                        `reportsettings`
                    SET
                        `value` = "'.mysql_real_escape_string($updateObj->value).'"
                    WHERE
                        `id` = '.((int) $updateObj->id);

                $this->db->query($updatestatement);

            }//endforeach


            $addList = str_replace("\n", "\\\\n", $addList);
            $addList = str_replace("\r", "", $addList);
            $addList = json_decode(stripslashes($addList));

            foreach($addList as $addObj){

                $insertstatement = '
                    INSERT INTO
                        `reportsettings`
                    (
                        `reportuuid`,
                        `name`,
                         `value`
                    ) VALUES (
                        "'.$this->reportUUID.'",
                        "'.mysql_real_escape_string($addObj->name).'",
                        "'.mysql_real_escape_string($addObj->value).'"
                    )';

                $this->db->query($insertstatement);

            }//endforeach

        }//end function save


        /**
         * function createFromDefaults
         *
         * Creates reportsettings records for report based on defaults from the class
         * instanciated by filename
         *
         * @param string $filename file name of report file to retrieve class from
         */
        function createFromDefaults($filename){

            $addingReportRecord = true;
            $noOutput = true;

            include_once("report/report_class.php");

            include($filename);

            if(!isset($reportClass))
                $error = new appError(200, "Report file is missing reportClass definition", "Report File Error");
            else
                $report = new $reportClass($this->db, $this->reportUUID, "tbld:d595ef42-db9d-2233-1b9b-11dfd0db9cbb");

            $settings = $report->addingRecordDefaultSettings();

            $startInsertStatement = "
                    INSERT INTO
                        `reportsettings`
                    (
                        `reportuuid`,
                        `name`,
                        `value`,
                        `type`,
                        `required`,
                        `defaultvalue`,
                        `description`
                    ) VALUES (
                        '".$this->reportUUID."',";

            foreach($settings as $setting){

                $insertstatement = $startInsertStatement;
                $insertstatement .= "'".$setting["name"]."', " ;
                $insertstatement .= "'".$setting["defaultValue"]."', " ;
                $insertstatement .= "'".$setting["type"]."', " ;
                $insertstatement .= ((int) $setting["required"]).", " ;
                $insertstatement .= "'".$setting["defaultValue"]."', " ;
                $insertstatement .= "'".$setting["description"]."')" ;

                $this->db->query($insertstatement);

            }//endforeach

        }//end function createFromDefaults

    }//end class

}//end if

if(class_exists("searchFunctions")){

    class tabledefsSearchFunctions extends searchFunctions{

        function delete_record($useUUID = false){

            if(!$useUUID){

                $whereclause = $this->buildWhereClause();
                //support tables link to tabledefs using uuids not ids, so we must make sure that they are uuids.
                $this->idsArray = getUuidArray($this->db, "tbld:d595ef42-db9d-2233-1b9b-11dfd0db9cbb", $this->idsArray);

            }else
                $whereclause = $this->buildWhereClause($this->maintable.".uuid");

            $linkedwhereclause = $this->buildWhereClause("reportuuid");

            $querystatement = "DELETE FROM reportsettings WHERE ".$linkedwhereclause.";";
            $queryresult = $this->db->query($querystatement);

            $querystatement = "DELETE FROM reports WHERE ".$whereclause.";";
            $queryresult = $this->db->query($querystatement);

            $message = $this->buildStatusMessage();
            $message.=" deleted.";
            return $message;

        }//end function delete_record

    }//end class

}//end if
?>
