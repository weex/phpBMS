<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/admin_functions.php");

	include("include/relationships_addedit_include.php");
	
	$pageTitle="Relationship";
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
	<h1 style="margin-right:162px;"><?php echo $pageTitle ?></h1>
	<fieldset style="clear:both;float:right;width:160px;padding-top:0px;margin-top:0px">
		<legend><label for="id">id</label></legend>
		<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:100%">				
	</fieldset>
	<fieldset style="padding-top:0px;">
		<legend><label for="name">name</label></legend>
		<?PHP field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"64","maxlength"=>"64","style"=>"")); ?>
	</fieldset>	
	<fieldset style="clear:both;">
		<legend>from</legend>		
		<label for="fromtableid">
			table<br />
			<?php displayTables("fromtableid",$therecord["fromtableid"]) ?>
		</label>
		<label for="fromfield">
			field<br />
			<?PHP field_text("fromfield",$therecord["fromfield"],1,"From field cannot be blank.","",Array("size"=>"32","maxlength"=>"64")); ?>
		</label>
	</fieldset>
	<fieldset>
		<legend>to</legend>
		<label for="totableid">
			table<br />
			<?php displayTables("totableid",$therecord["totableid"]) ?>
		</label>
		<label for="tofield">
			field<br />
			<?PHP field_text("tofield",$therecord["tofield"],1,"To field cannot be blank.","",Array("size"=>"32","maxlength"=>"64")); ?>
		</label>
		<label for="inherint" style="margin-top:10px">
			<?PHP field_checkbox("inherint",$therecord["inherint"])?>inherint relationsip
		</label>
		<div class=small>
			<em>check the inherint relationshop box if the "to table" has been defined
			with a join statement by default (see "query table" for the "to" tables table definition), and the join already establishes
			the link for this relationship.</em> 
		</div>
	</fieldset>
	<?php include("../../include/createmodifiedby.php"); ?>
</div>
<?php include("../../footer.php"); ?>
</form>
</body>
</html>