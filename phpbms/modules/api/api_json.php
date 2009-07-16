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
//$_POST["phpbmsusername"] = "api";
//$_POST["phpbmspassword"] = "spiderman";
//$_POST["request"] = '[
//    {
//        "tabledefid": "tbld:c9ff2c8c-ce1f-659a-9c55-31bca7cce70e",
//        "command" : "insert",
//        "data" : {"name":"FOObar","percentage":"3","inactive":"0","createdby":"2","creationdate":"2009-06-25 14:38:02","modifiedby":"2","modifieddate":"2009-06-25 14:38:02","custom1":null,"custom2":null,"custom3":null,"custom4":null,"custom5":null,"custom6":null,"custom7":null,"custom8":null}
//    }
//]';
//$_POST["request"] = '[
//    {
//        "tabledefid": "tbld:c9ff2c8c-ce1f-659a-9c55-31bca7cce70e",
//        "command" : "update",
//        "data" : {"useUuid":false, "id":2, "name":"JOOOOO","percentage":"3","inactive":"0","createdby":"2","creationdate":"2009-06-25 14:38:02","modifiedby":"2","modifieddate":"2009-06-25 14:38:02","custom1":null,"custom2":null,"custom3":null,"custom4":null,"custom5":null,"custom6":null,"custom7":null,"custom8":null}
//    }
//]';
//$_POST["request"] = '[
//    {
//        "tabledefid": "tbld:c9ff2c8c-ce1f-659a-9c55-31bca7cce70e",
//        "command" : "get",
//        "data" : {"uuid":"tax:66dd77f5-c68e-74f4-5ce6-64768282b232"}
//    }
//]';
//$_POST["request"] = '[
//    {
//        "tabledefid": "tbld:c9ff2c8c-ce1f-659a-9c55-31bca7cce70e",
//        "command" : "inactivate",
//        "data" : [4],
//        "options" : {"useUuid" : false}
//    }
//]';

require("../../include/session.php");
require("include/apiclass.php");

if(!isset($_POST["request"]))
    $error = new appError(700, "passed post parameter 'request' missing", "malformed api request", true , true, true, "json");

$api = new api($db, $_POST["request"], "json");

$api->process();

?>
