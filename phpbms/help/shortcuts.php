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
	$pageTitle="Keyboard Shortcuts";
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("../head.php")?>
</head>

<body>
<div class="bodyline" style="width:700px;">
	<h1><?php echo $pageTitle?></h1>
	
	<h2>List/Search Screens</h2>
	<div>
		<table border="0" cellpadding="0" cellspacing="0" class="querytable" width="250">
			<TR>				
				<Th rowspan=2 valign="bottom" class="queryheader" align="right">Command</Th>
				<th colspan=2 class="queryheader" align="center">Modifier</th>
				<th rowspan=2 valign="bottom" class="queryheader" align="center">Key</th>
			</TR>
			<TR>
				<th class="queryheader">PC</th>
				<th class="queryheader">Mac</th>
			</TR>
			<TR class="qr1" style="cursor:auto">
				<TD align="right" style="width:100%">New Record</TD>
				<td align="center">Alt</td>
				<td align="center">Ctrl</td>
				<td align="center">N</td>
			</TR>
			<TR class="qr2" style="cursor:auto">
				<TD align="right">Edit Record</TD>
				<td align="center">Alt</td>
				<td align="center">Ctrl</td>
				<td align="center">E</td>
			</TR>
			<TR class="qr1" style="cursor:auto">
				<TD align="right">Print</TD>
				<td align="center">Alt</td>
				<td align="center">Ctrl</td>
				<td align="center">P</td>
			</TR>
			<TR class="qr2" style="cursor:auto">
				<TD align="right">Delete (where applicable)</TD>
				<td align="center">Alt</td>
				<td align="center">Ctrl</td>
				<td align="center">D</td>
			</TR>			
			<TR class="qr1" style="cursor:auto">
				<TD align="right">Select All</TD>
				<td align="center">Alt</td>
				<td align="center">Ctrl</td>
				<td align="center">A</td>
			</TR>			
			<TR class="qr2" style="cursor:auto">
				<TD align="right">Select None</TD>
				<td align="center">Alt</td>
				<td align="center">Ctrl</td>
				<td align="center">X</td>
			</TR>			
			<TR class="qr1" style="cursor:auto">
				<TD align="right">Keep Highlighted</TD>
				<td align="center">Alt</td>
				<td align="center">Ctrl</td>
				<td align="center">K</td>
			</TR>			
			<TR class="qr2">
				<TD align="right">Omit Highlighted</TD>
				<td align="center">Alt</td>
				<td align="center">Ctrl</td>
				<td align="center">O</td>
			</TR>			
		</table>
	<h2>Add/Edit Screens</h2>
		<table border="0" cellpadding="0" cellspacing="0" class="querytable" width="250">
			<TR>				
				<Th rowspan=2 valign="bottom" class="queryheader" align="right">Command</Th>
				<th colspan=2 class="queryheader" align="center">Modifier</th>
				<th rowspan=2 valign="bottom" class="queryheader" align="center">Key</th>
			</TR>
			<TR>
				<th class="queryheader">PC</th>
				<th class="queryheader">Mac</th>
			</TR>
			<TR class="qr1" style="cursor:auto">
				<TD align="right" style="width:100%">Save Record</TD>
				<td align="center">Alt</td>
				<td align="center">Ctrl</td>
				<td align="center">S</td>
			</TR>
			<TR class="qr2" style="cursor:auto">
				<TD align="right">Cancel</TD>
				<td align="center">Alt</td>
				<td align="center">Ctrl</td>
				<td align="center">X</td>
			</TR>
		</table>		
	</div>
</div>
</body>
</html>