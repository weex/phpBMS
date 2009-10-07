<?php
/*
 $Rev: 643 $ | $LastChangedBy: nate $
 $LastChangedDate: 2009-09-02 14:00:56 -0600 (Wed, 02 Sep 2009) $
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
    require("../../include/session.php");
    
    if(!isset($_GET["id"]))
        $error = new appError(300,"Passed variable not set (id)");
    
    $querystatement = "
        SELECT
            `name`,
            `uuid`
        FROM
            `usersearches`
        WHERE
            `tabledefid` ='".mysql_real_escape_string($_GET["id"])."'
            AND
            `type` = 'SCH'
        ";
    
    $queryresult = $db->query($querystatement);
    
    $thereturn = array();
    $blankRecord = array("name"=>"<none>", "uuid"=>"");
    $thereturn[] = $blankRecord;
    
    while($therecord = $db->fetchArray($queryresult)){
        
        $addRecord = array(
            "name" => $therecord["name"],
            "uuid" => $therecord["uuid"]
        );
        
        $thereturn[] = $addRecord;
        
    }//end while
    
    $thereturn = json_encode($thereturn);
    
    echo $thereturn;
    
?>