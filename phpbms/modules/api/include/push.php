<?php

class push{

    /**
      *  $processor
      *  @var object Table object
      */
    var $processor;

    /**
      *  $tabledefid
      *  @var string The tabledefinition's uuid
      */
    var $tabledefid;
    
    /**
      *  $destTabledefid
      *  @var string Destiniation tabledef uuid.
      */
    var $destTabledefid;

    /**
      *  $uuidArray
      *  @var array Array of record uuids to be sent.
      */
    var $uuidArray = array();

    /**
      *  $apiUsername
      *  @var string The username of the api user on the
      *  destination.
      */
    var $apiUsername;

    /**
      *  $apiPassword
      *  @var string The password of the {@link, $apiUsername} user.
      */
    var $apiPassword;

    /**
      *  $apiDestination
      *  @var string The url destination of the push.
      */
    var $apiDestination;
    
    /**
      *  $useSsl
      *  @var bool Whether or not to use an ssl connection.
      */
    var $useSsl;

    /**
      *  $apiCommand
      *  @var string The command of the push.
      */
    var $apiCommand = "insert";

    /**
      *  $apiOptions
      *  @var array Array of api options.
      */
    var $apiOptions = array();

    /**
      *  $apiFormat
      *  @var string The format of the api query information.
      */
    var $apiFormat = "json";

    /**
      *  $httpMethod
      *  @var string The format of the api call (e.g. GET or POST).
      *  POST is reccomended due to space issues of GET.
      */
    var $httpFormat = "POST";
    
    /**
      *  $error
      *  @var bool
      */
    var $error = false;

    /**
     * function push
     * @param object $db phpbms database object
     * @param string $pushrecorduuid Uuid of the relevant push record
     * @param array $uuidArray Array of Uuids on which the push may be applied.
     */
    function push($db, $pushrecorduuid, $uuidArray = NULL){
        $this->db = $db;
        if($uuidArray)
            $this->uuidArray = $uuidArray;
        $this->_loadPushRecord($pushrecorduuid);

    }//end function


    /*
     * function _getProcessor
     *
     * Note: The file with the processor must be already included before running this
     * function.
     * 
     * @param $tabledefid
     */ 
    
    function _getProcessor($tabledefid) {

        $querystatement = "
            SELECT
                `modules`.`name` AS `modulename`,
                `tabledefs`.`maintable` AS `maintable`
            FROM
                `tabledefs` INNER JOIN `modules` ON `tabledefs`.`moduleid` = `modules`.`uuid`
            WHERE
                `tabledefs`.`uuid` = '".mysql_real_escape_string($tabledefid)."'";
    
        $queryresult = $this->db->query($querystatement);
    
        $thereturn = $this->db->fetchArray($queryresult);
    
        //next, see if the table class exists
        if(class_exists($thereturn["maintable"])){
    
                $classname = $thereturn["maintable"];
                $thetable = new $classname($this->db, $tabledefid);
    
        } else 
                $thetable = new phpbmsTable($this->db, $tabledefid);
        
        
        
        $this->processor = $thetable;
        $this->maintable = $thereturn["maintable"];
        
    }//end function
    
    
    /*
     * function _loadPushRecord
     * @param $pushrecorduuid
     */
    
    function _loadPushRecord($pushrecorduuid) {
        
        $pushrecorduuid = mysql_real_escape_string($pushrecorduuid);
        
        $querystatement = "
            SELECT
                `pushrecords`.`name`,
                `pushrecords`.`originuuid`,
                `pushrecords`.`destuuid`,
                `pushrecords`.`apicommand`,
                `pushrecords`.`whereclause`,
                `pushrecords`.`apiusername`,
                `pushrecords`.`apipassword`,
                `pushrecords`.`httpformat`,
                `pushrecords`.`dataformat`,
                `pushrecords`.`ssl`,
                `pushrecords`.`server`,
                `pushrecords`.`port`,
                `pushrecords`.`destscript`,
                `pushrecords`.`keepdestid`,
                `pushrecords`.`useuuid`,
                `pushrecords`.`dateformat`,
                `pushrecords`.`timeformat`,
                `pushrecords`.`extraoptions`
            FROM
                `pushrecords`
            WHERE
                `pushrecords`.`uuid` = '".$pushrecorduuid."'
        ";
        
        $queryresult = $this->db->query($querystatement);
        
        $therecord = $this->db->fetchArray($queryresult);
        
        /**
          *  Get the Processor 
          */
        
        $this->tabledefid = $therecord["originuuid"];
        $this->_getProcessor($this->tabledefid);
        
        /**
          *  Load the options 
          */
        $apiOptions["keepDestId"] = $therecord["keepdestid"];
        $apiOptions["useUuid"] = $therecord["useuuid"];
        $apiOptions["dateFormat"] = $therecord["dateformat"];
        $apiOptions["timeFormat"] = $therecord["timeformat"];
        $apiOptions["extraoptions"] = $therecord["extraoptions"];
        $this->_processApiOptions($apiOptions);
        
        /**
          *  Set api and processing information
          */
        
        $this->apiCommand = $therecord["apicommand"];
        
        $this->apiDestination = "http://";
        if($therecord["ssl"])
            $this->apiDestination = "https://";
        
        $this->apiDestination .= $therecord["server"];
        if($therecord["port"])
            $this->apiDestination .= ":".$therecord["port"];
        $this->apiDestination .= $therecord["destscript"];
        
        $this->useSsl = $therecord["ssl"];
        $this->apiFormat = $therecord["dataformat"];
        $this->apiPassword = $therecord["apipassword"];
        $this->apiUsername = $therecord["apiusername"];
        $this->httpFormat = $therecord["httpformat"];
        $this->destTabledefid = $therecord["destuuid"];
        
        /**
          *  Need to get correct uuids (all, select, or a savedsearch) 
          */
        
        switch($therecord["whereclause"]){
            
            case "all":
                $querystatement = "
                    SELECT
                        `uuid`
                    FROM
                        `".$this->maintable."`
                    ";
                    
                $queryresult = $this->db->query($querystatement);
                
                $this->uuidArray = array();
                while($queryrecord = $this->db->fetchArray($queryresult))
                    $this->uuidArray[] = $queryrecord["uuid"];
                    
                break;
            
            case "select":
                //$this->uuidArray = $this->uuidArray;
                break;
            
            default:
    
                $querystatement = "
                    SELECT
                        `usersearches`.`sqlclause`,
                        `usersearches`.`type`
                    FROM
                        `usersearches`
                    WHERE
                        `usersearches`.`uuid`='".mysql_real_escape_string($therecord["whereclause"])."'
                    ";
                
                $queryresult = $this->db->query($querystatement);
                
                $searchrecord = $this->db->fetchArray($queryresult);
                
                $querystatement = "
                    SELECT
                        `uuid`
                    FROM
                        `".$this->maintable."`
                    WHERE
                        (".$searchrecord["sqlclause"].")";

                $queryresult = $this->db->query($querystatement);
                
                $this->uuidArray = array();
                while($queryrecord = $this->db->fetchArray($queryresult))
                    $this->uuidArray[] = $queryrecord["uuid"];
                
                break;
            
        }//end switch

    }//end function
    

    /**
      *  function processApiOptions
      *  @param array $apiOptions
      */

    function _processApiOptions($apiOptions){

        if(!$apiOptions["keepDestId"])
            $this->apiOptions["keepDestId"] = true;
        else
            $this->apiOptions["keepDestId"] = (bool)$apiOptions["keepDestId"];

        if(!$apiOptions["useUuid"])
            $this->apiOptions["useUuid"] = true;
        else
            $this->apiOptions["useUuid"] = (bool)$apiOptions["useUuid"];

        if(!$apiOptions["dateFormat"])
            $this->apiOptions["dateFormat"] = "SQL";
        elseif($this->_isValidDateFormat($apiOptions["dateFormat"]))
            $this->apiOptions["dateFormat"] = (string)$apiOptions["dateFormat"];
        else
            $this->apiOptions["dateFormat"] = "SQL";

        if(!$apiOptions["timeFormat"])
            $this->apiOptions["timeFormat"] = "24 Hour";
        elseif($this->_isValidTimeFormat($apiOptions["timeFormat"]))
            $this->apiOptions["timeFormat"] = (string)$apiOptions["timeFormat"];
        else
            $this->apiOptions["timeFormat"] = "24 Hour";

        if($extraOptions = json_decode($apiOptions["extraoptions"], true))
            foreach($extraOptions as $name => $value)
                $this->apiOptions[$name] = $value;
        
    }//end function --processApiOptions--


     /**
     * function _isValidDateFormat
     *
     * @param $format Format to be checked.
     *
     * @return bool True if it is valid, false if not.
     */

    function _isValidDateFormat($format) {

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

    }//end function _isValidDateFormat


    /**
     * function _isValidTimeFormat
     *
     * @param $format Format to be checked.
     *
     * @return bool True if it is valid, false if not.
     */

    function _isValidTimeFormat($format) {

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

    }//end function _isValidTimeFormat


    /**
     * function encode
     * @param array $query An array containg the data
     * @return string The encoded query
     */

    function encode($query) {

        switch($this->apiFormat){

            case "json":
                $query = json_encode($query);
                break;

            default:
                $query = NULL;
                break;

        }//end switch

        return $query;

    }//end function
    
    
    /**
      *  function decode
      *  @param string $response String encoded response
      *  @return array The decoded response.  NULL if cannot be decoded.
      */
    
    function decode($response) {
        
        switch($this->apiFormat){
            
            case "json":
                $response = json_decode($response, true);
                break;
            
            default:
                $response = NULL;
                break;
            
        }//end switch
        
        return $response;
        
    }//end function
    

    /**
      *  function construct
      *  @return array The constructed query, fully in array form (not encoded).
      */

    function construct(){
        
        $query = array();
        $query["phpbmsusername"] = $this->apiUsername;
        $query["phpbmspassword"] = $this->apiPassword;

        $i = 0;
        foreach($this->uuidArray as $uuid){

            $query["request"][$i]["command"] = $this->apiCommand;
            $query["request"][$i]["options"] = $this->apiOptions;
            $query["request"][$i]["tabledefid"] = $this->destTabledefid;
            $query["request"][$i]["data"] = $this->processor->getRecord($uuid, true);

            $i++;

        }//end foreach
        
        return $query;

    }//end function --construct--

    /**
      *  function construct
      *  @param array $query The properly encoded query
      *  @return string The response (if any) from the query
      */

    function send($query){

        $data = http_build_query($query);
        $opts["http"]["method"] = $this->httpFormat;
        $opts["http"]["header"] = "Content-type: application/x-www-form-urlencoded";
        
        switch($this->httpFormat){
            
            case "POST":
                $opts["http"]["content"] = $data;    
            break;
            
            case "GET":
                $this->apiDestination .= "?".$data;
            break;
        
        }//end if

        $context = stream_context_create($opts);
        $contents = "";
        
        $fp = fopen($this->apiDestination, 'r', false, $context);
        if($fp != false){
            $contents = stream_get_contents($fp);
            fclose($fp);
        }else
            $this->error = true;
        
        
        return $contents;

    }//end method --send--

    /**
      *  function process
      *  @return string The response (if any) from the query.  If an error is found
      *  (response of error), return false.
      */

    function process(){

        $query = $this->construct();
        if(isset($query["request"])){
            $query["request"] = $this->encode($query["request"]);
            $return = $this->send($query);
            $return = $this->decode($return);
        }else
            return true;
        
        if(isset($return["type"]))
            if($return["type"] == "error")
                $return = false;

        if($this->error)
            $return = false;

        return $return;

    }//end method --process--

}

?>