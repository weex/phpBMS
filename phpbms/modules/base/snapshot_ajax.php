<?php 
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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

	@ include_once("../../include/session.php");
	@ include_once("../../include/common_functions.php");
	
	function updateTask($id,$completed,$type){
		global $dblink;
		
		if($completed)
			$compDate="CURDATE()";
		else
			$compDate="NULL";
		
		$querystatement="UPDATE notes SET completed=".$completed." , completeddate=".$compDate." WHERE id=".$id;
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(300,"Error Updating Note: ".mysql_error($dblink)."<br />".$querystatement);		
		
		if($completed && $type="TS")
			repeatTask($id);
		
		return "success";
		
	}
	
	function repeatTask($id){
		global $dblink;
		
		$querystatement="SELECT parentid,startdate,`repeat` FROM notes WHERE id=".$id;
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(300,"Error Retrieving Parent Note ID: ".mysql_error($dblink)."<br />".$querystatement);
		$therecord=mysql_fetch_array($queryresult);
		

		if($therecord["repeat"]==1 && $therecord["parentid"]==""){
			$therecord["parentid"]=$id;
		}

		if ($therecord["parentid"]){

			$lastTaskdate=dateFromSQLDate($therecord["startdate"]);
		
			$querystatement="SELECT id,startdate,enddate,repeatdays,repeatfrequency,repeattimes,repeattype,repeatuntildate FROM notes WHERE id=".$therecord["parentid"];
			$queryresult=mysql_query($querystatement,$dblink);
			if(!$queryresult) reportError(300,"Error Retrieving Parent Note Repeat Options: ".mysql_error($dblink)."<br />".$querystatement);
			$therecord=mysql_fetch_array($queryresult);
			$startdate=dateFromSQLDate($therecord["startdate"]);
			$enddate=dateFromSQLDate($therecord["enddate"]);
			$repeatuntil=dateFromSQLDate($therecord["repeatuntildate"]);
			$nextdate=$startdate;
			$rpTimes=1;

			switch($therecord["repeattype"]){
				case "repeatDaily":
					while($nextdate<=$lastTaskdate && ($therecord["repeattimes"]<=0 || $therecord["repeattimes"]>$rpTimes)){
						$nextdate=strtotime($therecord["repeatfrequency"]." days",$nextdate);
						$rpTimes++;
					}
					if($nextdate>$lastTaskdate && ($therecord["repeattimes"]>-1 || $nextdate<=$repeatuntil))
						createChildTask($therecord["id"],$nextdate,$startdate,$enddate);					
				break;
				case "repeatYearly":
					while($nextdate<=$lastTaskdate && ($therecord["repeattimes"]<=0 || $therecord["repeattimes"]>$rpTimes)){
						$nextdate=strtotime($therecord["repeatfrequency"]." years",$nextdate);
						$rpTimes++;
					}
					if($nextdate>$lastTaskdate && ($therecord["repeattimes"]>-1 || $nextdate<=$repeatuntil))
						createChildTask($therecord["id"],$nextdate,$startdate,$enddate);
				break;
				case "repeatMonthlybyDate":
					while($nextdate<=$lastTaskdate && ($therecord["repeattimes"]<=0 || $therecord["repeattimes"]>$rpTimes)){
						$tempdate=strtotime($therecord["repeatfrequency"]." months",$nextdate);
						if(date("d",$startdate)==date("d",$tempdate))
							$nextdate=$tempdate;
						else
							$nextdate=mktime(0,0,0,((integer) date("n",$nextdate))+$therecord["repeatfrequency"]+1,0,date("Y",$nextdate));						
						$rpTimes++;
					}
					if($nextdate>$lastTaskdate && ($therecord["repeattimes"]>-1 || $nextdate<=$repeatuntil))
						createChildTask($therecord["id"],$nextdate,$startdate,$enddate);
				break;
				case "repeatMonthlybyDay":
					while($nextdate<=$lastTaskdate && ($therecord["repeattimes"]<=0 || $therecord["repeattimes"]>$rpTimes)){
						$nextdate=strtotime($therecord["repeatfrequency"]." months",$nextdate);
						$rpTimes++;
					}
					if($nextdate>$lastTaskdate && ($therecord["repeattimes"]>-1 || $nextdate<=$repeatuntil)){
						$startWeekNum=ceil(strftime("%d",$startdate)/7);
						$nextday=mktime(0,0,0,strftime("%m",$nextday),1,strftime("%Y",$nextday));
						$nextLastDay=date("t",$nextdate);
						$success=false;
						while(!$success){							
							$nextWeekNum=ceil(strftime("%d",$nextdate)/7);
							$nextdate=strtotime("tomorrow",$nextdate);
							if(($nextWeekNum==$startWeekNum || ($startWeekNum==5 && strftime("%d",$nextdate)+7>$nextLastDay)) && strftime("%A",$startdate)==strftime("%A",$nextdate))
								$success=true;
						}					
						createChildTask($therecord["id"],$nextdate,$startdate,$enddate);
					}
				break;
				case "repeatWeekly":
					while($nextdate<$lastTaskdate && ($therecord["repeattimes"]<=0 || $therecord["repeattimes"]>$rpTimes)){
						$nextdate=strtotime($therecord["repeatfrequency"]." weeks",$nextdate);
						$rpTimes++;
					}
					if($nextdate>=$lastTaskdate  && ($therecord["repeattimes"]>-1 || $nextdate<=$repeatuntil)){
						$success=false;
						$dayAbbrev=getDayAbbrev($lastTaskdate);
						$lastTaskPos=strpos(" ".$therecord["repeatdays"],$dayAbbrev);
						if (strlen($therecord["repeatdays"])!=$lastTaskPos)					
							$lastTaskPos++;
						else {
							while(getDayAbbrev($nextdate)!="s")
								$nextdate=strtotime("yesterday",$nextdate);
							$nextdate=strtotime($therecord["repeatfrequency"]." weeks",$nextdate);
							$rpTimes++;							
							if($therecord["repeattimes"]>0 && $therecord["repeattimes"]<=$rpTimes)
								return true;
							else
								$lastTaskPos=1;
						}
							
						$newDayAbbrev=substr($therecord["repeatdays"],$lastTaskPos-1,1);
						if(getDayAbbrev($nextdate)==$newDayAbbrev)
							$success=true;
						else
							$success=false;	
						while(!$success){
							$nextdate=strtotime("tomorrow",$nextdate);
							if(getDayAbbrev($nextdate)==$newDayAbbrev)
								$success=true;
						}
						createChildTask($therecord["id"],$nextdate,$startdate,$enddate);
					}
				break;
			}//end switch
		}//end if
		return true;
	}

	function getDayAbbrev($day){
		switch(strftime("%A",$day)){
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
		}//end switch	
		return $dayAbbrev;
	}
	
	function createChildTask($parentid,$newdate,$startdate,$enddate){
		global $dblink;		
				
		$newenddate="NULL";
		if($enddate)			
			$newenddate="\"".strftime("%Y-%m-%d",$newdate+($enddate-$startdate))."\"";
					
		$querystatement="SELECT id,type,subject,content,status,starttime,private,modifiedby,location,importance,endtime,CURDATE() as creationdate,createdby,category,
					attachedtabledefid,attachedid,assignedtoid,assignedtodate,assignedtotime,assignedbyid
					FROM notes WHERE id=".$parentid;
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(300,"Error Retrieving Parent Note: ".mysql_error($dblink)."<br />".$querystatement);				
		$therecord=mysql_fetch_array($queryresult);
	
		$querystatement="SELECT id FROM notes WHERE parentid=".$parentid." AND completed=0 AND startdate=\"".strftime("%Y-%m-%d",$newdate)."\"";
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(300,"Error Retrieving Parent Note: ".mysql_error($dblink)."<br />".$querystatement);				
		if(mysql_num_rows($queryresult))
			return false;		
		
		if(!$therecord["assignedtoid"])
			$therecord["assignedtoid"]="NULL";
					
		$querystatement="INSERT INTO notes (parentid,startdate,enddate,completed,completeddate,`repeat`,repeatfrequency,repeattype,repeattimes,
					type,subject,content,status,starttime,private,modifiedby,location,importance,endtime,creationdate,createdby,category,
					attachedtabledefid,attachedid,assignedtoid,assignedtodate,assignedtotime,assignedbyid) VALUES (
					".$therecord["id"].", \"".strftime("%Y-%m-%d",$newdate)."\",".$newenddate.", 0, NULL,0,1,\"repeatDaily\",0,";
		$querystatement.="\"".$therecord["type"]."\", ";
		$querystatement.="\"".$therecord["subject"]."\", ";
		$querystatement.="\"".$therecord["content"]."\", ";
		$querystatement.="\"".$therecord["status"]."\", ";
		if($therecord["starttime"])
			$querystatement.="\"".$therecord["starttime"]."\", ";
		else
			$querystatement.="NULL, ";
		
		$querystatement.=$therecord["private"].", ";
		$querystatement.=$therecord["modifiedby"].", ";
		$querystatement.="\"".$therecord["location"]."\", ";
		$querystatement.="\"".$therecord["importance"]."\", ";
		if($therecord["endtime"])
			$querystatement.="\"".$therecord["endtime"]."\", ";
		else
			$querystatement.="NULL, ";
		$querystatement.="\"".$therecord["creationdate"]."\", ";
		$querystatement.=$therecord["createdby"].", ";
		$querystatement.="\"".$therecord["category"]."\", ";
		$querystatement.=$therecord["attachedtabledefid"].", ";
		$querystatement.=$therecord["attachedid"].", ";
		$querystatement.=$therecord["assignedtoid"].", ";
		if($therecord["assignedtodate"])
			$querystatement.="\"".$therecord["assignedtodate"]."\", ";
		else
			$querystatement.="NULL, ";			
		if($therecord["assignedtotime"])
			$querystatement.="\"".$therecord["assignedtotime"]."\", ";
		else
			$querystatement.="NULL, ";
		$querystatement.=$therecord["assignedbyid"].") ";
		
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(300,"Error Inserting Note: ".mysql_error($dblink)."<br />".$querystatement);		
		return true;
	}

	//=================================================================================================
	if(isset($_GET["cm"])){
		$thereturn="";
		switch($_GET["cm"]){
			case "updateTask":
				$thereturn=updateTask($_GET["id"],$_GET["cp"],$_GET["ty"]);
			break;
		}
		echo $thereturn;
	}
?>