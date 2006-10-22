<?php 
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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
			$statusmessage=addColumn(addSlashesToarray($_POST),$_GET["id"]);
		break;
		
		case "edit column":
			$statusmessage=updateColumn(addSlashesToarray($_POST));
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
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("../../head.php")?>

<script language="JavaScript" src="../../common/javascript/fields.js"></script>
</head>
<body><?php include("../../menu.php")?>


<?php tabledefs_tabs("Columns",$_GET["id"]);?><div class="bodyline"><div>
	<h1><?php echo $pageTitle?></h1>
   <table border="0" cellpadding="0" cellspacing="0" class="querytable">
	<tr>
	 <th nowrap class="queryheader">move</td>
	 <th align="left" nowrap class="queryheader">name</td>
	 <th align="left" nowrap class="queryheader">align</td>
	 <th align="center" nowrap class="queryheader">wrap</td>
	 <th align="left" nowrap class="queryheader">size</td>
	 <th align="left" nowrap class="queryheader">format</td>
	 <th nowrap class="queryheader">&nbsp;</td>
	</tr>
	<?php 
		$topdisplayorder=-1;
		$row=1;
		while($therecord=mysql_fetch_array($columnsquery)){ 
			$topdisplayorder=$therecord["displayorder"];
			if($row==1) $row=2; else $row=1;
	?>
	<tr class="qr<?php echo $row?>" style="cursor:auto">
		<td nowrap valign="top">
		 	<button type="button" class="invisibleButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=moveup&columnid=".$therecord["id"]?>';"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image/button-up.png" align="middle" alt="up" width="16" height="16" border="0" /></button>
		 	<button type="button" class="invisibleButtons" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=movedown&columnid=".$therecord["id"]?>';"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image/button-down.png" align="middle" alt="dn" width="16" height="16" border="0" /></button>
			<?php echo $therecord["displayorder"];?>
		</td>
		<td nowrap valign="top"><strong><?php echo $therecord["name"]?></strong></td>
		<td nowrap valign="top"><?php echo $therecord["align"]?></td>
		<td align="center" nowrap valign="top"><?php echo booleanFormat($therecord["wrap"])?></td>
		<td nowrap valign="top"><?php if($therecord["size"]) echo $therecord["size"]; else echo "&nbsp;";?></td>
		<td valign="top"><?php  if($therecord["format"]) echo $therecord["format"]; else  echo "&nbsp;"?></td>
		<td nowrap valign="top">
			 <button id="edit" name="doedit" type="button" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=edit&columnid=".$therecord["id"]?>';" class="invisibleButtons"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image/button-edit.png" alt="edit" width="16" height="16" border="0" /></button>
			 <button id="delete" name="dodelete" type="button" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=delete&columnid=".$therecord["id"]?>';" class="invisibleButtons"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image/button-delete.png" alt="delete" width="16" height="16" border="0" /></button>
		</td>
	</tr>	
	<?php } ?>
	</table>
	<fieldset style="margin-top:15px;">
		<legend><?php echo $action?></legend>
		<form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"] ?>" method="post" name="record" onSubmit="return validateForm(this);">
		<input id="columnid" name="columnid" type="hidden" value="<?php echo $thecolumn["id"]?>" />
		<input id="displayorder" name="displayorder" type="hidden" value="<?php if($action=="add column") echo $topdisplayorder+1; else echo $thecolumn["displayorder"]?>" />
		<label class="important" for="name">
			name<br />
			<?PHP field_text("name",$thecolumn["name"],1,"Column Name cannot be blank","",array("size"=>"32","maxlength"=>"64","class"=>"important")); ?>
		</label>
		<label for="column">
			field <em>(SQL clause)</em><br />
			<textarea id="column" name="column" cols="64" rows="2" style="width:99%"><?php echo $thecolumn["column"] ?></textarea>
		</label>
		<label for="align">
			text align<br />
			<?PHP basic_choicelist("align",$thecolumn["align"],array(array("name"=>"left","value"=>"left"),array("name"=>"center","value"=>"center"),array("name"=>"right","value"=>"right")),array("style"=>"width:170px;"));?>
		</label>
		<label for="wrap"><?PHP field_checkbox("wrap",$thecolumn["wrap"])?>wrap text</label>
		<label for="size">
			column size<br />
			<input id="size" name="size" type="text" value="<?php echo htmlQuotes($thecolumn["size"])?>" size="32" maxlength="128">
		</label>
		<label for="format">
			format<br />
			<?PHP basic_choicelist("format",$thecolumn["format"],array(array("name"=>"None","value"=>""),array("name"=>"Date","value"=>"date"),array("name"=>"Time","value"=>"time"),array("name"=>"Date and Time","value"=>"datetime"),array("name"=>"Currency","value"=>"currency"),array("name"=>"Boolean","value"=>"boolean"),array("name"=>"File Link","value"=>"filelink")),array("style"=>"width:170px;"));?>						
		</label>
		<label for="sortorder">
			sort order <em>(SQL clause)</em><br />
			<input id="sortorder" name="sortorder" type="text" value="<?php echo htmlQuotes($thecolumn["sortorder"])?>" size="64" maxlength="128" style="width:99%">
		</label>
		<div class="small"><em>leave sort order blank if it is exactly the same as the field</em></div>
		<label for="footerquery">
			footer <em>(SQL group function)</em><br />
			<textarea id="footerquery" name="footerquery" cols="32" rows="2" style="width:99%"><?php echo $thecolumn["footerquery"] ?></textarea>
		</label>
		<div align="right">
			<input name="command" id="save" type="submit" value="<?php echo $action?>" class="Buttons">
		</div>
		</form>
	</fieldset>
</div></div>
<?php include("../../footer.php")?>
</body>
</html>