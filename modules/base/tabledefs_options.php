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
			$statusmessage=addOption();
		break;
		
		case "edit option":
			$statusmessage=updateOption();
		break;
		
	}//end switch
	
	$optionsquery=getOptions($_GET["id"]);
	
	$pageTitle="Table Definition: Options";
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

<?php tabledefs_tabs("Options",$_GET["id"]);?><div class="untabbedbox">
<div>
	<h1><?php echo $pageTitle?></h1>
   <table border="0" cellpadding="3" cellspacing="0" class="querytable">
	<tr>
	 <th nowrap class="queryheader" align="center">Other Cmd</td>
	 <th nowrap class="queryheader" align="left">Name</td>
	 <th nowrap class="queryheader" align="left" width="100%">Option</td>
	 <th nowrap class="queryheader">&nbsp;</td>
	</tr>

	<?php 
		while($therecord=mysql_fetch_array($optionsquery)){ 
	?>
	<tr>
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
	<div>&nbsp;</div>
	<table border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td class=box valign=top nowrap>
		<h2 style="margin-top:0px;"><?php echo $action?></h2>
		<form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"] ?>" method="post" name="record" onSubmit="return validateForm(this);">
			<input name="optionid" type="hidden" value="<?php echo $theoption["id"]?>">
			<div>				
				list option as other command<br>
				<input type="radio" name="othercommand" value="0" class="radiochecks" <?php if(!$theoption["othercommand"]) echo "checked"?>> no
				&nbsp;&nbsp;
				<input name="othercommand" type="radio" class="radiochecks" value="1" <?php if($theoption["othercommand"]) echo "checked"?>>  yes
			</div>
			<div>
				name / function name <br>
				<?PHP field_text("name",$theoption["name"],1,"Name cannot be blank","",Array("size"=>"32","maxlength"=>"64","style"=>"")); ?>
			</div>			
			<div>
				option / function display name<br>
				<?PHP field_text("option",$theoption["option"],1,"Option cannot be blank","",Array("size"=>"32","maxlength"=>"64","style"=>"")); ?>
			</div>
			<div align="right">
				<input name="command" id="save" type="submit" value="<?php echo $action?>" class="Buttons" style="">
			</div>
		</form>			
			</td>
			<td class=tiny>&nbsp;</td>
			<td class=box valign=top>
		<div class=small>
		
			<h2 style="margin-top:0px;">Notes about Adding Options:</h2>
			<strong>Predefined Options:</strong><br>
				new = 1,0 (dictates new record creation is possible: 1 for true, 0 for false)<br>
				edit = 1,0 (dictates editing of records is possible: 1 for true, 0 for false)<br>
				select = 1,0 (dictates selection of records is possible: 1 for true, 0 for false)<br>
				printex = 1,0 (dictates printing/export of records is possible: 1 for true, 0 for false)<br>&nbsp;<br>
			<strong>other command options:</strong><br>
				Other commands should correspond to functions specific to the table. the name should contain no spaces, and correspond to a function defined in the &quot;include/tablename_search_functions.php&quot; file. The 'option' field should represents the plain english phrase that will show in the commands drop down on the list view.
		</div>					
			</td>
		</tr>
	</table>
</div>
</div>
</body>
</html>