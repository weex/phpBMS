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

class clientInfo{

	function clientInfo($db){

		$this->db = $db;

	}//end method - init


	function get($uuid){

		$uuid = mysql_real_escape_string($uuid);

		$return = $this->_getClient($uuid);

		$return["billingaddress"] = $this->_getAddress($uuid, "primary");

		$return["shiptoaddress"] = $this->_getAddress($uuid, "defaultshipto");

		if($return["hascredit"])
			$return["creditleft"] = $this->_getCreditLeft($uuid, $return["creditlimit"]);

		return $return;

	}//end method - get



	function _getClient($uuid){

		$returnArray = array(
			"hascredit" => 0,
			"creditlimit" => 0,
			"creditleft" => 0,
			"type" => "client",

			"paymentmethodid" => "",
			"shippingmethodid" => "",
			"discountid" => "",
			"taxareaid" => ""
		);

		$querystatement = "
			SELECT
				*
			FROM
				clients
			WHERE
				`uuid` = '".$uuid."'
		";

		$therecord = $this->db->fetchArray($this->db->query($querystatement));

		if($therecord){
			foreach($returnArray as $key =>$value)
				if($key != "creditleft")
					$returnArray[$key] = $therecord[$key];
		}//endif

		return $returnArray;

	}//end method - _getClient


	function _getAddress($clientID, $type){

		$returnArray = array(
			"id" => "",
			"uuid" => "",
			"address1" => "",
			"address2" => "",
			"city" => "",
			"state" => "",
			"postalcode" => "",
			"country" => "",
			"shiptoname" => "",
		);

		$querystatement = "
			SELECT
				addresses.*
			FROM
				addresstorecord INNER JOIN addresses ON addresstorecord.addressid = addresses.uuid
			WHERE
				addresstorecord.tabledefid = 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083'
				AND addresstorecord.recordid = '".$clientID."'
				AND addresstorecord.".$type." = 1";

		$therecord = $this->db->fetchArray($this->db->query($querystatement));

		if($therecord)
			foreach($returnArray as $key =>$value)
				$returnArray[$key] = $therecord[$key];

		return $returnArray;

	}//end method - _getAddress


	function _getCreditLeft($clientID, $creditlimit){

		$querystatement = "
			SELECT
				SUM(`amount` - `paid`) AS amtopen
			FROM
				aritems
			WHERE
				`status` = 'open'
				 AND clientid='".$clientID."'
				 AND posted=1";

		$arrecord = $this->db->fetchArray($this->db->query($querystatement));

		return  ((real) $creditlimit) - ((real) $arrecord["amtopen"]);

	}//end method - _getCreditLeft



	function display($record){

		$output = "{";

		foreach($record as $key=>$value){

			if(!is_array($value)){

				$output.= $key.":'".str_replace("'", "\'", formatVariable($value))."',";

			} else {

				$output .= $key.":{";

				foreach($value as $skey => $svalue)
					$output.= $skey.":'".str_replace("'", "\'", formatVariable($svalue))."',";

				$output = substr($output, 0, strlen($output)-1);

				$output .= "},";

			}//endif is_array

		}//endforeach - record

		$output = substr($output, 0, strlen($output)-1);

		$output .= "}";

		header("Content-type: text/plain");
		echo $output;

	}//end method - display

}//end class


//processing
//=========================================================================
if(isset($_GET["id"])){

	$clientInfo = new clientInfo($db);

	$clientRecord = $clientInfo->get($_GET["id"]);

	$clientInfo->display($clientRecord);

}//end if
?>