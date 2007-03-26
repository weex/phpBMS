<?php 
/*
 $Rev: 155 $ | $LastChangedBy: mipalmer $
 $LastChangedDate: 2006-10-22 15:09:20 -0600 (Sun, 22 Oct 2006) $
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

	include("include/invoicestatuses_addedit_include.php");

	 $pageTitle="Invoice Statuses"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/invoicestatuses.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>
<div class="bodyline">
	<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" name="record" onsubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onclick="return false;" /></div>
	<div id="topButtons">
		  <?php showSaveCancel(1); ?>
	</div>
	<h1 id="h1Title"><span><?php echo $pageTitle ?></span></h1>

	<fieldset id="fsAttributes">
		<legend>attribues</legend>
		<p>
			<label for="id">id</label><br />
			<input name="id" id="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="readonly" class="uneditable" />		
		</p>
		<p>		
			<?php fieldCheckbox("inactive",$therecord["inactive"],false,Array("tabindex"=>"4"))?><label for="inactive">inactive</label>
		</p>
		<p>
			<label for="priority">priority</label><br />
			<?php fieldText("priority",$therecord["priority"],$required=true,$message="Priority must be a valid integer.",$type="integer",$attributes=Array("size"=>"4","maxlength"=>"4"))?>
		</p>
		<p class="notes">
			Lower priority numbered items are displayed first.
		</p>
	</fieldset>

	<div id="nameDiv">
		<fieldset >
			<legend><label for="name">name</label></legend>
			<p><br />
				<?php fieldText("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"45","maxlength"=>"128","class"=>"important","style"=>"")); ?>			
			</p>
		</fieldset>
		<fieldset>
			<legend>Defaults</legend>
			<p><br />
				<?php fieldCheckbox("invoicedefault",$therecord["invoicedefault"],false,Array("tabindex"=>"4"))?><label for="invoicedefault">new order default</label>				
			</p>
			<p>
				<label for="defaultassignedtoid">assigned to</label><br />
				<?php fieldAutofill("defaultassignedtoid",$therecord["defaultassignedtoid"],9,"users.id","concat(users.firstname,\" \",users.lastname)","\"\"","users.revoked!=1",Array("size"=>"45","maxlength"=>"128")) ?>
			</p>
		</fieldset>
	</div>
	<?php include("../../include/createmodifiedby.php"); ?>	
	</form>
</div>
<?php include("../../footer.php");?>
</body>
</html>