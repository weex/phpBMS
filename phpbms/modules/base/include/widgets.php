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
	class widgets extends phpbmsTable{

		var $_availableModuleUUIDs = NULL;
		var $_availableRoleUUIDs = NULL;
		var $_availableUUIDs = NULL;


		function _populateUuidArray(){

			$this->_availableUUIDs = array();

			// I need id as well to let updates work with our verify function
			// i.e. if its an update on existing record, its ok if the uuid
			// is not unique iff its already associated to the record being updated
			$querystatement = "
				SELECT
					`id`,
					`uuid`
				FROM
					`widgets`;
				";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){
				while($therecord = $this->db->fetchArray($queryresult)){
					$uuid = $therecord["uuid"];
					$id = $therecord["id"];

					$this->_availableUUIDs[$uuid]["id"] = $id;
				}//end if
			}//end if

		}//end method --_populateUuidArray--


		function verifyVariables($variables){

			if(isset($variables["title"])){
				if($variables["title"] === "" || $variables["title"] === NULL)
					$this->verifyErrors[] = "The `title` field must not be blank.";
			}else
				$this->verifyErrors[] = "The `title` field must be set.";

			if(isset($variables["file"])){
				if($variables["file"] === "" || $variables["file"] === NULL)
					$this->verifyErrors[] = "The `file` field must not be blank.";
			}else
				$this->verifyErrors[] = "The `file` field must be set.";

			//table default of '' is sufficient
			if(isset($variables["roleid"])){

				if($this->_availableRoleUUIDs === NULL){
					$this->_availableRoleUUIDs = $this->_loadUUIDList("roles");
					$this->_availableRoleUUIDs[] = "";// no restrictions
					$this->_availableRoleUUIDs[] = "Admin";// admin restriction
				}//end if

				if(!in_array(((string)$variables["roleid"]), $this->_availableRoleUUIDs))
					$this->verifyErrors[] = "The `roleid` field does not give an existing/acceptable role id number.";

			}//end if

			//table default insufficient
			if(isset($variables["moduleid"])){

				if($this->_availableModuleUUIDs === NULL)
					$this->_availableModuleUUIDs = $this->_loadUUIDList("modules");

				if(!in_array((string)$variables["moduleid"], $this->_availableModuleUUIDs))
					$this->verifyErrors[] = "The `moduleid` field does not give an existing/acceptable module id number.";

			}else
				$this->verifyErrors[] = "The `moduleid` field must be set.";

			if(isset($variables["default"]))
				if($variables["default"] && $variables["default"] != 1)
					$this->verifyErrors[] = "The `revoked` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["type"])){
				switch($variables["type"]){

					case "big":
					case "little":
						break;

					default:
						$this->verifyErrors[] = "The `type` field's value must be one of the following: 'big' or 'little'";
					break;

				}//end switch
			}else
				$this->verifyErrors[] = "The `type` field must be set.";


			if(isset($variables["uuid"])){

				if($variables["uuid"] !== "" && $variables !== NULL){

					if($this->_availableUUIDs === NULL)
						$this->_populateUuidArray();

					if(!isset($variables["id"]))
						$tempid = 0;
					else
						$tempid = $variables["id"];

					$tempuuid = $variables["uuid"];// using this because it looks ugly to but the brackets within brackets

					if( array_key_exists((string)$variables["uuid"], $this->_availableUUIDs)){

						if( $this->_availableUUIDs[$tempuuid]["id"] !== $tempid )
							$this->verifyErrors = "The `uuid` field must give an unique uuid.";

					}//end if

				}else
					$this->verifyErrors[] = "The `uuid` field must not be blank.";

			}else
				$this->verifyErrors[] = "The `uuid` field must be set.";

			return parent::verifyVariables($variables);

		}//end method --verifyVariables--

	}//end class
}//end if


if(class_exists("searchFunctions")){
	class menuSearchFunctions extends searchFunctions{


	}//end class
}//end if
?>
