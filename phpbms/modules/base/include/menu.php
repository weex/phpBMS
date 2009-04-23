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
	class menus extends phpbmsTable{

		var $availableRoleIDs = array();

		function getDefaults(){

			$therecord = parent::getDefaults();

			$therecord["uuid"] = uuid("menu:");

			return $therecord;

		}//end function - getDefaults

		function checkParentMenuUUIDs($currentUUID = 0, $parentUUID = 0){

			if($parentUUID === 0)
				return true;

			//cannot be own parent
			$currentUUID = mysql_real_escape_string($currentUUID);
			//current setting of the parentid
			$parentUUID = mysql_real_escape_string($parentUUID);

			$querystatement = "
				SELECT
					`id`
				FROM
					`menu`
				WHERE
					`uuid` = '".$parentUUID."'
					AND
					`uuid`!='".$currentUUID."'
					AND
					`parentid` = '0'
					AND
					(
						`link` = ''
						OR
						`link` IS NULL
					)
					;
				";

			$queryresult = $this->db->query($querystatement);

			return $this->db->numRows($queryresult);

		}//end method --getParentMenuIDs--

		//pouplate the id roles array
		function populateRoleArray(){

			$this->availableRoleIDs = array();

			$querystatement = "
				SELECT
					`id`
				FROM
					`roles`;
				";

			$queryresult = $this->db->query($querystatement);

			$this->availableRoleIDs[] = 0;//for everyone
			$this->availableRoleIDs[] = -100;//for admin


			while($therecord = $this->db->fetchArray($queryresult))
				$this->availableRoleIDs[] = $therecord["id"];

		}//end method --populateRoleArray()--


		function verifyVariables($variables){

			if(isset($variables["uuid"])){
				if($variables["uuid"] === "" && $variables["uuid"] === NULL)
					$this->verifyErrors[] = "The `uuid` field cannot be blank.";
			}else
				$this->verifyErrors[] = "The `uuid` field must be set.";


			//table default (0) for `roleid` is ok (i.e. doesn't have to be set)
			if(isset($variables["roleid"])){

				//can either be numeric or equivalent to 0
				if(is_numeric($variables["roleid"]) || !$variables["roleid"]){

					//check for populated role id array
					if(!count($this->availableRoleIDs))
						$this->populateRoleArray();

					//check to see if the int typecast role id is in one of the available ones
					if(!in_array(((int)$variables["roleid"]), $this->availableRoleIDs))
						$this->verifyErrors[] = "The `roleid` field does not give an existing/acceptable role id number.";
				}else
					$this->verifyErrors[] = "The `roleid` field must be numeric or equivalent to 0.";

			}//end if

			//check parent ids under certain circumstances
			//not set is acceptable
			if(isset($variables["parentid"])){

				//can be either numeric or equivalent to 0 and its int typecast must be non-negative
				if($variables["parentid"] !== "" && $variables["parentid"] !== NULL){

					$uuid = 0;// can still check for an invalid parentid even though the current uuid is bad

					//use the current id if it exists (A menu record cannot be its own parent)
					if(isset($variables["uuid"]))
						if($variables["uuid"] !== "" && $variables["uuid"] !== NULL)
							$uuid = $variables["uuid"];

					//Select run every time because `id` can be different
					if( !$this->checkParentMenuUUIDs($uuid, $variables["parentid"]) )
						$this->verifyErrors[] = "The `parentid` field does not give an existing/acceptable parent id number.";

				}else
					$this->verifyErrors[] = "The `parentid` field must be not be blank.";

			}//end if

			return parent::verifyVariables($variables);

		}//end method --verifyVariables--


		function prepareVariables($variables){
			switch($variables["radio"]){
				case "cat":
					$variables["link"] = "";
					$variables["parentid"] = 0;
				break;
				case "search":
					$variables["link"] = $variables["linkdropdown"];
				break;
				case "link":
				default:
			}

			return $variables;
		}


		function displayTableDropDown($selectedlink){

			$querystatement="select id, displayname from tabledefs order by displayname";
			$thequery=$this->db->query($querystatement);

			echo "<select id=\"linkdropdown\" name=\"linkdropdown\">\n";
			while($therecord=$this->db->fetchArray($thequery)){
				echo "<option value=\"search.php?id=".$therecord["id"]."\" ";

				if ($selectedlink == "search.php?id=".$therecord["id"])
					echo "selected=\"selected\"";

				echo " >".$therecord["displayname"]."</option>\n";
			}
			echo "</select>\n";

		}//end method


		function displayParentDropDown($selectedpid, $uuid){

			//if($uuid == "")
			//	$uuid = 0;

			$querystatement = "
				SELECT
					`uuid`,
					`name`
				FROM
					`menu`
				WHERE
					`uuid` != '".$uuid."'
					AND
					`parentid` = 0
					AND
					(
						link=\"\"
						OR
						link IS NULL
					)
				ORDER BY
					`displayorder`;
				";

			$thequery=$this->db->query($querystatement);

			echo "<select name=\"parentid\" id=\"parentid\">\n";
			echo "<option value=\"0\" ";

			if ($selectedpid=="0")
				echo "selected=\"selected\"";

			echo " >-- none --</option>\n";
			while($therecord=$this->db->fetchArray($thequery)){
				echo "<option value=\"".$therecord["uuid"]."\" ";
				if ($selectedpid==$therecord["uuid"])
					echo "selected=\"selected\"";

				echo " >".$therecord["name"]."</option>\n";
			}
			echo "</select>\n";

		}//end method

	}//end class
}//end if


if(class_exists("searchFunctions")){
	class menuSearchFunctions extends searchFunctions{

		function delete_record(){

			//passed variable is array of user ids to be revoked
			$whereclause = $this->buildWhereClause();
			$verifywhereclause = $this->buildWhereClause("menu.parentid");

			$querystatement = "SELECT id FROM menu WHERE ".$verifywhereclause;
			$queryresult = $this->db->query($querystatement);

			if (!$this->db->numRows($queryresult)){
				$querystatement = "DELETE FROM menu WHERE ".$whereclause;
				$queryresult = $this->db->query($querystatement);
			}

			$message=$this->buildStatusMessage();
			$message.=" deleted.";
			return $message;
		}

	}//end class
}//end if
?>