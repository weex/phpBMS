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

<div class="bodyline">
	<h1><?php echo $pageTitle ?></h1>
	<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" name="record" onSubmit="return validateForm(this);">			
		<div style="float:right;width:100px;padding:0px;">
			<fieldset style="margin-top:0px;padding-top:0px;">
				<LEGEND>attributes</LEGEND>
				<label for="id">
					id<br />
					<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:99%" />
				</label>
				<label for="version">
					version<br />
					<input id="version" name="version" type="text" value="<?php echo $therecord["version"]; ?>" size="8" maxlength="8" readonly="true" class="uneditable" style="width:99%" />
				</label>
			</fieldset>			
		</div>
		<div style="margin-right:105px;padding:0px;">
			<FIELDSET>
				<LEGEND>name / folder</LEGEND>
				<label for="displayname">
					name<br />
					<input id="displayname" name="displayname" type="text" value="<?php echo htmlQuotes($therecord["displayname"]); ?>" size="8" maxlength="128" readonly="true" class="uneditable" style="width:99%;" />
				</label>
				<label for="name">
					folder name <em>(location)</em><br>
					<input id="name" name="name" type="text" value="<?php echo htmlQuotes($therecord["name"]); ?>" size="8" maxlength="128" readonly="true" class="uneditable" style="width:99%;" />
				</label>					
			</FIELDSET>
		</div>
		<fieldset style="clear:both;">
			<legend><label for="description">description</label></legend>
			<div style="padding-top:0px;"><textarea id="description" name="description" rows=7 cols="56" readonly="readonly" class="uneditable" style="width:99%"><?php echo $therecord["description"]?></textarea></div>
		</fieldset>
		
		<div class="box" align="right">
			<input name="cancelclick" type="hidden" value="0" />
			<input name="command" id="cancel" type="submit" value="cancel" class="Buttons" style="width:80px;margin-right:10px;" onClick="this.form.cancelclick.value=true;" />
			</div>
	</form>
</div>
</body>
</html>