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
session_cache_limiter('private');

include("../../include/session.php");

class productLookup{

	function productLookup($db){

		$this->db = $db;

	}//end method - init


	function meetsPrereq($productid, $clientid){

		// This method return true if the client/product
		// combination return no prerequisites or
		// if all prerequisites products have been
		// at least ordered by the client.

		$querystatement = "
			SELECT
				childid
			FROM
				prerequisites
			WHERE
				parentid = '".mysql_real_escape_string($productid)."'
		";

		$queryresult = $this->db->query($querystatement);

		if($this->db->numRows($queryresult)){

			$whereclause = "";
			while($therecord = $this->db->fetchArray($queryresult))
				$whereclause .= " OR lineitems.productid = '".$therecord["childid"]."'";

			$whereclause = substr($whereclause, 4);

			$checkstatement = "
				SELECT
					invoices.id
				FROM
					invoices INNER JOIN lineitems ON lineitems.invoiceid = invoices.id
				WHERE
					invoices.clientid = '".mysql_real_escape_string($clientid)."'
					AND invoices.type != 'Void'
					AND invoices.type != 'Quote'
					AND (".$whereclause.")
				";

			if($this->db->numRows($this->db->query($checkstatement)) > 0 || $clientid == "");
				return false;

		}//endif - numRows

		return true;

	}//end method - checkPrereq


	function getInfo($productid){

		$querystatement = "
			SELECT
				*
			FROM
				products
			WHERE
				uuid = '".mysql_real_escape_string($productid)."'
		";

		return $this->db->fetchArray($this->db->query($querystatement));

	}//end method - getInfo


	function display($record){

		$output = "{ prereqMet: ";
		if($record){

			$record["memo"] = str_replace("\r", "", str_replace("\n", " ", $record["memo"]));
			$output .= "true, record: {";

			foreach($record as $key=>$value)
				$output .= $key.": '".str_replace("'","\\'",htmlQuotes($value))."',";

			$output = substr($output,0,-1)."}";

		} else {

			$output .= "false";

		}//endif - record

		$output .= "}";

		header("Content-type: text/plain");
		echo $output;

	}//end method display

}//end class - productLookup


//processing
//=========================================================================
if(isset($_GET["cid"]) && isset($_GET["id"])){

	$lookup = new productLookup($db);

	if($lookup->meetsPrereq($_GET["id"], $_GET["cid"]))
		$therecord = $lookup->getInfo($_GET["id"]);
	else
		$therecord = false;

	$lookup->display($therecord);

}//end if