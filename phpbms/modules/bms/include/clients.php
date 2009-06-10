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

		var $_availablePaymentMethodUUIDs = NULL;
		var $_availableShippingMethodUUIDs = NULL;
		var $_availableDiscountUUIDs = NULL;
		var $_availableTaxUUIDs = NULL;
		var $_availableUserUUIDs = NULL;

		function checkForInvoices($id){
			$querystatement="SELECT id FROM invoices WHERE clientid=".((int) $id);
			$queryresult = $this->db->query($querystatement);

			return !($this->db->numRows($queryresult)===0);
		}//end method


		// CLASS OVERRIDES ===================================================================
		// ===================================================================================

		function clients($db,$tabledefid = 0,$backurl = NULL){

			$this->phpbmsTable($db,$tabledefid,$backurl);

			if(!class_exists("addresstorecord")){
				include_once("modules/bms/include/addresses.php");
				include_once("modules/bms/include/addresstorecord.php");
			}//endif

			$this->address = new addresstorecord($db, "tbld:27b99bda-7bec-b152-8397-a3b09c74cb23");

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

			unset($addressinfo["id"], $addressinfo["uuid"], $addressinfo["createdby"], $addressinfo["creationdate"], $addressinfo["modifiedby"], $addressinfo["modifieddate"]);

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
						`tabledefid` = 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083'
						AND `primary` = '1'
						AND `recordid` = '".$therecord["uuid"]."'
				";

				$queryresult = $this->db->query($querystatement);

				$addressinfo = $this->db->fetchArray($queryresult);

				if($addressinfo) {

					$addressinfo = $this->address->getRecord($addressinfo["id"]);

				} else {

					$addressinfo = $this->address->getDefaults();
					$addressinfo["addressid"] = NULL;

				}//endif

				unset($addressinfo["id"], $addressinfo["uuid"], $addressinfo["notes"], $addressinfo["email"], $addressinfo["createdby"], $addressinfo["creationdate"], $addressinfo["modifiedby"], $addressinfo["modifieddate"]);

				$therecord = array_merge($therecord, $addressinfo);

			}//endif

			return $therecord;

		}//end function - getRecord


		function verifyVariables($variables){

			if(isset($variables["type"])){
				switch($variables["type"]){

					case "prospect":

						if(isset($variables["becameclient"])){
							if($variables["becameclient"] !== "" || $variables["becameclient"] !== NULL)
								$this->verifyErrors[] = "Records with `type` of 'prospect'
									must have the `becameclient` field kept blank.";
						}//end if

						if(isset($variables["hascredit"])){
							if($variables["hascredit"])
								$this->verifyErrors[] = "Records with `type` of 'prospect'
									must have the `hascredit` field kept blank or 0.";
						}//end if

						if(isset($variables["creditlimit"])){
							if($variables["creditlimit"])
								$this->verifyErrors[] = "Records with `type` of 'prospect'
									must have the `creditlimit` field kept blank or 0.";
						}//end if

					break;

					case "client":
						if(isset($variables["becameclient"])){
							//Possibly run through string to date functions
							if(!$variables["becameclient"])
								$this->verifyErrors[] = "Records with `type` of 'client'
									must have not have the `becameclient` field blank.";
						}else
							$this->verifyErrors[] = "Records with `type` of 'client'
								must set the `becameclient` field.";
					break;

					default:
						$this->verifyErrors[] = "The value of the `type` field is invalid.
							It must either be 'prospect' or 'client'.";
					break;

				}//end switch
			}else
				$this->verifyErrors[] = "The `type` field must be set.";

			////check for currency on credit limit (((real value) >= 0 ... non-negative)
			//if(isset($variables["creditlimit"]))
			//	if(!is_numeric($variables["creditlimit"]) && $variables["creditlimit"])
			//		$this->verifyErrors[] = "The `creditlimit` field must be a real number or equivalent to zero.";

			//----------------[ phone & email ]------------------------------------------------------
			/*//check valid email
			if(isset($variables["email"]))
				if( $variables["email"] !== NULL && $variables["email"] !== "" && !validateEmail($variables["email"]))
					$this->verifyErrors[] = "The `email` field must have a valid email or must be left blank.";

			//check valid homephone
			if(isset($variables["homephone"]))
				if( $variables["homephone"] !== NULL && $variables["homephone"] !== "" && !validatePhone($variables["homephone"]))
					$this->verifyErrors[] = "The `homephone` field must have a valid phone number (as set in configuration) or must be left blank.";

			//check valid workphone
			if(isset($variables["workphone"]))
				if( $variables["workphone"] !== NULL && $variables["workphone"] !== "" && !validatePhone($variables["workphone"]))
					$this->verifyErrors[] = "The `workphone` field must have a valid phone number (as set in configuration) or must be left blank.";

			//check valid mobilephone
			if(isset($variables["mobilephone"]))
				if( $variables["mobilephone"] !== NULL && $variables["mobilephone"] !== "" && !validatePhone($variables["mobilephone"]))
					$this->verifyErrors[] = "The `mobilephone` field must have a valid phone number (as set in configuration) or must be left blank.";

			//check valid fax
			if(isset($variables["fax"]))
				if( $variables["fax"] !== NULL && $variables["fax"] !== "" && !validatePhone($variables["fax"]))
					$this->verifyErrors[] = "The `fax` field must have a valid phone number (as set in configuration) or must be left blank.";

			//check valid otherphone
			if(isset($variables["otherphone"]))
				if( $variables["otherphone"] !== NULL && $variables["otherphone"] !== "" && !validatePhone($variables["otherphone"]))
					$this->verifyErrors[] = "The `otherphone` field must have a valid phone number (as set in configuration) or must be left blank.";

			//check bool on has credit
			if(isset($variables["hascredit"]))
				if($variables["hascredit"] && $variables["hascredit"] != 1)
					$this->verifyErrors[] = "The `hascredit` field must be a boolean (equivalent to 0 or exactly 1).";*/


			//----------------[ Order Defaults]------------------------------------------------------

			//Payement Method
			if(isset($variables["paymentmethodid"])){

				if($this->_availablePaymentMethodUUIDs === NULL){
					$this->_availablePaymentMethodUUIDs = $this->_loadUUIDList("paymentmethods");
					$this->_availablePaymentMethodUUIDs[] = ""; //for none
				}

				if(!in_array((string)$variables["paymentmethodid"], $this->_availablePaymentMethodUUIDs))
					$this->verifyErrors[] = "The `paymentmethodid` field does not give an existing/acceptable payment method uuid.";

			}//end if

			if(isset($variables["shippingmethodid"])){

				if($this->_availableShippingMethodUUIDs === NULL){
					$this->_availableShippingMethodUUIDs = $this->_loadUUIDList("shippingmethods");
					$this->_availableShippingMethodUUIDs[] = ""; // for none
				}//end if

				if(!in_array((string)$variables["shippingmethodid"], $this->_availableShippingMethodUUIDs))
					$this->verifyErrors[] = "The `shippingmethodid` field does not give an existing/acceptable shipping method uuid.";

			}//end if

			if(isset($variables["discountid"])){

				if($this->_availableDiscountUUIDs === NULL){
					$this->_availableDiscountUUIDs = $this->_loadUUIDList("discounts");
					$this->_availableDiscountUUIDs[] = ""; //for none
				}//end if

				if(!in_array((string)$variables["discountid"], $this->_availableDiscountUUIDs))
					$this->verifyErrors[] = "The `discount` field does not give an existing/acceptable discount uuid.";

			}//end if

			if(isset($variables["taxareaid"])){

				if($this->_availableTaxUUIDs === NULL){
					$this->_availableTaxUUIDs = $this->_loadUUIDList("tax");
					$this->_availableTaxUUIDs[] = ""; //for none
				}//end if

				if(!in_array((string)$variables["taxareaid"], $this->_availableTaxUUIDs))
					$this->verifyErrors[] = "The `taxareaid` field does not give an existing/acceptable tax uuid.";

			}//end if

			//---------------------[ end order defaults ]----------------------------------------

			//check sales manager id
			if(isset($variables["salesmanagerid"])){

				if($this->_availableUserUUIDs === NULL){
					$this->_availableUserUUIDs = $this->_loadUUIDList("users");
					$this->_availableUserUUIDs[] = "";
				}//end if

				if(!in_array((string)$variables["salesmanagerid"], $this->_availableUserUUIDs))
					$this->verifyErrors[] = "The `salesmanagerid` field does not give an existing/acceptable user uuid.";

			}//end if


			return parent::verifyVariables($variables);

		}//end method


		function prepareVariables($variables){

			if(isset($variables["webaddress"]))
				if ($variables["webaddress"]=="http://")
					$variables["webaddress"] = NULL;

			if(!isset($variables["type"]))
				$variables["type"] = "client";

			if($variables["type"] == "prospect"){

				$variables["hascredit"] = 0;
				$variables["creditlimit"] = 0;
				$variables["becameclient"] = NULL;
			}else{
				$variables["type"] = "client";
				if(!isset($variables["becameclient"]))
					$variables["becameclient"] = NULL;
				if(!$variables["becameclient"])
					$variables["becameclient"] = dateToString(mktime());
			}//end if

			return $variables;

		}//end method


		function updateRecord($variables, $modifiedby = NULL){
			//$variables = $this->prepareVariables($variables);

			$thereturn = parent::updateRecord($variables, $modifiedby);

			$variables["recordid"] = $variables["uuid"];//here to pass addresstorecord validation
			$variables["tabledefid"] = "tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083";//here to pass addresstorecord validation
			//need to update the address
			$variables["uuid"] = $variables["addressid"];
			// don't want to blank out extra address information
			// if it was added later.
			unset($this->address->fields["email"]);
			unset($this->address->fields["phone"]);
			unset($this->address->fields["notes"]);
			unset($this->address->fields["title"]);
			unset($this->address->fields["createdby"]);
			unset($this->address->fields["creationdate"]);

			$variables = $this->address->prepareVariables($variables);
			$errorArray = $this->address->verifyVariables($variables);
			if(!count($errorArray)){
				$this->address->updateRecord($variables, $modifiedby, true);
			}else{
				foreach($errorArray as $error)
					$logError = new appError(-910, $error, "Address Verification Error");
			}//end if

			//restore the fields
			$this->address->getTableInfo();

			return $thereturn;

		}//end method - updateRecord


		function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false){

			//$variables = $this->prepareVariables($variables);

			$newid = parent::insertRecord($variables, $createdby, $overrideID, $replace);

			//need to create the address and addresstorecord id
			// make sure we are not setting extra info
			unset($this->address->fields["email"]);
			unset($this->address->fields["phone"]);
			unset($this->address->fields["notes"]);
			unset($variables["id"]);// This breaks the import otherwise...needs further testing and possibly a better solution
			$variables["title"] = "Main Addresss";
			$variables["tabledefid"] = "tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083";
			$variables["recordid"] = $variables["uuid"];
			$variables["uuid"] = "";
			$variables["defaultshipto"] = 1;
			$variables["primary"] = 1;

			if($newid){// temporary fix... may need to verify client id before hand... dunno
				$variables = $this->address->prepareVariables($variables);
				$errorArray = $this->address->verifyVariables($variables);
				if(!count($errorArray)){
					$this->address->insertRecord($variables, $createdby);
				}else{
					foreach($errorArray as $error)
						$logError = new appError(-910, $error, "Address Verification Error");
				}//end if
			}//end if

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
if(class_exists("phpbmsImport")){
	class clientsImport extends phpbmsImport{


		function clientsImport($table, $importType = "csv"){

			if($importType == "sugarcrm"){

				$importType = "csv";
				$switchedFrom = "sugarcrm";

			}//end if

			parent::phpbmsImport($table, $importType);

			if(isset($switchedFrom))
				$this->importType = $switchedFrom;

		}//end method --clientsImport--


		function _parseFromData($data){

			if($this->importType == "sugarcrm"){

				$this->importType = "csv";
				$switchedFrom = "sugarcrm";

			}//end if

			$thereturn = parent::_parseFromData($data);

			if(isset($switchedFrom))
				$this->importType = $switchedFrom;

			return $thereturn;

		}//end method --_parseFromFile--


		function _formatSugarVariables($rows, $titles){

			//Replace the titles with valid ones
			//(At the moment we only really need
			//the correct count, but, it seems
			//logical to correct the titles in case
			//they are needed in the future)
			$newTitles = array();
			foreach($titles as $index => $name){

				switch($name){

					case "name":
						$newTitles[] = "company";
					break;

					case "date_entered":
						$newTitles[] = "becameclient";
					break;

					case "description":
						$newTitles[] = "comments";
					break;

					case "deleted":
						$newTitles[] = "inactive";
					break;

					case "account_type":
						$newTitles[] = "type";
					break;

					case "industry":
						$newTitles[] = "category";
					break;

					case "phone_fax":
						$newTitles[] = "fax";
					break;

					case "billing_address_street":
						$newTitles[] = "address1";
					break;

					case "billing_address_city":
						$newTitles[] = "city";
					break;

					case "billing_address_state":
						$newTitles[] = "state";
					break;

					case "billing_address_postalcode":
						$newTitles[] = "postalcode";
					break;

					case "billing_address_country":
						$newTitles[] = "country";
					break;

					case "phone_office":
						$newTitles[] = "workphone";
					break;

					case "phone_alternate":
						$newTitles[] = "otherphone";
					break;

					case "website":
						$newTitles[] = "webaddress";
					break;

					case "shipping_address_street":
						$newTitles[] = "shipaddress1";
					break;

					case "shipping_address_city":
						$newTitles[] = "shipcity";
					break;

					case "shipping_address_state":
						$newTitles[] = "shipstate";
					break;

					case "shipping_address_postalcode":
						$newTitles[] = "shippostalcode";
					break;

					case "shipping_address_country":
						$newTitles[] = "shipcountry";
					break;

				}//end switch

			}//end foreach


			$newRows = array();
			foreach($rows as $rowData){

				$newRowData = array();
				$addComments = "";
				foreach($rowData as $name => $data){

					switch($name){

						case "name":
							$newRowData["company"] = trim($data);
						break;

						case "date_entered":
							$newRowData["becameclient"] = trim($data);
						break;

						case "description":
							$newRowData["comments"] = trim($data);
						break;

						case "deleted":
							$newRowData["inactive"] = trim($data);
						break;

						case "industry":
							$newRowData["category"] = trim($data);
						break;

						case "account_type":
							$newRowData["type"] = trim($data);
						break;

						case "phone_fax":
							$newRowData["fax"] = trim($data);
						break;

						case "billing_address_street":
							$newRowData["address1"] = trim($data);
						break;

						case "billing_address_city":
							$newRowData["city"] = trim($data);
						break;

						case "billing_address_state":
							$newRowData["state"] = trim($data);
						break;

						case "billing_address_postalcode":
							$newRowData["postalcode"] = trim($data);
						break;

						case "billing_address_country":
							$newRowData["country"] = trim($data);
						break;

						case "phone_office":
							$newRowData["workphone"] = trim($data);
						break;

						case "phone_alternate":
							$newRowData["otherphone"] = trim($data);
						break;

						case "website":
							if(strpos(trim($data), "http://") !== 0)
								$newRowData["webaddress"] = "http://".trim($data);
							else
								$newRowData["webaddress"] = trim($data);
						break;

						case "shipping_address_street":
							$newRowData["shipaddress1"] = trim($data);
						break;

						case "shipping_address_city":
							$newRowData["shipcity"] = trim($data);
						break;

						case "shipping_address_state":
							$newRowData["shipstate"] = trim($data);
						break;

						case "shipping_address_postalcode":
							$newRowData["shippostalcode"] = trim($data);
						break;

						case "shipping_address_country":
							$newRowData["shipcountry"] = trim($data);
						break;

						case "annual_revenue":
						case "rating":
						case "ownership":
						case "employees":
						case "ticker_symbol":
							if($data)
								$addComments .= "\n".str_replace("_"," ",$name).": ".trim($data);
						break;

					}//end switch

				}//end foreach

				if($newRowData["type"] == "prospect")
					$newRowData["becameclient"] = NULL;
				else
					$newRowData["type"] = "client";

				$newRowData["comments"] .= $addComments;
				$newRows[] = $newRowData;

			}//end foreach

			$thereturn["rows"] = $newRows;
			$thereturn["titles"] = $newTitles;
			return $thereturn;

		}//end method --_formatSugarvariables--


		function importRecords($rows, $titles){

			switch($this->importType){

				case "sugarcrm":
					$thereturn = $this->_formatSugarVariables($rows, $titles);
					$rows = $thereturn["rows"];
					$titles = $thereturn["titles"];

				case "csv":
					//count total fieldnames (top row of csv document)
					$fieldNum = count($titles);

					//the file starts at line number 1, but since line 1 is
					//supposed to be the fieldnames in the table(s), the lines
					//being insereted start @ 2.
					$rowNum = 2;

					//get the data one row at a time
					foreach($rows as $rowData){

						$theid = 0; // set for when verifification does not pass
						$verify = array(); //set for when number of field rows does not match number of titles

						//trim off leading/trailing spaces
						$trimmedRowData = array();
						foreach($rowData as $name => $data)
							$trimmedRowData[$name] = trim($data);

						//check to see if number of fieldnames is consistent for each row
						$rowFieldNum = count($trimmedRowData);

						//if valid, insert, if not, log error and don't insert.
						if($rowFieldNum == $fieldNum){
							$verify = $this->table->verifyVariables($trimmedRowData);
							if(!count($verify))
								$theid = $this->table->insertRecord($trimmedRowData, NULL, true);
						}else
							$this->error .= '<li> incorrect amount of fields for line number '.$rowNum.'.</li>';

						if($theid){
							//keep track of the ids in the transaction to be able to select them
							//for preview purposes
							$this->transactionIDs[] = $theid;

							//get first id to correct auto increment
							if(!$this->revertID)
								$this->revertID = $theid;

							//If it is a sugarcrm import, insert the shipping address as well
							$addressVerify = array();
							if($this->importType == "sugarcrm"){


								$variables = array();
								if($trimmedRowData["shipaddress1"]) $variables["address1"] = $trimmedRowData["shipaddress1"];
								if($trimmedRowData["shipcity"]) $variables["city"] = $trimmedRowData["shipcity"];
								if($trimmedRowData["shipstate"]) $variables["state"] = $trimmedRowData["shipstate"];
								if($trimmedRowData["shipcountry"]) $variables["country"] = $trimmedRowData["shipcountry"];

								//check to see if there is a shipping address
								if(count($variables)){

									//If there is a shipping address, we need to make any others'
									//`defaultshipto` to 0

									$querystatement = "
										UPDATE
											`addresstorecord`
										SET
											`addresstorecord`.`defaultshipto` = '0'
										WHERE
											`addresstorecord`.`recordid` = '".((int) $theid)."'
											AND
											`addresstorecord`.`tabledefid` = '2';
										";

									$this->table->db->query($querystatement);

									$variables["title"] = "Main Shipping Addresss";
									$variables["tabledefid"] = 2;
									$variables["recordid"] = $theid;
									$variables["defaultshipto"] = 1;
									$variables["primary"] = 0;
									$variables["existingaddressid"] = false;

									$addressVerify = $this->table->address->verifyVariables($variables);//verify address
									if(!count($addressVerify))//check for errors
										$this->table->address->insertRecord($variables);//insert if no errors

								}//end if

							}//end if
						}else
							$this->error .= '<li> failed insert for line number '.$rowNum.'.</li>';

							foreach($verify as $error)//log verify errors for display
								$this->error .= '<li class="subError">'.$error.'</li>';

							if(isset($addressVerify))
									foreach($addressVerify as $error)//log address verify errors for display
										$this->error .= '<li class="subError">'.$error.'</li>';

						$rowNum++;

					}//end foreach
				break;

			}//end switch

		}//end method --importRecords--


		function _getTransactionData(){

			$inStatement = "";
			foreach($this->transactionIDs as $theid)
				$inStatement .= $theid.",";

			if($inStatement)
				$inStatement = substr($inStatement, 0, -1);
			else
				$inStatement = "0";

			//There are two cases to minimize joins for csv files
			switch($this->importType){

				case "sugarcrm":
					$querystatement = "
						SELECT
							`clients`.*,
							`addresses1`.`address1` AS `mainaddress1`,
							`addresses1`.`address2` AS `mainaddress2`,
							`addresses1`.`city` AS `maincity`,
							`addresses1`.`state` AS `mainstate`,
							`addresses1`.`postalcode` AS `mainpostalcode`,
							`addresses1`.`country` AS `maincountry`,
							`addresses1`.`phone` AS `mainphone`,
							`addresses1`.`email` AS `mainemail`,
							`addresses1`.`custom1` AS `maincustom1`,
							`addresses1`.`custom2` AS `maincustom2`,
							`addresses1`.`custom3` AS `maincustom3`,
							`addresses1`.`custom4` AS `maincustom4`,
							`addresses1`.`custom5` AS `maincustom5`,
							`addresses1`.`custom6` AS `maincustom6`,
							`addresses1`.`custom7` AS `maincustom7`,
							`addresses1`.`custom8` AS `maincustom8`,
							`addresses2`.`address1` AS `shipaddress1`,
							`addresses2`.`address2` AS `shipaddress2`,
							`addresses2`.`city` AS `shipcity`,
							`addresses2`.`state` AS `shipstate`,
							`addresses2`.`postalcode` AS `shippostalcode`,
							`addresses2`.`country` AS `shipcountry`,
							`addresses2`.`phone` AS `shipphone`,
							`addresses2`.`email` AS `shipemail`,
							`addresses2`.`custom1` AS `shipcustom1`,
							`addresses2`.`custom2` AS `shipcustom2`,
							`addresses2`.`custom3` AS `shipcustom3`,
							`addresses2`.`custom4` AS `shipcustom4`,
							`addresses2`.`custom5` AS `shipcustom5`,
							`addresses2`.`custom6` AS `shipcustom6`,
							`addresses2`.`custom7` AS `shipcustom7`,
							`addresses2`.`custom8` AS `shipcustom8`
						FROM
							((((clients INNER JOIN addresstorecord AS `addresstorecord1` ON clients.id = addresstorecord1.recordid AND addresstorecord1.tabledefid=2 AND addresstorecord1.primary=1) INNER JOIN addresses AS addresses1 ON  addresstorecord1.addressid = addresses1.id)LEFT JOIN addresstorecord AS `addresstorecord2` on clients.id = addresstorecord2.recordid AND addresstorecord2.tabledefid=2 AND addresstorecord2.primary=0 AND addresstorecord2.defaultshipto=1) LEFT JOIN addresses AS addresses2 ON  addresstorecord2.addressid = addresses2.id)
						WHERE
							`clients`.`id` IN (".$inStatement.")
						ORDER BY
							`clients`.`id` ASC;
						";
				break;

				case "csv":
					$querystatement = "
						SELECT
							`clients`.*,
							`addresses`.`address1`,
							`addresses`.`address2`,
							`addresses`.`city`,
							`addresses`.`state`,
							`addresses`.`postalcode`,
							`addresses`.`country`,
							`addresses`.`phone`
						FROM
							((clients INNER JOIN addresstorecord ON clients.id = addresstorecord.recordid AND addresstorecord.tabledefid=2 AND addresstorecord.primary=1) INNER JOIN addresses ON  addresstorecord.addressid = addresses.id)
						WHERE
							`clients`.`id` IN (".$inStatement.")
						ORDER BY
							`clients`.`id` ASC;
						";
				break;

			}//end switch

			$queryresult = $this->table->db->query($querystatement);

			while($therecord = $this->table->db->fetchArray($queryresult))
				$this->transactionRecords[] = $therecord;


		}//end method --_gettransactionData--


		function displayTransaction($recordsArray, $fieldsArray){

			if(count($recordsArray) && count($fieldsArray)){

				//Need to include addresses in the fieldArray

				//list of values that should not be displayed
				$removalArray = array("id", "modifiedby", "modifieddate", "createdby", "creationdate", "notes", "title", "shiptoname");
				//gets the address table's columnnames/information (fields)
				$addressArray = $this->table->address->fields;

				//gets rid of the values that should not be displayed
				foreach($removalArray as $removalField){

					if(isset($addressArray[$removalField])){

						unset($addressArray[$removalField]);

					}//end if

				}//end foreach

				//get rid of stuff that should only be in addresses but is present in clients
				foreach($addressArray as $removalField => $junk){

					if(isset($fieldsArray[$removalField])){

						unset($fieldsArray[$removalField]);

					}//end if

				}//end foreach

				//need to get two sets of address fields, one named main* and the other ship*.
				if($this->importType == "sugarcrm"){

					foreach($addressArray as $field => $junk){

						$mainAddressArray["main".$field] = $junk;
						$shipAddressArray["ship".$field] = $junk;

					}//end foreach

					$addressArray = $mainAddressArray + $shipAddressArray;

				}//end if

				$fieldsArray = $fieldsArray + $addressArray;

				parent::displayTransaction($recordsArray, $fieldsArray);

			}//end if

		}//end method --displayTransaction--

	}//end class --clientsImport--
}//end if
?>
