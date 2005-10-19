<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/tabledefs_functions.php");

	include("include/tabledefs_searchfields_include.php");

	//process page
	$thecommand="";
	$action="add search field";
	$thesearchfield=setDefaultSearchField();
	
	if (isset($_GET["command"])) $thecommand=$_GET["command"];
	if (isset($_POST["command"])) $thecommand=$_POST["command"];
	
	switch($thecommand){
		case "edit":
			$singlesearchfieldsquery=getSearchfields($_GET["id"],$_GET["searchfieldid"]);
			$thesearchfield=mysql_fetch_array($singlesearchfieldsquery);
			$action="edit search field";
		break;
		
		case "delete":
			$statusmessage=deleteSearchfield($_GET["searchfieldid"]);
		break;
		
		case "add search field":
			$statusmessage=addSearchfield(addSlashesToArray($_POST),$_GET["id"]);
		break;
		
		case "edit search field":
			$statusmessage=updateSearchfield(addSlashesToArray($_POST));
		break;
		
		case "moveup":
			$statusmessage=moveSearchfield($_GET["searchfieldid"],"up");
		break;
		
		case "movedown":
			$statusmessage=moveSearchfield($_GET["searchfieldid"],"down");
		break;
	}//end switch
	
	$searchfieldsquery=getSearchfields($_GET["id"]);
	$pageTitle="Table Definition: Search Fields";
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">

<script language="JavaScript" src="../../common/javascript/fields.js"></script>
</head>
<body><?php include("../../menu.php")?>


<?php tabledefs_tabs("Search Fields",$_GET["id"]);?><div class="bodyline">
<div>
	<h1><?php echo $pageTitle?></h1>

   <table border="0" cellpadding="0" cellspacing="0" class="querytable">
	<tr>
	 <th align="left" nowrap class="queryheader">move</td>		
	 <th align="center" nowrap class="queryheader">type</td>
	 <th align="left" nowrap class="queryheader">name</td>
	 <th align="left" width="100%"  nowrap class="queryheader">field</td>
	 <th nowrap class="queryheader">&nbsp;</td>
	</tr>
	<?php 
		$topdisplayorder=-1;
		$row=1;
		while($therecord=mysql_fetch_array($searchfieldsquery)){ 
			$topdisplayorder=$therecord["displayorder"];
			if($row==1) $row=2; else $row=1;
	?>
	<tr class="qr<?php echo $row?>" style="cursor:auto">
		<td nowrap valign="top" class="small">
			<button type="button" class="invisibleButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=moveup&columnid=".$therecord["id"]?>';"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-up.png" align="middle" alt="up" width="16" height="16" border="0" /></button>
			<button type="button" class="invisibleButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=movedown&columnid=".$therecord["id"]?>';"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-down.png" align="middle" alt="dn" width="16" height="16" border="0" /></button>
			<?php echo $therecord["displayorder"]?>
		</td>
		<td nowrap valign="top" class="small" align="center"><strong><?php if($therecord["type"]=="field") echo "F"; else echo "W";?></strong></td>
		<td nowrap valign="top" class="small"><strong><?php echo $therecord["name"]?></strong></td>
		<td valign="top"><?php echo $therecord["field"]?></td>
		<td nowrap valign="top">
			<input name="command" type="button" value="edit" style="margin-right:0px;"  class="smallButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=edit&searchfieldid=".$therecord["id"]?>';">
			<input name="command" type="button" value="delete" class="smallButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=delete&searchfieldid=".$therecord["id"]?>';">
		</td>
	</tr>	
	<?php } ?>
	</table>
	<fieldset style="margin-top:15px;">
		<LEGEND><?php echo $action?></LEGEND>
		<form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"] ?>" method="post" name="record" onSubmit="return validateForm(this);">
			<input id="searchfieldid" name="searchfieldid" type="hidden" value="<?php echo $thesearchfield["id"]?>" />
			<input id="displayorder" name="displayorder" type="hidden" value="<?php if($action=="add search field") echo $topdisplayorder+1; else echo $thesearchfield["displayorder"]?>" />
			<label for="name">
				name<br />
				<?PHP field_text("name",$thesearchfield["name"],1,"Name cannot be blank","",Array("size"=>"32","maxlength"=>"64","style"=>"")); ?>
			</label>
			<label for="type">
				type<br />
				<?PHP basic_choicelist("type",$thesearchfield["type"],Array(Array("name"=>"field","value"=>"field"),Array("name"=>"where clause","value"=>"whereclause")),Array("style"=>"width:180px;"));?>
			</label>
			<label for="field">
				field name / SQL where clause<br>
				<?PHP field_text("field",$thesearchfield["field"],1,"Field Name cannot be blank","",Array("size"=>"32","maxlength"=>"255","style"=>"width:99%")); ?>
			</label>
			<div align="right">
				<input name="command" id="save" type="submit" value="<?php echo $action?>" class="Buttons" style="">		
			</div>				
		</form>
	</fieldset>
</div>
</div>
<?php include("../../footer.php");?>
</body>
</html>