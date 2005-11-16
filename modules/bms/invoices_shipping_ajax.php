<?PHP
/*
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
 | All rights reserved.                                                    |
 +-------------------------------------------------------------------------+
 |                                                                         |
 | Redistribution and use in source and binary forms, with or without      |
 | modification, are permitted provided that the following conditions are  |
 | met:                                                                    |
 |                                                                         |
 | - Redistributions of source code must retain the above copyright        |
 |   notice, this list of conditions and the following disclaimer.         |
 |                                                                         |
 | - Redistributions in binary form must reproduce the above copyright     |
 |   notice, this list of conditions and the following disclaimer in the   |
 |   documentation and/or other materials provided with the distribution.  |
 |                                                                         |
 | - Neither the name of Kreotek LLC nor the names of its contributore may |
 |   be used to endorse or promote products derived from this software     |
 |   without specific prior written permission.                            |
 |                                                                         |
 | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS     |
 | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT       |
 | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A |
 | PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
 | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,   |
 | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT        |
 | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,   |
 | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY   |
 | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT     |
 | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE   |
 | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.    |
 |                                                                         |
 +-------------------------------------------------------------------------+
*/

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