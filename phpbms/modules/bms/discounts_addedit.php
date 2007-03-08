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

	include("include/discounts_addedit_include.php");
	
	$pageTitle="Discounts / Promotions";
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/discounts.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="javascript/discount.js" type="text/javascript"></script>
</head>
<body onLoad="init()"><?php include("../../menu.php")?>
<div class="bodyline">
	<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" name="record" onSubmit="return validateForm(this);">
	<div id="topButtons">
		  <?php showSaveCancel(1); ?>
	</div>
	<h1 id="h1Title"><span><?php echo $pageTitle ?></span></h1>
	
	<div id="fsAttributes">
		<fieldset>
			<legend>attributes</legend>
			<p>
				<label for="id">id</label><br />
				<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="22" maxlength="5" readonly="true" class="uneditable" />
			</p>			
			<p>
				<?php fieldCheckbox("inactive",$therecord["inactive"])?> <label for="inactive">inactive</label>
			</p>		
		</fieldset>
		
		<fieldset>
			<legend>statistics</legend>
			<p>
				<strong>Orders</strong> (<?php echo $stats["Order"]["total"]?>)<br/>
				total: <?php echo numberToCurrency($stats["Order"]["sum"])?>
			</p>
			<p>
				<strong>Invoices</strong> (<?php echo $stats["Invoice"]["total"]?>)<br />
				total: <?php echo numberToCurrency($stats["Invoice"]["sum"])?>
			</p>
		</fieldset>
	</div>

	<div id="nameDiv">
		<fieldset>
			<legend><label for="name">name</label></legend>
			<p>
				<?php fieldText("name",htmlQuotes($therecord["name"]),1,"Name cannot be blank.","",Array("size"=>"40","maxlength"=>"64","class"=>"important")); ?>
			</p>
		</fieldset>
		
		<fieldset>
			<legend>discount</legend>
			<p>
				<strong>type</strong><br />
				<input type="radio" class="radiochecks" id="typePercentage" name="type" value="percent" <?php if($therecord["type"]=="percent") echo "checked" ?>  onClick="changeType()" /><label for="typePercentage">percentage</label>
				&nbsp;
				<input type="radio" class="radiochecks" id="typeAmount" name="type" value="amount" <?php if($therecord["type"]=="amount") echo "checked" ?> onClick="changeType()" /><label for="typeAmount">amount</label>
			</p>
			<p id="pValue">
				<label for="percentvalue">value</label><br />
				<?php fieldPercentage("percentvalue",$therecord["value"],2,1,"Value is required",Array("size"=>"10","maxlength"=>"10"));?>
			</p>
			<p id="aValue">
				<label for="amountvalue">value</label><br />
				<?php fieldCurrency("amountvalue",$therecord["value"],1,"Value is required",Array("size"=>"10","maxlength"=>"10"))?>
			</p>			
		</fieldset>
		
		<fieldset>
			<legend><label for="description">description</label></legend>
			<p>
				<span class="notes">(description is used on invoice reports)</span><br />
				<textarea id="description" name="description" cols="38" rows="4"><?php echo htmlQuotes($therecord["description"])?></textarea>
			</p>
		</fieldset>
	</div>
	<?php include("../../include/createmodifiedby.php"); ?>
	</form>
</div>
<?php include("../../footer.php")?>
</body>
</html>