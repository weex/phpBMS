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

class clientList{

	function clientList($db){
		
		$this->db = $db;
		
	}//end method


	function getData($id){

		$returnArray = array(
			"address1" => "",
			"address2" => "",
			"city" => "",
			"state" => "",
			"postalcode" => "",
			"country" => "",
			
			"hascredit" => 0,
			"creditlimit" => 0,
			"creditleft" => 0,

			"paymentmethodid" => 0,
			"shippingmethodid" => 0,
			"discountid" => 0,
			"taxareaid" => 0
		);

		$querystatement = "SELECT *	FROM clients WHERE id=".((int) $id);
		$queryresult = $this->db->query($querystatement);
		
		$therecord = $this->db->fetchArray($queryresult);

		if($therecord){		
			foreach($returnArray as $key =>$value)
				if($key != "creditleft")
					$returnArray[$key] = $therecord[$key];
		
			if($therecord["shiptoaddress1"])
				foreach($returnArray as $key =>$value)
					if(strpos($key,"shipto") !== false)
						$returnArray[str_replace("shipto","",$key)] = $therecord[$key];
		}//endif
		
		if($therecord["hascredit"]){
			//remaining AR
			$querystatement = "SELECT SUM(`amount` - `paid`) AS amtopen FROM aritems WHERE `status` = 'open' AND clientid=".((int) $id)." AND posted=1";
			$queryresult = $this->db->query($querystatement);
			
			$arrecord = $this->db->fetchArray($queryresult);
			
			$returnArray["creditleft"] = $therecord["creditlimit"] - $arrecord["amtopen"];
			
		}//end if
		
		return $returnArray;
	}//end method

	
	function showXML($therecord){
	
		header('Content-Type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>';
		echo '<response>';
		
		foreach($therecord as $key => $value){

		  ?>
		  <field><?php echo xmlEncode($key); ?></field>
		  <value><?php echo xmlEncode($value); ?></value>
		  <?php
			
		}//endforeach
		
		echo '</response>';
		
	}//end method
	
}//end class


//processing
//=========================================================================
if(isset($_GET["id"])){

	$clientInfo = new clientList($db);
	
	$therecord = $clientInfo->getData($_GET["id"]);
	
	$clientInfo->showXML($therecord);
	
}//end if
?>