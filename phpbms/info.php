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
	$loginNoKick=true;
	$loginNoDisplayError=true;

	require_once("include/session.php");
	
	
	function displayVersions($db){
		$querystatement="SELECT displayname,version from modules ORDER BY id";
		$queryresult=$db->query($querystatement);

		if($queryresult){
			while($therecord=$db->fetchArray($queryresult)){
				if($therecord["displayname"]!="Base"){
					echo $therecord["displayname"].": ";
					echo "v".$therecord["version"]."<br />";
				} else
					echo "<span class=\"important\">v".$therecord["version"]."</span><br /><br />";
			}
		}
	}
	
	$pageTitle="phpBMS Information";
	$phpbms->cssIncludes[] = "pages/info.css";
	
	$phpbms->showMenu = false;
	$phpbms->showFooter = false;
	
	include("header.php");
	
?><div class="bodyline" id="container">
	<h1>About phpBMS</h1>
	<p>phpBMS is commercial open source, web-based, business management software.</p>
	
	<div id="phpBMSLogo" class="box small">
		<?php if(isset($db)) displayVersions($db)?>
	</div>
	
	<div id="companyInfo">
		<p class="small">
			Copyright &copy; 2004 -2007 Kreotek, llc. All Rights Reserved.
			phpBMS, and the phpBMS logo are trademarks of Kreotek, llc. 
			Software is licensed under a <a href="license.txt">modified BSD license</a>.
		</p>
		
		<h3>Kreotek, LLC</h3>
		<p class="small">
			610 Quantum<br />
			Rio Rancho, NM 87124
		</p>
		<p>
			web: <a href="http://www.kreotek.com">http://www.kreotek.com</a><br />
			sales: <a href="mailto:sales@kreotek.com">sales@kreotek.com</a><br />
			support: <a href="mailtosupport@kreotek.com">support@kreotek.com</a><br />
			phone: <strong>1-800-731-8026</strong>
		</p>
		<h3>phpBMS Open Source Project</h3>
		<p>
			project web site: <a href="http://www.kreotek.com">http://www.phpbms.org</a><br />
			project forums: <a href="mailto:sales@kreotek.com">sales@kreotek.com</a><br />
		</p>
	</div>
	
		<h2>Source Code</h2>
		<ul>
			<li><strong>phpBMS</strong> - Commercial Open Source Business Management Web Appllication (<a href="http://www.kreotek.com">www.phpbms.org</a>)</li>
			<li><strong>fpdf</strong> - A PHP class which allows to generate PDF files with pure PHP (<a href="http://www.fpdf.org">www.fpdf.org</a>)</li>
		    <li><strong>moo.fx</strong> - Super lightweight JavaScript effects library (<a href="http://moofx.mad4milk.net/">moofx.mad4milk.net</a>) </li>
			<li><strong>mochikit</strong> - A lightweight JavaScript library (<a href="http://mochichit.com/">mochikit.com</a>) - phpBMS utilizes modified parts mochikit code and it's programming structure.</li>
		</ul>
		<h2>Technologies</h2>
		<ul>
			<li><strong>php</strong> -  A widely-used general-purpose scripting language that is especially suited for Web development and can be embedded into HTML.  (<a href="http://www.php.net">www.php.net</a>)</li>
			<li><strong>MySQL</strong> - An open source relational database management system (RDBMS) that uses Structured Query Language (SQL) (<a href="http://www.mysql.org">www.mysql.org</a>)</li>
			<li><strong>AJAX</strong> - Asynchronous Javascript And XML is a group of technologies that help browser based applications behave more like applications you run from your desktop.</li>
		</ul>

	<p align="right">
		<input type="button" value="Back" class="Buttons" onclick="document.location='<?php echo APP_PATH; if(isset($_SESSION["userinfo"])) echo DEFAULT_LOAD_PAGE?>'" id="loginButton"/>
	</p>
</div><?php include("footer.php") ?>