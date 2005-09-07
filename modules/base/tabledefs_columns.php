<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/tabledefs_functions.php");

	include("include/tabledefs_columns_include.php");

	//process page
	$thecommand="";
	$action="add column";
	$thecolumn=setColumnDefaults();
	
	if (isset($_GET["command"])) $thecommand=$_GET["command"];
	if (isset($_POST["command"])) $thecommand=$_POST["command"];
	
	switch($thecommand){
		case "edit":
			$singlecolumnsquery=getColumns($_GET["id"],$_GET["columnid"]);
			$thecolumn=mysql_fetch_array($singlecolumnsquery);
			$action="edit column";
		break;
		
		case "delete":
			$statusmessage=deleteColumn($_GET["columnid"]);
		break;
		
		case "add column":
			$statusmessage=addColumn();
		break;
		
		case "edit column":
			$statusmessage=updateColumn();
		break;
		
		case "moveup":
			$statusmessage=moveColumn($_GET["columnid"],"up");
		break;
		
		case "movedown":
			$statusmessage=moveColumn($_GET["columnid"],"down");
		break;
	}//end switch
	
	$columnsquery=getColumns($_GET["id"]);
	
	$pageTitle="Table Definition: Columns";
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

<?php tabledefs_tabs("Columns",$_GET["id"]);?><div class="untabbedbox">
<div>
	<h1><?php echo $pageTitle?></h1>
   <table border="0" cellpadding="0" cellspacing="0" class="querytable">
	<tr>
	 <th nowrap class="queryheader">move</td>
	 <th align="left" nowrap class="queryheader" width=100%>name</td>
	 <th align="center" nowrap class="queryheader">align</td>
	 <th align="center" nowrap class="queryheader">wrap</td>
	 <th align="center" nowrap class="queryheader">size</td>
	 <th align="center" nowrap class="queryheader">sort order</td>
	 <th nowrap class="queryheader">&nbsp;</td>
	</tr>
	<?php 
		$topdisplayorder=-1;
		while($therecord=mysql_fetch_array($columnsquery)){ 
			$topdisplayorder=$therecord["displayorder"];
	?>
	<tr>
		<td nowrap valign="top">
			<input name="command" type="button" value="up"		style="margin:0px;" class="smallButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=moveup&columnid=".$therecord["id"]?>';">
			<input name="command" type="button" value="dn" 	style="margin:0px;" class="smallButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=movedown&columnid=".$therecord["id"]?>';">
			<?php echo $therecord["displayorder"];?>
		</td>
		<td nowrap valign="top"><strong><?php echo $therecord["name"]?></strong></td>
		<td align="center" nowrap valign="top"><?php echo $therecord["align"]?></td>
		<td align="center" nowrap valign="top"><?php if($therecord["wrap"]) echo "X"; else echo "&nbsp;";?></td>
		<td align="center" nowrap valign="top"><?php if($therecord["size"]) echo $therecord["size"]; else echo "&nbsp;";?></td>
		<td align="center" valign="top"><?php  if($therecord["sortorder"]) echo $therecord["sortorder"]; else  echo "&nbsp;"?></td>
		<td nowrap valign="top">
			<input name="command" type="button" value="edit" 	style="margin:0px;"  class="smallButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=edit&columnid=".$therecord["id"]?>';">
			<input name="command" type="button" value="delete"	style="margin:0px;"  class="smallButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=delete&columnid=".$therecord["id"]?>';">
		</td>
	</tr>	
	<?php } ?>
	</table>
	<div>&nbsp;</div>
	<div class="box">
		<h2 style="margin-top:0px;"><?php echo $action?></h2>
		<form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"] ?>" method="post" name="record" onSubmit="return validateForm(this);">
		<input name="columnid" type="hidden" value="<?php echo $thecolumn["id"]?>">
		<input name="displayorder" type="hidden" value="<?php if($action=="add column") echo $topdisplayorder+1; else echo $thecolumn["displayorder"]?>">
		<div class="important">
			name<br>
			<?PHP field_text("name",$thecolumn["name"],1,"Column Name cannot be blank","",Array("size"=>"32","maxlength"=>"64","class"=>"important")); ?>
		</div>
		<div>
			text align<br>
			<?PHP basic_choicelist("align",$thecolumn["align"],Array(Array("name"=>"left","value"=>"left"),Array("name"=>"center","value"=>"center"),Array("name"=>"right","value"=>"right")),Array("style"=>"width:170px;"));?>
		</div>
		<div><?PHP field_checkbox("wrap",$thecolumn["wrap"])?>wrap text</div>
		<div>
			column size<br>
			<input name="size" type="text" value="<?php echo $thecolumn["size"]?>" size="32" maxlength="128" style="width:80%">
		</div>
		<div>
			column (SQL clause)<br>
			<textarea name="column" cols="32" rows="2" style="width:100%"><?php echo $thecolumn["column"] ?></textarea>
		</div>
		<div>
			sort order (SQL clause)<br>
			<input name="sortorder" type="text" value="<?php echo $thecolumn["sortorder"]?>" size="32" maxlength="128" style="width:80%">
			<div class="small">leave this blank if the sort order is the exact same as the column SQL</div>
		</div>
		<div>
			footer query<br>
			<textarea name="footerquery" cols="32" rows="2" style="width:100%"><?php echo $thecolumn["footerquery"] ?></textarea>
		</div>
		<div align="right">
			<input name="command" id="save" type="submit" value="<?php echo $action?>" class="Buttons">
		</div>
		</form>
	</div>
</div>
</div>
</body>
</html>