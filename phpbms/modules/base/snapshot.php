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

	require_once("../../include/session.php");
	require_once("../../include/common_functions.php");	
	require_once("./include/snapshot_include.php");

	$querystatement="SELECT name FROM modules WHERE name!=\"base\" ORDER BY name";
	$modulequery=mysql_query($querystatement,$dblink);
	
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $_SESSION["application_name"] ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php require("../../head.php")?>
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/snapshot.css" rel="stylesheet" type="text/css">

<script language="JavaScript" src="./javascript/snapshot.js" type="text/javascript" ></script>
<script language="JavaScript" >
 var chevronup=new Image();
 chevronup.src="/common/stylesheet/kreotek/button-up.png";
 var chevrondown=new Image();
 chevrondown.src="/common/stylesheet/kreotek/button-down.png";
</script>

<?php 
	while($modulerecord=mysql_fetch_array($modulequery)){
		?><script language="JavaScript" src="<?php echo "../".$modulerecord["name"]."/javascript/snapshot.js";?>" type="text/javascript" ></script><?php
	}//end while 
?>
</head>
<body>
<?php include("../../menu.php");?>
<div class="bodyline">
	<h1><?php echo $_SESSION["userinfo"]["firstname"]; if($_SESSION["userinfo"]["lastname"]) echo " ".$_SESSION["userinfo"]["lastname"]?>'s Snapshot</h1>

	<?php showSystemMessages() ?>
	
	<table cellpadding="0" cellspacing="4" border="0" width="100%">
		<tr>
			<td id="eventsBox" class="box" width="55%">
				<p id="todaysDate" class="tiny"><?php echo strftime("%A, %b. %e, %Y")?></p>
				<h2><a href="../../search.php?id=24">This week's Events</a></h2>
				<?php showSevenDays($_SESSION["userinfo"]["id"])?>			
			</td>
			<td class="tiny">&nbsp;</td>
			<td class="box" id="tasksBox" width="45%">
				<h2>Assignments/Tasks</h2>
				<?php 
					showTasks($_SESSION["userinfo"]["id"],"ReceivedAssignments"); 
					showTasks($_SESSION["userinfo"]["id"],"GivenAssignments");
					showTasks($_SESSION["userinfo"]["id"],"Tasks");
				?>
			</td>
		</tr>
	</table>

	<div style="clear:both;"></div>

	<?php 	
	mysql_data_seek($modulequery,0);	
	while($modulerecord=mysql_fetch_array($modulequery)){
		@ include "../".$modulerecord["name"]."/snapshot.php";
	}//end while 
	?>
	</div>
<?php include("../../footer.php")?>
</body>
</html>
