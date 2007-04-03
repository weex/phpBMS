<?php 
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2007, Kreotek LLC                                  |
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
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/tablequicksearch.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>

<?php showTabs($dblink,"tabledefs entry",4,$_GET["id"])?><div class="bodyline">
	<h1 id="topTitle"><span><?php echo $pageTitle?></span></h1>
	<div class="fauxP">
	<table border="0" cellpadding="3" cellspacing="0" class="querytable">
		<tr>
			 <th nowrap="nowrap">Move</th>
			 <th nowrap="nowrap"align="left">Name</th>
			 <th width="100%" nowrap="nowrap"align="left">Search</th>
			 <th width="100%" nowrap="nowrap"class="queryheader" align="left">Access</th>
			 <th nowrap="nowrap">&nbsp;</th>
		</tr>
	<?php 
		$topdisplayorder=-1;
		$row=1;
		while($therecord=mysql_fetch_array($quicksearchsquery)){ 
			$topdisplayorder=$therecord["displayorder"];
			if($row==1) $row=2; else $row=1;
	?>
	<tr class="qr<?php echo $row?> noselects">
	 <td nowrap="nowrap"valign="top">
	 	<button type="button" class="graphicButtons buttonUp" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=moveup&amp;quicksearchid=".$therecord["id"]?>';"><span>up</span></button>
	 	<button type="button" class="graphicButtons buttonDown" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=movedown&amp;quicksearchid=".$therecord["id"]?>';"><span>dn</span></button>
		 <?php echo $therecord["displayorder"]?>
	 </td>
	 <td nowrap="nowrap"valign="top"><strong><?php echo htmlQuotes($therecord["name"])?></strong></td>
	 <td valign="top" class="small"><?php echo htmlQuotes($therecord["search"])?></td>
	 <td valign="top" align="center" class="small" nowrap="nowrap"><?php echo displayRights($therecord["roleid"],$therecord["rolename"])?></td>
	 <td nowrap="nowrap" valign="top">
		 <button id="edit<?php echo $therecord["id"]?>" type="button" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=edit&amp;quicksearchid=".$therecord["id"]?>';" class="graphicButtons buttonEdit"><span>edit</span></button>
		 <button id="delete<?php echo $therecord["id"]?>" type="button" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=delete&amp;quicksearchid=".$therecord["id"]?>';" class="graphicButtons buttonDelete"><span>delete</span></button>
	 </td>
	</tr>	
	<?php } ?>
	<tr class="queryfooter">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	</table>
	</div>
	<form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"] ?>" method="post" name="record" onsubmit="return validateForm(this);">
	<fieldset>
		<legend><?php echo $action?></legend>
		<input name="quicksearchid" type="hidden" value="<?php echo $thequicksearch["id"]?>" />
		<input name="displayorder" type="hidden" value="<?php if($action=="add quick search item") echo $topdisplayorder+1; else echo $thequicksearch["displayorder"]?>" />
		<p>
			<label for="name" class="important">name</label><br/>
			<?php fieldText("name",$thequicksearch["name"],1,"Quicksearch Name cannot be black","",Array("size"=>"32","maxlength"=>"64")); ?>
		</p>
		<p>
			<label for="roleid">access (role)</label><br />
			<?php fieldRolesList("roleid",$thequicksearch["roleid"],$dblink)?>	
		</p>
		<p>
			<label for="search">search</label> <span class="notes">(SQL WHERE clause)</span><br />
			<textarea id="search" name="search" cols="32" rows="2"><?php echo htmlQuotes($thequicksearch["search"]) ?></textarea>
		</p>
		
		<p>
			<input name="command" id="save" type="submit" value="<?php echo $action?>" class="Buttons" />
		</p>
	</fieldset>
	</form>
</div>
<?php include("../../footer.php")?>
</body>
</html>