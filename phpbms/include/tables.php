<?php
/*
 $Rev: 249 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-07-02 15:50:36 -0600 (Mon, 02 Jul 2007) $
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


	class phpbmsTable{

		var $db = NULL;
		var $backurl = NULL;
		var $verifyErrors = array();

		// The table definition record id.
		var $id=0;

		var $fields = array();

		function phpbmsTable($db,$tabledefid = 0,$backurl = NULL){

			if(is_object($db))
				if(get_class($db)=="db")
					$this->db = $db;
			if($this->db === NULL)
				$error = new appError(-800,"database object is required for parameter 1.","Initializing phpbmsTable Class");

			$this->id = ((int) $tabledefid);

			if($backurl == NULL)
				$this->backurl = APP_PATH."search.php?id=".$this->id;
			else
				$this->backurl = $backurl;

			if(!$this->getTableInfo())
				$error = new appError(-810,"Table definition not found for id ".$this->id,"Initializing phpbmsTable Class");
		}


		function getTableInfo(){
			$querystatement = "SELECT * FROM tabledefs WHERE id=".$this->id;

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){
				foreach($this->db->fetchArray($queryresult) as $key => $value)
					$this->$key = $value;

				$this->fields = $this->db->tableInfo($this->maintable);

				return true;
			} else
				return false;
		}

		function getDefaultByType($fieldtype){
				$default = NULL;

				switch ($fieldtype){
					case "blob":
					case "string":
						$default = "";
					break;
					case "real":
					case "int":
						$default = 0;
					break;
					case "date":
						$default=dateToString(mktime(),"SQL");
					break;
					case "time":
						$default=timeToString(mktime(),"SQL");
					break;
					case "year":
						$default=strftime("%Y");
					break;
					case "datetime":
					case "timestamp":
						$default = dateToString(mktime(),"SQL")." ".timeToString(mktime(),"24 Hour");
					break;
				}

				return $default;

		}

		function prepareFieldForSQL($value,$type,$flags){
				switch ($type){

					case "blob":
					case "string":
						if($value === "" or $value === NULL){
							if(strpos($flags,"not_null") === false)
								$value = NULL;
							else
								$value = "''";
						} else
							$value = "'".$value."'";
					break;

					case "real":
						if($value === "" or $value === NULL){
							if(strpos($flags,"not_null") === false)
								$value = NULL;
							else
								$value = 0;
						} else
							$value = (real) $value;
					break;

					case "int":
						if($value === "" or $value === NULL){
							if(strpos($flags,"not_null") === false)
								$value = NULL;
							else
								$value = 0;
						} else
							$value = (int) $value;
					break;

					case "date":
						if($value === "" or $value === NULL){
							if(strpos($flags,"not_null") === false)
								$value = NULL;
							else
								$value = "'".dateToString(mktime(),"SQL")."'";
						} else
							$value = "'".sqlDateFromString($value)."'";
					break;

					case "time":
						if($value === "" or $value === NULL){
							if(strpos($flags,"not_null") === false)
								$value = NULL;
							else
								$value = "'".timeToString(mktime(),"SQL")."'";
						} else
							$value = "'".sqlTimeFromString($value)."'";
					break;

					case "year":
						if($value === "" or $value === NULL)
							if(strpos($flags,"not_null") === false)
								$value = NULL;
							else
								$value = strftime("%Y");
					break;

					case "datetime":
					case "timestamp":
						if($value === "" or $value === NULL){
							if(strpos($flags,"not_null") === false)
								$value = NULL;
							else
								$value = "'".dateToString(mktime(),"SQL")." ".timeToString(mktime(),"24 Hour")."'";
						} else{
							$datetimearray = explode(" ",$value);
							if(count($datetimearray) > 1){
								$value = "'".sqlDateFromString($datetimearray[0])." ".sqlTimeFromString($datetimearray[1])."'";
							} else
							$value = "'".$value."'";
						}
					break;
					case "password":
						$value = "ENCODE('".$value."','".ENCRYPTION_SEED."')";
					break;
				}//end case


				if($value === NULL)
					$value = "NULL";
				return $value;
		}//end method


		function getDefaults(){
			$therecord = array();

			foreach($this->fields as $fieldname => $thefield){
				switch($fieldname){
					case "id":
					case "modifiedby":
					case "modifieddate":
						$therecord[$fieldname] = NULL;
					break;

					case "createdby":
						$therecord["createdby"] = $_SESSION["userinfo"]["id"];
					break;

					default:
						if(strpos($thefield["flags"],"not_null") === false)
							$therecord[$fieldname] = NULL;
						else {
							$therecord[$fieldname] = $this->getDefaultByType($thefield["type"]);
						}
					break;
				}//end switch
			}//end foreach

			return $therecord;
		}


		function getRecord($id = 0){
			$id = (int) $id;

			$querystatement = "SELECT ";

			foreach($this->fields as $fieldname => $thefield){
				if(isset($thefield["select"]))
					$querystatement .= "(".$thefield["select"].") AS `".$fieldname."`, ";
				else
					$querystatement .= "`".$fieldname."`, ";
			}//end foreach
			$querystatement = substr($querystatement, 0, strlen($querystatement)-2);

			$querystatement .= " FROM `".$this->maintable."` WHERE `".$this->maintable."`.`id` = ".$id;

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult))
				$therecord = $this->db->fetchArray($queryresult);
			else
				$therecord = $this-> getDefaults();

			return $therecord;
		}//end getRecord function


		function prepareVariables($variables){

			return $variables;

		}//end method --prepareVariables--


		function verifyVariables($variables){

			$thereturn = array();

			if(!isset($this->verifyErrors))
				$this->verifyErrors = array();

			if(isset($variables["id"]))
				if(!is_numeric($variables["id"]) && $variables["id"])
					$this->verifyErrors[] = "The `id` field must be numeric or equivalent to zero (although positive is reccomended).";

			if(isset($variables["inactive"]))
				if($variables["inactive"] && $variables["inactive"] != 1)
					$this->verifyErrors[] = "The `inactive` field must be a boolean (equivalent to 0 or exactly 1).";

			if(count($this->verifyErrors))
				$thereturn = $this->verifyErrors;

			unset($this->verifyErrors);

			return $thereturn;

		}//end method --verifyVariables--


		function updateRecord($variables, $modifiedby = NULL){

			$variables = addSlashesToArray($variables);

			if($modifiedby === NULL)
				if(isset($_SESSION["userinfo"]["id"]))
					$modifiedby = $_SESSION["userinfo"]["id"];
				else
					$error = new appError(-840,"Session Timed Out.","Creating New Record");

			if(!isset($variables["id"]))
				$error = new appError(-820,"id not set","Updating Record");

			$updatestatement = "UPDATE `".$this->maintable."` SET ";

			foreach($this->fields as $fieldname => $thefield){
				if(!isset($thefield["select"])){
					switch($fieldname){
						case "id":
						case "creationdate":
						case "createdby":
						break;

						case "modifiedby":
							$updatestatement .= "`modifiedby` = ".((int) $modifiedby).", ";
						break;

						case "modifieddate":
							$updatestatement .= "`modifieddate` = NOW(), ";
						break;

						default:
							if(!isset($variables[$fieldname]) && strpos($thefield["flags"],"not_null") !== false)
								$variables[$fieldname] = $this->getDefaultByType($thefield["type"],true);

							if(isset($variables[$fieldname]))
								$updatestatement .= "`".$fieldname."` = ".$this->prepareFieldForSQL($variables[$fieldname],$thefield["type"],$thefield["flags"]).", ";
						break;
					}//end switch field name
				}//end if
			}//end foreach
			$updatestatement = substr($updatestatement, 0, strlen($updatestatement)-2);

			$updatestatement .= " WHERE `id`=".((int) $variables["id"]);

			$updateresult = $this->db->query($updatestatement);


			return true;
		}


		function insertRecord($variables,$createdby = NULL, $overrideID = false, $replace = false){

			if($createdby === NULL)
				if(isset($_SESSION["userinfo"]["id"]))
					$createdby = $_SESSION["userinfo"]["id"];
				else
					$error = new appError(-840,"Session Timed Out.","Creating New Record");


			$variables = addSlashesToArray($variables);

			$fieldlist = "";
			$insertvalues = "";
			foreach($this->fields as $fieldname => $thefield){
				if(!isset($thefield["select"])){
					switch($fieldname){
						case "id":
							if(isset($variables["id"]))
								if($overrideID && $variables["id"]){
									$fieldlist .= "id, ";
									$insertvalues .= ((int) $variables["id"]).", ";
								}//endif
							break;

						case "createdby":
						case "modifiedby":
							$fieldlist .= $fieldname.", ";
							$insertvalues .= ((int) $createdby).", ";
							break;

						case "creationdate":
						case "modifieddate":
							$fieldlist .= $fieldname.", ";
							$insertvalues .= "NOW(), ";
							break;

						default:
							if(!isset($variables[$fieldname]) && strpos($thefield["flags"],"not_null") !== false)
								$variables[$fieldname] = $this->getDefaultByType($thefield["type"],true);

							if(isset($variables[$fieldname])){
								$fieldlist .= "`".$fieldname."`, ";
								$insertvalues .= $this->prepareFieldForSQL($variables[$fieldname],$thefield["type"],$thefield["flags"]).", ";
							}//endif - fieldname
							break;
					}//end switch field name
				}//end if
			}//end foreach
			$fieldlist = substr($fieldlist, 0, strlen($fieldlist)-2);
			$insertvalues = substr($insertvalues, 0, strlen($insertvalues)-2);

			if($replace)
				$insertstatement = "REPLACE";
			else
				$insertstatement = "INSERT";

			$insertstatement .= " INTO ".$this->maintable." (".$fieldlist.") VALUES (".$insertvalues.")";
			$insertresult = $this->db->query($insertstatement);

			if($insertresult)
				return $this->db->insertId();
			else
				return false;
		}


		function processAddEditPage(){
			if(!isset($_POST["command"])){

				if(isset($_GET["id"])){
					//editing
					if(!hasRights($this->editroleid))
						goURL(APP_PATH."noaccess.php");
					else
						return $this->getRecord((integer) $_GET["id"]);
				} else {
					if(!hasRights($this->addroleid))
						goURL(APP_PATH."noaccess.php");
					else
						return $this->getDefaults();
				}
			}
			else
			{
				switch($_POST["command"]){
					case "cancel":
						// if we needed to do any clean up (deleteing temp line items)
						if(!isset($_POST["id"])) $_POST["id"]=0;

						$theurl = $this->backurl;

						if(isset($_POST["id"]))
							$theurl .= "#".((int) $_POST["id"]);
						goURL($theurl);
					break;
					case "save":

						$variables = $this->prepareVariables($_POST);
						$errorArray = $this->verifyVariables($variables);

						if($_POST["id"]) {

							$theid = $variables["id"];

							if(!count($errorArray)){

								$this->updateRecord($variables);

								//get record
								$therecord = $this->getRecord($theid);
								$therecord["phpbmsStatus"] = "Record Updated";
							}else{
								foreach($errorArray as $error)
									$logError = new appError(-900, $error, "Verification Error");

								//get record
								$therecord = $this->getRecord($theid);
								$therecord["phpbmsStatus"] = "Data Verification Error";
							}//end if



							return $therecord;
						}
						else {

							$theid = 0;

							if(!count($errorArray)){
								$theid = $this->insertRecord($variables);
								//get record
								$therecord = $this->getRecord($theid);
								$therecord["phpbmsStatus"] = "<div style=\"float:right;margin-top:-3px;\"><button type=\"button\" accesskey=\"n\" class=\"smallButtons\" onclick=\"document.location='".str_replace("&","&amp;",$_SERVER["REQUEST_URI"])."'\">add new</button></div>";
								$therecord["phpbmsStatus"] .= "Record Created";
							}else{
								foreach($errorArray as $error)
									$logError = new appError(-900, $error, "Verification Error");

								//get record
								$therecord = $this->getRecord($theid);
								$therecord["phpbmsStatus"] .= "Data Verification Error";
							}//end if

							return $therecord;
						}
					break;
				}//end command switch
			}// end if
		}// end function
	}//end class
?>