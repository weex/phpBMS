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
<script language="JavaScript" >
 var chevronup=new Image();
 chevronup.src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-up.png";
 var chevrondown=new Image();
 chevrondown.src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-down.png";
</script>
</head>
<body onLoad="init()">
<?php include("../../menu.php");?>
<div class="bodyline">
	<h1><?php echo $_SESSION["userinfo"]["firstname"]; if($_SESSION["userinfo"]["lastname"]) echo " ".$_SESSION["userinfo"]["lastname"]?>'s Snapshot</h1>
	<?php showSystemMessages() ?>	
	<table border="0" cellpadding="0" cellspacing="4" width="100%">
		<tr>
			<td width="55%" valign="top" class="box">
				<div class="tiny" style="float:right;margin-top:2px;">
					<?php echo strftime("%A, %b. %e, %Y")?>
				</div>
				<h2 style="margin-top:4px;">
					<a href="../../search.php?id=24">This week's Events</a>
				</h2>
				<div id="theEvents" style="overflow:auto;">
				<?php showSevenDays($_SESSION["userinfo"]["id"])?>
				</div>
			</td><td nowrap>&nbsp;</td>
			<td width="45%" valign="top" class="box" id="accordianContainer">
					<div id="accordian" style="overflow:hidden;margin:0px;padding:0px;">
						<div style="clear:both;float:right;cursor:pointer;cursor:hand;padding-bottom:0px;margin-bottom:0px;"><img id="accordianImg1" src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-moveupdn.png" align="absmiddle" alt="hide" onClick="accordian(this,'accordian',3)" width="16" height="16" border="0" /></div>
						<h2 style="margin-top:4px;">Received Assignments</h2>
						<div id="accordianSec1" style="margin:0px;padding:0px;">
							<?php showTasks($_SESSION["userinfo"]["id"],"ReceivedAssignments")?>
						</div>
						<div style="clear:both;float:right;cursor:pointer;cursor:hand;padding-bottom:0px;margin-bottom:0px;"><img id="accordianImg2" src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-moveupdn.png" align="absmiddle" alt="hide" onClick="accordian(this,'accordian',3)" width="16" height="16" border="0" /></div>
						<h2 style="margin-top:4px;">Given Assignments</h2>
						<div id="accordianSec2" style="margin:0px;padding:0px;">
							<?php showTasks($_SESSION["userinfo"]["id"],"GivenAssignments")?>
						</div>
						<div style="clear:both;float:right;cursor:pointer;cursor:hand;display:none;padding-bottom:0px;margin-bottom:0px;"><img id="accordianImg3" src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-moveupdn.png" align="absmiddle" alt="hide" onClick="accordian(this,'accordian',3)" width="16" height="16" border="0" /></div>
						<h2 style="margin-top:4px;"><a href="../../search.php?id=23">Tasks</a></h2>
						<div id="accordianSec3" style="display:block;margin:0px;padding:0px;">
							<?php showTasks($_SESSION["userinfo"]["id"],"Tasks")?>
						</div>
					</div>
				</td>
		</tr>
	</table>
	<?php if (checkForBMS() && $_SESSION["userinfo"]["accesslevel"]>=20) {?>
	<div class="box" style="display:inline-block;">	
		<div style="float:right;cursor:pointer;cursor:hand;"><img src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-up.png" align="absmiddle" alt="hide" onClick="hideSection(this,'TodaysOrders')" width="16" height="16" border="0" /></div>
		<h2 style="margin-top:4px;"><a href="../../search.php?id=3">Recent Orders</a></h2>
			<div id="TodaysOrders">
				<?php 
				if(date("D")=="Mon")
					$interval="3 DAY";
				else
					$interval="1 DAY";
				showTodaysOrders($interval) 
				?>
			</div>
			
		<div style="float:right;cursor:pointer;cursor:hand;"><img src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-up.png" align="absmiddle" alt="hide" onClick="hideSection(this,'TodaysClients')" width="16" height="16" border="0" /></div>
		<h2 style="margin-top:4px;"><a href="../../search.php?id=2">Recently Added Clients/Propects</a></h2>
			<div id="TodaysClients"><?php showTodaysClients($interval)?></div>
	</div>
	<?php }?>				
</div>
<?php include("../../footer.php")?>
</body>
</html>
