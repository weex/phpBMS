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

if(class_exists("phpbmsTable")){
	class clients extends phpbmsTable{
	
		function checkForInvoices($id){
			$querystatement="SELECT id FROM invoices WHERE clientid=".((int) $id);
			$queryresult = $this->db->query($querystatement);
	
			return !($this->db->numRows($queryresult)===0);
		}//end method
	
	
		// CLASS OVERRIDES ===================================================================
		// ===================================================================================

		function clients($db,$tabledefid = 0,$backurl = NULL){
			
			$this->phpbmsTable($db,$tabledefid,$backurl);
			
			$this->address = new addresstorecord($db, 306);
			
		}//end function - init
		

		function getDefaults(){

			$therecord = parent::getDefaults();

			$therecord["type"] = DEFAULT_CLIENTTYPE;
			
			if($therecord["type"] == "client") {

				$therecord["becameclient"] = dateToString(mktime(), "SQL");
				$therecord["hascredit"] = DEFAULT_HASCREDIT;
				$therecord["creditlimit"] = DEFAULT_CREDITLIMIT;

			}//end if
			
			$therecord["webaddress"] = "http://";
			
			//now for the address information.
			$addressinfo = $this->address->getDefaults();
			unset($addressinfo["id"], $addressinfo["createdby"], $addressinfo["creationdate"], $addressinfo["modifiedby"], $addressinfo["modifieddate"]);
			$addressinfo["addressid"] = NULL;
			
			return array_merge($therecord, $addressinfo);
						
		}//end function - getDefaults
		
	
		function getRecord($id){

			$id = (int) $id;
			
			$therecord = parent::getRecord($id);
			
			if($therecord["id"]){
				//need to grab the address as well
				
				$querystatement = "
					SELECT
						id
					FROM
						addresstorecord
					WHERE
						tabledefid = 2
						AND `primary` = 1
						AND recordid = ".$id;

				$queryresult = $this->db->query($querystatement);
				
				$addressinfo = $this->db->fetchArray($queryresult);

				if($addressinfo) {
					
					$addressinfo = $this->address->getRecord($addressinfo["id"]);
				
				} else {

					$addressinfo = $this->address->getDefaults();
					$addressinfo["addressid"] = NULL;
					
				}//endif			

				unset($addressinfo["id"], $addressinfo["notes"], $addressinfo["email"], $addressinfo["createdby"], $addressinfo["creationdate"], $addressinfo["modifiedby"], $addressinfo["modifieddate"]);

				$therecord = array_merge($therecord, $addressinfo);
			
			}//endif
			
			return $therecord;
		
		}//end function - getRecord
		

		function prepareVariables($variables){
		
			if ($variables["webaddress"]=="http://") 
				$variables["webaddress"] = NULL;

			if(!isset($variables["type"]))
				$variables["type"] = "client";
				
			if($variables["type"] == "prospect"){

				$variables["hascredit"] = 0;
				$variables["creditlimit"] = 0;
			
			}//end if

			return $variables;
			
		}//end method
		
	
		function updateRecord($variables, $modifiedby = NULL){
			
			$variables = $this->prepareVariables($variables);
			
			$thereturn = parent::updateRecord($variables, $modifiedby);
			
			//need to update the address
			$variables["id"] = $variables["addressid"];
			// don't want to blank out extra address information
			// if it was added later.
			unset($this->address->fields["email"]);
			unset($this->address->fields["phone"]);
			unset($this->address->fields["notes"]);
			unset($this->address->fields["title"]);
			unset($this->address->fields["createdby"]);
			unset($this->address->fields["creationdate"]);

			$this->address->updateRecord($variables, $modifiedby);

			//restore the fields
			$this->address->getTableInfo();
			
			return $thereturn;
			
		}//end method - updateRecord
		
		
		function insertRecord($variables, $createdby = NULL){
			
			$variables = $this->prepareVariables($variables);
			
			$newid = parent::insertRecord($variables, $createdby);
			
			//need to create the address and addresstorecord id
			// make sure we are not setting extra info
			unset($this->address->fields["email"]);
			unset($this->address->fields["phone"]);
			unset($this->address->fields["notes"]);
			$variables["title"] = "Main Addresss";
			$variables["tabledefid"] = 2;
			$variables["recordid"] = $newid;
			$variables["defaultshipto"] = 1;
			$variables["primary"] = 1;
			
			$this->address->insertRecord($variables, $createdby);

			//restore the fields
			$this->address->getTableInfo();
			
			return $newid;
			
		}//end method - insertRecord
		
	}//end class
	
}//end if


if(class_exists("searchFunctions")){
	class clientsSearchFunctions extends searchFunctions{

		function mark_asclient(){
		
			//passed variable is array of user ids to be revoked
			$whereclause = $this->buildWhereClause();	
		
			$querystatement = "UPDATE clients SET clients.type=\"client\",modifiedby=\"".$_SESSION["userinfo"]["id"]."\" WHERE (".$whereclause.");";
			$queryresult = $this->db->query($querystatement);
			
			$message = $this->buildStatusMessage();
			$message.=" converted to client.";
			return $message;
		}
		
		
		//Stamp Comments Field with info packet sent
		function stamp_infosent(){
		
			//passed variable is array of user ids to be revoked
			$whereclause = $this->buildWhereClause();
		
			$querystatement = "
				UPDATE
					clients 
				SET
					clients.comments = concat('Information Packet Sent', char(10), clients.comments), 
					clients.modifiedby=".$_SESSION["userinfo"]["id"].",
					clients.modifieddate = NOW()
				WHERE (".$whereclause.") AND clients.comments IS NOT NULL";
			$queryresult = $this->db->query($querystatement);
			
			$affected = $this->db->affectedRows();

			$querystatement = "
				UPDATE
					clients 
				SET
					clients.comments = 'Information Packet Sent', 
					clients.modifiedby=".$_SESSION["userinfo"]["id"].",
					clients.modifieddate = NOW()
				WHERE (".$whereclause.") AND clients.comments IS NULL";
			$queryresult = $this->db->query($querystatement);

			$affected += $this->db->affectedRows();
			
			$message = $this->buildStatusMessage($affected);
			$message.=" marked as info packet sent.";
			return $message;
		}
		
		
		//remove prospects
		function delete_prospects(){
		
			//passed variable is array of user ids to be revoked
			$clientWhereClause = $this->buildWhereClause();

			$querystatement = "
				SELECT
					id
				FROM
					clients
				WHERE
					(".$clientWhereClause.")
					AND clients.type = 'prospect'";
					
			$queryresult = $this->db->query($querystatement);
			
			//build array of ids to be removed
			$deleteIDs = array();
			while($therecord = $this->db->fetchArray($queryresult))
				array_push($deleteIDs, $therecord["id"]);

			if(count($deleteIDs)){
			
				$a2rWhere = $this->buildWhereClause("recordid", $deleteIDs);

				//First we get a list of all the addresses for the prospect
				$querystatement = "
					SELECT DISTINCT
						addressid
					FROM
						addresstorecord
					WHERE
						tabledefid = 2
						AND (".$a2rWhere.")";
						
				$a2rResult = $this->db->query($querystatement);
				
				$addressIDs = array();
				while($a2r = $this->db->fetchArray($a2rResult))
					array_push($addressIDs, $a2r["addressid"]);
					
				// delete all a2r records for prospect	
				$deletestatement = "
					DELETE FROM
						addresstorecord
					WHERE
						tabledefid = 2
						AND (".$a2rWhere.")";
						
				$this->db->query($deletestatement);
				
				//now go get a list of orphaned addresses
				$querystatement = "
					SELECT 
						addresses.id, 
						addresstorecord.id as a2rid 
					FROM 
						addresses LEFT JOIN addresstorecord ON addresstorecord.addressid = addresses.id
					WHERE
						".$this->buildWhereClause("addresses.id", $addressIDs);
						
				$addressResult = $this->db->query($querystatement);
				
				$addressIDs = array();
				while($address = $this->db->fetchArray($addressResult))
					if(!$address["a2rid"])
						array_push($addressIDs, $address["id"]);
						
				if(count($addressIDs)){
				
					//delete orphaned addresses		
					$deletestatement = "
						DELETE FROM 
							addresses
						WHERE
							".$this->buildWhereClause("addresses.id", $addressIDs);
							
					$this->db->query($deletestatement);
					
				}//endif - addressids

				//next get any quotes that we may have to delete
				$invoiceWhereClause = $this->buildWhereClause("clientid", $deleteIDs);
				$invoicestatement = "
					SELECT
						invoices.id
					FROM
						invoices
					WHERE
						".$invoiceWhereClause;
						
				$invoiceresult = $this->db->query($invoicestatement);
				
				//build invoice id array
				$invoiceids = array();
				while($therecord = $this->db->fetchArray($invoiceresult))
					array_push($invoiceids, $therecord["id"]);
				
				if(count($invoiceids)) {
					$invoiceWhereClause = $this->buildWhereClause("invoices.id", $invoiceids);
					
					$lineitemWhereClause = $this->buildWhereClause("invoiceid", $invoiceids);
		
					$lineItemDeleteStatement = "
						DELETE FROM
							lineitems
						WHERE
							".$lineitemWhereClause;
		
					$queryresult = $this->db->query($lineItemDeleteStatement);
		
					$statushistoryDeleteStatement = "
						DELETE FROM
							invoicestatushistory
						WHERE
							".$lineitemWhereClause;
		
					$queryresult = $this->db->query($statushistoryDeleteStatement);
					
					$invoiceDeleteStatement = "
						DELETE FROM
							invoices
						WHERE					
							".$invoiceWhereClause;
	
					$queryresult = $this->db->query($invoiceDeleteStatement);
	
				}//end if

				//lastly we remove the prospect record
				$delWhere = $this->buildWhereClause("clients.id", $deleteIDs);

				$deletestatement = "
					DELETE FROM 
						clients
					WHERE 
						".$delWhere;
						
				$this->db->query($deletestatement);
							
			}//endif - count deleteIDS												

		
			$message = $this->buildStatusMessage(count($deleteIDs));
			$message.=" deleted.";
			return $message;	
			
		}// end method - delete_prospects
		
		
		function massEmail(){
			if(DEMO_ENABLED != "true"){
				$_SESSION["emailids"]= $this->idsArray;
				goURL("modules/bms/clients_email.php");
			} else {
				return "mass e-mail feature disabled in demo";
			}
		}

	
	}//end class
}//end if
?>