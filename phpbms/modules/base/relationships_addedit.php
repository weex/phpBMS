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

	include("include/relationships_addedit_include.php");
	
	$pageTitle="Relationship";
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("../../head.php")?>
<script language="JavaScript" src="../../common/javascript/fields.js"></script>
</head>
<body><?php include("../../menu.php")?>


<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onClick="return false;"></div>
<div class="bodyline">
	<div style="float:right;width:160px;">
		  <?php showSaveCancel(1); ?>
	</div>
	<h1 style="margin-right:162px;"><?php echo $pageTitle ?></h1>
	<fieldset style="clear:both;float:right;width:160px;padding-top:0px;margin-top:0px">
		<legend><label for="id">id</label></legend>
		<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:100%">				
	</fieldset>
	<fieldset style="padding-top:0px;">
		<legend><label for="name">name</label></legend>
		<?PHP field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"64","maxlength"=>"64","style"=>"")); ?>
	</fieldset>	
	<fieldset style="clear:both;">
		<legend>from</legend>		
		<label for="fromtableid">
			table<br />
			<?php displayTables("fromtableid",$therecord["fromtableid"]) ?>
		</label>
		<label for="fromfield">
			field<br />
			<?PHP field_text("fromfield",$therecord["fromfield"],1,"From field cannot be blank.","",Array("size"=>"32","maxlength"=>"64")); ?>
		</label>
	</fieldset>
	<fieldset>
		<legend>to</legend>
		<label for="totableid">
			table<br />
			<?php displayTables("totableid",$therecord["totableid"]) ?>
		</label>
		<label for="tofield">
			field<br />
			<?PHP field_text("tofield",$therecord["tofield"],1,"To field cannot be blank.","",Array("size"=>"32","maxlength"=>"64")); ?>
		</label>
		<label for="inherint" style="margin-top:10px">
			<?PHP field_checkbox("inherint",$therecord["inherint"])?>inherint relationsip
		</label>
		<div class=small>
			<em>check the inherint relationshop box if the "to table" has been defined
			with a join statement by default (see "query table" for the "to" tables table definition), and the join already establishes
			the link for this relationship.</em> 
		</div>
	</fieldset>
	<?php include("../../include/createmodifiedby.php"); ?>
</div>
<?php include("../../footer.php"); ?>
</form>
</body>
</html>