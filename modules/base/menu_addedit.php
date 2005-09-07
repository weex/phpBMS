<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");
	include("include/menu_addedit_include.php");
	
	$pageTitle="Navigation Menu Item";
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

<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
<div class="bodyline">
	<div style="float:right;width:180px;">
		<?php include("../../include/savecancel.php"); ?>
		<div class="box">
			<div>
				id<br>
				<input name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="10" maxlength="10" readonly="true" class="uneditable" style="width:100%">
			</div>
		</div>
	</div>
	<div style="margin-right:183px;">
		<h1><?php echo $pageTitle ?></h1>
		<div>
			name<br>
			<?PHP field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"28","maxlength"=>"64","class"=>"important","style"=>"width:100%")); ?>			
		</div>
		<div class="box">
			<div>
			<strong>link</strong><br>
		 	<?php displayTableDropDown($therecord["link"]) ?>
			</div>
		 	<div><strong>-- or --</strong></div>
			<div>
		 	<input name="link" type="text" value="<?php if(substr($therecord["link"],0,10)!="search.php" ) echo $therecord["link"]?>" size="32" maxlength="255" style="width:100%">
			<div class=small>(set drop down to &quot;-- typed --&quot; and leave field above blank if this item is a category)</div>
			</div>
		</div>
		<div>parent<br>
			<?php displayParentDropDown($therecord["parentid"],$therecord["id"]) ?>
		</div>
		<div>
			 display order<br>
			 <?PHP field_text("displayorder",$therecord["displayorder"],1,"Display order cannot be blank and must be a valid integer.","integer",Array("size"=>"9","maxlength"=>"3")); ?>
			 <div class=small>(Lower numbers are displayed first.)</div>
		</div>
		<div>
			required access level for viewing<br>
			<?PHP field_text("accesslevel",$therecord["accesslevel"],1,"Access level cannot be blank and must be a valid integer.","integer",Array("size"=>"9","maxlength"=>"3")); ?>
		</div>
	</div>
	<?php include("../../include/createmodifiedby.php"); ?>
</div>
</form>
</body>
</html>