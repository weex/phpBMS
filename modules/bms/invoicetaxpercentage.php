<?PHP
	require("../../include/session.php");

	$querystatement="SELECT percentage FROM tax where id=".$_GET["id"];
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reporError(100,"Tax percentage could not be retrieved");
	if(mysql_num_rows($queryresult))
		$therecord=mysql_fetch_array($queryresult);
	else
		$therecord["percentage"]=0;
		
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>';
?>
<response>
  <value><?php echo $therecord["percentage"] ?></value>
</response>