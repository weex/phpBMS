<?php 
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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

	include("include/modules_view_include.php");
	
	$pageTitle="Installed Modules";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/modules.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>

<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" name="record" onSubmit="return validateForm(this);">			
<div class="bodyline">
	<h1 id="topTitle"><span><?php echo $pageTitle ?></span></h1>	
	
		<fieldset id="fsAttributes">
			<legend>attributes</legend>
			<p>
				<label for="id">id</label><br />
				<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable"/>			
			</p>
			<p>
				<label for="version">version</label><br />
				<input id="version" name="version" type="text" value="<?php echo $therecord["version"]; ?>" size="8" maxlength="8" readonly="true" class="uneditable" />
			</p>
		</fieldset>			

		<div id="leftSideDiv">
			<fieldset>
				<legend>name / folder</legend>
				<p>
					<label for="displayname">name</label><br />
					<input id="displayname" name="displayname" type="text" value="<?php echo htmlQuotes($therecord["displayname"]); ?>" size="45" maxlength="128" readonly="true" class="uneditable" />
				</p>
				
				<p>
					<label for="name">folder name/location</label><br />
					<input id="name" name="name" type="text" value="<?php echo htmlQuotes($therecord["name"]); ?>" size="64" maxlength="128" readonly="true" class="uneditable" />
				</p>				
			</fieldset>
		</div>
		<fieldset id="fsDescription">
			<legend><label for="description">description</label></legend>
			<p>
				<br />
				<textarea id="description" name="description" rows=7 cols="56" readonly="readonly" class="uneditable"><?php echo htmlQuotes($therecord["description"])?></textarea>
			</p>
		</fieldset>
		
		<p class="box" align="right">
			<input name="cancelclick" type="hidden" value="0" />
			<input name="command" id="cancel" type="submit" value="cancel" class="Buttons" onclick="this.form.cancelclick.value=true;" />
		</p>
</div>
</form>
</body>
</html>