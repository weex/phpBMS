<?php
/*
 $Rev: 285 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-27 14:05:27 -0600 (Mon, 27 Aug 2007) $
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

/**
 * API level processing
 *
 * The api class handles processing and format conversion for API calls to the
 * system.
 * @author Brian Rieb <brieb@kreotek.com>
 *
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
     * Constructor sets up {@link $db}, decodes {@link $data} using the passed
     * format from {@link $format}
     */
    function api($db, $data, $format="json"){

        $this->db = $db;
        $this->db->errorFormat = $format;

        $this->format = $format;

        $this->data = $this->decode($data);

    }//end function init (api)


    /**
     * decodes data (usually the passed post data) depending on the {@link $format}
     *
     * Currently, this function can only decode JSON data, but support for SOAP,
     * generic XML, or some other format may be added
     *
     * @param string $data Information to be decoded
     */
    function decode($data){

        if(get_magic_quotes_runtime() || get_magic_quotes_gpc())
            $data = stripslashes($data);

        switch($this->format){

            case "json":
                $data = json_decode($data);
                break;

        }//endswitch

        return $data;

    }//end function decode


    /**
     * encodes data (usually {@link $request}) depending on the {@link $format}
     *
     * Currently, this function can only decode JSON data, but support for SOAP,
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

        if(!is_array($this->data) && !is_object($this->data))
            $this->sendError("Passed data malformed.  Was expecting an array or object", $this->data, true);

        foreach($this->data as $request){

            if(!is_object($request))
                $this->sendError("Malformed request number ".$i, $request);

            if(!isset($request->tabledefid) || !isset($request->command) || !isset($request->data))
                $this->sendError("Malformed request number ".$i, $request);

            if((int) $request->tabledefid !== $tabledefid){

                $tabledefid = (int) $request->tabledefid;

                //First let's get the table information from the tabledef
                $querystatement = "
                    SELECT
                        `maintable`,
                        `deletebutton`,
                        `querytable`,
                        modules.name
                    FROM
                        `tabledefs` INNER JOIN `modules` ON tabledefs.moduleid = modules.id
                    WHERE
                        tabledefs.id = ".$tabledefid;

                $queryresult = $this->db->query($querystatement);

                if($this->db->numRows($queryresult) == 0){

                    if (!(in_array($request->command, array("procedure", "getsetting")))){

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

                    if(!method_exists($processor, $request->command)) {

                        $methodName = $request->command;

                        $this->response[] = $processor->$methodName($request->data);

                    }//endif

                }//end if

            }//endif


            /* If the command starts with api_, and there is a request overload, let's assume they
               are trying to call a homeade function in the ovveriden phpBMS table that they created.
            */
            if(!$methodName && substr($request->command, 0, 4) == "api_" && $hasTableClassOveride){

                include_once("include/tables.php");
                @ include_once("modules/".$modulename."/include/".$maintable.".php");

                if(class_exists($maintable))
                    $processor = new $maintable($this->db, $tabledefid);
                else
                    $processor = new phpbmsTable($this->db, $tabledefid);

                if(method_exists($processor, $request->command)){

                    $methodName = $request->command;

                    $this->response[] = $processor->$methodName($request->data);

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

                switch($request->command){

                    case "insert":
                        //======================================================
                        include_once("include/tables.php");

                        if($hasTableClassOveride){

                            @ include_once("modules/".$modulename."/include/".$maintable.".php");

                            if(class_exists($maintable))
                                $processor = new $maintable($this->db, $tabledefid);
                            else
                                $processor = new phpbmsTable($this->db, $tabledefid);

                        } else
                            $processor = new phpbmsTable($this->db, $tabledefid);

                        $errorArray = $processor->verifyVariables((array) $request->data);

                        if(count($errorArray))
                            $this->sendError("Insert failed from request number ".$i, $errorArray);
                        else {

                            $overrideID = false;
                            if(is_array($request->data))
                                if(isset($request->data["id"]))
                                    if(((int) $request->data["id"]) !== 0)
                                        $overrideID = true;

                            $newid = $processor->insertRecord((array) $request->data, null, $overrideID, true);

                            $this->_addToResponse("added", "record added to tabledef ".$tabledefid, $newid);

                        }//endif

                        break;

                    case "update":
                        //======================================================
                        include_once("include/tables.php");

                        if($hasTableClassOveride){

                            @ include_once("modules/".$modulename."/include/".$maintable.".php");

                            if(class_exists($maintable))
                                $processor = new $maintable($this->db, $tabledefid);
                            else
                                $processor = new phpbmsTable($this->db, $tabledefid);

                        } else
                            $processor = new phpbmsTable($this->db, $tabledefid);

                        $errorArray = $processor->verifyVariables((array) $request->data);

                        if(count($errorArray))
                            $this->sendError("Update failed from request number ".$i, $errorArray);
                        else {

                            $processor->updateRecord((array) $request->data);

                            $this->_addToResponse("updated", "record updated in tabledef ".$tabledefid);

                        }//endif

                        break;

                    case "get":
                        //======================================================

                        include_once("include/tables.php");

                        if($hasTableClassOveride){

                            @ include_once("modules/".$modulename."/include/".$maintable.".php");

                            if(class_exists($maintable))
                                $processor = new $maintable($this->db, $tabledefid);
                            else
                                $processor = new phpbmsTable($this->db, $tabledefid);

                        } else
                            $processor = new phpbmsTable($this->db, $tabledefid);

                        $therecord  = $processor->getRecord((int) $request->data);

                        if($therecord["id"] == ((int) $request->data))
                            $this->_addToResponse("retrieved", "record (".((int) $request->data).") retrieved in tabledef ".$tabledefid, $therecord);
                        else
                            $this->_addToResponse("retrieved", "no record found (".((int) $request->data).") in tabledef ".$tabledefid);

                        break;

                    case "delete":
                    case $deletebutton:
                        //======================================================
                        if(!is_array($request->data))
                                $this->sendError("Passaed data is not array in request number ".$i, $request->data);
                        else {

                            include_once("include/search_class.php");

                            if($hasTableClassOveride){

                                @ include_once("modules/".$modulename."/include/".$maintable.".php");

                                $className = $maintable."SearchFunctions";

                                if(class_exists($className))
                                    $processor = new $className($this->db, $tabledefid, $request->data);
                                else
                                    $processor = new searchFunctions($this->db, $tabledefid, $request->data);

                            } else
                                $processor = new searchFunctions($this->db, $tabledefid, $request->data);


                            $result = $processor->delete_record();

                            $this->_addToResponse($request->command, $result);

                        }//endif

                        break;

                    case "procedure":
                        //======================================================
                        if(!is_object($request->data))
                            $this->sendError("Wrong passed procedure format, expected object in request number ".$i, $request->data);
                        else{

                            if(!isset($request->data->name))
                                $this->sendError("Wrong passed procedure format, name missing in request number ".$i, $request->data);
                            else {

                                //check to see if stored procedure exists
                                $querystatement = "
                                    SHOW PROCEDURE STATUS LIKE '".mysql_real_escape_string($request->data->name)."'
                                ";

                                $queryresult = $this->db->query($querystatement);

                                if($this->db->numRows($queryresult) === 0)
                                    $this->sendError("Procedure '".$request->data->name."' does not exist in request number ".$i, $request->data);
                                else{

                                    $parameterList = "";

                                    if(isset($request->data->parameters))
                                        foreach($request->data->parameters as $parameter)
                                            $parameterList .= ", '".mysql_real_escape_string($parameter)."'";

                                    if($parameterList)
                                        $parameterList = substr(1, $parameterList);

                                    $procedurestatement = "
                                        CALL ".$request->data->name."(".$parameterList.")";

                                    $queryresult = $this->db->query($procedurestatement);

                                    $result = array();
                                    while($therecord = $this->db->fetchArray($queryresult))
                                        $result[] = $therecord;

                                    $this->_addToResponse("result",
                                                          "Procedure '".$request->data->name."' returned (".$this->db->numRows($queryresult).") in request number ".$i,
                                                          $result);

                                }//endif

                            }//endif

                        }//endif

                        break;

                    case "getsetting":
                        //======================================================
                        if(!is_array($request->data))
                            $this->sendError("Wrong passed data format, expected array in request number ".$i, $request->data);
                        else{

                            $whereclause = "";
                            foreach($request->data as $settingName)
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
                        if(!is_array($request->data) && !$hasTableClassOveride)
                                $this->sendError("Passaed data is not array or function (".$request->command.") does not exist in request number ".$i, $request->data);
                        else {

                                @ include_once("modules/".$modulename."/include/".$maintable.".php");

                                $className = $maintable."SearchFunctions";

                                if(!class_exists($className))
                                    $this->sendError("Function (".$request->command.") does not exist in request number ".$i, $request->data);
                                else{

                                    $processor = new $className($this->db, $tabledefid, $request->data);

                                    $methodName = $request->command;

                                    if(!method_exists($processor, $methodName))
                                        $this->sendError("Function (".$request->command.") does not exist in request number ".$i, $request->data);
                                    else {

                                        $result = $processor->$methodName();
                                        $this->_addToResponse($request->command, $result);

                                    }//endif method_exists

                                }//endif $className

                        }//endif

                        break;

                }//endswitch $request->command

            }//endif $modulename

            $i++;

        }//endforeach

        $this->displayResult();

    }//end function process


    /**
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
        $response["messsage"] = $message;
        if($extras)
            $response["extras"] = $extras;

        $this->response[] = $response;

    }//end function _addToResponse


    /**
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
