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
//since the search functions need access to the  table, lets include required files
require_once("include/tables.php");

if(class_exists("phpbmsTable")){
	class notes extends phpbmsTable{
	
		function checkForNewerRepeats($parentid,$id){
	
			if ($parentid=="NULL")
				$parentid=$id;
				
			$querystatement="SELECT creationdate FROM notes WHERE id=".$id;
			$queryresult = $this->db->query($querystatement);
			$therecord=$this->db->fetchArray($queryresult);
		
			$querystatement="SELECT id FROM notes WHERE completed=0 AND parentid=".$parentid." AND creationdate > \"".$therecord["creationdate"]."\"";
			$queryresult = $this->db->query($querystatement);
	
			if($this->db->numRows($queryresult)) return true; else return false;
		}
	
	
		function repeatTaskUpdate($parentid){
			
			$querystatement="DELETE FROM notes WHERE completed=0 AND parentid=".$parentid;
			$queryresult = $this->db->query($querystatement);
			
			$querystatement="SELECT id FROM notes WHERE completed=1 AND parentid=".$parentid." ORDER BY startdate DESC LIMIT 0,1";
			$queryresult = $this->db->query($querystatement);
			if($this->db->numRows($queryresult)){
				$therecord=$this->db->fetchArray($queryresult);
				$this->repeatTask($therecord["id"]);
			} else {
				$this->repeatTask($parentid);
			}
		}
	
	
		function updateTask($id,$completed,$type){
		
			if($completed)
				$compDate="CURDATE()";
			else
				$compDate="NULL";
			
			$querystatement="UPDATE notes SET completed=".((int) $completed)." , completeddate=".$compDate." WHERE id=".((int) $id);
			$queryresult=$this->db->query($querystatement);
			
			if($completed && $type="TS")
				$this->repeatTask($id);
			
			return "success";
			
		}
	
		
		function repeatTask($id){
			
			$querystatement="SELECT parentid,startdate,`repeat` FROM notes WHERE id=".$id;
			$queryresult=$this->db->query($querystatement);
			$therecord=$this->db->fetchArray($queryresult);
			
			if($therecord["repeat"]==1 && $therecord["parentid"]==""){
				$therecord["parentid"]=$id;
			}
	
			if ($therecord["parentid"]){
	
				$lastTaskdate=stringToDate($therecord["startdate"],"SQL");
			
				$querystatement = "SELECT id,startdate,enddate,repeatdays,repeatfrequency,repeattimes,repeattype,repeatuntildate 
									FROM notes WHERE id=".$therecord["parentid"];
				$queryresult = $this->db->query($querystatement);
	
				$therecord = $this->db->fetchArray($queryresult);
				$startdate = stringToDate($therecord["startdate"],"SQL");
				$enddate = stringToDate($therecord["enddate"],"SQL");
				$repeatuntil = stringToDate($therecord["repeatuntildate"],"SQL");
				$nextdate = $startdate;
				$rpTimes=1;
	
				switch($therecord["repeattype"]){
					case "repeatDaily":
						while($nextdate<=$lastTaskdate && ($therecord["repeattimes"]<=0 || $therecord["repeattimes"]>$rpTimes)){
							$nextdate=strtotime($therecord["repeatfrequency"]." days",$nextdate);
							$rpTimes++;
						}
						if($nextdate>$lastTaskdate && ($therecord["repeattimes"]>-1 || $nextdate<=$repeatuntil))
							$this->createChildTask($therecord["id"],$nextdate,$startdate,$enddate);					
					break;
					case "repeatYearly":
						while($nextdate<=$lastTaskdate && ($therecord["repeattimes"]<=0 || $therecord["repeattimes"]>$rpTimes)){
							$nextdate=strtotime($therecord["repeatfrequency"]." years",$nextdate);
							$rpTimes++;
						}
						if($nextdate>$lastTaskdate && ($therecord["repeattimes"]>-1 || $nextdate<=$repeatuntil))
							$this->createChildTask($therecord["id"],$nextdate,$startdate,$enddate);
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
							$this->createChildTask($therecord["id"],$nextdate,$startdate,$enddate);
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
							$this->createChildTask($therecord["id"],$nextdate,$startdate,$enddate);
						}
					break;
					case "repeatWeekly":
						while($nextdate<$lastTaskdate && ($therecord["repeattimes"]<=0 || $therecord["repeattimes"]>$rpTimes)){
							$nextdate=strtotime($therecord["repeatfrequency"]." weeks",$nextdate);
							$rpTimes++;
						}
						if($nextdate>=$lastTaskdate  && ($therecord["repeattimes"]>-1 || $nextdate<=$repeatuntil)){
							$success=false;
							$dayAbbrev=$this->getDayAbbrev($lastTaskdate);
							$lastTaskPos=strpos(" ".$therecord["repeatdays"],$dayAbbrev);
							if (strlen($therecord["repeatdays"])!=$lastTaskPos)					
								$lastTaskPos++;
							else {
								while($this->getDayAbbrev($nextdate)!="s")
									$nextdate=strtotime("yesterday",$nextdate);
								$nextdate=strtotime($therecord["repeatfrequency"]." weeks",$nextdate);
								$rpTimes++;							
								if($therecord["repeattimes"]>0 && $therecord["repeattimes"]<=$rpTimes)
									return true;
								else
									$lastTaskPos=1;
							}
								
							$newDayAbbrev=substr($therecord["repeatdays"],$lastTaskPos-1,1);
							if($this->getDayAbbrev($nextdate)==$newDayAbbrev)
								$success=true;
							else
								$success=false;	
							while(!$success){
								$nextdate=strtotime("tomorrow",$nextdate);
								if($this->getDayAbbrev($nextdate)==$newDayAbbrev)
									$success=true;
							}
							$this->createChildTask($therecord["id"],$nextdate,$startdate,$enddate);
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
					
			$newenddate="NULL";
			if($enddate)			
				$newenddate="\"".dateToString($newdate+($enddate-$startdate),"SQL")."\"";
						
			$querystatement="SELECT id,type,subject,content,status,starttime,private,modifiedby,location,importance,endtime,CURDATE() as creationdate,createdby,category,
						attachedtabledefid,attachedid,assignedtoid,assignedtodate,assignedtotime,assignedbyid
						FROM notes WHERE id=".$parentid;
			$queryresult=$this->db->query($querystatement);
	
			$therecord=$this->db->fetchArray($queryresult);
		
			$querystatement="SELECT id FROM notes WHERE parentid=".$parentid." AND completed=0 AND startdate=\"".dateToString($newdate,"SQL")."\"";
			$queryresult=$this->db->query($querystatement);
	
			if($this->db->numRows($queryresult))
				return false;		
			
			if(!$therecord["assignedtoid"])
				$therecord["assignedtoid"]="NULL";
						
			$querystatement="INSERT INTO notes (parentid,startdate,enddate,completed,completeddate,`repeat`,repeatfrequency,repeattype,repeattimes,
						type,subject,content,status,starttime,private,modifiedby,location,importance,endtime,creationdate,createdby,category,
						attachedtabledefid,attachedid,assignedtoid,assignedtodate,assignedtotime,assignedbyid) VALUES (
						".$therecord["id"].", \"".dateToString($newdate,"SQL")."\",".$newenddate.", 0, NULL,0,1,\"repeatDaily\",0,";
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
			
			$queryresult=$this->db->query($querystatement);
		}
	
	
		function getAttachedTableDefInfo($id){
			if($id){
				$querystatement="SELECT displayname,editfile FROM tabledefs WHERE id =".((int) $id);
				$queryresult=$this->db->query($querystatement);
				$therecord=$this->db->fetchArray($queryresult);
			}else{
				$therecord["displayname"]="";
				$therecord["editfile"]="";
			}
					
			return $therecord;	
		}
	
	
		//CLASS OVERRIDES =============================================================================
	
		function getDefaults(){
			$therecord = parent::getDefaults();
			
			$therecord["type"]="NT";
			if(isset($_GET["ty"]))
				$therecord["type"]=$_GET["ty"];		
	
			$therecord["private"]=true;
	
			$therecord["attachedtabledefid"]=(isset($_GET["tabledefid"]))?$_GET["tabledefid"]:NULL;
			$therecord["attachedid"]=(isset($_GET["refid"]))?$_GET["refid"]:NULL;
			//form quickview
			if(isset($_GET["cid"])){
				$therecord["attachedtabledefid"]=2;
				$therecord["attachedid"]=$_GET["cid"];
			}
	
			$therecord["repeatfrequency"]=1;
			$therecord["repeattype"]="repeatDaily";
			
			return $therecord;
		}
		
		
		function formatVariables($variables,$userid){
	
				if(isset($variables["thetype"]))
					$variables["type"] = $variables["thetype"];
				
				if(!isset($variables["completed"]))
					$variables["completeddate"] = NULL;
	
				if($variables["enddate"] == "") {
					$variables["enddate"] = NULL;
					$variables["endtime"] = NULL;
				}
	
				if($variables["startdate"] == "") {
					$variables["startdate"] = NULL;
					$variables["starttime"] = NULL;
				}
				
				if(isset($variables["repeat"])) {
	
					$tempRepeatType="repeat".$variables["repeattype"];
					if($variables["repeattype"]=="Monthly")
						$tempRepeatType.=$variables["rpmo"];
						
					$variables["repeattype"] = $tempRepeatType;
									
					$tempRepeatDays="";	
					if($variables["repeattype"]=="repeatWeekly"){
						if(isset($variables["wosc"])) $tempRepeatDays.=$variables["wosc"];
						if(isset($variables["womc"])) $tempRepeatDays.=$variables["womc"];
						if(isset($variables["wotc"])) $tempRepeatDays.=$variables["wotc"];
						if(isset($variables["wowc"])) $tempRepeatDays.=$variables["wowc"];
						if(isset($variables["worc"])) $tempRepeatDays.=$variables["worc"];
						if(isset($variables["wofc"])) $tempRepeatDays.=$variables["wofc"];
						if(isset($variables["woac"])) $tempRepeatDays.=$variables["woac"];
					}
					$variables["repeatdays"] = $tempRepeatDays;
	
					if($variables["rpuntil"]<1) $variables["repeattimes"]=$variables["rpuntil"];
	
					if($variables["repeattimes"]!=-1)
						$variables["repeatuntildate"] = NULL;
						
				}else {
					$variables["repeat"] = 0;
					$variables["repeattimes"] = 0;
					$variables["repeatdays"] = "";
					$variables["repeatfrequency"] = 1;
					$variables["repeattype"] = "";
					$variables["repeatuntil"] = NULL;				
				}
	
				if($variables["assignedtoid"] != $variables["assignedtochange"]){
					if($variables["assignedtoid"] != "NULL")
						$variables["assignedbyid"] = $userid; 
					else
						$variables["assignedbyid"] = $userid; 
				}
		
			return $variables;
		}
		
		
		function updateRecord($variables, $modifiedby = NULL){
			
			if($modifiedby == NULL)
				$modifiedby = $_SESSION["userinfo"]["id"];
			
			$variables = $this->formatVariables($variables,$modifiedby);
		
			parent::updateRecord($variables, $modifiedby);
			
			// update repeat options where applicable.
			if($variables["typeCheck"]=="TS" && (isset($variables["repeat"]) && $variables["parentid"]=="NULL"))
				if($this->checkForNewerRepeats($variables["id"],$variables["id"]))
					$this->repeatTaskUpdate($variables["id"]);
					
			//repeat task when completed (children only)
			if(isset($variables["completed"]) && $variables["completedChange"]!=1 && $variables["typeCheck"]=="TS" && (isset($variables["repeat"]) || $variables["parentid"]!="NULL")) {
				if(!$this->checkForNewerRepeats($variables["parentid"],$variables["id"])){
					$this->repeatTask($variables["id"]);
				}
			}
			
		}
	
		function insertRecord($variables, $createdby = NULL){
			
			if($createdby == NULL)
				$createdby = $_SESSION["userinfo"]["id"];
			
			$variables = $this->formatVariables($variables,$createdby);
		
			$newid = parent::insertRecord($variables, $createdby);
			
			return $newid;
		}
	
	}//end class
}//end if

if(class_exists("searchFunctions")){
	class notesSearchFunctions extends searchFunctions{

		function mark_asread(){
		
			//passed variable is array of user ids to be revoked
			$whereclause = $this->buildWhereClause();
			
			$querystatement = "UPDATE notes SET notes.completed=1,modifiedby=\"".$_SESSION["userinfo"]["id"]."\" WHERE (".$whereclause.") AND type!=\"SM\";";
			$queryresult = $this->db->query($querystatement);

			$message = $this->buildStatusMessage();

			$message.=" marked as completed/read.";
			
			//for repeatable tasks, need to repeat dem!
			$querystatement="SELECT id FROM notes WHERE type=\"TS\" AND ((parentid IS NOT NULL AND parentid!=0 ) OR `repeat`=1) AND (".$whereclause.")";
			$queryresult = $this->db->query($querystatement);
			if ($this->db->numRows($queryresult)){
			
				$thetable = new notes($this->db,12);
				while($this->therecord=$db->fetchArray($queryresult)){
					$thetable->repeatTask($therecord["id"]);
				}
			}
					
			return $message;
		}
		
		
		//delete notes
		function delete_record(){
			
			//passed variable is array of user ids to be revoked
			$whereclause = $this->buildWhereClause("notes.id");
		
			//we need to check for incomplete repeatable child tasks
			$querystatement="SELECT distinct notes.parentid FROM notes where notes.parentid is not null and notes.completed=0 and (".$whereclause.")";
			$repeatqueryresult = $this->db->query($querystatement);
		
			$querystatement = "DELETE FROM notes WHERE ((notes.createdby=".$_SESSION["userinfo"]["id"]." or notes.assignedtoid=".$_SESSION["userinfo"]["id"].") OR (".$_SESSION["userinfo"]["admin"]." =1)) and (".$whereclause.") and (notes.`repeat`!=1);";
			$queryresult = $this->db->query($querystatement);
		
			$message = $this->buildStatusMessage();
			$message.=" deleted";
			
			//repeat where applicable
			if ($this->db->numRows($repeatqueryresult)){
		
				$thetable = new notes($this->db,12);
				
				while($therecord=$this->db->fetchArray($repeatqueryresult)){
					$querystatement="SELECT id FROM notes WHERE completed=1 AND parentid=".$therecord["parentid"]." ORDER BY startdate DESC LIMIT 0,1";
					$queryresult = $this->db->query($querystatement);

					if($this->db->numRows($queryresult)){
						$completedrecord=$this->db->fetchArray($queryresult);
						$thetable->repeatTask($completedrecord["id"]);
					} else {
						$thetable->repeatTask($therecord["parentid"]);
					}			
				}
			}
			
			return $message;
		
		}	
	
	}//end class
}//end if
?>