<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/reports_addedit_include.php");

	$pageTitle="Report";

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">

<script language="JavaScript" src="../../common/javascript/fields.js"></script>
</head>
<body><?php include("../../menu.php")?>


<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
<div class="bodyline">
	<div style="float:right;width:160px;">
		  <?php showSaveCancel(1); ?>
	</div>
	<div style="margin-right:160px;">
		<h1><?php echo $pageTitle ?></h1>
	</div>

	<FIELDSET style="float:right;clear:both;width:200px">
		<LEGEND>Attributes</LEGEND>
		<label for="id">
			id<br />
			<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:170px" />
		</label>
		<label for="type">
			type<br />
			<?PHP basic_choicelist("type",$therecord["type"],Array(Array("name"=>"report","value"=>"report"),Array("name"=>"PDF report","value"=>"PDF Report"),Array("name"=>"export","value"=>"export")),Array("style"=>"width:180px","tabindex"=>"15"));?>		
		</label>
		<label for="tabledefid">
			report table<br />
			<?php displayTables("tabledefid",$therecord["tabledefid"]);?>
		</label>
		<label for="displayorder" style="padding-bottom:0px;">
			display order<br>
			<?PHP field_text("displayorder",$therecord["displayorder"],0,"","integer",Array("size"=>"10","maxlength"=>"10","tabindex"=>"25")); ?>
		</label>
		<div class="small"><em>(higher numbers are displayed first)</em></div>
		<label for="accesslevel" style="padding-bottom:0px;">
			access level<br>
			<?php basic_choicelist("accesslevel",$therecord["accesslevel"],array(array("value"=>"-10","name"=>"portal access only"),array("value"=>"10","name"=>"basic user (shipping)"),array("value"=>"20","name"=>"Power User (sales)"),array("value"=>"30","name"=>"Manager (sales manager)"),array("value"=>"50","name"=>"Upper Manager"),array("value"=>"90","name"=>"Administrator")),Array("class"=>"important","tabindex"=>"30"));?>
		</label>
	</FIELDSET>
	<FIELDSET>
		<LEGEND>name / file / description</LEGEND>
		<label for="name" class="important">
			name<br />
			<?PHP field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"32","maxlength"=>"64","style"=>"","class"=>"important","tabindex"=>"5")); ?>
		</label>
		<label style="margin-bottom:20px;">
			report file<br />
			<?PHP field_text("reportfile",$therecord["reportfile"],1,"file name cannot be blank.","",Array("size"=>"64","maxlength"=>"128","tabindex"=>"10")); ?>
		</label>
		<label for="description">
			description<br />
			<textarea id="description" name="description"  cols="61" rows="7" tabindex="35"><?PHP echo $therecord["description"]?></textarea>
		</label>
	</FIELDSET>
	<?php include("../../include/createmodifiedby.php"); ?>
</div>
<?php include("../../footer.php");?>
</form>
</body>
</html>