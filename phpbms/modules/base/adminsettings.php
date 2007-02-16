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

require_once("../../include/session.php");
require_once("../../include/common_functions.php");
require_once("../../include/fields.php");

require_once("include/adminsettings_include.php");
?>

<?php $pageTitle="Settings"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("../../head.php")?>
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/adminsettings.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/choicelist.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/datepicker.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/timepicker.js" type="text/javascript"></script>
<script language="JavaScript" src="javascript/adminsettings.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?><div class="bodyline">
	<form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" enctype="multipart/form-data" name="record" onSubmit="return processForm(this);">

	<div id="topButtons"><input id="updateSettings1" name="command" type="submit" class="Buttons" value="Update Settings" /></div>
	
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
			<label for="sapplication_name">application name</label><br/>
			<?php field_text("sapplication_name",$_SESSION["application_name"],1,"Application name cannot be blank.","",Array("size"=>"32","maxlength"=>"128")); ?><br />
			<span class="notes">
				<strong>Example:</strong> Replace this with your comapny name + BMS (e.g. "Kreotek BMS")
			</span>
		</p>

		<p>
			<label for="srecord_limit">record display limit</label><br />
			<?php field_text("srecord_limit",$_SESSION["record_limit"],1,"Record limit cannot be blank and must be a valid integer.","integer",Array("size"=>"9","maxlength"=>"3")); ?>
		</p>
		
		<p>
			<label for="sdefault_load_page">default page</label><br />
			<?php field_text("sdefault_load_page",$_SESSION["default_load_page"],1,"Load page cannot be blank.","",Array("size"=>"32","maxlength"=>"128")); ?>		
		</p>
	</fieldset>
	
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
			<label for="sencryption_seed">encryption seed</label><br />
			<?php field_text("sencryption_seed",$_SESSION["encryption_seed"],1,"Application name cannot be blank.","",Array("size"=>"32","maxlength"=>"128","readonly"=>"readonly","class"=>"uneditable")); ?>
		</p>

		<p>
			<label for="currentpassword">current password</label><br />
			<input type="password" name="currentpassword" id="currentpassword" size="32" readonly="readonly" class="uneditable"/>
		</p>

		<p>
			<input type="hidden" id="doencryptionupdate" name="doencryptionupdate" value=""/>
			<input type="submit" id="updateSettings3" name="command" class="Buttons" value="Update Encryption Seed" disabled="true" onclick="this.form['doencryptionupdate'].value=1"/>	
		</p>
	</fieldset>
		
	<fieldset>
		<legend>company</legend>
		<p>		
			<label for="scompany_name">company name</label><br />
			<input id="scompany_name" name="scompany_name" type="text" size="40" maxlength="128" value="<?php echo htmlQuotes($_SESSION["company_name"]) ?>" />
		</p>

		<p>
			<label for="scompany_address">address</label><br />
			<input id="scompany_address" name="scompany_address" type="text" value="<?php echo htmlQuotes($_SESSION["company_address"]) ?>" size="40" maxlength="128" />
		</p>
		
		<p>
			<label for="scompany_csz">city, state/province and zip/postal code</label><br />
			<input id="scompany_csz" name="scompany_csz" type="text" size="40" maxlength="128"  value="<?php echo htmlQuotes($_SESSION["company_csz"]) ?>" />
		</p>
		
		<p>
			<label for="scompany_phone">phone number</label><br />
			<input id="scompany_phone" name="scompany_phone" type="text" value="<?php echo htmlQuotes($_SESSION["company_phone"]) ?>" size="40" maxlength="128" />
		</p>
	</fieldset>
	
	<fieldset>
		<legend>Display / Print</legend>

		<div class="fauxP">
			<br />Printed Logo
			<div id="graphicHolder"><img alt="logo" src="<?php echo $_SESSION["app_path"]?>dbgraphic.php?t=files&amp;f=file&amp;mf=type&amp;r=1" /></div>
		</div>
		
		<p>
			<label for="printedlogo">upload new logo file</label><br /><span class="notes">(PNG ot JPEG format)</span>
			<input id="printedlogo" name="printedlogo" type="file" size="64" /><br />
			<span class="notes">
				<strong>Note:</strong> This graphic is used on some reports. <br />
				On PDF reports, it prints in a mximum of 1.75" x 1.75". PNG format.<br />
				If you are uploading a PNG, <strong>it must be an 8-bit (256 color) non-interlaced PNG</strong>.
			</span>
		</p>
		
		
		<p>
			<label for="sstylesheet">web style set (stylesheets)</label><br />
			<select id="sstylesheet" name="sstylesheet">
			<?php 
				$thedir="../../common/stylesheet";
				$thedir_stream=@opendir($thedir);
				
				while($entry=readdir($thedir_stream)){
					if ($entry!="." and  $entry!=".." and is_dir($thedir."/".$entry)) {
						echo "<option value=\"".$entry."\"";
							if($entry==$_SESSION["stylesheet"]) echo " selected=\selected\" ";
						echo ">".$entry."</option>";
					}
				}
					
			?>
			</select>		
		</p>
	</fieldset>
	<fieldset>
		<legend>Localization</legend>
		<p>
			<label for="">phone format</label><br />
			<select id="sphone_format" name="sphone_format">
				<option value="US - Strict" <?php if($_SESSION["phone_format"]=="US - Strict")  echo "selected=\selected\"";?>>US - Strict</option>
				<option value="US - Loose" <?php if($_SESSION["phone_format"]=="US - Loose")  echo "selected=\selected\"";?>>US - Loose</option>
			</select>
		</p>
	</fieldset>
	<?php 
	$querystatement="SELECT name FROM modules WHERE name!=\"base\" ORDER BY name";
	$modulequery=mysql_query($querystatement,$dblink);
	
	while($modulerecord=mysql_fetch_array($modulequery)){
		@ include "../".$modulerecord["name"]."/adminsettings.php";
	}//end while 
	?>
	<div class="box" id="footerbox">
		<input id="updateSettings1" name="command" type="submit" class="Buttons" value="Update Settings" />
	</div>
	</form>
</div>
<?php include("../../footer.php"); ?></body>
</html>