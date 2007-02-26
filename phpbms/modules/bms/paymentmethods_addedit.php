<?php 
/*
 $Rev: 155 $ | $LastChangedBy: mipalmer $
 $LastChangedDate: 2006-10-22 15:09:20 -0600 (Sun, 22 Oct 2006) $
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

	include("include/paymentmethods_addedit_include.php");

	 $pageTitle="Payment Methods"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/paymentmethods.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="javascript/paymentmethods.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>
<div class="bodyline">
	<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onClick="return false;" /></div>
	<div id="topButtons">
		  <?php showSaveCancel(1); ?>
	</div>
	<h1 id="h1Title"><span><?php echo $pageTitle ?></span></h1>

	<fieldset id="fsAttributes">
		<legend>attribues</legend>
		<p>
			<label for="id">id</label><br />
			<input name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" />
		</p>
		<p>
			<?php field_checkbox("inactive",$therecord["inactive"])?><label for="inactive">inactive</label>
		</p>
		<p>
			<label for="priority">priority</label><br />
			<?php field_text("priority",$therecord["priority"],0,"","",Array("size"=>"8","maxlength"=>"8","class"=>"","style"=>"")); ?>			
		</p>
		<p class="notes">
			Lower priority numbered items are displayed first.
		</p>
	</fieldset>
<div id="nameDiv">
	<fieldset >
		<legend>name / type</legend>
			<p>
				<label for="name">name</label><br />
				<?php field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"28","maxlength"=>"64","class"=>"important","style"=>"")); ?>			
			</p>
			<p>
				<label for="type">type</label><br />
				<select name="type">
					<option value="" <?PHP if($therecord["type"]=="") echo "selected"; ?>>&lt;None&gt;</option>
					<option value="draft" <?PHP if($therecord["type"]=="draft") echo "selected"; ?>>Draft</option>
					<option value="charge"<?PHP if($therecord["type"]=="charge") echo "selected"; ?>>Charge</option>
					<option value="receivable"<?PHP if($therecord["type"]=="receivable") echo "selected"; ?>>Receivable</option>
				</select>
			</p>
			<p class="notes">Type determines which fields are shown on the invoice creation 
			  screen. For example, if a payment method of Business Check is selected with 
			  a type of draft then the check number, routing number and account number 
			  fields will be shown. However, if VISA is selected with a type of charge 
			  then the credit card number and expiration date fields will be shown.
			</p>
	</fieldset>
	<fieldset >
		<legend>processing</legend>
		<p>
			<?php field_checkbox("onlineprocess",$therecord["onlineprocess"],false,Array("onchange"=>"checkScript(this)"))?><label for="onlineprocess">online process</label>
		</p>
		<p id="pProcessscript" <?php if($therecord["onlineprocess"]==0) echo "style=\"display:none\" "?>>
			<label for="processscript">process script</label><br />
			<input id="processscript" name="processscript" type="text" value="<?php echo htmlQuotes($therecord["processscript"])?>" size="64" maxlength="128"/>
		</p>
	</fieldset>
</div>
	<?php include("../../include/createmodifiedby.php"); ?>	
	</form>
</div>
<?php include("../../footer.php");?>
</body>
</html>
