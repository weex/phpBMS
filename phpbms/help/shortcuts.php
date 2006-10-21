<?php 
	$loginNoKick=true;
	$loginNoDisplayError=true;
	require("../include/session.php");	
	$pageTitle="Keyboard Shortcuts";
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
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