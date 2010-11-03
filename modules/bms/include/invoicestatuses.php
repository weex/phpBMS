<?php
/*
 $Rev: 170 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2006-11-10 19:49:30 -0700 (Fri, 10 Nov 2006) $
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
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
	class invoicestatuses extends phpbmsTable {

		var $_availableUserUUIDs = NULL;


		function verifyVariables($variables){

			if(isset($variables["setreadytopost"]))
				if($variables["setreadytopost"] && $variables["setreadytopost"] != 1)
					$this->verifyErrors[] = "The `setreadytopost` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["invoicedefault"]))
				if($variables["invoicedefault"] && $variables["invoicedefault"] != 1)
					$this->verifyErrors[] = "The `invoicedefault` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["defaultassignedtoid"])){

				if($this->_availableUserUUIDs === NULL){
					$this->_availableUserUUIDs = $this->_loadUUIDList("users");
					$this->_availableUserUUIDs[] = "";//for everyone/no one
				}//end if

				if(!in_array(((string)$variables["defaultassignedtoid"]), $this->_availableUserUUIDs))
					$this->verifyErrors[] = "The `defaultassignedtoid` field does not give an existing/acceptable user uuid.";

			}//end if

			return parent::verifyVariables($variables);

		}//end method --verifyVariables--


		function updateRecord($variables, $modifiedby = NULL, $useUuid = false){
			if(isset($variables["invoicedefault"]))
				$this->updateInvoiceDefault();

			parent::updateRecord($variables, $modifiedby, $useUuid);
		}

		function updateInvoiceDefault(){
			$querystatement="UPDATE `".$this->maintable."` SET `invoicedefault` = 0";
			$queryresult = $this->db->query($querystatement);
		}
	}
}

if(class_exists("searchFunctions")){
	class invoicestatusesSearchFunctions extends searchFunctions{

		function delete_record($useUUID = false){

			if(!$useUUID)
				$whereclause=$this->buildWhereClause();
			else
				$whereclause = $this->buildWhereClause($this->maintable.".uuid");

			//$whereclause = $this->buildWhereClause($theids,"invoicestatuses.id");

			$querystatement = "
				UPDATE
					`invoicestatuses`
				SET
					`inactive`='1',
					`modifiedby`='".$_SESSION["userinfo"]["id"]."'
				WHERE
					(".$whereclause.")
				AND
					`invoicedefault`='0'
			";

			$queryresult = $this->db->query($querystatement);

			$message = $this->buildStatusMessage();
			$message .= " marked inactive.";
			return $message;
		}

	}//end class
}

?>