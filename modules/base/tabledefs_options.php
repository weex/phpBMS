<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/tabledefs_functions.php");

	include("include/tabledefs_options_include.php");

	//process page
	$thecommand="";
	$action="add option";
	$theoption=setOptionDefaults();
	if (isset($_GET["command"])) $thecommand=$_GET["command"];
	if (isset($_POST["command"])) $thecommand=$_POST["command"];
	
	switch($thecommand){
		case "edit":
			$singleoptionquery=getOptions($_GET["id"],$_GET["optionid"]);
			$theoption=mysql_fetch_array($singleoptionquery);
			$action="edit option";
		break;
		
		case "delete":
			$statusmessage=deleteOption($_GET["optionid"]);
		break;
		
		case "add option":
			$statusmessage=addOption(addSlashesToArray($_POST),$_GET["id"]);
		break;
		
		case "edit option":
			$statusmessage=updateOption(addSlashesToArray($_POST));
		break;
		
	}//end switch
	
	$optionsquery=getOptions($_GET["id"]);
	
	$pageTitle="Table Definition: Options";
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">

<script language="JavaScript" src="../../common/javascript/fields.js"></script>
</head>
<body><?php include("../../menu.php")?>


<?php tabledefs_tabs("Options",$_GET["id"]);?><div class="bodyline">
<div>
	<h1><?php echo $pageTitle?></h1>
   <table border="0" cellpadding="3" cellspacing="0" class="querytable">
	<tr>
	 <th nowrap class="queryheader" align="center">command list</td>
	 <th nowrap class="queryheader" align="left">name / function name</td>
	 <th nowrap class="queryheader" align="left" width="100%">option / function display name</td>
	 <th nowrap class="queryheader">&nbsp;</td>
	</tr>

	<?php 
		while($therecord=mysql_fetch_array($optionsquery)){ 
	?>
	<tr class="qr2" style="cursor:auto">
	 <td align="center" nowrap><?php if($therecord["othercommand"]) echo "<strong>X</strong>"; else echo "&nbsp;"; ?></td>
	 <td nowrap><strong><?php echo $therecord["name"]?></strong></td>
	 <td nowrap class="small"><?php echo $therecord["option"]?></td>
	 <td nowrap valign="top">
		 <input name="command" type="button" value="edit" style="margin-right:0px;"  class="smallButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=edit&optionid=".$therecord["id"]?>';">
		 <input name="command" type="button" value="delete" class="smallButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=delete&optionid=".$therecord["id"]?>';">
	 </td>
	</tr>	
	<?php } ?>
	</table>
	<fieldset>
		<legend><?php echo $action?></legend>
		<form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"] ?>" method="post" name="record" onSubmit="return validateForm(this);">
			<input id="optionid" name="optionid" type="hidden" value="<?php echo $theoption["id"]?>" />
			<div>				
				command type<br />
				<label for="oc1" style="display:inline">
				<input type="radio" id="oc1" name="othercommand" value="0" class="radiochecks" <?php if(!$theoption["othercommand"]) echo "checked"?>> pre-defined
				</label>
				<label for="oc2" style="display:inline">
				<input name="othercommand" id="oc2" type="radio" class="radiochecks" value="1" <?php if($theoption["othercommand"]) echo "checked"?>>  command list
				</label>
			</div>
			<label for="name">
				name / function name <br />
				<?PHP field_text("name",$theoption["name"],1,"Name cannot be blank","",Array("size"=>"64","maxlength"=>"64","style"=>"")); ?>
			</label>
			<label for="option">
				option / function display name<br />
				<?PHP field_text("option",$theoption["option"],1,"Option cannot be blank","",Array("size"=>"64","maxlength"=>"64","style"=>"")); ?>
			</label>
			<div align="left">
				<input name="command" id="save" type="submit" value="<?php echo $action?>" class="Buttons" style="">
			</div>
		</form>
	</fieldset>
	<fieldset>
		<legend>notes</legend>
		<div class=small>	
			<strong>pre-defined:</strong><br>
				new = 1,0 (is record creation possible: 1 for true, 0 for false)<br />
				edit = 1,0 (is editing/viewing of records possible: 1 for true, 0 for false)<br />
				select = 1,0 (is selection of records possible: 1 for true, 0 for false)<br />
				printex = 1,0 (is printing of records possible: 1 for true, 0 for false)<br />&nbsp;<br />
			<strong>command list:</strong><br />
				command list options correspond to php functions specific to the table. 
				function name references a php function defined in the &quot;include/tablename_search_functions.php&quot; file.<br /><br />
				
				The "function display name" is how the function is displayed to the end user in the command list drop down.
		</div>					
	</fieldset>	
</div>
</div>
<?php include("../../footer.php"); ?>
</body>
</html>