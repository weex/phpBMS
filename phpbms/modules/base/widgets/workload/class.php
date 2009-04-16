<?php
    class wdgta1aec114954b37c104747d4e851c728c extends widget{

        var $uuid ="wdgt:a1aec114-954b-37c1-0474-7d4e851c728c";
        var $type = "little";
        var $title = "Workload";
        var $jsIncludes = array('modules/base/widgets/workload/workload.js');
        var $cssIncludes = array('widgets/base/workload.css');

        var $phpbms;
        var $userid;

        function displayMiddle(){

            global $phpbms;

	    $this->phpbms = $phpbms;
	    $this->userid = $_SESSION["userinfo"]["id"];

            $this->showTasks("GivenAssignments");

            $this->showTasks("ReceivedAssignments");

        }//end function showMiddle


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
					OR (startdate IS NOT NULL AND startdate <= DATE_ADD(CURDATE(),INTERVAL 30 DAY) AND enddate IS NULL AND assignedtodate IS NULL)
					OR (enddate IS NOT NULL AND enddate <= DATE_ADD(CURDATE(),INTERVAL 30 DAY))
					OR (assignedtodate IS NOT NULL AND assignedtodate <= DATE_ADD(CURDATE(),INTERVAL 30 DAY))
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
				$section["title"] = "Today";
				$section["date"] = mktime(0,0,0,date("m"),date("d"),date("Y"));

				while($therecord = $this->db->fetchArray($queryresult)) {

					$className="tasks";

					if($therecord["completed"])
						$className.=" complete";
					else if($therecord["ispastdue"] || $therecord["ispastassigneddate"])
						$className.=" pastDue";

					if($therecord["private"]) $className.=" private";

					$className.=" ".$therecord["type"];

					$checkBoxID = $id.$therecord["type"]."C".$therecord["id"];

					$link = $linkStart."?id=".$therecord["id"]."&amp;backurl=".APP_PATH."modules/base/snapshot.php";

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

								case "Today":
									$section["title"] = "Soon";
									$section["date"] = mktime(0,0,0,date("m"),date("d")+7,date("Y"));
									break;

								case "Soon":
									$section["title"] = "Later";
									$section["date"] = mktime(0,0,0,1,1,2038);
									break;
								case "Later":
									//should never be here
									$section["date"] = $xdate;

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

    }//end class workload
?>
