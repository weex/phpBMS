<?php 
/*
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
 | All rights reserved.                                                    |
 +-------------------------------------------------------------------------+
 |                                                                         |
 | Redistribution and use in source and binary forms, with or without      |
 | modification, are permitted provided that the following conditions are  |
 | met:                                                                    |
 |                                                                         |
 | - Redistributions of source code must retain the above copyright        |
 |   notice, this list of conditions and the following disclaimer.         |
 |                                                                         |
 | - Redistributions in binary form must reproduce the above copyright     |
 |   notice, this list of conditions and the following disclaimer in the   |
 |   documentation and/or other materials provided with the distribution.  |
 |                                                                         |
 | - Neither the name of Kreotek LLC nor the names of its contributore may |
 |   be used to endorse or promote products derived from this software     |
 |   without specific prior written permission.                            |
 |                                                                         |
 | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS     |
 | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT       |
 | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A |
 | PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
 | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,   |
 | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT        |
 | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,   |
 | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY   |
 | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT     |
 | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE   |
 | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.    |
 |                                                                         |
 +-------------------------------------------------------------------------+
*/

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
			$statusmessage=moveSearchfield($_GET["columnid"],"up");
		break;
		
		case "movedown":
			$statusmessage=moveSearchfield($_GET["columnid"],"down");
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
		<td nowrap valign="top" class="small" align="center"><strong><?php echo $therecord["type"] ;?></strong></td>
		<td nowrap valign="top" class="small"><strong><?php echo $therecord["name"]?></strong></td>
		<td valign="top"><?php echo $therecord["field"]?></td>
		<td nowrap valign="top">
			 <button id="edit" name="doedit" type="button" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=edit&searchfieldid=".$therecord["id"]?>';" class="invisibleButtons"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-edit.png" alt="edit" width="16" height="16" border="0" /></button>
			 <button id="delete" name="dodelete" type="button" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=delete&searchfieldid=".$therecord["id"]?>';" class="invisibleButtons"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-delete.png" alt="delete" width="16" height="16" border="0" /></button>
		</td>
	</tr>	
	<?php } ?>
	</table>
	<fieldset style="margin-top:15px;">
		<legend><?php echo $action?></legend>
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