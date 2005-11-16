<?php 
/*
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

	include("include/productcategories_addedit_include.php");
	
	$pageTitle="Product Category";
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">

<script language="JavaScript" src="../../common/javascript/fields.js"></script>
</head>
<body><?php include("../../menu.php")?>
<div class="bodyline">
	<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" name="record" onSubmit="return validateForm(this);">
	<div style="float:right;width:160px;">
		  <?php showSaveCancel(1); ?>
	</div>
	<div style="margin-right:160px;">
		<h1><?php echo $pageTitle ?></h1>
	</div>
	
	<fieldset style="clear:both;float:right;width:150px;">
		<legend>attributes</legend>
		<label for="id">
			id<br />
			<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="22" maxlength="5" readonly="true" class="uneditable" style="">
		</label>
		<label for="webenabled" style="text-align:center;margin-top:15px;"><?PHP field_checkbox("webenabled",$therecord["webenabled"])?>web enabled</label>
		<label for="webdisplayname" style="margin-top:5px;">
			web display name<br />
			<input id="webdisplayname" name="webdisplayname" type="text" value="<?php echo htmlQuotes($therecord["webdisplayname"])?>" size="22" maxlength="64" style="">
		</label>
	</fieldset>
	<div style="margin:0px;margin-right:155px;padding:0px;">
		<fieldset>
			<legend><label for="name">name</label></legend>
			<div style="padding-top:0px;">
				<?PHP field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"40","maxlength"=>"64","class"=>"important","style"=>"width:98%")); ?>
			</div>
		</fieldset>
		<fieldset>
			<legend><label for="description">description</label></legend>
			<div style="padding-top:0px;"><textarea name="description" cols="38" rows="4" id="description" style="width:98%"><?PHP echo $therecord["description"]?></textarea></div>
		</fieldset>
		</div>
	<?php include("../../include/createmodifiedby.php"); ?>
	</form>
</div>
<?php include("../../footer.php")?>
</body>
</html>