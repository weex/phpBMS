<?php 
include("../../include/session.php");
include("../../include/common_functions.php");

include("include/admin_functions.php");
include("include/adminsettings_include.php");
?>

<?PHP $pageTitle="Administration: General"?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
</head>
<body><?php include("../../menu.php")?>
<?php admin_tabs("General");?><div class="untabbedbox"><div>
	<form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" enctype="multipart/form-data">
	<div style="float:left;width:50%;padding-right:3px;">
		<h1><?php echo $pageTitle ?></h1>
		<h2>General Settings</h2>
		<div>
			application name<br>
			<input name="sapplication_name" type="text" size="32" maxlength="128"  value="<?php echo $_SESSION["application_name"] ?>">	
		</div>
		<div>
			encryption seed<br>
			<input type="text" name="sencryption_seed" size="32" maxlength="128"  value="<?php echo $_SESSION["encryption_seed"] ?>">
			<div class=small><strong>Note: Changing the encryption seed will void any non-blank passwords. They will need to be reset immediately.</strong></div>
		</div>
		<div>
			record limit<br>
			<input type="text" name="srecord_limit" size="6" maxlength="4" value="<?php echo $_SESSION["record_limit"] ?>">
		</div>
		<div>
			default load page<br>
			<input name="sdefault_load_page" type="text" size="32" maxlength="128" value="<?php echo $_SESSION["default_load_page"]?>">
		</div>


		<h2>MySQL Settings</h2>		
		<div class=small><strong><strong>Note: Changing the MySQL settings may break the web application. Be very careful when changing these settings.</strong></strong></div>
		<div>
			Server Name (usually localhost)<br>
			<input name="smysql_server" type="text" size="32" maxlength="128" value="<?php echo $_SESSION["mysql_server"] ?>">
		</div>
		<div>
			database name (usually phpbms)<br>
			<input name="smysql_database" type="text" size="32" maxlength="128" value="<?php echo $_SESSION["mysql_database"] ?>">
		</div>
		<div>
			mysql username<br>
			<input name="smysql_user" type="text" size="32" maxlength="128" value="<?php echo $_SESSION["mysql_user"] ?>">		
		</div>
		<div>
			mysql user password<br>
			<input name="smysql_userpass" type="text" size="32" maxlength="128" value="<?php echo $_SESSION["mysql_userpass"] ?>">
		</div>

	</div>
	<div style="margin-left:51%;border-left:2px #DDDDDD dotted;">
		<div align=right>
			<input name="command" type="submit" class="Buttons" value="Update Settings">
		</div>
		<h2>Company Information</h2>
		<div>
			company name<br>
			<input name="scompany_name" type="text" size="40" maxlength="128" value="<?php echo $_SESSION["company_name"] ?>">
		</div>
		<div>
			address<br>
			<input name="scompany_address" type="text" id="scompany_address" value="<?php echo $_SESSION["company_address"] ?>" size="40" maxlength="128">
		</div>
		<div>
			city, state/province and zip/postal code<br>
			<input name="scompany_csz" type="text" size="40" maxlength="128"  value="<?php echo $_SESSION["company_csz"] ?>">
		</div>
		<div>
			phone number<br>
			<input name="scompany_phone" type="text" id="scompany_phone"  value="<?php echo $_SESSION["company_phone"] ?>" size="40" maxlength="128">
		</div>

		<h2>Display/Print Settings</h2>		
		<div class=dottedline align=center>
		    <img src="../../report/logo.png" width="150"><br>
			<strong>printed logo</strong><br>&nbsp;
			<div align="left">
				upload new printed logo file<br>
				<input name="printedlogo" type="file" size="40" accept="image/x-png">
				<div class="small"><strong>Note: This graphic is used on PDF reports.  Prints at 1.75" x 1.75". PNG format.</strong> </div>
			</div>
		</div>
			
		<div>
			web application style sheet<br>
			<select name="sstylesheet">
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
			</div>

	</div>
	
	

	

		    <?php 
			$thequerystatement="SELECT name FROM modules WHERE name!=\"base\" ORDER BY name";
			$modulequery=mysql_query($thequerystatement,$dblink);
			
			while($modulerecord=mysql_fetch_array($modulequery)){
				echo "<DIV>&nbsp;</DIV>";
				include "../".$modulerecord["name"]."/adminsettings.php";
			}//end while 
			?>

	<div align=right style="padding:10px;"><input name="command" type="submit" class="Buttons" value="Update Settings"></div>
	</form>
</div></div>
</body>
</html>