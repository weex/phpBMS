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
if(class_exists("phpbmsTable")) {
	class reports extends phpbmsTable{

		var $_availableTabledefUUIDs = array();
		var $_availableRoleUUIDs = array();

		function getDefaults(){
			$therecord = parent::getDefaults();

			$therecord["type"]="report";
                        $therecord["uuid"] = uuid("reports:");

			return $therecord;

		}


		function verifyVariables($variables){

			//cannot be table default ("")
			if(isset($variables["reportfile"])){
				if($variables["reportfile"] === "" || $variables["reportfile"] === NULL)
					$this->verifyErrors[] = "The `reportfile` field must not be blank.";
			}else
				$this->verifyErrors[] = "The `reportfile` field must be set.";

			//Table default (NULL) OK
			if(isset($variables["type"]))
				if($variables["type"] !== "")//don't care if it's ""
					switch($variables["type"]){
						case "report":
						case "PDF Report":
						case "export":
							break;

						default:
							$this->verifyErrors[] = "The `type` field is not an accepted value.  It must be 'report', 'PDF Report', or 'export.";
						break;

					}//end switch

			//Table Default ('') ok becuase it means report is globally available to any table
			if(isset($variables["tabledefid"])){

				if(!count($this->_availableTabledefUUIDs)){
					$this->_availableTabledefUUIDs = $this->_loadUUIDList("tabledefs");
					//add the global option
					$this->_availableTabledefUUIDs[] = "";
				}//end if

				if( !in_array((string)$variables["tabledefid"], $this->_availableTabledefUUIDs) )
					$this->verifyErrors[] = "The `tabledefid` field does not give an existing/acceptable table definition uuid.";

			}//end if

			//Table Default ('') ok becuase it means report is globally available to any user
			if(isset($variables["roleid"])){

				if(!count($this->_availableRoleUUIDs)){
					$this->_availableRoleUUIDs = $this->_loadUUIDList("roles");
					$this->_availableRoleUUIDs[] = ""; // for no role restrictions
					$this->_availableRoleUUIDs[] = "Admin"; //for the Admin restriction
				}//end if

				if( !in_array((string)$variables["roleid"], $this->_availableRoleUUIDs) )
					$this->verifyErrors[] = "The `roleid` field does not give an existing/acceptable to role id number.";

			}//end if

			return parent::verifyVariables($variables);

		}//end method


		function displayTables($fieldname,$selectedid){

			$querystatement="SELECT uuid, displayname FROM tabledefs ORDER BY displayname";
			$thequery=$this->db->query($querystatement);

			echo "<select id=\"".$fieldname."\" name=\"".$fieldname."\">\n";

			echo "<option value=\"\" ";
			if ($selectedid=="") echo "selected=\"selected\"";
			echo " style=\"font-weight:bold\">global</option>\n";

			while($therecord=$this->db->fetchArray($thequery)){
				echo "	<option value=\"".$therecord["uuid"]."\"";
					if($selectedid==$therecord["uuid"]) echo " selected=\"selected\"";
				echo ">".$therecord["displayname"]."</option>\n";
			}

			echo "</select>\n";
		}//end method

	}//end class
}//end if
?>