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
	$loginNoKick = true;
	$loginNoDisplayError = true;

	require_once("include/session.php");

	class versions{

		var $db;
		var $queryresult;

		function versions($db){

			$this->db = $db;

		}//end function init

		function get(){

			$querystatement = "
				SELECT
					`name`,
					`displayname`,
					`version`
				FROM
					`modules`
				ORDER BY
					`id`";

			$this->queryresult = $this->db->query($querystatement);

		}//end function get


		function show(){

			if(!$this->queryresult)
				return false;

			while($therecord = $this->db->fetchArray($this->queryresult)){

				if($therecord["name"] != "base")
					echo formatVariable($therecord["displayname"]).": v".$therecord["version"]."<br />";
				else
					echo '<p class="important">v'.$therecord["version"].'</p>';

			}//endwhile

			return true;

		}//end function show

	}//end class

	$versions = new versions($db);
	$versions->get();

	$pageTitle="phpBMS Information";
	$phpbms->cssIncludes[] = "pages/info.css";

	$phpbms->showMenu = false;
	$phpbms->showFooter = false;

	include("header.php");

?><div class="bodyline" id="container">

	<div id="phpBMSLogo" class="box small">
		<?php $versions->show() ?>
	</div>

	<h1>About phpBMS</h1>
	<p>phpBMS is commercial, open source, web-based, business management software.</p>

	<div id="companyInfo">
		<p >
			Copyright &copy; <?php echo date("Y")?> Kreotek, LLC. All Rights Reserved.
			phpBMS, and the phpBMS logo are trademarks of Kreotek, LLC.
			Software is licensed under a <a href="license.txt">modified BSD license</a>.
		</p>

		<h3>Kreotek, LLC</h3>
		<p>
			610 Quantum<br />
			Rio Rancho, NM 87124 USA
		</p>
		<p><a href="http://www.kreotek.com">http://www.kreotek.com</a></p>
		<p>
			sales: <a href="mailto:sales@kreotek.com">sales@kreotek.com</a><br />
			support: <a href="mailtosupport@kreotek.com">support@kreotek.com</a>
		</p>
		<p>
			U.S. and Canada Toll Free<br />
			1-800-731-8026

		<p>
			Outside US and Canada<br />
			+1-505-994-6388
		</p>

		<h3>phpBMS Open Source Project</h3>
		<p>
			community web site: <a href="http://www.kreotek.com">http://www.phpbms.org</a><br />
			community forums: <a href="http://www.phpbms.org/forum">http://www.phpbms.org/forum</a><br />
		</p>
	</div>

	<h2>Source Code</h2>
	<ul>
		<li>
			<h3>phpBMS (<a href="http://www.phpbms.org">www.phpbms.org</a>)</h3>
			<p>Commercial Open Source Business Management Web Appllication</p>
		</li>

		<li>
			<h3>fpdf (<a href="http://www.fpdf.org">www.fpdf.org</a>)</h3>
			<p>A PHP class which allows to generate PDF files with pure PHP</p>
		</li>

		<li>
			<h3>moo.fx (<a href="http://moofx.mad4milk.net/">moofx.mad4milk.net</a>)</h3>
			<p>Super lightweight JavaScript effects library</p>
		</li>

		<li>
			<h3>mochikit (<a href="http://mochichit.com/">mochikit.com</a>)</h3>
			<p>A lightweight JavaScript library - phpBMS utilizes modified parts of mochikit code and it's programming structure for JavaScript as inspiration.</p>
		</li>

		<li>
			<h3>parseCSV (<a href="http://code.google.com/p/parsecsv-for-php/" >code.google.com/p/parsecsv-for-php/</a>)</h3>
			<p>An easy to use PHP class to read and write CSV data properly.</p>
		</li>
	</ul>

	<p align="right"><input type="button" value="Log In" class="Buttons" onclick="document.location='./'" id="loginButton" /></p>

</div><?php include("footer.php") ?>
