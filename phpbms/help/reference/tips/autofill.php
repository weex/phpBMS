<?php 
	$loginNoKick=true;
	$loginNoDisplayError=true;
	require("../../../include/session.php");	
	$pageTitle="Using the Autofill Features";
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
	<h2>What is it and how does it work?</h2>
	<div>
		The autofill feature on some fields in phpBMS is a very power because it allows you to dynamically look up record 
		information as you type.  Autofill fields are denoted by the maginfying glass located in the right side of the field (<em>Fig 1.</em>).
	</div>
	<div align="center" class="small important">
		<img src="images/autofill1.png" width="269" height="56" border="1"><br />
		Figure 1. The autofill field.
	</div>
	<div>
		To begin using the feature, simply click inside the field and start typing.  As you type, phpBMS will display a drop down of records that 
		match what you have typed. It will also fill in the first record that matches your typing as you type. To complete the entry, simply tab out
		of the field or select one of the other entries from the displyed drop down by clicking on it or using your up/down arrow keys (<em>Fig. 2</em>)
	</div>
	<div align="center" class="small important">
		<img src="images/autofill2.png" width="269" height="117" border="1"><br />
		Figure 2. Choosing a record entry from the drop down.
	</div>
	<h2>What if I want to search for records by the more than first letters?</h2>
	<div>
		Let's say using our example in figures 1 and 2, we wanted to search by the first name, or the last part of their last name.  How could we do that?
		Simply used the SQL wildcard character (<strong>%</strong>) when beginning to type. For example, if we wanted to search for our client whose first name
		was "Louis", we would simply start type "%" followed by his name "Louis" and phpBMS will find allrecords with the string "Louis" anwher in the first, 
		last, or company names (<em>Fig. 3.</em>).
	</div>
	<div align="center" class="small important">
		<img src="images/autofill3.png" width="269" height="74" border="1"><br />
		Figure 3. Using the wildcard character.
	</div>
	<div>
		Next, select the appropriate record form the dorpdown by clicking on it or using your up/down arrow keys.
	</div>
</div>
</body>
</html>