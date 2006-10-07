<?php 
/*
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

require_once("include/admin_functions.php");
require_once("include/adminsettings_include.php");
?>

<?PHP $pageTitle="Settings"?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js"></script>
<script language="JavaScript" src="../../common/javascript/choicelist.js"></script>
<script language="JavaScript" src="../../common/javascript/datepicker.js"></script>
<script language="JavaScript" src="../../common/javascript/timepicker.js"></script>
</head>
<body><?php include("../../menu.php")?>
<?php admin_tabs("Settings");?><div class="bodyline">
	<form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" enctype="multipart/form-data" name="record" onSubmit="return validateForm(this);">

	<div style="float:right;width:150px;" align="right">
			<input id="updateSettings1" name="command" type="submit" class="Buttons" value="Update Settings">	
	</div>
	<h1 style="margin-right:155px;"><?php echo $pageTitle ?></h1>
	<div class="box" style="background-color:white;height:40px;" align="right">
		<div style="float:right;padding-top:9px;padding-right:14px;"><a href="../../info.php"><img src="../../common/image/logo.png" width="85" height="22" border="0" /></a></div>
		<div align="left">
			<div style="padding:0px;" class="large important">phpBMS</div>
			<div style="padding:0px;" class="small">Business Management Web Application</div>						
		</div>
	</div>
	<fieldset>
		<legend>general</legend>
		<label for="sapplication_name">
			application name<br/>
			<?PHP field_text("sapplication_name",$_SESSION["application_name"],1,"Application name cannot be blank.","",Array("size"=>"32","maxlength"=>"128")); ?>				
		</label>
		<div class="notes">
			<strong>Example:</strong> Replace this with your comapny name + BMS (e.g. "Kreotek BMS")
		</div>
		<label for="sencryption_seed">
			encryption seed<br />
			<?PHP field_text("sencryption_seed",$_SESSION["encryption_seed"],1,"Application name cannot be blank.","",Array("size"=>"32","maxlength"=>"128")); ?>				
		</label>
		<div class="notes">
			<strong>Note:</strong>
			Changing the encryption seed will void all current passwords. They will need to be reset immediately before logging out.
		</div>
		<label for="srecord_limit">
			record display limit<br />
			<?PHP field_text("srecord_limit",$_SESSION["record_limit"],1,"Record limit cannot be blank and must be a valid integer.","integer",Array("size"=>"9","maxlength"=>"3")); ?>
		</label>
		<label for="sdefault_load_page">
			default load page<br />
			<?PHP field_text("sdefault_load_page",$_SESSION["default_load_page"],1,"Load page cannot be blank.","",Array("size"=>"32","maxlength"=>"128")); ?>
		</label>
	</fieldset>
		
	<fieldset>
		<legend>company</legend>
		<label for="scompany_name">
			company name<br />
			<input id="scompany_name" name="scompany_name" type="text" size="40" maxlength="128" value="<?php echo htmlQuotes($_SESSION["company_name"]) ?>" />
		</label>
		<label for="scompany_address">
			address<br />
			<input id="scompany_address" name="scompany_address" type="text" value="<?php echo htmlQuotes($_SESSION["company_address"]) ?>" size="40" maxlength="128" />
		</label>
		<label for="scompany_csz">
			city, state/province and zip/postal code<br />
			<input id="scompany_csz" name="scompany_csz" type="text" size="40" maxlength="128"  value="<?php echo htmlQuotes($_SESSION["company_csz"]) ?>" />
		</label>
		<label for="scompany_phone">
			phone number<br />
			<input id="scompany_phone" name="scompany_phone" type="text" value="<?php echo htmlQuotes($_SESSION["company_phone"]) ?>" size="40" maxlength="128" />
		</label>
	</fieldset>
	
	<fieldset>
		<legend>Display / Print</legend>
		<div>Printed Logo</div>
		<div style="border:1px solid black; height:150px;width:400px;overflow:scroll;">
			<img src="<?php echo $_SESSION["app_path"]?>dbgraphic.php?t=files&f=file&mf=type&r=1">
		</div>
		<label for="printedlogo">
			upload new logo file <em>(png format)</em><br />
			<input id="printedlogo" name="printedlogo" type="file" size="40" accept="image/x-png" />
		</label>
		<div class="notes">
			<strong>Note:</strong> This graphic is used on some reports.  On PDF reports, it prints in a mximum of 1.75" x 1.75". PNG format.
			PNG must be gray scale on at most 8 bits (256 levels), indexed color, or true color (24 bits). PNG cannot 
			contain an alpha channel, or interlacing.
		</div>
		<label for="sstylesheet">
			style<br>
			<select id="sstylesheet" name="sstylesheet">
			<?php 
				$thedir="../../common/stylesheet";
				$thedir_stream=@opendir($thedir);
				
				while($entry=readdir($thedir_stream)){
					if ($entry!="." and  $entry!=".." and is_dir($thedir."/".$entry)) {
						echo "<option value=\"".$entry."\"";
							if($entry==$_SESSION["stylesheet"]) echo " selected ";
						echo ">".$entry."</option>";
					}
				}
					
			?>
			</select>
		</label>
	</fieldset>

	<?php 
	$querystatement="SELECT name FROM modules WHERE name!=\"base\" ORDER BY name";
	$modulequery=mysql_query($querystatement,$dblink);
	
	while($modulerecord=mysql_fetch_array($modulequery)){
		@ include "../".$modulerecord["name"]."/adminsettings.php";
	}//end while 
	?>
	<div class="box" style="clear:both;" align=right><input id="updateSettings1" name="command" type="submit" class="Buttons" value="Update Settings" /></div>
	</form>
</div>
<?php include("../../footer.php"); ?></body>
</html>