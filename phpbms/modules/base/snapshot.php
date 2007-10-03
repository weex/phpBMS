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
	require_once("../../include/session.php");	
	require_once("include/snapshot_include.php");
	
	//Page details;
	$pageTitle = APPLICATION_NAME;
	
	$phpbms->cssIncludes[] = "pages/snapshot.css";
	
	$phpbms->jsIncludes[] = "modules/base/javascript/snapshot.js";
	
	foreach($phpbms->modules as $modulename => $modinfo)
		if(file_exists("../".$modulename."/javascript/snapshot.js") && $modulename != "base")
			$phpbms->jsIncludes[] = "modules/".$modulename."/javascript/snapshot.js";
	
	require("header.php");
	
?>
<div class="bodyline">
	<h1><?php echo $_SESSION["userinfo"]["firstname"]; if($_SESSION["userinfo"]["lastname"]) echo " ".$_SESSION["userinfo"]["lastname"]?>'s Snapshot</h1>
	<?php showSystemMessages($db) ?>
	
	<div id="notesStuff">
	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td id="eventsBox" class="box" width="60%">&nbsp;</td>
			<td id="spacertd">&nbsp;</td>
			<td class="box" id="tasksBox">
				<h2>Workload</h2>
				<?php 
					showTasks($db,$_SESSION["userinfo"]["id"],"GivenAssignments");
					showTasks($db,$_SESSION["userinfo"]["id"],"ReceivedAssignments"); 
					showTasks($db,$_SESSION["userinfo"]["id"],"Tasks");
				?>
			</td>
		</tr>
	</table>
	</div>
	
	<div style="clear:both;"></div>

	<?php 	
	foreach($phpbms->modules as $modulename => $modinfo)				
		if(file_exists("../".$modulename."/snapshot.php") && $modulename != "base")
			include("../".$modulename."/snapshot.php");
	?>
</div>

<?php include("footer.php")?>