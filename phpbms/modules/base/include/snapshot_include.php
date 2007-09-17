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
function showSystemMessages($db){
	$querystatement="SELECT notes.id,subject,content,concat(users.firstname,\" \",users.lastname) as createdby,
					notes.creationdate
					FROM notes INNER JOIN users ON notes.createdby=users.id
					WHERE type=\"SM\" ORDER BY importance DESC,notes.creationdate";
					
	$queryresult=$db->query($querystatement);
		
	if($db->numRows($queryresult)){ 
	?>
	<div class="box" id="systemMessageContainer">	
		<h2>System Messages</h2>
		<?php while($therecord=$db->fetchArray($queryresult)) {
				$therecord["content"]=str_replace("\n","<br />",htmlQuotes($therecord["content"]));
		?>
		<h3 class="systemMessageLinks"><?php echo htmlQuotes($therecord["subject"])?> <span>[ <?php echo htmlQuotes(formatFromSQLDateTime($therecord["creationdate"]))?> <?php echo htmlQuotes($therecord["createdby"])?>]</span></h3>			
		<div class="systemMessages">
			<p><?php echo $therecord["content"]?></p>
		</div>						
		<?php }//end while ?>
	</div>
	<?php } 
}


function showTasks($db,$userid,$type="Tasks"){

	global $phpbms;

	$querystatement="SELECT id,type,subject, completed, if(enddate < CURDATE(),1,0) as ispastdue, startdate, enddate, private, assignedbyid, assignedtoid
				FROM notes
				WHERE ";
	switch($type){
		case "Tasks":
			$querystatement.=" type=\"TS\" AND (private=0 or (private=1 and createdby=".$userid.")) 
							   AND (completed=0 or (completed=1 and completeddate=CURDATE()))
							   AND (assignedtoid is null or assignedtoid=0)";
			$title="Tasks";
			$id = "TS";
			break;
		
		case "ReceivedAssignments":
			$querystatement.=" assignedtoid=".$userid." AND (completed=0 or (completed=1 and completeddate=CURDATE()))";
			$title="Assignments";
			$id = "AS";
			break;
		
		case "GivenAssignments":
			$querystatement.=" assignedbyid=".$userid." AND (completed=0 or (completed=1 and completeddate=CURDATE()))";
			$title="Delegations";
			$id = "DG";
		break;
	}
	$querystatement.="AND (
					(startdate is null AND enddate is null) OR
					(startdate is not null AND startdate <= DATE_ADD(CURDATE(),INTERVAL 7 DAY)) OR
					(enddate is not null AND enddate <= DATE_ADD(CURDATE(),INTERVAL 7 DAY)) 
				   )";

	$querystatement.=" ORDER BY importance DESC,notes.enddate,notes.endtime,notes.startdate DESC,notes.starttime DESC,notes.creationdate DESC";


	$queryresult=$db->query($querystatement);
	
	?>
	<h3 class="tasksLinks"><?php echo $title; if($db->numRows($queryresult)) {?> <span class="small">(<?php echo $db->numRows($queryresult)?>)</span><?php } ?></h3>
	<div class="tasksDivs"><div>
	<?php
	
	if($db->numRows($queryresult)){ 	
		while($therecord=$db->fetchArray($queryresult)) {

		$className="tasks";

		if($therecord["completed"]) 
			$className.=" complete";
		else if($therecord["ispastdue"]) 
			$className.=" pastDue";

		if($therecord["private"]) $className.=" private";
	
		$className.=" ".$therecord["type"];
		
		$checkBoxID = $id.$therecord["type"]."C".$therecord["id"];
	?>
	<p id="<?php echo $id.$therecord["id"]?>" class="<?php echo $className?>">
		<input class="radiochecks taskChecks" id="<?php  echo $checkBoxID?>" name="<?php  echo $checkBoxID?>" type="checkbox" value="1" <?php if($therecord["completed"]) echo 'checked="checked"'?>  align="middle" />
		<a href="<?php echo getAddEditFile($db,12)."?id=".$therecord["id"]?>&amp;backurl=snapshot.php"><?php echo htmlQuotes($therecord["subject"])?></a>
		<?php 
			if($type == "Tasks") {
				if($therecord["enddate"]) {
				?><em >(<?php echo htmlQuotes(formatFromSQLDate($therecord["enddate"])) ?>)</em><?php 
				}
			} else {
				?> <em>(<?php if($type=="ReceivedAssignments") $tid=$therecord["assignedbyid"]; else $tid=$therecord["assignedtoid"]; echo htmlQuotes($phpbms->getUserName($tid))?>)</em><?php 
			} ?>
	</p>
	<?php } } else {
	?><p class="small disabledtext">no <?php echo strtolower($title)?></p><?php
	}?></div></div> <?php 
}


?>

