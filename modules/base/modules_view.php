<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/modules_view_include.php");
	
	$pageTitle="Installed Modules";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">

<script language="JavaScript" src="../../common/javascript/fields.js"></script>
</head>
<body><?php include("../../menu.php")?>

<?PHP if (isset($statusmessage)) {?>
	<div class="standout" style="margin-bottom:3px;"><?PHP echo $statusmessage ?></div>
<?PHP } // end if ?>
<div class="bodyline">
				<h1><?php echo $pageTitle ?></h1>
	<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" name="record" onSubmit="return validateForm(this);">
			<div style="float:right;width:100px;"><br>
				<div class="box">
					<div>
						id<br>
						<input name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:100%">
					</div>
					<div>
						version<br>
						<input name="vresion" type="text" value="<?php echo $therecord["version"]; ?>" size="8" maxlength="8" readonly="true" class="uneditable" style="width:100%">
					</div>
				</div>
			
			</div>
			<div style="margin-right:103px;">
				<div>
					name<br>
					<input name="displayname" type="text" value="<?php echo $therecord["displayname"]; ?>" size="8" maxlength="128" readonly="true" class="uneditable" style="width:100%;">
				</div>
				<div>
					module folder name<br>
					<input name="name" type="text" value="<?php echo $therecord["name"]; ?>" size="8" maxlength="128" readonly="true" class="uneditable" style="width:100%;">
				</div>
				<?php if($therecord["description"]){?>
				<div>
					description<br>
					<em><?php echo $therecord["description"]?></em>
				</div>
				<?php } else echo "<DIV>&nbsp;<br>&nbsp;</div>"?>
			</div>
			<div class="recordbottom" align="right">
	 	<input name="cancelclick" type="hidden" value="0">
	 	<input name="command" id="cancel" type="submit" value="cancel" class="Buttons" style="width:80px;" onClick="this.form.cancelclick.value=true;">
	 	&nbsp;&nbsp;			
			</div>
	</form>
</div>
</body>
</html>