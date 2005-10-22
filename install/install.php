<?php 
	function showModules(){
		$thedir= @ opendir("../modules/");
		while($entry=readdir($thedir))
			if($entry!="base" and $entry!="." and $entry !=".." and is_dir("../modules/".$entry))
				echo "<option value=\"".$entry."\">".$entry."</option>";
		
	}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Install phpBMS</title>
<link href="../common/stylesheet/mozilla/base.css" rel="stylesheet" type="text/css">
<style>
H1{ font-size:24px;}
H2{ font-size:20px;}
h3{ font-size:18px; border-bottom:1px solid #666666; padding:2px; margin-left:10px; color:#0B63A2;}
h4{ font-size:14px; border-bottom:1px solid #666666; padding:2px; margin-left:25px; color:#0B63A2;}
</style>
<script language="JavaScript" src="../common/javascript/common.js"></script>
<script language="javascript">	
	function runCommand(command){
		var theURL="installxml.php?command="+command;
		if(command=="updatesettings"){
			var mServer=getObjectFromID("mysqlserver");
			var mDatabase=getObjectFromID("mysqldb");
			var mUser=getObjectFromID("mysqluser");
			var mPassword=getObjectFromID("mysqluserpass");
			theURL+="&ms="+encodeURIComponent(mServer.value);
			theURL+="&mdb="+encodeURIComponent(mDatabase.value);
			theURL+="&mu="+encodeURIComponent(mUser.value);
			theURL+="&mup="+encodeURIComponent(mPassword.value);
		}
		var responseText= getObjectFromID(command+"results");
		loadXMLDoc(theURL,null,false);
		if(req.responseXML)
			response = req.responseXML.documentElement;
		else 
			alert(req.responseText);
		responseText.value+=response.firstChild.data+"\n";
	}
	
	function runModuleInstall(){
		var themodule=getObjectFromID("modules");
		var responseText= getObjectFromID("moduleresults");
		if(themodule.value=="")
			alert("First, select a Module");
		else {
			var theURL="../modules/"+themodule.value+"/install/install.php";
			loadXMLDoc(theURL,null,false);
			if(req.responseXML)
				response = req.responseXML.documentElement;
			else alert(req.responseText);
			responseText.value+=response.firstChild.data+"\n";
		}
	}
</script>
</head>

<body>
<div class="bodyline" style="padding:4px;">
	<h1>phpBMS Installation Instructions</h1>
	<h2>Application Requirements</h2>
	<ul>
		<li>MySQL 3.23.58 or higher</li>
		<li>PHP 4.1.2 or higher</li>
		<li>Web Server (Tested using Apache and IIS.)</li>
	</ul>
	


	<h2>Setting Up The Database</h2>
	
	<div class="box">
	<h3>Step 1 - Set Database Connection Information</h3>
	<div style="margin-left:10px;">
		There are two ways to provide the connection information phpBMS will need to interact with the MySQL database.
		Providing the information by filling in the fields below can be quicker, but if phpBMS is not being hosted securely, 
		the MySQL login information can be compromised.</div>

	<div style="float:right;width:40%;padding-top:0">
		<h4 style="margin-left:0;margin-bottom:1px;">Results</h4>
		<div>
			<textarea name="results" id="updatesettingsresults" style="width:100%;font-family:'Courier New', Courier, mono;font-size:11px;" rows="12"></textarea>
		</div>
	</div>
	<h4 style="margin-right:42%">A) Enter Connection Information</h4>
	
	<div style="margin-left:25px;margin-right:42%;">
		<div>Enter The mySQL connection information and then click the &quot;Create Settings&quot; button. This will create the settings file required to run phpBMS. It will also copy some other default files used throughout the program (logo picture used on reports, etc..)</div>
		<div><em style="color:red;">(insecure if not done through SSL)</em></div>
		<div>
			MySQL Server<br>
			<input type="text" name="mysqlserver" id="mysqlserver" size="32" value="localhost">
		</div>
		<div>
			MySQL Database<br>
			<input type="text" name="mysqldb" id="mysqldb" size="32" value="phpbms"><br>
			<em class=small>(If the database has not been created yet, it will be in step two.)</em>
		</div>
		<div>
			MySQL User<br>
			<input type="text" name="mysqluser" id="mysqluser" size="32" value="">
		</div>
		<div>
			MySQL Password<br>
			<input type="password" name="mysqluserpass" id="mysqluserpass" size="32" value="">
		</div>
		<div><input type="button" value="Create Settings" class="Buttons" onClick="runCommand('updatesettings')"></div>
	</div>
	
	<h4>B) Or, Manually Create the settings.php File</h4>
	<div style="margin-left:25px;">		
	Copy the file <strong>defaultsettings.php</strong> and create the file <strong>settings.php</strong>. Make sure to modify the MySQL settings in the <strong>settings.php</strong> file. You can also modify any other settings at this time but the four
	settings that are essential to connecting to the database are:
	<ul class="small">
		<li><strong>mysql_server</strong>: the mysql server location (in most cases, this should be the same location as the web server, or &quot;localhost &quot;)<br><br>
			<em style="color:red;">Note:  If your database server is different then your web server,  data security issues can occur.</em><br>
			&nbsp;
			</li>
		<li><strong>mysql_database</strong>: the name of the database to be used by phpBMS. If it has not been created yet, step two will create it.<br>
			&nbsp;</li>
		<li><strong>mysql_user</strong>: the name of the user PHP will use to access the database.<br>
			&nbsp;</li>
		<li><strong>mysql_userpass</strong>:	the	password for the user used	to access the database.</li>
	</ul>
	</div>

	<div style="float:right;width:40%;padding-top:0">
		<h4 style="margin-left:0;margin-bottom:1px;">Results</h4>
		<div>
			<textarea name="results" id="testconnectionresults" style="width:100%;font-family:'Courier New', Courier, mono;font-size:11px;" rows="4"></textarea>
		</div>
	</div>
	<h4 style="margin-right:42%">Test the Database Connection</h4>
	<div style="margin-left:25px;margin-right:42%">
		Once the database connection information has been entered, test the connection.  If connection fails, check
		to make sure the settings are correct.
	</div>
	<div style="margin-left:25px;margin-right:42%"><input type="button" value="Test Connection" class="Buttons" onClick="runCommand('testconnection')"><br>&nbsp;</div>
	</div>


	<div class="box">
	<div style="float:right;width:40%;padding-top:0">
		<h3 style="margin-left:0;margin-bottom:1px;">Results</h3>
		<div>
			<textarea name="results" id="createdatabaseresults" style="width:100%;font-family:'Courier New', Courier, mono;font-size:11px;" rows="10"></textarea>
		</div>
	</div>
	
	<h3 style="margin-right:42%">Step 2 - Create the Database</h3>
	<div style="margin-left:10px;margin-right:42%">
		<div>
			If you have already created the MySQL database, skip to step three.  If not, click the "Create Database" button.  
			This will attempt to create the database set above.
		</div>
		<div><input type="button" value="Create Database" class="Buttons" onClick="runCommand('createdatabase')"></div>
		<div class=small><strong>Note:</strong> Some ISPs limit the number of mySQL databases you are allowed, or control database creation through a web application, or on a request basis. If you have any questions, contact your ISP.</div>
		<div>&nbsp;</div>		
		<div>&nbsp;</div>
	</div>
	</div>	
	
	<div class="box" style="clear:right">
	<div style="float:right;width:40%;padding-top:0;">
		<h3 style="margin-left:0;margin-bottom:1px;">Results</h3>
		<div>
			<textarea name="results" id="populatedataresults" style="width:100%;font-family:'Courier New', Courier, mono;font-size:11px;" rows="15"></textarea>
		</div>
	</div>
	
	<h3 style="margin-right:42%">Step 3 - Create Base Tables and Populate Default Information </h3>
	<div style="margin-left:10px;margin-right:42%">
		<div>
			Next, phpBMS needs to create the base level tables and populate the tables with the basic information needed to 
			work.
		</div>
		<div><input type="button" value="Create Tables and Populate Default Information" class="Buttons" onClick="runCommand('populatedata')"></div>
		<div>
			Upon successful creation of the tables and population of default data phpBMS is almost ready to run.
			One of the records populated was the default administrator record.  This information is required for the first successful login:
		</div>
		<div style="padding-left:50px;" class="large">
			username: <strong>admin</strong><br>
			password: <strong>phpbms</strong>
		</div>
		<div><em style="color:red;"><strong>Note:</strong> Change the password after the first successful login.</em></div>
		<div>&nbsp;</div>
	</div>
	</div>	
	
	
	<h2 style="clear:both;">Installing Additional Modules </h2>
	<div>
		The base phpBMS system contains only the basic framework, administration and notes capabilities. 
		Client and product management, invoicing and mass emailing capabilities are contained in a separate
		BMS module.  Modules are stored in the "modules" folder. To install the BMS module, proceed to step 4.
	</div>
	
	<div class="box">
	<div style="float:right;width:40%;padding-top:0;">
		<h3 style="margin-left:0;margin-bottom:1px;">Results</h3>
		<div>
			<textarea name="results" id="moduleresults" style="width:100%;font-family:'Courier New', Courier, mono;font-size:11px;" rows="12"></textarea>
		</div>
	</div>
	<h3 style="margin-right:42%;">Step 4 - Install Additional Modules</h3>
	<div style="margin-left:10px;margin-right:42%">
		<div>To install a module, choose the module form the list and click the "Install Module" button.</div>
		<div>
			Select a module to install<br>
			<select size="5" id="modules" name="modules" style="width:200px;font-size:12px;" >
			<?php showModules()?>
			</select>
		</div>
		<div>
			<input type="button" id="installmodule" name="installmodule" value="Install Module" style="width:200px;" class="Buttons" onClick="runModuleInstall()">
		</div>			
		<div>&nbsp;</div>
	</div>
	</div>

	<h2>Securing The Application</h2>
	<div>
		phpBMS can contain sensitive information, log in information and sensitive credit card information that could be exposed to the internet insecurely.
		We recommend performing the following actions to secure phpBMS
		    <ul>
			<li>
				<strong>Delete Installation/Disable Folders</strong> - Once the installation process has been completed for the base system and the BMS modules,
				Either delete the installation folders, or make them inaccessible from the web server.<br>&nbsp;
			</li>
			<li>
				<strong>Run phpBMS On a Secure Connection (SSL)</strong> - To make sure that usernames, passwords, credit card and potentially 
				other sensitive information are not passed in clear text over the internet, running phpBMS over a secure socket layer is highly recommended.  
				Even if phpBMS is not exposed to the Internet, and is setup on a local LAN, we recommend running it over SSL.
			</li>
		</ul>
	</div>
	<h2>Completing The Installation</h2>
	<div>If phpBMS was installed successfully, go to the log in screen, use the administrative user name password to log in and complete the installation process</div>
	<div><input type="button" id="login" name="login" value="Go to Log In Screen" class="Buttons" onClick="document.location='../'"></div>
	
</div>
</body>
</html>
