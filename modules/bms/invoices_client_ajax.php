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
	$querystatement="SELECT address1, address2, city, state, postalcode, country, tax.name as taxname,
						shiptoaddress1, shiptoaddress2, shiptocity, shiptostate, shiptopostalcode, shiptocountry,
						paymentmethod,ccnumber,ccexpiration
						FROM clients LEFT JOIN tax on clients.taxareaid=tax.id 
						WHERE clients.id=".((integer)$_GET["id"]);
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(500,"Cannot retrieve Client info: ".mysql_error($dblink)." <br/><br/> ".$querystatement);
	
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
		$therecord["taxname"]="";
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

  <field>ds-taxareaid</field>
  <value><?php echo xmlEncode($therecord["taxname"]) ?></value>
</response>