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

		var $availableTabledefIDs = array();
		var $availableRoleIDs = array();

		function getDefaults(){
			$therecord = parent::getDefaults();

			$therecord["type"]="report";
                        $therecord["uuid"] = uuid("reports:");

			return $therecord;

		}

		//populates tabledef id array
		function populateTabledefArray(){

			$this->availableTabledefIDs = array();

			$querystatement = "
				SELECT
					`id`
				FROM
					`tabledefs`;
				";

			$queryresult = $this->db->query($querystatement);

			//This is for the "global" option
			$this->availableTabledefIDs[] = 0;

			while($therecord = $this->db->fetchArray($queryresult))
				$this->availableTabledefIDs[] = $therecord["id"];



		}//end method --populateRoleArray--

		//populates role id array
		function populateRoleArray(){

			$this->availableRoleIDs = array();

			$querystatement = "
				SELECT
					`id`
				FROM
					`roles`;
				";

			$queryresult = $this->db->query($querystatement);

			//These two are added for no restriction on the role (0) and for
			//administrative restrictioin (-100)
			$this->availableRoleIDs[] = 0;
			$this->availableRoleIDs[] = -100;

			while($therecord = $this->db->fetchArray($queryresult))
				$this->availableRoleIDs[] = $therecord["id"];

		}//end method --populateRoleArray--


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

			//Table Default (0) ok becuase it means report is globally available to any table
			if(isset($variables["tabledefid"])){

				//must be a non-negative number or NULL/""
				if( (!$variables["tabledefid"]) || ((int)$variables["tabledefid"]) > 0 ){

					if(!count($this->availableTabledefIDs))
						$this->populateTabledefArray();

					if( !in_array((int)$variables["tabledefid"], $this->availableTabledefIDs) )
						$this->verifyErrors[] = "The `tabledefid` field does not give an existing/acceptable to table definition id number.";

				}else
					$this->verifyErrors[] = "The `tabledefid` field must be either a non-negative number or equivalent to 0.";

			}//end if

			//Table Default (0) ok becuase it means report is globally available to any user
			if(isset($variables["roleid"])){

				//must be a number or NULL/""
				if(is_numeric($variables["roleid"]) || !$variables["roleid"]){

					if(!count($this->availableRoleIDs))
						$this->populateRoleArray();

					if( !in_array((int)$variables["roleid"], $this->availableRoleIDs) )
						$this->verifyErrors[] = "The `roleid` field does not give an existing/acceptable to role id number.";

				}else
					$this->verifyErrors[] = "The `roleid` field must be numeric or equivalent to 0.";

			}//end if

			return parent::verifyVariables($variables);

		}//end method


		function displayTables($fieldname,$selectedid){

			$querystatement="SELECT id, displayname FROM tabledefs ORDER BY displayname";
			$thequery=$this->db->query($querystatement);

			echo "<select id=\"".$fieldname."\" name=\"".$fieldname."\">\n";

			echo "<option value=\"0\" ";
			if ($selectedid=="0") echo "selected=\"selected\"";
			echo " style=\"font-weight:bold\">global</option>\n";

			while($therecord=$this->db->fetchArray($thequery)){
				echo "	<option value=\"".$therecord["id"]."\"";
					if($selectedid==$therecord["id"]) echo " selected=\"selected\"";
				echo ">".$therecord["displayname"]."</option>\n";
			}

			echo "</select>\n";
		}//end method

	}//end class
}//end if
?>