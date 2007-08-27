<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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

	include("../../include/session.php");
	$querystatement="SELECT address1, address2, city, state, postalcode, country,
						shiptoaddress1, shiptoaddress2, shiptocity, shiptostate, shiptopostalcode, shiptocountry,
						paymentmethodid,shippingmethodid,discountid,taxareaid
						FROM clients
						WHERE clients.id=".((integer)$_GET["id"]);
	$queryresult = $db->query($querystatement);
	
	if($db->numRows($queryresult)) {
		$therecord=$db->fetchArray($queryresult);
		
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
		$therecord["paymentmethodid"]=0;
		$therecord["shippingmethodid"]=0;
		$therecord["discountid"]=0;
		$therecord["taxareaid"]=0;
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

  <field>paymentmethodid</field>
  <value><?php echo xmlEncode($therecord["paymentmethodid"]) ?></value>

  <field>shippingmethodid</field>
  <value><?php echo xmlEncode($therecord["shippingmethodid"]) ?></value>

  <field>discountid</field>
  <value><?php echo xmlEncode($therecord["discountid"]) ?></value>

  <field>taxareaid</field>
  <value><?php echo xmlEncode($therecord["taxareaid"]) ?></value>
</response>