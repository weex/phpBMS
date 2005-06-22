<?PHP
	include("include/session.php");	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" >
<html>
<head>
<title>View SQL</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="bodyline" style="padding:+4px;">
	<h1>SQL Statement</h1>
	<div class="box"><code><?php echo stripslashes(htmlspecialchars($_SESSION["thequerystatement"])) ?></code></div>
	<?php if($_SESSION["sqlerror"]) {?>
	<h1>SQL Error</h1>
	<div class="box"><?php echo $_SESSION["sqlerror"]?></div>
	<?php } ?>
	<div align="right" class="recordbottom">
		<input name="Button" type="button" class="Buttons" value="done" onClick="window.close()" style="width:75px">
	</div>
</div>
</body>
</html>
