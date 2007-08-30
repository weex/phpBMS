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
		}
	
		// CLASS OVERRIDES ===================================================================
		
		function getDefaults(){
			$therecord = parent::getDefaults();
			
			$therecord["type"]="prospect";
			$therecord["webaddress"]="http://";
			
			return $therecord;
		}
		
	
		function prepareVariables($variables){
			if ($variables["webaddress"]=="http://") 
				$variables["webaddress"] = NULL;
					
			return $variables;
		}
		
	
		function updateRecord($variables, $modifiedby = NULL){
			
			$variables = $this->prepareVariables($variables);
			
			return parent::updateRecord($variables, $modifiedby);
		}
		
		
		function insertRecord($variables, $createdby = NULL){
			
			$variables = $this->prepareVariables($variables);
			
			return parent::insertRecord($variables, $createdby);
		}//end method
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
		
			$querystatement = "update clients set comments=concat(\"Information Packet Sent\",char(10),comments), modifiedby=".$_SESSION["userinfo"]["id"]." where ".$whereclause.";";
			$queryresult = $this->db->query($querystatement);
			
			$message = $this->buildStatusMessage();
			$message.=" marked as info packet sent.";
			return $message;
		}
		
		
		//remove prospects
		function delete_prospects(){
		
			//passed variable is array of user ids to be revoked
			$whereclause = $this->buildWhereClause();
			
			$querystatement = "DELETE FROM clients where (".$whereclause.") and type=\"prospect\";";
			$queryresult = $this->db->query($querystatement);
		
			$message = $this->buildStatusMessage();
			$message.=" deleted.";
			return $message;	
		}
		
		
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