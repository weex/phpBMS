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
	include("include/users_addedit_include.php");
	
	$pageTitle="User";
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>

<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/users.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/choicelist.js" type="text/javascript"></script>
<script language="JavaScript" src="javascript/users.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return submitForm(this);"><div id="dontSubmit"><input type="submit" value=" " onClick="return false;" /></div>
<div class="bodyline">
	<div id="topButtons"><?php showSaveCancel(1); ?></div>
	<h1 id="topTitle"><span><?php echo $pageTitle ?></span></h1>
	
	<fieldset id="fsAttributes">
		<legend>attributes</legend>
		
		<p>
			<label for="id">id</label><br />
			<input name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable"/>		
		</p>

		<p><?php field_checkbox("admin",$therecord["admin"])?><label for="admin">administrator</label></p>
		

		<p><?php field_checkbox("revoked",$therecord["revoked"])?><label for="revoked">revoke access</label></p>		

		<p>
			<?php field_checkbox("portalaccess",$therecord["portalaccess"])?><label for="admin">portal access</label><br />
			<span class="notes">
				user accounts marked as portal access cannot login to phpBMS, but are used by external applications
				when creating/modifiying information from outside the application for recording purposes.
			</span>
		</p>
	</fieldset>
	
	<div id="leftSideDiv">
		<fieldset id="fsName">
			<legend>name</legend>
			<p id="firstnameP">
				<label for="firstname" class="important" >first name</label><br />
				<?php field_text("firstname",$therecord["firstname"],1,"First name cannot be blank.","",Array("size"=>"32","maxlength"=>"64","class"=>"important")); ?>			
			</p>
			<p>
				<label for="lastname" class="important">last name</label><br />
				<?php field_text("lastname",$therecord["lastname"],1,"Last name cannot be blank.","",Array("size"=>"32","maxlength"=>"64","class"=>"important")); ?>					
			</p>
		</fieldset>

		<fieldset>
			<legend>log in</legend>
			<p>
				<label for="login" class="important">name</label><br />
				<?php field_text("login",$therecord["login"],1,"Login cannot be blank.","",Array("size"=>"32","maxlength"=>"32","class"=>"important")); ?>
			</p>

			<p>
				<label for="lastlogin" >last log in</label><br />
				<input id="lastlogin" name="lastlogin" type="text" value="<?php echo formatFromSQLDate($therecord["lastlogin"]); ?>" size="32" maxlength="64" readonly="true" class="uneditable"  />			
			</p>

			<p>
				<label for="password">set new password</label><br />
				<input id="password" name="password" type="password" size="32" maxlength="32" />			
			</p>
			
			<p>
				<label for="password2">confirm new password</label><br />
				<input id="password2" name="password2" type="password" size="32" maxlength="32" />
			</p>
		</fieldset>

		<fieldset>
			<legend>contact / user information</legend>
			<p>
				<label for="email">e-mail address</label><br />
				<?php field_email("email",$therecord["email"],Array("size"=>"32","maxlength"=>"128")); ?>						
			</p>
			<p>
				<label for="phone">phone/extension</label><br />
				<input type="text" id="phone" name="phone" value="<?php echo htmlQuotes($therecord["phone"]) ?>" size="32" maxlength="32" />
			</p>
			<p>
			<label for="department">department</label><br />
				<?php choicelist("department",$therecord["department"],"department"); ?>			
			</p>
			<p>
				<label for="employeenumber">employee number</label><br />
				<input type="text" id="employeenumber" name="employeenumber" value="<?php echo htmlQuotes($therecord["employeenumber"]) ?>" size="32" maxlength="32" />			
			</p>
		</fieldset>
		<?php if($therecord["id"]){?>
		<fieldset>
			<legend>roles</legend>
			<input type="hidden" name="roleschanged" id="roleschanged" value="0" />
			<input type="hidden" name="newroles" id="newroles" value="" />
			<div class="fauxP">
			<div id="assignedrolesdiv">
				assigned roles<br />
				<select id="assignedroles" size="10" multiple>
					<?php displayRoles($therecord["id"],"assigned",$dblink)?>
				</select>
			</div>
			<div id="rolebuttonsdiv">
				<p>
					<button type="button" class="Buttons" onclick="moveRole('availableroles','assignedroles')">&lt; add role</button>
				</p>
				<p>
					<button type="button" class="Buttons" onclick="moveRole('assignedroles','availableroles')">remove role &gt;</button>
				</p>
			</div>
			<div id="availablerolesdiv">
				available roles<br />
				<select id="availableroles" size="10" multiple>
					<?php displayRoles($therecord["id"],"available",$dblink)?>
				</select>
			</div>
			</div>
		</fieldset>		
		<?php }?>
	</div>	

	<?php include("../../include/createmodifiedby.php"); ?>
</div>
<?php include("../../footer.php")?>
</form>
</body>
</html>