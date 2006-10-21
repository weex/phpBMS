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
	function showModules(){
		$thedir= @ opendir("../modules/");
		while($entry=readdir($thedir))
			if($entry!="base" and $entry!="." and $entry !=".." and is_dir("../modules/".$entry) and $entry!=".svn")
				echo "<option value=\"".$entry."\">".$entry."</option>";
		
	}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
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
<div align="center"><div class="bodyline" style="width:740px" align="left">
	<h1>phpBMS Installation Instructions</h1>
	<h2>Application Requirements</h2>
	<div class="box">
	<ul>
		<li>MySQL 3.23.58 or higher</li>
		<li>PHP 4.1.2 or higher</li>
		<li>Web Server (Tested using Apache and IIS.)</li>
	</ul>
	</div>
	


	<h2>Set Up The Database</h2>
	
	<div class="box">
	<h3>Step 1 - Set Database Connection Information</h3>
	<div style="margin-left:10px;">
		This step will require you to manually create the settings file. that is used by PHP to connect to your MySQL database. </div>

	
	
	<h4> Create the settings.php File</h4>
	<div style="margin-left:25px;">		
	Make a copy the file <strong>defaultsettings.php</strong> (located in the web application root) and name the new file <strong>settings.php</strong>. Next, modify the following prarameters:
	<ul class="small">
		<li><strong>mysql_server</strong>: the mysql server location (in most cases, this should be the same location as the web server, or &quot;localhost&quot;)<br>
			<div class="small" style=" color:#DF0005"><strong>Note:</strong> If your database server is different then your web server,  make sure you take any necessary security precautions.</div>
			</li>
		<li><strong>mysql_database</strong>: the name of the database to be used by phpBMS. If it has not been created yet, step two will create it if your mysql user has rights o create the database. <br>
			&nbsp;</li>
		<li><strong>mysql_user</strong>: the name of the user PHP will use to access the database.<br>
			&nbsp;</li>
		<li><strong>mysql_userpass</strong>:	the	password for the user that access the database.</li>
	</ul>
	</div>

	<div style="float:right;width:40%;padding-top:0">
		<h4 style="margin-left:0;margin-bottom:1px;">Results</h4>
		<div>
			<textarea name="results" id="testconnectionresults" style="width:98%;font-family:'Courier New', Courier, mono;font-size:11px;" rows="4"></textarea>
		</div>
	</div>
	
	<h4 style="margin-right:42%">Test the Database Connection</h4>
	<div style="margin-left:25px;margin-right:42%">
		Once the <strong>settings.php</strong> file has been created and the database connection information has been entered, test the connection.  If connection fails, check
		to make sure the <strong>settings.php</strong> is setup correctly.
	</div>
	<div style="margin-left:25px;margin-right:42%"><input type="button" value="Test Connection" class="Buttons" onClick="runCommand('testconnection')"><br>&nbsp;</div>
	</div>
	<div>&nbsp;</div>

	<div class="box">
	<div style="float:right;width:40%;padding-top:0">
		<h3 style="margin-left:0;margin-bottom:1px;">Results</h3>
		<div>
			<textarea name="results" id="createdatabaseresults" style="width:98%;font-family:'Courier New', Courier, mono;font-size:11px;" rows="8"></textarea>
		</div>
	</div>
	
	<h3 style="margin-right:42%">Step 2 - Create the Database</h3>
	<div style="margin-left:10px;margin-right:42%">
		<div>
			If you have already created the MySQL database, skip to step three.  If not, click the "Create Database" button.  
			This will attempt to create the database set above.
The	user that you specified in the settings.php file must have rights to create the database. </div>
		<div><input type="button" value="Create Database" class="Buttons" onClick="runCommand('createdatabase')"></div>
		<div class="small" style=" color:#DF0005"><strong>Note:</strong> Some ISPs limit the number of mySQL databases you are allowed, or control database creation through a web application, or on a request basis. If you have any questions, contact your ISP.</div>
		<div>&nbsp;</div>		
		<div>&nbsp;</div>
	</div>
	</div>	
		<div>&nbsp;</div>

	<div class="box" style="clear:right">
	<div style="float:right;width:40%;padding-top:0;">
		<h3 style="margin-left:0;margin-bottom:1px;">Results</h3>
		<div>
			<textarea name="results" id="populatedataresults" style="width:98%;font-family:'Courier New', Courier, mono;font-size:11px;" rows="12"></textarea>
		</div>
	</div>
	
	<h3 style="margin-right:42%">Step 3 - Create Default Information </h3>
	<div style="margin-left:10px;margin-right:42%">
		<div>
			Next, phpBMS needs to create the base level tables and populate the tables with the basic information that phpBMS needs to 
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
		<div class="small" style=" color:#DF0005"><strong>Note:</strong> Make sure to change the encryptions seed and password after the first successful login.</div>
		<div>&nbsp;</div>
	</div>
	</div>	
	
	
	<h2 style="clear:both;">Install Additional Modules </h2>
	<div class="box">
<div>
		The base phpBMS system contains <strong>only</strong> the bare framework, administration, and note/task/event capabilities. 
		Additional modules will add functionality such as client/prospect and product management, quote/order/invoice control, and mass e-mailing. The modules are installed separately in the next step. </div>	
	<div style="float:right;width:40%;padding-top:0;">
		<h3 style="margin-left:0;margin-bottom:1px;">Results</h3>
		<div>
			<textarea name="results" id="moduleresults" style="width:98%;font-family:'Courier New', Courier, mono;font-size:11px;" rows="10"></textarea>
		</div>
	</div>
	<h3 style="margin-right:42%;">Step 4 - Install Additional Modules</h3>
	<div style="margin-left:10px;margin-right:42%">
		<div>To install a module, highlight the module form the list and click the "Install Module" button.</div>
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

	<h2>Secure The Application</h2>
	<div class="box">
		phpBMS can contain sensitive information, log in information and sensitive credit card information that could be exposed to the internet insecurely.
		We recommend performing the following actions to secure phpBMS
:		    
			<ul>
			<li>
				<strong>Delete Installation/Disable Folders</strong> - Once the installation process has been completed for the base system and the BMS modules,
				Either delete the installation folders, or make them inaccessible by the web server and php.<br>
				&nbsp;
			</li>
			<li>
				<strong>Run phpBMS On a Secure Connection (SSL)</strong> - To make sure that usernames, passwords, credit card and potentially 
				other sensitive information are not passed in clear text over the internet, running phpBMS over a secure socket layer is highly recommended.  
				Even if phpBMS is not exposed to the Internet, and is setup on a local LAN, we recommend running it over SSL.
</li>
		    <li><strong>Restrict access to the settings.php file -</strong> Make sure that only php can read the settings.php file, and that php does not access to write to the file. </li>
			</ul>
	</div>
	<h2>Complete the Installation</h2>
	<div class="box">
		If all of the above actions completed successfully, phpBMS was installed successfully. Go to the log in screen, use the administrative user name password (above) and log in to finish the installation process. After logging in, you may want to go to the administration section, and set up the basic settings for your application as well as change the admininstrative username and password.
			<div><input type="button" id="login" name="login" value="Go to Log In Screen" class="Buttons" onClick="document.location='../'"></div>
	</div>
	
	
</div>
</div>
</body>
</html>
