<?php
/*
 $Rev: 145 $ | $LastChangedBy: mipalmer $
 $LastChangedDate: 2006-10-21 18:27:44 -0600 (Sat, 21 Oct 2006) $
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2007, Kreotek LLC                                  |
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

	session_cache_limiter('private');
	require("../../include/session.php");
	require("include/ups_price.php");

	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>';
	
	if(!isset($_GET["shipvia"]) || !isset($_GET["shiptozip"])){
	?><response>
	<value>0</value>
</response><?php
		exit();
	}
		
	//SWITCH OUT THE SHIPPING METHOD
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
	
	$total_pckges=0;
	$total_shipping=0;
	$total_weight=0;

	foreach($_GET as $name=>$item){
		if(strpos($name,"LI")!==false){
			//convert data to array
			$lineitem=explode("[//]",$item);
			//grab info from product.			
			$querystatement="SELECT isprepackaged,weight,packagesperitem FROM products WHERE id=".trim($lineitem[0]);
			$queryresult=$db->query($querystatement);

			$therecord=$db->fetchArray($queryresult);
			$therecord["quantity"]=trim($lineitem[4]);
			
			//get the pricing
			if($therecord["isprepackaged"]){
				$UPSreturn=UPSprice($shippingmethod,SHIPPING_POSTALCODE,$_GET["postalcodeto"],$therecord["weight"]);
				if (!$UPSreturn["success"]) $shipping=0; else $shipping=$UPSreturn["charge"];
				$total_shipping+=($shipping*$therecord["quantity"]);
			}
			else {
				// for non prepackaged add up total weight and total packages
				$total_pckges+=$therecord["quantity"]*$therecord["packagesperitem"];
				$total_weight+=$therecord["quantity"]*$therecord["weight"];
			}
		}
	}
	
	$total_pckges=ceil($total_pckges);

	if($total_pckges){
		$avg_weight=$total_weight/$total_pckges;
		//check for errors on price
		$UPSreturn=UPSprice($shippingmethod,SHIPPING_POSTALCODE,$_GET["shiptozip"],$avg_weight);
		if (!$UPSreturn["success"]) $shipping=0; else $shipping=$UPSreturn["charge"];
			$total_shipping+=($shipping*$total_pckges);
	}//end if
	
	//mark up
	$total_shipping=$total_shipping*SHIPPING_MARKUP;
	
?>
<response>
	<value><?php echo $total_shipping?></value>
</response>