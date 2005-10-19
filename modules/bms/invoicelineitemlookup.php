<?PHP
	include("../../include/session.php");

	if (!isset($_GET["cid"])) $_GET["cid"]="0";
	if (!$_GET["cid"]) $_GET["cid"]="0";	
	$prereqnotmet=false;
	
	//check prerequisites
	$prereqstatement="select childid from prerequisites where parentid =".$_GET["id"];
	$prereqquery = mysql_query($prereqstatement,$dblink);
	
	if (mysql_num_rows($prereqquery)) {
		$checkpids="";
		while ($prereqrecord= mysql_fetch_array($prereqquery))
			$checkpids=$checkpids." or lineitems.productid=".$prereqrecord["childid"];

		$checkpids=substr($checkpids,4);
		// See if they have ordered the stuff
		$prlookupstatement="SELECT invoices.id from 
			(clients inner join invoices on clients.id=invoices.clientid) 
			inner join lineitems on invoices.id = lineitems.invoiceid
			where clients.id=".$_GET["cid"]." and invoices.status != \"Void\" and invoices.status != \"Quote\" and
			(".$checkpids.")";
		$prquery=mysql_query($prlookupstatement,$dblink);
		if (!$prquery) reportError(100,mysql_error($dblink)." ".$prlookupstatement);
		if (!mysql_num_rows($prquery)){
			$prereqnotmet=true;
		}
	} // end if
	
	
	if(!$prereqnotmet) {
		$querystatement="Select id,partnumber,partname,unitprice,concat(\"\$\",format((unitprice),2)) as formatedprice, 
						description, weight, unitcost
						from products where id=".$id;
		$queryresult= mysql_query($querystatement,$dblink);
		$therecord=mysql_fetch_array($queryresult);
	} else {
		$therecord["id"]="Prerequisite Not Met";
		$therecord["partnumber"]="";
		$therecord["partname"]="";
		$therecord["unitprice"]="";
		$therecord["formatedprice"]="";
		$therecord["description"]="";
		$therecord["weight"]="";
		$therecord["unitcost"]="";
	}
	
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>';
?>
<response>
	<field>partnumber</field>
	<value><?php echo $therecord["id"]?></value>

	<field>ds-partnumber</field>
	<value><?php echo xmlEncode($therecord["partnumber"]) ?></value>
	
	<field>partname</field>
	<value><?php echo $therecord["id"] ?></value>

	<field>ds-partname</field>
	<value><?php echo xmlEncode($therecord["partname"]) ?></value>

	<field>memo</field>
	<value><?php echo xmlEncode($therecord["description"]) ?></value>

	<field>price</field>
	<value><?php echo xmlEncode($therecord["formatedprice"]) ?></value>

	<field>unitcost</field>
	<value><?php echo $therecord["unitcost"] ?></value>

	<field>unitweight</field>
	<value><?php echo $therecord["weight"] ?></value>
</response>