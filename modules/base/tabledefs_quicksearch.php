<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/tabledefs_functions.php");

	include("include/tabledefs_quicksearch_include.php");

	//process page
	$thecommand="";
	$action="add quick search item";
	$thequicksearch=setDefaultQuickSearch();
	if (isset($_GET["command"])) $thecommand=$_GET["command"];
	if (isset($_POST["command"])) $thecommand=$_POST["command"];
	
	switch($thecommand){
		case "edit":
			$singlequicksearchsquery=getQuicksearchs($_GET["id"],$_GET["quicksearchid"]);
			$thequicksearch=mysql_fetch_array($singlequicksearchsquery);
			$action="edit quick search item";
		break;
		
		case "delete":
			$statusmessage=deleteQuicksearch($_GET["quicksearchid"]);
		break;
		
		case "add quick search item":
			$statusmessage=addQuicksearch();
		break;
		
		case "edit quick search item":
			$statusmessage=updateQuicksearch();
		break;
		
		case "moveup":
			$statusmessage=moveQuicksearch($_GET["quicksearchid"],"up");
		break;
		
		case "movedown":
			$statusmessage=moveQuicksearch($_GET["quicksearchid"],"down");
		break;
	}//end switch
	
	$quicksearchsquery=getQuicksearchs($_GET["id"]);
	
	$pageTitle="Table Definition: Quick Search";
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

<?php tabledefs_tabs("Quick Search",$_GET["id"]);?><div class="untabbedbox">
<div>
	<h1><?php echo $pageTitle?></h1>
	<table border="0" cellpadding="3" cellspacing="0" class="querytable">
		<tr>
			 <th nowrap class="queryheader">Move</td>
			 <th nowrap class="queryheader" align="left">Name</td>
			 <th width="100%" nowrap class="queryheader" align="left">Search</td>
			 <th nowrap class="queryheader">&nbsp;</td>
		</tr>
	<?php 
		$topdisplayorder=-1;
		while($therecord=mysql_fetch_array($quicksearchsquery)){ 
			$topdisplayorder=$therecord["displayorder"];
	?>
	<tr>	 
	 <td nowrap valign="top">
		 <input name="command" type="button" value="up" style="margin-right:0px;" class="smallButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=moveup&quicksearchid=".$therecord["id"]?>';">
		 <input name="command" type="button" value="dn" style="margin-right:4px;" class="smallButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=movedown&quicksearchid=".$therecord["id"]?>';">
		 <?php echo $therecord["displayorder"]?>
	 </td>
	 <td nowrap valign="top"><strong><?php echo $therecord["name"]?></strong></td>
	 <td valign="top" class="small"><?php echo $therecord["search"]?></td>
	 <td nowrap valign="top">
		 <input name="command" type="button" value="edit" style="margin-right:0px;"  class="smallButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=edit&quicksearchid=".$therecord["id"]?>';">
		 <input name="command" type="button" value="delete" class="smallButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=delete&quicksearchid=".$therecord["id"]?>';">
	 </td>
	</tr>	
	<?php } ?>
	</table>
	<div>&nbsp;</div>
	<div class="box">
		<form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"] ?>" method="post" name="record" onSubmit="return validateForm(this);">
		<input name="quicksearchid" type="hidden" value="<?php echo $thequicksearch["id"]?>">
		<input name="displayorder" type="hidden" value="<?php if($action=="add quick search item") echo $topdisplayorder+1; else echo $thequicksearch["displayorder"]?>">
		<h2 style="margin-top:0px;"><?php echo $action?></h2>
		<div>
			name<br>
			<?PHP field_text("name",$thequicksearch["name"],1,"Quicksearch Name cannot be black","",Array("size"=>"32","maxlength"=>"64","style"=>"")); ?>
		</div>
		<div>
			search (SQL where clause)<br>
			<textarea name="search" cols="32" rows="3" style="width:100%"><?php echo $thequicksearch["search"] ?></textarea>
		</div>
		<div align="right">
			<input name="command" id="save" type="submit" value="<?php echo $action?>" class="Buttons" style="">
		</div>
		</form>
	</div>
</div>
</div>
</body>
</html>