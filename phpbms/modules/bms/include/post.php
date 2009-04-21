<?php
/*
 $Rev: 311 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-10-02 19:51:27 -0600 (Tue, 02 Oct 2007) $
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

    class poster{

        var $db;

        var $startdate = NULL;
        var $enddate = NULL;

        var $sections = array();

        function poster($db, $startdate, $enddate){

            $this->db = $db;
            $this->startdate = $startdate;
            $this->enddate = $enddate;

        }//end function init


        function process($variables){

            $return = false;

            if(isset($variables["cmd"])){

                switch($variables["cmd"]){

                    case "find":
                        $return = "date range updated";
                        break;

                    case "post":

                        $sectionsToPost = array();

                        foreach($variables as $key=>$value)
                            if(substr($key,0,3) === "sec")
                                $sectionsToPost[] = $value;

                        if(count($sectionsToPost))
                            $return = $this->post($sectionsToPost)." records posted.";
                        else
                            $return = "no records to post";
                        break;

                }//endswitch

            }//endif

            return $return;

        }//end function process


        function getSections(){

            $db = $this->db;

            //query tabledefs for all tabledefs that can post
            $querystatement = "
                SELECT
                    tabledefs.id,
                    modules.name,
                    tabledefs.maintable
                FROM
                    tabledefs INNER JOIN modules ON tabledefs.moduleid = modules.id
                WHERE
                    tabledefs.canpost = 1";

            $queryresult = $this->db->query($querystatement);

            while($therecord = $this->db->fetchArray($queryresult)){

                $includeFile = "../".$therecord["name"]."/include/".$therecord["maintable"].".php";

                if(file_exists($includeFile)){

                    include_once($includeFile);

                    if(class_exists($therecord["maintable"]."Post")){

                        $class = $therecord["maintable"]."Post";

                        $this->sections[$therecord["maintable"]]["id"] = $therecord["id"];
                        $this->sections[$therecord["maintable"]]["class"] = new $class($this->db);

                    }//endif

                }//endif

            }//endwhile

        }//end function getAreas


        function showSections(){

            ?>
                <table id="postSections" cellpadding="0" cellspacing="0" border="0">
                    <thead>
                        <tr>
                            <th id="ttype">transaction type</th>
                            <th>within<br />range</th>
                            <th>total<br />records</th>
                        </tr>
                    </thead>
                    <tbody>
            <?php

            foreach($this->sections as $sectionName => $section){

                ?><tr><?php

                $recordsInDateRange = count($section["class"]->getRecordsToPost($this->startdate, $this->enddate));
                $totalRecords = count($section["class"]->getRecordsToPost());

                if($recordsInDateRange){

                    ?>
                        <td><input type="checkbox" id="<?php echo $sectionName?>" name="sec<?php echo $sectionName?>" value="<?php echo $sectionName?>" checked="checked"/><label for="<?php echo $sectionName?>"> <?php echo formatVariable($sectionName)?></label></td>
                        <td><input type="text" readonly="readonly" value="<?php echo $recordsInDateRange ?>" size="5"/></td>
                    <?php

                } else {

                    ?>
                        <td class="non"><input type="checkbox" id="<?php echo $sectionName?>" name="sections" value="<?php echo $sectionName?>" disabled="disabled" /><label for="<?php echo $sectionName?>"> <?php echo formatVariable($sectionName)?></label></td>
                        <td><input type="text" disabled="disabled" class="uneditable" value="<?php echo $recordsInDateRange ?>" size="5"/></td>
                    <?php

                }//endif


                ?>
                    <td><input type="text" disabled="disabled" class="uneditable" value="<?php echo $totalRecords ?>" size="5"/></td>
                </tr><?php


            }//endforeach

            ?>
                    </tbody>
                </table>
            <?php

        }//end function showSections


        function post($sectionsToPost){

            $postedRecords = 0;

            //create posting session record
            $sessionId = $this->createSession();

            //post each section
            foreach($this->sections as $sectionName => $section){

                if(in_array($sectionName, $sectionsToPost)){

                    $ids = $section["class"]->getRecordsToPost($this->startdate, $this->enddate);
                    $where = $section["class"]->whereFromIds($ids);

                    $postedRecords += $section["class"]->post($where, $sessionId);

                }//endif

            }//endforeach

            $this->updateSession($sessionId, $postedRecords);

            return $postedRecords;

        }//end function sectionsToPost


        function createSession(){

            $insertstatement = "
                INSERT INTO
                    postingsessions
                    (sessiondate, `source`, recordsposted, userid)
                VALUES
                    (NOW(), 'posting', 0 ,".$_SESSION["userinfo"]["id"].")";

            $this->db->query($insertstatement);

            return $this->db->insertId();

        }//end function createSession


        function updateSession($sessionId, $recordsPosted){

            $updatestatement = "
                UPDATE
                    postingsessions
                SET
                    recordsposted = ".$recordsPosted."
                WHERE
                    id = ".$sessionId;

            $this->db->query($updatestatement);

        }//end function updateSession

    }//end class

?>
