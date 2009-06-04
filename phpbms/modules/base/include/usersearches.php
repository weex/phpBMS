<?php
/*
 $Rev: 205 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-03-26 16:50:25 -0600 (Mon, 26 Mar 2007) $
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
	class userSearches extends phpbmsTable{

		var $_availableUserUUIDs = NULL;
		var $_availableTabledefUUIDs = NULL;
		var $_availableRoleUUIDs = NULL;

		function verifyVariables($variables){

			//table default (SCH) is sufficient
			if(isset($variables["type"])){

				switch($variables["type"]){

					case "SRT":
					case "SCH":
					break;

					default:
						$this->verifyErrors[] = "The value of `type` field is invalid. Its value must be
							'SRT' or 'SCH'.";
					break;

				}//end switch

			}//end if

			//table default (0) is sufficient
			if(isset($variables["userid"])){

				if($this->_availableUserUUIDs === NULL){

					$this->_availableUserUUIDs = $this->_loadUUIDList("users");
					$this->_availableUserUUIDs[] = '';

				}//endif

				if(!in_array((string) $variables["userid"], $this->_availableUserUUIDs))
					$this->verifyErrors[] = "The `userid` field does not give an existing/acceptable user id number.";

			}//end if

			//The table default is not enough, so it must be set
			if(isset($variables["tabledefid"])){

				if($this->_availableTabledefUUIDs === NULL)
						$this->_availableTabledefUUIDs = $this->_loadUUIDList("tabledefs");

				if(!in_array($variables["tabledefid"], $this->_availableTabledefUUIDs))
					$this->verifyErrors[] = "The `tabledefid` field does not give an existing/acceptable table definition id number.";

			} else
				$this->verifyErrors[] = "The `tabledefid` field must be set.";

			//table default (0) is sufficient
			if(isset($variables["roleid"])){

				if($this->_availableRoleUUIDs === NULL){

					$this->_availableRoleUUIDs = $this->_loadUUIDList("roles");
					$this->_availableRoleUUIDs[] = "";
					$this->_availableRoleUUIDs[] = "Admin";

				}//endif

				if(!in_array(((string) $variables["roleid"]), $this->_availableRoleUUIDs))
					$this->verifyErrors[] = "The `roleid` field does not give an existing/acceptable role id number.";

			}//end if

			return parent::verifyVariables($variables);

		}//end function verifyVariables


                /**
                 * display tables drop down for tabledefs
                 *
                 * @param string $fieldname name/id to give the select box
                 * @param string $selectedid tabledef uuid currently selected
                 */
		function showTableSelect($fieldname, $selectedid){

                    $querystatement="
                        SELECT
                            uuid,
                            displayname
                        FROM
                            tabledefs
                        ORDER
                            BY displayname";

                    $queryresult = $this->db->query($querystatement);

                    ?><select id="<?php echo $fieldname ?>" name="<?php echo $fieldname ?>"><?php

                    while($therecord = $this->db->fetchArray($queryresult)){

                        ?><option value="<?php echo $therecord["uuid"]?>" <?php if($selectedid == $therecord["uuid"]) echo 'selected="selected"' ?>><?php

                        echo formatVariable($therecord["displayname"]);

                        ?></option><?php

                    }//endwhile

                    ?></select><?php

		}//end function showTableSelect


		function updateRecord($variables, $modifiedby = NULL){

			if(isset($variables["makeglobal"]))
				$variables["userid"] = '';

			parent::updateRecord($variables, $modifiedby);

		}//end function updateRecord

	}//end class
}//end if
?>
