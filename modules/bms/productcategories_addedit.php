<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/productcategories_addedit_include.php");
	
	$pageTitle="Product Category";
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" >
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../common/javascript/common.js"></script>
<script language="JavaScript" src="../../common/javascript/fields.js"></script>
</head>
<body><?php include("../../menu.php")?>
<?PHP if (isset($statusmessage)) {?>
	<div class="standout" style="margin-bottom:3px;"><?PHP echo $statusmessage ?></div>
<?PHP } // end if ?>
<div class="bodyline">
	<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" name="record" onSubmit="return validateForm(this);">
	<div style="float:right;width:200px;">
			<?php include("../../include/savecancel.php"); ?>
			<div class="box">
				<div>
					id<br>
					<input name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:100%">
				</div>
				<div style="margin-top:10px;"><?PHP field_checkbox("webenabled",$therecord["webenabled"])?>web enabled</div>
				<div>
					web display name<br>
					<input name="webdisplayname" type="text" value="<?php echo $therecord["webdisplayname"]?>" size="22" maxlength="64" style="width:100%">
				</div>
			</div>
	</div>
	<div style="margin-right:203px;">
		<h1><?php echo $pageTitle ?></h1>
		<div>
			name<br>
			<?PHP field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"28","maxlength"=>"64","class"=>"important","style"=>"width:100%")); ?>
		</div>
		<div>
			description<br>
			<textarea name="description" cols="32" rows="3" id="description" style="width:100%"><?PHP echo $therecord["description"]?></textarea>			
		</div>
	</div>
	<?php include("../../include/createmodifiedby.php"); ?>
	</form>
</div>
</body>
</html>