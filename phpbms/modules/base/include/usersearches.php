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

		var $availableUserIDs = array();
		var $availableTabledefIDs = array();
		var $availableRoleIDs = array();


		function populateUserArray(){

			$this->availableUserIDs = array();

			$querystatement = "
				SELECT
					`id`
				FROM
					`users`;
				";

			$queryresult = $this->db->query($querystatement);

			$this->availableUserIDs[] = 0;//for global

			while($therecord = $this->db->fetchArray($queryresult))
				$this->availableUserIDs[] = $therecord["id"];

		}//end method --populateUserArray--


		function populateRoleArray(){

			$this->availableRoleIDs = array();

			$querystatement = "
				SELECT
					`id`
				FROM
					`roles`;
				";

			$queryresult = $this->db->query($querystatement);

			$this->availableRoleIDs[] = 0;//for global
			$this->availableRoleIDs[] = -100;//for admin

			while($therecord = $this->db->fetchArray($queryresult))
				$this->availableRoleIDs[] = $therecord["id"];


		}//end method --populateRoleArray--


		function populateTabledefArray(){

			$this->availableTabledefIDs = array();

			$querystatement = "
				SELECT
					`id`
				FROM
					`tabledefs`;
				";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){
				while($therecord = $this->db->fetchArray($queryresult))
					$this->availableTabledefIDs[] = $therecord["id"];
			}else
				$this->availableTabledefIDs[] = "none";// so the function doesn't keep
				//getting called


		}//end method --populateRoleArray--


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

				if( !$variables["userid"] || ((int) $variables["userid"] > 0) ){

					if(!count($this->availableUserIDs))
						$this->populateUserArray();

					if(!in_array(((int)$variables["userid"]), $this->availableUserIDs))
						$this->verifyErrors[] = "The `userid` field does not give an existing/acceptable user id number.";

				}else
					$this->verifyErrors[] = "The `userid` field must be a non-negative number or equivalent to 0.";

			}//end if

			//The table default is not enough, so it must be set
			if(isset($variables["tabledefid"])){

				if(is_numeric($variables["tabledefid"]) || !$variables["tabledefid"]){

					if(!count($this->availableTabledefIDs))
						$this->populateTabledefArray();

					if(!in_array(((int)$variables["tabledefid"]), $this->availableTabledefIDs))
						$this->verifyErrors[] = "The `tabledefid` field does not give an existing/acceptable table definition id number.";
				}else
					$this->verifyErrors[] = "The `tabledefid` field must be numeric or equivalent to 0.";
			}else
				$this->verifyErrors[] = "The `tabledefid` field must be set.";

			//table default (0) is sufficient
			if(isset($variables["roleid"])){

				if(is_numeric($variables["roleid"]) || !$variables["roleid"]){

					if(!count($this->availableRoleIDs))
						$this->populateRoleArray();

					if(!in_array(((int)$variables["roleid"]), $this->availableRoleIDs))
						$this->verifyErrors[] = "The `roleid` field does not give an existing/acceptable role id number.";
				}else
					$this->verifyErrors[] = "The `roleid` field must be numeric or equivalent to 0.";

			}//end if

			return parent::verifyVariables($variables);

		}//end method


		function displayTables($fieldname,$selectedid){

			$querystatement="SELECT id, displayname FROM tabledefs ORDER BY displayname";
			$thequery=$this->db->query($querystatement);

			echo "<select id=\"".$fieldname."\" name=\"".$fieldname."\">\n";
			while($therecord=$this->db->fetchArray($thequery)){
				echo "	<option value=\"".$therecord["id"]."\"";
					if($selectedid==$therecord["id"]) echo " selected=\"selected\"";
				echo ">".$therecord["displayname"]."</option>\n";
			}
			echo "</select>\n";
		}


		function updateRecord($variables, $modifiedby = NULL){

			if(isset($variables["makeglobal"]))
				$variables["userid"] = 0;

			parent::updateRecord($variables, $modifiedby);
		}

	}//end class
}//end if
?>