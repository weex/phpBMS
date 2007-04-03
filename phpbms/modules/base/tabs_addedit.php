<?php 
/*
 $Rev: 205 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-03-26 15:50:25 -0700 (Mon, 26 Mar 2007) $
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
	include("include/tabs_addedit_include.php");
	
	$pageTitle="Tabs";
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/tabs.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/choicelist.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onsubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onclick="return false;" /></div>
<div class="bodyline">
	<div id="topButtons">
		<?php showSaveCancel(1); ?>
	</div>
	<h1 id="h1Title"><span><?php echo $pageTitle ?></span></h1>
	
	<fieldset id="fsAttributes">
		<legend>attributes</legend>
		<p>
			<label for="id">id</label><br />
			<input id="id" name="id" type="text" value="<?php echo htmlQuotes($therecord["id"]); ?>" size="10" maxlength="10" readonly="readonly" class="uneditable" />
		</p>
		
		<p>
			<label for="displayorder">display order</label><br />
			 <?php fieldText("displayorder",$therecord["displayorder"],1,"Display order cannot be blank and must be a valid integer.","integer",Array("size"=>"9","maxlength"=>"3")); ?><br />
			 <span class="notes"><strong>Note:</strong> Lower numbers are displayed first.</span>
		</p>
		
		<p>
			<label for="roleid">access (role)</label><br />
			<?php fieldRolesList("roleid",$therecord["roleid"],$dblink)?>
		</p>
		<p>
			<?php fieldCheckbox("enableonnew",$therecord["enableonnew"],false)?><label for="enableonnew">enable on new record</label>
		</p>
	</fieldset>
	
	<div id="leftSideDiv">
		<fieldset>
			<legend>details</legend>
			<p>
				<label for="name" class="important">name</label><br />
				<?php fieldText("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"32","maxlength"=>"64","class"=>"important")); ?>			
			</p>
			<p>
				<label for="tabgroup">tab group</label><br />
				<?php fieldChoiceList("tabgroup",$therecord["tabgroup"],"tabgroups"); ?>
			</p>
				
			<p>
				<label for="location">location</label><br />
				<input id="location" name="location" value="<?php echo htmlQuotes($therecord["location"])?>" size="64" maxlength="128" /><br />
				<span class="notes">location should be relative to application root.</span>
			</p>

			<p>
				<label for="tooltip">tool tip</label><br />
				<input id="tooltip" name="tooltip" value="<?php echo htmlQuotes($therecord["tooltip"])?>" size="64" maxlength="128" />
			</p>
		</fieldset>
		<fieldset>
			<legend><label for="notificationsql">Notification SQL</label></legend>
			<span class="notes">SQL statement used to determine display of notification icon on tab.  SQL statment should return a single record, with field "theresult" - a number.  Use {{id]}} for id substitution.</span><br />
			<textarea id="notificationsql" name="notificationsql" cols="40" rows="5"><?php echo htmlQuotes($therecord["notificationsql"])?></textarea>
		</fieldset>
	</div>
	<?php include("../../include/createmodifiedby.php"); ?>
</div>
<?php include("../../footer.php"); ?>
</form>
</body>
</html>