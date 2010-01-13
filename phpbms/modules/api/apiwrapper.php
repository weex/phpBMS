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
     * function insertRecords
     * 
     * @param string $tabledefuuid The uuid of the tabledefinition that you wish to insert records.
     * @param array $data An array of associative arrays consising of fieldname => value pairs.
     * @param array $options An associative array of options. Possible options are : 'useUuid', 'dateFormat', 'timeFormat', 'keepDestId'
     *
     * @return array An array of associative arrays of responses for each insert
     * @returnf array Associative array (integer key)
     * @returnff string type The result of the individual insert (either 'added' if successful, or 'error' if not).
     * @returnff string message The detailed message describing the result
     * @returnff string extras The uuid of the inserted record (or the integer id if the 'useUuid' option is false).  <em>Note:</em> his field only exists if type is not 'error'.
     */
    
    public function insertRecords($tabledefuuid, $data, $options = NULL) {
        
       return $this->_runTableCommnad("insert", $tabledefuuid, $data, $options);
        
    }//end function
    
    
    /*
     * function updateRecords
     * 
     * @param string $tabledefuuid The uuid of the tabledefinition that you wish to update records.
     * @param array $data An array of associative arrays consising of fieldname => value pairs.
     * @param array $options An associative array of options. Possible options are : 'useUuid', 'dateFormat', 'timeFormat', 'keepDestId'
     *
     * @return array An array of associative arrays of responses for each update
     * @returnf array Associative array (integer key)
     * @returnff string type The result of the individual update (either 'updated' if successful, or 'error' if not).
     * @returnff string message The detailed message describing the result
     */
    
    public function updateRecords($tabledefuuid, $data, $options = NULL) {
        
        return $this->_runTableCommnad("update", $tabledefuuid, $data, $options);
        
    }//end function
    
    
    /*
     * function getRecords
     * 
     * @param string $tabledefuuid The uuid of the tabledefinition that you wish to get records.
     * @param array $data An array of table uuids (or integer ids if useUuid option is set to false)
     * @param array $options An associative array of options. Possible options are : 'useUuid', 'dateFormat', 'timeFormat'
     *
     * @return array An array of associative arrays of responses for each get
     * @returnf array Associative array (integer key)
     * @returnff string type The result of the individual get (either 'retrieved' if successful, or 'error' if not).
     * @returnff string message The detailed message describing the result
     * @returnff array extras The associative array containing the record retrieved.  <em>Note:</em> This field only exists if type is not 'error' AND there is a record that corresponds to the uuid/id searched for.
     */
    
    public function getRecords($tabledefuuid, $data, $options = NULL) {
        
        return $this->_runTableCommnad("get", $tabledefuuid, $data, $options);
        
    }//end function
    
    
    /*
     * function deleteRecords
     * 
     * @param string $tabledefuuid The uuid of the tabledefinition that you wish to delete records.
     * @param array $data An array associative arrays with the key 'uuid' (or 'id' if useUuid option is set to false) and the relevant value.
     * @param array $options An associative array of options. Possible options are : 'useUuid'
     *
     * @return array An array of associative arrays of responses for each delete
     * @returnf array Associative array (integer key)
     * @returnff string type The result of the individual delete (either 'delete' or 'inactivate' (depending upon the tabledef) if successful, or 'error' if not). <em>Note:</em> The successful type (i.e. the non-error type) can possibly be values other than 'delete' or 'inactivate' depending upon the table definition.
     * @returnff string message The detailed message describing the result
     */
    
    public function deleteRecords($tabledefuuid, $records, $options = NULL) {
        
        return $this->_runTableCommnad("delete", $tabledefuuid, $records, $options);
        
    }//end function
    
    
    /*
     * function runStoredProcedure
     * 
     * @param string $procedureName The stored procedure to be called.
     *
     * @return array An associative array response for the procedure
     * @returnf string type The result of the procedure (either 'result' if successful, or 'error' if not).
     * @returnf string message The detailed message describing the result
     */
    
    public function runStoredProcedure($procedureName) {
        
        $params["request"][0]["command"] = "procedure";
        $params["request"][0]["data"]["name"] = $procedurename;
        
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
     * @param array $options An associative array of options. Possible options are : 'useUuid'
     *
     * @return array An associative array response for the get
     * @returnf string type The result of the get (either 'result' if successful, or 'error' if not).
     * @returnf string message The detailed message describing the result
     * @returnf array extras If the type is 'result', this will be a (possibly empty) array of uuids (or ids if the 'useUuid' option is false).  <em>Note:</em> This field only exists if type is not 'error'.
     */
    
    public function searchClientByEmail($email, $options = NULL) {
        
        $method = "api_searchByEmail";
        $tabledefuuid = "tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083";
        $data["email"] = (string)$email;
        
        $response = $this->runApiMethod($method, $tabledefuuid, $data, $options);
        
        if($response !== false)
            return $response[0];
        else
            return false;
        
    }//end function
    
    /*
     * function searchClientByNameAndPostalcode
     * @param $firstname
     * @param $lastname
     * @param $postalcode
     * @param array $options An associative array of options. Possible options are : 'useUuid'
     *
     * @return array An associative array response for the get
     * @returnf string type The result of the get (either 'result' if successful, or 'error' if not).
     * @returnf string message The detailed message describing the result
     * @returnf array extras If the type is 'result', this will be a (possibly empty) array of uuids (or ids if the 'useUuid' option is false).  <em>Note:</em> This field only exists if type is not 'error'.
     */
    
    public function searchClientByNameAndPostalcode($firstname, $lastname, $postalcode, $options = NULL) {
        
        $method = "api_searchByNameAndPostalcode";
        $tabledefuuid = "tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083";
        $data["firstname"] = (string)$firstname;
        $data["lastname"] = (string)$lastname;
        $data["postalcode"] = (string)$postalcode;
        
        $response = $this->runApiMethod($method, $tabledefuuid, $data, $options);
        
        if($response !== false)
            return $response[0];
        else
            return false;
        
    }//end function
    
    
    /*
     * function searchClientByUsernameAndPassword
     * @param $username
     * @param $password
     * @param array $options An associative array of options. Possible options are : 'useUuid'
     *
     * @return array An associative array response for the get
     * @returnf string type The result of the get (either 'result' if successful, or 'error' if not).
     * @returnf string message The detailed message describing the result
     * @returnf array extras If the type is 'result', this will be a (possibly empty) array of uuids (or ids if the 'useUuid' option is false).  <em>Note:</em> This field only exists if type is not 'error'.
     */
    
    public function searchClientByUsernameAndPassword($username, $password, $options = NULL) {
        
        $method = "api_searchByUsernameAndPassword";
        $tabledefuuid = "tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083";
        $data["username"] = (string)$username;
        $data["password"] = (string)$password;
        
        $response = $this->runApiMethod($method, $tabledefuuid, $data, $options);
        
        if($response !== false)
            return $response[0];
        else
            return false;
        
    }//end function
    
    
    /*
     * function searchSalesOrdersByClientUuid
     * @param $clientuuid
     * @param $ordertype
     * @param $startdate
     * @param $enddate
     * @param array $options An associative array of options. Possible options are : 'useUuid'
     *
     * @return array An associative array response for the get
     * @returnf string type The result of the get (either 'result' if successful, or 'error' if not).
     * @returnf string message The detailed message describing the result
     * @returnf array extras If the type is 'result', this will be a (possibly empty) array of uuids (or ids if the 'useUuid' option is false).  <em>Note:</em> This field only exists if type is not 'error'.
     */
    
    function searchSalesOrdersByClientUuid($clientuuid, $ordertype = NULL, $startdate = NULL, $enddate = NULL, $options = NULL) {
        
        $method = "api_searchByClientUuid";
        $tabledefuuid = "tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883";
        $data["clientid"] = $clientuuid;
        if($ordertype !== NULL)
            $data["type"] = $ordertype;
        if($startdate !== NULL)
            $data["startdate"] = $startdate;
        if($enddate !== NULL)
            $data["enddate"] = $enddate;
            
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
        
        if(substr($method, 0, 4) == "api_"){
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
     * @param bool $decode
     * 
     * @return array False if major (i.e. connection) error has occurred.  Otherwise, returns an array of associative arrays.
     */
    
    private function _callServer($params, $decode = false) {
        
        $params["phpbmsusername"] = $this->username;
        $params["phpbmspassword"] = $this->password;
        
        $this->errorMessage = "";
        $this->errorCode = "";
        
        $post_vars = $this->httpBuildQuery($params);
        
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
            
            $sock = fsockopen("ssl://".$host, $port, $errno, $errstr, 30);
        } else {
            
            if($this->port !== NULL)
                $port = $this->port;
            else
                $port = 80;
            
            $sock = fsockopen($host, $port, $errno, $errstr, 30);
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
        if ($info["timed_out"]) return false;
        
        if(ini_get("magic_quotes_runtime")) $response = stripslashes($response);
        
        if($decode){
            $decodedResponse = $this->_decode($response);
                
            if($response && $decodedResponse === false) {
                $this->errorMessage = "Bad Response. Got This:".$response;
                return false;
            } else {
                $response = $decodedResponse;
            }
        }//end if
        
        
        return $response;
        
    }//end function --_callServer--
    
}//end class

?>