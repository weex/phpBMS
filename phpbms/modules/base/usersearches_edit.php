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
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/usersearches.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onClick="return false;" /></div>
<div class="bodyline">
	<div id="topButtons">
		  <?php showSaveCancel(1); ?>
	</div>
	<h1 id="topTitle"><span><?php echo $pageTitle ?></span></h1>
	
	<fieldset id="fsAttributes">
		<legend>attirbutes</legend>
		<p>
			<label for="id">id</label><br />
			<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable"/>
		</p>
		
		<p>
			<label for="type">type</label><br />
			<?php fieldBasicList("type",$therecord["type"],array(array("value"=>"SCH","name"=>"Search"),array("value"=>"SRT","name"=>"Sort")),Array("class"=>"uneditable","disabled"=>"true")); ?>		
		</p>
		
		<p>
			<label for="tabledefid">table</label><br />
			<?php displayTables("tabledefid",$therecord["tabledefid"]) ?>		
		</p>
		
		<p>
			<label for="username">user</label><br />
			<input id="username" name="username" type="text" value="<?php echo htmlQuotes($username) ?>" size="32" readonly="true" class="uneditable" />
		</p>
		
		<?php if($therecord["userid"]!=0) {?>
		<p>
			<input id="makeglobal" name="makeglobal" type="checkbox" class="radiochecks" value="1" /><label for="makeglobal">make global</label>
		</p>
		<?php } else {?>
		<p>
			<label for="roleid">access (role)</label><br />
			<?php fieldRolesList("roleid",$therecord["roleid"],$dblink)?>
		</p>
		<?php } ?>
	</fieldset>

	<div id="leftSideDiv">
		<fieldset>
			<legend>name / sql</legend>
			<p>
				<label for="name" class="important">name</label><br />
				<?php fieldText("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"64","maxlength"=>"128","classe"=>"important")); ?>
			</p>
			
			<p>
				<label for="sqlclause">SQL where clause</label><br />
				<textarea id="sqlclause" name="sqlclause" cols="62" rows="12"><?php echo htmlQuotes($therecord["sqlclause"])?></textarea>
			</p>
		</fieldset>
	</div>

	<div class="box" id="createmodifiedby">
		<div id="savecancel2"><?php showSaveCancel(2)?></div>
		<div>
			<br /><br /><br /><br />
			<input id="cancelclick" name="cancelclick" type="hidden" value="0" />
		</div>		
	</div>
</div>
<?php include("../../footer.php");?>
</form>
</body>
</html>