<?php
/*
 $Rev: 254 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-07 18:38:38 -0600 (Tue, 07 Aug 2007) $
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
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

	class receiptitems{

		function receiptitems($db){

			$this->db = $db;

		}//end method

		function get($receiptid){

			$querystatement = "
				SELECT
					`receiptitems`.`aritemid`,
					`receiptitems`.`applied`,
					`receiptitems`.`discount`,
					`receiptitems`.`taxadjustment`,
					`aritems`.`type`,
					`aritems`.`relatedid`,
					`aritems`.`itemdate`,
					`aritems`.`amount`,
					`aritems`.`paid`,
					IF(`aritems`.`type` = 'invoice', `invoices`.`id`, '') AS `invoiceid`
				FROM
					(`receiptitems` INNER JOIN `aritems` ON `receiptitems`.`aritemid` = `aritems`.`uuid`) LEFT JOIN `invoices` ON `aritems`.`relatedid`=`invoices`.`uuid`
				WHERE
					`receiptitems`.`receiptid` = '".mysql_real_escape_string($receiptid)."'
				ORDER BY
					`aritems`.`type`,
					`aritems`.`itemdate`";

				//return $this->db->query($querystatement);

				$queryresult =  $this->db->query($querystatement);

				$thereturn = array();
				while($therecord = $this->db->fetchArray($queryresult))
					$thereturn[] = $therecord;

				return $thereturn;

		}//end function


		function show($itemslist, $receiptPosted, $receiptid){

			$count = 1;

			foreach($itemslist as $therecord){

				$recID = "i".$count;

				if($therecord["type"] == "invoice"){

					$tempDate = stringToDate($therecord["itemdate"], "SQL");
					$dueDate = dateToString( strtotime(TERM1_DAYS." days", $tempDate) );

				} else
					$dueDate = "&nbsp;";

				if($therecord["type"] == "credit" && $therecord["relatedid"] == $receiptid){
					$therecord["relatedid"] = "";
					$therecord["amount"] = 0;
					$therecord["aritemid"] = "";
					$therecord["invoiceid"] = "";
				}

				if($receiptPosted)
					$docDue = $therecord["amount"] - $therecord["paid"];
				elseif($therecord["relatedid"])
					$docDue = $therecord["amount"] - $therecord["paid"] - $therecord["applied"] - $therecord["discount"] - $therecord["taxadjustment"];
				else
					$docDue = 0;


				?>

				<tr id="<?php echo $recID?>" class="receiptTR">
					<td>
						<input type="hidden" id="<?php echo $recID?>ARID" value="<?php echo $therecord["aritemid"]?>" />
						<input type="hidden" id="<?php echo $recID?>RecID" value="<?php echo $therecord["relatedid"]?>" />
						<input id="<?php echo $recID?>DocRef" class="invisibleTextField" readonly="readonly" value="<?php echo $therecord["invoiceid"]?>" size="4"/>
					</td>
					<td><input id="<?php echo $recID?>Type" class="invisibleTextField" readonly="readonly" value="<?php echo $therecord["type"]?>" size="12" /></td>
					<td><input id="<?php echo $recID?>DocDate" class="invisibleTextField" readonly="readonly" value="<?php echo formatFromSQLDate($therecord["itemdate"])?>" size="9" /></td>
					<td><input id="<?php echo $recID?>DueDate" class="invisibleTextField" readonly="readonly" value="<?php echo $dueDate?>" size="9" /></td>
					<td align="right">
						<input type="hidden" id="<?php echo $recID?>DocPaid" value="<?php echo $therecord["paid"]?>" />
						<input id="<?php echo $recID?>DocAmount" class="invisibleTextField currency" readonly="readonly" value="<?php echo formatVariable($therecord["amount"], "currency")?>" size="10" maxlength="12"/>
					</td>
					<td><input id="<?php echo $recID?>DocDue" class="invisibleTextField currency dueFields" readonly="readonly" value="<?php echo formatVariable($docDue, "currency")?>" size="10" maxlength="12"/></td>
					<td><input id="<?php echo $recID?>Applied" class="currency appliedFields" value="<?php echo formatVariable($therecord["applied"], "currency")?>" size="10" maxlength="12"/></td>
					<td><input id="<?php echo $recID?>Discount" class="currency" value="<?php echo formatVariable($therecord["discount"], "currency")?>" size="10" maxlength="12"/></td>
					<td><input id="<?php echo $recID?>TaxAdj" class="currency" value="<?php echo formatVariable($therecord["taxadjustment"], "currency")?>" size="10" maxlength="12"/></td>
					<td><button type="button" id="<?php echo $recID?>RemoveARItemButton" class="graphicButtons buttonMinus" title="remove item"><span>-</span></button></td>
				</tr>

				<?php

				$count++;
			}//end foreach

		}//end method


		function set($itemlist, $receiptid, $clientid, $userid){

			//remove any existing items
			$deletestatement = "DELETE FROM receiptitems WHERE receiptid = '".mysql_real_escape_string($receiptid)."'";
			$this->db->query($deletestatement);

			//remove any ar credits created by ths receipt
			$deletestatement = "DELETE FROM aritems WHERE relatedid = '".mysql_real_escape_string($receiptid)."' AND `type` = 'credit'";
			$this->db->query($deletestatement);

			foreach($itemlist as $itemRecord){

				//if no ar uuid, or the credit is from this record, we need to create the ar item
				if(!$itemRecord["aritemid"] || ($itemRecord["relatedid"] == $receiptid && $itemRecord["type"] == "credit") ){

					$arrecord = array();
					$arrecord["type"] = "credit";
					$arrecord["status"] = "open";
					$arrecord["posted"] = 0;
					$arrecord["amount"] = -1 * currencyToNumber($itemRecord["applied"]);
					$arrecord["itemdate"] = $itemRecord["itemdate"];
					$arrecord["clientid"] = $clientid;
					$arrecord["relatedid"] = $receiptid;

					if(!isset($aritems))
						$aritems = new phpbmsTable($this->db, "tbld:c595dbe7-6c77-1e02-5e81-c2e215736e9c");

					if(!isset($arrecord["uuid"]))
						$arrecord["uuid"] = uuid($aritems->prefix.":");

					$aritems->insertRecord($arrecord, $userid);

					$itemRecord["aritemid"] = $arrecord["uuid"];

				}//end if

				$insertstatement ="
				INSERT INTO
					receiptitems
					(aritemid, receiptid, applied, discount, taxadjustment)
				VALUES (
					'".mysql_real_escape_string($itemRecord["aritemid"])."',
					'".mysql_real_escape_string($receiptid)."',
					".currencyToNumber($itemRecord["applied"]).",
					".currencyToNumber($itemRecord["discount"]).",
					".currencyToNumber($itemRecord["taxadjustment"])."
				)";

				$this->db->query($insertstatement);

			}//endforeach

		}//end method

	}// end class


	// RECEIPTS CLASS
	//======================================================================
	class receipts extends phpbmsTable{

		var $_availableClientUUIDs = NULL;
		var $_availablePaymentMethodUUIDs = NULL;
		var $receiptitems = NULL;

		function receipts($db, $tabldefid, $backurl = NULL){

			if(ENCRYPT_PAYMENT_FIELDS){
				$this->encryptedFields[] = "ccnumber";
				$this->encryptedFields[] = "ccverification";
				$this->encryptedFields[] = "routingnumber";
				$this->encryptedFields[] = "accountnumber";
				$this->encryptedFields[] = "ccexpiration";
			}//end if

			parent::phpbmsTable($db, $tabldefid, $backurl);

		}//end method --receipts--

		function showPaymentOptions($selectedid){

			global $phpbms;

			$selectedid = mysql_real_escape_string($selectedid);

			$querystatement = "
				SELECT
					id,
					uuid,
					name,
					type,
					onlineprocess,
					processscript
				FROM
					paymentmethods
				WHERE
					(inactive = 0
					OR uuid='".$selectedid."')
					AND type != 'receivable'
				ORDER BY
					priority,
					name";

			$queryresult = $this->db->query($querystatement);

			?>
			<label for="paymentmethodid">payment type</label><br />
			<select id="paymentmethodid" name="paymentmethodid">
				<?php
					$phpbms->bottomJS[] = 'paymentTypes = Array()';
					$phpbms->bottomJS[] = 'paymentTypes["s-1"] = Array();';
					$phpbms->bottomJS[] = 'paymentTypes["s-1"]["type"] = "other";';
					$phpbms->bottomJS[] = 'paymentTypes["s-1"]["onlineprocess"] = 0';
					$phpbms->bottomJS[] = 'paymentTypes["s-1"]["onlineprocess"] = null';

					while($therecord = $this->db->fetchArray($queryresult)){

						$phpbms->bottomJS[] = 'paymentTypes["s'.$therecord["uuid"].'"] = Array();';
						$phpbms->bottomJS[] = 'paymentTypes["s'.$therecord["uuid"].'"]["type"] = "'.$therecord["type"].'";';
						$phpbms->bottomJS[] = 'paymentTypes["s'.$therecord["uuid"].'"]["onlineprocess"] = "'.$therecord["onlineprocess"].'";';
						$phpbms->bottomJS[] = 'paymentTypes["s'.$therecord["uuid"].'"]["processscript"] = "'.$therecord["processscript"].'";';

						?><option value="<?php echo $therecord["uuid"]?>" <?php if($therecord["uuid"] == $selectedid) echo 'selected="selected"'?>><?php echo $therecord["name"]?></option><?php
						echo "\n";

					}//endwhile

				?>
				<option value="-1" <?php if($selectedid == -1 || $selectedid == "-1") echo 'selected="selected"'?>>Other...</option>
			</select><?php


		}//end method

		// CLASS OVERRIDES ======================================================================================

		function getDefaults(){
			$therecord = parent::getDefaults();

			$therecord["clientid"] = "";
			$therecord["status"] = "open";
			$therecord["receiptdate"] = dateToString(mktime(),"SQL");

			$therecord["itemslist"] = array();

			return $therecord;
		}


		/**
		 *
		 * function getRecord
		 * retrieves sales order record
		 *
		 * @param int|string $id the id or uuid of the sales order record
		 * @param bool $useUuid is the passed $id is referenceing an id (false/default) or a uuid (true)
		 *
		 * @return array associateive array with the record information
		 */
		function getRecord($id, $useUuid = false){

			$therecord = parent::getRecord($id, $useUuid);

			if((int)$therecord["posted"] != 0 && ENCRYPT_PAYMENT_FIELDS){

				$querystatement = "
					SELECT
						`ccnumber`,
						`ccverification`,
						`ccexpiration`,
						`accountnumber`,
						`routingnumber`
					FROM
						`receipts`
					WHERE
						`uuid` = '".$therecord["uuid"]."'
				";

				$queryresult = $this->db->query($querystatement);

				$unEncryptedRecord = $this->db->fetchArray($queryresult);

				$therecord["ccnumber"] = $unEncryptedRecord["ccnumber"];
				$therecord["ccverification"] = $unEncryptedRecord["ccverification"];
				$therecord["ccexpiration"] = $unEncryptedRecord["ccexpiration"];
				$therecord["accountnumber"] = $unEncryptedRecord["accountnumber"];
				$therecord["routingnumber"] = $unEncryptedRecord["routingnumber"];

			}//end if

			if(ENCRYPT_PAYMENT_FIELDS && (int)$therecord["posted"] == 0){

				if($therecord["ccverification"])
					$therecord["ccverification"] = str_repeat("*",strlen($therecord["ccverification"]));
				if($therecord["ccnumber"])
					$therecord["ccnumber"] = str_repeat("*",strlen($therecord["ccnumber"] -4 )).substr($therecord["ccnumber"], -4);
				if($therecord["routingnumber"])
					$therecord["routingnumber"] = str_repeat("*",strlen($therecord["routingnumber"] -4 )).substr($therecord["routingnumber"], -4);
				if($therecord["accountnumber"])
					$therecord["accountnumber"] = str_repeat("*",strlen($therecord["accountnumber"] -4 )).substr($therecord["accountnumber"], -4);

			}//end if

			/**
			  *  Now, need to get receiptitems.
			  */

			if($this->receiptitems === NULL)
				$this->receiptitems = new receiptitems($this->db);

			$therecord["itemslist"] = $this->receiptitems->get($therecord["uuid"]);

			$therecord["itemschanged"] = 1;

			return $therecord;

		}//end method


		function verifyVariables($variables){

			//default not sufficient
			if(isset($variables["clientid"])){

				if($this->_availableClientUUIDs === NULL)
					$this->_availableClientUUIDs = $this->_loadUUIDList("clients");

				if(!in_array(((string)$variables["clientid"]),$this->_availableClientUUIDs))
					$this->verifyErrors[] = "The `clientid` field does not give an existing/acceptable client uuid.";

			}else
				$this->verifyErrors[] = "The `clientid` field must be set.";

			//because enum, default not sufficient
			if(isset($variables["status"])){
				switch($variables["status"]){

					case "open":
						if(isset($variables["readytopost"]))
							if($variables["readytopost"])
								$this->verifyErrors[] = "If the `status` is 'open', the `readytopost` field must be not be '1'";
						break;

					case "collected":
						break;

					default:
						$this->verifyErrors[] = "The value of the `status` field is invalid.  It must
							be either 'open' or 'collected'.";
					break;

				}//end switch
			}else
				$this->verifyErrors[] = "The `status` field must be set.";

			// Default is not sufficient
			if(isset($variables["paymentmethodid"])){

				if($this->_availablePaymentMethodUUIDs === NULL){
					$this->_availablePaymentMethodUUIDs = $this->_loadUUIDList("paymentmethods");
					$this->_availablePaymentMethodUUIDs[] = -1;
				}//end if

				if(!in_array(((string)$variables["paymentmethodid"]),$this->_availablePaymentMethodUUIDs))
					$this->verifyErrors[] = "The `paymentmethod` field does not give an existing/acceptable payment method uuid.";

			}else
				$this->verifyErrors[] = "The `paymentmethodid` field must be set.";

			//check booleans
			if(isset($variables["readytopost"]))
				if($variables["readytopost"] && $variables["readytopost"] != 1)
					$this->verifyErrors[] = "The `readytopost` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["posted"]))
				if($variables["posted"] && $variables["posted"] != 1)
					$this->verifyErrors[] = "The `posted` field must be a boolean (equivalent to 0 or exactly 1).";

			return parent::verifyVariables($variables);

		}//end method --verifyVariables--


		function prepareVariables($variables){

			$variables["amount"] = currencyToNumber($variables["amount"]);

			if($variables["ccnumber_old"] == $variables["ccnumber"])
				unset($variables["ccnumber"]);

			if($variables["ccexpiration_old"] == $variables["ccexpiration"])
				unset($variables["ccexpiration"]);

			if($variables["ccverification_old"] == $variables["ccverification"])
				unset($variables["ccverification"]);

			if($variables["accountnumber_old"] == $variables["accountnumber"])
				unset($variables["accountnumber"]);

			if($variables["routingnumber_old"] == $variables["routingnumber"])
				unset($variables["routingnumber"]);

			if($variables["itemschanged"]){

				$variables["itemslist"] = stripslashes($variables["itemslist"]);
				$variables["itemslist"] = json_decode($variables["itemslist"], true);

			}//end if

			return $variables;
		}


		function updateRecord($variables, $modifiedby = NULL, $useUuid = false){

			if(parent::updateRecord($variables, $modifiedby, $useUuid)){

				if(ENCRYPT_PAYMENT_FIELDS && (isset($variables["ccnumber"]) || isset($variables["ccexpiration"]) || isset($variables["ccverification"]) || isset($variables["accountnumber"]) || isset($variables["routingnumber"])) ){

					if($useUuid)
						$whereclause = "`uuid` = '".mysql_real_escape_string($variables["uuid"])."'";
					else
						$whereclause = "`id` = '".(int)$variables["id"]."'";

					$querystatement = "
					UPDATE
						`receipts`
					SET ";

					$fieldlist = "";
					if(isset($variables["ccnumber"])){
						$variables["ccnumber"] = mysql_real_escape_string($variables["ccnumber"]);
						$fieldlist .= ", `ccnumber` = ".$this->db->encrypt("'".$variables["ccnumber"]."'");
					}//end if
					if(isset($variables["ccexpiration"])){
						$variables["ccexpiration"] = mysql_real_escape_string($variables["ccexpiration"]);
						$fieldlist .= ", `ccexpiration` = ".$this->db->encrypt("'".$variables["ccexpiration"]."'");
					}//end if
					if(isset($variables["ccverification"])){
						$variables["ccverification"] = mysql_real_escape_string($variables["ccverification"]);
						$fieldlist .= ", `ccverification` = ".$this->db->encrypt("'".$variables["ccverification"]."'");
					}//end if
					if(isset($variables["accountnumber"])){
						$variables["accountnumber"] = mysql_real_escape_string($variables["accountnumber"]);
						$fieldlist .= ", `accountnumber` = ".$this->db->encrypt("'".$variables["accountnumber"]."'");
					}//end if
					if(isset($variables["routingnumber"])){
						$variables["routingnumber"] = mysql_real_escape_string($variables["routingnumber"]);
						$fieldlist .= ", `routingnumber` = ".$this->db->encrypt("'".$variables["routingnumber"]."'");
					}//end if

					$fieldlist = substr($fieldlist, 1);

					$querystatement .= $fieldlist." WHERE `posted` = '0' AND ".$whereclause;

					$this->db->query($querystatement);

				}//end if

				if($variables["itemschanged"]==1){

					if($this->receiptitems === NULL)
						$this->receiptitems = new receiptitems($this->db);

					$this->receiptitems->set($variables["itemslist"], $variables["uuid"], $variables["clientid"], $modifiedby);

				}//end if

			}//end if

		}//end method



		function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false, $useUuid = false){

			$newid = parent::insertRecord($variables, $createdby, $overrideID, $replace, $useUuid);

			if(ENCRYPT_PAYMENT_FIELDS && (isset($variables["ccnumber"]) || isset($variables["ccexpiration"]) || isset($variables["ccverification"]) || isset($variables["accountnumber"]) || isset($variables["routingnumber"])) ){

				if($useUuid){
					$whereclause = "`uuid` = '".$newid["uuid"]."'";
					$variables["uuid"] = $newid["uuid"];
				}else
					$whereclause = "`id` = '".$newid."'";

				$querystatement = "
					UPDATE
						`receipts`
					SET ";

					$fieldlist = "";
					if(isset($variables["ccnumber"])){
						$variables["ccnumber"] = mysql_real_escape_string($variables["ccnumber"]);
						$fieldlist .= ", `ccnumber` = ".$this->db->encrypt("'".$variables["ccnumber"]."'");
					}//end if
					if(isset($variables["ccexpiration"])){
						$variables["ccexpiration"] = mysql_real_escape_string($variables["ccexpiration"]);
						$fieldlist .= ", `ccexpiration` = ".$this->db->encrypt("'".$variables["ccexpiration"]."'");
					}//end if
					if(isset($variables["ccverification"])){
						$variables["ccverification"] = mysql_real_escape_string($variables["ccverification"]);
						$fieldlist .= ", `ccverification` = ".$this->db->encrypt("'".$variables["ccverification"]."'");
					}//end if
					if(isset($variables["accountnumber"])){
						$variables["accountnumber"] = mysql_real_escape_string($variables["accountnumber"]);
						$fieldlist .= ", `accountnumber` = ".$this->db->encrypt("'".$variables["accountnumber"]."'");
					}//end if
					if(isset($variables["routingnumber"])){
						$variables["routingnumber"] = mysql_real_escape_string($variables["routingnumber"]);
						$fieldlist .= ", `routingnumber` = ".$this->db->encrypt("'".$variables["routingnumber"]."'");
					}//end if

					$fieldlist = substr($fieldlist, 1);

					$querystatement .= $fieldlist." WHERE `posted` = '0' AND ".$whereclause;

				$this->db->query($querystatement);

			}//end if

			if($variables["itemschanged"]==1){

				if($this->receiptitems === NULL)
					$this->receiptitems = new receiptitems($this->db);

				$this->receiptitems->set($variables["itemslist"], $variables["uuid"], $variables["clientid"], $createdby);

			}//end if

			return $newid;
		}
	}//end class
}// end if


if(class_exists("searchFunctions")){
	class receiptsSearchFunctions extends searchFunctions{

		function post($useUuid = false){

			if(!$useUuid)
				$whereclause = $this->buildWhereClause();
			else
				$whereclause = $this->buildWhereClause($thies->maintable.".`uuid`");

			include_once("include/post_class.php");
			defineReceiptsPost();

			$receiptsPost = new receiptsPost($this->db);

			$count = $receiptsPost->post($whereclause);

			$message = $this->buildStatusMessage($count);
			$message .= " posted.";

			return $message;
		}//end method


		function delete_record($useUUID = false){

			if(!$useUUID)
				$whereclause=$this->buildWhereClause();
			else
				$whereclause = $this->buildWhereClause($this->maintable.".uuid");

			$querystatement ="
				SELECT
					id,
					`uuid`
				FROM
					receipts
				WHERE
					posted=0
					AND `status` != 'collected'
					AND (".$whereclause.")";

			$queryresult = $this->db->query($querystatement);

			$count = $this->db->numRows($queryresult);

			$newWhere = "";
			while($therecord = $this->db->fetchArray($queryresult))
				$newWhere .= " OR `uuid` = '".$therecord["uuid"]."'";

			if(strlen($newWhere))
				$newWhere = substr($newWhere, 4);
			else
				$newWhere = "id = -1974";

			$deletestatement = "
				DELETE FROM
					aritems
				WHERE
					`type` = 'credit'
					AND (".str_replace("uuid", "relatedid", $newWhere).")";

			$this->db->query($deletestatement);

			$deletestatement = "
				DELETE FROM
					receiptitems
				WHERE
					".str_replace("uuid", "receiptid", $newWhere);

			$this->db->query($deletestatement);

			$deletestatement = "
				DELETE FROM
					receipts
				WHERE
					".$newWhere;

			$this->db->query($deletestatement);

			$message = $this->buildStatusMessage($count);
			$message.=" deleted.";

			return $message;
		}//end method


		function _getFullyDistributed($useUuid = false){

			if(!$useUuid)
				$whereclause = $this->buildWhereClause();
			else
				$whereclause = $this->buildWhereClause($thies->maintable.".`uuid`");

			$querystatement = "
				SELECT
					id,
					`uuid`,
					amount
				FROM
					receipts
				WHERE
					posted = '0'
					AND (".$whereclause.")";

			$queryresult = $this->db->query($querystatement);

			$newWhere = "";
			$count = 0;
			while($therecord = $this->db->fetchArray($queryresult)){

				$querystatement = "
					SELECT
						SUM(applied) AS thesum
					FROM
						receiptitems
					WHERE
						receiptid = '".$therecord["uuid"]."'
				";

				$checkresult = $this->db->query($querystatement);

				$checkrecord = $this->db->fetchArray($checkresult);

				if($therecord["amount"] == $checkrecord["thesum"]){
					$newWhere .= " OR id = ".$therecord["id"];
					$count++;

				}//endif

			}//endif

			if(strlen($newWhere))
				$newWhere = substr($newWhere, 4);
			else
				$newWhere = "id = -1974";

			return array("clause" => $newWhere, "count" => $count);

		}//end method


		function mark_rtp($useUuid = false){

			$where = $this->_getFullyDistributed($useUuid);

			$updatestatement="
				UPDATE
					receipts
				SET
					`status` = 'collected',
					readytopost = 1,
					modifiedby = ".$_SESSION["userinfo"]["id"].",
					modifieddate = NOW()
				WHERE
					readytopost='0'
					AND
					".$where["clause"];

			$this->db->query($updatestatement);

			$message = $this->buildStatusMessage($where["count"]);
			$message.=" marked ready to post.";

			return $message;
		}//end method


		function mark_collected($useUuid = false){

			$where = $this->_getFullyDistributed($useUuid);

			$updatestatement="
				UPDATE
					receipts
				SET
					`status` = 'collected',
					modifiedby = ".$_SESSION["userinfo"]["id"].",
					modifieddate = NOW()
				WHERE
					".$where["clause"];

			$this->db->query($updatestatement);

			$message = $this->buildStatusMessage($where["count"]);
			$message.=" marked collected.";

			return $message;
		}//end method

	}//end class
}//end if


function defineReceiptsPost(){

	class receiptsPost extends tablePost{

		var $maintable = "receipts";
		var $datefieldname = "receiptdate";
		var $notpostedCriteria = "receipts.posted = 0";

		function prepareWhere($whereclause=NULL){

			$this->whereclause = "";

			if($whereclause)
				$this->whereclause = "(".$whereclause.") AND ";

			$this->whereclause .= "receipts.posted = '0' AND receipts.readytopost = '1'";

		}//end method


		function post($whereclause = NULL, $postsessionid = 0){

			if($whereclause)
				$this->prepareWhere($whereclause);

			if(!$postsessionid)
				$postsessionid = $this->generatePostingSession("search");

			$querystatement = "
				SELECT
					uuid
				FROM
					receipts
				WHERE
					".$this->whereclause;

			$queryresult = $this->db->query($querystatement);

			$count = $this->db->numRows($queryresult);
			$newWhere = "";
			while($therecord = $this->db->fetchArray($queryresult)){

				$querystatement = "
					SELECT
						receiptitems.aritemid,
						receiptitems.applied,
						receiptitems.discount,
						receiptitems.taxadjustment,
						aritems.amount,
						aritems.paid,
						aritems.status,
						aritems.relatedid,
						`aritems`.`type`
					FROM
						`receiptitems` INNER JOIN `aritems` ON `receiptitems`.`aritemid` = `aritems`.`uuid`
					WHERE
						receiptitems.receiptid = '".$therecord["uuid"]."'
				";

				$itemsresult = $this->db->query($querystatement);

				while($itemrecord = $this->db->fetchArray($itemsresult)){

					if($itemrecord["relatedid"] == $therecord["uuid"] && $itemrecord["type"] == "credit")
						$paid = $itemrecord["paid"];
					else
						$paid = $itemrecord["paid"] + $itemrecord["applied"] + $itemrecord["discount"] + $itemrecord["taxadjustment"];

					if($paid == $itemrecord["amount"])
						$status = "closed";
					else
						$status = "open";

					$updatestatement ="
						UPDATE
							aritems
						SET
							posted = '1',
							paid = ".$paid.",
							`status` = '".$status."',
							modifiedby = ".$_SESSION["userinfo"]["id"].",
							modifieddate = NOW()
						WHERE
							uuid = '".$itemrecord["aritemid"]."'
					";

					$this->db->query($updatestatement);

					if($itemrecord["type"] == "invoice"){

						$updatestatement = "
							UPDATE
								invoices
							SET
								amountpaid = ".$paid.",
								modifiedby = ".$_SESSION["userinfo"]["id"].",
								modifieddate = NOW()
							WHERE
								uuid = '".$itemrecord["relatedid"]."'
						";

							$this->db->query($updatestatement);

					}//endif

				}//endwhile

				$newWhere .= " OR uuid = '".$therecord["uuid"]."'";
			}//endwhile

			if(strlen($newWhere))
				$newWhere = substr($newWhere, 4);

			if($newWhere){

				$updatestatement = "
					UPDATE
						receipts
					SET
						posted = '1',
						postingsessionid = '".$postsessionid."',";

				if(ENCRYPT_PAYMENT_FIELDS){
					$querystatement .="
						ccverification = REPEAT('*',LENGTH(".$this->db->decrypt('`ccverification`').")),
						ccexpiration =  REPEAT('*',LENGTH(".$this->db->decrypt('`ccexpiration`').")),
						routingnumber =  NULL,
						accountnumber =  NULL,
						ccnumber = LPAD(SUBSTRING(".$this->db->decrypt('`ccnumber`').",-4),LENGTH(".$this->db->decrypt('`ccnumber`')."),'*'),
					";
				}//endif

				$updatestatement .="
						modifiedby = ".$_SESSION["userinfo"]["id"].",
						modifieddate = NOW()
					WHERE
						".$newWhere;

				$this->db->query($updatestatement);


			}//endif


			$this->updatePostingSession($postsessionid, $count);

			return $count;

		}//end method

	}//end class receiptsPost

}//end function

if(class_exists("tablePost")){
	defineReceiptsPost($db);
}
?>
