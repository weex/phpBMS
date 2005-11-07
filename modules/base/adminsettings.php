<?php 
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
		<div class="small">
			<em>Replace this with your comapny name + BMS (e.g. "Kreotek BMS")</em>
		</div>
		<label for="sencryption_seed">
			encryption seed<br />
			<?PHP field_text("sencryption_seed",$_SESSION["encryption_seed"],1,"Application name cannot be blank.","",Array("size"=>"32","maxlength"=>"128")); ?>				
		</label>
		<div class="small important">
			<em>Changing the encryption seed will void all current passwords. They will need to be reset immediately before logging out.</em>
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
		<legend>My<span style="text-transform:capitalize;">SQL</span></legend>
		<div class="small important">
			<em>Changing the MySQL settings may break the web application. Be very careful when changing these settings.</em>
		</div>
		<label for="smysql_server">
			server name <em>(usually localhost)</em><br />
			<?PHP field_text("smysql_server",$_SESSION["mysql_server"],1,"mySQL server name cannot be blank.","",Array("size"=>"32","maxlength"=>"128")); ?>
		</label>
		<label for="smysql_database">
			database name <em>(usually phpbms)</em><br />
			<?PHP field_text("smysql_database",$_SESSION["mysql_database"],1,"mySQL database name cannot be blank.","",Array("size"=>"32","maxlength"=>"128")); ?>
		</label>
		<label for="smysql_user">
			mysql username<br />
			<input id="smysql_user" name="smysql_user" type="text" size="32" maxlength="128" value="<?php echo htmlQuotes($_SESSION["mysql_user"])?>" />
		</label>
		<label for="smysql_userpass">
			mysql user password<br />
			<input id="smysql_userpass" name="smysql_userpass" type="text" size="32" maxlength="128" value="<?php echo $_SESSION["mysql_userpass"] ?>" />
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
			<img src="../../report/logo.png">
		</div>
		<label for="printedlogo">
			upload new logo file <em>(png format)</em><br />
			<input id="printedlogo" name="printedlogo" type="file" size="40" accept="image/x-png" />
		</label>
		<div class="small important">
			This graphic is used on some reports.  On PDF reports, it prints in a mximum of 1.75" x 1.75". PNG format.
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
		include "../".$modulerecord["name"]."/adminsettings.php";
	}//end while 
	?>
	<div class="box" style="clear:both;" align=right><input id="updateSettings1" name="command" type="submit" class="Buttons" value="Update Settings" /></div>
	</form>
</div>
<?php include("../../footer.php"); ?></body>
</html>