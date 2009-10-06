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
	class tableDefinitions extends phpbmsTable{

		var $_availableRoleUUIDs = NULL;
		var $_availableModuleUUIDs = NULL;

		function getDefaults(){
			$therecord = parent::getDefaults();

			$therecord["moduleid"]="mod:29873ee8-c12a-e3f6-9010-4cd24174ffd7";
			$therecord["deletebutton"]="delete";
			$therecord["type"]="table";
			$therecord["searchroleid"] = "";
			$therecord["importroleid"] = "Admin";
			$therecord["advsearchroleid"] = "Admin";
			$therecord["viewsqlroleid"] = "Admin";

			return $therecord;
		}

		function verifyVariables($variables){

			//the following ifs are constructed in such a way as to allow
			//the integer 0 as an acceptable value

			if(isset($variables["maintable"])){
				if($variables["maintable"] === "" || $variables["maintable"] === NULL)
					$this->verifyErrors[] = "The `maintable` field must not be blank.";
			}else
				$this->verifyErrors[] = "The `maintable` field must be set.";

			if(isset($variables["addfile"])){
				if($variables["addfile"] === "" || $variables["addfile"] === NULL)
					$this->verifyErrors[] = "The `addfile` field must not be blank.";
			}else
				$this->verifyErrors[] = "The `addfile` field must be set.";

			if(isset($variables["editfile"])){
				if($variables["editfile"] === "" || $variables["editfile"] === NULL)
					$this->verifyErrors[] = "The `editfile` field must not be blank.";
			}else
				$this->verifyErrors[] = "The `editfile` field must be set.";

			if(isset($variables["querytable"])){
				if($variables["querytable"] === "" || $variables["querytable"] === NULL)
					$this->verifyErrors[] = "The `querytable` field must not be blank.";
			}else
				$this->verifyErrors[] = "The `querytable` field must be set.";

			if(isset($variables["defaultwhereclause"])){
				if($variables["defaultwhereclause"] === "" || $variables["defaultwhereclause"] === NULL)
					$this->verifyErrors[] = "The `defaultwhereclause` field must not be blank.";
			}else
				$this->verifyErrors[] = "The `defaultwhereclause` field must be set.";

			if(isset($variables["defaultsortorder"])){
				if($variables["defaultsortorder"] === "" || $variables["defaultsortorder"] === NULL)
					$this->verifyErrors[] = "The `defaultsortorder` field must not be blank.";
			}else
				$this->verifyErrors[] = "The `defaultsortorder` field must be set.";

			if(isset($variables["deletebutton"])){
				if($variables["deletebutton"] === "" || $variables["deletebutton"] === NULL)
					$this->verifyErrors[] = "The `delete` field must not be blank.";
			}else
				$this->verifyErrors[] = "The `delete` field must be set.";

			//table default sufficient
			if(isset($variables["type"]))
				switch($variables["type"]){

					case "table":
					case "view":
					case "system":
					break;

					default:
						$this->verifyErrors[] = "The value of `type` field is invalid. Its value must be
							'table', 'view', or 'system'.";
					break;

				}//end switch

			if(isset($variables["addroleid"])){

				if($this->_availableRoleUUIDs === NULL){
					$this->_availableRoleUUIDs = $this->_loadUUIDList("roles");
					$this->_availableRoleUUIDs[] = "";//for no restrictions
					$this->_availableRoleUUIDs[] = "Admin";//for admin restriction
				}//end if

				if(!in_array(((string)$variables["addroleid"]), $this->_availableRoleUUIDs))
					$this->verifyErrors[] = "The `addroleid` field does not give an existing/acceptable role id number.";


			}//end if

			if(isset($variables["editroleid"])){

				if($this->_availableRoleUUIDs === NULL){
					$this->_availableRoleUUIDs = $this->_loadUUIDList("roles");
					$this->_availableRoleUUIDs[] = "";//for no restrictions
					$this->_availableRoleUUIDs[] = "Admin";//for admin restriction
				}//end if

				if(!in_array(((string)$variables["editroleid"]), $this->_availableRoleUUIDs))
					$this->verifyErrors[] = "The `editroleid` field does not give an existing/acceptable role id number.";

			}//end if

			if(isset($variables["importroleid"])){

				if($this->_availableRoleUUIDs === NULL){
					$this->_availableRoleUUIDs = $this->_loadUUIDList("roles");
					$this->_availableRoleUUIDs[] = "";//for no restrictions
					$this->_availableRoleUUIDs[] = "Admin";//for admin restriction
				}//end if

				if(!in_array(((string)$variables["importroleid"]), $this->_availableRoleUUIDs))
					$this->verifyErrors[] = "The `importroleid` field does not give an existing/acceptable role id number.";

			}//end if

			if(isset($variables["searchroleid"])){

				if($this->_availableRoleUUIDs === NULL){
					$this->_availableRoleUUIDs = $this->_loadUUIDList("roles");
					$this->_availableRoleUUIDs[] = "";//for no restrictions
					$this->_availableRoleUUIDs[] = "Admin";//for admin restriction
				}//end if

				if(!in_array(((string)$variables["searchroleid"]), $this->_availableRoleUUIDs))
					$this->verifyErrors[] = "The `searchroleid` field does not give an existing/acceptable role id number.";


			}//end if

			if(isset($variables["advsearchroleid"])){

				if($this->_availableRoleUUIDs === NULL){
					$this->_availableRoleUUIDs = $this->_loadUUIDList("roles");
					$this->_availableRoleUUIDs[] = "";//for no restrictions
					$this->_availableRoleUUIDs[] = "Admin";//for admin restriction
				}//end if

				if(!in_array(((string)$variables["advsearchroleid"]), $this->_availableRoleUUIDs))
					$this->verifyErrors[] = "The `advsearchroleid` field does not give an existing/acceptable role id number.";

			}//end if

			if(isset($variables["viewsqlroleid"])){

				if($this->_availableRoleUUIDs === NULL){
					$this->_availableRoleUUIDs = $this->_loadUUIDList("roles");
					$this->_availableRoleUUIDs[] = "";//for no restrictions
					$this->_availableRoleUUIDs[] = "Admin";//for admin restriction
				}//end if

				if(!in_array(((string)$variables["viewsqlroleid"]), $this->_availableRoleUUIDs))
					$this->verifyErrors[] = "The `viewsqlroleid` field does not give an existing/acceptable role id number.";

			}//end if

			//check moduleid
			if(isset($variables["moduleid"])){

				if($this->_availableModuleUUIDs === NULL)
					$this->_availableModuleUUIDs = $this->_loadUUIDList("modules");

				if(!in_array((string)$variables["moduleid"], $this->_availableModuleUUIDs))
					$this->verifyErrors[] = "The `moduleid` field does not give an existing/acceptable role id number.";

			}else
				$this->verifyErrors[] = "The `moduleid` field must be set."; //table default insufficent


			// Check boolean
			if(isset($variables["canpost"]))
				if($variables["canpost"] && $variables["canpost"] != 1)
					$this->verifyErrors[] = "The `canpost` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["apiaccessible"]))
				if($variables["apiaccessible"] && $variables["apiaccessible"] != 1)
					$this->verifyErrors[] = "The `apiaccessible` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["hascustomfields"]))
				if($variables["hascustomfields"] && $variables["hascustomfields"] != 1)
					$this->verifyErrors[] = "The `hascustomfields` field must be a boolean (equivalent to 0 or exactly 1).";

			return parent::verifyVariables($variables);

		}//end method --verifyVariables--

		function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false, $useUuid = false){

			$newid = parent::insertRecord($variables, $createdby, $overrideID, $replace, $useUuid);

			//we need to create the some default supporting records
			//first a single column.
			$querystatement = "INSERT INTO `tablecolumns`
			(`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`)
			VALUES ('".$variables["uuid"]."','id','".$variables["maintable"].".id','left','',0,'',0,'',NULL, '');";
			$this->db->query($querystatement);

			//next default button options
			$querystatement = "INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`)
			VALUES ('".$variables["uuid"]."','new','1',0,0, '');";
			$this->db->query($querystatement);

			$querystatement = "INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`)
			VALUES ('".$variables["uuid"]."','edit','1','1',0, '');";
			$this->db->query($querystatement);

			$querystatement = "INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`)
			VALUES ('".$variables["uuid"]."','printex','1',0,0, '');";
			$this->db->query($querystatement);

			$querystatement = "INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`)
			VALUES ('".$variables["uuid"]."','select','1',0,0,'');";
			$this->db->query($querystatement);

			$querystatement = "INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`)
			VALUES ('".$variables["uuid"]."','import','0',0,0,'Admin');";
			$this->db->query($querystatement);

			//next quicksearch
			$querystatement = "INSERT INTO `tablefindoptions` (`tabledefid`, `name`, `search`, `displayorder`, `roleid`)
			VALUES ('".$variables["uuid"]."','All Records','".$variables["maintable"].".id!=-1',0, '');";
			$this->db->query($querystatement);

			//and last findfields
			$querystatement = "INSERT INTO `tablesearchablefields` (`tabledefid`, `field`, `name`, `displayorder`, `type`)
			VALUES ('".$variables["uuid"]."','".$variables["maintable"].".id','id',1,'field');";
			$this->db->query($querystatement);

			return $newid;

		}
	}//end class
}//end if

if(class_exists("searchFunctions")){
	class tabledefsSearchFunctions extends searchFunctions{

		function delete_record($useUUID = false){

			if(!$useUUID){
				$whereclause = $this->buildWhereClause();
				//support tables link to tabledefs using uuids not ids, so we must make sure that they are uuids.
				$this->idsArray = getUuidArray($this->db, "tbld:5c9d645f-26ab-5003-b98e-89e9049f8ac3", $this->idsArray);
			}else
				$whereclause = $this->buildWhereClause($this->maintable.".uuid");

			//passed variable is array of user ids to be revoked
			$linkedwhereclause="";
			$relationshipswhereclause="";

			$linkedwhereclause = $this->buildWhereClause("tabledefid");
			$relationshipswhereclause = $this->buildWhereClause("fromtableid")." OR ".$this->buildWhereClause("totableid");

			$querystatement = "DELETE FROM tablecolumns WHERE ".$linkedwhereclause.";";
			$queryresult = $this->db->query($querystatement);

			$querystatement = "DELETE FROM tablefindoptions WHERE ".$linkedwhereclause.";";
			$queryresult = $this->db->query($querystatement);

			$querystatement = "DELETE FROM tableoptions WHERE ".$linkedwhereclause.";";
			$queryresult = $this->db->query($querystatement);

			$querystatement = "DELETE FROM tablesearchablefields WHERE ".$linkedwhereclause.";";
			$queryresult = $this->db->query($querystatement);

			$querystatement = "DELETE FROM usersearches WHERE ".$linkedwhereclause.";";
			$queryresult = $this->db->query($querystatement);

			$querystatement = "DELETE FROM relationships WHERE ".$relationshipswhereclause.";";
			$queryresult = $this->db->query($querystatement);

			$querystatement = "DELETE FROM tabledefs WHERE ".$whereclause.";";
			$queryresult = $this->db->query($querystatement);

			$message = $this->buildStatusMessage();
			$message.=" deleted.";
			return $message;
		}//end method

	}//end class
}//end if
?>
