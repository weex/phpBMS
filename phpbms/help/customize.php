<?php 
	$loginNoKick=true;
	$loginNoDisplayError=true;
	require("../include/session.php");	
	$pageTitle="phpBMS Customization";
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css" />
<style>
li{margin-bottom:10px;}
</style>
<script language="JavaScript" src="<?php echo $_SESSION["app_path"]?>common/javascript/common.js" type="text/javascript" ></script>
<script language="javascript">
	function navTo(){
		var theselect=getObjectFromID("navselect");
		document.location=theselect.value;
	}
</script>
</head>

<body>
<div class="bodyline" style="width:700px;">
	<h1><?php echo $pageTitle?></h1>
	<div style="float:right">
		<select id="navselect">
			<option value="">choose...</option>
			<option value="<?php echo $_SESSION["app_path"]?>help/reference">Using phpBMS</option>
			<option value="http://www.kreotek.com/products/phpbms/tutorials">Tutorials</option>
			<option value="<?php echo $_SESSION["app_path"]?>help/customize.php">Customizing phpBMS</option>
			<option value="<?php echo $_SESSION["app_path"]?>help/shortcuts.php">Keyboard Shortcuts</option>
			<option value="<?php echo $_SESSION["app_path"]?>info.php">About phpBMS</option>
		</select>
		<input type="button" class="Buttons" value="go" onClick="navTo()"/>
	</div>
	<h2>For Managers and Administrators</h2>
	<div>
		Why would you dictate your business processes based on the software you use? Shouldn't your business software conform 
		to the way <strong>you</strong> do business? Every business is different. phpBMS was designed specifically to be easily 
		customized to meet your company's specific needs.
	</div>
	<div>
		<a href="http://www.kreotek.com">Kreotek</a>, the developers of phpBMS, can make the necassary customizations you need
		to conform phpBMS to your exact business specifications.  Common customizations include:
		<ul>
			<li>Custom reports</li>
			<li>Custom fields or additional tables / data</li>
			<li>eCommerce integration </li>
			<li>Legacy data conversion / integration </li>
			<li>Custom imports/export</li>
		</ul>
	</div>
	<h2>For Developers</h2>
	<div>phpBMNS is based on the popular PHP+MySQL environment.  We have organized the program into modules to allow for easy customization without
	necessarily locking you into a single version of the program.  In addtion, many of the common functions have abstracted so that changes 
	can be made with no code at all.</div>
	<h3>Customization Tips</h3>
	<div>
		So your going to make some modifications to phpBMS and want to make sure to do it correctly so that you minimize upgrade woes int the future? Here are some steps to help you do this correctly. These customizations assume you will not be modifying files outside the modules folder (the core)
			<ol class="small">
			<li>
				Create a new custom module record: Currently module records cannot be input from the phpBMS interface.  You will need to inject this
				record through the MySQL command line or through a nice program like <a href="http://mysql.com">MySQL Control Center</a>
			</li>
			<li>
				Create the module sub-directory underneath the modules folder.  Also add the following sub-folders inside the folder:<br>
				include<br>
				report<br>
				javascript<br>
			install</li>
			<li>
				Create a text file named version.txt file in the install folder. It should contain the numerical value of the version you are programming</li>
		    <li> Make copies of all the files you are going to modify from the existing module to your module. Be sure to copy over any included or required support files as well that exist in the modules folder. <strong><br>
		    	<br>
		    	Note:</strong>You may end up copying and modifying many related files over.</li>
		    <li>Make your modifications.. Creative ways to edit in your changes are to put a distinguishing comment marking out any custom code, or even better, move the modifications to an include, or series of included files.</li>
		    <li>Modify the menu system and/or table definitions to point to your version of any modified files.</li>
		    <li>If your feeling really up to it, you can make install,update, and uninstall scripts in the install folder that will help others when using your module.</li>
        </ol>
	        <p>By following these steps, future updates to phpBMS can be re-integrated with your custom code more easily. </p>
	</div>
	<h3>Need Further Assistance?</h3>
	<div>
		Are you trying to modify phpBMS and have some questions about the code or functionality? Try the sourceforge <a href="http://sourceforge.net/forum/?group_id=107155a">forums</a> or contact kretoek via <a href="mailto:support@kreotek.com">e-mail</a>.  If you need priority support, consider a 
		<a href="http://kreotek.com/products/phpbms/services.html#developersupport">kreotek developer support contract</a>.  </div>
	
</div>
</body>
</html>