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
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("../../head.php")?>
<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="javascript">
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
<div>
	<h1><?php echo $pageTitle?></h1>
   <table border="0" cellpadding="3" cellspacing="0" class="querytable">
	<tr>
	 <th nowrap class="queryheader" align="center">other</td>
	 <th nowrap class="queryheader" align="left">name</td>
	 <th nowrap class="queryheader" align="left">option / function</td>
	 <th nowrap class="queryheader" align="center">restricted</td>	 
	 <th nowrap class="queryheader">&nbsp;</td>
	</tr>

	<?php 
		$row=1;
		while($therecord=mysql_fetch_array($optionsquery)){ 
		if($row==1)$row=2;else $row=1;
	?>
	<tr class="qr<?php echo $row?>" style="cursor:auto">
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
	 <td nowrap align="center"><?php if ($therecord["accesslevel"]>10) echo "X"; else echo "&middot;";?></td>	
	 
	 <td nowrap valign="top">
		 <button id="edit" name="doedit" type="button" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=edit&optionid=".$therecord["id"]?>';" class="invisibleButtons"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image/button-edit.png" alt="edit" width="16" height="16" border="0" /></button>
		 <button id="delete" name="dodelete" type="button" onClick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&command=delete&optionid=".$therecord["id"]?>';" class="invisibleButtons"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image/button-delete.png" alt="delete" width="16" height="16" border="0" /></button>
	 </td>
	</tr>	
	<?php } ?>
	</table>
	<fieldset>
		<legend><?php echo $action?></legend>
		<form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"] ?>" method="post" name="record" onSubmit="return validateForm(this);">
			<input id="optionid" name="optionid" type="hidden" value="<?php echo $theoption["id"]?>" />
			<div>				
				command type<br />
				<label for="oc1" style="display:inline">
				<input type="radio" id="oc1" name="othercommand" value="0" onClick="switchType()" class="radiochecks" <?php if(!$theoption["othercommand"]) echo "checked"?>> pre-defined
				</label>
				<label for="oc2" style="display:inline">
				<input name="othercommand" id="oc2" type="radio" class="radiochecks" value="1" onClick="switchType()" <?php if($theoption["othercommand"]) echo "checked"?>>  other 
				</label>
			</div>
			<div id="pdList" style="padding:0px;margin:0px;display:none;">
				<label for="pdName">
					function<br />
					<?php basic_choicelist("pdName",$therecord["name"],array(array("value"=>"new","name"=>"new"),array("value"=>"select","name"=>"select"),array("value"=>"edit","name"=>"edit"),array("value"=>"printex","name"=>"printing")),Array("class"=>"important"));?>
				</label>
				<div>
					option<br />
					<label for="pdOptionAllowed" style="display:inline">
						<input type="radio" class="radiochecks" id="pdOptionEnabled" name="pdOption" value="1" <?php if ($theoption["option"]!==0) echo "checked"?> /> allowed
					</label>
					<label for="pdOptionNot" style="display:inline">
						<input type="radio" class="radiochecks" id="pdOptionNot" name="pdOption" value="0" <?php if ($theoption["option"]===0) echo "checked"?> /> not allowed
					</label>
				</div>
			</div>
			<div id="other" style="padding:0px;margin:0px;display:none;">
				<label for="name">
					function name <br />
					<?PHP field_text("name",$theoption["name"],0,"","",Array("size"=>"64","maxlength"=>"64","style"=>"")); ?>
				</label>
				<label for="option">
					display name<br />
					<?PHP field_text("option",$theoption["option"],0,"","",Array("size"=>"64","maxlength"=>"64","style"=>"")); ?>
				</label>
			</div>
			<label for="accesslevel">
				access level<br />
				<?php basic_choicelist("accesslevel",$theoption["accesslevel"],array(array("value"=>"-10","name"=>"portal access only"),array("value"=>"10","name"=>"basic user (shipping)"),array("value"=>"20","name"=>"Power User (sales)"),array("value"=>"30","name"=>"Manager (sales manager)"),array("value"=>"50","name"=>"Upper Manager"),array("value"=>"90","name"=>"Administrator")),Array("class"=>"important"));?>			
			</label>
			<div align="left">
				<input name="command" id="save" type="submit" value="<?php echo $action?>" class="Buttons" style="">
			</div>
		</form>
	</fieldset>
</div>
</div>
<?php include("../../footer.php"); ?>
</body>
</html>