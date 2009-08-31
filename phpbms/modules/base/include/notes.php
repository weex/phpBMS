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

	var $weekArray = array("First"=>"1", "Second"=>"2", "Third"=>"3", "Fourth"=>"4", "Last"=>"5");
	var $dayOfWeekArray = array();


	function notes($db,$tabledefid = 0,$backurl = NULL){

		$this->dayOfWeekArray[nl_langinfo(constant("DAY_1"))] = 7;
		for($i=1; $i<=6; $i++)
			$this->dayOfWeekArray[nl_langinfo( constant("DAY_".($i+1)) )] = $i;

		parent::phpbmsTable($db,$tabledefid,$backurl);

	}


	function showWeeklyOptions($therecord,$repeatbase){
		if($therecord["repeattype"] == "Weekly")
			$daysSelected = explode("::",$therecord["repeateachlist"]);
		else
			$daysSelected = array(strftime("%u",$repeatbase));

		$daysAvailable = array(7,1,2,3,4,5,6);

		foreach($daysAvailable as $dayNum){
			$tempday = ($dayNum != 7)?($dayNum+1):(1);
			?><button id="dayOption<?php echo $dayNum?>" class="<?php

			if(in_array($dayNum,$daysSelected))
				echo "pressed";

			?>Buttons" type="button" value="<?php echo $dayNum?>" onclick="daySelect(this)"><?php

			echo nl_langinfo(constant("ABDAY_".$tempday));

			?></button><?php
		}


	}

	function showMonthlyOptions($therecord,$repeatbase){
		if($therecord["repeattype"] == "Monthly" && $therecord["repeateachlist"])
			$daysSelected = explode("::",$therecord["repeateachlist"]);
		else
			$daysSelected = array(strftime("%e",$repeatbase));


		for($dayNum = 1; $dayNum <= 31; $dayNum++){
			?><button id="monthDayOption<?php echo $dayNum?>" class="<?php

			if(in_array($dayNum,$daysSelected))
				echo "pressed";

			?>Buttons monthDays" type="button" value="<?php echo $dayNum?>" onclick="monthDaySelect(this)" <?php

				if($therecord["repeatontheday"])
					echo 'disabled="disabled"';

			?>><?php

			echo $dayNum;

			?></button><?php
			if(($dayNum % 7) == 0) echo "<br />";
		}

	}//end method


	function showYearlyOptions($therecord,$repeatbase){
		if($therecord["repeattype"] == "Yearly")
			$monthsSelected = explode("::",$therecord["repeateachlist"]);
		else
			$monthsSelected = array(date("n",$repeatbase));

		for($monthNum = 1; $monthNum <=12; $monthNum++){
			?><button id="yearlyMonthOption<?php echo $monthNum?>" class="<?php

			if(in_array($monthNum,$monthsSelected))
				echo "pressed";

			?>Buttons yearlyMonths" type="button" value="<?php echo $monthNum?>" onclick="yearlyMonthSelect(this)"><?php

			echo nl_langinfo(constant("ABMON_".$monthNum));

			?></button><?php
			if(($monthNum % 4) == 0) echo "<br />";
		}
	}


		function newerRepeats($parentid,$id){

			if ($parentid=="NULL")
				$parentid = getUuid($db, "tbld:a4cdd991-cf0a-916f-1240-49428ea1bdd1", $id);

			$querystatement = "
                            SELECT
                                creationdate
                            FROM
                                notes
                            WHERE id=".((int) $id);

			$queryresult = $this->db->query($querystatement);
			$therecord=$this->db->fetchArray($queryresult);

			$querystatement="
                            SELECT
                                id
                            FROM
                                notes
                            WHERE
                                completed = 0
                                AND parentid='".mysql_real_escape_string($parentid)."'
                                AND creationdate > '".$therecord["creationdate"]."'";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)) return true; else return false;

		}//end fucntion newerRepeats


		function resetRepeating($parentid){

			$deletstatement="DELETE FROM notes WHERE completed=0 AND parentid = '".$parentid."'";
			$this->db->query($deletstatement);

			$updatestatement="UPDATE notes SET parentid=NULL WHERE completed=1 AND parentid= '".$parentid."'";
			$queryresult = $this->db->query($updatestatement);

		}//end function resetRepeating


		function updateTask($id,$completed,$type){

			if($completed)
				$compDate="CURDATE()";
			else
				$compDate="NULL";

			$querystatement="UPDATE notes SET completed=".((int) $completed)." , completeddate=".$compDate." WHERE id=".((int) $id);
			$queryresult=$this->db->query($querystatement);

			if($completed && $type == "TS")
				$this->repeatTask($id);

			return "success";

		}//end function updateTask


		function repeatTask($id, $dateToCheck = NULL){

			if($dateToCheck == NULL)
				$dateToCheck = mktime(0,0,0);

                        //see if we need to grab the parent
			$querystatement = "
                            SELECT
                                parentnotes.id as parentid,
                                notes.repeating
                            FROM
                                notes LEFT JOIN notes AS parentnotes
                                ON parentnotes.uuid = notes.parentid
                            WHERE
                                notes.id=".((int) $id);

			$queryresult = $this->db->query($querystatement);

			$therecord = $this->db->fetchArray($queryresult);
				if($therecord["parentid"])
					$id = $therecord["parentid"];
				elseif(!$therecord["repeating"])
					return false;

			$querystatement = "
                            SELECT
                                notes.uuid,
                                notes.repeattype,
                                notes.repeatevery,
                                notes.startdate,
                                notes.enddate,
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
                                repeating = 1
                                AND id=".((int) $id)."
                                AND type='TS'
				AND (
                                    notes.repeatuntil IS NULL
                                    OR notes.repeatuntil >= '".dateToString($dateToCheck,"SQL")."'
                                    )
				AND (
                                    notes.repeattimes IS NULL
                                    OR notes.repeattimes < notes.timesrepeated
                                    )";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){
				$therecord = $this->db->fetchArray($queryresult);

				if($therecord["lastrepeat"])
					$startDate = stringToDate($therecord["lastrepeat"],"SQL");
				else
					$startDate = stringToDate($therecord["startdate"],"SQL");

				if($dateToCheck <= $startDate)
					$dateToCheck = strtotime("tomorrow",$startDate);

				$dateArray = $this->getValidInRange($startDate,$dateToCheck,$therecord);

				foreach($dateArray as $date){
					if($date != $startDate){
						if($therecord["enddate"])
							$enddate = stringToDate($therecord["enddate"],"SQL");
						else
							$enddate = NULL;

						$return = $this->createChildTask($therecord["uuid"], $date, stringToDate($therecord["startdate"],"SQL"), $enddate );

						if($return){

							//child created update the parentrecord
							$updatestatement = "UPDATE notes SET ";
							$updatestatement .= "lastrepeat='".dateToString($date,"SQL")."',";
							if(!$therecord["firstrepeat"])
								$updatestatement .= "firstrepeat='".dateToString($date,"SQL")."',";
							$updatestatement .="timesrepeated = timesrepeated+1 WHERE id =".$id;

							$this->db->query($updatestatement);

							break;
						}//endif

					}//endif

				}//endforeach

			}//endif

		}//end method


		function getValidInRange($startDate,$endDate,$therecord){
			$nextDate = $startDate;

			//should pad the end date to make sure we get all weekly repeats
			$endDate = strtotime("+7 days",$endDate);

			$validDates = array();

			while($nextDate <= $endDate){

				switch($therecord["repeattype"]){
					case "Daily":
						//==================================================================================
						$validDates[] = $nextDate;
						$nextDate = strtotime("+".$therecord["repeatevery"]." days",$nextDate);
						break;

					case "Weekly":
						//==================================================================================
						$weekDayArray = explode("::",$therecord["repeateachlist"]);

						//need to start from the sunday of the current week
						$tempDate = strtotime(nl_langinfo( constant("DAY_1") ),$nextDate);
						$tempDate = strtotime("-7 days",$tempDate);

						foreach($weekDayArray as $weekday){
							if($weekday == 7)
								$validDates[]=$tempDate;
							else{
								$weekday++;
								$validDates[] = strtotime(nl_langinfo( constant("DAY_".$weekday) ),$tempDate);
							}
						}// endforeach


						$nextDate = strtotime("+".$therecord["repeatevery"]." week",$nextDate);

						break;

					case "Monthly":
						//==================================================================================
						$dateArray = localtime($nextDate,true);
						$daysInMonth = date("d", mktime(0,0,0,$dateArray["tm_mon"],0,$dateArray["tm_year"]+1900) );

						if($therecord["repeateachlist"]){
							$dayArray = explode("::",$therecord["repeateachlist"]);

							foreach($dayArray as $theday)
								$validDates[] = mktime(0,0,0,$dateArray["tm_mon"]+1,$theday,$dateArray["tm_year"]+1900);

						} else{
							// check for things like second tuesday or last friday;
							$tempDate = mktime(0,0,0,$dateArray["tm_mon"]+1,1,$dateArray["tm_year"]+1900);
							$weekday = $therecord["repeatontheday"];
							$weekday = ($weekday == 7)? 1: ($weekday+1);
							if($therecord["repeatontheday"] != strftime("%u",$tempDate));
								$tempDate = strtotime(nl_langinfo( constant("DAY_".$weekday) ),$tempDate);

							while(date("n",$tempDate) == ($dateArray["tm_mon"]+1)){

								if($therecord["repeatontheweek"] == 5){
									// 5 is the "last" option, so we just need to see if
									// the date falls in the last 6 days
									if($daysInMonth - date("d",$tempDate) < 7)
										$validDates[] = $tempDate;

								} else {
									if( ceil(date("d",$tempDate)/7) == $therecord["repeatontheweek"])
										$validDates[] = $tempDate;
								}// endif

								$tempDate = strtotime("+7 days",$tempDate);

							}// endwhile
						}//endif

						$nextDate = strtotime("+".$therecord["repeatevery"]." months", $nextDate);
						break;

					case "Yearly":
						//==================================================================================
						$monthArray = explode("::",$therecord["repeateachlist"]);
						foreach($monthArray as $monthNum){
							$dateArray = localtime($nextDate,true);
							$daysInMonth = date("d", mktime(0,0,0,$monthNum,0,$dateArray["tm_year"]+1900) );

							if(!$therecord["repeatontheday"]){
								$tempDay = ($dateArray["tm_mday"] > $daysInMonth)? $daysInMonth :$dateArray["tm_mday"];
								$validDates[] = mktime(0,0,0,$monthNum,$tempDay,$dateArray["tm_year"]+1900);

							} else {
								// check for things like second tuesday or last friday;
								$tempDate = mktime(0,0,0,$monthNum,1,$dateArray["tm_year"]+1900);

								$weekday = $therecord["repeatontheday"];
								$weekday = ($weekday == 7)? 1: ($weekday+1);
								if($therecord["repeatontheday"] != strftime("%u",$tempDate));
									$tempDate = strtotime(nl_langinfo( constant("DAY_".$weekday) ),$tempDate);


								while(date("n",$tempDate) == $monthNum){
									if($therecord["repeatontheweek"] == 5){
										// 5 is the "last" option, so we just need to see if
										// the date falls in the last 6 days
										if($daysInMonth - date("d",$tempDate) < 7)
											$validDates[] = $tempDate;

									} else {
										if( ceil(date("d",$tempDate)/7) == $therecord["repeatontheweek"])
											$validDates[] = $tempDate;
									}// endif

									$tempDate = strtotime("+7 days",$tempDate);

								}// endwhile

							}//endif

						}//endforeach

						$nextDate = strtotime("+".$therecord["repeatevery"]." years",$nextDate);

						break;
				}//endswitch

			}//end while

			return $validDates;

		}//end method


		function createChildTask($parentid, $newdate, $startdate, $enddate=NULL){

			//let's check to see if the new task already exists
			$querystatement = "
                            SELECT
                                id
                            FROM
                                notes
                            WHERE
                                parentid='".$parentid."'
                                AND startdate='".dateToString($newdate,"SQL")."'";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult))
				return false;

			$newenddate="NULL";
			if($enddate)
				$newenddate="\"".dateToString($newdate+($enddate-$startdate),"SQL")."\"";

			$querystatement="
                            SELECT
                                id,
                                uuid,
                                type,
                                subject,
                                content,
                                status,
                                starttime,
                                private,
                                modifiedby,
                                location,
                                importance,
                                endtime,
                                CURDATE() as creationdate,
                                createdby,
                                category,
                                attachedtabledefid,
                                attachedid,
                                assignedtoid,
                                assignedtodate,
                                assignedtotime,
                                assignedbyid
			    FROM
                                notes
                            WHERE
                                uuid='".$parentid."'";

			$queryresult=$this->db->query($querystatement);

			$therecord=$this->db->fetchArray($queryresult);

			$querystatement = "
                            SELECT
                                id
                            FROM
                                notes
                            WHERE
                                parentid = '".$parentid."'
                                AND completed = 0 AND startdate ='".dateToString($newdate,"SQL")."'";

			$queryresult=$this->db->query($querystatement);

			if($this->db->numRows($queryresult))
				return false;

			if(!$therecord["assignedtoid"])
				$therecord["assignedtoid"]="NULL";

			$querystatement="
                            INSERT INTO
                                notes
                                    (
                                    uuid,
                                    parentid,
                                    startdate,
                                    enddate,
                                    completed,
                                    completeddate,
                                    type,
                                    subject,
                                    content,
                                    status,
                                    starttime,
                                    private,
                                    modifiedby,
                                    location,
                                    importance,
                                    endtime,
                                    creationdate,
                                    createdby,
                                    category,
                                    attachedtabledefid,
                                    attachedid,
                                    assignedtoid,
                                    assignedtodate,
                                    assignedtotime,
                                    assignedbyid)
                            VALUES (
                                '".uuid(getUuidPrefix($this->db, "tbld:a4cdd991-cf0a-916f-1240-49428ea1bdd1").":")."',
				'".$therecord["uuid"]."',
                                '".dateToString($newdate,"SQL")."',
                                ".$newenddate.",
                                0,
                                NULL,";

			$querystatement.="'".$therecord["type"]."', ";
			$querystatement.="'".$therecord["subject"]."', ";
			$querystatement.="'".$therecord["content"]."', ";
			$querystatement.="'".$therecord["status"]."', ";
			if($therecord["starttime"])
				$querystatement.="'".$therecord["starttime"]."', ";
			else
				$querystatement.="NULL, ";

			$querystatement.=$therecord["private"].", ";
			$querystatement.=$therecord["modifiedby"].", ";
			$querystatement.="'".$therecord["location"]."', ";
			$querystatement.="'".$therecord["importance"]."', ";
			if($therecord["endtime"])
				$querystatement.="'".$therecord["endtime"]."', ";
			else
				$querystatement.="NULL, ";
			$querystatement.="'".$therecord["creationdate"]."', ";
			$querystatement.=$therecord["createdby"].", ";
			$querystatement.="'".$therecord["category"]."', ";
			$querystatement.="'".$therecord["attachedtabledefid"]."', ";
			$querystatement.="'".$therecord["attachedid"]."', ";
			$querystatement.="'".$therecord["assignedtoid"]."', ";
			if($therecord["assignedtodate"])
				$querystatement.="'".$therecord["assignedtodate"]."', ";
			else
				$querystatement.="NULL, ";
			if($therecord["assignedtotime"])
				$querystatement.="'".$therecord["assignedtotime"]."', ";
			else
				$querystatement.="NULL, ";
			$querystatement.="'".$therecord["assignedbyid"]."') ";

			$queryresult=$this->db->query($querystatement);

			return true;
		}//end method


		/**
                 * retrieves table name for a record with attached information
                 *
                 * @param string $uuid tabledef's uuid
                 *
                 * return array the tabledef's displayname and editfile
                 */
                function getAttachedTableDefInfo($uuid){

                    if($uuid){

                        $querystatement = "
                            SELECT
                                displayname,
                                editfile
                            FROM
                                tabledefs
                            WHERE
                                uuid = '".mysql_real_escape_string($uuid)."'";

                        $queryresult = $this->db->query($querystatement);

                        $therecord=$this->db->fetchArray($queryresult);

                    } else {

                        $therecord["displayname"]="";
                        $therecord["editfile"]="";

                    }//endif

                    return $therecord;

		}//endif


		//CLASS OVERRIDES =============================================================================

		function getDefaults(){
			$therecord = parent::getDefaults();

			if(isset($_GET["ty"]))
				$therecord["type"]=$_GET["ty"];
			else
				$therecord["type"]="NT";

			$therecord["typeCheck"] = $therecord["type"];

			$therecord["private"]=true;

			$therecord["attachedtabledefid"]=(isset($_GET["tabledefid"]))?$_GET["tabledefid"]:NULL;
			$therecord["attachedid"]=(isset($_GET["refid"]))?$_GET["refid"]:NULL;

			//from quickview
			if(isset($_GET["cid"])){
				$therecord["attachedtabledefid"] = "tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083";
				$therecord["attachedid"]=$_GET["cid"];
			}

			$therecord["repeatevery"] = 1;
			$therecord["repeattype"] = "Daily";

			return $therecord;
		}


		function getRecord($id, $useUuid = false){

			$therecord = parent::getRecord($id, $useUuid);

			$therecord["typeCheck"] = $therecord["type"];

			return $therecord;

		}//end method


		function verifyVariables($variables){

			//table default ok
			if(isset($variables["type"])){
				switch($variables["type"]){

					case "NT":
					case "TS":
					case "EV":
						break;

					case "SM":
						//for system message, not private
						if(isset($variables["private"]))
							if($variables["private"])
								$this->verifyErrors[] = "For records with `type` of 'SM' (System Message), the `private
												field must be equivalent to 0.";
						break;

					default:
						$this->verifyErrors[] = "The `type` field given is not an acceptable value. Acceptable values are 'NT', 'TS', 'EV', or 'SM'";
						break;

				}//end switch
			}//end if

			//Doesn't need to be set... only used if repeating

			if(isset($variables["repeat"]))
				if($variables["repeat"])
					if(isset($variables["startdate"])){
						if($variables["startdate"]){

						}else
							$this->verifyErrors[] = "If a `note` record is set to repeat it must also have a `startdate`.";
					}else
						$this->verifyErrors[] = "If a `note` record is set to repeat it must also have a `startdate`.";

			if(isset($variables["repeattype"]))
				if($variables["repeattype"])//it can be "" or NULL
					switch($variables["repeattype"]){


						case "Daily":
						case "Weekly":
						case "Monthly":
						case "Yearly":
							break;

						default:
							$this->verifyErrors[] = "The `repeatetype` field given is not an acceptable value.
											Acceptable values are 'Daily', 'Weekly', 'Monthly', or 'Yearly'";
							break;

					}//end switch


			//check booleans
			if(isset($variables["completed"]))
				if($variables["completed"] && $variables["completed"] != 1)
					$this->verifyErrors[] = "The `completed` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["private"]))
				if($variables["private"] && $variables["private"] != 1)
					$this->verifyErrors[] = "The `private` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["repeating"]))
				if($variables["repeating"] && $variables["repeating"] != 1)
					$this->verifyErrors[] = "The `repeating` field must be a boolean (equivalent to 0 or exactly 1).";

			return parent::verifyVariables($variables);

		}//end method --verifyVariables--


		function prepareVariables($variables,$userid = NULL){

			if($userid == NULL)
				if(isset($_SESSION["userinfo"]["id"]))
					$userid = $_SESSION["userinfo"]["id"];

				if($variables["id"]){ //i.e. only on update

					unset($this->fields["type"]);

					if($variables["typeCheck"]=="TS" && isset($variables["repeating"]) && $variables["lastrepeat"]){
						$variables["lastrepeat"] = NULL;
						$variables["firstrepeat"] = NULL;
						$variables["timesrepeated"] = NULL;

						if(isset($variables["completed"]))
							$variables["completedChange"] = 0;

						$this->resetRepeating($variables["id"]);
					}//end if

				}//end if

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

				if(isset($variables["repeating"])) {

					$thename="Every ";

					switch($variables["repeattype"]){
						case "Daily":
							if($variables["repeatevery"] != 1)
								$thename .= $variables["repeatevery"]." days";
							else
								$thename .= " day ";

							$variables["repeatechlist"] = NULL;
							$variables["repeatontheday"] = NULL;
							$variables["repeatontheweek"] = NULL;
						break;

						case "Weekly":
							if($variables["repeatevery"] != 1)
								$thename .= $variables["repeatevery"]." weeks on";
							else
								$thename .= "week on";

							foreach(explode("::",$variables["eachlist"]) as $dayNum){
								$tempday = ($dayNum != 7)?($dayNum+1):(1);
								$thename .=" ".nl_langinfo(constant("ABDAY_".$tempday)).", ";
							}
							$thename = substr($thename,0,strlen($thename)-2);

							if(strpos($thename,",") != false)
								$thename = strrev(preg_replace("/,/","dna ",strrev($thename),1));

							$variables["repeateachlist"] = $variables["eachlist"];
							$variables["repeatontheday"] = NULL;
							$variables["repeatontheweek"] = NULL;
						break;

						case "Monthly":
							if($variables["repeatevery"] != 1)
								$thename .= $variables["repeatevery"]." months";
							else
								$thename .= "month";

							$thename .= " on the";
							if($variables["monthlyWhat"] == 1){

								foreach(explode("::",$variables["eachlist"]) as $dayNum)
									$thename .=" ".ordinal($dayNum).", ";

								$thename = substr($thename,0,strlen($thename)-2);

								if(strpos($thename,",") != false)
									$thename = strrev(preg_replace("/,/","dna ",strrev($thename),1));

								$variables["repeateachlist"] = $variables["eachlist"];
								$variables["repeatontheday"] = NULL;
								$variables["repeatontheweek"] = NULL;
							} else {
								foreach($this->weekArray as $key=>$value)
									if($value == $variables["monthlyontheweek"])
										$thename .= " ".strtolower($key);

								foreach($this->dayOfWeekArray as $key=>$value)
									if($value == $variables["monthlyontheday"])
										$thename .= " ".$key;

								$variables["repeateachlist"] = NULL;
								$variables["repeatontheday"] = $variables["monthlyontheday"];
								$variables["repeatontheweek"] = $variables["monthlyontheweek"];
							}
						break;

						case "Yearly":
							if($variables["repeatevery"] > 1)
								$thename .= $variables["repeatevery"]." years";
							else
								$thename .= "year";

							$thename .= " in";

							foreach(explode("::",$variables["eachlist"]) as $monthNum)
								$thename .=" ".nl_langinfo(constant("MON_".$monthNum)).", ";

							$thename = substr($thename,0,strlen($thename)-2);
							if(strpos($thename,",") != false)
								$thename = strrev(preg_replace("/,/","dna ",strrev($thename),1));

							$variables["repeateachlist"] = $variables["eachlist"];

							if(isset($variables["yearlyOnThe"])){
								$thename .= " on the";
								foreach($this->weekArray as $key=>$value)
									if($value == $variables["yearlyontheweek"])
										$thename .= " ".strtolower($key);

								foreach($this->dayOfWeekArray as $key=>$value)
									if($value == $variables["yearlyontheday"])
										$thename .= " ".$key;

								$variables["repeatontheday"] = $variables["yearlyontheday"];
								$variables["repeatontheweek"] = $variables["yearlyontheweek"];

							} else {

								$variables["repeatontheday"] = NULL;
								$variables["repeatontheweek"] = NULL;

							}//end if
						break;
					}

					switch($variables["repeatend"]){
						case "never":
							$variables["repeatuntil"] = NULL;
							$variables["repeattimes"] = NULL;
							break;

						case "after":
							$thename .= " for ".$variables["repeattimes"];

							$variables["repeatuntil"] = NULL;
							break;

						case "on date":
							$thename .= " until ".$variables["repeatuntil"];
							$variables["repeattimes"] = NULL;
							break;
					}
					$thename = trim($thename).".";
					$variables["repeatname"] = $thename;

					$variables["firstrepeat"] = dateToString(stringToDate($variables["firstrepeat"],"SQL"));
					$variables["lastrepeat"] = dateToString(stringToDate($variables["lastrepeat"],"SQL"));;
					$variables["timesrepeated"] = NULL;

				}else {

					$variables["repeat"] = 0;
					$variables["repeatechlist"] = NULL;
					$variables["repeatontheday"] = NULL;
					$variables["repeatontheweek"] = NULL;
					$variables["repeatname"] = NULL;
					$variables["repeatuntil"] = NULL;
					$variables["repeattimes"] = NULL;

					$variables["firstrepeat"] = NULL;
					$variables["lastrepeat"] = NULL;
					$variables["timesrepeated"] = NULL;

				}//endif repeat

				if($variables["assignedtoid"] != $variables["assignedtochange"]){
					if($variables["assignedtoid"] != "")
						$variables["assignedbyid"] = getUuid($this->db, "tbld:afe6d297-b484-4f0b-57d4-1c39412e9dfb", $userid);
					else
						$variables["assignedbyid"] = '';
				}//endif

			return $variables;

		}//end method


		function updateRecord($variables, $modifiedby = NULL, $useUuid = false){

			$thereturn = parent::updateRecord($variables, $modifiedby, $useUuid);

			$this->getTableInfo();

			if($variables["typeCheck"]=="TS" && isset($variables["completed"]) && $variables["completedChange"]==0) {
					if($variables["parentid"]){
						if(!$this->newerRepeats($variables["parentid"],$variables["id"])){
							$this->repeatTask($variables["parentid"]);
						}

					} elseif(isset($variables["repeating"])) {
						if(!$this->newerRepeats($variables["id"],$variables["id"])){
							$this->repeatTask($variables["id"]);
						}

					}//endif elseif
			}//endif

			return $thereturn;

		}//end endmethod


		function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false, $useUuid = false){

			$newid = parent::insertRecord($variables, $createdby, $overrideID, $replace, $useUuid);

			if(isset($variables["completed"]) && isset($variables["repeating"]))
				$this->repeatTask($newid);

			return $newid;
		}//end method

	}//end class
}//end if

if(class_exists("searchFunctions")){
	class notesSearchFunctions extends searchFunctions{

		function mark_asread($useUUID = false){

			if(!$useUUID)
				$whereclause = $this->buildWhereClause();
			else
				$whereclause = $this->buildWhereClause("`notes`.`uuid`");

			$querystatement = "
				UPDATE
					`notes`
				SET
					`notes`.`completed` = '1',
					`modifiedby`='".$_SESSION["userinfo"]["id"]."'
				WHERE
					(".$whereclause.")
					AND
					`type`! = 'SM'
			";
			$queryresult = $this->db->query($querystatement);

			$message = $this->buildStatusMessage();

			$message .=" marked as completed/read.";

			//for repeatable tasks, need to repeat dem!
			$querystatement = "
				SELECT
					`id`,
					`parentid`
				FROM
					`notes`
				WHERE
					`type`='TS'
					AND
					(
						(
							`parentid` IS NOT NULL
							AND
							`parentid`!='0'
						)
						OR
						`repeating`='1'
					)
					AND
					(".$whereclause.")
			";

			$queryresult = $this->db->query($querystatement);
			if($this->db->numRows($queryresult)){

				$thetable = new notes($this->db,12);

				while($therecord = $db->fetchArray($queryresult)){
					if($variables["parentid"])
						if(!$thetable->newerRepeats($therecord["parentid"],$therecord["id"]))
							$thetable->repeatTask($therecord["parentid"]);
					elseif(isset($therecord["repeating"]))
						if(!$thetable->newerRepeats($therecord["id"],$therecord["id"]))
							$thetable->repeatTask($therecord["id"]);
				}//endwhile

			}//endif

			return $message;
		}


		//delete notes
		function delete_record($useUUID = false){

			if(!$useUUID)
				$whereclause=$this->buildWhereClause("notes.id");
			else
				$whereclause = $this->buildWhereClause($this->maintable.".uuid");

			//we need to check for incomplete repeatable child tasks
			$querystatement = "
				SELECT
					`notes`.`id`,
					`notes`.`parentid`,
					`notes`.`repeating`,
					`notes`.`completed`
				FROM
					`notes`
				WHERE
					(".$whereclause.")
					AND
					(
						(
							`notes`.`createdby = '".$_SESSION["userinfo"]["id"]."'
							OR
							`notes`.`assignedtoid` = '".$_SESSION["userinfo"]["uuid"]."'
						)
						OR
						(
							'".$_SESSION["userinfo"]["admin"]."' = '1'
						)
					)
			";

			$repeatqueryresult = $this->db->query($querystatement);

			//repeat where applicable
			if ($this->db->numRows($repeatqueryresult)){

				$thetable = new notes($this->db,12);

				$repeatArray=array();
				$orphanArray= array();
				while($therecord=$this->db->fetchArray($repeatqueryresult)){

					if($therecord["parentid"] && $therecord["completed"] == 0){
						$repeatArray[] = array("parentid" => $therecord["parentid"], "id"=> $therecord["id"]);
					} elseif($therecord["repeating"]){
						$orphanArray[] = array("id" => $therecord["id"]);
					}//endif elseif

				}//endwhile

				foreach($repeatArray as $repeat){
					if (!in_array($repeat["parentid"],$orphanArray))
						if(!$thetable->newerRepeats($repeat["parentid"],$repeat["id"]))
							$thetable->repeatTask($repeat["parentid"]);
				}//end foreach

				foreach($orphanArray as $orphaner){
					$thetable->resetRepeating($orphaner);
				}

			}//end if

			$querystatement = "
				DELETE FROM
					`notes`
				WHERE
					(
						(
							`notes`.`createdby` = '".$_SESSION["userinfo"]["id"]."'
							OR
							`notes`.`assignedtoid` = '".$_SESSION["userinfo"]["uuid"]."'
						)
						OR
						(
							'".$_SESSION["userinfo"]["admin"]."' ='1'
						)
					)
					AND
					(".$whereclause.")
			";

			$queryresult = $this->db->query($querystatement);

			$message = $this->buildStatusMessage();
			$message.=" deleted";

			return $message;

		}

	}//end class
}//end if
?>
