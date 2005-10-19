<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/clientemailprojects_edit_include.php");
?><?PHP $pageTitle="Client E-mail Project"?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js"></script>
</head>
<body><?php include("../../menu.php")?>

<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
<div class="bodyline">
	<div style="float:right;width:160px;">
		  <?php showSaveCancel(1); ?>
	</div>
	<h1 style="margin-right:162px;"><?php echo $pageTitle ?></h1>
	<fieldset style="clear:both;float:right;width:180px;margin-top:0px;">
		<legend><label for=id>id</label></label></legend>
		<div style="padding-top:0px;">
		<input name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:99%"/>
		</div>
	</fieldset>

	<fieldset>
		<legend><label for="name">name</label></legend>
		<div style="padding-top:0px;"><?PHP field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"32","maxlength"=>"128","style"=>"width:99%")); ?></div>
	</fieldset>
	
	<fieldset>
		<legend><label for="username">user</label></legend>
		<div style="padding-top:0px;">
			<input id="username" name="username" type="text" value="<?php echo $username ?>" size="32" readonly="true" class="uneditable">
		</div>
		<?php if($therecord["userid"]!=0) {?>
		<label for="makeglobal"><input id="makeglobal" name="makeglobal" type="checkbox" class="radiochecks" value="1"> make global</label>
		<?php } ?>
	</fieldset>

	<fieldset>
		<legend>e-mail</legend>
		<label for="from">
			from<br />
			<?php if(is_numeric($therecord["emailfrom"])) $therecord["emailfrom"]=getEmailInfo($therecord["emailfrom"]);?>
			<input id="from" name="from" value="<?php echo htmlQuotes($therecord["emailfrom"])?>" style="width:50%" readonly="true" class="uneditable">
		</label>
		<label for="to">
			to<br />
			<input id="to" name="to" value="<?php if($therecord["emailto"]=="selected" or $therecord["emailto"]=="all") echo htmlQuotes($therecord["emailto"]); else echo "saved search"?>" style="width:50%" readonly="true" class="uneditable">
			<?php if(is_numeric($therecord["emailto"]))	{?>			
			&nbsp;<input id="to2" name="to2" value="<?php echo htmlQuotes(showSavedSearch($therecord["emailto"]))?>" style="width:40%" readonly="true" class="uneditable">				
			<?php } ?>
		</label>
		<label for="subject">
			subject<br />
			<input id="subject" name="subject" type="text" value="<?php echo $therecord["subject"]?>" size="32" readonly="true" class="uneditable" style="width:99%">
		</label>
		<label for="body">
			<textarea id="body" name="body" readonly="true" class="uneditable" style="width:99%" rows=20><?php echo $therecord["body"]?></textarea>
		</label>
	</fieldset>
	<div class="box" align="right">
	<div style="padding:0px;margin:0px;">
		<?php showSaveCancel(2); ?>
	</div>
<input id="cancelclick" name="cancelclick" type="hidden" value="0" />
</div>
</div>
<?php include("../../footer.php");?>
</form>

</body>
</html>