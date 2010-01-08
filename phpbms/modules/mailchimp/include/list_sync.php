<?php
/*
 $Rev: 267 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-14 13:08:27 -0600 (Tue, 14 Aug 2007) $
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
class listSync{

    /**
      *  $lastSyncDate
      *  @var string The last date the list has been synced
      */
    var $lastSyncDate = NULL;
    /**
      *  $batchLimit
      *  @var int The size of individual "batches" of clients to be
      *  pulled/pushed.
      */
    var $batchLimit = 500;
    /**
      *   $listId
      *   @var string The list id of the MailChimp mailing list.
      */
    var $listId;

    /**
      *  $api
      *  @var object The MCAPI object.
      */
    var $api;
    /**
      *  $db
      *  @var object The phpbms database object.
      */
    var $db;
    /**
      *  $errors
      *  @var array An array of errors.  Each containing the 'message' and
      *  'code' fields.
      */
    var $errors = array();
    /**
      *  $stopScript
      *  @var bool Whether or not to contiune the process function's internal
      *  function calls (excepting error reporting)
      */
    var $stopScript = false;


    /**
      *  function listSync
      *  Instaciation function
      *  @param object $db The phpbms database object
      *  @param string $apiKey The MailChimp api key
      *  @param string $listId The MailChimp list id
      *  @param string $lastSyncDate The last date that the bms has synchronized
      *  with the MailChimp list
      *  @param integer $batchlimit The size of the individual batches
      *  @param bool $secure Whether to send info over ssl
      */

    function listSync($db, $apiKey, $listId, $lastSyncDate = NULL, $batchlimit = NULL, $secure = false){

        $this->db = $db;
        $this->listId = $listId;

        /**
          *   Check for a valid datetime format?
          */
        if($lastSyncDate)
            $this->lastSyncDate = $lastSyncDate;

        if((int)$batchlimit > 0)
           $this->batchlimit = (int)$batchlimit;

        $this->api = new MCAPI($apiKey, NULL, $secure);
        $this->api->ping();
        if($this->api->errorCode){
            $this->_addError("Unable to successfully ping(): ".$this->api->errorMessage, $this->api->errorCode, true);
            return false;
        }//end if

        /**
          *   check to see if there is a uuid field
          */
        $hasUuid = false;
        $mergeVars = $this->api->listMergeVars($this->listId);
        if($this->api->errorCode){
            $this->_addError("Unable to access list: ".$this->api->errorMessage, $this->api->errorCode, true);
            return false;
        }//end if

        foreach($mergeVars as $mergeVar){

            if($mergeVar["tag"] == "UUID"){
                $hasUuid = true;
                break;
            }//end if

        }//end foreach

        if(!$hasUuid){
            $this->_addError("The list does not have a merge variable with tag of 'UUID'.", NULL, true);
            return false;
        }

    }//end function

    /**
      *   function _addError
      *
      *   Add an error to the class' error array.
      *
      *   @param string $message Text of error message
      *   @param int $errorCode Error number
      *   @param bool $fatal Whether or not the error is fatal, and if the process
      *   function needs to be stopped.
      */

    function _addError($message, $errorCode = NULL, $fatal = false){

        $tempArray["message"] = $message;
        $tempArray["code"] = $errorCode;

        if($fatal){
            $this->stopScript = true;
            $tempArray["errorType"] = "error";
        }else{
            $tempArray["errorType"] = "warning";
        }//end if

        $this->errors[] = $tempArray;

    }//end function


    /**
      *   function _reportResult
      *
      *   Reports the result of the process function.
      *   @return array Result of the process function.
      *   @returnf string type Type of result (either 'error' or 'success')
      *   @returnf array details The details of the result.  Only relevant for
      *   returns of type 'error'.  It is an array of error message and error
      *   code pairs.
      */

    function _reportResult(){

        $return = array();

        if(count($this->errors)){

            if($this->stopScript)
                $return["type"] = "error";
            else
                $return["type"] = "warning";

            $return["details"] = $this->errors;

        }else{

            $return["type"] = "success";
            $return["details"] = array();

        }//end if

        return $return;

    }//end foreach


    /**
      *  function pullChanges
      *
      *  Pull any changes from the Mailchimp side.  Usually this means people
      *  who have unsubscribed and people who cannot be reached via the email
      *  address.
      */

    function pullChanges(){

        /**
          *  pull all the unsubscribed
          */
        $unsubscribed = array();
        $start = 0;
        do{

            $members = $this->api->listMembers($this->listId, 'unsubscribed', $this->lastSyncDate, $start, $this->batchLimit);
            if($this->api->errorCode){
                $this->_addError("Unable to load listMembers(): ".$this->api->errorMessage, $this->api->errorCode, true);
                return false;
            }//end if

            foreach($members as $member){

                $info = $this->api->listMemberInfo($this->listId, $member["email"]);
                if($this->api->errorCode){
                    $this->_addError("Unable to load listMemberInfo(): ".$this->api->errorMessage, $this->api->errorCode, true);
                    return false;
                }//end if

                $unsubscribed[] = $info["merges"]["UUID"];

            }//end foreach

            $start++;
        }while(count($members) == $this->batchLimit);

        /**
          *  pull all the cleaned
          */
        $start = 0;
        do{

            $members = $this->api->listMembers($this->listId, 'cleaned', $this->lastSyncDate, $start, $this->batchLimit);
            if($this->api->errorCode){
                $this->_addError("Unable to load listMembers(): ".$this->api->errorMessage, $this->api->errorCode, true);
                return false;
            }//end ifd

            foreach($members as $member){

                $info = $this->api->listMemberInfo($this->listId, $member["email"]);
                if($this->api->errorCode){
                    $this->_addError("Unable to load listMemberInfo(): ".$this->api->errorMessage, $this->api->errorCode, true);
                    return false;
                }//end if
                $unsubscribed[] = $info["merges"]["UUID"];

            }//end foreach

            $start++;
        }while(count($members) == $this->batchLimit);


        /**
          *  If there are records to unsubscribe, set their `canemail` to '0'
          */
        if(count($unsubscribed)){

            /**
              *  construct the in statement
              */
            $inValues = "";
            foreach($unsubscribed as $uuid)
                $inValues .= ",'".$uuid."'";

            $inValues = substr($inValues, 1);

            /**
              *  set the cleaned/unsubscribed to canemail = 0
              */
            $querystatement = "
                UPDATE
                    `clients`
                SET
                    `canemail` = '0'
                WHERE
                    `uuid` IN (".$inValues.")
            ";

            $queryresult = $this->db->query($querystatement);

        }//end if

    }//end function

    /**
      *   function unsubscribeInvalid
      *
      *   Unsubscribe the emails that are related to
      *   client records those that don't have an email (or don't exist),
      *   or that can't be emailed anymore.
      */

    function unsubscribeInvalid(){

        /**
          *   Get rid of the temorary table.
          */
        $dropTableStatement = "
            DROP TABLE IF EXISTS
                `tempEmail`
        ";
        $this->db->query($dropTableStatement);

        /**
          *  Create a temporary table
          */
        $createTableStatement = "
            CREATE TEMPORARY TABLE
                `tempEmail`
            (
                `email` varchar(128) default NULL
            ) ENGINE=INNODB";

        $this->db->query($createTableStatement);

        /**
          *  pull all the subscribed
          */
        $start = 0;
        do{
            $valuesClause = "";

            $members = $this->api->listMembers($this->listId, 'subscribed', NULL, $start, $this->batchLimit);

            if($this->api->errorCode){
                $this->_addError("Unable to load listMemberInfo():".$this->api->errorMessage, $this->api->errorCode, true);
                return false;
            }//end if

            foreach($members as $member)
                $valuesClause .= ",('".$member["email"]."')";

            $valuesClause = substr($valuesClause, 1);


            /**
              *  Put the subscribed into a temporary table
              */
            if($valuesClause){
                $insertStatement = "
                    INSERT INTO
                        `tempEmail`
                    (`email`)
                        VALUES
                    ".$valuesClause;

                $this->db->query($insertStatement);
            }//end if

            $start++;
        }while(count($members) == $this->batchLimit);


        /**
          *  Get all the emails of client records that are subscribed
          *  but should not be subscribed.
          */
        $selectStatement = "
            SELECT DISTINCT
                `tempEmail`.`email`
            FROM
                (`tempEmail` LEFT JOIN `clients` ON `tempEmail`.`email` = `clients`.`email`)
            WHERE
                `clients`.`canemail` = '0'
                OR
                `clients`.`email` IS NULL
        ";

        $selectresult = $this->db->query($selectStatement);

        /**
          *   Unsubscribe them from the mailchimp list
          */
        $unsubscribeList = array();
        while($therecord = $this->db->fetchArray($selectresult))
            $unsubscribeList[] = $therecord["email"];

        /**
          *  If there are records to unsubscribe (deleted), do so.
          */
        if(count($unsubscribeList)){

            $return = $this->api->listBatchUnsubscribe($this->listId, $unsubscribeList, true, false);
            if ($this->api->errorCode){
                $this->_addError("Batch Unsubscibe Failed!: ".$this->api->errorMessage, $this->api->errorCode, true);
                return false;
            }else
                foreach($return['errors'] as $val)
                    $this->_addError("Unsubscribing email ".$val["email"]." failed: ".$val["message"], $val["code"]);

        }//end if

        /**
          *   Get rid of the temorary table.
          */
        $dropTableStatement = "
            DROP TABLE IF EXISTS
                `tempEmail`
        ";
        $this->db->query($dropTableStatement);

    }//end function


    /**
      *  function pushChanges
      *
      *  Push changes from any client record that has been changed
      *  since the last sync date.
      */
    function pushChanges(){

        /**
          *   Get the changed records / fields
          */
        $querystatement = "
            SELECT
                `email`,
                `uuid`,
                `firstname`,
                `lastname`,
                `company`,
                `type`
            FROM
                `clients`
            WHERE
                `email` IS NOT NULL
                AND
                `email` != ''
                AND
                `canemail` != '0'
        ";

        /**
          *  If there is a last sync *date*, limit the records by their modified
          *  by date.
          */
        if($this->lastSyncDate)
            $querystatement .= " AND `modifieddate` > '".$this->lastSyncDate."'";


        $queryresult = $this->db->query($querystatement);

        /**
          *   Format the variables to be interpreted by Mailchimp.
          */
        $batchVars = array();
        while($therecord = $this->db->fetchArray($queryresult)){

            $tempArray["EMAIL"] = $therecord["email"];
            $tempArray["FNAME"] = $therecord["firstname"];
            $tempArray["LNAME"] = $therecord["lastname"];
            $tempArray["COMPANY"] = $therecord["company"];
            $tempArray["TYPE"] = $therecord["type"];
            $tempArray["UUID"] = $therecord["uuid"];

            $batchVars[] = $tempArray;
        }//end while

        /**
          *  Update / Insert the changes
          */
        if(count($batchVars)){

            $return = $this->api->listBatchSubscribe($this->listId, $batchVars, false, true);
            if($this->api->errorCode){
                $this->_addError("Batch Subscribe failed: ".$this->api->errorMessage, $this->api->errorCode, true);
                return false;
            }else{

                $instatement = "";

                foreach($return['errors'] as $val){

                    $this->_addError("Subscribing or updating uuid '".$val["row"]["UUID"]."' failed: ".$val["message"], $val["code"]);

                    $memberInfo = $this->api->listMemberInfo($this->listId, $val["row"]["EMAIL"]);

                    if($memberInfo["status"] != "subcribed")
                        $instatement .= ", '".$val["row"]["UUID"]."'";

                }//end foreach

                if($instatement){

                    $instatement = substr($instatement, 2);
                    $instatement = "(".$instatement.")";

                    $updatestatement = "
                        UPDATE
                            `clients`
                        SET
                            `canemail` = '0',
                            `comments` = CONCAT(IF(`comments` IS NOT NULL, `comments`, ''), '\n[', NOW(), '] The `canemail` field of this record has been unchecked by the mailchimp list sync')
                        WHERE
                            `uuid` IN ".$instatement;

                    $this->db->query($updatestatement);

                }//end if

            }//end if

        }//end if

    }//end function


    /*
     * function resetSyncDate
     *
     * Set the last sync date in the settings table to be NOW()
     */

    function resetSyncDate() {

        $querystatement = "
            UPDATE
                `settings`
            SET
                `value` = NOW()
            WHERE
                `name` = 'mailchimp_last_sync_date'
        ";

        $queryresult = $this->db->query($querystatement);

    }//end if


    /**
      *  function process
      *
      *  Perform the list sync
      */
    function process(){

        if(!$this->stopScript)
            $this->pullChanges();
        if(!$this->stopScript)
            $this->unsubscribeInvalid();
        if(!$this->stopScript)
            $this->pushChanges();
        if(!$this->stopScript)
            $this->resetSyncDate();

        return $this->_reportResult();

    }//end function

}//end class
?>
