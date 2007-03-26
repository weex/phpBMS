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
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/tableoptions.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
	function init(){
		switchType();
	}
	
	function switchType(){
		var oc1=getObjectFromID("oc1");
		var pdFields=getObjectFromID("pdList");
		var other=getObjectFromID("other");
		if(oc1.checked){
			pdFields.style.display="block";
			other.style.display="none";
		} else {
			pdFields.style.display="none";
			other.style.display="block";
		}
	}
</script>
</head>
<body onLoad="init()"><?php include("../../menu.php")?>


<?php tabledefs_tabs("Options",$_GET["id"]);?><div class="bodyline">
	<h1 id="topTitle"><span><?php echo $pageTitle?></span></h1>
	<div class="fauxP">
   <table border="0" cellpadding="3" cellspacing="0" class="querytable">
	<tr>
	 <th nowrap align="center">other</th>
	 <th nowrap align="left">name</th>
	 <th nowrap align="left">option / function</th>
	 <th nowrap align="center">access</th>
	 <th nowrap>&nbsp;</th>
	</tr>

	<?php 
		$row=1;
		while($therecord=mysql_fetch_array($optionsquery)){ 
		if($row==1)$row=2;else $row=1;
	?>
	<tr class="qr<?php echo $row?> noselects">
	 <td align="center" nowrap><?php echo booleanFormat($therecord["othercommand"])?></td>
	 <td nowrap><strong><?php 
	 		if($therecord["othercommand"]) echo $therecord["option"]; else echo $therecord["name"];	?></strong>
	</td>
	 <td nowrap class="small"><?php 
	 	if($therecord["othercommand"]) 
			echo $therecord["name"]; 
		else {
			if($therecord["option"]==1)
				echo "allowed";
			else
				echo "not allowed";				
		}
	?></td>
	 <td nowrap align="center"><?php displayRights($therecord["roleid"],$therecord["rolename"])?></td>	
	 
	 <td nowrap valign="top">
		 <button id="edit<?php echo $therecord["id"]?>" type="button" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=edit&amp;optionid=".$therecord["id"]?>';" class="graphicButtons buttonEdit"><span>edit</span></button>
		 <button id="delete<?php echo $therecord["id"]?>" type="button" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=delete&amp;optionid=".$therecord["id"]?>';" class="graphicButtons buttonDelete"><span>delete</span></button>
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
			<input id="optionid" name="optionid" type="hidden" value="<?php echo $theoption["id"]?>" />
			<p>command type</p>
			<p>			
				<input type="radio" id="oc1" name="othercommand" value="0" onClick="switchType()" class="radiochecks" <?php if(!$theoption["othercommand"]) echo "checked=\"checked\""?>  /><label for="oc1">pre-defined</label>
				&nbsp;
				
				<input name="othercommand" id="oc2" type="radio" class="radiochecks" value="1" onClick="switchType()" <?php if($theoption["othercommand"]) echo "checked=\"checked\""?>  /><label for="oc2">other</label>
			</p>
			
			<div id="pdList">
				<p>
					<label for="pdName">function</label><br />
					<?php fieldBasicList("pdName",$therecord["name"],array(array("value"=>"new","name"=>"new"),array("value"=>"select","name"=>"select"),array("value"=>"edit","name"=>"edit"),array("value"=>"printex","name"=>"printing")));?>
				</p>
				<p>option</p>
				<p>
					<input type="radio" class="radiochecks" id="pdOptionEnabled" name="pdOption" value="1" <?php if ($theoption["option"]!==0) echo "checked=\"checked\""?> /><label for="pdOptionAllowed">allowed</label>
					&nbsp;
					<input type="radio" class="radiochecks" id="pdOptionNot" name="pdOption" value="0" <?php if ($theoption["option"]===0) echo "checked=\"checked\""?> /><label for="pdOptionNot">not allowed</label>
				</p>			
			</div>

			<div id="other">
				<p>
					<label for="name">function name</label><br />
					<?php fieldText("name",$theoption["name"],0,"","",Array("size"=>"64","maxlength"=>"64")); ?>
				</p>
				<p>
					<label for="option">display name</label><br />
					<?php fieldText("option",$theoption["option"],0,"","",Array("size"=>"64","maxlength"=>"64")); ?>
				</p>
			</div>
			<p>
				<label for="roleid">access (role)</label><br />
				<?php fieldRolesList("roleid",$theoption["roleid"],$dblink)?>				
			</p>

			<p>
				<input name="command" id="save" type="submit" value="<?php echo $action?>" class="Buttons" />
			</p>
		</form>
	</fieldset>
</div>
<?php include("../../footer.php"); ?>
</body>
</html>