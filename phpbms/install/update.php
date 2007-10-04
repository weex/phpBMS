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
<title>phpBMS Update</title>
<link href="../common/stylesheet/mozilla/base.css" rel="stylesheet" type="text/css" />
<link href="install.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../common/javascript/common.js" type="text/javascript"></script>
<script language="JavaScript" src="../common/javascript/moo/prototype.lite.js" type="text/javascript" ></script>
<script language="JavaScript" src="../common/javascript/moo/moo.fx.js" type="text/javascript" ></script>
<script language="JavaScript" src="../common/javascript/moo/moo.fx.pack.js" type="text/javascript" ></script>
<script language="JavaScript" src="update.js" type="text/javascript" ></script>
<script language="JavaScript" type="text/javascript">
	<?php $modules = array_merge($modules,loadModules("update")) ?>
</script>
</head>

<body>
	<h1 id="topTitle">phpBMS v<?php echo $modules["base"]["version"]?> Update</h1>
	
	<div class="bodyline" id="step1">
		<h1>Before Updating</h1>
		
		<p class="important">Backup all of your data and program files before running any update.</p>
		<p>
			By downloading and decompressing this update, you may have already replaced script files 
			from the previous version of phpBMS. If you have decompressed these files to a separate 
			directory and have made custom changes directly to the system we recommend backing up 
			those files before continuing. 
		</p>
		<p>For the latest information about phpBMS check the <a href="http://www.phpbms.org">phpBMS Project web site</a>.</p>

		<p class="nextprevP">
			<button type="button" class="disabledButtons nextprevButtons" onclick="goSection('back')" disabled="disabled">back</button>
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('next')">next</button>
		</p>
	</div>

	
	<div class="bodyline" id="step2">
		<h1>Enter Administrative Log In Information</h1>
		<p>
			Running the update requires administrative access privleges.  Please enter the login credentials
			that have administrative priveleges.
		</p>

		<fieldset>
			<legend>administrative login</legend>
			<p>
				<label for="username">name</label><br />
				<input name="name" type="text" id="username" size="32" maxlength="64" />
				<input name="name" type="hidden" id="version"  value="<?php echo $modules["base"]["version"] ?>" />
			</p>
			<p>
				<label for="password">password</label><br />
				<input name="password" type="password" id="password" size="32" maxlength="24"  />
			</p>
			<p>
				<input type="button" value="Verify" class="Buttons" onclick="runCommand('verifyLogin')" />
			</p>
		</fieldset>
		<h3>Administrative Verification Results</h3>
		<p>
			<textarea name="results" id="verifyLoginresults" class="results" cols="80" rows="2"></textarea>
		</p>
		
		<p class="nextprevP">
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('back')">back</button>
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('next')">next</button>
		</p>		
	</div>
	
	
	<div class="bodyline" id="step3">
		<h1>Check for phpBMS Core Update Availability</h1>

		<p>Check to see if the phpBMS core needs to be updated</p>
		<p class="testButtonsP">
			<input type="button" value="Check Core Availability" class="Buttons" onclick="runCommand('checkBaseUpdate')" />
		</p>
		<h3>Availability Results</h3>
		<p>
			<textarea name="results" id="checkBaseUpdateresults" class="results" cols="80" rows="2"></textarea>
		</p>


		<p class="nextprevP">
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('back')">back</button>
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('next')">next</button>
		</p>		
	</div>


	<div class="bodyline" id="step4">
		<h1>Update phpBMS Core</h1>
		
		<p>If an update was reported as available in the previous section, you should  run the update to the phpBMS core.</p>
		
		<p class="notes">
			If no update is available, running the update on an already updated version of phpBMS
			can cause data corruption and break the application.
		</p>
		
		<p class="testButtonsP">
			<input type="button" value="Update Core" class="Buttons" onclick="runCommand('updateBaseVersion')" />
		</p>
		<h3>Core Update Results</h3>
		<p>
			<textarea name="results" id="updateBaseVersionresults" class="results" cols="80" rows="8"></textarea>
		</p>

		<p class="nextprevP">
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('back')">back</button>
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('next')">next</button>
		</p>		
	</div>
	
	
	<div class="bodyline" id="step5">
		<h1>Update Installed Modules</h1>
		<p>Before updating an installed module, make sure that you meet any module requirements listed.</p>
		
		<p>
			<label for="modules">available modules</label><br />
			<select id="modules" name="modules" onchange="changeModule()">
				<option value="0">Select a module to update...</option>
				<?php showModules($modules);?>
			</select>			
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
			<p class="notes">
				The version above is not necessarily the current data version.  Use the
				"Check For Updates" button to see if an update is necessary.
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
			<p class="testButtonsP">
				<input type="button" id="checkModule" value="Check For Updates" class="Buttons" onclick="runCommand('checkModuleUpdate')" />
				<input type="button" id="updatemodule" name="updatemodule" value="Update Module" class="Buttons" onclick="runModuleUpdate()" disabled="disabled"/>
			</p>
		</div>

		
		<h3>Module Update Results</h3>
		<p>
			<textarea name="results" id="checkModuleUpdateresults" class="results" cols="80" rows="8"></textarea>
		</p>

		
		<p class="nextprevP">
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('back')">back</button>
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('next')">next</button>
		</p>		
	</div>

	<div class="bodyline" id="step6">
		<h1>Complete the Update Process</h1>
		
		<p>
			To complete the update process, you may need to <strong>restart your browser</strong>, or <strong>clear site cookies and browser
			cache</strong> in order for all the changes to take affect.
		</p>
		
		<p class="testButtonsP"><input type="button" id="login" name="login" value="phpBMS Log In" class="Buttons" onclick="document.location='../'" /></p>
		
		<p class="nextprevP">
			<button type="button" class="Buttons nextprevButtons" onclick="goSection('back')">back</button>
			<button type="button" class="disabledButtons nextprevButtons" onclick="goSection('next')" disabled="disabled">next</button>
		</p>	
	</div>
	
	<p class="tiny" align="center"> $Rev$ |  $LastChangedDate$</p>
</body>
</html>
