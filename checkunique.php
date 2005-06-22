<?PHP
	include("include/session.php");

	$name=stripslashes($_GET["name"]);

	if(!isset($_GET["excludeid"])) $_GET["excludeid"]="0";
	if(!$_GET["excludeid"]) $_GET["excludeid"]="0";
	$querystatement="SELECT id FROM ".$_GET["table"]." WHERE id!=".$_GET["excludeid"]." and ".$_GET["field"]."=\"".$_GET["value"]."\";";
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) die($querystatement);
	$numrows=mysql_num_rows($queryresult);
	
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>';
?>
<response>
  <name><?php echo $_GET["name"]; ?></name>
  <isunique><?php if($numrows) echo "0"; else echo "1"; ?></isunique>
</response>