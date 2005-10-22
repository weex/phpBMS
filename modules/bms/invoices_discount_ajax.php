<?PHP
	require("../../include/session.php");
	
	if(!isset($_GET["id"])) reportError(300,"Passed veriable not set (id)");
	
	$querystatement="SELECT if(discounts.type+0=1,concat(discounts.value,\"%\"),discounts.value) AS value FROM discounts WHERE id=".$_GET["id"];
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reporError(100,"Discount could not be retrieved");
	if(mysql_num_rows($queryresult))
		$therecord=mysql_fetch_array($queryresult);
	else
		$therecord["value"]=0;
		
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>';
?>
<response>
  <value><?php echo $therecord["value"] ?></value>
</response>