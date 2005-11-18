<?php 
/*
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
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

function showSystemMessages(){
	global $dblink;
	
	$querystatement="SELECT notes.id,subject,content,concat(users.firstname,\" \",users.lastname) as createdby,
					date_format(notes.creationdate,\"%c/%e/%Y\") as creationdate
					FROM notes INNER JOIN users ON notes.createdby=users.id
					WHERE type=\"SM\" ORDER BY importance DESC,notes.creationdate";
					
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Error Retrieving System Messages: ".mysql_error($dblink)."<br />".$querystatement);
	
	if(mysql_num_rows($queryresult)){ 
	?>
	<div class="box">		
		<div style="float:right;cursor:pointer;cursor:hand;"><img src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-up.png" align="absmiddle" alt="hide" onClick="hideSection(this,'systemMessages')" width="16" height="16" border="0" /></div>
		<h2 style="margin-top:4px;">System Messages</h2>
		<div id="systemMessages" style="margin:0px;padding:0px">
		<?php while($therecord=mysql_fetch_array($queryresult)) {
				$therecord["content"]=str_replace("\n","<br />",$therecord["content"]);
		?>
		<div>
			<a href="/Show Content" onClick="showContent(<?php echo $therecord["id"]?>);return false;">
			<?php if($therecord["content"]) {?>
			<img src="../../common/image/left_arrow.gif" width="10" height="10" border="0" id="SMG<?php echo $therecord["id"]?>" />
			<?php } else {?>
			<img src="../../common/image/spacer.gif" width="10" height="10" border="0" id="SMG<?php echo $therecord["id"]?>" />				
			<?php }?><em style="font-weight:normal;"><?php echo $therecord["creationdate"]?> <?php echo $therecord["createdby"]?></em> - <?php echo $therecord["subject"]?></a>
		</div>
		<?php if($therecord["content"]) {?>
		<div class="small SysMessageContent"  id="SMT<?php echo $therecord["id"]?>">
			<?php echo $therecord["content"]?>
		</div>			
		<?php } } ?>
		</div>
	</div>
	<?php } 
}


function showTasks($userid,$type="Tasks"){
	global $dblink;
	$querystatement="SELECT id,type,subject, completed, if(enddate < CURDATE(),1,0) as ispastdue, startdate, enddate, private, assignedbyid, assignedtoid
				FROM notes
				WHERE ";
	switch($type){
		case "Tasks":
			$querystatement.=" type=\"TS\" AND (private=0 or (private=1 and createdby=".$userid.")) 
							   AND (completed=0 or (completed=1 and completeddate=CURDATE()))
							   AND (assignedtoid is null or assignedtoid=0)";
							   
		break;
		case "ReceivedAssignments":
			$querystatement.=" assignedtoid=".$userid." AND (completed=0 or (completed=1 and completeddate=CURDATE()))";
		break;
		case "GivenAssignments":
			$querystatement.=" assignedbyid=".$userid." AND (completed=0 or (completed=1 and completeddate=CURDATE()))";
		break;
	}
	$querystatement.="AND (
					(startdate is null AND enddate is null) OR
					(startdate is not null AND startdate <= DATE_ADD(CURDATE(),INTERVAL 7 DAY)) OR
					(enddate is not null AND enddate <= DATE_ADD(CURDATE(),INTERVAL 7 DAY)) 
				   )";

	$querystatement.=" ORDER BY importance DESC,notes.enddate,notes.endtime,notes.startdate DESC,notes.starttime DESC,notes.creationdate DESC";

	
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Error Retrieving System Messages: ".mysql_error($dblink)."<br />".$querystatement);
	if(mysql_num_rows($queryresult)){ 	
		while($therecord=mysql_fetch_array($queryresult)) {
		$className="task";		
		if($therecord["completed"]) 
			$className.=" taskCompleted";
		else if($therecord["ispastdue"]) 
			$className.=" taskPastDue";
		if($therecord["private"]) $className.=" taskPrivate";
		
	?>
	<div id="TS<?php echo $therecord["id"]?>" class="small <?php echo $className?>">
		<input type="hidden" id="TSprivate<?php echo $therecord["id"]?>" value="<?php echo $therecord["private"]?>"/>
		<input type="hidden" id="TSispastdue<?php echo $therecord["id"]?>" value="<?php echo $therecord["ispastdue"]?>"/>
		<input class="radiochecks" id="TSC<?php echo $therecord["id"]?>" name="TSC<?php echo $therecord["id"]?>" type="checkbox" value="1" <?php if($therecord["completed"]) echo "checked"?> onClick="checkTask(<?php echo $therecord["id"]?>,'<?php echo $therecord["type"]?>')" align="middle"/>
		<a href="<?php echo getAddEditFile(12)."?id=".$therecord["id"]?>&backurl=snapshot.php"><?php echo $therecord["subject"]?></a>
		<?php if($type=="Tasks") if($therecord["enddate"]) {?><em class="small">(<?php echo dateFormat($therecord["enddate"]) ?>)</em><?php } ?>
		<?php if($type!="Tasks"){?> <em>(<?php if($type=="ReceivedAssignments") $tid=$therecord["assignedbyid"]; else $tid=$therecord["assignedtoid"]; echo getUserName($tid)?>)</em><?php } ?>
	</div>
	<?php } } else {
	?><div class="small disabledtext">no <?php if($type=="Tasks") echo "tasks"; else echo "assignments"?></div><?php
	}
}

function showSevenDays($userid){
	
	$theday=mktime(0,0,0);
	
	$repeatArray =getRepeatableInTime($theday,strtotime("7 days",$theday),$userid);
	
	$today="Today - (";
	$rownum=0;
	?><table border="0" cellspacing="0" cellpadding="0" width="100%"><?php
	for($i=0;$i<7;$i++){
		?><tr><td colspan=2 class="eventDayName"><?php echo $today.strftime("%A",$theday); if($today){echo ")"; $today="";}?></td></tr><?php 
		$donext=true;

		$queryresult=getEventsForDay($theday,$userid,$repeatArray[$theday]);
		if (mysql_num_rows($queryresult)){
			while($therecord=mysql_fetch_array($queryresult)){
				$times=$therecord["starttime"]."&nbsp;";
				if($therecord["endtime"]){
					$times.= "- ";
					if($therecord["startdate"]!=$therecord["enddate"])
						$times.=$therecord["fenddate"]." ";
					$times.=$therecord["endtime"]." ";								
				}
				?><tr>
					<td class="small event" nowrap valign="top"><?php echo $times?></td>
					<td class="small event" valign="top" width="100%"><a href="<?php echo getAddEditFile(12)."?id=".$therecord["id"]?>&backurl=snapshot.php"><?php echo $therecord["subject"]?></a></td>
				</tr><?php
			}
		} else {
			?><tr><td colspan=2 class="small event disabledtext">no events</td></tr><?php		
		}		
						
		$theday=strtotime("tomorrow",$theday);
	}//end for
	?></table><?php
	
}

function getRepeatableInTime($fromdate,$todate,$userid){
	global $dblink;
	
	//first we create the array
	$theday=$fromdate;
	$repeatList=false;
	while ($theday<$todate){
		$repeatList[$theday]=array();
		$theday=strtotime("tomorrow",$theday);
	}
	
	$querystatement="SELECT id,startdate,repeatdays,repeatfrequency,repeattimes,repeattype,repeatuntildate FROM notes WHERE `repeat`=1 AND type=\"EV\" AND (private=0 or (private=1 and createdby=".$userid."))";
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Error Retrieving Repeatable Events: ".mysql_error($dblink)."<br />".$querystatement);
	
	while($therecord=mysql_fetch_array($queryresult)){		
		$startdate=dateFromSQLDate($therecord["startdate"]);
		$repeatuntil=dateFromSQLDate($therecord["repeatuntildate"]);

		foreach($repeatList as $targetdate=>$unused){
			if($therecord["repeattimes"]!=-1 || $repeatuntil>=$targetdate){
				$rpTimes=0;				
				$testdate=$startdate;
				switch($therecord["repeattype"]){
					case "repeatDaily":
						while($testdate<$targetdate && ($therecord["repeattimes"]<=0 || $therecord["repeattimes"]>$rpTimes)){
							$testdate=strtotime($therecord["repeatfrequency"]." days",$testdate);
							if($testdate==$targetdate)
								$repeatList[$targetdate][]=$therecord["id"];
							$rpTimes++;
						}
					break;
					case "repeatYearly":
						while($testdate<$targetdate && ($therecord["repeattimes"]<=0 || $therecord["repeattimes"]>$rpTimes)){
							$testdate=strtotime($therecord["repeatfrequency"]." years",$testdate);
							if($testdate==$targetdate)
								$repeatList[$targetdate][]=$therecord["id"];
							$rpTimes++;
						}
					break;
					case "repeatMonthlybyDate":
						while($testdate<$targetdate && ($therecord["repeattimes"]<=0 || $therecord["repeattimes"]>$rpTimes)){
							$testdate=strtotime($therecord["repeatfrequency"]." months",$testdate);
							if($testdate==$targetdate)
								$repeatList[$targetdate][]=$therecord["id"];
							$rpTimes++;
						}
					case "repeatMonthlybyDay":
						$testWeekNum=ceil(strftime("%d",$startdate)/7);
						$targetLastDay=date("t",$targetdate);
						while($testdate<$targetdate && ($therecord["repeattimes"]<=0 || $therecord["repeattimes"]>$rpTimes)){
							$testdate=strtotime($therecord["repeatfrequency"]." months",$testdate);
							$targetWeekNum=ceil(strftime("%d",$targetdate)/7);
														
							if(strftime("%m %Y",$testdate)==strftime("%m %Y",$targetdate) && ($testWeekNum==$targetWeekNum || ($testWeekNum==5 && strftime("%d",$targetdate)+7>$targetLastDay)) && strftime("%A",$startdate)==strftime("%A",$targetdate))
								$repeatList[$targetdate][]=$therecord["id"];
							$rpTimes++;
						}
					case "repeatWeekly":
						while($testdate<$targetdate && ($therecord["repeattimes"]<=0 || $therecord["repeattimes"]>$rpTimes)){
							$testdate=strtotime($therecord["repeatfrequency"]." weeks",$testdate);
							
							if(strftime("%W %Y",$testdate)==strftime("%W %Y",$targetdate)){
								$dayAbbrev="";
								switch(strftime("%A",$targetdate)){
									case "Sunday":
										$dayAbbrev="s";
									break;
									case "Monday":
										$dayAbbrev="m";
									break;
									case "Tuesday":
										$dayAbbrev="t";
									break;
									case "Wednesday":
										$dayAbbrev="w";
									break;
									case "Thursday":
										$dayAbbrev="r";
									break;
									case "Friday":
										$dayAbbrev="f";
									break;
									case "Saturday":
										$dayAbbrev="a";
									break;
									
								}
								if (strpos(" ".$therecord["repeatdays"],$dayAbbrev))
									$repeatList[$targetdate][]=$therecord["id"];
							}
							$rpTimes++;
						}
					break;
				}//end switch
			}//end if
		}//end foreach (day)
	}//end while (record)
	return $repeatList;
}

function getEventsForDay($day,$userid,$repeatArray){
	global $dblink;
	
	$repeatIDs="";
	if(count($repeatArray)){
		$repeatIDs=" OR notes.id in(";
		foreach($repeatArray as $id)
			$repeatIDs.=$id.", ";
		$repeatIDs=substr($repeatIDs,0,strlen($repeatIDs)-2);
		$repeatIDs.=") ";
	}
	$querystatement="SELECT DISTINCT id,subject, startdate,enddate,
				date_format(enddate,\"%c/%e/%Y\") as fenddate,
				time_format(starttime,\"%l:%i %p\") as starttime,
				time_format(endtime,\"%l:%i %p\") as endtime
				FROM notes
				WHERE type=\"EV\" AND (startdate=\"".strftime("%Y-%m-%d",$day)."\" ".$repeatIDs.") AND (private=0 or (private=1 and createdby=".$userid.")) 
				ORDER BY notes.starttime,notes.endtime,importance DESC";

	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Error Retrieving days events: ".mysql_error($dblink)."<br />".$querystatement);
	
	return $queryresult;
}
?>

