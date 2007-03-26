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

	include("include/clientemailprojects_edit_include.php");
?><?php $pageTitle="Client E-mail Project"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/clientemailprojects.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>

<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onsubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onclick="return false;" /></div>
<div class="bodyline">
	<div id="topButtons">
		  <?php showSaveCancel(1); ?>
	</div>
	<h1 id="topTitle"><span><?php echo $pageTitle ?></span></h1>
	
	<fieldset id="fsID">
		<legend><label for="id">id</label></label></legend>
		<p>
			<br />
			<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="readonly" class="uneditable"/>
		</p>
	</fieldset>

	<div id="leftSideDiv">
		<fieldset>
			<legend><label for="name">name</label></legend>
			<p>
				<br />
				<?php fieldText("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"60","maxlength"=>"128")); ?>
			</p>
		</fieldset>
	</div>
	
	<fieldset id="fsUser">
		<legend><label for="username">user</label></legend>
		<p><br />
			<input id="username" name="username" type="text" value="<?php echo $username ?>" size="50" readonly="readonly" class="uneditable" />
		</p>
		<?php if($therecord["userid"]!=0) {?>
		<p>
			<input id="makeglobal" name="makeglobal" type="checkbox" class="radiochecks" value="1" /><label for="makeglobal">make global</label>
		</p>		
		<?php } ?>
	</fieldset>

	<fieldset>
		<legend>e-mail</legend>
		<p>
			<label for="from">from</label><br />
			<?php if(is_numeric($therecord["emailfrom"])) $therecord["emailfrom"]=getEmailInfo($therecord["emailfrom"]);?>
			<input id="from" name="from" value="<?php echo htmlQuotes($therecord["emailfrom"])?>" readonly="readonly" class="uneditable" size="60" />		
		</p>
		<p>
			<label for="to">to</label><br />
			<input id="to" name="to" value="<?php if($therecord["emailto"]=="selected" or $therecord["emailto"]=="all") echo htmlQuotes($therecord["emailto"]); else echo "saved search"?>" size="60" readonly="readonly" class="uneditable" />
			<?php if(is_numeric($therecord["emailto"]))	{?>			
			<br /><input id="to2" name="to2" value="<?php echo htmlQuotes(showSavedSearch($therecord["emailto"]))?>" readonly="readonly" class="uneditable" size="60"/>
			<?php } ?>		
		</p>
		
		<p>
			<label for="subject">subject</label><br />
			<input id="subject" name="subject" type="text" value="<?php echo $therecord["subject"]?>" size="32" readonly="readonly" class="uneditable" />		
		</p>
		<p>
			<textarea id="body" name="body" readonly="readonly" class="uneditable" cols="80" rows="40"><?php echo htmlQuotes($therecord["body"])?></textarea>
		</p>
	</fieldset>

	<div class="box" align="right">
		<?php showSaveCancel(2); ?>
		<input id="cancelclick" name="cancelclick" type="hidden" value="0" />
	</div>
</div>
<?php include("../../footer.php");?>
</form>

</body>
</html>