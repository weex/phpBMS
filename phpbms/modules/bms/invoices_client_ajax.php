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

	var $recordcount = 0;

	function clientList($db){
		
		$this->db = $db;
		
	}//end method


	function getSingleRecord($id){

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
		
		$this->recordcount = 1;
		
		return $returnArray;
		
	}//end method


	function findRecords($term, $offset=0){
	
		$term = trim(mysql_real_escape_string($term));
		
		$terms = explode(" ",$term);
		
		
		$prospects="";
		
		if(!PROSPECTS_ON_ORDERS)
			$prospects = "AND clients.type = 'client'";
		
		$wheres="";
		foreach($terms as $value){
		
			$wheres .="
				AND (
					clients.company LIKE '".$value."%'
					OR clients.company LIKE '% ".$value."%'
					OR clients.firstname LIKE '".$value."%'
					OR clients.lastname LIKE '".$value."%'
					OR clients.lastname LIKE '%-".$value."%'
				)";
		
		}//endforeach
	
		$querystatement = "
			SELECT
				id,
				firstname,
				lastname,
				company,
				type,
				city,
				state,
				postalcode,
				country
			FROM
				clients
			WHERE
				clients.inactive = 0
				".$prospects."
				".$wheres."
			ORDER BY
				IF(ISNULL(clients.company),'Z',clients.company),
				IF(ISNULL(clients.lastname),'Z',clients.lastname),
				clients.firstname
			LIMIT ".((int) $offset).", 8";

		$countstatement = "
			SELECT 
				COUNT(id) AS thecount
			FROM
				clients
			WHERE
				clients.inactive = 0
				".$prospects."
				".$wheres;

		$queryresult = $this->db->query($countstatement);
		$therecord = $this->db->fetchArray($queryresult);
		$this->recordcount = $therecord["thecount"];
				
		return $this->db->query($querystatement);
	
	}//end method

	
	function showXML($result){
	
		header('Content-Type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>';
		echo '<response total="'.$this->recordcount.'">';
		
		
		if(is_array($result)){

			foreach($result as $key => $value){
	
				?><field name="<?php echo xmlEncode($key); ?>" value="<?php echo xmlEncode($value); ?>" />
				<?php
				
			}//endforeach

		} else {
		
			while($therecord = $this->db->fetchArray($result)){
			
				echo '<record>';
	
				foreach($therecord as $key => $value){
		
					?><field name="<?php echo xmlEncode($key); ?>" value="<?php echo xmlEncode($value); ?>" />
					<?php
					
				}//endforeach
	
				echo '</record>';
				
			}//endwhile
			
		}//endif		
		
		echo '</response>';
		
	}//end method
	
}//end class


//processing
//=========================================================================
if(isset($_GET["w"])){

	$clientInfo = new clientList($db);
	
	switch($_GET["w"]){
	
		case "id":
			$theresult = $clientInfo->getSingleRecord($_GET["t"]);
			break;
			
		case "term":
			if(!isset($_GET["o"]))
				$_GET["o"] = 0;
			$theresult = $clientInfo->findRecords($_GET["t"],((int) $_GET["o"]));
			break;
	
	}//endswtich
	
	if(isset($theresult))	
		$clientInfo->showXML($theresult);
	
}//end if
?>