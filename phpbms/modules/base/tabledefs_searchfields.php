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


<?php tabledefs_tabs("Search Fields",$_GET["id"]);?><div class="bodyline">
	<h1 id="topTitle"><span><?php echo $pageTitle?></span></h1>
	
	<div class="fauxP">
   <table border="0" cellpadding="0" cellspacing="0" class="querytable">
	<tr>
	 <th align="left" nowrap class="queryheader">move</th>		
	 <th align="center" nowrap class="queryheader">type</th>
	 <th align="left" nowrap class="queryheader">name</th>
	 <th align="left" width="100%"  nowrap class="queryheader">field</th>
	 <th nowrap class="queryheader">&nbsp;</th>
	</tr>
	<?php 
		$topdisplayorder=-1;
		$row=1;
		while($therecord=mysql_fetch_array($searchfieldsquery)){ 
			$topdisplayorder=$therecord["displayorder"];
			if($row==1) $row=2; else $row=1;
	?>
	<tr class="qr<?php echo $row?> noselects">
		<td nowrap valign="top" class="small">
			<button type="button" class="graphicButtons buttonUp" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=moveup&amp;columnid=".$therecord["id"]?>';"><span>up</span></button>
			<button type="button" class="graphicButtons buttonDown" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=movedown&amp;columnid=".$therecord["id"]?>';"><span>dn</span></button>
			<?php echo $therecord["displayorder"]?>
		</td>
		<td nowrap valign="top" class="small" align="center"><strong><?php echo htmlQuotes($therecord["type"]);?></strong></td>
		<td nowrap valign="top" class="small"><strong><?php echo htmlQuotes($therecord["name"])?></strong></td>
		<td valign="top"><?php echo htmlQuotes($therecord["field"])?></td>
		<td nowrap valign="top">
			 <button id="edit<?php echo $therecord["id"]?>" type="button" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=edit&amp;searchfieldid=".$therecord["id"]?>';" class="graphicButtons buttonEdit"><span>edit</span></button>
			 <button id="delete<?php echo $therecord["id"]?>" type="button" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=delete&amp;searchfieldid=".$therecord["id"]?>';" class="graphicButtons buttonDelete"><span>delete</span></button>
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
	</table></div>
	
	<fieldset>
		<legend><?php echo $action?></legend>
		<form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"] ?>" method="post" name="record" onSubmit="return validateForm(this);">
			<input id="searchfieldid" name="searchfieldid" type="hidden" value="<?php echo $thesearchfield["id"]?>" />
			<input id="displayorder" name="displayorder" type="hidden" value="<?php if($action=="add search field") echo $topdisplayorder+1; else echo $thesearchfield["displayorder"]?>" />
			<p>
				<label for="name">name</label><br />
				<?php field_text("name",$thesearchfield["name"],1,"Name cannot be blank","",Array("size"=>"32","maxlength"=>"64")); ?>
			</p>
			<p>
				<label for="type">type</label><br />
				<?php basic_choicelist("type",$thesearchfield["type"],Array(Array("name"=>"field","value"=>"field"),Array("name"=>"where clause","value"=>"whereclause")));?>
			</p>
			
			<p>
				<label for="field">field name / SQL where clause</label><br />
				<?php field_text("field",$thesearchfield["field"],1,"Field Name cannot be blank","",Array("size"=>"32","maxlength"=>"255")); ?>
			
			</p>
			<p>
				<input name="command" id="save" type="submit" value="<?php echo $action?>" class="Buttons" />
			</p>				
		</form>
	</fieldset>
</div>
<?php include("../../footer.php");?>
</body>
</html>