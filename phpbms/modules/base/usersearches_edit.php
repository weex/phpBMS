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

	include("include/usersearches_edit_include.php");
	
	$pageTitle="Saved Searches";
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("../../head.php")?>

<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>


<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onClick="return false;"></div>
<div class="bodyline">
	<div style="float:right;width:160px;">
		  <?php showSaveCancel(1); ?>
	</div>
	<h1 style="margin-right:165px;"><?php echo $pageTitle ?></h1>
	<fieldset class="box" style="float:right;width:200px;">
		<legend>attirbutes</legend>
		<label for="id">
			id<br />
			<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:98%">				
		</label>
		<label for="type">
			type<br />
			<?PHP basic_choicelist("type",$therecord["type"],array(array("value"=>"SCH","name"=>"Search"),array("value"=>"SRT","name"=>"Sort")),Array("class"=>"uneditable","disabled"=>"true","style"=>"width:98%")); ?>
		</label>
		<label for="tabledefid">
			table<br />
			<?php displayTables("tabledefid",$therecord["tabledefid"]) ?>
		</label>
		<label for="username">
			user<br />
			<input id="username" name="username" type="text" value="<?php echo htmlQuotes($username) ?>" size="32" readonly="true" class="uneditable">
		</label>
		<?php if($therecord["userid"]!=0) {?>
		<label for="makeglobal" style="text-align:center"><input id="makeglobal" name="makeglobal" type="checkbox" class="radiochecks" value="1"> make global</label>
		<?php } else {?>
		<label for="accesslevel">
			access level<br />
			<?php basic_choicelist("accesslevel",$therecord["accesslevel"],array(array("value"=>"-10","name"=>"portal access only"),array("value"=>"10","name"=>"basic user (shipping)"),array("value"=>"20","name"=>"Power User (sales)"),array("value"=>"30","name"=>"Manager (sales manager)"),array("value"=>"50","name"=>"Upper Manager"),array("value"=>"90","name"=>"Administrator")),Array("class"=>"important"));?>			
		</label>
		<?php } ?>
	</fieldset>
	<fieldset>
		<legend>name / sql</legend>
		<label for="name">
			name<br />
			<?PHP field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"64","maxlength"=>"128","style"=>"")); ?>
		</label>
		<label for="sqlclause">
			sql clause<br />
			<textarea id="sqlclause" name="sqlclause" cols="62" rows="10"><?php echo $therecord["sqlclause"]?></textarea>
		</label>
	</fieldset>

<div class="box" align="right" style="clear:both;">
	<div style="padding:0px;margin:0px;">
		<?php showSaveCancel(2); ?>
	</div>
	<input id="cancelclick" name="cancelclick" type="hidden" value="0" />
</div>
</div>
<?php include("../../footer.php");?>
</form>
</body>
</html>