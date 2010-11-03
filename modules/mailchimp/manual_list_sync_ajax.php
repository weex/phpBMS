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

class manualSync{

    var $db;

    function manualSync($db){

        $this->db = $db;
        $this->db->errorFormat = "json";

    }//end function init


    function checkSecurity(){

        $querystatement = "
            SELECT
                roleid
            FROM
                tableoptions
            WHERE
                name= 'massemail'
                AND tabledefid = 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083'
                ";

        $queryresult = $this->db->query($querystatement);

        $therecord = $this->db->fetchArray($queryresult);

        if(hasRights($therecord["roleid"]))
           return true;
        else
            return false;

    }//end function checkSecurity


    function sync(){

        $listSync = new listSync(
                                 $this->db,
                                 MAILCHIMP_APIKEY,
                                 MAILCHIMP_LIST_ID,
                                 MAILCHIMP_LAST_SYNC_DATE,
                                 NULL,
                                 MAILCHIMP_SECURE
                                 );

        $response = $listSync->process();

        if(!isset($response["type"])){
            $response = array();
            $response["type"] = "error";
            $response["details"] = array("message"=>"Fatal error: no valid response from script.", "code"=>NULL);
        }//end if

        $response = json_encode($response);
        echo($response);

    }//end function sync

}//end class

/**
 * PROCESSING ==================================================================
 */
if(!isset($noOutput)){

    require("../../include/session.php");
    include("include/MCAPI.class.php");//for MCAPI CLASS (listSync class uses it)
    include("include/list_sync.php");//for listSync class

    $sync = new manualSync($db);

    if($sync->checkSecurity())
        $sync->sync();
    else{

        $response = array();
        $response["type"] = "error";
        $response["details"] = array("message"=>"No rights to run script", "code"=>NULL);
        echo json_encode($response);

    }//endif

}//endif


?>
