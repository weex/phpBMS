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
	require("../include/session.php");
	if(!isset($_GET["tid"])) $error = new appError(200,"URL variable missing: tid");
	if(!is_numeric($_GET["tid"])) $error = new appError(300,"URL variable invalid type: tid");
	
	if($_SESSION["printing"]["sortorder"])
		$sortorder=$_SESSION["printing"]["sortorder"];
	else
		$sortorder="";
	
	$querystatement="SELECT maintable,displayname FROM tabledefs WHERE id=".((int $_GET["tid"]);
	$thequery=$db->query($querystatement);                   
	if(!$thequery)	$error = new appError(100,"Could not retrieve table information");
	$therecord=$db->fetchArray($thequery);
	
	$querystatement="SELECT * FROM ".$therecord["maintable"]." ".$_SESSION["printing"]["whereclause"].$sortorder;
	$thequery=$db->query($querystatement);                   

	$num_fields=$db->numFields($thequery);
?>
<html>
<head>
<title><?php echo $therecord["displayname"]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css">
<!--
BODY,TH,TD{
	font-size : 10px;
	font-family : sans-serif;
	color : Black; 
}
TABLE{border:2px solid black;border-bottom-width:1px;border-right-width:1px;}
TH, TD{ padding:2px; border-right:1px solid black;border-bottom:1px solid black;}
TH {
	font-size:11px;
	font-weight: bold;
	border-bottom-width:2px;
}
-->
</style>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0">
<tr>
<?php 
	for($i=0;$i<$num_fields;$i++){
		echo "<th>".$db->fieldName($thequery,$i)."</th>";
	}
?>
</tr>
<?php 
	while($therecord=$db->fetchArray($thequery)){
		echo "<TR>\n";
		for($i=0;$i<$num_fields;$i++){
			echo "<TD>".($therecord[$i]?$therecord[$i]:"&nbsp;")."</td>\n";
		}
		echo "</TR>\n";
	}
?>
</table>
</body>
</html>