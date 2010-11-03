<?php

class pushrecords extends phpbmsTable{

    var $_availableUserSearchesUuids = NULL;
    var $_availableTabledefUuids = NULL;
    
    /**
     * function pushrecords
     * @param $db
     * @param $tabledefid
     * @param $backurl
     */

    function pushrecords($db, $tabledefid, $backurl = NULL) {

        parent::phpbmsTable($db, $tabledefid, $backurl);

    }//end function --pushrecords--

    /**
      *  function getDefaults
      */

    function getDefaults(){

        $therecord = parent::getDefaults();

        $therecord["apicommand"] = "insert";
        $therecord["whereselection"] = "all";
        $therecord["customwhere"] = "";
        $therecord["usecustomdestuuid"] = 0;
        $therecord["customdestuuid"] = "";
        $therecord["customcommand"] = "";
        $therecord["keepdestid"] = 1;
        $therecord["useuuid"] = 1;

        return $therecord;

    }//end function --getDefaults--

    /**
      *  function getRecord()
      */

    function getRecord($id, $useUuid = false){

        $therecord = parent::getRecord($id, $useUuid);

        /**
          *  Custom api command
          */

        $therecord["customcommand"] = "";

        switch($therecord["apicommand"]){
            case "insert":
            case "update":
                break;

            default:
                $therecord["customcommand"] = $therecord["apicommand"];
                $therecord["apicommand"] = "custom";
                break;

        }//end switch

        /**
          *  Custom where (saved search)
          */

        $therecord["customwhere"] = "";
        $therecord["whereselection"] = "";
        switch($therecord["whereclause"]){

            case "all":
            case "select":
                $therecord["whereselection"] = $therecord["whereclause"];
                break;

            default:
                $therecord["customwhere"] = $therecord["whereclause"];
                $therecord["whereselection"] = "custom";
                break;

        }//end switch

        /**
          *  Custom destination tabledefuuid
          */

        $querystatement = "
            SELECT
                `id`
            FROM
                `tabledefs`
            WHERE
                `uuid` = '".mysql_real_escape_string($therecord["destuuid"])."'
            ";

        $queryresult = $this->db->query($querystatement);

        $therecord["usecustomdestuuid"] = 0;
        $therecord["customdestuuid"] = "";
        if(!$this->db->numRows($queryresult)){
            $therecord["usecustomdestuuid"] = 1;
            $therecord["customdestuuid"] = $therecord["destuuid"];
        }//end if



        return $therecord;

    }//end function --getDefaults--


    /**
     * function prepareVariables
     * @param $variables
     */

    function prepareVariables($variables) {

        $variables = parent::prepareVariables($variables);

        if(!isset($variables["usecustomdestuuid"]))
            $variables["usecustomdestuuid"] = 0;

        if($variables["usecustomdestuuid"])
            $variables["destuuid"] = $variables["customdestuuid"];

        switch($variables["whereselection"]){
            case "all":
            case "select":
                $variables["whereclause"] = $variables["whereselection"];
                break;

            default:
                $variables["whereclause"] = $variables["customwhere"];
                break;

        }//end switch

        switch($variables["apicommand"]){
            case "insert":
            case "update":
                break;

            case "custom":
                $variables["apicommand"] = $variables["customcommand"];
                break;
        }//end switch

        return $variables;

    }//end function --prepareVariables--
    
    /**
      *  function verifyVariables
      *  @param array $variables
      */
    
    function verifyVariables($variables){
        
        /**
          *  originuuid 
          */
        if(isset($variables["originuuid"])){
            
            if($this->_availableTabledefUuids === NULL)
                $this->_availableTabledefUuids = $this->_loadUUIDList("tabledefs");
            
            if(!in_array((string)$variables["originuuid"], $this->_availableTabledefUuids))
                $this->verifyErrors[] = "The `originuuid` field does not give an existing/acceptable tabledefinition uuid.";
            
        }else
            $this->verifyErrors[] = "The `originuuid` field must be set.";
        
        /**
          *  whereclause 
          */
        if(isset($variables["whereclause"])){
            
            switch($variables["whereclause"]){
                
                case "all":
                case "select":
                    break;
                
                default:
                    
                    if($this->_availableUserSearchesUuids === NULL)
                        $this->_availableUserSearchesUuids = $this->_loadUUIDList("usersearches");
                    
                    if(!in_array((string)$variables["whereclause"], $this->_availableUserSearchesUuids))
                        $this->verifyErrors[] = "The `usersearches` field does not give an existing/acceptable user search uuid or be of value 'all' or 'select'.";
                    
                    break;
                
            }//end switch
            
        }else
            $this->verifyErrors[] = "The `whereclause` field must be set.";
        
        
        /**
          *  apicommand
          */
        
        if(isset($variables["apicommand"])){
            
            if($variables["apicommand"] === "" || $variables["apicommand"] === NULL)
                $this->verifyErrors[] = "The `apicommand` field must not be blank.";
            
        }else
            $this->verifyErrors[] = "The `apicommand` field must be set.";
            
        
        /**
          *  dateformat 
          */
        
        if(isset($variables["dateformat"])){
            
            switch($variables["dateformat"]){
                
                case "SQL":
                case "English, UK":
                case "English, US":
                case "Dutch, NL":
                    break;
                
                default:
                    $this->verifyErrors[] = "The `dateformat` field's value must be one of the following:
                        'SQL', 'English, UK', 'English, US', or 'Dutch, NL'.";
                    break;
                
            }//end switch
            
        }else
            $this->verifyErrors[] = "The `dateformat` field must be set.";
        
        /**
          *  timeformat 
          */
        if(isset($variables["timeformat"])){
            
            switch($variables["timeformat"]){
                
                case "24 Hour":
                case "12 Hour":
                    break;
                
                default:
                    $this->verifyErrors[] = "The `timeformat` field's value
                        must be one of the following: '24 Hour' or '12 Hour'.";
                    break;
                
            }//end switch
            
        }else
            $this->verifyErrors[] = "The `timeformat` field must be set.";
        
        /**
          *  http format 
          */
        if(isset($variables["httpformat"])){
            
            switch($variables["httpformat"]){
                
                case "POST":
                case "GET":
                    break;
                
                default:
                    $this->verifyErrors[] = "The `httpformat` field's value must either be 'POST' or 'GET'.";
                    break;
                
            }//end switch
            
        }else
            $this->verifyErrors[] = "The `httpformat` field must be set.";
        
        /**
          *  data format 
          */
        if(isset($variables["dataformat"])){
            
            switch($variables["dataformat"]){
                
                case "json":
                    break;
                
                default:
                    $this->verifyErrors[] = "The `dataformat` field's value must be 'json'.";
                    break;
                
            }//end switch
            
        }else
            $this->verifyErrors[] = "The `dataformat` field must be set.";
        
        /**
          *  Booleans 
          */
        if(isset($variables["useuuid"]))
				if($variables["useuuid"] && $variables["useuuid"] != 1)
					$this->verifyErrors[] = "The `useuuid` field must be a boolean (equivalent to 0 or exactly 1).";
        
        if(isset($variables["keepdestid"]))
				if($variables["keepdestid"] && $variables["keepdestid"] != 1)
					$this->verifyErrors[] = "The `keepdestid` field must be a boolean (equivalent to 0 or exactly 1).";
                    
        if(isset($variables["ssl"]))
				if($variables["ssl"] && $variables["ssl"] != 1)
					$this->verifyErrors[] = "The `ssl` field must be a boolean (equivalent to 0 or exactly 1).";
        
        return parent::verifyVariables($variables);
        
    }//end function

}//end class


?>