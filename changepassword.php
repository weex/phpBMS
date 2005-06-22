<?PHP
	include("include/session.php");
	
	$statement="&nbsp;";
	if(isset($_POST["command"])){
		//they want to change their password
		$checkstatement="select id from users where id=".$_SESSION["userinfo"]["id"]." and password=ENCODE(\"".$_POST["currpass"]."\",\"".$_SESSION["encryption_seed"]."\")";
		$checkquery=mysql_query($checkstatement,$dblink);
		if(mysql_num_rows($checkquery)){
			if($_POST["newpass1"]==$_POST["newpass2"]){
				$checkstatement="update users set password=ENCODE(\"".$_POST["newpass1"]."\",\"".$_SESSION["encryption_seed"]."\") where id=".$_SESSION["userinfo"]["id"];
				$checkquery=mysql_query($checkstatement,$dblink);
								
				echo "<SCRIPT language=\"JavaScript\">window.close()</SCRIPT>";
				die();
			} else $statement="New and retyped passwords did not match.";
		} else $statement="Current password not entered correctly.";
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" >
<html>
<head>
<title>Change Password</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
</head>
<body>
<form action="<?PHP echo $_SERVER["PHP_SELF"] ?>" method="post" name="thechoice">
<div class="bodyline">
	<h1 style="margin:0px;">Change Password</h1>
	<div <?PHP if($statement!="&nbsp;") echo "class=\"standout\""?>><?PHP echo $statement?></div>	
	<div>
		current password<br>
		<input name="currpass" type="password" id="currpass" style="width:100%">
	</div>
	
	<div>
		new password<br>
		<input name="newpass1" type="password" id="newpass1" style="width:100%">
	</div>
	
	<div>
		re-type new password<br>
		<input name="newpass2" type="password" id="newpass2" style="width:100%">
	</div>
	<div align="right">
		<input name="command" type="submit" class="Buttons" value="change" style="width:75px">
		<input name="Button" type="button" class="Buttons" value="cancel" onClick="window.close()" style="width:75px;">
	</div>
</div>
</form>
</body>
</html>