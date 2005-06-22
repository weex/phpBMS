<?PHP
	include("../../include/session.php");

	$thequery="SELECT 	id,partnumber,partname
						from products where id=".$_GET["id"];
	$thequery = mysql_query($thequery,$dblink);
	$therecord=mysql_fetch_array($thequery);
	
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>';
?>
<response>
	<field>partnumber</field>
	<value><?php echo $therecord["id"];?></value>

	<field>ds-partnumber</field>
	<value><?php echo xmlEncode($therecord["partnumber"]);?></value>

	<field>partname</field>
	<value><?php echo xmlEncode($therecord["id"]);?></value>

	<field>ds-partname</field>
	<value><?php echo xmlEncode($therecord["partname"]);?></value>
</response>