<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/admin_functions.php");

	include("include/usersearches_edit_include.php");
	
	$pageTitle="Saved Searches";
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
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

<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
<div class="bodyline">
	<div style="float:right;width:180px;">
		  <?php include("../../include/savecancel.php"); ?>
		  <div class="box">
		  	<div>
				id<br>
				<input name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:100%">				
			</div>
			<div>
				type<br>
				<?PHP basic_choicelist("type",$therecord["type"],array(array("value"=>"SCH","name"=>"Search"),array("value"=>"SRT","name"=>"Sort")),Array("class"=>"uneditable","disabled"=>"true","style"=>"width:100%")); ?>
			</div>
		  </div>
	</div>
	<div style="margin-right:183px;">
		<h1><?php echo $pageTitle ?></h1>
		<div>
			name<br>
			<?PHP field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"32","maxlength"=>"128","style"=>"width:100%")); ?>
		</div>
		<div>
			table<br>
			<?php displayTables("tabledefid",$therecord["tabledefid"]) ?>
		</div>
		<div>
			user<br>
			<input name="username" type="text" value="<?php echo $username ?>" size="32" readonly="true" class="uneditable">
		</div>
		<?php if($therecord["userid"]!=0) {?>
		<div><input name="makeglobal" type="checkbox" class="radiochecks" value="1"> make global</div>
		<?php } ?>
	</div>
		<div>
			sql clause<br>
			<textarea name="sqlclause" cols="55" rows="4" style="width:100%"><?php echo $therecord["sqlclause"]?></textarea>
		</div>
<div class="recordbottom" align="right">
	<div style="padding:0px;margin:0px;">
		<?php include("../../include/savecancel.php"); ?>
	</div>
<input name="cancelclick" type="hidden" value="0">
</div>

</div>
</form>
</body>
</html>