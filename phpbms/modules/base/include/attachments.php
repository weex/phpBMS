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
if(class_exists("files")){
	class attachments extends files{


		function getRecord($id, $useUuid = false){

			if(!$useUuid){

				$id = (int) $id;
				$whereField = "id";

			}else{

				$id = mysql_real_escape_string($id);
				$whereField = "uuid";

			}//end if

			$querystatement = "
				SELECT
					`files`.`id`,
					`files`.`uuid`,
					`attachments`.`id` AS `attachmentid`,
					`name`,
					`description`,
					`type`,
					`roleid`,
					ISNULL(`file`) AS `nofile`,
					`attachments`.`createdby`,
					`attachments`.`creationdate`,
					`attachments`.`modifiedby`,
					`attachments`.`modifieddate`
				FROM
					`attachments`INNER JOIN `files` ON `attachments`.`fileid`=`files`.`uuid`
				WHERE
					`attachments`.`".$whereField."`='".$id."'
				";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult))
				$therecord = $this->db->fetchArray($queryresult);
			else
				$therecord = $this-> getDefaults();


			return $therecord;

		}

		function getDefaults(){
			$therecord = parent::getDefaults();

			$therecord["attachmentid"] = NULL;
			$therecord["nofile"] = 1;

			return $therecord;

		}


		function prepareVariables($variables){

			$variables["getid"] = $variables["attachmentid"]; //to fix the get record problem

			return parent::prepareVariables($variables);

		}//end method


		function updateRecord($variables, $modifiedby = NULL, $useUuid = false){
			parent::updateRecord($variables, $modifiedby, $useUuid);

			$_POST["id"] = $variables["attachmentid"];

		}


		function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false, $useUuid = false){

			if($createdby == NULL)
				$createdby = $_SESSION["userinfo"]["id"];

			if($variables["newexisting"]=="new"){
				//we need to add a new file record before adding a new
				//attachment record
				$variables["fileid"] = parent::insertRecord($variables, $createdby, $overrideID, $replace, $useUuid);
			}

			//next we create the attachment record
			$querystatement = "
				SELECT
					`maintable`
				FROM
					`tabledefs`
				WHERE
					`uuid` = '".mysql_real_escape_string($variables["tabledefid"])."'
				";

			$queryresult = $this->db->query($querystatement);
			$therecord = $this->db->fetchArray($queryresult);
			$tabldefid = mysql_real_escape_string($variables["tabledefid"]);
			$maintable = mysql_real_escape_string($therecord["maintable"]);

			$querystatement = "
				SELECT
					`uuid`
				FROM
					`".$maintable."`
				WHERE
					`id` = '".$variables["recordid"]."'
				";

			$queryresult = $this->db->query($querystatement);
			$therecord = $this->db->fetchArray($queryresult);
			$recordid = mysql_real_escape_string($therecord["uuid"]);

			$querystatement = "
				SELECT
					`uuid`
				FROM
					`files`
				WHERE
					`id` = '".$variables["fileid"]."'
				";

			$queryresult = $this->db->query($querystatement);
			$therecord = $this->db->fetchArray($queryresult);
			$fileid = mysql_real_escape_string($therecord["uuid"]);

			$querystatement="INSERT INTO attachments ";
			$querystatement.="(fileid,tabledefid,recordid,
								createdby,creationdate,modifiedby) VALUES (";

			$querystatement.="'".$fileid."', ";
			$querystatement.="'".$tabldefid."', ";
			$querystatement.="'".$recordid."', ";

			$querystatement.=$createdby.", ";
			$querystatement.="Now(), ";
			$querystatement.=$createdby.")";

			$queryresult = $this->db->query($querystatement);

			if($queryresult)
				return $this->db->insertId();
			else
				return false;

		}//end method

	}//end class
}//end if

if(class_exists("searchFunctions")){
	class attachmentsSearchFunctions extends searchFunctions{

		function delete_record($useUUID = false){

			if(!$useUUID){
				$whereclause=$this->buildWhereClause();
				$fieldname = "`id`";
			}else{
				$whereclause = $this->buildWhereClause($this->maintable.".uuid");
				$fieldname = "`uuid`";
			}

			$rowsdeleted=0;
			foreach($this->idsArray as $id){
				$querystatement = "SELECT fileid FROM attachments WHERE ".$fieldname."='".$id."'";
				$queryresult = $this->db->query($querystatement);
				$therecord=$this->db->fetchArray($queryresult);

				$querystatement = "DELETE FROM attachments WHERE ".$fieldname."='".$id."'";
				$queryresult = $this->db->query($querystatement);
				$rowsdeleted++;

				$querystatement = "SELECT id FROM attachments WHERE fileid='".$therecord["fileid"]."'";
				$queryresult = $this->db->query($querystatement);

				if(!$this->db->numRows($queryresult)){
					$querystatement = "DELETE FROM files WHERE `uuid`='".$therecord["fileid"]."'";
					$queryresult = $this->db->query($querystatement);
				}

			}

			$querystatement = "DELETE FROM attachments WHERE ".$whereclause;
			$queryresult = $this->db->query($querystatement);

			$message = $this->buildStatusMessage($rowsdeleted);
			$message.=" deleted.";
			return $message;
		}

	}//end class
}//end if
?>