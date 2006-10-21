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
			$statusmessage=addQuicksearch(addSlashesToArray($_POST),$_GET["id"]);
		break;
		
		case "edit quick search item":
			$statusmessage=updateQuicksearch(addSlashesToArray($_POST));
		break;
		
		case "moveup":
			$statusmessage=moveQuicksearch($_GET["quicksearchid"],"up",$_GET["id"]);
		break;
		
		case "movedown":
			$statusmessage=moveQuicksearch($_GET["quicksearchid"],"down",$_GET["id"]);
		break;
	}//end switch
	
	$quicksearchsquery=getQuicksearchs($_GET["id"]);
	
	$pageTitle="Table Definition: Quick Search";
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("../../head.php")?>

<script language="JavaScript" src="../../common/javascript/fields.js"></script>
</head>
<body><?php include("../../menu.php")?>


<?php tabledefs_tabs("Quick Search",$_GET["id"]);?><div class="bodyline">
<div>
	<h1><?php echo $pageTitle?></h1>
	<table border="0" cellpadding="3" cellspacing="0" class="querytable" style="margin-bottom:15px;">
		<tr>
			 <th nowrap class="queryheader">Move</td>
			 <th nowrap class="queryheader" align="left">Name</td>
			 <th width="100%" nowrap class="queryheader" align="left">Search</td>
			 <th width="100%" nowrap class="queryheader" align="left">Access Level</td>
			 <th nowrap class="queryheader">&nbsp;</td>
		</tr>
	<?php 
		$topdisplayorder=-1;
		$row=1;
		while($therecord=mysql_fetch_array($quicksearchsquery)){ 
			$topdisplayorder=$therecord["displayorder"];
			if($row==1) $row=2; else $row=1;
	?>
	<tr class="qr<?php echo $row?>" style="cursor:auto">
	 <td nowrap valign="top">
	 	<button type="button" class="invisibleButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=moveup&quicksearchid=".$therecord["id"]?>';"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image/button-up.png" align="middle" alt="up" width="16" height="16" border="0" /></button>
	 	<button type="button" class="invisibleButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=movedown&quicksearchid=".$therecord["id"]?>';"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image/button-down.png" align="middle" alt="dn" width="16" height="16" border="0" /></button>
		 <?php echo $therecord["displayorder"]?>
	 </td>
	 <td nowrap valign="top"><strong><?php echo $therecord["name"]?></strong></td>
	 <td valign="top" class="small"><?php echo $therecord["search"]?></td>
	 <td valign="top" align=center class="small"><?php echo $therecord["accesslevel"]?></td>
	 <td nowrap valign="top">
		 <button id="edit" name="doedit" type="button" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=edit&quicksearchid=".$therecord["id"]?>';" class="invisibleButtons"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image/button-edit.png" alt="edit" width="16" height="16" border="0" /></button>
		 <button id="delete" name="dodelete" type="button" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=delete&quicksearchid=".$therecord["id"]?>';" class="invisibleButtons"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image/button-delete.png" alt="delete" width="16" height="16" border="0" /></button>
	 </td>
	</tr>	
	<?php } ?>
	</table>
	<form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"] ?>" method="post" name="record" onSubmit="return validateForm(this);">
	<fieldset>
		<legend><?php echo $action?></legend>
		<input name="quicksearchid" type="hidden" value="<?php echo $thequicksearch["id"]?>" />
		<input name="displayorder" type="hidden" value="<?php if($action=="add quick search item") echo $topdisplayorder+1; else echo $thequicksearch["displayorder"]?>" />
		<label for="name" class="important">
			name<br/>
			<?PHP field_text("name",$thequicksearch["name"],1,"Quicksearch Name cannot be black","",Array("size"=>"32","maxlength"=>"64","style"=>"")); ?>
		</label>
		<label for="accesslevel">
			access level<br />
			<?php basic_choicelist("accesslevel",$thequicksearch["accesslevel"],array(array("value"=>"-10","name"=>"portal access only"),array("value"=>"10","name"=>"basic user (shipping)"),array("value"=>"20","name"=>"Power User (sales)"),array("value"=>"30","name"=>"Manager (sales manager)"),array("value"=>"50","name"=>"Upper Manager"),array("value"=>"90","name"=>"Administrator")));?>
		</label>
		<label for="search">
			search (SQL where clause)<br>
			<textarea id="search" name="search" cols="32" rows="2" style="width:99%"><?php echo htmlQuotes($thequicksearch["search"]) ?></textarea>
		</label>
		<div align="right">
			<input name="command" id="save" type="submit" value="<?php echo $action?>" class="Buttons" style="">
		</div>
	</fieldset>
	</form>
</div>
</div>
<?php include("../../footer.php")?>
</body>
</html>