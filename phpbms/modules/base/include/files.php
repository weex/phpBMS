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
	class files extends phpbmsTable{

		var $_availableRoleUUIDs = NULL;

		function getPicture($name){
			if (function_exists('file_get_contents')) {
				$file = addslashes(file_get_contents($_FILES[$name]['tmp_name']));
			} else {
				// If using PHP < 4.3.0 use the following:
				$file = addslashes(fread(fopen($_FILES[$name]['tmp_name'], 'r'), filesize($_FILES[$name]['tmp_name'])));
			}

			return $file;
		}
		
		
		/**
          * function getDefaults
          *
          * Retrieves default values for a single record
          *
          * Uses the field names to guess a default value.  If it cannot find
          * one of the standard names it sets the default value based on the type
          *
          * @retrun array associative array with record defaults
          */
        function getDefaults(){
			
			$therecord = parent::getDefaults();
			
			$therecord["apifileurl"] = "";
			
			return $therecord;
			
		}//end function --getDefaults


		/**
         * function getRecord
         *
         * Retrieves a single record from the database
         *
         * @param integer|string $id the record id or uuid
         * @param bool $useUuid specifies whther the $id is a uuid (true) or not.  Default is false
         *
         * @return array the record as an associative array
         */
        function getRecord($id, $useUuid = false){
			
			if($useUuid){
			
				$id = mysql_real_escape_string($id);
				$whereclause = "`uuid` = '".$id."'";
			
			} else {
			
				$id = (int) $id;
				$whereclause = "`id` = ".$id;
			
			}//endif
			
			$querystatement = "
				SELECT
					`id`,
					`uuid`,
					`name`,
					`description`,
					`type`,
					`createdby`,
					`creationdate`,
					`modifiedby`,
					`modifieddate`,
					`roleid`,
					'' AS `file`,
					`custom1`,
					`custom2`,
					`custom3`,
					`custom4`,
					`custom5`,
					`custom6`,
					`custom7`,
					`custom8`
				FROM
					`files`
				WHERE
					".$whereclause;
					
			$queryresult = $this->db->query($querystatement);
					
			if($this->db->numRows($queryresult)){
                $therecord = $this->db->fetchArray($queryresult);
				
				if(!empty($_SERVER["HTTPS"]))
					$protocol = "https://";
				else
					$protocol = "http://";
					
				
				if( ($_SERVER["SERVER_PORT"] == "443" && !empty($_SERVER["HTTPS"])) || ($_SERVER["SERVER_PORT"] == "80" && empty($_SERVER["HTTPS"])) )
					$port = "";
				else
					$port = ":".$_SERVER["SEVER_PORT"];
				
				$therecord["apifileurl"] = $protocol.$_SERVER["SERVER_NAME"].$port.APP_PATH."modules/api/api_servefile.php?i=".(int)$therecord["id"];
			}else
                $therecord = $this-> getDefaults();
			
			return $therecord;
			
		}//end function --getRecord--
		
		
		function verifyVariables($variables){

			//if it is set, we'll have to check, if not, it defaults to '' which is an acceptable
			//value.
			if(isset($variables["roleid"])){

				//check to see if the RoleIDs are populated
				if($this->_availableRoleUUIDs === NULL){
					$this->_availableRoleUUIDs = $this->_loadUUIDList("roles");
					$this->_availableRoleUUIDs[] = "";
					$this->_availableRoleUUIDs[] = "Admin";
				}//end if

				if(!in_array(((string)$variables["roleid"]), $this->_availableRoleUUIDs))
					$this->verifyErrors[] = "The `roleid` field does not give an existing/acceptable role id number.";

			}//end if

			return parent::verifyVariables($variables);

		}//end method


		function prepareVariables($variables){

			if(isset($_FILES['upload']))
				if($_FILES['upload']["name"]){

					$variables["name"] = $_FILES['upload']["name"];
					$variables["type"] = $_FILES['upload']['type'];
					$variables["file"] = $this->getPicture("upload");

				} else {
					unset($this->fields["type"]);
					unset($this->fields["file"]);
				}//end if

			return parent::prepareVariables($variables);

		}//end function


		function updateRecord($variables, $modifiedby = NULL, $useUuid = false){

			$thereturn = parent::updateRecord($variables, $modifiedby, $useUuid);

			//restore the fields
			$this->getTableInfo();

			return $thereturn;
		}//end method


		function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false, $useUuid = false){

			$newid = parent::insertRecord($variables, $createdby, $overrideID, $replace, $useUuid);

			//restore the fields
			$this->getTableInfo();

			return $newid;

		}//end method

	}//end class

}//end if

if(class_exists("searchFunctions")){
	class filesSearchFunctions extends searchFunctions{

		function delete_record($useUUID = false){

			if(!$useUUID){
				$whereclause = $this->buildWhereClause();

				//attachments.fileid must be a uuid and not an id.
				//so, we adjust the attachment where clause to always be
				//uuids.

				$this->idsArray = getUuidArray($this->db, "tbld:80b4f38d-b957-bced-c0a0-ed08a0db6475", $this->idsArray);

			}else
				$whereclause = $this->buildWhereClause($this->maintable.".uuid");


			$attachmentwhereclause = $this->buildWhereClause("attachments.fileid");

			$querystatement = "DELETE FROM attachments WHERE ".$attachmentwhereclause." AND attachments.fileid!='file:c1818692-cfaa-8536-7f62-e385c0f6920d';";
			$queryresult = $this->db->query($querystatement);

			$querystatement = "DELETE FROM files WHERE ".$whereclause." AND files.uuid!='file:c1818692-cfaa-8536-7f62-e385c0f6920d';";
			$queryresult = $this->db->query($querystatement);

			$message = $this->buildStatusMessage();
			$message.=" deleted";
			return $message;
		}

	}//end class
}//end if
?>