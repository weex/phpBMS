<?php
/*
 $Rev: 285 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-27 14:05:27 -0600 (Mon, 27 Aug 2007) $
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

/**
 * API level processing
 *
 * The api class handles processing and format conversion for API calls to the
 * system.
 * @author Brian Rieb <brieb@kreotek.com>
 */
class api{

    /**
     * phpBMS DB object
     * @var object
     */
    var $db;

    /**
     * incoming data format.
     * @var string
     */
    var $format;

    /**
     * decoded data passed by the post
     * @var array
     */
    var $data = array();

    /**
     * array of response to be displayed either after processing has completed
     * or when a fatal error occurs
     * @var array
     */
    var $response = array();

    /**
      *  $options
      *  @var object Object containing the current request's options
      */
    var $options;

    /**
     * function api
     *
     * Constructor sets up {@link $db}, decodes {@link $data} using the passed
     * format from {@link $format}
     *
     * @param db object $db database object
     * @param array $data data that is to be decoded
     * @param string $format format of the data
     */
    function api($db, $data, $format="json"){

        $this->db = $db;
        $this->db->errorFormat = $format;

        $this->format = $format;

        $this->data = $this->decode($data);

    }//end function init (api)


    /**
     * function decode
     * decodes data (usually the passed post data) depending on the {@link $format}
     *
     * Currently, this function can only decode JSON data, but support for SOAP,
     * generic XML, or some other format may be added
     *
     * @param string $data Information to be decoded
     *
     * @return array
     */
    function decode($data){

        if(get_magic_quotes_runtime() || get_magic_quotes_gpc())
            $data = stripslashes($data);

        switch($this->format){

            case "json":
                $data = json_decode($data, true);
                break;

        }//endswitch

        return $data;

    }//end function decode


    /**
     * function encode
     * encodes data (usually {@link $request}) depending on the {@link $format}
     *
     * Currently, this function can only encode JSON data, but support for SOAP,
     * generic XML, or some other format may be added
     *
     * @param string $data Information to be encoded
     */
    function encode($data){

        switch($this->format){

            case "json":
                $data = json_encode($data);
                break;

        }//endswitch

        return $data;

    }//end function encode


    /**
     * function isValidDateFormat
     *
     * @param $format Format to be checked.
     *
     * @return bool True if it is valid, false if not.
     */

    function isValidDateFormat($format) {

        switch($format){

            case "SQL":
            case "English, US":
            case "English, UK":
            case "Dutch, NL":
                $valid = true;
            break;

            default:
                $valid = false;
            break;

        }//end switch

        return $valid;

    }//end function isValidTimeFormat


    /**
     * function isValidTimeFormat
     *
     * @param $format Format to be checked.
     *
     * @return bool True if it is valid, false if not.
     */

    function isValidTimeFormat($format) {

        switch($format){

            case "24 Hour":
            case "12 Hour":
                $valid = true;
            break;

            default:
                $valid = false;
            break;

        }//end switch

        return $valid;

    }//end function isValidTimeFormat


    /**
     * function processOptions
     * @param array $options
     */

    function processOptions($options) {

        $this->options->useUuid = true;
            if(isset($options["useUuid"]))
                $this->options->useUuid = (bool)$options["useUuid"];

        /**
          *   date format options
          */

        $this->options->dateFormat = "SQL";
        if(defined("DATE_FORMAT"))
            $this->options->dateFormat = DATE_FORMAT;

        if(isset($options["dateFormat"]))
            if($this->isValidDateFormat($options["dateFormat"]))
                $this->options->dateFormat = $options["dateFormat"];

        /**
          *  Time format options
          */

        $this->options->timeFormat = "24 Hour";
        if(defined("TIME_FORMAT"))
            $this->options->timeFormat = TIME_FORMAT;

        if(isset($options["timeFormat"]))
            if($this->isValidTimeFormat($options["timeFormat"]))
                $this->options->timeFormat = $options["timeFormat"];

        /**
          *  Id field options
          *
          *  This option dictates whether or not to keep the destination's
          *  id field if "replacing" (via the mysql replace) when there is
          *  no id field set.
          */
        $this->options->keepDestId = true;
        if(isset($options["keepDestId"]))
            $this->options->keepDestId = (bool)$options["keepDestId"];

    }//end method --processOptions--

    /**
    * function process
    * Process request array posted to api
    *
    * The method process() loops through the request array, and attempts to
    * find a corresponding function to run for the request. It first looks for
    * a corresponding api class to load. If it does not find it, it next to
    * see if there is an overriden table class file, and finally if none of
    * these are present, it uses the standard table class.
    *
    */
    function process(){

        $i = 1;
        $tabledefid = null;

        if(!is_array($this->data))
            $this->sendError("Passed data malformed.  Was expecting an array.", $this->data, true);

        foreach($this->data as $request){

            if(!is_array($request))
                $this->sendError("Malformed request number ".$i, $request);

            if(!isset($request["tabledefid"]) || !isset($request["command"]) || !isset($request["data"]))
                $this->sendError("Malformed request number ".$i, $request);

            /**
              *  Process the options and populate the options object.
              */
            if(!isset($request["options"]))
                $request["options"] = NULL;

            $this->processOptions($request["options"]);

            if((int) $request["tabledefid"] !== $tabledefid){

                $tabledefid = mysql_real_escape_string($request["tabledefid"]);

                //First let's get the table information from the tabledef
                $querystatement = "
                    SELECT
                        `maintable`,
                        `deletebutton`,
                        `querytable`,
                        `modules`.`name`,
                        `apiaccessible`
                    FROM
                        `tabledefs` INNER JOIN `modules` ON tabledefs.moduleid = modules.uuid
                    WHERE
                        tabledefs.uuid = '".$tabledefid."'
                ";

                $queryresult = $this->db->query($querystatement);

                if($this->db->numRows($queryresult) == 0){

                    if (!(in_array($request["command"], array("procedure", "getsetting")))){

                        $this->sendError("Invalid tabledefid (".$tabledefid.") from request number ".$i);
                        continue;

                    } else {

                        $deletebutton = "delete";
                        $maintable = "settings";
                        $modulename = "base";

                        $hasAPIOveride = false;
                        $hasTableClassOveride = false;

                    }//endif

                } else {

                    $therecord = $this->db->fetchArray($queryresult);

                    if(!$therecord["apiaccessible"]){
                        $this->sendError("Invalid tabledefid (".$tabledefid.") from request number ".$i.": This table definition is inaccessible via api.");
                        continue;
                    }//endif


                    $deletebutton = $therecord["deletebutton"];

                    $maintable = $therecord["maintable"];

                    $modulename = $therecord["name"];

                    //check for ovridding classes only once.
                    $hasAPIOveride = file_exists("../extendedapi/".$maintable.".php");
                    $hasTableClassOveride = file_exists("../".$modulename."/include/".$maintable.".php");

                }//endif

            }//endif


            /* Order in which to check for processors is as follows:

               If the extendedAPI module is present, look for a file matching the main
               table name of the table def.
               Example: modules/extendedapi/clients.php

               If a table class file exists in the module's include folder
               use that.
               Example: modules/bms/include/clients.php

               Use the standard class module.

            */
            $methodName = "";

            if($hasAPIOveride){
                // Found an API module table php

                @ include_once("modules/extendedapi/".$maintable.".php");

                $className = $className."Api";

                if(class_exists($className)) {

                    $processor = new $className($this->db);
                    $processor->dateFormat =  $this->options->dateFormat;
                    $processor->timeFormat =  $this->options->timeFormat;

                    if(!method_exists($processor, $request["command"])) {

                        $methodName = $request["command"];

                        $this->response[] = $processor->$methodName($request["data"], $this->options->useUuid);

                    }//endif

                }//end if

            }//endif


            /* If the command starts with api_, and there is a request overload, let's assume they
               are trying to call a homeade function in the ovveriden phpBMS table that they created.
            */
            if(!$methodName && substr($request["command"], 0, 4) == "api_" && $hasTableClassOveride){

                include_once("include/tables.php");
                @ include_once("modules/".$modulename."/include/".$maintable.".php");

                if(class_exists($maintable)){
                    $processor = new $maintable($this->db, $tabledefid);
                    $processor->dateFormat =  $this->options->dateFormat;
                    $processor->timeFormat =  $this->options->timeFormat;
                }else{
                    $processor = new phpbmsTable($this->db, $tabledefid);
                    $processor->dateFormat =  $this->options->dateFormat;
                    $processor->timeFormat =  $this->options->timeFormat;
                }

                if(method_exists($processor, $request["command"])){

                    $methodName = $request["command"];

                    $this->response[] = $processor->$methodName($request["data"], $this->options->useUuid);

                }//endif

            }//endif

            if(!$methodName) {
                /* Either using the modules overriden table class or search
                   functions class or the standard one There are several
                   standard commands that can be passed:

                   * insert - calls the tabledefs insertRecord command, the
                                same command that is called on standard
                                phpBMS forms. a variable array should be
                                passed in the request data.

                   * update - calls the tabledefs iupdateRecord command, the
                                same command that is called on standard
                                phpBMS forms. a variable array should be
                                passed in the request data

                   * delete (or the corresponding delete button command)
                            - calls the deleteRecord searchFunctions command
                            data should be an array of ids

                   * procedure - This calls a stored MySQL stored procedure
                                 request data should pass an object with the
                                 (name) and optionally an array of any
                                 (parameters)

                    In addition, you can pass a command that corresponds to
                    any additional commands as defined in the table definition
                    the request data passed should contain an array of ids
                */

                switch($request["command"]){

                    case "ping":
                        //======================================================

                        $this->_addToResponse("message", "Everything is phpBMSy!");

                        break;

                    case "insert":
                        //======================================================
                        include_once("include/tables.php");

                        if($hasTableClassOveride){

                            @ include_once("modules/".$modulename."/include/".$maintable.".php");

                            if(class_exists($maintable)){
                                $processor = new $maintable($this->db, $tabledefid);
                                $processor->dateFormat =  $this->options->dateFormat;
                                $processor->timeFormat =  $this->options->timeFormat;
                            }else{
                                $processor = new phpbmsTable($this->db, $tabledefid);
                                $processor->dateFormat =  $this->options->dateFormat;
                                $processor->timeFormat =  $this->options->timeFormat;
                            }//end if

                        } else{
                            $processor = new phpbmsTable($this->db, $tabledefid);
                            $processor->dateFormat =  $this->options->dateFormat;
                            $processor->timeFormat =  $this->options->timeFormat;
                        }//end if

                        $errorArray = $processor->verifyVariables((array) $request["data"]);

                        if(count($errorArray))
                            $this->sendError("Insert failed from request number ".$i, $errorArray);
                        else {

                            $overrideID = false;
                            if(is_array($request["data"]))
                                if(isset($request["data"]["id"])){

                                    if(((int)$request["data"]["id"]) !== 0)
                                        $overrideID = true;
                                    if($this->options->keepDestId && isset($request["data"]["uuid"]) && $this->options->useUuid)
                                        $request["data"]["id"] = getId($this->db, $processor->uuid, $request["data"]["uuid"]);

                                }elseif($this->options->keepDestId && isset($request["data"]["uuid"]) && $this->options->useUuid)
                                    $request["data"]["id"] = getId($this->db, $processor->uuid, $request["data"]["uuid"]);

                            $createUuid = true;
                            if(is_array($request["data"]))
                                if(isset($request["data"]["uuid"]))
                                    if((string)$request["data"]["uuid"] !== ""){
                                        $overrideID = true;
                                        $createUuid = false;
                                    }//end if

                            if(!isset($processor->fields["uuid"]))
                                $createUuid = false;

                            $newid = $processor->insertRecord($request["data"], NULL, $overrideID, true, $createUuid);

                            if($newid){
                                if($createUuid){
                                    $this->_addToResponse("added", "record added to tabledef ".$tabledefid, $newid["uuid"]);
                                }elseif(isset($processor->fields["uuid"])){
                                    $this->_addToResponse("added", "record added to tabledef ".$tabledefid, $request["data"]["uuid"]);
                                }else{
                                    $this->_addToResponse("added", "record added to tabledef ".$tabledefid, $newid);
                                }//end if
                            }else
                                $this->sendError("Insert failed from request number ".$i);

                        }//endif

                        break;

                    case "update":
                        //======================================================
                        include_once("include/tables.php");

                        if($hasTableClassOveride){

                            @ include_once("modules/".$modulename."/include/".$maintable.".php");

                            if(class_exists($maintable)){
                                $processor = new $maintable($this->db, $tabledefid);
                                $processor->dateFormat =  $this->options->dateFormat;
                                $processor->timeFormat =  $this->options->timeFormat;
                            }else{
                                $processor = new phpbmsTable($this->db, $tabledefid);
                                $processor->dateFormat =  $this->options->dateFormat;
                                $processor->timeFormat =  $this->options->timeFormat;
                            }//end if

                        } else {
                            $processor = new phpbmsTable($this->db, $tabledefid);
                            $processor->dateFormat =  $this->options->dateFormat;
                            $processor->timeFormat =  $this->options->timeFormat;
                        }//end if

                        $errorArray = $processor->verifyVariables($request["data"]);

                        if($this->options->useUuid){
                            if(!isset($request["data"]["uuid"]))
                                $errorArray[] = "The `uuid` field must be set.";
                        }else{
                            if(!isset($request["data"]["id"]))
                                $errorArray[] = "The `id` field must be set.";
                        }//end if


                        if(count($errorArray))
                            $this->sendError("Update failed from request number ".$i, $errorArray);
                        else {

                            $processor->updateRecord($request["data"], NULL, (bool)$this->options->useUuid);

                            $this->_addToResponse("updated", "record updated in tabledef ".$tabledefid);

                        }//endif

                        break;

                    case "get":
                        //======================================================

                        include_once("include/tables.php");

                        if($hasTableClassOveride){

                            @ include_once("modules/".$modulename."/include/".$maintable.".php");

                            if(class_exists($maintable)){
                                $processor = new $maintable($this->db, $tabledefid);
                                $processor->dateFormat =  $this->options->dateFormat;
                                $processor->timeFormat =  $this->options->timeFormat;
                            }else{
                                $processor = new phpbmsTable($this->db, $tabledefid);
                                $processor->dateFormat =  $this->options->dateFormat;
                                $processor->timeFormat =  $this->options->timeFormat;
                            }//end if

                        } else {
                            $processor = new phpbmsTable($this->db, $tabledefid);
                            $processor->dateFormat =  $this->options->dateFormat;
                            $processor->timeFormat =  $this->options->timeFormat;
                        }//end if

                        $errorMessage = "";
                        if($this->options->useUuid){
                            if(!isset($request["data"]["uuid"]))
                                $errorMessage = "The `uuid` field must be set.";
                        }else{
                            if(!isset($request["data"]["id"]))
                                $errorMessage = "The `id` field must be set.";
                        }//end if

                        if($errorMessage)
                            $this->sendError("Update failed from request number ".$i, $errorMessage);
                        elseif(!$this->options->useUuid){
                            $therecord = $processor->getRecord((int) $request["data"]["id"], $this->options->useUuid);
                            $thereturn = $therecord["id"];
                            $thevalue = (int)$request["data"]["id"];
                        }else{
                            $therecord = $processor->getRecord(mysql_real_escape_string($request["data"]["uuid"]), $this->options->useUuid);
                            $thereturn = $therecord["uuid"];
                            $thevalue = $request["data"]["uuid"];
                        }

                        if($thereturn == $thevalue)
                            $this->_addToResponse("retrieved", "record (".htmlQuotes($thevalue).") retrieved in tabledef ".$tabledefid, $therecord);
                        else
                            $this->_addToResponse("retrieved", "no record found (".htmlQuotes($thevalue).") in tabledef ".$tabledefid);

                        break;

                    case "delete":
                    case $deletebutton:
                        //======================================================
                        if(!is_array($request["data"]))
                                $this->sendError("Passed data is not array in request number ".$i, $request["data"]);
                        else {

                            include_once("include/search_class.php");

                            if($hasTableClassOveride){

                                @ include_once("modules/".$modulename."/include/".$maintable.".php");

                                $className = $maintable."SearchFunctions";

                                if(class_exists($className))
                                    $processor = new $className($this->db, $tabledefid, $request["data"]);
                                else
                                    $processor = new searchFunctions($this->db, $tabledefid, $request["data"]);

                            } else
                                $processor = new searchFunctions($this->db, $tabledefid, $request["data"]);


                            $result = $processor->delete_record($this->options->useUuid);

                            $this->_addToResponse($request["command"], $result);

                        }//endif

                        break;

                    case "procedure":
                        //======================================================
                        if(!is_array($request["data"]))
                            $this->sendError("Wrong passed procedure format, expected object in request number ".$i, $request["data"]);
                        else{

                            if(!isset($request["data"]["name"]))
                                $this->sendError("Wrong passed procedure format, name missing in request number ".$i, $request["data"]);
                            else {

                                //check to see if stored procedure exists
                                $querystatement = "
                                    SHOW PROCEDURE STATUS LIKE '".mysql_real_escape_string($request["data"]["name"])."'
                                ";

                                $queryresult = $this->db->query($querystatement);

                                if($this->db->numRows($queryresult) === 0)
                                    $this->sendError("Procedure '".$request["data"]["name"]."' does not exist in request number ".$i, $request["data"]);
                                else{

                                    $parameterList = "";

                                    if(isset($request["data"]["parameters"]))
                                        foreach($request["data"]["parameters"] as $parameter)
                                            $parameterList .= ", '".mysql_real_escape_string($parameter)."'";

                                    if($parameterList)
                                        $parameterList = substr(1, $parameterList);

                                    $procedurestatement = "
                                        CALL ".$request["data"]["name"]."(".$parameterList.")";

                                    $queryresult = $this->db->query($procedurestatement);

                                    $result = array();
                                    while($therecord = $this->db->fetchArray($queryresult))
                                        $result[] = $therecord;

                                    $this->_addToResponse("result",
                                                          "Procedure '".$request["data"]["name"]."' returned (".$this->db->numRows($queryresult).") in request number ".$i,
                                                          $result);

                                }//endif

                            }//endif

                        }//endif

                        break;

                    case "getsetting":
                        //======================================================
                        if(!is_array($request["data"]))
                            $this->sendError("Wrong passed data format, expected array in request number ".$i, $request["data"]);
                        else{

                            $whereclause = "";
                            foreach($request["data"] as $settingName)
                                $whereclause = "OR `name` = '".mysql_real_escape_string($settingName)."' ";

                            if($whereclause)
                                $whereclause = "WHERE ".substr($whereclause, 2);

                            $querystatement = "
                                SELECT
                                    `name`,
                                    `value`
                                FROM
                                    `settings`
                                ".$whereclause;

                            $queryresult = $this->db->query($querystatement);

                            $settings = array();

                            while($therecord = $this->db->fetchArray($queryresult))
                                $settings[$therecord["name"]] = $therecord["value"];

                            $this->_addToResponse("result",
                                                  "GetSettings returned (".count($settings).") in request number ".$i,
                                                  $settings);

                        }//endif

                        break;

                    default:
                        //======================================================
                        // a catch all for other requests.  This should correspond
                        // to an ovrriden search class function only. Calling
                        // some commands can cause response errors so be careful
                        if(!is_array($request["data"]) && !$hasTableClassOveride)
                                $this->sendError("Passaed data is not array or function (".$request["command"].") does not exist in request number ".$i, $request["data"]);
                        else {

                                @ include_once("modules/".$modulename."/include/".$maintable.".php");

                                $className = $maintable."SearchFunctions";

                                if(!class_exists($className))
                                    $this->sendError("Function (".$request["command"].") does not exist in request number ".$i, $request["data"]);
                                else{

                                    $processor = new $className($this->db, $tabledefid, $request["data"]);
                                    $processor->dateFormat =  $this->options->dateFormat;
                                    $processor->timeFormat =  $this->options->timeFormat;

                                    $methodName = $request["command"];

                                    if(!method_exists($processor, $methodName))
                                        $this->sendError("Function (".$request["command"].") does not exist in request number ".$i, $request["data"]);
                                    else {

                                        $result = $processor->$methodName();
                                        $this->_addToResponse($request["command"], $result);

                                    }//endif method_exists

                                }//endif $className

                        }//endif

                        break;

                }//endswitch $request["command"]

            }//endif $modulename

            $i++;

        }//endforeach

        $this->displayResult();

    }//end function process


    /**
     * function sendError
     * Logs error to response message and optionally exits
     *
     * Adds an entry of type error to the response array. Can also optionally
     * quit processing of the scrpt gracefully, and outputs the response.
     *
     * @param string $errorMessage message to log in response
     * @param var $dump any extra information to send back n the response - usually a dump of the offending variable
     * @param boolean $stop true/false whether to display response and stop exceution of the script
     */
    function sendError($errorMessage = "", $dump = "", $stop = false){

        $this->_addToResponse("error", $errorMessage, $dump);

        if($stop){

            //display the current result and exits;
            $this->displayResult();
            exit;

        }//endif

    }//end function sendError


    /**
     * function _addToResponse
     * adds an entry to the response array
     *
     * Adds a detailed entry to the response array.
     *
     * @param string $type the type of message being sent
     * @param string $message message to send
     * @param var $extras any extra information to send
     */
    function _addToResponse($type = "message", $message ="", $extras = ""){

        $response["type"] = $type;
        $response["message"] = $message;
        if($extras)
            $response["extras"] = $extras;

        $this->response[] = $response;

    }//end function _addToResponse


    /**
     * function displayResult
     * outputs formated response array
     *
     * Formats the response array for out put and then echos the result
     */
    function displayResult(){

        $response = $this->encode($this->response);

        switch($this->format){

            default:
                echo $response;
                break;

        }//endswitch

    }//end function displayError

}//end class api

?>
