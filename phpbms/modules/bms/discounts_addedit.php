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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("../../head.php")?>
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="javascript/discount.js"></script>
</head>
<body onLoad="init()"><?php include("../../menu.php")?>
<div class="bodyline">
	<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" name="record" onSubmit="return validateForm(this);">
	<div style="float:right;width:160px;">
		  <?php showSaveCancel(1); ?>
	</div>
	<div style="margin-right:160px;">
		<h1><?php echo $pageTitle ?></h1>
	</div>
	
	<div style="clear:both;margin:0px;width:200px;padding:0px;float:right;">
	<fieldset >
		<legend>attributes</legend>
		<label for="id">
			id<br />
			<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="22" maxlength="5" readonly="true" class="uneditable" style="width:99%">
		</label>
		<label for="inactive" style="text-align:center;"><?php field_checkbox("inactive",$therecord["inactive"])?> inactive</label>
	</fieldset>
	<fieldset >
		<legend>statistics</legend>
		<div>
			<strong>Orders</strong> (<?php echo $stats["Order"]["total"]?>)<br/>
			<div >total: <?php echo currencyFormat($stats["Order"]["sum"])?></div>			
		</div>
		<div>
			<strong>Invoices</strong> (<?php echo $stats["Invoice"]["total"]?>)<br />
			<div >total: <?php echo currencyFormat($stats["Invoice"]["sum"])?></div>			
		</div>
	</fieldset>
	</div>
	<div style="margin:0px;margin-right:205px;padding:0px;">
		<fieldset>
			<legend><label for="name">name</label></legend>
			<div style="padding-top:0px;">
				<?php field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"40","maxlength"=>"64","class"=>"important","style"=>"width:98%")); ?>
			</div>
		</fieldset>
		<fieldset>
			<legend>discount</legend>
			<div><strong>type</strong>
				<label for="typePercentage"><input type="radio" class="radiochecks" id="typePercentage" name="type" value="percent" <?php if($therecord["type"]=="percent") echo "checked" ?>  onClick="changeType()" />percentage</label>
				<label for="typeAmount"><input type="radio" class="radiochecks" id="typeAmount" name="type" value="amount" <?php if($therecord["type"]=="amount") echo "checked" ?> onClick="changeType()" />amount</label>
			</div>
			<label for="percentvalue" id="pValue">
				value<br />
				<?php field_percentage("percentvalue",$therecord["value"],2,1,"Value is required",Array("size"=>"10","maxlength"=>"10"));?>
			</label>
			<label for="amountvalue" id="aValue">
				value<br />
				<?php field_dollar("amountvalue",$therecord["value"],1,"Value is required",Array("size"=>"10","maxlength"=>"10"))?>
			</label>
		</fieldset>
		<fieldset>
			<legend><label for="description">description</label></legend>
			<div style="padding-top:0px;"><textarea name="description" cols="38" rows="4" id="description" style="width:98%"><?php echo $therecord["description"]?></textarea></div>
			<div class="small"><em>(description is used on invoice reports)</em></div>
		</fieldset>
		</div>
	<?php include("../../include/createmodifiedby.php"); ?>
	</form>
</div>
<?php include("../../footer.php")?>
</body>
</html>