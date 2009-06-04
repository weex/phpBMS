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

include_once("../../../../include/session.php");
include_once("include/tables.php");
include("modules/base/include/notes.php");


class eventCalendar{

    var $db;

    function eventCalendar($db){

        $this->db = $db;

    }//end function init


    function displayWeek($userid, $dayInWeek = null){
        // Creates a week view calendar for the widget

        if(!$dayInWeek)
            $dayInWeek = mktime(0,0,0);

        $firstDay = $dayInWeek;
        $dayArray = localtime($firstDay,true);

        while($dayArray["tm_wday"]!= 0){

            $firstDay = strtotime("yesterday",$firstDay);
            $dayArray = localtime($firstDay,true);

        }//endwhile

        //build the initial array
        $events = array();
        $lastDay = strtotime("6 days",$firstDay);

        $tempDay = $firstDay;

        for($i=0;$i<7;$i++){

                $events["d".$tempDay] = array();
                $tempDay = strtotime("tomorrow",$tempDay);

        }//endfor

        //first lets get the regular events in the timeframe;
        $querystatement = "
            SELECT
                notes.id,
                notes.startdate,
                notes.starttime,
                notes.enddate,
                notes.endtime,
                notes.subject
            FROM
                notes
            WHERE
                (
                    notes.private = 0
                    OR notes.createdby=".$userid."
                )
                AND notes.type='EV'
                AND notes.repeating = 0
                AND notes.startdate >= '".dateToString($firstDay,"SQL")."'
                AND notes.startdate <= '".dateToString($lastDay,"SQL")."'";

        $queryresult = $this->db->query($querystatement);

        while($therecord = $this->db->fetchArray($queryresult))
                $events["d".stringToDate($therecord["startdate"],"SQL")]["t".stringToTime($therecord["starttime"],"24 Hour")][] = $therecord;

        //next we do recurring events
        $querystatement = "
            SELECT
                notes.id,
                notes.startdate,
                notes.starttime,
                notes.enddate,
                notes.endtime,
                notes.subject,
                notes.repeattype,
                notes.repeatevery,
                notes.firstrepeat,
                notes.lastrepeat,
                notes.timesrepeated,
                notes.repeatontheday,
                notes.repeatontheweek,
                notes.repeateachlist,
                notes.repeatuntil,
                notes.repeattimes
            FROM
                notes
            WHERE
                repeating =1
                AND (
                    notes.private = 0
                    OR notes.createdby=".$userid."
                ) AND notes.type='EV'
                AND (
                    notes.repeatuntil IS NULL
                    OR notes.repeatuntil >= '".dateToString($firstDay,"SQL")."'
                    )
                AND (
                    notes.repeattimes IS NULL
                    OR notes.repeattimes > notes.timesrepeated
                    )";

        $queryresult = $this->db->query($querystatement);

        $thetable = new notes($this->db, "tbld:a4cdd991-cf0a-916f-1240-49428ea1bdd1");

        while($therecord = $this->db->fetchArray($queryresult)){

            $dateArray = $thetable->getValidInRange(stringToDate($therecord["startdate"],"SQL"),$lastDay,$therecord);

            foreach($dateArray as $date){

                if($date >= $firstDay && $date <= $lastDay){

                    if($therecord["enddate"])
                        $therecord["enddate"] = dateToString($date + ( stringToDate($therecord["enddate"],"SQL") - stringToDate($therecord["startdate"],"SQL") ), "SQL");

                        $therecord["startdate"] = dateToString($date,"SQL");
                        $events["d".$date]["t".stringToTime($therecord["starttime"],"24 Hour")][] = $therecord;

                    }//endif

            }//endforeach

        }//endwhile

        $querystatement = "
            SELECT
                DECODE(password,'".ENCRYPTION_SEED."') AS decpass
            FROM
                users
            WHERE
                id=".$_SESSION["userinfo"]["id"];

        $queryresult = $this->db->query($querystatement);

        $passrec = $this->db->fetchArray($queryresult);

        $icallink="?u=".$_SESSION["userinfo"]["id"]."&amp;h=".md5("phpBMS".$_SESSION["userinfo"]["firstname"].$_SESSION["userinfo"]["lastname"].$_SESSION["userinfo"]["id"].$passrec["decpass"])

        ?>
        <input type="hidden" id="eventDateLast" value="<?php echo strtotime("-7 days",$firstDay)?>" />
        <input type="hidden" id="eventDateToday" value="<?php echo mktime(0,0,0)?>" />
        <input type="hidden" id="eventDateNext" value="<?php echo strtotime("tomorrow",$lastDay)?>" />

        <ul id="eventButtons">
            <li id="icalLi"><a href="ical.php<?php echo $icallink ?>" title="ical subscription link" id="icalA"><span>ical</span></a>&nbsp;</li>
            <li><button id="eventLastWeek" type="button" title="previous week" class="smallButtons"><span>&lt;&lt;</span></button></li>
            <li><button id="eventToday" type="button" title="today" class="smallButtons"><span>today</span></button></li>
            <li><button id="eventNextWeek" type="button" title="next week" class="smallButtons"><span>&gt;&gt;</span></button></li>
        </ul>
        <table border="0" cellspacing="0" cellpadding="0" width="100%" id="eventsList"><?php

        foreach($events as $date => $times){

                ?><tr class="eventDayName" <?php if(mktime(0,0,0) === ((int) str_replace("d","",$date)) ) echo 'id="today"'?>>
                        <td nowrap="nowrap"><?php echo strftime("%A",((int) str_replace("d","",$date)) ); ?></td>
                        <td width="100%" align="right"><?php echo strftime("%b %e %Y",((int) str_replace("d","",$date)) ); ?></td>
                </tr><?php

                if(count($times)){
                        ksort($times);
                        foreach ($times as $time => $timeevents){

                                foreach($timeevents as $event){

                                ?>
                                <tr>
                                        <td nowrap="nowrap" valign="top" align="right"><?php echo formatFromSQLTime($event["starttime"])?></td>
                                        <td valign="top" ><a href="<?php echo getAddEditFile($this->db, "tbld:a4cdd991-cf0a-916f-1240-49428ea1bdd1")."?id=".$event["id"]?>&amp;backurl=snapshot.php"><?php echo htmlQuotes($event["subject"])?></a></td>
                                </tr><?php

                                }//endforeach events

                        }//endforeach time
                } else{
                        ?>
                        <tr>
                                <td class="disabledtext" align="right">no events</td>
                                <td>&nbsp;</td>
                        </tr><?php
                }// endif


        }//endforeach day

        ?></table><?php

    }//end displayWeek

}//end class eventCalendar


//==============================================================================
if(isset($_GET["cm"])){

    $thereturn = "";

    switch($_GET["cm"]){

        case "getWeek":
            if(!isset($_GET["d"]))
                    $_GET["d"]=mktime(0,0,0);
            else
                    $_GET["d"]=(int) $_GET["d"];

            $eventCalendar = new eventCalendar($db);

            $thereturn = $eventCalendar->displayWeek($_SESSION["userinfo"]["id"], $_GET["d"]);

            break;

    }//endswitch

    echo $thereturn;

}//endif
?>
