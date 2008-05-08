<?php
/*
 $Rev: 254 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-07 18:38:38 -0600 (Tue, 07 Aug 2007) $
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
if(class_exists("addresses")){
	class addresstorecord extends addresses{
	

		function createAddressToRecord($variables, $addressid, $createdby = NULL){
			// This functions adds the addresstorecord record tying the client and address
			// records.
		
			if(!$createdby && isset($_SESSION["userinfo"]["id"]))
				$createdby = $_SESSION["userinfo"]["id"];
				
			$insertstatement = "
				INSERT INTO
					addresstorecord
				(
					tabledefid,
					recordid,
					defaultshipto,
					`primary`,
					addressid,
					createdby,
					creationdate,
					modifiedby,
					modifieddate
				) VALUES (
					".((int) $variables["tabledefid"]).",
					".((int) $variables["recordid"]).",
					".((int) $variables["defaultshipto"]).",
					".((int) $variables["primary"]).",
					".((int) $addressid).",
					".((int) $createdby).",
					NOW(),
					".((int) $createdby).",
					NOW()					
				)";
			
			$this->db->query($insertstatement);
			
			return $this->db->insertID();
			
		}//end method - createAddressToRecord
		
		// CLASS OVERRIDES ===============================================
		// ===============================================================
		function getRecord($id){
			$id = (int) $id;
			
			$querystatement = "
				SELECT
					*	
				FROM 
					addresstorecord
				WHERE 
					id = ".$id;			
					
			$queryresult = $this->db->query($querystatement);
			if($this->db->numRows($queryresult)) { 
			
				$therecord = $this->db->fetchArray($queryresult);
				$addressrecord = parent::getRecord($therecord["addressid"]);

				unset($addressrecord["id"], $addressrecord["createdby"], $addressrecord["creationdate"], $addressrecord["modifiedby"], $addressrecord["modifieddate"]);
				
				$therecord = array_merge($addressrecord, $therecord);
				
			} else 
				$therecord = $this->getDefaults();

			return $therecord;
	
		}//end method - getRecord
		
		
		function getDefaults(){
		
			$therecord = parent::getDefaults();
			
			$therecord["addressid"] = 0;
			$therecord["tabledefid"] = 0;
			$therecord["recordid"] = NULL;
			$therecord["defaultshipto"] = 0;
			$therecord["primary"] = 0;			
			
			return $therecord;	
	
		}//end method - getDefauls
		
		

		function formatVariables($variables){
			
			if(!isset($variables['primary']))
				$variables['primary'] = 0;

			if(!isset($variables['defaultshipto']))
				$variables['defaultshipto'] = 0;
			
			if(!isset($variables["existingaddressid"]))
				$variables["existingaddressid"] = false;
						
			return $variables;			
		
		}//end function



		function updateRecord($variables, $modifiedby = NULL){
	
			$variables = $this->formatVariables($variables);

			$variables["id"] = $variables["addressid"];
	
			$thereturn = parent::updateRecord($variables, $modifiedby);
						
			return $thereturn;
			
		}//end method
		
	
		function insertRecord($variables, $createdby = NULL){

			$variables = $this->formatVariables($variables);

			if($variables["existingaddressid"]){
			
				$this->createAddressToRecord($variables, $variables["existingaddressid"], $createdby);
				
				$newAtrID = $variables["existingaddressid"];
			
			} else {

				$newid = parent::insertRecord($variables, $createdby);
				
				//create the addresstorecord
				$newAtrID = $this->createAddressToRecord($variables, $newid, $createdby);
				
			}//endif - existingaddressid
			
			return $newAtrID;
					
		}//end method
		
	}//end class
}//end if


if(class_exists("searchFunctions")){
	class addresstorecordSearchFunctions extends searchFunctions{

		function delete_record(){

			$whereclause = $this->buildWhereClause();
			
			//We need to itterate trhough each record
			// to check for cross-record addresses			
			$querystatement = "
				SELECT
					*
				FROM
					addresstorecord
				WHERE ".$whereclause;
			
			$queryresult = $this->db->query($querystatement);
			
			$removedCount = 0;
			$deletedCount = 0;
			$beenMarked = false;
			
			while($therecord = $this->db->fetchArray($queryresult)){

				//we can disreguard primary and default shipt to addresses as they
				// cannot be removed
				if(!$therecord["primary"] && !$therecord["defaultshipto"]){
				
					//look up address to see if it is associated with other records
					$querystatement = "
						SELECT
							id
						FROM
							addresstorecord
						WHERE
							addressid = ".$therecord["addressid"]."
							AND tabledefid = ".$therecord["tabledefid"]."
							AND recordid != ".$therecord["recordid"];
							
					$lookupResult = $this->db->query($querystatement);

					if(!$this->db->numRows($lookupResult)){
					
						//we can safely delete the address (no other associations)
						$deletestatement = "
							DELETE FROM
								addresses
							WHERE
								id =".$therecord["addressid"];

						$this->db->query($deletestatement);
						
						$deletedCount++;
						$removedCount--;
						
					}//end if - numRows
				
					//remove the connecting record
					$deletestatement = "
						DELETE FROM
							addresstorecord
						WHERE
							id =".$therecord["id"];
					
					$this->db->query($deletestatement);
					
					$removedCount++;
				
				} else {
				
					$beenMarked = true;			
					
				}//endif - primary or defaultshipto
				
			}//endwhile - fetchArray

			//next, craft the response
			
			$message = "";
			
			if($removedCount){	
				
				$message .= $removedCount." address";
				
				if($removedCount > 1)
					$message .= "es";
				
				$message .= " dissociated from record. ";
				
			}//endif removedCount
			
			if($deletedCount){	
				
				$message .= $deletedCount." address";
				
				if($deletedCount > 1)
					$message .= "es";
				
				$message .= " deleted. ";
				
			}//endif removedCount

			if($message == "")
				$message = "No addresses removed from record. ";

			if($beenMarked)
				$message .= "(Addresses marked primary or default ship to cannot be removed.)";
			return $message;
			
		}//end method - delete

		
		function markPrimary(){
		
			return $this->_markAs("primary")." primary address.";
		
		}//end method - markPrimary
		
		
		function markDefaultShipTo(){

			return $this->_markAs("defaultshipto")." default ship to address.";

		}//end method - markDefaultShipTo


		function _markAs($what){

			//grab the tabledef and record id from the first selected id
			$querystatement = "
				SELECT
					tabledefid,
					recordid
				FROM
					addresstorecord
				WHERE
					id =".((int) $this->idsArray[0]);
			
			$relatedInfo = $this->db->fetchArray($this->db->query($querystatement));
			
			//Next, mark all addresses associated with record as false
			$updatestatement = "
				UPDATE
					addresstorecord
				SET
					`".$what."` = 0
				WHERE
					tabledefid = ".$relatedInfo["tabledefid"]."
					AND recordid = ".$relatedInfo["recordid"];
					
			$this->db->query($updatestatement);
			
			//Finally, mark the first record.
			$updatestatement = "
				UPDATE
					addresstorecord
				SET
					`".$what."` = 1
				WHERE
					id = ".((int) $this->idsArray[0]);
			
			$this->db->query($updatestatement);

			// If more than one record was selected, make sure
			// to indicate that only the first record was marked
			$message = "";
			if(count($this->idsArray) > 1)
				$message .= "First ";
			$message .= "Record marked as ";

			return $message;
			
		}//end method _markAs

	}//end class
}//end if
?>