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

	include("include/modules_view_include.php");
	
	$pageTitle="Installed Modules";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("../../head.php")?>

<script language="JavaScript" src="../../common/javascript/fields.js"></script>
</head>
<body><?php include("../../menu.php")?>

<div class="bodyline">
	<h1><?php echo $pageTitle ?></h1>
	<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" name="record" onSubmit="return validateForm(this);">			
		<div style="float:right;width:100px;padding:0px;">
			<fieldset style="margin-top:0px;padding-top:0px;">
				<legend>attributes</legend>
				<label for="id">
					id<br />
					<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:99%" />
				</label>
				<label for="version">
					version<br />
					<input id="version" name="version" type="text" value="<?php echo $therecord["version"]; ?>" size="8" maxlength="8" readonly="true" class="uneditable" style="width:99%" />
				</label>
			</fieldset>			
		</div>
		<div style="margin-right:105px;padding:0px;">
			<fieldset>
				<legend>name / folder</legend>
				<label for="displayname">
					name<br />
					<input id="displayname" name="displayname" type="text" value="<?php echo htmlQuotes($therecord["displayname"]); ?>" size="8" maxlength="128" readonly="true" class="uneditable" style="width:99%;" />
				</label>
				<label for="name">
					folder name <em>(location)</em><br>
					<input id="name" name="name" type="text" value="<?php echo htmlQuotes($therecord["name"]); ?>" size="8" maxlength="128" readonly="true" class="uneditable" style="width:99%;" />
				</label>					
			</fieldset>
		</div>
		<fieldset style="clear:both;">
			<legend><label for="description">description</label></legend>
			<div style="padding-top:0px;"><textarea id="description" name="description" rows=7 cols="56" readonly="readonly" class="uneditable" style="width:99%"><?php echo $therecord["description"]?></textarea></div>
		</fieldset>
		
		<div class="box" align="right">
			<input name="cancelclick" type="hidden" value="0" />
			<input name="command" id="cancel" type="submit" value="cancel" class="Buttons" style="width:80px;margin-right:10px;" onClick="this.form.cancelclick.value=true;" />
			</div>
	</form>
</div>
</body>
</html>