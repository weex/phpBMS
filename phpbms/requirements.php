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
	$loginNoDisplayError=true;;
	require("include/session.php")
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>phpBMS Browser Requirements</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="common/stylesheet/mozilla/base.css" rel="stylesheet" type="text/css" />
<link href="common/stylesheet/mozilla/pages/requirements.css" rel="stylesheet" type="text/css" />
</head>

<body><div class="bodyline" id="container">	
	<div class="box" id="logo" style=""><span>phpBMS</span></div>
	<h1>phpBMS Browser Requirements</h1>
	<h2 id="cba">Client Browser Aplication</h2>

	<h3>JavaScript v2.0</h3>
	<p>This application makes heavy use of newer JavaScript functions, including the paradigm known as AJAX. Without Javascript, enabled, phpBMS will not run correctly.</p>

	<h3>Window pop-ups:</h3>
	<p>This application utilizes JavaScript to open new windows. If you disable Javascript window opening (like in Firefox or Opera) or are utilizing a 3rd-party application to stop Internet Explorer  from opening unwanted windows, this application might not work correctly.</p>

	<h3>Cookies</h3>
	<p>A single cookie is set to identify the user during a session.</p>

	<h3>Style Sheets (CSS) v1.1</h3>
	<p>Your browser must support the rendering of Cascading Style Sheets. Without this support, the application will not work correctly.</p>

	<h2>Tested Browsers</h2>
	<table border="0" cellpadding="0" cellspacing="0" class="querytable" id="browserTable">
    	<tr>
    		<th class="queryheader" nowrap="nowrap">Browser Application</th>
    		<th class="queryheader">Version</th>
    		<th class="queryheader">Platform(s)</th>
    		<th align="center" class="queryheader">Compatibility</th>
   		</tr>
    	<tr class="row2">
        	<td>Firefox</td>
        	<td>1.5.0.7</td>
        	<td>Windows/Macintosh</td>
        	<td align="center" class="important">X</td>
   		</tr>		
    	<tr class="row1">
    		<td>Internet Explorer</td>
    		<td>6.0.2900.2190</td>
    		<td>Windows</td>
    		<td align="center" class="important">X</td>
   		</tr>
    	<tr class="row2">
    		<td>Opera</td>
    		<td>9.02</td>
    		<td>Windows</td>
    		<td align="center" class="important">X</td>
   		</tr>
    	<tr class="row1">
    		<td>Internet Explorer</td>
    		<td>6.0.2900.2190</td>
    		<td>Windows</td>
    		<td align="center" class="important">X</td>
   		</tr>
		<tr class="queryfooter"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
   	</table>
	<p align="right">
		<input type="button" value="Log In" class="Buttons" onclick="document.location='./'" id="loginButton"/>
	</p>
</div>
</body>
</html>
