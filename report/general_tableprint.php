<?php 
	if(!isset($_GET["tabledefid"])) reportError(200,"URL variable missing: tabledefid");
	if(!is_numeric($_GET["tabledefid"])) reportError(300,"URL variable invalid type: tabledefid");
	
	if($_SESSION["printing"]["sortorder"])
		$sortorder=$_SESSION["printing"]["sortorder"];
	else
		$sortorder="";
	
	require("../include/session.php");
	
	$querystatement="SELECT maintable,displayname FROM tabledefs WHERE id=".$_GET["tabledefid"];
	$thequery=mysql_query($querystatement,$dblink);                   
	if(!$thequery)	reportError(100,"Could not retrieve table information");
	$therecord=mysql_fetch_array($thequery);
	
	$querystatement="SELECT * FROM ".$therecord["maintable"]." ".$_SESSION["printing"]["whereclause"].$sortorder;
	$thequery=mysql_query($querystatement,$dblink);                   

	if(!$thequery) die("Invalid sql statement. If you entered the where clause manually, make sure you include the word 'where'. ".$querystatement);	

	$num_fields=mysql_num_fields($thequery);
?>
<html>
<head>
<title><?php echo $therecord["displayname"]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
<?PHP 
	for($i=0;$i<$num_fields;$i++){
		echo "<th>".mysql_field_name($thequery,$i)."</th>";
	}
?>
</tr>
<?PHP 
	while($therecord=mysql_fetch_array($thequery)){
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