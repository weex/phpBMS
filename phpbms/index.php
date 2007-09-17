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
	require_once("include/session.php");
	require_once("include/login_include.php");
	
	$pageTitle = APPLICATION_NAME." Log in";
	
	$phpbms->cssIncludes[] = "pages/login.css";
	$phpbms->jsIncludes[] = "common/javascript/login.js";
	
	$phpbms->showMenu = false;
	$phpbms->showFooter = false;	
	
	include("header.php");
?>
	
	<div id="loginbox" class="bodyline" >
		<h1><span><?php echo APPLICATION_NAME;?></span></h1>
		<?php if ($failed) {?><div class="standout" id="failed"><?php echo $failed?></div><?php } ?>
		<noscript>
			<p class="standout" align="center">JavaScript is disabled.</p>
			<p> Please check browser requirements.</p>
		</noscript>
		
		<form name="form1" method="post" action="<?php echo $_SERVER["PHP_SELF"]?>">
			<fieldset>
				<legend>Log In</legend>
				<p>
					<label for="username">name</label><br />
					<input name="name" type="text" id="username" size="25" maxlength="64" value="<?php echo htmlQuotes($_POST["name"])?>" disabled="disabled"/>
				</p>
				
				<p>
					<label for="password">password</label><br />
					<input name="password" type="password" id="password" size="25" maxlength="24" disabled="disabled"/>
				</p>
				
			</fieldset>
			<p id="buttonP"><button class="Buttons" type="submit" id="loginButton" disabled="disabled">Log In</button></p>
		</form>		
		<p id="moreinfoButtonP"><button id="moreinfoButton" type="button" class="graphicButtons buttonInfo"><span>more info</span></button></p>
		<div id="moreinfo" >
			<div class="box">
			<h2><a href="http://www.phpbms.org" title="phpBMS"><span class="alt">phpBMS</span></a></h2>
			<h3>Business Management Web Application</h3>
			<p class="tiny">
				<a href="requirements.php">browser requirements</a> |
				<a href="info.php">program info</a>
			</p>
			</div>
		</div>
	</div>

		
	<?php if(DEMO_ENABLED=="true"){?>
	<div id="demobox" class="bodyline">
		<h2>Demonstration Mode</h2>
		<p>
			Use the following user credentials to log into phpBMS. Each user 
			highlights a different security access level.
		</p>
		<dl>
			<dt>Shipping Personnel</dt>
			<dd>
				username: shipping<br />
				password: shipping
</dd>
			<dt>Sales Personnel</dt>
			<dd>username: sales<br />
			password: sales</dd>
			<dt>Sales Manager</dt>
			<dd>username: salesmanager<br />
				password: salesmanager
		     </dd>
		</dl>
	</div>
	<?php } include("footer.php")?>
