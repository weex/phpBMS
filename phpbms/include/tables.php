<?php
/*
$Rev: 249 $ | $LastChangedBy: brieb $
$LastChangedDate: 2007-07-02 15:50:36 -0600 (Mon, 02 Jul 2007) $
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
    class phpbmsTable{

        var $db = NULL;
        var $backurl = NULL;
        var $verifyErrors = array();
        var $customFieldsQueryResult = false;

        var $id;
        var $uuid;
        var $fields = array();

        var $payemnts;

        /**
          *  $dateFormat
          *
          *  @var The format of dates being passed to the insert/updates
          */
        var $dateFormat = "";

        /**
          *  $timeFormat
          *
          *  @var The format of times being passed to the insert/updates
          */
        var $timeFormat = "";

        /**
          *  $encryptedFields
          *
          *  @var array A list of field names that are encrypted.  This affects
          *  the getRecord, insertRecord, and updateRecord.
          */
        var $encryptedFields = array();


        /**
         * Initializes phpBMS Table object
         *
         * @param object $db database object
         * @param string $tabledefid table definition's uuid
         * @param string $backurl string with the web URL of where to redirect
         */
        function phpbmsTable($db, $tabledefid, $backurl = NULL){

            if(is_object($db))
                if(get_class($db) == "db")
                    $this->db = $db;

            if($this->db === NULL)
                $error = new appError(-800,"database object is required for parameter 1.","Initializing phpbmsTable Class");

            $this->uuid = mysql_real_escape_string($tabledefid);

            if(!$this->getTableInfo())
                $error = new appError(-810,"Table definition not found for id ".$this->id,"Initializing phpbmsTable Class");

            if($backurl == NULL)
                $this->backurl = APP_PATH."search.php?id=".urlencode($this->uuid);
            else
                $this->backurl = $backurl;

            if(defined("DATE_FORMAT"))
                $this->dateFormat = DATE_FORMAT;

            if(defined("TIME_FORMAT"))
                $this->timeFormat = TIME_FORMAT;

        }//end function init


        /**
         * rerieves table definition information and creates object variables of the data retrieved
         */
        function getTableInfo(){

            $querystatement = "
                SELECT
                    *
                FROM
                    `tabledefs`
                WHERE
                    `uuid` = '".$this->uuid."'";

            $queryresult = $this->db->query($querystatement);

            if($this->db->numRows($queryresult)){

                foreach($this->db->fetchArray($queryresult) as $key => $value)
                    $this->$key = $value;

                $this->fields = $this->db->tableInfo($this->maintable);

                foreach($this->encryptedFields as $encryptFieldname)
                    $this->fields[$encryptFieldname]["select"] = $this->db->decrypt("`".$encryptFieldname."`");

                return true;

            } else
                return false;

        }//end function getTableInfo

        // gets a default value for a field
        // given it's type
        function getDefaultByType($fieldtype){

            switch ($fieldtype){

                    case "blob":
                    case "string":
                        $default = "";
                        break;

                    case "real":
                    case "int":
                        $default = 0;
                        break;

                    case "date":
                        $default = dateToString(mktime(),"SQL");
                        break;

                    case "time":
                        $default = timeToString(mktime(),"SQL");
                        break;

                    case "year":
                        $default = @strftime("%Y");
                        break;

                    case "datetime":
                    case "timestamp":
                        $default = dateToString(mktime(),"SQL")." ".timeToString(mktime(),"24 Hour");
                        break;

                    default:
                        $default = null;

            }//endswitch

            return $default;

        }//end function getDefaultsByType



        /**
          *  function prepareFieldForSQL
          *
          *  Given a value, and field type, prepare the
          *  value for SQL insertion (replacing nul with the SQL string NULL,
          *  and typeing variables)
          *
          *  @param string/int $value to be prepared.
          *  @param string $type mysql field type
          *  @param string $flags A list of flags seperated by spaces (" ").
          */
        function prepareFieldForSQL($value,$type,$flags){

            switch ($type){

                case "blob":
                case "string":
                    if($value === "" or $value === NULL){

                        if(strpos($flags,"not_null") === false)
                            $value = NULL;
                        else
                            $value = "''";

                    } else
                        $value = "'".$value."'";
                    break;

                case "real":
                    if($value === "" or $value === NULL){

                        if(strpos($flags,"not_null") === false)
                            $value = NULL;
                        else
                            $value = 0;

                    } else
                        $value = (real) $value;
                    break;

                case "int":
                    if($value === "" or $value === NULL){

                        if(strpos($flags,"not_null") === false)
                            $value = NULL;
                        else
                            $value = 0;

                    } else
                        $value = (int) $value;
                    break;

                case "date":
                    if($value === "" or $value === NULL){

                        if(strpos($flags,"not_null") === false)
                            $value = NULL;
                        else
                            $value = "'".dateToString(mktime(),"SQL")."'";
                    } else
                        $value = "'".sqlDateFromString($value, $this->dateFormat)."'";
                    break;

                case "time":
                    if($value === "" or $value === NULL){

                        if(strpos($flags,"not_null") === false)
                            $value = NULL;
                        else
                            $value = "'".timeToString(mktime(),"SQL")."'";

                    } else
                        $value = "'".sqlTimeFromString($value, $this->timeFormat)."'";
                    break;

                case "year":
                    if($value === "" or $value === NULL)
                        if(strpos($flags,"not_null") === false)
                            $value = NULL;
                        else
                            $value = @strftime("%Y");
                    break;

                case "datetime":
                case "timestamp":
                    if($value === "" or $value === NULL){

                        if(strpos($flags,"not_null") === false)
                            $value = NULL;
                        else
                            $value = "'".dateToString(mktime(),"SQL")." ".timeToString(mktime(),"24 Hour")."'";

                    } else {

                        $datetimearray = explode(" ",$value);
                        $date = null;
                        $time = null;

                        //If the value can be split by spaces we assume we
                        // are looking at a "date time"
                        if(count($datetimearray) > 1){

                            $date = sqlDateFromString($datetimearray[0], $this->dateFormat);

                            //times can have spaces... so we need
                            //to resemble in some cases.
                            if(count($datetimearray) > 2)
                                $datetimearray[1] = $datetimearray[1]." ".$datetimearray[2];

                            $time = sqlTimeFromString($datetimearray[1], $this->timeFormat);

                        }//endif

                        //If we don't have a date, perhaps only a date was passed
                        if(!$date){

                            $date = sqlDateFromString($value, $this->dateFormat);

                            //still no date?, then assume only a time was passed,
                            // so we need to set the time to the deafult
                            // date.
                            if(!$date)
                                $date = "0000-00-00";

                        }//endif

                        //if we don't have a time, let's try the getting the
                        //time from the full value.
                        if(!$time)
                            $time = sqlTimeFromString($value, $this->timeFormat);

                        $value = "'".trim($date." ".$time)."'";

                    }//end if

                    break;

                case "password":
                    $value = "ENCODE('".$value."','".ENCRYPTION_SEED."')";
                    break;

            }//end switch

            if($value === NULL)
                $value = "NULL";

            return $value;

        }//end function prepareFieldForSQL


        /**
          * Retrieves default values for a single record
          *
          * Uses the field names to guess a default value.  If it cannot find
          * one of the standard names it sets the default value based on the type
          *
          * @retrun array associative array with record defaults
          */
        function getDefaults(){

            $therecord = array();

            foreach($this->fields as $fieldname => $thefield){

                switch($fieldname){

                    case "id":
                    case "modifiedby":
                    case "modifieddate":
                        $therecord[$fieldname] = NULL;
                        break;

                    case "uuid":
                        $therecord["uuid"] = uuid($this->prefix.":");
                        break;

                    case "createdby":
                        $therecord["createdby"] = $_SESSION["userinfo"]["id"];
                        break;

                    default:
                        if(strpos($thefield["flags"],"not_null") === false)
                            $therecord[$fieldname] = NULL;
                        else
                            $therecord[$fieldname] = $this->getDefaultByType($thefield["type"]);

                        break;

                }//endswitch

            }//endforeach

            return $therecord;

        }//end function getDefaults


        /**
         * Retrieves a single record from the database
         *
         * @param integer|string $id the record id or uuid
         * @param bool $useUuid specifies whther the $id is a uuid (true) or not.  Default is false
         *
         * @return array the record as an associative array
         */
        function getRecord($id, $useUuid = false){

            $whereclause = "`".$this->maintable."`.";

            if($useUuid){

                $id = mysql_real_escape_string($id);
                $whereclause .= "`uuid` = '".$id."'";

            } else {

                $id = (int) $id;
                $whereclause .= "`id` = ".$id;

            }//endif

            // iterate through all possible fields and comprise a list
            // of columns to retrieve

            $fieldlist = "";
            foreach($this->fields as $fieldname => $thefield){

                if(isset($thefield["select"]))
                    $fieldlist .= ", (".$thefield["select"].") AS `".$fieldname."`";
                else
                    $fieldlist .= ", `".$fieldname."`";

            }//end foreach

            if($fieldlist)
                $fieldlist = substr($fieldlist, 1);


            $querystatement = "
                SELECT
                    ".$fieldlist."
                FROM
                    `".$this->maintable."`
                WHERE
                    ".$whereclause;

            $queryresult = $this->db->query($querystatement);

            if($this->db->numRows($queryresult))
                $therecord = $this->db->fetchArray($queryresult);
            else
                $therecord = $this-> getDefaults();

            return $therecord;

        }//end getRecord function


        /**
         * prepares variables (usually from a form) for inserting or updating
         *
         * @param array $variables associative array with the record information
         *
         * @return array associative array with the record information 'prepped'
         */
        function prepareVariables($variables){

            if(isset($variables["uuid"]))
                if(!$variables["uuid"])
                    $variables["uuid"] = uuid($this->prefix.":");

            return $variables;

        }//end function prepareVariables


        /**
         * function _loadUUIDList
         *
         * @param string $tableName The name of a table with `uuid` field.
         *
         * @return array A list of uuids used in the table.
         */
        function _loadUUIDList($tableName) {

            $list = array();
            $tableName = mysql_real_escape_string($tableName);

            $querystatement = "
                SELECT
                    `uuid`
                FROM
                    `".$tableName."`
                ";

            $queryresult = $this->db->query($querystatement);

            while($therecord = $this->db->fetchArray($queryresult))
                $list[] = $therecord["uuid"];

            return $list;

        }//end method --_loadUUIDList--


        /**
         * function _checkForPresentUUID
         *
         * @param string $tableName The name of a table with `uuid` field.
         * @param string $uuid The uuid to be checked.
         *
         * @return boolean Whether or not the $uuid is a `uuid` in $tablename.
         */
        function _checkForValidUUID($tableName, $uuid) {

            $tableName = mysql_real_escape_string($tableName);
            $uuid = mysql_real_escape_string($uuid);

            $querystatement = "
                SELECT
                    `uuid`
                FROM
                    `".$tableName."`
                WHERE
                    `uuid` = '".$uuid."'
                ";

            $queryresult = $this->db->query($querystatement);

            if($this->db->numRows($queryresult))
                return true;
            else
                return false;

        }//end method --_checkForValidUUID--


        //verifies if variables passes will constitute a valid record creation/update
        function verifyVariables($variables){

            $thereturn = array();

            if(!isset($this->verifyErrors))
                $this->verifyErrors = array();

            if(isset($variables["id"]))
                if(!is_numeric($variables["id"]) && $variables["id"])
                    $this->verifyErrors[] = "The `id` field must be numeric or equivalent to zero (although positive is recommended).";

            if(isset($variables["uuid"]))
                if(!$variables["uuid"])
                    $this->verifyErrors[] = "The `uuid` field cannot be blank";

            if(isset($variables["inactive"]))
                if($variables["inactive"] && $variables["inactive"] != 1)
                    $this->verifyErrors[] = "The `inactive` field must be a boolean (equivalent to 0 or exactly 1).";

            if(count($this->verifyErrors))
                $thereturn = $this->verifyErrors;

            unset($this->verifyErrors);

            return $thereturn;

        }//end function verifyVariables


        /**
         * function updateRecord
         *
         * updates a record
         *
         * @param array $variables associative array with the record information
         * @param int|NULL $modifiedby The user's id that modiied the record.
         *                             If the modified is not passed or is NULL the function will use
         *                             the currently logged in user.
         * @param bool $useUuid specifies whther to use the id or the uuid (true) in the whereclause.  Default is false.
         *
         * @return bool true or false depending upon update success
         */
        function updateRecord($variables, $modifiedby = NULL, $useUuid = false){

            //escape slashes
            $variables = addSlashesToArray($variables);

            // if no modified by was passed, use the currently logged in user
            if($modifiedby === NULL)
                if(isset($_SESSION["userinfo"]["id"]))
                    $modifiedby = $_SESSION["userinfo"]["id"];
                else
                    $error = new appError(-840,"Session Timed Out.","Updating Record");


            $updatestatement = "
                UPDATE
                    `".$this->maintable."`
                SET ";

            foreach($this->fields as $fieldname => $thefield){
                if(!isset($thefield["select"])){

                    switch($fieldname){

                        case "id":
                        case "uuid":
                        case "creationdate":
                        case "createdby":
                            break;

                        case "modifiedby":
                            $updatestatement .= "`modifiedby` = ".((int) $modifiedby).", ";
                            break;

                        case "modifieddate":
                            $updatestatement .= "`modifieddate` = NOW(), ";
                            break;

                        default:
                            if(!isset($variables[$fieldname]) && strpos($thefield["flags"],"not_null") !== false)
                                $variables[$fieldname] = $this->getDefaultByType($thefield["type"],true);

                            if(isset($variables[$fieldname]))
                                $updatestatement .= "`".$fieldname."` = ".$this->prepareFieldForSQL($variables[$fieldname],$thefield["type"],$thefield["flags"]).", ";
                            break;

                    }//end switch field name

                }//end if
            }//end foreach

            $updatestatement = substr($updatestatement, 0, strlen($updatestatement)-2);

            if(!$useUuid){

                if(!isset($variables["id"]))
                    $variables["id"] = 0;

                $updatestatement .= "
                    WHERE
                        `id`=".((int) $variables["id"]);

            } else {

                if(!isset($variables["uuid"]))
                    $variables["uuid"] = '';

                $updatestatement .= "
                    WHERE
                        `uuid` = '".mysql_real_escape_string($variables["uuid"])."'";

            }//endif

            $this->db->query($updatestatement);

            return true;

        }//end function updateRecord


        /**
         * Inserts a new record into the table.
         *
         * @param array $variables associaive array with the record information
         * @param int $createdby id of the user creating the record.  If NULL (default) it will use the currently logged in user
         * @param bool $overrideID Whether to override the `id` field with the new given value (if it exists)
         * @param bool $replace use the SQL replace statement (true) instead of insert (false, deault)
         * @param bool $useUuid generates a uuid and specifies the function to retrn an array with uuid and id instead of just the id
         *
         * @return int|array|bool retruns the id of the newly created record of false on error.  If $useUuid is set to true, it will
         *                        return an associaive array with both the new uuid and new id.
         */
        function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false, $useUuid = false){

            if($createdby === NULL)
                if(isset($_SESSION["userinfo"]["id"]))
                    $createdby = $_SESSION["userinfo"]["id"];
                else
                    $error = new appError(-840,"Session Timed Out.","Creating New Record");

            $variables = addSlashesToArray($variables);

            $fieldlist = "";
            $insertvalues = "";

            foreach($this->fields as $fieldname => $thefield){

                if(!isset($thefield["select"])){

                    switch($fieldname){
                        case "id":
                            if($overrideID && isset($variables["id"])){

                                if($variables["id"]){
                                    $fieldlist .= "id, ";
                                    $insertvalues .= ((int) $variables["id"]).", ";
                                }//end if

                            }//endif

                            break;

                        case "uuid":
                            if(!$useUuid){

                                $fieldlist .= "`uuid`, ";
                                $insertvalues .= "'".mysql_real_escape_string($variables["uuid"])."', ";

                            }//endif
                            break;

                        case "createdby":
                        case "modifiedby":
                            $fieldlist .= $fieldname.", ";
                            $insertvalues .= ((int) $createdby).", ";
                            break;

                        case "creationdate":
                        case "modifieddate":
                            $fieldlist .= $fieldname.", ";
                            $insertvalues .= "NOW(), ";
                            break;

                        default:

                            if(!isset($variables[$fieldname]) && strpos($thefield["flags"],"not_null") !== false)
                                $variables[$fieldname] = $this->getDefaultByType($thefield["type"],true);

                            if(isset($variables[$fieldname])){

                                $fieldlist .= "`".$fieldname."`, ";
                                $insertvalues .= $this->prepareFieldForSQL($variables[$fieldname],$thefield["type"],$thefield["flags"]).", ";

                            }//endif - fieldname

                            break;

                    }//end switch field name

                }//end if

            }//end foreach

            //generate uuid
            if($useUuid && isset($this->fields["uuid"])){

                $fieldlist .= "`uuid`, ";
                $variables["uuid"] = uuid($this->prefix.":");
                $insertvalues .= "'".$variables["uuid"]."', ";

            }//endif

            $fieldlist = substr($fieldlist, 0, strlen($fieldlist)-2);
            $insertvalues = substr($insertvalues, 0, strlen($insertvalues)-2);

            if($replace)
                $insertstatement = "REPLACE";
            else
                $insertstatement = "INSERT";

            $insertstatement .= " INTO ".$this->maintable." (".$fieldlist.") VALUES (".$insertvalues.")";

            $insertresult = $this->db->query($insertstatement);

            if($insertresult) {

                $newid = $this->db->insertId();

                if($useUuid)
                    return array("uuid" => $variables["uuid"], "id" => $newid);
                else
                    return $newid;

            } else
                return false;

        }//end function insertRecord


        // default stucture for processing an addedit page.  It assumes that the
        // page receives it's information through a post, and that it at least
        // includes a command parameter.  The presence of an id field value
        // dictates wehter the proess should insert or update a record
        function processAddEditPage(){

            // no command parameter present?
            if(!isset($_POST["command"])){

                // assuming just entered the page (no POST)
                // presence of a GET id means editing an existing record
                // (vs. creating a new)
                if(isset($_GET["id"])){

                    //editing... make sure they have access to edit
                    if(!hasRights($this->editroleid))
                        goURL(APP_PATH."noaccess.php");
                    else {

                        $this->getCustomFieldInfo();
                        return $this->getRecord((integer) $_GET["id"]);

                    }//endif

                } else {

                    //creating new record
                    if(!hasRights($this->addroleid))
                        goURL(APP_PATH."noaccess.php");
                    else {

                        $this->getCustomFieldInfo();
                        return $this->getDefaults();

                    }//endif

                }//end if GET-id

            } else {

                // command present
                switch($_POST["command"]){

                    //pressed the cancel button
                    case "cancel":

                        // if we needed to do any clean up (deleteing temp line items)
                        if(!isset($_POST["id"])) $_POST["id"]=0;

                        $theurl = $this->backurl;

                        if(isset($_POST["id"]))
                                $theurl .= "#".((int) $_POST["id"]);

                        goURL($theurl);
                        break;

                    case "save":

                        $variables = $this->prepareVariables($_POST);
                        $errorArray = $this->verifyVariables($variables);

                        if($_POST["id"]) {

                            $theid = $variables["id"];

                            if(!count($errorArray)){

                                $this->updateRecord($variables);
                                if(isset($variables["getid"]))
                                    if(is_numeric($variables["getid"]))
                                        $theid = (int) $variables["getid"];// special variable to override the
                                        //id for get record

                                //get record
                                $this->getCustomFieldInfo();
                                $therecord = $this->getRecord($theid);
                                $therecord["phpbmsStatus"] = "Record Updated";

                            } else {

                                foreach($errorArray as $error)
                                    $logError = new appError(-900, $error, "Verification Error");

                                //get record
                                $this->getCustomFieldInfo();
                                $therecord = $this->getRecord($theid);
                                $therecord["phpbmsStatus"] = "Data Verification Error";

                            }//end if

                            return $therecord;

                        } else {

                            $theid = 0;

                            if(!count($errorArray)){

                                $theid = $this->insertRecord($variables);
                                //get record
                                $therecord = $this->getRecord($theid);
                                $therecord["phpbmsStatus"] = "<div style=\"float:right;margin-top:-3px;\"><button type=\"button\" accesskey=\"n\" class=\"smallButtons\" onclick=\"document.location='".str_replace("&","&amp;",$_SERVER["REQUEST_URI"])."'\">add new</button></div>";
                                $therecord["phpbmsStatus"] .= "Record Created";

                            } else {

                                foreach($errorArray as $error)
                                    $logError = new appError(-900, $error, "Verification Error");

                                //get record
                                $therecord = $this->getRecord($theid);
                                $therecord["phpbmsStatus"] .= "Data Verification Error";

                            }//end if

                            return $therecord;

                        }//endif

                        break;

                }//end command switch

            }// end if - command present

        }//end function processAddEditPage


        function getCustomFieldInfo(){

            if($this->hascustomfields){

                $querystatement = "
                    SELECT
                        `tabledefid`,
                        `field`,
                        `name`,
                        `format`,
                        `generator`,
                        `roleid`,
                        `required`
                    FROM
                        `tablecustomfields`
                    WHERE
                        `tabledefid` = '".$this->uuid."'
                    ORDER BY
                        `displayorder`";

                $this->customFieldsQueryResult = $this->db->query($querystatement);

            }//end if

        }//end function getCustomFieldInfo

    }//end class
?>
