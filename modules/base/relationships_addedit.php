<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/admin_functions.php");

	include("include/relationships_addedit_include.php");
	
	$pageTitle="Table Relationship";
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
			<?PHP field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"32","maxlength"=>"64","style"=>"width:100%")); ?>
		</div>
		<div>
			from table<br>
			<?php displayTables("fromtableid",$therecord["fromtableid"]) ?>
		</div>
		<div class="dottedline">
			from field<br>
			<?PHP field_text("fromfield",$therecord["fromfield"],1,"From field cannot be blank.","",Array("size"=>"32","maxlength"=>"64")); ?>
		</div>
		<div>
			to table<br>
			<?php displayTables("totableid",$therecord["totableid"]) ?>
		</div>
		<div class="dottedline">
			to field<br>
			<?PHP field_text("tofield",$therecord["tofield"],1,"To field cannot be blank.","",Array("size"=>"32","maxlength"=>"64")); ?>
		</div>
		<div>
			<?PHP field_checkbox("inherint",$therecord["inherint"])?>inherint relationsip
			<div class=small>
			if this is checked, then the "to table" has a join statement
			(see query table in the table definition) that
			already establishes the proper relationship between the two tables.
			</div>
		</div>
	</div>
	<?php include("../../include/createmodifiedby.php"); ?>
</div>
</form>
</body>
</html>