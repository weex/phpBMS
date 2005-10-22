<?PHP
	include("../../include/session.php");
	$querystatement="SELECT address1, address2, city, state, postalcode, country,
						shiptoaddress1, shiptoaddress2, shiptocity, shiptostate, shiptopostalcode, shiptocountry,
						paymentmethod,ccnumber,ccexpiration
						from clients where id=".$_GET["id"];
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reporError(500,"Cannot retrieve Client info");
	
	if(mysql_num_rows($queryresult)) {
		$therecord=mysql_fetch_array($queryresult);
		
		if ($therecord["shiptoaddress1"]){
			$theadd1=$therecord["shiptoaddress1"];
			$theadd2=$therecord["shiptoaddress2"];
			$thecity=$therecord["shiptocity"];
			$thestate=$therecord["shiptostate"];
			$thepostalcode=$therecord["shiptopostalcode"];
			$thecountry=$therecord["shiptocountry"];
		}
		else {
			$theadd1=$therecord["address1"];
			$theadd2=$therecord["address2"];
			$thecity=$therecord["city"];
			$thestate=$therecord["state"];
			$thepostalcode=$therecord["postalcode"];
			$thecountry=$therecord["country"];		
		}
	} else {
		$theadd1="";
		$theadd2="";
		$thecity="";
		$thestate="";
		$thepostalcode="";
		$thecountry="";
		$therecord["paymentmethod"]="";
		$therecord["ccnumber"]="";
		$therecord["ccexpiration"]="";
	}
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>';
?>
<response>
  <field>address1</field>
  <value><?php echo xmlEncode($theadd1) ?></value>

  <field>address2</field>
  <value><?php echo xmlEncode($theadd2) ?></value>

  <field>city</field>
  <value><?php echo xmlEncode($thecity) ?></value>

  <field>state</field>
  <value><?php echo xmlEncode($thestate) ?></value>

  <field>postalcode</field>
  <value><?php echo xmlEncode($thepostalcode) ?></value>

  <field>country</field>
  <value><?php echo xmlEncode($thecountry) ?></value>

  <field>paymentmethod</field>
  <value><?php echo xmlEncode($therecord["paymentmethod"]) ?></value>

  <field>ccnumber</field>
  <value><?php echo xmlEncode($therecord["ccnumber"]) ?></value>

  <field>ccexpiration</field>
  <value><?php echo xmlEncode($therecord["ccexpiration"]) ?></value>
</response>