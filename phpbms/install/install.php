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
	function showModules(){
		$thedir= @ opendir("../modules/");
		while($entry=readdir($thedir))
			if($entry!="base" and $entry!="." and $entry !=".." and is_dir("../modules/".$entry) and $entry!=".svn")
				echo "<option value=\"".$entry."\">".$entry."</option>";
		
	}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Install phpBMS</title>
<link href="../common/stylesheet/mozilla/base.css" rel="stylesheet" type="text/css" />
<link href="../common/stylesheet/mozilla/pages/install.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../common/javascript/common.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">	
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
<div class="bodyline" id="container">
	<h1>phpBMS Installation</h1>
	<p>Welcome to phpBMS. This page will guide you through the step of installing phpBMS, it's database, and included BMS module onto your web server. If you have any problems, questions or need help, please visit the <a href="http://www.phpbms.org">phpBMS Project web site </a>.</p>
	<p>&nbsp;</p>
	<h1>Server  Requirements</h1>
	<ul><li>
			<p>MySQL 3.23.58 or higher</p>
		</li>
		<li>
			<p>PHP 4.1.2 or higher</p>
		</li>
		<li>
			<p>Web Server (Tested using Apache and IIS.)</p>
		</li>
    </ul>
	
	<p>&nbsp;</p>
	<h1>Browser Requirements</h1>
	<p>see the <a href="../requirements.php">requirements page</a>.</p>
	<p>&nbsp;</p>
	<h1>Set Up The Database</h1>
	
	<div class="box">
	<h2>Step 1 - Set Database Connection Information</h2>
	<p>This step will require you to manually create the settings file. that is used by PHP to connect to your MySQL database. </p>

	
	
	<h3> Create the settings.php File</h3>
	<p>Make a copy the file <strong>defaultsettings.php</strong> (located in the web application root) and name the new file <strong>settings.php</strong>.</p>
	<p> Next, modify the following parameters inside the settings.php file:</p>
	<ul class="small">
		<li><p>
			<strong>mysql_server</strong>: the MySQL server location <br />
			In most cases, this should be the same location as the web server, i.e. &quot;localhost&quot;
			</p>
			<p class="notes"><strong>Note:</strong> If your database server is different then your web server,  make sure you take any necessary security precautions to secure transmission between the two servers.</p>
			</li>
		<li>
			<p><strong>mysql_database</strong>: the name of the database to be used by phpBMS. <br />
				If the data base has not been created, You can use <a href="#step2">step two</a> to create a new database for phpBMS.</p>
		</li>
		<li>
			<p><strong>mysql_user</strong>: the name of the user PHP will use to access the database.</p>
		</li>
		<li>
			<p><strong>mysql_userpass</strong>:	the	password for the user that access the database.</p>
		</li>
		<li>
			<p><strong>mysql_pconnect</strong>:	specify whether php should use the mysql_pconnect or mysql_connect command. Some web hosting providers do not allow mysql_pconnect.</p>
		</li>
	</ul>

	
	<h3>Test the Database Connection</h3>
	<p>
		Once the <strong>settings.php</strong> file has been created and the database connection information has been entered, test the connection.  If connection fails, check
		to make sure the <strong>settings.php</strong> is setup correctly.
	</p>

		<p><input type="button" value="Test Connection" class="Buttons" onclick="runCommand('testconnection')" /></p>

		<h3>Connection Test Results</h3>
		<p>
			<textarea name="results" id="testconnectionresults" cols="40" rows="4" class="results"></textarea>
		</p>

	</div>
	<div>&nbsp;</div>




	<div class="box">	
		<h2><a name="step2"></a>Step 2 - Create the Database</h2>
		<p>
		If you have already created the MySQL database that phpBMS will use, you can 
		skip to <a href="#step3">step three</a>.  
		</p>
		<p>If you have not created the database, phpBMS can attempt to create the database for you.<br />
			The	user that is specified in the settings.php file must 
			have sufficient privileges to create the database. 
		</p>
		<p><input type="button" value="Create Database" class="Buttons" onclick="runCommand('createdatabase')" /></p>
		<p class="notes"><strong>Note:</strong> Some ISPs limit the number of mySQL databases you are allowed,  control 
		database creation through a web application, or allow changes on a request basis. If you are having problems,, try contacting your ISP.</p>

		<h3>Database Creation Results</h3>
		<p>
			<textarea name="results" id="createdatabaseresults" cols="80" class="results"rows="4"></textarea>
		</p>

	</div>	
		<div>&nbsp;</div>

	<div class="box">
	
	<h2>Step 3 - Construct Base System</h2>
		<p>
			Next, phpBMS needs to create the base level tables and populate the tables with the basic information that phpBMS needs to 
			work.
		</p>
		<p><input type="button" value="Create Tables and Populate Default Information" class="Buttons" onclick="runCommand('populatedata')" /></p>
		<h3>Table Creation and Population Results</h3>
		<p>
			<textarea name="results" id="populatedataresults" class="results" cols="80" rows="10"></textarea>
		</p>
		<h3>Default User and Password</h3>
		<p>
			Upon successful creation of the tables and population of default data phpBMS is almost ready to run.
			One of the records populated was the default administrator record.  This information is required for the first successful login:
		</p>
		<blockquote class="large">
				username: <strong>admin</strong><br />			
				password: <strong>phpbms</strong>
		</blockquote>
		<p class="notes"><strong>Note:</strong> Make sure to change the encryptions seed and password after the first successful login.</p>
	</div>	
	
	
	<p>&nbsp;</p>
	<h1 >Install Additional Modules </h1>
	<div class="box">
		<p>&nbsp;
		</p>
		<p>The base phpBMS system contains <strong>only</strong> the bare framework, administration, and note/task/event capabilities. 
			Additional modules will add functionality such as client/prospect and product management, quote/order/invoice control, 
			and mass e-mailing.
		</p>
		<h3>Step 4 - Install Additional Modules</h3>
		<p>To install a module, highlight the module form the list and click the "Install Selected Module" button.</p>
		<p>
			Select a module to install<br />
			<select size="4" id="modules" name="modules">
			<?php showModules()?>
			</select>
		</p>
		<p>
			<input type="button" id="installmodule" name="installmodule" value="Install Selected Module" class="Buttons" onclick="runModuleInstall()" />
		</p>			

		<h3>Module Installation Results</h3>
		<p>
			<textarea name="results" id="moduleresults" class="results" cols="80" rows="10"></textarea>
		</p>

	</div>

	<p>&nbsp;</p>
	<h1>Secure The Application</h1>
	<div class="box">
		<p>&nbsp;</p>
		<p>phpBMS can contain sensitive information such as usernames, passwords, ,sensitive credit card information that could be exposed to the internet insecurely.
			We recommend performing the following actions to secure phpBMS:		    
		</p>
		<ul>
			<li>
				<p><strong>Delete Installation/Disable Folders</strong> - Once the installation process has been completed for the base system and the BMS modules,
					Either delete the installation folders, or make them inaccessible by the web server and php.</p>
			</li>
			<li>
				<p><strong>Run phpBMS On a Secure Connection (SSL)</strong> - To make sure that usernames, passwords, credit card and other sensitive information are not passed in clear text over the internet, running phpBMS over a secure socket layer is highly recommended.  
					Even if phpBMS is not exposed to the Internet, and is setup on a local LAN, we recommend running it over SSL.</p>
			</li>
		    <li>
		    	<p><strong>Restrict access to the settings.php file -</strong> Make sure that only php can read the settings.php file, and that php does not access to write to the file. </p>
	    	</li>
		</ul>
	</div>
    <p>&nbsp;</p>
	<h1>Complete the Installation</h1>
	<div class="box">
		<h2>Step 5 Log In </h2>
		<p>If all of the above actions completed successfully, phpBMS was installed successfully. </p>
		<p>Navigate to log in screen, use the administrative user name password (above) and log in to finish the installation process. After logging in, you may want to go to the administration section, and set up the basic settings for your application as well as change the administrative username and password. </p>
		<p><input type="button" id="login" name="login" value="Go to Log In Screen" class="Buttons" onclick="document.location='../'" /></p>
	</div>
	<p>&nbsp;</p>
	<h1>Troubleshooting</h1>
	<div class="box">
		<h2>General Help </h2>
		<p>
		If you ran into any problems during this installation, have question about how the program works, or would like any additional
		information about phpBMS, please visit the <a href="http://www.phpbms.org">phpBMS Project web site</a>.  The phpBMS project web site
		has many resources to help you including a user wiki, users forum, and mailing list that can help you.</p>
		<h2>Paid Customization, Installation, Support Options</h2>
		<p>Paid technical support and phpBMS customization is available from <a href="http://www.kreotek.com">Kreotek</a>,  </p>
	</div>
	
</div>
<p align="center" class="tiny">$Rev$ | $LastChangedDate$</p>
</body>
</html>
