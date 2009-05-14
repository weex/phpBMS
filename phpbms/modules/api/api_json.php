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

//Test Information
$_POST["phpbmsusername"] = "api";
$_POST["phpbmspassword"] = "spiderman";
$_POST["request"] = '[
    {
        "tabledefid": 2,
        "command" : "update",
        "data" : {"command":"save","id":"6","hascredit":0,"creditlimit":0,"type":"prospect","becameclient":null,"category":"","salesmanagerid":"","ds-salesmanagerid":"","leadsource":"","paymentmethodid":"0","shippingmethodid":"0","discountid":"0","taxareaid":"0","username":"","password":"","firstname":"Test x2","lastname":"API x2","company":"TEST","workphone":"","homephone":"","mobilephone":"","fax":"","otherphone":"","email":"","webaddress":"","taxid":"","addressid":"7","address1":"","address2":"","city":"","state":"","postalcode":"","country":"","comments":"","custom1":"0","custom2":"0","custom3":"","custom4":"","custom5":"","custom6":"G1242166240674","createdby":"","creationdate":"05\/12\/2009 5:08 PM","modifiedby":"","cancelclick":"0","modifieddate":"05\/12\/2009 5:08 PM"}
    }
]';


require("../../include/session.php");
require("include/apiclass.php");

if(!isset($_POST["request"]))
    $error = new appError(700, "passed post parameter 'request' missing", "malformed api request", true , true, true, "json");

$api = new api($db, $_POST["request"], "json");

$api->process();

?>
