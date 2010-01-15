<?php

class apiwrapper{

    /**
      *  @var string api username
      */
    var $username;

    /**
      *  @var string api password
      */
    var $password;

    /**
      *  @var string the hostname of the target script
      */
    var $apiHostname;

    /**
      *  @var string the url path from $apiHostname to the target script
      */
    var $apiUrl;

    /**
      *  @var integer Custom port
      */
    var $port = NULL;

    /**
      *  @var bool Whether or not to use an ssl connection
      */
    var $secure = false;

    /**
      *  @var integer size (in bytes) of each fread
      */
    var $chunkSize = 8192;

    /**
      *  @var integer time (in seconds) of connection timeout
      */
    var $timeout = 300;


    /*
     * function __construct
     *
     * @param string $hostname The hostname of the target script
     * @param string $urlPath The location of the target script in relation to $hostname (usually something like 'modules/api/api_json.php').
     * @param string $username The username of an user with portal access
     * @param string $password The password of the same user with portal access
     * @param bool $secure Whether or not to transfer data over ssl.
     * @param integer $port If there is a non-standard port (i.e. different than 80 if not using an ssl connection, and different than 443 if using an ssl connection).
     */

    public function __construct($hostname, $urlPath, $username, $password, $secure = false, $port = NULL) {

        $this->apiHostname = $hostname;
        $this->apiUrl = $urlPath;
        $this->username = $username;
        $this->password = $password;
        $this->secure = (bool) $secure;
        if($port === 0 || (int)$port > 0)
            $this->port = $port;

    }//end function

    /*
     * function ping
     * @param string $tabledefuuid A tabledef uuid.
     *
     * @return array|bool An associative array containing the response, or false if a connection error has occured (in which case see $this->errorMessage).
     * @returnf string type The type of response.  This will either be 'error' or 'message'.
     * @returnf string message The related message.  This will be 'Everything is phpBMSy!' if no errors have occurred and the tabledefuuid is api accessible.
     */

    function ping($tabledefuuid) {

        $params["request"][0]["tabledefid"] = $tabledefuuid;
        $params["request"][0]["command"] = "ping";
        $params["request"][0]["data"] = array();

        $response = $this->_callServer($params);

        if($response !== false)
            return $response[0];
        else
            return false;

    }//end funciton


    /*
     * function insertRecords
     *
     * @param string $tabledefuuid The uuid of the tabledefinition that you wish to insert records.
     * @param array $data An array of associative arrays consising of fieldname => value pairs.
     * @param bool $generateUuid Whether to generate a new uuid for the inserted record (and ignore any passed uuid field).
     * @param bool $keepDestId This option dictates whether or not to keep the destination's id field if "replacing" (via the mysql replace) when there is no id field set.
     * @param string $dateFormat The format of the dates (if any) in $data.  Possible choices are : 'SQL', 'English, US', 'English, UK', or 'Dutch, NL'.  If none of these are chosen, it will default to 'SQL'.
     * @param string $timeFormat The format of the times (if any) in $data.  Possible choices are : '24 Hour' or '12 Hour'.  If none of these are chosen, it will default to '24 Hour'.
     *
     *
     * @return array An array of associative arrays of responses for each insert
     * @returnf array Associative array (integer key)
     * @returnff string type The result of the individual insert (either 'added' if successful, or 'error' if not).
     * @returnff string message The detailed message describing the result
     * @returnff string extras The uuid of the inserted record (or the integer id if the 'useUuid' option is false).  <em>Note:</em> his field only exists if type is not 'error'.
     */

    public function insertRecords($tabledefuuid, $data, $generateUuid = true, $keepDestId = true, $dateFormat = NULL, $timeFormat = NULL) {

        if($generateUuid !== true)
            $generateUuid = false;

        if($keepDestId !== true)
            $keepDestId = false;

        switch($dateFormat){

            case "SQL":
            case "English, US":
            case "English, UK":
            case "Dutch, NL":
            break;

            default:
                $dateFormat = "SQL";
            break;

        }//end switch

        switch($timeFormat){

            case "24 Hour":
            case "12 Hour":
            break;

            default:
                $timeFormat = "24 Hour";
            break;

        }//end switch

        $options = array(
                    "useUuid" => $generateUuid,
                    "dateFormat" => $dateFormat,
                    "timeFormat" => $timeFormat,
                    "keepDestId" => $keepDestId
                    );

        return $this->_runTableCommnad("insert", $tabledefuuid, $data, $options);

    }//end function


    /*
     * function updateRecords
     *
     * @param string $tabledefuuid The uuid of the tabledefinition that you wish to update records.
     * @param array $data An array of associative arrays consising of fieldname => value pairs.
     * @param bool $useUuid Whether to use the id (false) or the uuid (true) in the update whereclause.
     * @param string $dateFormat The format of the dates (if any) in $data.  Possible choices are : 'SQL', 'English, US', 'English, UK', or 'Dutch, NL'.  If none of these are chosen, it will default to 'SQL'.
     * @param string $timeFormat The format of the times (if any) in $data.  Possible choices are : '24 Hour' or '12 Hour'.  If none of these are chosen, it will default to '24 Hour'.
     *
     * @return array An array of associative arrays of responses for each update
     * @returnf array Associative array (integer key)
     * @returnff string type The result of the individual update (either 'updated' if successful, or 'error' if not).
     * @returnff string message The detailed message describing the result
     */

    public function updateRecords($tabledefuuid, $data, $useUuid = true, $dateFormat = NULL, $timeFormat = NULL) {

        if($useUuid !== true)
            $useUuid = false;

        switch($dateFormat){

            case "SQL":
            case "English, US":
            case "English, UK":
            case "Dutch, NL":
            break;

            default:
                $dateFormat = "SQL";
            break;

        }//end switch

        switch($timeFormat){

            case "24 Hour":
            case "12 Hour":
            break;

            default:
                $timeFormat = "24 Hour";
            break;

        }//end switch

        $options = array(
                    "useUuid" => $useUuid,
                    "dateFormat" => $dateFormat,
                    "timeFormat" => $timeFormat
                    );

        return $this->_runTableCommnad("update", $tabledefuuid, $data, $options);

    }//end function


    /*
     * function getRecords
     *
     * @param string $tabledefuuid The uuid of the tabledefinition that you wish to get records.
     * @param mixed $ids Either an array uuids (or ids if $useUuid = false), or an individual uuid (or id).
     * @param bool $useUuid Whether the data is an array of uuids or ids
     *
     * @return array An array of associative arrays of responses for each get
     * @returnf array Associative array (integer key)
     * @returnff string type The result of the individual get (either 'retrieved' if successful, or 'error' if not).
     * @returnff string message The detailed message describing the result
     * @returnff array extras The associative array containing the record retrieved.  <em>Note:</em> This field only exists if type is not 'error' AND there is a record that corresponds to the uuid/id searched for.
     */

    public function getRecords($tabledefuuid, $ids, $useUuid = true) {

        if($useUuid !== true)
            $useUuid = false;

        if($useUuid)
            $keyName = "uuid";
        else
            $keyName = "id";

        if(!is_array($ids))
            $ids = array($ids);

        $data = array();
        foreach($ids as $id){

            if($useUuid)
                $id = (string)$id;
            else
                $id = (int)$id;
            $data[][$keyName] = $id;

        }//end foreach

        $options = array(
                         "useUuid" => $useUuid
                        );

        return $this->_runTableCommnad("get", $tabledefuuid, $data, $options);

    }//end function


    /*
     * function deleteRecords
     *
     * @param string $tabledefuuid The uuid of the tabledefinition that you wish to delete (or inactivate, depending upon the tabledef) records.
     * @param mixed $ids Either an array uuids (or ids if $useUuid is false), or an individual uuid (or id).
     * @param bool $useUuid Whether the data is an array of uuids or ids.
     *
     * @return array An array of associative arrays of responses for each delete
     * @returnf array Associative array (integer key)
     * @returnff string type The result of the individual delete (either 'delete' or 'inactivate' (depending upon the tabledef) if successful, or 'error' if not). <em>Note:</em> The successful type (i.e. the non-error type) can possibly be values other than 'delete' or 'inactivate' depending upon the table definition.
     * @returnff string message The detailed message describing the result
     */

    public function deleteRecords($tabledefuuid, $ids, $useUuid = true) {

        if($useUuid !== true)
            $useUuid = false;

        if($useUuid)
            $keyName = "uuid";
        else
            $keyName = "id";

        if(!is_array($ids))
            $ids = array($ids);

        $data = array();
        foreach($ids as $id){

            if($useUuid)
                $id = (string)$id;
            else
                $id = (int)$id;
            $data[][$keyName] = $id;

        }//end foreach

        $options = array(
                         "useUuid" => $useUuid
                        );

        return $this->_runTableCommnad("delete", $tabledefuuid, $data, $options);

    }//end function


    /*
     * function runStoredProcedure
     *
     * @param string $tabledefuuid The uuid of an api accessible tabledefinition.
     * @param string $procedureName The stored procedure to be called.
     *
     * @return array An associative array response for the procedure
     * @returnf string type The result of the procedure (either 'result' if successful, or 'error' if not).
     * @returnf string message The detailed message describing the result
     */

    public function runStoredProcedure($tabledefuuid, $procedureName) {

        $params["request"][0]["command"] = "procedure";
        $params["request"][0]["data"]["name"] = $procedurename;
        $params["request"][0]["tabledefid"] = $tabledefuuid;

        $response = $this->_callServer($params);

        if($response !== false)
            return $response[0];
        else
            return false;

    }//end function


    /*
     * function getSetting
     *
     * @param array $settings Array of settings names
     *
     * @return array An associative array response for the get
     * @returnf string type The result of the get (either 'result' if successful, or 'error' if not).
     * @returnf string message The detailed message describing the result
     * @returnf array extras The associative array containing the settings retrieved.  <em>Note:</em> This field only exists if type is not 'error'.
     */

    public function getSettings($settings) {

        $params["request"][0]["command"] = "getsettings";
        $params["request"][0]["data"] = $settings;

        $response = $this->_callServer($params);

        if($response !== false)
            return $response[0];
        else
            return false;

    }//end function


    /*
     * function searchClientByEmail
     * @param string $email The email to be searched for
     * @param bool $useUuid Whether to return uuids or ids.
     *
     * @return array An associative array response for the get
     * @returnf string type The result of the get (either 'result' if successful, or 'error' if not).
     * @returnf string message The detailed message describing the result
     * @returnf array extras If the type is 'result', this will be a (possibly empty) array of uuids (or ids if the 'useUuid' option is false).  <em>Note:</em> This field only exists if type is not 'error'.
     */

    public function searchClientByEmail($email, $useUuid = true) {

        $method = "api_searchByEmail";
        $tabledefuuid = "tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083";
        $data["email"] = (string)$email;

        if($useUuid !== true)
            $useUuid = false;

        $options = array(
                          "useUuid" => $useUuid
                        );

        $response = $this->runApiMethod($method, $tabledefuuid, $data, $options);

        if($response !== false)
            return $response[0];
        else
            return false;

    }//end function

    /*
     * function searchClientByNameAndPostalcode
     * @param name $firstname The first name to search for in the client's table
     * @param name $lastname The last name to search for in the client's table
     * @param name $postalcode The postal code to search for in the client's table
     * @param bool $useUuid Whether to return uuids or ids.
     *
     * @return array An associative array response for the get
     * @returnf string type The result of the get (either 'result' if successful, or 'error' if not).
     * @returnf string message The detailed message describing the result
     * @returnf array extras If the type is 'result', this will be a (possibly empty) array of uuids (or ids if the 'useUuid' option is false).  <em>Note:</em> This field only exists if type is not 'error'.
     */

    public function searchClientByNameAndPostalcode($firstname, $lastname, $postalcode, $useUuid = true) {

        $method = "api_searchByNameAndPostalcode";
        $tabledefuuid = "tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083";
        $data["firstname"] = (string)$firstname;
        $data["lastname"] = (string)$lastname;
        $data["postalcode"] = (string)$postalcode;

        if($useUuid !== true)
            $useUuid = false;

        $options = array(
                          "useUuid" => $useUuid
                        );

        $response = $this->runApiMethod($method, $tabledefuuid, $data, $options);

        if($response !== false)
            return $response[0];
        else
            return false;

    }//end function


    /*
     * function searchClientByUsernameAndPassword
     * @param string $username The username to search for in the client's table
     * @param string $password The password to search for in the client's table
     * @param bool $useUuid Whether to return uuids or ids.
     *
     * @return array An associative array response for the get
     * @returnf string type The result of the get (either 'result' if successful, or 'error' if not).
     * @returnf string message The detailed message describing the result
     * @returnf array extras If the type is 'result', this will be a (possibly empty) array of uuids (or ids if the 'useUuid' option is false).  <em>Note:</em> This field only exists if type is not 'error'.
     */

    public function searchClientByUsernameAndPassword($username, $password, $useUuid = true) {

        $method = "api_searchByUsernameAndPassword";
        $tabledefuuid = "tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083";
        $data["username"] = (string)$username;
        $data["password"] = (string)$password;

        if($useUuid !== true)
            $useUuid = false;

        $options = array(
                          "useUuid" => $useUuid
                        );

        $response = $this->runApiMethod($method, $tabledefuuid, $data, $options);

        if($response !== false)
            return $response[0];
        else
            return false;

    }//end function


    /*
     * function searchSalesOrdersByClientUuid
     * @param string $clientuuid The uuid of a client record
     * @param string $ordertype The type of the sales order.  Possible types are :'Quote','Order','Invoice','VOID'
     * @param string $startdate The sql encoded DATETIME lower range of creation dates.
     * @param string $enddate The sql encoded DATETIME upper range of creation dates.
     * @param bool $useUuid Whether to return uuids or ids.
     *
     * @return array An associative array response for the get
     * @returnf string type The result of the get (either 'result' if successful, or 'error' if not).
     * @returnf string message The detailed message describing the result
     * @returnf array extras If the type is 'result', this will be a (possibly empty) array of uuids (or ids if the 'useUuid' option is false).  <em>Note:</em> This field only exists if type is not 'error'.
     */

    function searchSalesOrdersByClientUuid($clientuuid, $ordertype = NULL, $startdate = NULL, $enddate = NULL, $useUuid = true) {

        $method = "api_searchByClientUuid";
        $tabledefuuid = "tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883";
        $data["clientid"] = $clientuuid;
        if($ordertype !== NULL)
            $data["type"] = $ordertype;
        if($startdate !== NULL)
            $data["startdate"] = $startdate;
        if($enddate !== NULL)
            $data["enddate"] = $enddate;

        if($useUuid !== true)
            $useUuid = false;

        $options = array(
                          "useUuid" => $useUuid
                        );

        $response = $this->runApiMethod($method, $tabledefuuid, $data, $options);

        if($response !== false)
            return $response[0];
        else
            return false;

    }//end function


    /*
     * function getFile
     * @param string $url The full url to the api_servfile.php url. This can be retrieved from the getRecords method on a `files` table get (the 'apifileurl' key in the record).
     *
     * @return mixed The file if successful, false, if not.
     */

    public function getFile($url) {

        $urlInfo = parse_url($url);

        $oldHost = $this->apiHostname;
        $oldPort = $this->port;
        $oldUrl = $this->apiUrl;

        $this->apiHostname = $urlInfo["host"];
        if($port === 0 || (int)$port > 0)
            $this->port = $urlInfo["port"];
        else
            $this->port = NULL;
        $this->apiUrl = $urlInfo["path"]."?".$urlInfo["query"];

        $response = $this->_callServer(array());

        $this->apiUrl = $oldUrl;
        $this->apiHostname = $oldHost;
        $this->port = $oldPort;

        if($response !== false)
            return $response;
        else{
            $this->errorMessage = "";
            return false;
        }//end if

    }//end function


    /*
     * function runApiMethod
     *
     * @param string $method The name of the api method to be called.  This must start with 'api_'.
     * @param string $tabledefuuid The uuid of the tabledefinition with the relevant api method.
     * @param mixed $data Method specific data
     * @param array $options An associative array of options. Possible options are : 'useUuid', 'dateFormat', 'timeFormat'
     *
     * @return mixed Response dependent upon the api command
     */

    public function runApiMethod($method, $tabledefuuid, $data, $options = NULL) {

        if(substr($method, 0, 4) != "api_"){

            $this->errorMessage = "The command is not a valid api method";
            return false;

        }//end if

        $params["request"][0]["tabledefid"] = $tabledefuuid;
        $params["request"][0]["options"] = $options;
        $params["request"][0]["command"] = $method;
        $params["request"][0]["data"] = $data;

        return $this->_callServer($params);

    }//end function


    /*
     * function runSearchMethod
     *
     * @param string $method The name of the api method to be called.  This must start with 'api_'.
     * @param string $tabledefuuid The uuid of the tabledefinition with the relevant api method.
     * @param mixed $data Method specific data
     * @param array $options An associative array of options. Possible options are : 'useUuid', 'dateFormat', 'timeFormat'
     *
     * @return array An array of associative arrays of responses for each search method
     * @returnf array Associative array (integer key)
     * @returnff string type The result of the individual search method (either '$method' if successful, or 'error' if not).
     * @returnff string message The detailed message describing the result
     * @returnff array extras The associative array containing the record retrieved.  <em>Note:</em> This field only exists if type is 'error'.
     */

    public function runSearchMethod($method, $tabledefuuid, $data, $options = NULL) {

        if(substr($method, 0, 4) == "api_"){
            $this->errorMessage = "The command is not a valid search method";
            return false;
        }//end if

        switch($method){

            case "insert":
            case "update":
            case "delete":
            case "procedure":
            case "get":
            case "getsettings":
                $this->errorMessage = "The command is not a valid search method";
                return false;
                break;

        }//end switch

        return $this->_runTableCommnad($method, $tabledefuuid, $data, $options);

    }//end function


    /*
     * function _runTableCommnad
     *
     * @param string $command The api command
     * @param string $tabledefuuid The uuid of the tabledefinition with the relevant method.
     * @param array $records Array of associative arrays
     * @param array $options An associative array of options. Possible options are : 'useUuid', 'dateFormat', 'timeFormat', 'keepDestId'
     *
     * @return array The response from _callServer
     */

    private function _runTableCommnad($command, $tabledefuuid, $records, $options) {

        $params = array();
        $i = 0;
        foreach($records as $record){
            $params["request"][$i]["tabledefid"] = $tabledefuuid;
            $params["request"][$i]["options"] = $options;
            $params["request"][$i]["command"] = $command;
            $params["request"][$i]["data"] = $record;
            $i++;
        }//end foreach

        return $this->_callServer($params);


    }//end function


    /*
     * function _encode
     *
     * @param array $message Message to be encoded
     *
     * @return string Encoded message
     */

    private function _encode($message) {

        return json_encode($message);

    }//end function


    /*
     * function _decode
     *
     * @param string $response Encoded api response
     *
     * @return array Decoded api response
     */

    private function _decode($response) {

        return json_decode($response, true);

    }//end function


    /*
     * function _callServer
     *
     * @param array $params
     *
     * @return array False if major (i.e. connection) error has occurred.  Otherwise, returns an array of associative arrays.
     */

    private function _callServer($params) {

        $params["phpbmsusername"] = $this->username;
        $params["phpbmspassword"] = $this->password;

        if(!isset($params["request"]))
            $params["request"] = array();

        $params["request"] = $this->_encode($params["request"]);


        $this->errorMessage = "";
        $this->errorCode = "";

        $post_vars = http_build_query($params);

        $payload = "POST " .$this->apiUrl. " HTTP/1.0\r\n";
        $payload .= "Host: " . $this->apiHostname . "\r\n";
        $payload .= "Content-type: application/x-www-form-urlencoded\r\n";
        $payload .= "Content-length: " . strlen($post_vars) . "\r\n";
        $payload .= "Connection: close \r\n\r\n";
        $payload .= $post_vars;

        ob_start();
        if ($this->secure){

            if($this->port !== NULL)
                $port = $this->port;
            else
                $port = 443;

            $sock = fsockopen("ssl://".$this->apiHostname, $port, $errno, $errstr, 30);
        } else {

            if($this->port !== NULL)
                $port = $this->port;
            else
                $port = 80;

            $sock = fsockopen($this->apiHostname, $port, $errno, $errstr, 30);
        }
        if(!$sock) {
            $this->errorMessage = "Could not connect (ERR $errno: $errstr)";
            $this->errorCode = "-99";
            ob_end_clean();
            return false;
        }

        $response = "";
        fwrite($sock, $payload);
        stream_set_timeout($sock, $this->timeout);
        $info = stream_get_meta_data($sock);
        while ((!feof($sock)) && (!$info["timed_out"])) {
            $response .= fread($sock, $this->chunkSize);
            $info = stream_get_meta_data($sock);
        }
        if ($info["timed_out"]) {
            $this->errorMessage = "Could not read response (timed out)";
            $this->errorCode = -98;
        }
        fclose($sock);
        ob_end_clean();
        list($throw, $response) = explode("\r\n\r\n", $response, 2);

        if ($info["timed_out"]) return false;

        if(ini_get("magic_quotes_runtime")) $response = stripslashes($response);


        $decodedResponse = $this->_decode($response);

        if($response && $decodedResponse === false) {
            $this->errorMessage = "Bad Response. Got This:".$response;
            return false;
        } else {
            $response = $decodedResponse;
        }

        return $response;

    }//end function --_callServer--

}//end class

?>
