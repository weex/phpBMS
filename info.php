<?php 
/*
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
	require("include/session.php");
	
	if(isset($dblink)){
		$querystatement="SELECT displayname,version from modules ORDER BY id";
		$queryresult=mysql_query($querystatement,$dblink);
	} else
		$queryresult="";
	
	function displayVersions($queryresult){
		if($queryresult){
			while($therecord=mysql_fetch_array($queryresult)){
				if($therecord["displayname"]!="Base"){
					echo $therecord["displayname"].": ";
					echo "v".$therecord["version"]."<br />";
				} else
					echo "<span class=\"important\">v".$therecord["version"]."</span><br /><br />";
			}
		}
	}
	
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>phpBMS Information</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css" />
	<style>li{margin-bottom:4px;}</style>
</head>

<body><?php if(isset($_SESSION["app_path"])) include("menu.php")?>
<div class="bodyline" style="width:700px;">
	<h1 style="margin-bottom:20px;">Commercial Open Source Business Management Web Application</h1>
	<div style="float:right;background-color:white;padding:10px;margin-right:20px;" align="right" class="box small">
		<img src="common/image/logo-large.png" width="250" height="57" /><br />
		<?php displayVersions($queryresult)?>
	</div>
	<div style="width:300px;padding-bottom:0px;">
		<div class="small">
			Copyright &copy; 2004 -2005 kreotek, llc. All Rights Reserved.
			phpBMS, and the phpBMS logo are trademarks of kreotek, llc.
		</div>
		<div style="padding:10px; padding-bottom:0px;">
			<div class="large important">Kreotek, LLC</div>
			<div style="" class="small">
				481 Rio Rancho Blvd.<br />
				Rio Rancho, NM 87124
			</div>		
			<div  class="important" style="padding-bottom:0px;">Contact Information</div>
			<div style="padding-top:0px;" class="small">
				web: <a href="http://www.kreotek.com">http://www.kreotek.com</a><br />
				sales: <a href="mailto:sales@kreotek.com">sales@kreotek.com</a><br />
				support: <a href="mailtosupport@kreotek.com">support@kreotek.com</a><br />
				phone: <strong>1-800-731-8026</strong>
			</div>
		</div>
	</div>
	<div style="padding-top:0px;">
		<h2>Source Code</h2>
		<ul>
			<li><strong>phpBMS</strong> - Commercial Open Source Business Management Web Appllication (<a href="http://www.kreotek.com">www.kreotek.com</a>)</li>
			<li><strong>fpdf</strong> - A PHP class which allows to generate PDF files with pure PHP (<a href="http://www.fpdf.org">www.fpdf.org</a>)</li>
		</ul>
		<h2>Technologies</h2>
		<ul>
			<li><strong>php</strong> - 
		   A widely-used general-purpose scripting language that is especially suited for Web development and can be embedded into HTML.  (<a href="http://www.php.net">www.php.net</a>)</li>
			<li><strong>MySQL</strong> - An open source relational database management system (RDBMS) that uses Structured Query Language (SQL) (<a href="http://www.mysql.org">www.mysql.org</a>)</li>
			<li><strong>AJAX</strong> - Asynchronous Javascript And XML is a group of technologies that help browser based applications behave more like applications you run from your desktop.</li>
		</ul>
	</div>
	<?php if(!isset($_SESSION["app_path"])) {?>
	<div class="box" align="right" style="clear:both">
		<br />
		<input type="button" value="back to login page" class="Buttons" onClick="document.location='index.php'"/>&nbsp;&nbsp;<br/>
		<br />
	</div>
	<?php } ?>
</div>
</body>
</html>
