<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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
class baseSnapshot{

	function baseSnapshot($db, $phpbms, $userid){

		$this->db = $db;
		$this->phpbms = $phpbms;
		$this->userid = $userid;

	}//endmethod (init)


	function showSystemMessages(){

		$querystatement = "
			SELECT
				notes.id,
				notes.subject,
				notes.content,
				concat(users.firstname,' ',users.lastname) AS createdby,
				notes.creationdate
			FROM
				notes INNER JOIN users ON notes.createdby=users.id
			WHERE
				type='SM'
			ORDER BY
				importance DESC,
				notes.creationdate";

		$queryresult = $this->db->query($querystatement);

		if($this->db->numRows($queryresult)){ ?>

		<div id="systemMessageContainer">
			<h2>System Messages</h2>
			<?php while($therecord = $this->db->fetchArray($queryresult)) {

					$therecord["content"] = str_replace("\n","<br />",htmlQuotes($therecord["content"]));

			?>
				<div class="systemMessageLinks">
					<h3><?php echo htmlQuotes($therecord["subject"])?></h3>
					<div>by <?php echo htmlQuotes($therecord["createdby"])?> on <?php echo htmlQuotes(formatFromSQLDateTime($therecord["creationdate"]))?></div>
				</div>
				<div class="systemMessages"><p><?php echo $therecord["content"]?></p></div>
			<?php }//end while ?>
		</div>
		<?php }//endif

	}//end method showSystemMessages


	function showTasks($type){

		$querystatement="
			SELECT
				id,
				type,
				subject,
				completed,
				if(enddate < CURDATE(),1,0) AS ispastdue,
				if(assignedtodate < CURDATE(),1,0) AS ispastassigneddate,
				startdate,
				enddate,
				assignedtodate,
				private,
				assignedbyid,
				assignedtoid,
				IF(assignedtodate IS NOT NULL, assignedtodate, IF((enddate IS NOT NULL && type = 'TS'), enddate, IF((startdate IS NOT NULL && type = 'EV'), startdate, CURDATE()))) AS xdate
			FROM
				notes
			WHERE";

		switch($type){

			case "ReceivedAssignments":

				$querystatement.="
					((
						assignedtoid = ".$this->userid."
						OR 	(
							type = 'TS'
							AND (assignedtoid = 0 OR assignedtoid IS NULL)
							AND createdby = ".$this->userid."
							)
					)
						AND 	(
							completed = 0
							OR 	(
								completed = 1
								AND completeddate >= CURDATE()
								)
							)
					)";

				$title = "Assignments";
				$id = "AS";
				break;

			case "GivenAssignments":

				$querystatement.="
					(assignedbyid = ".$this->userid."
					AND (completed = 0
						OR (completed = 1 AND completeddate >= CURDATE())
					))";

				$title = "Delegations";
				$id = "DG";
				break;

		}//endswitch


		$querystatement.="AND (
					(startdate IS NULL AND enddate IS NULL AND assignedtodate IS NULL)

					OR (
						(startdate IS NOT NULL AND startdate <= DATE_ADD(CURDATE(),INTERVAL 30 DAY))
						AND (
							(enddate IS NOT NULL AND enddate <= DATE_ADD(CURDATE(),INTERVAL 30 DAY))
							OR (assignedtodate IS NOT NULL AND assignedtodate <= DATE_ADD(CURDATE(),INTERVAL 30 DAY))
						)
					)
				   )";

		$querystatement.=" ORDER BY
				importance DESC,
				xdate,
				subject";


		$queryresult = $this->db->query($querystatement);

		$numRows = $this->db->numRows($queryresult);
		?>

		<h3 class="tasksLinks"><?php echo $title; if($numRows) {?> <span class="small">(<?php echo $numRows?>)</span><?php } ?></h3>

		<div class="tasksDivs">
			<div>

			<?php if($numRows){

				$linkStart = getAddEditFile($this->db,12);
				$section["title"] = "Now";
				$section["date"] = mktime(0,0,0,date("m"),date("d")+3,date("Y"));;

				while($therecord = $this->db->fetchArray($queryresult)) {

					$className="tasks";

					if($therecord["completed"])
						$className.=" complete";
					else if($therecord["ispastdue"] || $therecord["ispastassigneddate"])
						$className.=" pastDue";

					if($therecord["private"]) $className.=" private";

					$className.=" ".$therecord["type"];

					$checkBoxID = $id.$therecord["type"]."C".$therecord["id"];

					$link = $linkStart."?id=".$therecord["id"]."&amp;backurl=snapshot.php";

					$rightSide = "";

					if($therecord["assignedtodate"])
						$rightSide .= "FUP: ".formatFromSQLDate($therecord["assignedtodate"])."<br />";

					switch($therecord["type"] ){

						case "TS":
							if($therecord["enddate"])
								$rightSide .= "Due: ".formatFromSQLDate($therecord["enddate"])."<br />";
							break;

						case "EV":
							$rightSide .= "Start: ".formatFromSQLDate($therecord["startdate"])."<br />";
							$rightSide .= "End: ".formatFromSQLDate($therecord["enddate"])."<br />";
							break;

					}//endswitch

					if(!$rightSide)
						$rightSide = "&nbsp;";

					$bottomInfo = "";

					switch($type){

						case "ReceivedAssignments":
							if($therecord["assignedbyid"])
								$bottomInfo = "Assigned By: ".htmlQuotes($this->phpbms->getUserName($therecord["assignedbyid"]));
							break;

						case "GivenAssignments":
							$bottomInfo = "Assigned To: ".htmlQuotes($this->phpbms->getUserName($therecord["assignedtoid"]));
							break;

					}//endswitch

					// Looking for grouping changes in headers (3 days, 4-7 days, > 7 days)
					$xdate = stringToDate($therecord["xdate"],"SQL") ;
					if($xdate > $section["date"]){

						while($xdate > $section["date"]){

							switch($section["title"]){

								case "Now":
									$section["title"] = "Soon";
									$section["date"] = mktime(0,0,0,date("m"),date("d")+7,date("Y"));
									break;

								case "Soon":
									$section["title"] = "Later";
//									$section["date"] = mktime(0,0,0,date("m"),date("d")+31,date("Y"));
									$section["date"] = mktime(0,0,0,date("m"),date("d"),2038);
									break;

							}//end switch

						}//endwhile

						?><div class="taskSection"><?php echo $section["title"] ?></div><?php

					}//end if

					?>

					<div id="<?php echo $id.$therecord["id"]?>" class="<?php echo $className?>">

						<span class="taskRight"><?php echo $rightSide ?></span>

						<input class="radiochecks taskChecks" id="<?php  echo $checkBoxID?>" name="<?php  echo $checkBoxID?>" type="checkbox" value="1" <?php if($therecord["completed"]) echo 'checked="checked"'?>  align="middle" />

						<a href="<?php echo $link?>"><?php echo htmlQuotes($therecord["subject"])?></a>

						<?php if($bottomInfo){ ?>

							<p><?php echo $bottomInfo ?></p>

						<? }//endif ?>
					</div>

				<?php }//endwhile
				} else { ?>
					<p class="small disabledtext">no <?php echo strtolower($title)?></p><?php
				}?>
			</div>
		</div> <?php

	}//end method showTasks


}// end class baseSnapshot

?>
