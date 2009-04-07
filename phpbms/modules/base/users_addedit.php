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
	include("include/tables.php");
	include("include/fields.php");
	include("include/users.php");

	$thetable = new users($db,9);
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle="User";

	$phpbms->cssIncludes[] = "pages/users.css";
	$phpbms->jsIncludes[] = "modules/base/javascript/users.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();
		$theform->onsubmit="return submitForm(this);";

		$theinput = new inputCheckbox("admin",$therecord["admin"],"administrator");
		$theform->addField($theinput);

		$theinput = new inputCheckbox("revoked",$therecord["revoked"],"access revoked");
		$theform->addField($theinput);

		$theinput = new inputCheckbox("portalaccess",$therecord["portalaccess"],"portal access");
		$theform->addField($theinput);


		$theinput = new inputField("firstname",$therecord["firstname"],"first name",true,NULL,32,64);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputField("lastname",$therecord["lastname"],"last name",false,NULL,32,64);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputField("login",$therecord["login"],"log in name",true,NULL,32,64);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputField("email",$therecord["email"],"e-mail address",false,"email",32,64);
		$theform->addField($theinput);

		$theinput = new inputField("phone",$therecord["phone"],"phone/extension",false,"phone",32,64);
		$theform->addField($theinput);

		$theinput = new inputField("lastip", $therecord["lastip"], "last log in IP");
		$theinput->setAttribute("readonly", "readonly");
		$theinput->setAttribute("class", "uneditable");
		$theform->addField($theinput);

		$theinput = new inputChoiceList($db,"department",$therecord["department"],"department");
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");

?><div class="bodyline">
	<?php $theform->startForm($pageTitle)?>
	<fieldset id="fsAttributes">
		<legend>attributes</legend>

		<p>
			<label for="id">id</label><br />
			<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="readonly" class="uneditable"/>
		</p>

		<p><?php $theform->showField("admin");?></p>

		<p><?php $theform->showField("revoked");?></p>

		<p><?php $theform->showField("portalaccess");?></p>

		<p class="notes">
			user accounts marked as portal access cannot login to phpBMS, but are used by external applications
			when creating/modifiying information from outside the application for recording purposes.
		</p>
	</fieldset>

	<div id="leftSideDiv">
		<fieldset id="fsName">
			<legend>name</legend>

			<p id="firstnameP"><?php $theform->showField("firstname");?></p>

			<p><?php $theform->showField("lastname");?></p>

		</fieldset>

		<fieldset>
			<legend>log in</legend>

			<p><?php $theform->showField("login");?></p>

			<p>
				<label for="lastlogin" >last log in</label><br />
				<input id="lastlogin" name="lastlogin" type="text" value="<?php echo formatFromSQLDateTime($therecord["lastlogin"]); ?>" size="32" maxlength="64" readonly="readonly" class="uneditable"  />
			</p>

			<p><?php $theform->showField("lastip"); ?></p>

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

			<p><?php $theform->showField("email");?></p>

			<p><?php $theform->showField("phone");?></p>

			<p><?php $theform->showField("department");?></p>

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
				<select id="assignedroles" size="10" multiple="multiple">
					<?php $thetable->displayRoles($therecord["id"],"assigned")?>
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
				<select id="availableroles" size="10" multiple="multiple">
					<?php $thetable->displayRoles($therecord["id"],"available")?>
				</select>
			</div>
			</div>
		</fieldset>
		<?php }?>
	</div>

	<?php
		$theform->showCreateModify($phpbms,$therecord);
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>