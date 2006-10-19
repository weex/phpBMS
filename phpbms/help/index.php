<?php 
	$loginNoKick=true;
	$loginNoDisplayError=true;
	require("../include/session.php");	
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>phpBMS Resources</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php require("head.php")?>
</head>

<body>
<div class="bodyline" style="width:700px;">
	<h1>phpBMS Resources</h1>
	<ul>
		<li><a href="http://phpbms.org/wiki/PhpbmsGuide"><strong>PhpbmsGuide</strong></a> (at phpbms.org)
			<div class=small>User and Administrator Guide.</div>
		</li>
		<li>
			<a href="http://www.kreotek.com/products/phpbms/tutorials"><strong>Tutorials</strong></a> <em>(at kreotek.com)</em>
			<div class=small>Step by step examples on how to perform common tasks inside phpBMS.</div>
		</li>
		<li>
			<strong><a href="http://phpbms.org/wiki/PhpbmsFaq">PhpbmsFaq</a></strong> <em>(at phpbms.org)</em>
			<div class=small>Frequently Asked Questions </div>
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