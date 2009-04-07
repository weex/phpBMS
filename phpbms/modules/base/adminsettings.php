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

require_once("../../include/session.php");
require_once("include/fields.php");

foreach($phpbms->modules as $module => $moduleinfo)
	if($module != "base"  && file_exists("../".$module."/adminsettings.php"))
		include("modules/".$module."/adminsettings.php");


require_once("modules/base/include/adminsettings_include.php");
$settings = new settings($db);

if(!hasRights(-100))
	goURL(APP_PATH."noaccess.php");

if (isset($_POST["command"]))
	$statusmessage = $settings->processForm($_POST);

$therecord = $settings->getSettings();

$pageTitle="Configuration";

$phpbms->cssIncludes[] = "pages/adminsettings.css";


$phpbms->jsIncludes[] = "modules/base/javascript/adminsettings.js";
foreach($phpbms->modules as $module => $moduleinfo)
	if($module != "base"  && file_exists("../".$module."/javascript/adminsettings.js"))
		$phpbms->jsIncludes[] = "modules/".$module."/javascript/adminsettings.js";

	//Form Elements
	//==============================================================
	$theform = new phpbmsForm();
	$theform->enctype="multiform/form-data";

	$theinput = new inputField("application_name",$therecord["application_name"],"application name",true);
	$theform->addField($theinput);

	$theinput = new inputField("record_limit",$therecord["record_limit"],"record display limit",true,"integer",5,3);
	$theform->addField($theinput);

	$theinput = new inputField("default_load_page",$therecord["default_load_page"],"default page",true);
	$theform->addField($theinput);

	$theinput = new inputField("encryption_seed",$therecord["encryption_seed"],"encryption_seed",true);
	$theinput->setAttribute("readonly","readonly");
	$theinput->setAttribute("class","uneditable");
	$theform->addField($theinput);

	$theinput = new inputField("currency_sym",$therecord["currency_sym"],"currency symbol",true,NULL,5,5);
	$theform->addField($theinput);

	$theinput = new inputField("currency_accuracy",$therecord["currency_accuracy"],"currency decimal points of accuracy",true,"integer",4,1);
	$theform->addField($theinput);

	$theinput = new inputField("decimal_symbol",$therecord["decimal_symbol"],"decimal symbol",true,NULL,4,1);
	$theform->addField($theinput);

	$theinput = new inputField("thousands_separator",$therecord["thousands_separator"],"thousands separator",true,NULL,4,1);
	$theform->addField($theinput);

	$theinput = new inputCheckbox("persistent_login",$therecord["persistent_login"],"persistent login");
	$theform->addField($theinput);

	$theinput = new inputField("login_refresh",$therecord["login_refresh"],"login refresh (minutes)",true,"integer",4,1);
	$theform->addField($theinput);


	$theform->extraModules = array();
	foreach($phpbms->modules as $module => $moduleinfo)
		if($module != "base" && class_exists($module."Display")){

			$class = $module."Display";
			$theform->extraModules[$module] = new $class();
			if(method_exists($theform->extraModules[$module],"getFields"))
				$additionalFields = $theform->extraModules[$module]->getFields($therecord);

			foreach($additionalFields as $field)
				$theform->addField($field);
		}


	$theform->jsMerge();
	//==============================================================
	//End Form Elements

	include("header.php");
?>
<div class="bodyline">
	<form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" enctype="multipart/form-data" name="record" onsubmit="return processForm(this);">

	<h1 id="h1Title"><span><?php echo $pageTitle ?></span></h1>

	<div id="phpbmsSplash" class="box">
		<div id="phpbmslogo">
			<a href="../../info.php"><span>phpBMS logo</span></a>
		</div>
		<div id="splashTitle" class="small">
			<h2>phpBMS</h2>
			Business Management Web Application
		</div>
	</div>

	<fieldset>
		<legend>general</legend>

		<p>
			<?php $theform->fields["application_name"]->display();?><br />
			<span class="notes">
				<strong>Example:</strong> Replace this with your comapny name + BMS (e.g. "Kreotek BMS").  Replacing
				the application name will reset the session cookie, and require you to log in again.
			</span>
		</p>

		<p>
			<?php $theform->fields["record_limit"]->display();?>
		</p>

		<p>
			<?php $theform->fields["default_load_page"]->display();?>
		</p>

		<p>
			<label for="stylesheet">web style set (stylesheets)</label><br />
			<select id="stylesheet" name="stylesheet">
				<?php $settings->displayStylesheets($therecord["stylesheet"]);?>
			</select>
		</p>
	</fieldset>
	<p class="updateButtonP"><input name="command" type="submit" class="Buttons" value="update settings" /></p>

	<fieldset>
		<legend>company</legend>
		<p>
			<label for="company_name">company name</label><br />
			<input id="company_name" name="company_name" type="text" size="40" maxlength="128" value="<?php echo htmlQuotes($therecord["company_name"]) ?>" />
		</p>

		<p>
			<label for="company_address">address</label><br />
			<input id="company_address" name="company_address" type="text" value="<?php echo htmlQuotes($therecord["company_address"]) ?>" size="40" maxlength="128" />
		</p>

		<p>
			<label for="company_csz">city, state/province and zip/postal code</label><br />
			<input id="company_csz" name="company_csz" type="text" size="40" maxlength="128"  value="<?php echo htmlQuotes($therecord["company_csz"]) ?>" />
		</p>

		<p>
			<label for="company_phone">phone number</label><br />
			<input id="company_phone" name="company_phone" type="text" value="<?php echo htmlQuotes($therecord["company_phone"]) ?>" size="40" maxlength="128" />
		</p>

		<?php if(isset($therecord["company_taxid"])){?>
                <p>
			<label for="company_taxid">company tax id</label><br />
			<input id="company_taxid" name="company_taxid" type="text" value="<?php echo htmlQuotes($therecord["company_taxid"]) ?>" size="40" maxlength="128" />
		</p>
                <?php }//endif - tax id?>

		<div class="fauxP">
			print logo
			<div id="graphicHolder"><img alt="logo" src="<?php echo APP_PATH?>dbgraphic.php?t=files&amp;f=file&amp;mf=type&amp;r=1" /></div>
		</div>

		<p>
			<label for="printedlogo">upload new logo file</label> <span class="notes">(PNG or JPEG format)</span><br />
			<input id="printedlogo" name="printedlogo" type="file" size="64" /><br />
		</p>

		<p class="notes">
			<strong>Note:</strong> This graphic is used on some reports. <br />
			On PDF reports, phpBMS prints the logo at maximum dimensions of 1.75" x 1.75".<br />
			If you are uploading a PNG, <strong>it must be an 8-bit (256 color) non-interlaced PNG without transparency or alpha channels/layers.</strong>.
		</p>

	</fieldset>
	<p class="updateButtonP"><input name="command" type="submit" class="Buttons" value="update settings" /></p>

	<fieldset>
		<legend>Localization</legend>
		<p>
			<label for="phone_format">phone format</label><br />
			<select id="phone_format" name="phone_format">
				<option value="US - Strict" <?php if($therecord["phone_format"] == "US - Strict")  echo "selected=\"selected\"";?>>US - Strict</option>
				<option value="US - Loose" <?php if($therecord["phone_format"] == "US - Loose")  echo "selected=\"selected\"";?>>US - Loose</option>
				<option value="UK - Loose" <?php if($therecord["phone_format"] == "UK - Loose")  echo "selected=\"selected\"";?>>UK - Loose</option>
				<option value="International" <?php if($therecord["phone_format"] == "International")  echo "selected=\"selected\"";?>>International</option>
				<option value="No Verification" <?php if($therecord["phone_format"] == "No Verification")  echo "selected=\"selected\"";?>>No Verification</option>
			</select>
		</p>
		<p>
			<label for="date_format">date format</label><br />
			<select id="date_format" name="date_format">
				<option value="SQL" <?php if($therecord["date_format"] == "SQL")  echo "selected=\"selected\"";?>>SQL (<?php echo dateToString(mktime() ,"SQL")?>)</option>
				<option value="English, US" <?php if($therecord["date_format"] == "English, US")  echo "selected=\"selected\"";?>>English, US (<?php echo dateToString(mktime(),"English, US")?>)</option>
				<option value="English, UK" <?php if($therecord["date_format"] == "English, UK")  echo "selected=\"selected\"";?>>English, UK (<?php echo dateToString(mktime(),"English, UK")?>)</option>
				<option value="Dutch, NL" <?php if($therecord["date_format"] == "Dutch, NL")  echo "selected=\"selected\"";?>>Dutch, NL (<?php echo dateToString(mktime(),"Dutch, NL")?>)</option>
			</select>
		</p>
		<p>
			<label for="time_format">time format</label><br />
			<select id="time_format" name="time_format">
				<option value="24 Hour" <?php if($therecord["time_format"]=="24 Hour")  echo "selected=\"selected\"";?>>24 Hour (<?php echo timeToString(mktime() ,"24 Hour")?>)</option>
				<option value="12 Hour" <?php if($therecord["time_format"]=="12 Hour")  echo "selected=\"selected\"";?>>12 Hour (<?php echo timeToString(mktime(),"12 Hour")?>)</option>
			</select>
		</p>
		<p>&nbsp;</p>
		<p>
			<?php $theform->fields["currency_sym"]->display(); ?>
		</p>
		<p>
			<?php $theform->fields["currency_accuracy"]->display(); ?>
		</p>
		<p>
			<?php $theform->fields["decimal_symbol"]->display(); ?>
		</p>
		<p>
			<?php $theform->fields["thousands_separator"]->display(); ?>
		</p>
	</fieldset>
	<p class="updateButtonP"><input name="command" type="submit" class="Buttons" value="update settings" /></p>

	<fieldset>
		<legend>encryption seed</legend>
		<p class="notes"><br />
			<strong>Note:</strong>
			The encryption seed is used to encrypt all passwords before storing them in the database.<br />
			Changing the encryption seed will void all current passwords. By entering your admin password,<br />
			your password will be reencrypted with the new seed.  All other users passwords will be voided<br />
			and need to be reentered.
		</p>

		<p>
			<input type="checkbox" value="1" name="changeseed" id="changeseed" onchange="toggleEncryptionEdit(this)" class="radiochecks"/><label for="changeseed">change seed</label>
		</p>

		<p>
			<?php $theform->fields["encryption_seed"]->display();?>
		</p>

		<p>
			<label for="currentpassword">current password</label><br />
			<input type="password" name="currentpassword" id="currentpassword" size="32" readonly="readonly" class="uneditable"/>
		</p>

	</fieldset>
	<p class="updateButtonP">
		<input type="hidden" id="doencryptionupdate" name="doencryptionupdate"/>
		<input type="submit" id="updateSettings3" name="command" class="Buttons" value="update encryption seed" disabled="disabled" onclick="setEncryptionUpdate()"/>
	</p>

	<fieldset>
		<legend>Security / Log in</legend>
		<p><?php $theform->fields["persistent_login"]->display(); ?></p>
		<p><?php $theform->fields["login_refresh"]->display(); ?></p>
		<p class="notes"><strong>Note:</strong> persistent login will keep you logged in, even when idle.  The refresh
			defines how often (in minutes) phpbms will send a logged in notice.
		</p>
		<p class="notes"><strong>Note:</strong> Does not work with Microsoft Internet Explorer versions less than 7.
		</p>
	</fieldset>
	<p class="updateButtonP"><input name="command" type="submit" class="Buttons" value="update settings" /></p>

	<?php
	foreach($theform->extraModules as $module)
		if(method_exists($module,"display")){
			$module->display($theform,$therecord);
		?>
	<?php }//end for ?>
	</form>
</div>
<?php include("footer.php"); ?>
