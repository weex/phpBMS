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
	$loginNoKick=true;
	$loginNoDisplayError=true;
	require("include/session.php");

	$pageTitle = "phpBMS Browser Requirements";

	$phpbms->cssIncludes[] = "pages/requirements.css";

	$phpbms->showMenu = false;
	$phpbms->showFooter = false;

	include("header.php");
        
?><div class="bodyline" id="container">

	<h1>phpBMS Browser Requirements</h1>

	<ul>

		<li>
			<h3>Enabled JavaScript v2.0 or higher processing</h3>
			<p>
				phpBMS heavily uses JavaScript, including newer functions only found in version 2.0 or higher.
				Most newer browsers support this, but make sure that JavaScript is enabled for this web application
			</p>
		</li>

		<li>
			<h3>Allow window pop-ups</h3>
			<p>
				This application sparingly uses JavaScript to open multiple windows when printing.  If your browser
				or third-party browser plug in prohibits pop-ups from this application, printing reports will not
				be displayed.
			</p>
		</li>

		<li>
			<h3>Cookie support</h3>
			<p>
				phpBMS sets a single cookie to track your login.  Cookie support for this site
				must be enabled in order for the application to allow you to log in
			</p>
		</li>

		<li>
			<h3>Full Cascading Style Sheet (CSS) v1.2 support</h3>
			<p>
				phpBMS takes advantage of CSS v1.2 to render pages.  Without this
				support, the application may not look correct, and can even break some
				functionality.
			</p>
		</li>
	</ul>

	<h2>Tested Browsers</h2>

	<p>
		This is a list of tested browsers known to work with the current version of phpBMS.  If you successfully
		test another browser with this version, please report it on our forums at <a href="http://phpbms.org">http://phpbms.org</a>
		so we can add it to the list.  Be sure to report the phpBMS version, browser name, browser version, and
		operating system.
	</p>

	<ul>
		<li>Firefox v3.07 for Macintosh</li>
		<li>Firefox v3.07 for Windows</li>
		<li>Safari v3.2.1 for Macintosh</li>
		<li>Internet Explorer v7.0.5730.13 for Windows</li>
		<li>Opera v9.64 for Windows</li>
	</ul>

	<p align="right">
		<input type="button" value="Log In" class="Buttons" onclick="document.location='./'" id="loginButton"/>
	</p>
</div><?php include("footer.php");?>
