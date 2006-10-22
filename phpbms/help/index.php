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

	$loginNoKick=true;
	$loginNoDisplayError=true;
	require("../include/session.php");	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>phpBMS Resources</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("head.php")?>
</head>

<body>
<div class="bodyline" style="width:700px;">
	<h1>phpBMS Resources</h1>
	<ul>
		<li><a href="http://phpbms.org/wiki/PhpbmsGuide"><strong>PhpbmsGuide</strong></a> (at phpbms.org)
			<div class=small>User and Administrator Guide.</div>
		</li>
		<li>
			<a href="http://www.kreotek.com/products/phpbms/tutorials"><strong>Tutorials</strong></a> <em>(at kreotek.com)</em>
			<div class=small>Step by step examples on how to perform common tasks inside phpBMS.</div>
		</li>
		<li>
			<strong><a href="http://phpbms.org/wiki/PhpbmsFaq">PhpbmsFaq</a></strong> <em>(at phpbms.org)</em>
			<div class=small>Frequently Asked Questions </div>
		</li>
		<li>
			<a href="shortcuts.php"><strong>Keyboard Shortcuts</strong></a>
			<div class=small>List of keyboard shortcuts for use in common areas in phpBMS.</div>
		</li>
		<li>
			<a href="<?php echo $_SESSION["app_path"]?>info.php"><strong>About phpBMS</strong></a>
			<div class=small>Basic Program &amp; technology information.</div>
		</li>
	</ul>
</div>
</body>
</html>