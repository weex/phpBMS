<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/reports_addedit_include.php");

	$pageTitle="Report";

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" >
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
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
		  </div>
	</div>
	<div style="margin-right:183px;">
		<h1><?php echo $pageTitle ?></h1>
		<div>
			name<br>
			<?PHP field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"32","maxlength"=>"64","style"=>"","class"=>"important")); ?>
		</div>
		<div>
			type<br>
			<?PHP basic_choicelist("type",$therecord["type"],Array(Array("name"=>"report","value"=>"report"),Array("name"=>"PDF report","value"=>"PDF Report"),Array("name"=>"export","value"=>"export")));?>		
		</div>
		<div>
			display order (higher numbers are displayed first)<br>
			<?PHP field_text("displayorder",$therecord["displayorder"],0,"","integer",Array("size"=>"10","maxlength"=>"10","style"=>"")); ?>
		</div>
		<div>
			report table<br>
			<?php displayTables("tabledefid",$therecord["tabledefid"]);?>
		</div>
		<div>
			report file<br>
			<?PHP field_text("reportfile",$therecord["reportfile"],1,"file name cannot be blank.","",Array("size"=>"32","maxlength"=>"128","style"=>"width:100%")); ?>
		</div>
		<div>
			description<br>
			<textarea name="description" rows="3" id="description" style="width:100%"><?PHP echo $therecord["description"]?></textarea>
		</div>
	</div>
	<?php include("../../include/createmodifiedby.php"); ?>
</div>
</form>
</body>
</html>