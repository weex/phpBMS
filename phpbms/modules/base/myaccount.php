<?php 
/*
 $Rev: 186 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-02-16 11:59:50 -0700 (Fri, 16 Feb 2007) $
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

require_once("../../include/session.php");
require_once("../../include/common_functions.php");
require_once("../../include/fields.php");
require_once("include/myaccount_include.php");

$pageTitle="My Account"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/myaccount.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/choicelist.js" type="text/javascript"></script>
<script language="JavaScript" src="javascript/myaccount.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?><div class="bodyline">
	<form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" name="record" id="record" onsubmit="return false">
	<input type="hidden" id="command" name="command" value=""/>
	
	<h1><span><?php echo $pageTitle ?></span></h1>

	<fieldset>
		<legend>Name</legend>
		<p id="nameP"><?php echo htmlQuotes($_SESSION["userinfo"]["firstname"]." ".$_SESSION["userinfo"]["lastname"])?></p>
	</fieldset>
	
	<fieldset>
		<legend>Change Password</legend>
		<p>
			<label for="curPass">current password</label><br />
			<input type="password" id="curPass" name="curPass" maxlength="32"/>
		</p>
		
		<p>
			<label for="newPass">new password</label><br />
			<input type="password" id="newPass" name="newPass" maxlength="32"/>
		</p>
		<p>
			<label for="confirmPass">re-type new password</label><br />
			<input type="password" id="confirmPass" name="confirmPass" maxlength="32"/>
		</p>
		<p>
			<button type="button" class="Buttons" onclick="changePass()">Change Password</button>
		</p>
	</fieldset>
	
	<fieldset>
		<legend>Contact Information</legend>
			<p>
				<label for="email">e-mail address</label><br />
				<?php fieldEmail("email",$_SESSION["userinfo"]["email"],Array("size"=>"32","maxlength"=>"128")); ?>						
			</p>
			<p>
				<label for="phone">phone/extension</label><br />
				<input type="text" id="phone" name="phone" value="<?php echo htmlQuotes($_SESSION["userinfo"]["phone"]) ?>" size="32" maxlength="32" />
			</p>
			<p>
				<button type="button" class="Buttons" onclick="changeContact()">Update Contact Information</button>
			</p>
	</fieldset>
	
	<fieldset>
		<legend>Access / Assigned Roles</legend>
		<ul>
		<?php 
			if($_SESSION["userinfo"]["admin"]) {?><li><strong>Administrtor</strong></li><?php }
			displayRoles($_SESSION["userinfo"]["id"],$dblink)
		?></ul>
	</fieldset>
	</form>
</div>
<?php include("../../footer.php"); ?></body>
</html>