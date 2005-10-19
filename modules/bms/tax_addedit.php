<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/tax_addedit_include.php");

	 $pageTitle="Tax Area"?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">

<script language="JavaScript" src="../../common/javascript/fields.js"></script>
</head>
<body><?php include("../../menu.php")?>
<div class="bodyline">
	<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
		<div style="float:right;width:160px;">
			  <?php showSaveCancel(1); ?>
		</div>
		<div style="margin-right:160px;">
			<h1><?php echo $pageTitle ?></h1>
		</div>

	<fieldset style="float:right;width:100px;padding-bottom:10px;">
		<legend>attribues</legend>
		<label for="id">
			id<br />
			<input name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:98%">
			<br/>&nbsp;
			<br/>&nbsp;
		</label>
	</fieldset>
	<fieldset >
		<legend>name / percentage</legend>
		<label for="name" class="important">
			name<br />
			<?PHP field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"28","maxlength"=>"64","class"=>"important","style"=>"")); ?>			
		</label>
		<label for="percentage">
			percentage<br />
			<?PHP field_percentage("percentage",$therecord["percentage"],5,0,"Percentage must be a valid percentage.",Array("size"=>"11","maxlength"=>"10")); ?>
		</label>
	</fieldset>
	<?php include("../../include/createmodifiedby.php"); ?>	
	</form>
</div>
<?php include("../../footer.php");?>
</body>
</html>