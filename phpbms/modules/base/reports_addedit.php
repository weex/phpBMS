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

	include("include/reports_addedit_include.php");

	$pageTitle="Report";

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("../../head.php")?>
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/reports.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onClick="return false;" /></div>
<div class="bodyline">
	<div id="topButtons"><?php showSaveCancel(1); ?></div>
	<h1 id="topTitle"><span><?php echo $pageTitle ?></span></h1>

	<fieldset id="fsAttributes">
		<legend>Attributes</legend>
		<p>
			<label for="id">id</label><br />
			<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable"/>		
		</p>
		<p>
			<label for="tabledefid">report table</label><br />
			<?php displayTables("tabledefid",$therecord["tabledefid"]);?><br />
			<span class="notes">Note: Use the global option to associate the report with every table in the system.</span>
		</p>
		
		<p>
			<label for="displayorder">
			display order</label><br />
			<?php field_text("displayorder",$therecord["displayorder"],0,"","integer",Array("size"=>"10","maxlength"=>"10","tabindex"=>"25")); ?><br />
			<span class="notes">Lower numbers are displayed first.  Reports with the same order are grouped together.</span>
		</p>
		<p>
			<label for="accesslevel" >access<br /></label>
			<?php basic_choicelist("accesslevel",$therecord["accesslevel"],array(array("value"=>"-10","name"=>"portal access only"),array("value"=>"10","name"=>"basic user (shipping)"),array("value"=>"20","name"=>"Power User (sales)"),array("value"=>"30","name"=>"Manager (sales manager)"),array("value"=>"50","name"=>"Upper Manager"),array("value"=>"90","name"=>"Administrator")),Array("class"=>"important","tabindex"=>"30"));?>
		</p>
	</fieldset>
	<div id="leftSideDiv">
		<fieldset>
			<legend>details</legend>
			<p>
				<label for="name" class="important">name</label><br />
				<?php field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"32","maxlength"=>"64","style"=>"","class"=>"important","tabindex"=>"5")); ?>		
			</p>
			<p>
				<label for="type">type</label><br />
				<?php basic_choicelist("type",$therecord["type"],Array(Array("name"=>"report","value"=>"report"),Array("name"=>"PDF report","value"=>"PDF Report"),Array("name"=>"export","value"=>"export")),Array("tabindex"=>"15"));?>
			</p>
			<p>
				<label for="reportfile">report file</label><br />
				<?php field_text("reportfile",$therecord["reportfile"],1,"file name cannot be blank.","",Array("size"=>"64","maxlength"=>"128","tabindex"=>"10")); ?><br />
				<span class="notes">Note: file name should include path</span>
			</p>
			
			<p>
				<label for="description">description</label><br />
				<textarea id="description" name="description"  cols="61" rows="7" tabindex="35"><?php echo htmlQuotes($therecord["description"])?></textarea>
			</p>
		</fieldset>
	</div>
	<p>&nbsp;</p>
	<?php include("../../include/createmodifiedby.php"); ?>
</div>
<?php include("../../footer.php");?>
</form>
</body>
</html>