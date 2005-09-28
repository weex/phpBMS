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
<?PHP if (isset($statusmessage)) {?>
	<div class="standout" style="margin-bottom:3px;"><?PHP echo $statusmessage ?></div>
<?PHP } // end if ?>
<div class="bodyline">
	<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
	<div style="float:right;width:200px;"><br><br>
		<div class="box">
			<div>
				id<br>
				<input name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:100%">
			</div>
		</div>
	</div>
	<div style="margin-right:203px">
		<h1><?php echo $pageTitle ?></h1>
		<div>
			name<br>
			<?PHP field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"28","maxlength"=>"64","class"=>"important","style"=>"")); ?>			
		</div>
		<div>
			percentage<br>
			<?PHP field_text("percentage",$therecord["percentage"],0,"Percentage must be a valid number.","real",Array("size"=>"11","maxlength"=>"10","style"=>"text-align:right")); ?>%
		</div>
	</div>
	<?php include("../../include/createmodifiedby.php"); ?>	
	</form>
</div>
</body>
</html>