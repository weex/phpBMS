<?PHP
	include("../../include/session.php");
	include("include/ups_price.php");
	if(!$_GET["id"]) $_GET["id"]=-2;

	switch($_GET["shipvia"]){
		case "UPS Ground (Res)":
			$shippingmethod="GNDRES";
		break;
		case "UPS Ground (Com)":
			$shippingmethod="GNDCOM";
		break;
		case "UPS 2nd Day Air":
			$shippingmethod="2DA";
		break;
		case "UPS 3 Day Select":
			$shippingmethod="3DS";
		break;
		case "UPS Next Day Air":
			$shippingmethod="1DA";
		break;
	}
  	$querystatement="SELECT templineitems.quantity as quantity, templineitems.unitweight as unitweight, 
				isoversized, isprepackaged, packagesperitem
				FROM templineitems LEFT JOIN products ON templineitems.productid=products.id 
				WHERE templineitems.invoiceid=".$_GET["id"]." AND templineitems.sessionid=\"".session_id()."\" ";
				
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,mysql_error($dblink)." ".$querystatement);
	
	$total_pckges=0;
	$total_shipping=0;
	$total_weight=0;
	
	while ($therecord=mysql_fetch_array($queryresult)){
		if($therecord["isprepackaged"]){
			$UPSreturn=UPSprice($shippingmethod,$_SESSION["shipping_postalcode"],$_GET["postalcodeto"],$therecord["unitweight"]);
			if (!$UPSreturn["success"]) $shipping=0; else $shipping=$UPSreturn["charge"];
			$total_shipping+=($shipping*$therecord["quantity"]);
		}
		else {
			// for non prepackaged add up total weight and total packages
			$total_pckges+=$therecord["quantity"]*$therecord["packagesperitem"];
			$total_weight+=$therecord["quantity"]*$therecord["unitweight"];
		}
	}// end while

	//make sure to check for errors
	
	$total_pckges=ceil($total_pckges);
	if($total_pckges){
		$avg_weight=$total_weight/$total_pckges;
		//check for errors on price
		$UPSreturn=UPSprice($shippingmethod,$_SESSION["shipping_postalcode"],$_GET["postalcodeto"],$avg_weight);
		if (!$UPSreturn["success"]) $shipping=0; else $shipping=$UPSreturn["charge"];
			$total_shipping+=($shipping*$total_pckges);
	}//end if
	
	//mark up
	$total_shipping=$total_shipping*$_SESSION["shipping_markup"];
	
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>';
?>
<response>
	<value><?php echo $total_shipping?></value>
</response>