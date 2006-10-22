<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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
		$querystatement="SELECT id,partnumber,partname,unitprice,concat(\"\$\",format((unitprice),2)) as formatedprice, 
						description, weight, unitcost, taxable
						FROM products WHERE id=".$_GET["id"];
		$queryresult= mysql_query($querystatement,$dblink);
		$therecord=mysql_fetch_array($queryresult);
	} else {
		$therecord["id"]="Prerequisite Not Met";
		$therecord["partnumber"]="";
		$therecord["partname"]="";
		$therecord["unitprice"]="";
		$therecord["formatedprice"]="";
		$therecord["description"]="";
		$therecord["taxable"]="";
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

	<field>taxable</field>
	<value><?php echo xmlEncode($therecord["taxable"]) ?></value>

	<field>memo</field>
	<value><?php echo xmlEncode($therecord["description"]) ?></value>

	<field>price</field>
	<value><?php echo xmlEncode($therecord["formatedprice"]) ?></value>

	<field>unitcost</field>
	<value><?php echo $therecord["unitcost"] ?></value>

	<field>unitweight</field>
	<value><?php echo $therecord["weight"] ?></value>
</response>