<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
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

	include("update_include.php");

	$updater = new updater();
	$updater->buildList();


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

	$mysqlVer = $updater->getMySQLVersion();

	if(floatval($mysqlVer) >= 5)
		$mysqlPassFailClass = "success";
	else
		$mysqlPassFailClass = "fail";

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>phpBMS Update</title>
<link href="../common/stylesheet/mozilla/base.css" rel="stylesheet" type="text/css" />
<link href="install.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../common/javascript/common.js" type="text/javascript"></script>
<script language="JavaScript" src="../common/javascript/moo/prototype.lite.js" type="text/javascript" ></script>
<script language="JavaScript" src="../common/javascript/moo/moo.fx.js" type="text/javascript" ></script>
<script language="JavaScript" src="../common/javascript/moo/moo.fx.pack.js" type="text/javascript" ></script>
<script language="JavaScript" src="update.js" type="text/javascript" ></script>
</head>

<body>
	<noscript>
		<div class="bodyline">
			<h1>Javascript Support Disabled</h1>
			<p>Both the installer and the main phpBMS program require JavaScript support in order to run.</p>
		</div>
	</noscript>

	<h1 id="topTitle">phpBMS v<?php echo $updater->list["base"]["version"]?> Update</h1>

	<div class="bodyline">

		<div id="navPanel">
			<select id="navSelect" size="10">
				<option value="1" selected="selected">* Preparing For Update</option>
				<option value="2">* Update Core Program</option>
				<option value="3">* Update Modules</option>
				<option value="4">* Finish Update</option>
			</select>
			<p><input type="checkbox" id="debug" /><label for="debug">updating debug</label></p>
		</div>
		<div id="stepsPanel">

			<div class="steps" id="step1">

				<p class="nextprevP">
					<button type="button" class="disabledButtons prevButtons" disabled="disabled">back</button>
					<button type="button" class="Buttons nextButtons">next</button>
				</p>

				<h1>Preparing For Update</h1>
				<p>
					Before updating, there are several steps to take and ensure that backup runs smoothly.
				</p>
				<ul>
					<li>
						<strong>Backup</strong> your database and phpbms directory. If you have access to a shell, using
						the mysqldump command is a fast way of creating a reliable backup of your data.
					</li>

					<li>
						By downloading and decompressing this update, you may have already replaced script files
			                        from the previous version of phpBMS. If you have decompressed these files to a separate
			                        directory and have made custom changes directly to the system we recommend backing up
			                        those files before continuing.
					</li>

					<li>
						For the latest information about phpBMS check the <a href="http://www.phpbms.org">phpBMS Project web site</a>.
					</li>
				</ul>

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
							<td><span class="<?php echo $mysqlPassFailClass ?>"><?php echo $mysqlVer ?></span></td>
						</tr>

					</tbody>
				</table>

			</div>

			<div class="steps" id="step2">

				<p class="nextprevP">
					<button type="button" class="Buttons prevButtons">back</button>
					<button type="button" class="Buttons nextButtons">next</button>
				</p>

				<h1>Update Core Program</h1>

				<p>
					The database reports the current version is <strong><?php echo $updater->list["base"]["currentversion"] ?></strong>.
					The application files show the application version to upgrade to is <strong><?php echo $updater->list["base"]["version"] ?></strong>.
				</p>

				<?php if($updater->checkBaseUpdate()) { ?>

					<p class="notes"><strong>Versions match.  No update is necessary.</strong></p>

				<?php } else { ?>

					<p class="notes"><strong>You must update the phpBMS Core Program before updating any modules.</strong></p>


					<p><button class="Buttons" id="updatecoreButton">Update Core Program</button> <span id="coreDataNoDebug"></span></p>

					<div class="debugResults">
						<h3>Update Core Results</h3>
						<p><textarea name="results" id="coredataupdateresults" cols="40" rows="4" class="results"></textarea></p>
					</div>

				<?php } //endif  ?>
			</div>

			<div class="steps" id="step3">

				<p class="nextprevP">
					<button type="button" class="Buttons prevButtons">back</button>
					<button type="button" class="Buttons nextButtons">next</button>
				</p>

				<h1>Update Modules</h1>

                                <p>
                                    To install a module that is not currently installed, wait until the update process
                                    has completed successfully.  Then run the installation script and skip to the "install
                                    modules" section.
                                </p>

				<?php $updater->showModulesUpdate(); ?>

				<div class="debugResults">
					<h3>Module Installation Results</h3>
					<p><textarea name="results" id="moduleupdateresults" class="results" cols="80" rows="10"></textarea></p>
				</div>

			</div>

			<div class="steps" id="step4">

				<p class="nextprevP">
					<button type="button" class="Buttons prevButtons">back</button>
					<button type="button" class="disabledButtons nextButtons" disabled="disabled">next</button>
				</p>

				<h1>Finish Update</h1>
				<p>
					To finish the update process you will need to:
				</p>
				<ul>
					<li>
						<h3>Delete Install Folders</h3>
						<p>
							You must delete both the core installation folder, as well as all modules' installation
							folders before you can use the system
						</p>
					</li>
					<li>
						<h3>Clear your Browser Cache</h3>
						<p>
							Part of the update process may have replaced javascript and stylesheet (css) files.
							Most browsers cache these files to speed loading times. In order to insure that
							your web application is using all of the latest updates, you will need to
							clear the browser cache of all client browsers that access the application.
						</p>
						<p>
							Most browsers will clear this cache automatically if you simply restart the browser
						</p>
					</li>
					<li>
						<h3>Payment Information Encryption</h3>
						<p>
                                                    If you store sensitive payment information such as credit card
                                                    numbers make sure to enable the encryption option in the configuration page
                                                    underneath the BMS module section. You will also need to create and link
                                                    to an external file on the server that contains the encryption key. Typically,
                                                    encryption key files are text files containing a 64-128 character hash.
						</p>
					</li>

				</ul>
				<h2>Troubleshooting</h2>
				<h3>General Help </h3>
				<p>
				If you have problems during updating, have questions about how the program works, or would like additional
				information about phpBMS, please visit the <a href="http://www.phpbms.org">phpBMS Project web site</a>.  The phpBMS project web site
				has many resources to help you including a user wiki, users forum, and mailing list that can help you.</p>
				<h3>Paid Customization, Update, Support Options</h3>
				<p>Paid technical support and phpBMS customization is available from <a href="http://www.kreotek.com">Kreotek</a>.</p>

                                <h2>Help Make phpBMS Better!</h2>

                                <h3>Registration</h3>

                                <p>
                                    Take the time to register your copy of phpBMS.  Registration helps
                                    guide future development of the project so we can continue to make
                                    focus on the communities needs.
                                </p>
                                <p id="registerP">Take a minute to <a href="kreotek.com/registerphpbms">register your copy of phpBMS</a> today.</p>

                                <h3>Contribute</h3>

                                <p>
                                    phpBMS is true open source software.  Contributions in the form of code patches, new modules,
                                    documentation, and bug reporting are always appreciated.  You can find out more about
                                    contributing to the project at <a href="http://www.phpbms.org">community web site</a>.
                                </p>
			</div>

		</div>

	</div>

	<p class="tiny" align="center"> $Rev$ |  $LastChangedDate$</p>
</body>
</html>
