<?php
/*
 $Rev: 392 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2008-05-13 17:06:00 -0600 (Tue, 13 May 2008) $
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

	require("install_include.php");
	require("../phpbmsversion.php");

	$moduleClass = new modules();
	$moduleClass->list = array_merge($modules, $moduleClass->list);

	//check for php version
	$neededVer = "5.2.0";
	$phpVer = phpversion();
	if(floatval($neededVer) <= floatval($phpVer))
		$phpVerClass = "success";
	else
		$phpVerClass = "fail";

	//check to see if mysql plugin present
	if(phpversion("mysql"))
		$mysqlPresent = "success";
	else
		$mysqlPresent = "fail";

	//check the web server
	$webServer = explode(" ",$_SERVER['SERVER_SOFTWARE']);
	$webServer = explode("/", $webServer[0]);

	if(strtolower($webServer[0]) == "apache"){

		if(floatval($webServer[1]) > 2)
			$webServerReport["class"] = "success";
		else
			$webServerReport["class"] = "warning";

		$webServerReport["message"] = $webServer[0]."/".$webServer[1];

	} else {
		$webServerReport["class"] = "warning";
		$webServerReport["message"] = "Non-Apache servers are untested and may have problems.";
	}


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
	<?php $moduleClass->displpayJS() ?>
</script>
</head>

<body>
	<noscript>
		<div class="bodyline">
			<h1>Javascript Support Disabled</h1>
			<p>Both the installer and the main phpBMS program require JavaScript support in order to run.</p>
		</div>
	</noscript>

	<h1 id="topTitle">phpBMS v<?php echo $moduleClass->list["base"]["version"]?> Installation</h1>

	<div class="bodyline">

		<div id="navPanel">
			<select id="navSelect" size="10">
				<option value="1" selected="selected">* System Requirements</option>
				<option value="2">* Create settings.php File</option>
				<option value="3">* Test Database Connection</option>
				<option value="4">* Create the Database</option>
				<option value="5">* Populate Core Data</option>
				<option value="6">* Install Modules</option>
				<option value="7">* Secure the Application</option>
				<option value="8">* Complete Installation</option>
			</select>
			<p><input type="checkbox" id="debug" /><label for="debug">installation debug</label></p>
		</div>
		<div id="stepsPanel">

			<div class="steps" id="step1">

				<p class="nextprevP">
					<button type="button" class="disabledButtons prevButtons" disabled="disabled">back</button>
					<button type="button" class="Buttons nextButtons">next</button>
				</p>

				<h1>System Requirements</h1>
				<p>
					Welcome to the phpBMS installation process. If
					you have any problems with the installation process,
					try activating the installation debug checkbox on the left.
					Before continuing, make sure your system meets the
					requirements.
				</p>

				<h2>Server Requirements</h2>
				<table id="sysRequirements" cellpadding="0" cellspacing="0" border="0">
					<thead>
						<tr>
							<td>requirement</td>
							<td>server</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>PHP 5.2.0 or higher</td>
							<td><span class="<?php echo $phpVerClass?>"><?php echo $phpVer?></span></td>
						</tr>
						<tr>
							<td>PHP MySQL Support</td>
							<td><span class="<?php echo $mysqlPresent?>"><?php echo $mysqlPresent?></span></td>
						</tr>
						<tr>
							<td>Apache 2.0 or higher</td>
							<td><span class="<?php echo $webServerReport["class"]?>"><?php echo $webServerReport["message"]?></span></td>
						</tr>
						<tr>
							<td>MySQL Server 5.0 or higher</td>
							<td>tested when connection established</td>
						</tr>

					</tbody>
				</table>

				<h2>Recommend Settings</h2>

				<ul>
					<li>CRON support for running php scripts.</li>
					<li>GD PHP Support (for some graphics on reports)</li>
				</ul>

			</div>


			<div class="steps" id="step2">
				<p class="nextprevP">
					<button type="button" class="Buttons prevButtons">back</button>
					<button type="button" class="Buttons nextButtons">next</button>
				</p>

				<h1>Create settings.php File</h1>

				<p>
					You need to create the settings.php file that will contain your
					database connection information. The easiest way to do this is to
					make a copy the file <strong>defaultsettings.php</strong> (located
					in the phpBMS application's root folder) and name the copied file
					<strong>settings.php</strong>.
				</p>

				<p>Next, modify the following parameters inside the settings.php file to match your server:</p>

				<ul class="small">
					<li><p><strong>mysql_server</strong></p>
					    <p>
						The MySQL server location<br/>
						In most cases, this should be the same location as the web server, i.e. &quot;localhost&quot;
						</p>
					</li>
					<li>
						<p><strong>mysql_database</strong></p>
						<p>The name of the database to be used by phpBMS. <br />
						If the database has not been created, you will be given the opportunity to create the database.</p>
					</li>
					<li>
						<p><strong>mysql_user</strong></p>
						<p>The name of the user PHP will use to access the database.</p>
					</li>
					<li>
						<p><strong>mysql_userpass</strong></p>
						<p>The password for the user PHP will use to access the database.</p>
					</li>
					<li>
						<p><strong>mysql_pconnect</strong></p>
						<p>(&quot;true&quot; or &quot;false&quot;) This tells phpBMS to use either the PHP mysql_pconnect or mysql_connect command. Some web hosting providers do not allow mysql_pconnect.</p>
					</li>
				</ul>

			</div>


			<div class="steps" id="step3">
				<p class="nextprevP">
					<button type="button" class="Buttons prevButtons">back</button>
					<button type="button" class="Buttons nextButtons">next</button>
				</p>

				<h1>Test Database Connection</h1>
				<p>
					Once the <strong>settings.php</strong> file has been created and the database connection information has been entered, test the connection.
				</p><p>
					If connection fails, check
					to make sure the <strong>settings.php</strong> is setup correctly. You may also want
					to enable the installation debug to view specifics of why the connection is not working.
				</p>

				<p><input type="button" value="Test Connection" class="Buttons" id="testConnectionButton" /> <strong id="testConnectionNoDebug"></strong></p>

				<div class="debugResults">
					<h3>Connection Test Results</h3>
					<p><textarea name="results" id="testconnectionresults" cols="40" rows="4" class="results"></textarea></p>
				</div>

			</div>


			<div class="steps" id="step4">
				<p class="nextprevP">
					<button type="button" class="Buttons prevButtons">back</button>
					<button type="button" class="Buttons nextButtons">next</button>
				</p>

				<h1>Create the Database (Schema)</h1>

				<p>
					If you have manually created the database (schema) specified in your
					settings.php file (mysql_database) that phpBMS will use already exists,
					you can <strong>skip this step</strong>.
				</p>

				<p>If the database (schema) that phpBMS will use does not already exist, phpBMS can attempt to create it for you.</p>

				<p class="notes">
					<strong>Note:</strong> Some hosting companies limit the number of mySQL databases
					you are allowed, control database creation through a web application, or allow
					changes on a request basis. If you are having problems contact your hosting
					company.
				</p>

				<p><input type="button" value="Create Database Schema" class="Buttons" id="createDatabaseButton"/> <strong id="createDatabaseNoDebug"></strong></p>

				<div class="debugResults">
					<h3>Schema Creation Results</h3>
					<p><textarea name="results" id="createdatabaseresults" cols="80" class="results"rows="4"></textarea></p>
				</div>

			</div>


			<div class="steps" id="step5">

				<p class="nextprevP">
					<button type="button" class="Buttons prevButtons">back</button>
					<button type="button" class="Buttons nextButtons">next</button>
				</p>

				<h1>Populate Core Data</h1>

				<p>
					phpBMS needs to create the core functionality tables and
					populate them with the default data needed to run the program.
				</p>

				<p>
					<label for="appname">application name</label><br />
					<input type="text" id="appname" value="phpBMS"/>
				</p>

				<p>
					<label for="email">administrator e-mail address</label><br />
					<input type="text" id="email" value=""/>
				</p>

				<p><input type="button" value="Install Core Data" class="Buttons" id="coreDataButton" /> <strong id="coreDataNoDebug"></strong></p>

				<div class="debugResults">
					<h3>Core Data Population Results</h3>
					<p><textarea name="results" id="coredatainstallresults" class="results" cols="80" rows="20"></textarea></p>
				</div>

			</div>

			<div class="steps" id="step6">

				<p class="nextprevP">
					<button type="button" class="Buttons prevButtons">back</button>
					<button type="button" class="Buttons nextButtons">next</button>
				</p>

				<h1>Install Modules</h1>

				<p>
					The base phpBMS system contains <strong>only</strong> the basic framework, administration, and note/task/event capabilities.
					Additional modules add functionality such as client and product management, sales orders, and mass e-mailing.
					None of these modules are required for the phpBMS application to run, but functionality may be limited.
				</p>

				<p class="notes">
					<strong>Note:</strong> The order in which modules is installed can be important.
					Some modules may depend on other modules.  Check each of the requirements for a module before
					installing it.
				</p>

				<h2>Available Modules</h2>

				<?php $moduleClass->displayInstallTable() ?>

				<div class="debugResults">
					<h3>Module Installation Results</h3>
					<p><textarea name="results" id="moduleinstallresults" class="results" cols="80" rows="10"></textarea></p>
				</div>

			</div>

			<div class="steps" id="step7">

				<p class="nextprevP">
					<button type="button" class="Buttons prevButtons">back</button>
					<button type="button" class="Buttons nextButtons">next</button>
				</p>

				<h1>Secure the Application</h1>

				<p>
					phpBMS can contain sensitive information such as user names, passwords,
					client data, and payment information that could be exposed to the internet
					insecurely.
				</p>


				<h2>Required Security Steps</h2>

				<ul>
					<li>
						<h3>Delete Installation Folders</h3>
						<p>
							You must delete both the core installation folder, as well as all modules' installation
							folders before you can use the system
						</p>
					</li>
					<li>
						<h3>Setup and Restrict Access to the Cron Script</h3>
						<p>
                                                    phpBMS has a scheduler function that runs items on a timed basis using cron
                                                    or another scheduler program to run php via command line.  Check the
                                                    Scheduler under the system menu after logging in for details on configuring this
                                                    in your crontab file.  Once configured, you will want to disable your web server from
                                                    allowing this file to be called from your web server (the outside).
						</p>
					</li>
				</ul>

				<h2>Additional Security Steps</h2>

				<ul>
					<li>
						<h3>Payment Information Encryption</h3>
						<p>
                                                    If you plan on storing sensitive payment information such as credit card
                                                    numbers make sure to enable the encrption option in the configuration page
                                                    underneath the BMS module section. You will also need to create and link
                                                    to an external file on the server that contains the encryption key. Typically,
                                                    encyrption key files are text files containing a 64-128 character hash.
						</p>
					</li>

					<li>
						<h3>Run phpBMS On a Secure Connection (SSL)</h3>
						<p>
							Make sure that usernames, passwords, credit card and other
							sensitive information are not passed in clear text over the internet
							by running phpBMS over a secure socket layer.
							Even if phpBMS is not exposed to the Internet, and is setup on a
							local LAN, we recommend running it over SSL.
						</p>
					</li>

					<li>
						<h3>Limit Access to the Application</h3>
						<p>
							phpBMS is not designed to be used as a client
							portal.  Allowing clients the ability to log in
							to our system could give access to sensitive
							information to clients, vendors, or sales partners.
						</p>
					</li>
				</ul>

			</div>

			<div class="steps" id="step8">

				<p class="nextprevP">
					<button type="button" class="Buttons prevButtons">back</button>
					<button type="button" class="disabledButtons nextButtons" disabled="disabled">next</button>
				</p>

				<h1>Complete Installation</h1>


				<h2>Administrator Information</h2>
				<table id="userpassTable">
					<tbody>
						<tr>
							<td align="right">login: </td>
							<td ><strong>admin</strong></td>
						</tr>
						<tr>
							<td align="right">password: </td>
							<td ><strong id="pass2">no password set</strong></td>
						</tr>
					</tbody>
				</table>

				<p class="notes">
					<strong>Note:</strong> Remember to remove the install folder and any modules' install folder
					before logging in. You may want to write down the password above before navigating to the
					<a href="../" >application page</a>.
				</p>

				<h2>Troubleshooting</h2>
				<h3>General Help </h3>
				<p>
				If you have problems during installation, have questions about how the program works, or would like additional
				information about phpBMS, please visit the <a href="http://www.phpbms.org">phpBMS Project web site</a>.  The phpBMS project web site
				has many resources to help you including a user wiki, users forum, and mailing list that can help you.</p>
				<h3>Paid Customization, Installation, Support Options</h3>
				<p>Paid technical support and phpBMS customization is available from <a href="http://www.kreotek.com">Kreotek</a>,</p>

			</div>
		</div>
	</div>


<p align="center" class="tiny">$Rev: 392 $ | $LastChangedDate: 2008-05-13 17:06:00 -0600 (Tue, 13 May 2008) $</p>
</body>
</html>
