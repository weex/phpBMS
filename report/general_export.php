<?PHP
	if(!isset($_GET["tabledefid"])) reportError(200,"URL variable missing: tabledefid");
	if(!is_numeric($_GET["tabledefid"])) reportError(300,"URL variable invalid type: tabledefid");

	if($_SESSION["printing"]["sortorder"])
		$sortorder=$_SESSION["printing"]["sortorder"];
	else
		$sortorder="";
		
	require("../include/session.php");
	header("Content-type: text/plain");
	header('Content-Disposition: attachment; filename="export.txt"');
	
	$querystatement="SELECT maintable FROM tabledefs WHERE id=".$_GET["tabledefid"];
	$thequery=mysql_query($querystatement,$dblink);                   
	if(!$thequery)	reportError(100,"Could not retrieve table information");
	$therecord=mysql_fetch_array($thequery);
	
	$querystatement="SELECT * FROM ".$therecord["maintable"]." ".$_SESSION["printing"]["whereclause"].$sortorder;
	$thequery=mysql_query($querystatement,$dblink);                   

	if(!$thequery) die("Invalid sql statement. If you entered the where clause manually, make sure you include the word 'where'. ".$querystatement);
	$num_fields=mysql_num_fields($thequery);

	for($i=0;$i<$num_fields;$i++){
		echo mysql_field_name($thequery,$i).",";
	}
	echo "\n";

	while($therecord=mysql_fetch_array($thequery)){
		for($i=0;$i<$num_fields;$i++){
			echo "\"".$therecord[$i]."\",";
		}
		echo "\n";
	}
?>