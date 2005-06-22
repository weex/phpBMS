<?PHP
	include("../../include/session.php");
	if(!$id) $id=-2;

  	$thequery="select templineitems.quantity templineitems.unitweight as unitweight, products.isoversized, isprepackaged, packagesperitem
				from templineitems left join products on templineitems.productid=products.id where templineitems.invoiceid=".$id." and templineitems.sessionid=\"".session_id()."\" 
				and sessionid=\"".session_id()."\";";
	$theresult=mysql_query($thequery);

	while ($therecord=mysql_fetch_array($result)){
		if($therecord[prepackage]){
			if($therecord[oversized])
				$over="yes";
			else
				$over="no";
			//make sure to check for errors
			$total_shipping=$total_shipping+$therecord[quantity]*ups_price($shipmethod,"87124",$theinvoice[postalcode],$therecord[weight],$over);
		}
		else {
			// for non prepackaged add up total weight and total packages
			$total_pckges+=$therecord[quantity]*$therecord[packagesperitem];
			$total_weight+=$therecord[quantity]*$therecord[unitweight];
		}
	}// end while

	//make sure to check for errors
	$total_pckges=ceiling($total_pckges);
	$avg_weight=$total_weight/total_pckges;
	//check for errors on price
	$shipping+=ups_price($shipmethod,"87124",$theinvoice[postalcode],$therecord[avg_weight]);
	$total_shipping=$total_shipping+($shipping*$total_pckges);
	//mark up
	$total_shipping=$total_shipping*1.1;
?>