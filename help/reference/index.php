<?php 
	$loginNoKick=true;
	$loginNoDisplayError=true;
	require("../../include/session.php");	
	$pageTitle="Using phpBMS";
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css" />
<style>li{margin-bottom:10px;}</style>
<script language="JavaScript" src="<?php echo $_SESSION["app_path"]?>common/javascript/common.js" type="text/javascript" ></script>
<script language="javascript">
	function navTo(){
		var theselect=getObjectFromID("navselect");
		document.location=theselect.value;
	}
</script>
</head>

<body> 
<div class="bodyline" style="width:700px;">
	<h1><?php echo $pageTitle?></h1>
	<div style="float:right">
		<select id="navselect">
			<option value="">choose...</option>
			<option value="<?php echo $_SESSION["app_path"]?>help/reference">Using phpBMS</option>
			<option value="http://www.kreotek.com/products/phpbms/tutorials">Tutorials</option>
			<option value="<?php echo $_SESSION["app_path"]?>help/customize.php">Customizing phpBMS</option>
			<option value="<?php echo $_SESSION["app_path"]?>help/shortcuts.php">Keyboard Shortcuts</option>
			<option value="<?php echo $_SESSION["app_path"]?>info.php">About phpBMS</option>
		</select>
		<input type="button" class="Buttons" value="go" onClick="navTo()"/>
	</div>
	
	<ul>
		<li><strong>phpBMS User Manual</strong> <em>(at kreotek.com)</em>
			<div class=small>coming soon</div>
		</li>
		<li><strong>phpBMS Administrators Manual</strong> <em>(at kreotek.com)</em>
			<div class=small>coming soon</div>
		</li>
		<li><strong>Installation / Configuration Manual</strong> <em>(at kreotek.com)</em>
			<div class=small>coming soon</div>
		</li>
		<li>
			<a href="tips"><strong>Tips and Techniques</strong></a>
			<div class=small>Tip on how to get the most out of your phpBMS system.</div>
		</li>
	</ul>

</div>
</body>
</html>