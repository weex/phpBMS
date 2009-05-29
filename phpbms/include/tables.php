<?php
/*
$Rev: 249 $ | $LastChangedBy: brieb $
$LastChangedDate: 2007-07-02 15:50:36 -0600 (Mon, 02 Jul 2007) $
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
    class phpbmsTable{

        var $db = NULL;
        var $backurl = NULL;
        var $verifyErrors = array();
        var $customFieldsQueryResult = false;

        // The table definition record id.
        var $id = 0;

        var $fields = array();


        function phpbmsTable($db, $tabledefid = 0, $backurl = NULL){

            if(is_object($db))
                if(get_class($db)=="db")
                    $this->db = $db;

            if($this->db === NULL)
                $error = new appError(-800,"database object is required for parameter 1.","Initializing phpbmsTable Class");

            $this->id = ((int) $tabledefid);

            if($backurl == NULL)
                $this->backurl = APP_PATH."search.php?id=".$this->id;
            else
                $this->backurl = $backurl;

            if(!$this->getTableInfo())
                $error = new appError(-810,"Table definition not found for id ".$this->id,"Initializing phpbmsTable Class");

        }//end function init


        // gets table definition information
        // based on passed id, and sets class
        // porperties based on results
        function getTableInfo(){

            $querystatement = "
                SELECT
                    *
                FROM
                    `tabledefs`
                WHERE
                    `id` = ".$this->id;

            $queryresult = $this->db->query($querystatement);

            if($this->db->numRows($queryresult)){

                foreach($this->db->fetchArray($queryresult) as $key => $value)
                    $this->$key = $value;

                $this->fields = $this->db->tableInfo($this->maintable);

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
                        $default = strftime("%Y");
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


        // Given a value, and field type, prepare the
        // value for SQL insertion (replacing nul with the SQL string NULL,
        // and typeing variables)
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
                        $value = "'".sqlDateFromString($value)."'";
                    break;

                case "time":
                    if($value === "" or $value === NULL){

                        if(strpos($flags,"not_null") === false)
                            $value = NULL;
                        else
                            $value = "'".timeToString(mktime(),"SQL")."'";

                    } else
                        $value = "'".sqlTimeFromString($value)."'";
                    break;

                case "year":
                    if($value === "" or $value === NULL)
                        if(strpos($flags,"not_null") === false)
                            $value = NULL;
                        else
                            $value = strftime("%Y");
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

                            $date = sqlDateFromString($datetimearray[0]);

                            //times can have spaces... so we need
                            //to resemble in some cases.
                            if(count($datetimearray) > 2)
                                $datetimearray[1] = $datetimearray[1]." ".$datetimearray[2];

                            $time = sqlTimeFromString($datetimearray[1]);

                        }//endif

                        //If we don't have a date, perhaps only a date was passed
                        if(!$date){

                            $date = sqlDateFromString($value);

                            //still no date?, then assume only a time was passed,
                            // so we need to set the time to the deafult
                            // date.
                            if(!$date)
                                $date = "0000-00-00";

                        }//endif

                        //if we don't have a time, let's try the getting the
                        //time from the full value.
                        if(!$time)
                            $time = sqlTimeFromString($value);

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


        // Gets a specific individual record from the table
        function getRecord($id = 0){

            $id = (int) $id;

            $querystatement = "
                SELECT ";

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
                    `".$this->maintable."`.`id` = ".$id;

            $queryresult = $this->db->query($querystatement);

            if($this->db->numRows($queryresult))
                $therecord = $this->db->fetchArray($queryresult);
            else
                $therecord = $this-> getDefaults();

            return $therecord;

        }//end getRecord function


        // This is a placeholder function for preparing variables form a form
        // In most cases it is overriden.
        function prepareVariables($variables){

            if(isset($variables["uuid"]))
                if(!$variables["uuid"])
                    $variables["uuid"] = uuid($this->prefix.":");

            return $variables;

        }//end function prepareVariables


        //verifies if variables passes will constitute a valid record creation/update
        function verifyVariables($variables){

            $thereturn = array();

            if(!isset($this->verifyErrors))
                $this->verifyErrors = array();

            if(isset($variables["id"]))
                if(!is_numeric($variables["id"]) && $variables["id"])
                    $this->verifyErrors[] = "The `id` field must be numeric or equivalent to zero (although positive is reccomended).";

            if(isset($variables["uuid"]))
                if(!$variables["uuid"])
                    $this->verifyErrors[] = "The `uuid` field annot be blank";

            if(isset($variables["inactive"]))
                if($variables["inactive"] && $variables["inactive"] != 1)
                    $this->verifyErrors[] = "The `inactive` field must be a boolean (equivalent to 0 or exactly 1).";

            if(count($this->verifyErrors))
                $thereturn = $this->verifyErrors;

            unset($this->verifyErrors);

            return $thereturn;

        }//end function verifyVariables


        function updateRecord($variables, $modifiedby = NULL, $uuid = false){

            //escape slashes
            $variables = addSlashesToArray($variables);

            if($modifiedby === NULL)
                if(isset($_SESSION["userinfo"]["id"]))
                    $modifiedby = $_SESSION["userinfo"]["id"];
                else
                    $error = new appError(-840,"Session Timed Out.","Updating Record");

            //all updates should have an id
            if(!isset($variables["id"]))
                $error = new appError(-820,"id not set","Updating Record");


            $updatestatement = "
                UPDATE
                    `".$this->maintable."`
                SET ";

            foreach($this->fields as $fieldname => $thefield){
                if(!isset($thefield["select"])){

                    switch($fieldname){

                        case "id":
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

            if(!$uuid){

                $updatestatement .= "
                    WHERE
                        `id`=".((int) $variables["id"]);


            } else {

                $updatestatement .= "
                    WHERE
                        `uuid` = '".mysql_real_escape_string($variables["id"])."'";

            }//endif

            $this->db->query($updatestatement);

            return true;

        }//end function updateRecord


        function insertRecord($variables,$createdby = NULL, $overrideID = false, $replace = false, $uuid = false){

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
                            if(isset($variables["id"]))
                                if($overrideID && $variables["id"]){

                                    $fieldlist .= "id, ";
                                    $insertvalues .= ((int) $variables["id"]).", ";

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

            $fieldlist = substr($fieldlist, 0, strlen($fieldlist)-2);
            $insertvalues = substr($insertvalues, 0, strlen($insertvalues)-2);

            if($replace)
                $insertstatement = "REPLACE";
            else
                $insertstatement = "INSERT";

            $insertstatement .= " INTO ".$this->maintable." (".$fieldlist.") VALUES (".$insertvalues.")";
            $insertresult = $this->db->query($insertstatement);

            if($insertresult) {

                if($uuid)
                    return $variables["uuid"];
                else
                    return $this->db->insertId();

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
                        `tabledefid` = ".$this->id."
                    ORDER BY
                        `displayorder`";

                $this->customFieldsQueryResult = $this->db->query($querystatement);

            }//end if

        }//end function getCustomFieldInfo

    }//end class
?>
