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

include("install_include.php");
include("version.php");

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>phpBMS Installation</title>
<link href="../common/stylesheet/mozilla/base.css" rel="stylesheet" type="text/css" />
<link href="install.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../common/javascript/moo/prototype.lite.js" type="text/javascript" ></script>
<script language="JavaScript" src="../common/javascript/moo/moo.fx.js" type="text/javascript" ></script>
<script language="JavaScript" src="../common/javascript/moo/moo.fx.pack.js" type="text/javascript" ></script>
<script language="JavaScript" src="../common/javascript/common.js" type="text/javascript"></script>
<script language="JavaScript" src="install.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
	<?php $modules = array_merge($modules,loadModules("install")) ?>
</script>
</head>

<body>

	<h1 id="topTitle">phpBMS v<?php echo $modules["base"]["version"]?> Installation</h1>
	
	
	<div class="bodyline" id="step1">
		<h1>Welcome</h1>
		<p>
			Welcome to the phpBMS installation process. We will guide you through
			the initial installation of phpBMS system.  Before continuing, make
			sure your system meets the requirements.
		</p>

		<h2>Minimum Server Requirements</h2>
		<ul>
			<li>MySQL 3.23.58 or higher</li>
			<li>PHP 4.3.0 or higher (preferably PHP 4.0</li>
			<li>Web Server</li>
		</ul>

		<h2>Reccomended Server Settings</h2>
		<ul>
			<li>MySQL 5 or higher</li>
			<li>PHP 5 or higher</li>
			<li>Apache 2 or higher</li>
			<li>Cron Access</li>
			<li>PHP JSON-PEAR</li>
		</ul>
		<p class="nextprevP">
			<button type="button" class="disabledButtons nextprevButtons" onclick="goSection('back')" disabled="disabled">back</button>
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('next')">next</button>
		</p>
	</div>


	<div class="bodyline" id="step2">
		<h1>Creating the settings.php file</h1>

		<p>Make a copy the file <strong>defaultsettings.php</strong> (located in the web application root)</p>
		<p>and name the new file <strong>settings.php</strong>.</p>
		<p>Next, modify the following parameters inside the settings.php file:</p>
		
		<ul class="small">
			<li><p>
				<strong>mysql_server</strong>: the MySQL server location <br />
				In most cases, this should be the same location as the web server, i.e. &quot;localhost&quot;
				</p>
			</li>
			<li>
				<p><strong>mysql_database</strong>: the name of the database to be used by phpBMS. <br />
				If the database has not been created, You can use <a href="#step2">step two</a> to create a new database for phpBMS.</p>
			</li>
			<li>
				<p><strong>mysql_user</strong>: the name of the user PHP will use to access the database.</p>
			</li>
			<li>
				<p><strong>mysql_userpass</strong>:	the	password for the user PHP will use to access the database.</p>
			</li>
			<li>
				<p><strong>mysql_pconnect</strong>:	(&quot;true&quot; or &quot;false&quot;) This tells phpBMS to use either the PHP mysql_pconnect or mysql_connect command. Some web hosting providers do not allow mysql_pconnect.</p>
			</li>
		</ul>

		<p class="nextprevP">
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('back')">back</button>
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('next')">next</button>
		</p>	
	</div>


	<div class="bodyline" id="step3">
		<h1>Test the Database Connection</h1>		
		<p>
			Once the <strong>settings.php</strong> file has been created and the database connection information has been entered, test the connection.  If connection fails, check
			to make sure the <strong>settings.php</strong> is setup correctly.
		</p>
	
		<p class="testButtonsP"><input type="button" value="Test Connection" class="Buttons" onclick="runCommand('testconnection')" /></p>

		<h3>Connection Test Results</h3>
		<p><textarea name="results" id="testconnectionresults" cols="40" rows="4" class="results"></textarea></p>
				
		<p class="nextprevP">
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('back')">back</button>
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('next')">next</button>
		</p>	
	</div>
	
	
	<div class="bodyline" id="step4">
		<h1>Create the Database (Schema)</h1>

		<p>If the database (schema) that phpBMS will use already exists, you can skip this step. </p>
		<p>If the database (schema) that phpBMS will use does not exist, phpBMS can attempt to create it.</p>
		<p class="notes"><strong>Note:</strong> Some hosting companies limit the number of mySQL databases you are allowed, control database creation through a web application, or allow changes on a request basis. If you are having problems contact your hosting company.</p>

		<p class="testButtonsP"><input type="button" value="Create Database" class="Buttons" onclick="runCommand('createdatabase')" /></p>
	
		<h3>Database Creation Results</h3>
		<p><textarea name="results" id="createdatabaseresults" cols="80" class="results"rows="4"></textarea></p>

		<p class="nextprevP">
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('back')">back</button>
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('next')">next</button>
		</p>	
	</div>	
	

	<div class="bodyline" id="step5">
		<h1>Populate Core Data</h1>
		<p>phpBMS need to create the core tables and populate them.</p>
		<p class="testButtonsP"><input type="button" value="Install Core Data" class="Buttons" onclick="runCommand('populatedata')" /></p>
		<h3>Population Results</h3>
		<p>
			<textarea name="results" id="populatedataresults" class="results" cols="80" rows="10"></textarea>
		</p>

		<p class="nextprevP">
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('back')">back</button>
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('next')">next</button>
		</p>	
	</div>

	<div class="bodyline" id="step6">
		<h1>Core installation is complete</h1>
		<p>
			The core installation of phpBMS is complete.  The phpBMS core does <strong>not</strong>
			contain any business management features. If you do not wish to install these features,
			you can go directly to the <a href="../">login</a> page.
		</p>
		<p>
			For most users, you will want to install at least the BMS module to give you full client and invoicing
			capabilities.  If so, proceed to the next section.
		</p>
		<h2>Administrator log in information</h2>
		<blockquote class="large">
				login: <strong>admin</strong><br />			
				password: <strong>phpbms</strong>
		</blockquote>
		<p class="notes"> Make sure to change the encryption seed, login name, and password after the first successful login.</p>
		<p class="nextprevP">
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('back')">back</button>
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('next')">next</button>
		</p>	
	</div>

	<div class="bodyline" id="step7">
		<h1>Installing Additional Modules</h1>
		<p>The base phpBMS system contains <strong>only</strong> the bare framework, administration, and note/task/event capabilities. Additional modules add functionality such as client and product management, sales orders, and mass e-mailing. </p>

		<p>
			<label for="modules">available modules</label><br />
			<select id="modules" name="modules" onchange="changeModule()"><option value="0">Select a module to install...</option><?php showModules($modules)?></select> <input type="button" id="installmodule" name="installmodule" value="Install Module" class="Buttons" onclick="runModuleInstall()" disabled="disabled"/>
		</p>
		
		<div id="moduleInformation" class="box" style="display:none">
			<h2>Module Information</h2>
			<p>
				module Name<br />
				<strong id="modulename"></strong>
			</p>
			<p>
				version: <strong id="moduleversion"></strong>
			</p>
			<p>
				description<br />
				<strong id="moduledescription"></strong>				
			</p>
			<p>
				requirements<br />
				<strong id="modulerequirements"></strong>
			</p>
			<p class="notes">make sure your system meets all of the module's requirements.</p>
		</div>
			
		<h3>Module Installation Results</h3>
		<p>
			<textarea name="results" id="moduleresults" class="results" cols="80" rows="10"></textarea>
		</p>

		<p class="nextprevP">
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('back')">back</button>
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('next')">next</button>
		</p>	
	</div>	
	
	<div class="bodyline" id="step8">
		<h1>Securing the Application</h1>
		
		<p>
			phpBMS can contain sensitive information such as user names, passwords, client information, and sensitive payment
		    information that could be exposed to the internet insecurely.
			We recommend performing the following actions to secure phpBMS:	</p>
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
		
		<p class="nextprevP">
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('back')">back</button>
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('next')">next</button>
		</p>	
	</div>	
	
	<div class="bodyline" id="step9">
		<h1>Installtion Complete</h1>
		
		<p>
			If you received no errors in the result boxes and the previous sections your intallation of
			phpBMS has completed succsfully and you can log in. Note the administrtor login information.
			You will need this when logging in for the first time.
		</p>

		<h2>Administrator log in information</h2>
		<blockquote class="large">
				login: <strong>admin</strong><br />			
				password: <strong>phpbms</strong>
		</blockquote>
		<p class="notes"> Make sure to change the encryption seed, login name, and password after the first successful login.</p>

		<p class="testButtonsP"><input type="button" id="login" name="login" value="phpBMS Log In" class="Buttons" onclick="document.location='../'" /></p>
	
		<h2>Troubleshooting</h2>
		<h3>General Help </h3>
		<p>
		If you ran into any problems during this installation, have question about how the program works, or would like any additional
		information about phpBMS, please visit the <a href="http://www.phpbms.org">phpBMS Project web site</a>.  The phpBMS project web site
		has many resources to help you including a user wiki, users forum, and mailing list that can help you.</p>
		<h3>Paid Customization, Installation, Support Options</h3>
		<p>Paid technical support and phpBMS customization is available from <a href="http://www.kreotek.com">Kreotek</a>,</p>
		
		<p class="nextprevP">
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('back')">back</button>
			<button type="button" class="disabledButtons nextprevButtons" onclick="goSection('next')" disabled="disabled">next</button>
		</p>	
	</div>		
	
<p align="center" class="tiny">$Rev$ | $LastChangedDate$</p>
</body>
</html>
