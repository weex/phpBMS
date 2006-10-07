<?php 
	$loginNoKick=true;
	$loginNoDisplayError=true;
	require("../include/session.php");	
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>phpBMS Resources</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css" />
	<style>li{margin-bottom:10px;}</style>
</head>

<body>
<div class="bodyline" style="width:700px;">
	<h1>phpBMS Resources</h1>
	<ul>
		<li><a href="reference"><strong>Using phpBMS</strong></a>
			<div class=small>User and Administrator How-tos, tips, and reference.</div>
		</li>
		<li>
			<a href="http://www.kreotek.com/products/phpbms/tutorials"><strong>Tutorials</strong></a> <em>(at kreotek.com)</em>
			<div class=small>Step by step examples on how to perform common tasks inside phpBMS.</div>
		</li>
		<li>
			<a href="customize.php"><strong>Customizing phpBMS</strong></a>
			<div class=small>
				Maganger/Administation information and contacts for obtaining customization
				services as well as develeloper and programmer resources for modifing and adding to phpBMS.
			</div>
		</li>
		<li>
			<strong>Frequently Asked Questions (FAQs)</strong>
			<div class=small>coming soon</div>
		</li>
		<li>
			<a href="shortcuts.php"><strong>Keyboard Shortcuts</strong></a>
			<div class=small>List of keyboard shortcuts for use in common areas in phpBMS.</div>
		</li>
		<li>
			<a href="<?php echo $_SESSION["app_path"]?>info.php"><strong>About phpBMS</strong></a>
			<div class=small>Basic Program &amp; technology information.</div>
		</li>
	</ul>
</div>
</body>
</html>