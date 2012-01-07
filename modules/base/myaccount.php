<?php
/*
 $Rev: 186 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-02-16 11:59:50 -0700 (Fri, 16 Feb 2007) $
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
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

require_once("../../include/fields.php");
require_once("include/myaccount.php");

$user = new myAccount($db, $_SESSION["userinfo"]["uuid"]);

if(isset($_POST["command"])){

    switch($_POST["command"]){

        case "Change Password":

            if($_POST["newPass"] === $_POST["confirmPass"])
                $statusmessage = $user->changePassword($_POST["curPass"], $_POST["newPass"]);
            else
                $statusmessage = "New password not confirmed";
            break;

        case "Update Contact":

            $statusmessage = $user->update($_POST);
            break;
            
        case "Update Email":

            $statusmessage = $user->updateEmail($_POST);
            break;
            
    }//endswitch

}//endif

$pageTitle="My Account";

	$phpbms->cssIncludes[] = "pages/myaccount.css";
	$phpbms->jsIncludes[] = "modules/base/javascript/myaccount.js";

		//Form Elements
		//==============================================================
        	$theform = new phpbmsForm();

        	$theinput = new inputField("phone",$_SESSION["userinfo"]["phone"],"phone/extension",false,"phone",32,64);
        	$theform->addField($theinput);
        
        	$theinput = new inputField("email",$_SESSION["userinfo"]["email"],"e-mail address",false,"email",32,64);
        	$theinput->setAttribute("title","Enter the email address to send site mail from. Will also be used as part of your contact details.");
        	$theform->addField($theinput);
        
        	$theinput = new inputField("sendmail",$_SESSION["userinfo"]["sendmail"],"sendmail path",false,null,32,255);
        	$theinput->setAttribute("title","Enter the path to the sendmail program directory on the host server. Defaults to: '/usr/sbin/sendmail'");
        	$theform->addField($theinput);

       		$theinput = new inputField("smtphost",$_SESSION["userinfo"]["smtphost"],"SMTP host",false,null,32,255);
        	$theinput->setAttribute("title","Enter the name of the SMTP host. Defaults to: 'localhost'");
        	$theform->addField($theinput);

        	$theinput = new inputField("smtpport",$_SESSION["userinfo"]["smtpport"],"SMTP port",false,"integer",10,10);
        	$theinput->setAttribute("title","Enter the port number of your SMTP server. Use 25 for most unsecure servers and 465 for most secure servers. Defaults to: 25");
        	$theform->addField($theinput);
        
        	$theinput = new inputField("smtpuser",$_SESSION["userinfo"]["smtpuser"],"SMTP username",false,NULL,32,255);
        	$theinput->setAttribute("title","Enter the username for access to the SMTP host.");
        	$theform->addField($theinput);

        	$theinput = new inputField("smtppass",$_SESSION["userinfo"]["smtppass"],"SMTP password",false,"password",32,255);
        	$theinput->setAttribute("title","Enter the password for the SMTP host.");
        	$theform->addField($theinput);

        	$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");
?><div class="bodyline">
	<form action="<?php echo htmlentities($_SERVER["PHP_SELF"])?>" method="post" name="record" id="record" onsubmit="return false">
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
	</fieldset>
	<p>
		<button type="button" class="Buttons" onclick="changePass()">Change Password</button>
	</p>

	<fieldset>
		<legend>Contact Information</legend>

			<p><?php $theform->showField("email")?></p>

			<p><?php $theform->showField("phone")?></p>

	</fieldset>
	<p><button type="button" class="Buttons" onclick="changeContact()">Update Contact Information</button></p>

	<fieldset>
		<legend>Access / Assigned Roles</legend>
		<ul>
		<?php
			if($_SESSION["userinfo"]["admin"]) {?><li><strong>Administrator</strong></li><?php }
			$user->displayRoles();
		?></ul>
	</fieldset>
	</form>
</div>

<?php include("footer.php"); ?>
