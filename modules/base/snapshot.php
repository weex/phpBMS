<?php
	require_once("../../include/session.php");
	require_once("../../include/common_functions.php");	
	require_once("./include/snapshot_include.php");
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $_SESSION["application_name"] ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<!-- These Javscript files and scripts are required for the query_searchdisplay and query_function files to
	 work properly -->
<script language="JavaScript" src="../../common/javascript/common.js" type="text/javascript" ></script>
<script language="JavaScript" src="./javascript/snapshot.js" type="text/javascript" ></script>
</head>
<body>
<?php include("../../menu.php");?>
<div class="bodyline">
	<h1><?php echo $_SESSION["userinfo"]["firstname"]; if($_SESSION["userinfo"]["lastname"]) echo " ".$_SESSION["userinfo"]["lastname"]?>'s Snapshot</h1>
	<?php showSystemMessages() ?>	
	<table border="0" cellpadding="3" cellspacing="0" width="100%">
		<tr>
			<td width="49%" valign="top">
				<div class="box" >	
					<h2 style="margin-top:4px;">
						<div class="tiny" style="float:right"><?php echo strftime("%A, %b. %e, %Y")?></div>
						<a href="http://phpbms/search.php?id=24">This week's Events</a>
					</h2>
					<div>
					<?php showSevenDays($_SESSION["userinfo"]["id"])?>
					</div>
				</div>
			</td>
			<td width="49%" valign="top">
				<div class="box">	
					<h2 style="margin-top:4px;"><a href="http://phpbms/search.php?id=23">Tasks</a></h2>
					<div>
						<?php showTasks($_SESSION["userinfo"]["id"],"Tasks")?>
					</div>
					<h2 style="margin-top:4px;">Received Assignments</h2>
					<div>
						<?php showTasks($_SESSION["userinfo"]["id"],"ReceivedAssignments")?>
					</div>
					<h2 style="margin-top:4px;">Given Assignments</h2>
					<div>
						<?php showTasks($_SESSION["userinfo"]["id"],"GivenAssignments")?>
					</div>
				</div>
				<div class="box">	
					<h2 style="margin-top:4px;"><a href="http://phpbms/search.php?id=3">Today's Orders</a></h2>
						<div><?php showTodaysOrders()?></div>
						
					<h2 style="margin-top:4px;"><a href="http://phpbms/search.php?id=2">Clients/Propects Entered Today</a></h2>
						<div><?php showTodaysClients()?></div>
				</div>
			</td>
		</tr>
	</table>
</div>

</body>
</html>