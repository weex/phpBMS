<?php
/*
 $Rev: 702 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2010-01-01 15:14:57 -0700 (Fri, 01 Jan 2010) $
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
 * payment processing template object and file layout
 */
class paymentProcessor{

    var $db;
    var $salesorderUUID;


    /**
     * function paymentProcessor
     *
     * initializes the class
     *
     * @param object $db the db object
     * @param string $salesorderUUID the UUID of the salesorder that will be processed
     */
    function paymentProcessor($db, $salesorderUUID){

        $this->db = $db;
        $this->db->errorFormat = "json";

        $this->salesorderUUID = mysql_real_escape_string($salesorderUUID);

    }//end function init


    /**
     * function process
     *
     * process the actual payment.  This function needs to do a couple of things
     * It needs to retrieve any pertinent information from the sales order/client
     * record that it needs to pass on to the payment gateway.  It then needs
     * to interact with the gateway.  Next, if successfull it will probably need
     * to update the sales order record with the appropriate transaction id that
     * the gateway returned.  Lastly, it needs to return the response in JSON format.
     *
     * Remember, if you hit any errors, it is best to return errors in JSON format
     *
     * @return string json struct with the response from the gateway
     *
     * @returnf 'result' should be either success, declined, or error
     * @returnf 'transactionid' transactionid if success
     * @returnf 'details' any error or decline details if not success
     */
    function process(){

        $thereturn = array();

        /**
         * Retrieve pertinent sales order / client information
         * Also need to check if appropriate information is there to pass
         * to getway
         */

        /**
         * Make connection to gateway and send information
         */

        /**
         * parse results from gateway and format the return.
         */

        $thereturn["result"] = "success";
        $thereturn["transactionid"] = "TR000001";

        return json_encode($thereturn);

    }//end function process

}//end class


/**
 * PROCESSING ==============================================================
 */
if(!isset($noOutput)){


    require_once("../../include/session.php");

    /**
     * the processor will sent the sales order id via get
     */
    if(!isset($_GET["soid"]))
        $error = new appError(200, "Passed parameter expexted", "", true, true, false, "json");

    $processor = new paymentProcessor($db, $_GET["soid"]);

    echo $processor->process();

}//endif
?>
