<?php
/*
 $Rev: 254 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-07 18:38:38 -0600 (Tue, 07 Aug 2007) $
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

if(class_exists("phpbmsTable")){

    class productcategories extends phpbmsTable{

        function checkForValidParentid($uuid, $parentid){

            if((string)$parentid != ""){
                $querystatement = "
                    SELECT
                        `id`
                    FROM
                        `productcategories`
                    WHERE
                        (
                            `uuid` != '".$uuid."'
                            AND (`parentid` = '' OR `parentid` != '".$uuid."')
                            AND (`uuid` = '".$parentid."')
                        )
                    ";

                $queryresult = $this->db->query($querystatement);

                return $this->db->numRows($queryresult);
            }else{
                return true;
            }//end if

        }//end function

        function verifyVariables($variables){

            //check booleans
            if(isset($variables["webenabled"]))
                if($variables["webenabled"] && $variables["webenabled"] != 1)
                    $this->verifyErrors[] = "The `webenabled` field must be a boolean (equivalent to 0 or exactly 1).";

            if(isset($variables["parentid"])){

                $tempParentId = $variables["parentid"];

                $tempUUID = "";
                if(isset($variables["uuid"]))
                    $tempUUID = $variables["uuid"];

                if(!$this->checkForValidParentid($tempUUID, $tempParentId))
                    $this->verifyErrors[] = "The `parentid` field does not give a valid parent id.";

            }//end if

            return parent::verifyVariables($variables);

        }//end method

        function showParentsSelect($uuid = "", $value){

            $id = mysql_real_escape_string($uuid);
            $value = mysql_real_escape_string($value);

            $querystatement = "
                SELECT
                    `uuid`,
                    `name`
                FROM
                    `productcategories`
                WHERE
                    `uuid` != '".$uuid."'
                    AND (`parentid` = '' OR `parentid` != '".$uuid."')
                    AND (`inactive` = 0 OR `uuid` = '".$value."')";

            $queryresult = $this->db->query($querystatement);

            ?>
                <label for="parentid">Parent Category</label><br />
                <select id="parentid" name="parentid">
                    <option value="" <?php if($value == "") echo 'selected="selected"'?>>No Parent</option>
                    <?php

                        while($therecord = $this->db->fetchArray($queryresult)){

                            ?><option value="<?php echo $therecord["uuid"]?>" <?php if($therecord["uuid"] == $value) echo 'selected="selected"'?>><?php echo formatVariable($therecord["name"]); ?></option><?php

                        }//endwhile

                    ?>
                </select>
            <?php


        }//end function showParentsSelect

    }//end class

}//end if
?>
