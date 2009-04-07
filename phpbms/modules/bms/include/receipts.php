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

	class receiptitems{

		function receiptitems($db){

			$this->db = $db;

		}//end method

		function get($receiptid){

			$querystatement = "
				SELECT
					receiptitems.aritemid,
					receiptitems.applied,
					receiptitems.discount,
					receiptitems.taxadjustment,
					aritems.type,
					aritems.relatedid,
					aritems.itemdate,
					aritems.amount,
					aritems.paid
				FROM
					receiptitems INNER JOIN aritems ON receiptitems.aritemid = aritems.id
				WHERE
					receiptitems.receiptid = ".((int) $receiptid)."
				ORDER BY
					aritems.type,
					aritems.itemdate";

				return $this->db->query($querystatement);

		}//end function


		function show($queryresult, $receiptPosted, $receiptid){

			$count = 1;

			while($therecord = $this->db->fetchArray($queryresult)){

				$recID = "i".$count;

				if($therecord["type"] == "invoice"){

					$tempDate = stringToDate($therecord["itemdate"], "SQL");
					$dueDate = dateToString( strtotime(TERM1_DAYS." days", $tempDate) );

				} else
					$dueDate = "&nbsp;";

				if($therecord["type"] == "deposit" && $therecord["relatedid"] == $receiptid){
					$therecord["relatedid"] = "";
					$therecord["amount"] = 0;
					$therecord["aritemid"] = 0;
				}

				if($receiptPosted)
					$docDue = $therecord["amount"] - $therecord["paid"];
				elseif($therecord["relatedid"])
					$docDue = $therecord["amount"] - $therecord["paid"] - $therecord["applied"] - $therecord["discount"] - $therecord["taxadjustment"];
				else
					$docDue = 0;


				?>

				<tr id="<?php echo $recID?>">
					<td>
						<input type="hidden" id="<?php echo $recID?>ARID" value="<?php echo $therecord["aritemid"]?>" />
						<input id="<?php echo $recID?>DocRef" class="invisibleTextField" readonly="readonly" value="<?php echo $therecord["relatedid"]?>" size="4" />
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
			}//endwhile


		}//end method


		function set($itemlist, $receiptid, $clientid, $userid){

			//remove any exisiting items
			$deletestatement = "DELETE FROM receiptitems WHERE receiptid =".((int) $receiptid);
			$this->db->query($deletestatement);

			//remove any ar deposits created by ths receipt
			$deletestatement = "DELETE FROM aritems WHERE relatedid = ".((int) $receiptid)." AND `type` = 'deposit'";
			$this->db->query($deletestatement);

			$itemsArray = explode(";;", $itemlist);

			foreach($itemsArray as $item){

				$itemRecord = explode("::", $item);

				if(count($itemRecord) > 1){

					//if no ar id, or the deposit is from this record, we need to create the ar item
					if(!$itemRecord[0] || ($itemRecord[1] == $receiptid && $itemRecord[2] == "deposit") ){

						$arrecord = array();
						$arrecord["type"] = "deposit";
						$arrecord["status"] = "open";
						$arrecord["posted"] = 0;
						$arrecord["amount"] = -1 * currencyToNumber($itemRecord[8]);
						$arrecord["itemdate"] = $itemRecord[3];
						$arrecord["clientid"] = $clientid;
						$arrecord["relatedid"] = $receiptid;


						if(!isset($aritems))
							$aritems = new phpbmsTable($this->db, 303);

						$aritems->insertRecord($arrecord, $userid);

						$itemRecord[0] = $this->db->insertId();

					}//end if

					$insertstatement ="
					INSERT INTO
						receiptitems
						(aritemid, receiptid, applied, discount, taxadjustment)
					VALUES (
						".((int) $itemRecord[0]).",
						".((int) $receiptid).",
						".currencyToNumber($itemRecord[8]).",
						".currencyToNumber($itemRecord[9]).",
						".currencyToNumber($itemRecord[10])."
					)";

					$this->db->query($insertstatement);

				}//endif

			}//endforeach

		}//end method

	}// end class


	// RECEIPTS CLASS
	//======================================================================
	class receipts extends phpbmsTable{

		var $availableClientIDs = array();
		var $availablePaymentMethodIDs = array();

		function showPaymentOptions($selectedid){

			global $phpbms;

			$querystatement = "
				SELECT
					id,
					name,
					type,
					onlineprocess,
					processscript
				FROM
					paymentmethods
				WHERE
					(inactive = 0
					OR id=".((int) $selectedid).")
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

						$phpbms->bottomJS[] = 'paymentTypes["s'.$therecord["id"].'"] = Array();';
						$phpbms->bottomJS[] = 'paymentTypes["s'.$therecord["id"].'"]["type"] = "'.$therecord["type"].'";';
						$phpbms->bottomJS[] = 'paymentTypes["s'.$therecord["id"].'"]["onlineprocess"] = "'.$therecord["onlineprocess"].'";';
						$phpbms->bottomJS[] = 'paymentTypes["s'.$therecord["id"].'"]["processscript"] = "'.$therecord["processscript"].'";';

						?><option value="<?php echo $therecord["id"]?>" <?php if($therecord["id"] == $selectedid) echo 'selected="selected"'?>><?php echo $therecord["name"]?></option><?php
						echo "\n";

					}//endwhile

				?>
				<option value="-1" <?php if($selectedid == -1) echo 'selected="selected"'?>>Other...</option>
			</select><?php


		}//end method

		// CLASS OVERRIDES ======================================================================================

		function getDefaults(){
			$therecord = parent::getDefaults();

			$therecord["clientid"] = "";
			$therecord["status"] = "open";
			$therecord["receiptdate"] = dateToString(mktime(),"SQL");

			return $therecord;
		}


		function populateClientArray(){

			$this->availableClientIDs = array();

			$querystatement = "
				SELECT
					`id`
				FROM
					`clients`;
				";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){
				while($therecord = $this->db->fetchArray($queryresult))
					$this->availableClientIDs[] = $therecord["id"];
			}else
				$this->availableClientIDs[] = "none";

		}//end method --populateClientArray--


		function populatePaymentArray(){

			$this->availablePaymentMethodIDs = array();

			$querystatement = "
				SELECT
					`id`
				FROM
					`paymentmethods`;
				";

			$queryresult = $this->db->query($querystatement);

			//for the "other" choice on receipts
			$this->availablePaymentMethodIDs[] = -1;

			while($therecord = $this->db->fetchArray($queryresult))
				$this->availablePaymentMethodIDs[] = $therecord["id"];

		}//end method --populatePaymentArray--


		function verifyVariables($variables){

			//default not sufficient
			if(isset($variables["clientid"])){

				if(((int) $variables["clientid"]) > 0){

					if(!count($this->availableClientIDs))
						$this->populateClientArray();

					if(!in_array(((int)$variables["clientid"]),$this->availableClientIDs))
						$this->verifyErrors[] = "The `clientid` field does not give an existing/acceptable client id number.";
				}else
					$this->verifyErrors[] = "The `clientid` field must be a positive number.";

			}else
				$this->verifyErrors[] = "The `clientid` field must be set.";

			//because enum, default not sufficient
			if(isset($variables["status"])){
				switch($variables["status"]){

					case "open":
					case "collected":
						break;

					default:
						$this->verifyErrors[] = "The value of the `status` field is invalid.  It must
							be either 'open' or 'closed'.";
					break;

				}//end switch
			}else
				$this->verifyErrors[] = "The `status` field must be set.";

			// Default is not sufficient
			if(isset($variables["paymentmethodid"])){

				if(is_numeric($variables["paymentmethodid"])){

					if(!count($this->availablePaymentMethodIDs))
						$this->populatePaymentArray();

					if(!in_array(((int)$variables["paymentmethodid"]),$this->availablePaymentMethodIDs))
						$this->verifyErrors[] = "The `paymentmethod` field does not give an existing/accpetable payment method id number.";
				}else
					$this->verifyErrors[] = "The `paymentmethodid` field must be numeric.";

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

			return $variables;
		}


		function updateRecord($variables, $modifiedby = NULL){

			if(parent::updateRecord($variables, $modifiedby)){

				if($variables["itemschanged"]==1){

					$items = new receiptitems($this->db);

					$items->set($variables["itemslist"], $variables["id"], $variables["clientid"], $modifiedby);

				}//end if

			}//end if

		}//end method



		function insertRecord($variables, $createdby = NULL){

			$newid = parent::insertRecord($variables, $createdby);

			if($variables["itemschanged"]==1){

				$items = new receiptitems($this->db);

				$items->set($variables["itemslist"], $newid, $variables["clientid"], $createdby);

			}//end if

			return $newid;
		}
	}//end class
}// end if


if(class_exists("searchFunctions")){
	class receiptsSearchFunctions extends searchFunctions{

		function post(){

			$whereclause = $this->buildWhereClause();

			include_once("include/post_class.php");
			defineReceiptPost();

			$receiptPost = new receiptPost($this->db);

			$count = $receiptPost->post($whereclause);

			$message = $this->buildStatusMessage($count);
			$message .= " posted.";

			return $message;
		}//end method


		function delete_record(){

			//passed variable is array of user ids to be revoked
			$whereclause = $this->buildWhereClause();

			$querystatement ="
				SELECT
					id
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
				$newWhere .= " OR id = ".$therecord["id"];

			if(strlen($newWhere))
				$newWhere = substr($newWhere, 4);
			else
				$newWhere = "id = -1974";

			$deletestatement = "
				DELETE FROM
					aritems
				WHERE
					`type` = 'deposit'
					AND (".str_replace("id =", "relatedid =", $newWhere).")";

			$this->db->query($deletestatement);

			$deletestatement = "
				DELETE FROM
					receiptitems
				WHERE
					".str_replace("id =", "receiptid =", $newWhere);

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


		function _getFullyDistributed(){

			$whereclause = $this->buildWhereClause();

			$querystatement = "
				SELECT
					id,
					amount
				FROM
					receipts
				WHERE
					posted = 0
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
						receiptid = ".$therecord["id"];

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


		function mark_rtp(){

			$where = $this->_getFullyDistributed();

			$updatestatement="
				UPDATE
					receipts
				SET
					`status` = 'collected',
					readytopost = 1,
					modifiedby = ".$_SESSION["userinfo"]["id"].",
					modifieddate = NOW()
				WHERE
					".$where["clause"];

			$this->db->query($updatestatement);

			$message = $this->buildStatusMessage($where["count"]);
			$message.=" marked ready to post.";

			return $message;
		}//end method


		function mark_collected(){

			$where = $this->_getFullyDistributed();

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


function defineReceiptPost(){

	class receiptPost extends tablePost{

		function receiptPost($db, $modifiedby = NULL){

			parent::tablePost($db, $modifiedby);

		}//end method


		function prepareWhere($whereclause=NULL){

			$this->whereclause = "";

			if($whereclause)
				$this->whereclause = "(".$whereclause.") AND ";

			$this->whereclause .= "receipts.posted = 0 AND receipts.readytopost = 1";

		}//end method


		function post($whereclause=NULL){

			if($whereclause)
				$this->prepareWhere($whereclause);


			$querystatement = "
				SELECT
					id
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
						aritems.type
					FROM
						receiptitems INNER JOIN aritems ON receiptitems.aritemid = aritems.id
					WHERE
						receiptitems.receiptid = ".$therecord["id"];

				$itemsresult = $this->db->query($querystatement);

				while($itemrecord = $this->db->fetchArray($itemsresult)){

					if($itemrecord["relatedid"] == $therecord["id"] && $itemrecord["type"] == "deposit")
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
							posted = 1,
							paid = ".$paid.",
							`status` = '".$status."',
							modifiedby = ".$_SESSION["userinfo"]["id"].",
							modifieddate = NOW()
						WHERE
							id = ".$itemrecord["aritemid"];

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
								id =".$itemrecord["relatedid"];

							$this->db->query($updatestatement);

					}//endif

				}//endwhile

				$newWhere .= " OR id = ".$therecord["id"];
			}//endwhile

			if(strlen($newWhere))
				$newWhere = substr($newWhere, 4);

			if($newWhere){

				$updatestatement = "
					UPDATE
						receipts
					SET
						posted = 1,";

				if(CLEAR_PAYMENT_ON_INVOICE){
					$updatestatement .="
						ccverification = REPEAT('*',LENGTH(ccverification)),
						ccexpiration =  REPEAT('*',LENGTH(ccexpiration)),
						routingnumber =  NULL,
						accountnumber =  NULL,
						ccnumber = LPAD(SUBSTRING(ccnumber,-4),LENGTH(ccnumber),'*'),
					";
				}//endif

				$updatestatement .="
						modifiedby = ".$_SESSION["userinfo"]["id"].",
						modifieddate = NOW()
					WHERE
						".$newWhere;

				$this->db->query($updatestatement);


			}//endif

			return $count;

		}//end method

	}//end class invoicePost

}//end function

if(class_exists("tablePost")){
	defineReceiptPost($db);
}
?>