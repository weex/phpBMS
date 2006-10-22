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
	$loginNoDisplayError=true;;
	require("include/session.php")
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>phpBMS Browser Requirements</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("head.php")?>
</head>

<body><div class="bodyline" style="width:700px;">	
	<h1>phpBMS Client Browser Requirements</h1>
	<div class="box" style="float:right;background-color:white;"><img src="common/image/logo-large.png" width="250" height="57" /></div>
	<div>
	<ul>
    	<li><strong class="large">JavaScript v2.0</strong><br />
        		<br />
		This application makes heavy use of newer JavaScript functions, both in doing form verification, and with the many AJAX routines. <br />
		<br />
		<strong>A note about window pop-ups:</strong>		This application does utilize Javascript to open new windows. If you disable Javascript window opening (like in Mozilla or Opera) or are utilizing a 3rd-party application to stop Internet Explorer (IE) from opening unwanted windows, this application might not work correctly.<br />
&nbsp; </li>
    	<li><strong class="large">Cookies</strong><br />
        		<br />
		A single cookie is set to identify the user during a session. <br />
&nbsp; </li>
    	<li><strong class="large">Style Sheets (CSS) v1.1</strong><br />
        		<br />
		Your browser must support the rendering of Cascading Style Sheets. The default style sheet scheme is supported on most browsers, but definitely looks best in Firefox. <br />
    		</li>
	</ul></div>
	<h2>Tested Browsers</h2>
	<div><table border="0" cellpadding="0" cellspacing="0" class="querytable">
    	<tr>
    		<th class="queryheader" nowrap>Browser Application</th>
    		<th class="queryheader">Version</th>
    		<th class="queryheader">Platform(s)</th>
    		<th align="center" class="queryheader">Compatibility</th>
   		</tr>
    	<tr class="qr1" style="cursor:auto">
        	<td>Firefox</td>
        	<td>1.0.7</td>
        	<td>Windows/Macintosh</td>
        	<td align="center" class="important">X</td>
   		</tr>
		
    	<tr class="qr2" style="cursor:auto">
    		<td>Internet Explorer</td>
    		<td>6.0.2800.1106</td>
    		<td>Windows</td>
    		<td align="center" class="important">X</td>
   		</tr>
   	</table>
	</div>
	<div class="box" align="right">
		<br />
		<input type="button" value="back to login page" class="Buttons" onClick="document.location='index.php'"/>&nbsp;&nbsp;<br/>
		<br />
	</div>
</div>
</body>
</html>
