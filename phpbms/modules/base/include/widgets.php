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

		var $availableModuleIDs = array();
		var $availableRoleIDs = array();
		var $availableUuids = array();

		function getDefaults(){

			$therecord = parent::getDefaults();

			$therecord["uuid"] = uuid("wdgt:");

			return $therecord;

		}//end function - getDefaults


		function _populateRoleArray(){

			$this->availableRoleIDs = array();

			$querystatement = "
				SELECT
					`id`
				FROM
					`roles`;
				";

			$queryresult = $this->db->query($querystatement);

			$this->availableRoleIDs[] = 0;//for everyone
			$this->availableRoleIDs[] = -100;//for administrators

			while($therecord = $this->db->fetchArray($queryresult))
				$this->availableRoleIDs[] = $therecord["id"];

		}//end method --_populateRoleArray--


		function _populateModuleArray(){

			$this->availableModuleIDs = array();

			$querystatement = "
				SELECT
					`id`
				FROM
					`modules`;
				";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){
				while($therecord = $this->db->fetchArray($queryresult))
					$this->availableModuleIDs[] = $therecord["id"];
			}else
				$this->availableModuleIDs[] = "AN IMPOSSIBLE ID";

		}//end  method --_populateModuleArray--


		function _populateUuidArray(){

			$this->availableUuids = array();

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

					$this->availableUuids[$uuid]["id"] = $id;
				}
			}else{
				$uuid = "THIS IS a really WEIRD STRING that I *hope* would never ever ever ever ever be a uuid (despite its amazing uniqueness)";
				$id = "aoihweoighaow giuahrweughauerhgaiudsf iaheiugaiuweg iagweiuha wiueg"; //put in an impossible widget id

				$this->availableUuids[$uuid] = $id;
			}//end if

		}//end method --_populateUuidArray--


		function verifyVariables($variables){

			if(isset($variables["title"])){
				if($variables["title"] === "" || $variables["title"] === NULL)
					$this->verifyErrors[] = "The `title` field must be not be blank.";
			}else
				$this->verifyErrors[] = "The `title` field must be set.";

			if(isset($variables["file"])){
				if($variables["file"] === "" || $variables["file"] === NULL)
					$this->verifyErrors[] = "The `file` field must be not be blank.";
			}else
				$this->verifyErrors[] = "The `file` field must be set.";

			//table default of 0 is sufficient
			if(isset($variables["roleid"])){

				if(is_numeric($variables["roleid"]) || !$variables["roleid"]){

					if(!count($this->availableRoleIDs))
						$this->_populateRoleArray();

					if(!in_array(((int)$variables["roleid"]), $this->availableRoleIDs))
						$this->verifyErrors[] = "The `roleid` field does not give an existing/acceptable role id number.";

				}else
					$this->verifyErrors[] = "The `roleid` field must be numeric or equivalent to 0.";

			}//end if

			//table default insufficient
			if(isset($variables["moduleid"])){

				if((int)$variables["moduleid"] > 0 ){

					if(!count($this->availableModuleIDs))
						$this->_populateModuleArray();

					if(!in_array((int)$variables["moduleid"], $this->availableModuleIDs))
						$this->verifyErrors[] = "The `moduleid` field does not give an existing/acceptable module id number.";

				}else
					$this->verifyErrors[] = "The `moduleid` field must be numeric and greater than 0.";

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

					if(!count($this->availableUuids))
						$this->_populateUuidArray();

					if(!isset($variables["id"]))
						$tempid = 0;
					else
						$tempid = $variables["id"];

					$tempuuid = $variables["uuid"];// using this because it looks ugly to but the brackets within brackets

					if( array_key_exists($variables["uuid"], $this->availableUuids)){

						if( $this->availableUuids[$tempuuid]["id"] !== $tempid )
							$this->verifyErrors = "The `uuid` field must give an unique uuid.";

					}else
						$this->availableUuids[$tempuuid]["id"] = "aoihweoighaow giuahrweughauerhgaiudsf iaheiugaiuweg iagweiuha wiueg";

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
