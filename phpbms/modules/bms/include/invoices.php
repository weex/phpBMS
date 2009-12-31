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
	class invoices extends phpbmsTable{

		var $_availableClientUUIDs = NULL;
		var $_availableUserUUIDs = NULL;
		var $_availableStatusUUIDs = NULL;
		var $lineitems = NULL;

		function invoices($db, $tabledefid, $backUrl = NULL){

			if(ENCRYPT_PAYMENT_FIELDS){
				$this->encryptedFields[] = "ccnumber";
				$this->encryptedFields[] = "ccverification";
				$this->encryptedFields[] = "routingnumber";
				$this->encryptedFields[] = "accountnumber";
				$this->encryptedFields[] = "ccexpiration";
			}//end if

			parent::phpbmsTable($db,$tabledefid,$backUrl);

		}//end method

		function showClientType($uuid){

			if($uuid){

				$querystatement="
					SELECT
						type
					FROM
						clients
					WHERE
						`uuid`='".mysql_real_escape_string($uuid)."'
				";

				$therecord = $this->db->fetchArray($this->db->query($querystatement));

			} else {

				$therecord["type"] = "client";

			}//endif id

			?><input type="hidden" id="clienttype" name="clienttype"  <?php echo 'value="'.$therecord["type"].'"'?> /><?php

		}//end method


		function updateStatus($invoiceid,$statusid,$statusdate,$assignedtoid, $replace = false){

			$invoiceuuid = getUuid($this->db, $this->uuid, (int) $invoiceid);

			$statusid = mysql_real_escape_string($statusid);
			$assignedtoid = mysql_real_escape_string($assignedtoid);

			if(!$replace){

				$querystatement = "
					DELETE FROM
						`invoicestatushistory`
					WHERE
						`invoiceid`='".$invoiceuuid."'
						AND
						`invoicestatusid`='".$statusid."'
				";

			}else{

				$querystatement = "
					DELETE FROM
						`invoicestatushistory`
					WHERE
						`invoiceid` = '".$invoiceuuid."'
				";

			}//end if

			$queryresult = $this->db->query($querystatement);

			$querystatement = "
				INSERT INTO
					`invoicestatushistory`
					(invoiceid,invoicestatusid,statusdate,assignedtoid) VALUES (";
			$querystatement .= "'".$invoiceuuid."', ";
			$querystatement .= "'".$statusid."', ";

			if($statusdate=="" || $statusdate=="0/0/0000") $tempdate="NULL";
			else{
				$tempdate="\"".sqlDateFromString($statusdate)."\"";
			}
			$querystatement.=$tempdate.",";
			if($assignedtoid=="")
				$querystatement.="NULL";
			else
				$querystatement .= "'".$assignedtoid."'";

			$querystatement.=")";

			$queryresult=$this->db->query($querystatement);
		}


		function showPaymentSelect($uuid,$paymentMethods){
			?><select name="paymentmethodid" id="paymentmethodid" onchange="showPaymentOptions()">
				<option value="" <?php if($uuid=="") echo "selected=\"selected\""?>>&lt;none&gt;</option>
			<?php foreach($paymentMethods as $method){?>
				<option value="<?php echo $method["uuid"]?>" <?php if($uuid==$method["uuid"]) echo "selected=\"selected\""?>><?php echo $method["name"]?></option>
			<?php } ?>
			</select>
			<?php
		}


		function getDefaultStatus(){

			$querystatement="SELECT uuid FROM invoicestatuses WHERE invoicedefault=1";
			$queryresult=$this->db->query($querystatement);

			$therecord=$this->db->fetchArray($queryresult);

			return $therecord["uuid"];
		}


		function getStatuses($statusid){

			global $phpbms;

			$querystatement="
				SELECT
					`invoicestatuses`.`uuid`,
					`invoicestatuses`.`name`,
					`invoicestatuses`.`invoicedefault`,
					`invoicestatuses`.`setreadytopost`,
					`invoicestatuses`.`defaultassignedtoid`,
					`users`.`firstname`,
					`users`.`lastname`
				FROM
					(`invoicestatuses` LEFT JOIN `users` ON `invoicestatuses`.`defaultassignedtoid`=`users`.`uuid`)
				WHERE
					`invoicestatuses`.`inactive`='0'
					OR
					`invoicestatuses`.`uuid`='".mysql_real_escape_string($statusid)."'
				ORDER BY
					`invoicestatuses`.`priority`,
					`invoicestatuses`.`name`";

			$queryresult=$this->db->query($querystatement);

			$thereturn = array();

			$phpbms->topJS[] = 'statuses=Array();';


			while($therecord = $this->db->fetchArray($queryresult)){

				$thereturn[] = $therecord;

				$uuid = $therecord["uuid"];

				$phpbms->topJS[] = 'statuses["'.$uuid.'"]=Array();';
				$phpbms->topJS[] = 'statuses["'.$uuid.'"]["name"]="'.htmlQuotes($therecord["name"]).'";';
				$phpbms->topJS[] = 'statuses["'.$uuid.'"]["setreadytopost"]='.$therecord["setreadytopost"].';';
				$phpbms->topJS[] = 'statuses["'.$uuid.'"]["userid"]="'.htmlQuotes($therecord["defaultassignedtoid"]).'";';
				$phpbms->topJS[] = 'statuses["'.$uuid.'"]["firstname"]="'.htmlQuotes($therecord["firstname"]).'";';
				$phpbms->topJS[] = 'statuses["'.$uuid.'"]["lastname"]="'.htmlQuotes($therecord["lastname"]).'";';

			}//endwhile

			return $thereturn;

		}//end function


		function showStatusDropDown($statusid,$statuses){

			?><select id="statusid" name="statusid" class="important">
				<?php

				foreach($statuses as $therecord){
					?><option value="<?php echo $therecord["uuid"]?>" <?php if($statusid==$therecord["uuid"]) echo "selected=\"selected\""?>><?php echo $therecord["name"]?></option><?php
				}//endforeach

				?>
			</select><?php

		}//end method


		function getPayments($paymentmethodid){

			$querystatement="
				SELECT
					`id`,
					`uuid`,
					`name`,
					`type`,
					`onlineprocess`,
					`processscript`
				FROM
					`paymentmethods`
				WHERE
					`inactive`='0'
					OR
					`uuid`='".mysql_real_escape_string($paymentmethodid)."'
				ORDER BY
					`priority`,
					`name`";

			$queryresult=$this->db->query($querystatement);

			$thereturn = array();

			global $phpbms;

			$phpbms->topJS[] = 'paymentMethods=Array();';

			while($therecord = $this->db->fetchArray($queryresult)) {
				$thereturn[$therecord["uuid"]]=$therecord;

				$uuid = $therecord["uuid"];

				$phpbms->topJS[] = 'paymentMethods["'.$uuid.'"]=Array();';
				$phpbms->topJS[] = 'paymentMethods["'.$uuid.'"]["name"]="'.htmlQuotes($therecord["name"]).'";';
				$phpbms->topJS[] = 'paymentMethods["'.$uuid.'"]["type"]="'.htmlQuotes($therecord["type"]).'";';
				$phpbms->topJS[] = 'paymentMethods["'.$uuid.'"]["onlineprocess"]="'.htmlQuotes($therecord["onlineprocess"]).'";';
				$phpbms->topJS[] = 'paymentMethods["'.$uuid.'"]["processscript"]="'.htmlQuotes($therecord["processscript"]).'";';
			}

			return $thereturn;

		}//end function


		function showShippingSelect($uuid,$shippingMethods){

			?><select name="shippingmethodid" id="shippingmethodid" onchange="changeShipping()">
				<option value="" <?php if($uuid=="") echo "selected=\"selected\""?>>&lt;none&gt;</option>
			<?php foreach($shippingMethods as $method){?>
				<option value="<?php echo $method["uuid"]?>" <?php if($uuid==$method["uuid"]) echo "selected=\"selected\""?>><?php echo $method["name"]?></option>
			<?php } ?>
			</select>
			<?php

		}//end function


		function getShipping($shippingmethodid){

			$shippingmethodid = mysql_real_escape_string($shippingmethodid);

			$querystatement = "
				SELECT
					`id`,
					`uuid`,
					`name`,
					`canestimate`,
					`estimationscript`
				FROM
					`shippingmethods`
				WHERE
					`inactive`='0'
					OR
					`uuid`='".$shippingmethodid."'
				ORDER BY
					`priority`,
					`name`
			";

			$queryresult = $this->db->query($querystatement);

			$thereturn = array();

			global $phpbms;

			$phpbms->topJS[] = 'shippingMethods=Array();';

			while($therecord = $this->db->fetchArray($queryresult)) {
				$thereturn[$therecord["uuid"]]=$therecord;

				$uuid = $therecord["uuid"];

				$phpbms->topJS[] = 'shippingMethods["'.$uuid.'"]=Array();';
				$phpbms->topJS[] = 'shippingMethods["'.$uuid.'"]["name"]="'.htmlQuotes($therecord["name"]).'";';
				$phpbms->topJS[] = 'shippingMethods["'.$uuid.'"]["canestimate"]='.((int) $therecord["canestimate"]).';';
				$phpbms->topJS[] = 'shippingMethods["'.$uuid.'"]["estimationscript"]="'.htmlQuotes($therecord["estimationscript"]).'";';
			}

			return $thereturn;

		}//end function


		/**
		 * function getCreditMemos
		 *
		 * @param string $uuid The uuid of the invoice record whose credit
		 * memos you wish to receive.
		 */

		function getCreditMemos($uuid) {

			$querystatement = "
				SELECT
					`id`,
					`orderdate`,
					`invoicedate`,
					`totalti`
				FROM
					`invoices`
				WHERE
					`cmuuid` = '".$uuid."'
					AND
					`iscreditmemo` != '0'
			";

			$queryresult = $this->db->query($querystatement);


			$thereturn = array();
			while($therecord = $this->db->fetchArray($queryresult)){

				$thereturn[$therecord["id"]] = $therecord;

			}//end while

			return $thereturn;

		}//end method --getCreditMemos--


		/**
		 * function showCreditMemos
		 * @param array $records array of records
		 */

		function showCreditMemos($records) {

			if(count($records)){

				?><table class="querytable simple" cellspacing="0" cellpadding="0" border="0">
					<thead>
						<tr>
							<th nowrap="nowrap" align="left">
								Credit Memo ID
							</th>
							<th nowrap="nowrap" align="left">
								Order Date
							</th>
							<th nowrap="nowrap" align="left">
								Invoice Date
							</th>
							<th width="90%" nowrap="nowrap" align="right">
								Total
							</th>
							<th>
								&nbsp;
							</th>
						</tr>
					</thead>
					<tbody id="resultTbody">
				<?php

				$count = 1;
				$classMod = 1;
				foreach($records as $record){

					$trid = "cm".$count;
					if($classMod === 1)
						$classMod = 2;
					else
						$classMod = 1;
					?>
						<tr class="qr<?php echo $classMod; ?>" id="<?php echo $trid; ?>">
							<td>
								<?php echo $record["id"]; ?>
							</td>
							<td>
								<?php echo formatVariable($record["orderdate"], "date"); ?>
							</td>
							<td>
								<?php echo formatVariable($record["invoicedate"], "date"); ?>
							</td>
							<td align="right">
								<?php echo formatVariable($record["totalti"], "currency"); ?>
							</td>
							<td>
								<button type="button" class="graphicButtons buttonInfo CMButtons" title="view record" id="cmb-<?php echo $record["id"]; ?>">
									<span>
										view record
									</span>
								</button>
							</td>
						</tr>
					<?php

					$count++;
				}//end foreach

				?>

						</tbody>
					</table>

				<?php

			}

		}//end if


		function showTaxSelect($uuid){

			$uuid = mysql_real_escape_string($uuid);
			$querystatement = "
				SELECT
					`uuid`,
					`name`,
					`percentage`
				FROM
					`tax`
				WHERE
					`inactive`='0' OR `uuid`='".$uuid."'
				ORDER BY
					`name`";

			$queryresult = $this->db->query($querystatement);

			?><select name="taxareaid" id="taxareaid" onchange="getPercentage()" size="5">
				<option value="" <?php if($uuid=="") echo "selected=\"selected\""?>>&lt;none&gt;</option>
				<?php
					while($therecord = $this->db->fetchArray($queryresult)){
						?><option value="<?php echo $therecord["uuid"]?>" <?php if($uuid==$therecord["uuid"]) echo "selected=\"selected\""?>><?php echo $therecord["name"].": ".$therecord["percentage"]."%"?></option><?php
					}
				?>
			</select><?php

		}//end function


		function showDiscountSelect($uuid){

			$uuid = mysql_real_escape_string($uuid);
			$querystatement = "
				SELECT
					`uuid`,
					`name`,
					`type`,
					`value`
				FROM
					`discounts`
				WHERE
					`inactive` != '1'
				ORDER BY
					`name`";
			$queryresult=$this->db->query($querystatement);

			?><select name="discountid" id="discountid" size="8">
				<option value="" <?php if($uuid=='') echo 'selected="selected"'?>>&lt;none&gt;</option>
				<?php
					while($therecord=$this->db->fetchArray($queryresult)){
						if($therecord["type"]=="amount")
							$therecord["value"]=numberToCurrency($therecord["value"]);
						else
							$therecord["value"].="%";
						?><option value="<?php echo $therecord["uuid"]?>" <?php if($uuid==$therecord["uuid"]) echo "selected=\"selected\""?>><?php echo $therecord["name"].": ".$therecord["value"]?></option><?php
					}
				?>
			</select><?php

		}//end function


		function getDiscount($uuid){

			$therecord["name"]="";
			$therecord["value"]=0;

			if($uuid){

				$uuid = mysql_real_escape_string($uuid);
				$querystatement = "
					SELECT
						`name`,
						`type`,
						`value`
					FROM
						`discounts`
					WHERE
						`uuid`='".$uuid."'
				";

				$queryresult = $this->db->query($querystatement);

				$therecord = $this->db->fetchArray($queryresult);
				if($therecord["type"]!="amount"){
					$therecord["value"].="%";
					$therecord["name"].=": ".$therecord["value"];
				} else
					$therecord["name"].=": ".numberToCurrency($therecord["value"]);
			}

			$therecord["name"]=htmlQuotes($therecord["name"]);

			return $therecord;

		}//end function


		function getTax($uuid){

			$therecord["name"]="";

			if($uuid){

				$uuid = mysql_real_escape_string($uuid);
				$querystatement = "
					SELECT
						`name`,
						`percentage`
					FROM
						`tax`
					WHERE
						`uuid`='".$uuid."'
				";

				$queryresult = $this->db->query($querystatement);

				if($this->db->numRows($queryresult))
					$therecord=$this->db->fetchArray($queryresult);
			} else{
				$therecord["name"] = NULL;
				$therecord["percentage"] = NULL;
			}

			$therecord["name"]= htmlQuotes($therecord["name"]);

			return $therecord;

		}//end function


		function prospectToClient($clientid){

			$updatestatement = "
				UPDATE
					clients
				SET
					type='client',
					becameclient = NOW()
				WHERE
					`uuid`='".$clientid."'
			";

			$this->db->query($updatestatement);

		}//end function

		// CLASS OVERRIDES ======================================================================================

		function getDefaults(){
			$therecord = parent::getDefaults();

			if(isset($_GET["cid"]))
				$therecord["clientid"] = $_GET["cid"];
			else
				$therecord["clientid"]="";

			$therecord["clientrealid"] = 0;
			$therecord["type"] = "Order";
			$therecord["statusid"] = $this->getDefaultStatus();
			$therecord["orderdate"] = dateToString(mktime(),"SQL");
			$therecord["statusdate"] = dateToString(mktime(),"SQL");
			$therecord["printedinstructions"] = INVOICE_DEFAULT_PRINTINSTRUC;

			$therecord["discountid"] = DEFAULT_DISCOUNT;
			$therecord["taxareaid"] = DEFAULT_TAXAREA;
			$therecord["shippingmethodid"] = DEFAULT_SHIPPING;
			$therecord["paymentmethodid"] = DEFAULT_PAYMENT;

			$discountinfo=$this->getDiscount($therecord["discountid"]);
			$therecord["discountname"]=$discountinfo["name"];
			$therecord["discount"]=$discountinfo["value"];

			$taxinfo = $this->getTax($therecord["taxareaid"]);
			$therecord["taxname"]=$taxinfo["name"];
			$therecord["taxpercentage"]=$taxinfo["percentage"];
			$therecord["amountdue"]=0;

			$therecord["hascredit"]=0;
			$therecord["creditlimit"]=0;
			$therecord["creditleft"]=0;

			$therecord["thelineitems"] = array();
			$therecord["cmid"] = "";

			return $therecord;
		}


		/**
		 * retrieves sales order record
		 *
		 * @param int|string $id the id or uuid of the sales order record
		 * @param bool $useUuid is the passed $id is referenceing an id (false/default) or a uuid (true)
		 *
		 * @return array associateive array with the record information
		 */
		function getRecord($id, $useUuid = false){

			$therecord = parent::getRecord($id, $useUuid);

			/**
			  *  If `type` is not an order and not a quote and payment information is being
			  *  encypted, we need to not decypt unencrypted payment information.
			  */
			if($therecord["type"] != "Order" && $therecord["type"] != "Quote" && ENCRYPT_PAYMENT_FIELDS){

				$querystatement = "
					SELECT
						`ccnumber`,
						`ccverification`,
						`ccexpiration`,
						`accountnumber`,
						`routingnumber`
					FROM
						`invoices`
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

			if(ENCRYPT_PAYMENT_FIELDS && ($therecord["type"] == "Order" || $therecord["type"] == "Quote")){

				if($therecord["ccverification"])
					$therecord["ccverification"] = str_repeat("*",strlen($therecord["ccverification"]));
				if($therecord["ccnumber"])
					$therecord["ccnumber"] = str_repeat("*",strlen($therecord["ccnumber"] -4 )).substr($therecord["ccnumber"], -4);
				if($therecord["routingnumber"])
					$therecord["routingnumber"] = str_repeat("*",strlen($therecord["routingnumber"] -4 )).substr($therecord["routingnumber"], -4);
				if($therecord["accountnumber"])
					$therecord["accountnumber"] = str_repeat("*",strlen($therecord["accountnumber"] -4 )).substr($therecord["accountnumber"], -4);

			}//end if

			$discountinfo = $this->getDiscount($therecord["discountid"]);

			$therecord["discountname"] = $discountinfo["name"];
			$therecord["discount"] = $discountinfo["value"];

			$taxinfo = $this->getTax($therecord["taxareaid"]);
			$therecord["taxname"] = $taxinfo["name"];

			//need to calculate the amount due
			$therecord["amountdue"] = $therecord["totalti"] - $therecord["amountpaid"];

			$querystatement = "
				SELECT
					id AS clientrealid,
					hascredit,
					creditlimit,
					`type` AS `clienttype`
				FROM
					clients
				WHERE
					`uuid`='".$therecord["clientid"]."'";

			$queryresult = $this->db->query($querystatement);

			$therecord = array_merge($this->db->fetchArray($queryresult), $therecord);

			/**
			  *  Need to get the lineitems as well
			  */
			if($this->lineitems === NULL)
				$this->lineitems = new lineitems($this->db, $therecord["id"]);
			$lineitems["thelineitems"] = $this->lineitems->get();

			$therecord = array_merge($lineitems, $therecord);

			//if the client has AR, we need to grab the creditlimit
			if($therecord["hascredit"]){

				$querystatement = "
					SELECT
						SUM(`amount` - `paid`) AS amtopen
					FROM
						aritems
					WHERE
						`status` = 'open'
					AND
						clientid = '".$therecord["clientid"]."'
						AND posted = 1";

				$queryresult = $this->db->query($querystatement);

				$arrecord = $this->db->fetchArray($queryresult);

				$therecord["creditleft"] = $therecord["creditlimit"] - $arrecord["amtopen"];

			} else
				$therecord["creditleft"] = 0;

			$therecord["cmid"] = "";
			if($therecord["cmuuid"])
				$therecord["cmid"] = getId($this->db, $this->uuid, $therecord["cmuuid"]);

			/**
			  * processing variables
			  * (used as guides for the api)
			  */
			$therecord["statuschanged"] = true;
			$therecord["lineitemschanged"] = true;
			$therecord["billingsaveoptions"] = NULL;
			$therecord["shippingsaveoptions"] = NULL;
			$therecord["oldType"] = $therecord["type"];


			return $therecord;

		}//end function getRecord


		function verifyVariables($variables){

			//must have a client
			if(isset($variables["clientid"])){

				if($this->_availableClientUUIDs === NULL)
					$this->_availableClientUUIDs = $this->_loadUUIDList("clients");

				if(!in_array(((string)$variables["clientid"]),$this->_availableClientUUIDs))
					$this->verifyErrors[] = "The `clientid` field does not give an existing/acceptable client id number.";

			}else
				$this->verifyErrors[] = "The `clientid` field must be set.";

			//table default (NULL) is not enough
			if(isset($variables["type"])){

				switch($variables["type"]){

					case "Quote":
					case "Order":
					case "Invoice":
					case "VOID":
						break;

					default:
						$this->verifyErrors[] = "The value of the `type` field is invalid.  It must be 'Quote',
							'Order', 'Invoice', or 'VOID'.";
						break;

				}//end switch

			}else
				$this->verifyErrors[] = "The `type` field must be set.";

			//check assigned to id
			if(isset($variables["assignedtoid"])){

				if($this->_availableUserUUIDs === NULL){

					$this->_availableUserUUIDs = $this->_loadUUIDList("users");
					$this->_availableUserUUIDs[] = "";//for none

				}//end if

				if(!in_array(((string)$variables["assignedtoid"]),$this->_availableUserUUIDs))
					$this->verifyErrors[] = "The `assignedtoid` field does not give an existing/acceptable user id number.";

			}//end if

			//check status id
			if(isset($variables["statusid"])){

				if($this->_availableStatusUUIDs === NULL)
					$this->_availableStatusUUIDs = $this->_loadUUIDList("invoicestatuses");

				if(!in_array(((string)$variables["statusid"]),$this->_availableStatusUUIDs))
					$this->verifyErrors[] = "The `statusid` field does not give an existing/acceptable status id number.";

			}//end if

			//check booleans
				//readytopost
				if(isset($variables["readytopost"]))
					if($variables["readytopost"] && $variables["readytopost"] != 1)
						$this->verifyErrors[] = "The `readytopost` field must be a boolean (equivalent to 0 or exactly 1).";

				//weborder
				if(isset($variables["weborder"]))
					if($variables["weborder"] && $variables["weborder"] != 1)
						$this->verifyErrors[] = "The `weborder` field must be a boolean (equivalent to 0 or exactly 1).";

				//shiptosameasbilling
				if(isset($variables["shiptosameasbilling"]))
					if($variables["shiptosameasbilling"] && $variables["shiptosameasbilling"] != 1)
						$this->verifyErrors[] = "The `shiptosameasbilling` field must be a boolean (equivalent to 0 or exactly 1).";

				//iscreditmemo
				if(isset($variables["iscreditmemo"]))
					if($variables["iscreditmemo"] && $variables["iscreditmemo"] != 1)
						$this->verifyErrors[] = "The `iscreditmemo` field must be a boolean (equivalent to 0 or exactly 1).";
			//check addresss ids
			//check secondary line item ids

			return parent::verifyVariables($variables);

		}//end method verifyVariables


		function prepareVariables($variables){

			$variables["totaltni"] = currencyToNumber($variables["totaltni"]);
			$variables["discountamount"] = currencyToNumber($variables["discountamount"]);
			$variables["totaltaxable"] = ((real) $variables["totaltaxable"]);
			$variables["totalti"] = currencyToNumber($variables["totalti"]);
			$variables["shipping"] = currencyToNumber($variables["shipping"]);
			$variables["tax"] = currencyToNumber($variables["tax"]);
			$variables["amountpaid"] = currencyToNumber($variables["amountpaid"]);

			if($variables["type"]=="Invoice" && $variables["invoicedate"] == "")
				$variables["invoicedate"] = dateToString(mktime());

			if($variables["taxpercentage"]=="" or  $variables["taxpercentage"]=="0" or $variables["taxpercentage"]=="0.0")
				$variables["taxpercentage"]="NULL";

			$variables["taxpercentage"]=str_replace("%","",$variables["taxpercentage"]);

			if($variables["type"]=="VOID"){
				$variables["totaltni"]=0;
				$variables["totalti"]=0;
				$variables["amountpaid"]=0;
			}

			if(!isset($therecord["iscreditmemo"]))
				$therecord["iscreditmemo"] = 0;

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

			/**
			  *  Lineitems are formated in JSON format, and need to be converted
			  *  to a php array.
			  */
			if($variables["lineitemschanged"]==1){

				$variables["thelineitems"] = stripslashes($variables["thelineitems"]);
				$variables["thelineitems"] = json_decode($variables["thelineitems"], true);

			}//endif

			return $variables;

		}//end function prepareVariables


		function addressUpdate($variables, $invoiceid, $modifiedby, $method){
		// Updates/Inserts address records (billing or shipping )for the sales order's corresponding client
			switch($method){
				case "shipping":
					$varprefix = "shipto";
					$idprefix = "shipto";
				break;

				case "billing":
					$varprefix = "";
					$idprefix = "billing";
				break;
			}//endswitch

			switch($variables[$idprefix."saveoptions"]){

				case "updateAddress":
					if($variables[$idprefix."addressid"]){

						$address = new addresses($this->db, "tbld:27b99bda-7bec-b152-8397-a3b09c74cb23");

						$querystatment = "
							SELECT
								`id`
							FROM
								`addresses`
							WHERE
								`uuid` = '".mysql_real_escape_string($variables[$idprefix."addressid"])."'
						";
						$queryresult = $this->db->query($querystatment);
						$arecord = $this->db->fetchArray($queryresult);

						$addressRecord = $address->getRecord($arecord["id"]);
						if($varprefix == "shipto")
							$addressRecord["shiptoname"] = $variables["shiptoname"];
						$addressRecord["address1"] = $variables[$varprefix."address1"];
						$addressRecord["address2"] = $variables[$varprefix."address2"];
						$addressRecord["city"] = $variables[$varprefix."city"];
						$addressRecord["state"] = $variables[$varprefix."state"];
						$addressRecord["postalcode"] = $variables[$varprefix."postalcode"];
						$addressRecord["country"] = $variables[$varprefix."country"];

						$address->updateRecord($addressRecord, $modifiedby);

					}//endif

					break;

				case "createAddress":
					$addresstorecord = new addresstorecord($this->db,"tbld:27b99bda-7bec-b152-8397-a3b09c74cb23");

					if($varprefix == "shipto")
						$addressRecord["shiptoname"] = $variables["shiptoname"];
					$atrRecord["uuid"] = uuid($addresstorecord->prefix.":");
					$atrRecord["address1"] = $variables[$varprefix."address1"];
					$atrRecord["address2"] = $variables[$varprefix."address2"];
					$atrRecord["city"] = $variables[$varprefix."city"];
					$atrRecord["state"] = $variables[$varprefix."state"];
					$atrRecord["postalcode"] = $variables[$varprefix."postalcode"];
					$atrRecord["country"] = $variables[$varprefix."country"];
					$atrRecord["recordid"] = $variables["clientid"];
					$atrRecord["tabledefid"] = "tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083";
					$atrRecord["primary"] = 0;
					$atrRecord["defaultshipto"] = 0;
					$atrRecord["existingaddressid"] = 0;
					$atrRecord["notes"] = "Created from sales order #".$invoiceid;

					$newAtrID = $addresstorecord->insertRecord($atrRecord, $modifiedby);

					$atrRecord = $addresstorecord->getRecord($newAtrID);

					//Need to connect the sales order to the new address record
					$updatestatement = "
						UPDATE
							invoices
						SET
							".$idprefix."addressid = '".$atrRecord["uuid"]."'
						WHERE
							uuid = '".$invoiceid."'
					";

					$this->db->query($updatestatement);

					break;

			}//endswitch - saveoptions

		}//end method - addressUpdate


		function updateRecord($variables, $modifiedby = NULL, $useUuid = false){

			//can't modify an invoice
			if(isset($variables["oldType"]))
				if($variables["oldType"] == "Invoice")
					return false;

			//can't modify payment information if you do not have sales rights
			if(!hasRights("role:de7e6679-8bb2-29ee-4883-2fcd756fb120")){

				unset($this->fields["paymentmethodid"]);
				unset($this->fields["checkno"]);
				unset($this->fields["bankname"]);
				unset($this->fields["ccnumber"]);
				unset($this->fields["ccexpiration"]);
				unset($this->fields["accountnumber"]);
				unset($this->fields["routingnumber"]);
				unset($this->fields["transactionid"]);

			}else{
				if(ENCRYPT_PAYMENT_FIELDS && (isset($variables["ccnumber"]) || isset($variables["ccexpiration"]) || isset($variables["ccverification"]) || isset($variables["accountnumber"]) || isset($variables["routingnumber"])) ){

					if($useUuid)
						$whereclause = "`uuid` = '".mysql_real_escape_string($variables["uuid"])."'";
					else
						$whereclause = "`id` = '".(int)$variables["id"]."'";

					$querystatement = "
					UPDATE
						`invoices`
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

					$querystatement .= $fieldlist." WHERE `type` != 'Invoice' AND `type` != 'VOID' AND ".$whereclause;

					$this->db->query($querystatement);

				}//end if
			}//end if


			if(parent::updateRecord($variables, $modifiedby, $useUuid)){

				if(!isset($variables["id"]))
					$variables["id"] = getId($this->db, $this->uuid, $variables["uuid"]);

				if($variables["lineitemschanged"]==1){

					if($this->lineitems === NULL)
						$this->lineitems = new lineitems($this->db, $variables["id"]);
					else
						$this->lineitems->invoiceid = $variables["id"];
					$this->lineitems->set($variables["thelineitems"], $modifiedby);

				}//endif

				if($variables["statuschanged"]==1)
					$this->updateStatus($variables["id"],$variables["statusid"],$variables["statusdate"],$variables["assignedtoid"]);

				// Check to see if we need to update/create the client addresses from the
				// billing address

				if(!isset($variables["billingsaveoptions"]))
					$variables["billingsaveoptions"] = NULL;

				if(!isset($variables["shiptosaveoptions"]))
					$variables["shiptosaveoptions"] = NULL;

				if($variables["billingsaveoptions"] != "orderOnly" || $variables["shiptosaveoptions"] != "orderOnly"){

					require_once("addresses.php");
					require_once("addresstorecord.php");

					$this->addressUpdate($variables, $variables["id"], $modifiedby, "billing");
					$this->addressUpdate($variables, $variables["id"], $modifiedby, "shipping");

				}//end if

			}//end if

			if($variables["clienttype"] == "prospect" && $variables["type"] == "Order")
				$this->prospectToClient($variables["clientid"]);

			//reset field after updating (if unset by rights management)
			$this->getTableInfo();

		}//end function updateRecord



		function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false, $useUuid = false){

			if($createdby === NULL)
				$createdby = $_SESSION["userinfo"]["id"];

			$newid = parent::insertRecord($variables, $createdby, $overrideID, $replace, $useUuid);
			$id = $newid;
			if(isset($newid["uuid"]))
				$id = $newid["id"];

			if(ENCRYPT_PAYMENT_FIELDS && (isset($variables["ccnumber"]) || isset($variables["ccexpiration"]) || isset($variables["ccverification"]) || isset($variables["accountnumber"]) || isset($variables["routingnumber"])) ){

				if($useUuid)
					$whereclause = "`uuid` = '".$newid["uuid"]."'";
				else
					$whereclause = "`id` = '".$newid."'";

				$querystatement = "
					UPDATE
						`invoices`
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

					$querystatement .= $fieldlist." WHERE `type` != 'Invoice' AND `type` != 'VOID' AND ".$whereclause;

				$this->db->query($querystatement);

			}//end if

			if(!isset($variables["billingsaveoptions"]))
				$variables["billingsaveoptions"] = NULL;

			if(!isset($variables["shiptosaveoptions"]))
				$variables["shiptosaveoptions"] = NULL;

			if($variables["billingsaveoptions"] != "orderOnly" || $variables["shiptosaveoptions"] != "orderOnly"){

				require_once("addresses.php");
				require_once("addresstorecord.php");

				$newuuid = mysql_real_escape_string($variables["uuid"]);

				$this->addressUpdate($variables, $newuuid, $createdby, "billing");
				$this->addressUpdate($variables, $newuuid, $createdby, "shipping");

			}//end if


			if($variables["lineitemschanged"]==1){

				if($this->lineitems === NULL)
					$this->lineitems = new lineitems($this->db, $id);
				else
					$this->lineitems->invoiceid = $id;

				$this->lineitems->set($variables["thelineitems"], $createdby);

			}//end if

			//if($variables["statuschanged"]==1)
				$this->updateStatus($id,$variables["statusid"],$variables["statusdate"],$variables["assignedtoid"], $replace);

			if($variables["clienttype"] == "prospect" && $variables["type"] == "Order")
				$this->prospectToClient($variables["clientid"]);

			return $newid;

		}//end method - insertRecord

		/*
		 * function api_searchByClientUuid
		 * @param array $requestData Array containting the key "clientid", and,
		 * optionally, the keys "type" (relating to the invoice type, e.g. "Quote"),
		 * "startdate", and "enddate" which put an upper and lower bound to the
		 * invoice's datetime (with sql format).
		 * @param bool $returnUuid If true, returns result's uuid , if
		 * false, the id.
		 * @return array An array containing response information
		 * @returnf string 'type' The type of response (e.g. 'error' or 'result')
		 * @returnf string 'message' Message explaining the type / result
		 * @returnf array details Either the array of uuid / ids if no errors
		 * were encountered, or the original $requestData if there was an error
		 */

		function api_searchByClientUuid($requestData, $returnUuid = true) {

			/**
			  *  check for required field
			  */
			if(!isset($requestData["clientid"])){
				$response["type"] = "error";
				$response["message"] = "Data does not contain a key of 'clientid'.";
				$response["details"] = $requestData;

				return $response;
			}//end if

			/**
			  *  do sql search
			  */
			$querystatement = "
				SELECT
					`id`,
					`uuid`
				FROM
					`invoices`
				WHERE
					`clientid` = '".mysql_real_escape_string($requestData["clientid"])."'
			";

			if(isset($requestData["type"]))
				$querystatement .= "AND `type` = '".mysql_real_escape_string($requestData["type"])."'";

			if(isset($requestData["startdate"]))
				$querystatement .= "AND `creationdate` >= '".mysql_real_escape_string($requestData["startdate"])."'";

			if(isset($requestData["enddate"]))
				$querystatement .= "AND `creationdate` <= '".mysql_real_escape_string($requestData["enddate"])."'";

			$queryresult = $this->db->query($querystatement);

			/**
			  *  report findings
			  */
			$thereturn["details"] = array();
			$thereturn["message"] = "The function api_searchByPartNumber has been run successfully.";
			$thereturn["type"] = "result";
			while($therecord = $this->db->fetchArray($queryresult)){

				if($returnUuid)
					$thereturn["details"][] = $therecord["uuid"];
				else
					$thereturn["details"][] = $therecord["id"];

			}//end while

			return $thereturn;

		}//end function --api_searchByClientUuid--

	}//end class


	// LINE ITEMS CLASS
	// ==============================================================================================
	class lineitems{

		var $db;
		var $invoiceid;
		var $invoicetype;

		function lineitems($db, $invoiceid, $invoicetype = "Order"){

			$this->db = $db;
			$this->invoiceid = ((int) $invoiceid);
			$this->invoicetype = $invoicetype;

		}//end method


		function get(){

			$querystatement = "
				SELECT
					products.id,
					products.uuid AS pid,
					products.partname,
					products.partnumber,

					lineitems.id,
					lineitems.productid,
					lineitems.taxable,
					lineitems.quantity,
					lineitems.unitprice,
					lineitems.unitcost,
					lineitems.unitweight,
					lineitems.memo
				FROM
					lineitems LEFT JOIN products ON lineitems.productid = products.uuid
				WHERE
					invoiceid = ".$this->invoiceid."
				ORDER BY
					lineitems.displayorder";

			$queryresult = $this->db->query($querystatement);

			$thereturn = array();
			while($therecord = $this->db->fetchArray($queryresult))
				$thereturn[] = $therecord;

			return $thereturn;

		}//end method


		function show($lineitemArray){

			$count = 1;
			foreach($lineitemArray as $therecord){

				?><tr id="li<?php echo $count?>" class="lineitems">

					<td colspan="2" class="lineitemsLeft" <?php if($this->invoicetype == "Void" || $this->invoicetype == "Invoice") echo 'nowrap="nowrap"'?>>
						<input type="hidden" id="li<?php echo $count?>ProductID" value="<?php echo $therecord["pid"]?>"/>
						<input type="hidden" id="li<?php echo $count?>Taxable" value="<?php echo $therecord["taxable"]?>"/>
						<input type="hidden" id="li<?php echo $count?>UnitWeight" class="lineitemWeights" value="<?php echo $therecord["unitweight"]?>"/>
						<input type="hidden" id="li<?php echo $count?>UnitCost" class="lineitemCosts" value="<?php echo $therecord["unitcost"]?>"/>
						<div>
							<?php if($therecord["partnumber"] || $therecord["partname"] ) {?>
							<p><?php echo formatVariable($therecord["partnumber"]) ?></p>
							<p class="important"><?php echo formatVariable($therecord["partname"])?></p>
							<?php } else
									echo "&nbsp;";
							?>
						</div>
					</td>

					<td><input id="li<?php echo $count?>Memo" class="lineitemMemos" value="<?php echo formatVariable($therecord["memo"])?>"/></td>

					<td><input id="li<?php echo $count?>UnitPrice" class="fieldCurrency lineitemPrices" value="<?php echo formatVariable($therecord["unitprice"], "currency")?>"/></td>
					<td><input id="li<?php echo $count?>Quantity" class="lineitemQuantities" value="<?php echo formatVariable($therecord["quantity"], "real")?>"/></td>
					<td><input id="li<?php echo $count?>Extended" class="uneditable fieldCurrency lineitemExtendeds" readonly="readonly" value="<?php echo formatVariable($therecord["quantity"] * $therecord["unitprice"], "currency")?>"/></td>

					<td class="lineitemsButtonTDs">
						<div id="li<?php echo $count?>ButtonsDiv" class="lineitemsButtonDivs">
							<button type="button" id="li<?php echo $count?>ButtonDelete" class="graphicButtons buttonMinus LIDelButtons" title="Remove line item"><span>-</span></button><br />
							<button type="button" id="li<?php echo $count?>ButtonMoveUp" class="graphicButtons buttonUp LIUpButtons" title="Move Item Up"><span>Up</span></button><br />
							<button type="button" id="li<?php echo $count?>ButtonMoveDown" class="graphicButtons buttonDown LIDnButtons" title="Move Item Down"><span>Dn</span></button><br />
						</div>
					</td>

				</tr><?php

				$count++;

			}//end foreach

		}//end method


		function set($itemlist, $userid = NULL, $useUuid = false){

			if(!$userid)
				$userid = $_SESSION["userinfo"]["id"];

			$deletestatement = "
				DELETE FROM
					lineitems
				WHERE
					invoiceid = ".$this->invoiceid;

			$this->db->query($deletestatement);

			$count = 0;

			foreach($itemlist as $item){

				//$theid = ($useUuid)? mysql_real_escape_string($itemRecord[0]): getUuid($this->db, "tbld:7a9e87ed-d165-c4a4-d9b9-0a4adc3c5a34", (int) $itemRecord[0]);

				$insertstatement ="
					INSERT INTO
						lineitems(
							invoiceid,
							productid,
							memo,
							taxable,
							unitweight,
							unitcost,
							unitprice,
							quantity,
							displayorder,
							createdby,
							creationdate,
							modifiedby,
							modifieddate
						)
					VALUES (
						".$this->invoiceid.",
						'".mysql_real_escape_string($item["productid"])."',
						'".mysql_real_escape_string($item["memo"])."',
						".((int) $item["taxable"]).",
						".((real) $item["unitweight"]).",
						".((real) $item["unitcost"]).",
						".((real) $item["unitprice"]).",
						".((real) $item["quantity"]).",
						".$count.",
						".$userid.",
						NOW(),
						".$userid.",
						NOW()
					)";

				$this->db->query($insertstatement);

				$count++;

			}//endforeach

		}//end method

	}//end class

}// end if


if(class_exists("searchFunctions")){
	class invoicesSearchFunctions extends searchFunctions{

		function email_invoice($useUuid = false){

				if(DEMO_ENABLED == "true")
					return "Functionality disabled in demo.";

				$this->db->setEncoding("latin1");

				require_once("report/report_class.php");
                                include("modules/bms/report/invoices_pdf_class.php");

				$processed = 0;

				foreach($this->idsArray as $id){

					$report = new invoicePDF($this->db, 'rpt:44b21461-6e67-c284-0ccf-36ab1af47c9b', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 'P', 'in', 'Letter');

					if(!$useUuid)
						$report->generate("invoices.id = ".$id);
					else
						$report->generate("invoices.uuid = ".$id);

					if($report->output("email"))
						$processed++;

				}//end foreach

				$this->db->setEncoding();


				$count = count($this->idsArray);
				$message = $processed." of ".$count." invoice PDF";

				if($count !== 1)
					$message .= "s";

				$message .= " e-mailed to client.";

				return $message;

		}//end method


		function email_quote($useUuid = false){

				if(DEMO_ENABLED == "true")
					return "Functionality disabled in demo.";

				$this->db->setEncoding("latin1");

				$noOutput = true;
				require_once("report/report_class.php");
				include("modules/bms/report/invoices_pdf_class.php");
				include("modules/bms/report/invoices_pdfquote.php");

				$processed = 0;

				foreach($this->idsArray as $id){

					$report = new quotePDF($this->db, 'rpt:44b21461-6e67-c284-0ccf-36ab1af47c9b', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 'P', 'in', 'Letter');

					if(!$useUuid)
						$report->generate("invoices.id = ".$id);
					else
						$report->generate("invoices.uuid = ".$id);

					if($report->output("email"))
						$processed++;

				}//end foreach

				$this->db->setEncoding();


				$count = count($this->idsArray);
				$message = $processed." of ".$count." quote PDF";

				if($count !== 1)
					$message .= "s";

				$message .= " e-mailed to client.";

				return $message;

		}//end method


		function _mark_as_status($statusid, $useUuid = false){

			//Look up shippings defaults
			$querystatement = "
				SELECT
					`defaultassignedtoid`,
					`setreadytopost`
				FROM
					`invoicestatuses`
				WHERE
					`uuid` = '".$statusid."'
			";
			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){
				$therecord = $this->db->fetchArray($queryresult);

				if($therecord["defaultassignedtoid"]!="")
					$assignedtoid = $therecord["defaultassignedtoid"];
				else
					$assignedtoid="NULL";

				$readytopost = $therecord["setreadytopost"];
			} else {
				return "No status with uuid ".$statusid." found.";
			}

			if(!$useUuid)
				$whereclause = $this->buildWhereClause();
			else
				$whereclause = $this->buildWhereClause($thies->maintable.".`uuid`");

			$whereclause = "(".$whereclause.") AND invoices.type!='Invoice' AND invoices.type!='VOID' AND invoices.statusid !='".$statusid."'";

			// since marking RTP is dependent on the payment method type,
			// items must be updated individually
			$querystatement = "
				SELECT
					invoices.id,
					invoices.uuid,
					paymentmethods.type,
					invoices.invoicedate
				FROM
					invoices LEFT JOIN paymentmethods ON invoices.paymentmethodid = paymentmethods.uuid
				WHERE
					".$whereclause;

			$queryresult = $this->db->query($querystatement);

			$count=0;
			while($therecord = $this->db->fetchArray($queryresult)){

				$updatestatement = "
					UPDATE
						invoices
					SET
						invoices.statusdate=NOW(),
						assignedtoid='".$assignedtoid."',
						modifiedby=".$_SESSION["userinfo"]["id"].", ";

				if($readytopost){

					$updatestatement.="readytopost=1, ";

					if(!$therecord["invoicedate"] || $therecord["invoicedate"] == "0000-00-00")
						$updatestatement.="invoicedate = NOW(), ";

					if($therecord["type"] == "receivable")
						$updatestatement.="amountpaid = 0, ";
					else
						$updatestatement.="amountpaid = totalti,";

				}//endif

				$updatestatement.="
						invoices.statusid='".$statusid."',
						modifieddate=NOW()
					WHERE
						id = ".$therecord["id"];

				$updateresult = $this->db->query($updatestatement);

				//delete conlflicting history
				$querystatement = "DELETE FROM invoicestatushistory WHERE invoiceid='".$therecord["uuid"]."' AND invoicestatusid='".$statusid."'";
				$deleteresult = $this->db->query($querystatement);

				//insert new history
				$querystatement="INSERT INTO invoicestatushistory (invoiceid,invoicestatusid,statusdate,assignedtoid) values (";
				$querystatement.="'".$therecord["uuid"]."','".$statusid."',NOW(),";
				$querystatement.="'".$assignedtoid."'";
				$querystatement.=")";
				$insertresult = $this->db->query($querystatement);

				$count++;

			}//endwhile

			$message = $this->buildStatusMessage($count);

			return $message;

		}//end private method


		function mark_ashipped($useUuid = false){

			$statusid = "inst:e8b5e6a7-5797-7901-6266-6adeedd15ec9"; //The default id for "shipped";

			$message = $this->_mark_as_status($statusid, $useUuid);

			return $message." marked as shipped.";

		}//end method


		function mark_rtp($useUuid = false){

			if(!$useUuid)
				$whereclause = $this->buildWhereClause();
			else
				$whereclause = $this->buildWhereClause($thies->maintable.".`uuid`");

			$whereclause = "(".$whereclause.") AND invoices.type='Order'";

			// since marking RTP is dependent on the payment method type,
			// items must be updated individually
			$querystatement = "
				SELECT
					invoices.id,
					paymentmethods.type,
					invoices.invoicedate
				FROM
					invoices LEFT JOIN paymentmethods ON invoices.paymentmethodid = paymentmethods.uuid
				WHERE
					".$whereclause;

			$queryresult = $this->db->query($querystatement);

			$count = 0;
			while($therecord = $this->db->fetchArray($queryresult)){
				$updatestatement ="
					UPDATE
						`invoices`
					SET
						`modifiedby` = '".$_SESSION["userinfo"]["id"]."', ";

				if(!$therecord["invoicedate"] || $therecord["invoicedate"] == "0000-00-00")
					$updatestatement.="invoicedate = NOW(), ";

				if($therecord["type"] == "receivable")
					$updatestatement.="amountpaid = 0, ";
				else
					$updatestatement.="amountpaid = totalti, ";

				$updatestatement.="
						readytopost=1,
						modifieddate=NOW()
					WHERE
						id =".$therecord["id"];

				$updateresult = $this->db->query($updatestatement);

				$count++;

			}//endwhile

			$message = $this->buildStatusMessage($count);
			return $message." marked ready to post.";

		}//end method


		function mark_aspaid($useUuid = false){

			if(!$useUuid)
				$whereclause = $this->buildWhereClause();
			else
				$whereclause = $this->buildWhereClause($thies->maintable.".`uuid`");

			$whereclause = trim("
				invoices.type='Order'
				AND paymentmethods.type != 'receivable'
				AND (".$whereclause.")");

			$querystatement ="
				SELECT
					invoices.id
				FROM
					invoices LEFT JOIN paymentmethods ON invoices.paymentmethodid = paymentmethods.uuid
				WHERE
					".$whereclause;

			$queryresult = $this->db->query($querystatement);

			$count = 0;
			while($therecord = $this->db->fetchArray($queryresult)){

				$updatestatement = "
					UPDATE invoices
					SET
						invoices.amountpaid=invoices.totalti,
						modifiedby=\"".$_SESSION["userinfo"]["id"]."\",
						modifieddate=NOW()
					WHERE
						id=".$therecord["id"];

				$this->db->query($updatestatement);

				$count++;

			}//endwhile


			$message=$this->buildStatusMessage($count);
			$message.=" paid in full.";

			return $message;

		}//end method


		function mark_asinvoice($useUuid = false){

			if(!$useUuid)
				$whereclause = $this->buildWhereClause();
			else
				$whereclause = $this->buildWhereClause($thies->maintable.".`uuid`");

			include_once("include/post_class.php");
			defineInvoicesPost();

			$invoicesPost = new invoicesPost($this->db);

			$count = $invoicesPost->post($whereclause);

			$message = $this->buildStatusMessage($count);
			$message .= " posted as invoice";

			return $message;
		}//end method


		function delete_record($useUuid = false){

			if(!$useUuid)
				$whereclause=$this->buildWhereClause();
			else
				$whereclause = $this->buildWhereClause($this->maintable.".uuid");

			$querystatement = "
				UPDATE
					invoices
				SET
					invoices.type='VOID',";

			if(ENCRYPT_PAYMENT_FIELDS){
				$querystatement .="
					ccverification = REPEAT('*',LENGTH(".$this->db->decrypt('`ccverification`').")),
					ccexpiration =  REPEAT('*',LENGTH(".$this->db->decrypt('`ccexpiration`').")),
					routingnumber =  NULL,
					accountnumber =  NULL,
					ccnumber = LPAD(SUBSTRING(".$this->db->decrypt('`ccnumber`').",-4),LENGTH(".$this->db->decrypt('`ccnumber`')."),'*'),
				";
			}//endif

			$querystatement .="
					modifiedby = ".$_SESSION["userinfo"]["id"].",
					modifieddate = NOW()
				WHERE (".$whereclause.")
					AND invoices.type!='Invoice';";

			$queryresult = $this->db->query($querystatement);

			$message = $this->buildStatusMessage();
			$message.=" voided.";

			return $message;
		}//end method



		/**
		  *  function create_credit_memo
		  *
		  *
		  *  @param bool $useUuid Whether the ids in $this->idsArray are
		  *  uuids or ids.
		  */
		function create_credit_memo($useUuid = false){

			if(!$useUuid)
				$whereclause = $this->buildWhereClause();
			else
				$whereclause = $this->buildWhereClause($this->maintable.".uuid");

			if(isset($_SESSION["userinfo"]["id"]))
				$createdby = $_SESSION["userinfo"]["id"];
			else
				$error = new appError(-841,"Session Timed Out.","Creating New Record");

			$invoicestatusQuery = "
				SELECT
					`uuid`
				FROM
					`invoicestatuses`
				WHERE
					`invoicedefault` != '0'
			";

			$queryresult = $this->db->query($invoicestatusQuery);

			$statusRecord = $this->db->fetchArray($queryresult);


			if(!class_exists("phpbmsTable"))
				include_once("include/tables.php");

			$invoices = new phpbmsTable($this->db, "tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883");
			$count = 0;
			if(count($this->idsArray))
				foreach($this->idsArray as $id){

					$invoiceRecord = $invoices->getRecord($id, $useUuid);

					if($invoiceRecord["type"] == "Invoice" && !$invoiceRecord["iscreditmemo"]){

						$count++;

						$fieldsList = "";
						$insertValues = "";
						foreach($invoiceRecord as $name => $value){

							switch($name){

								case "id":
								case "ccexpiration":
								case "bankname":
								case "checkno":
								case "routingnumber":
								case "accountnumber":
								case "transactionid":
								case "postingsessionid":
								case "readytopost":
								case "assignedtoid":
								case "invoicedate":
								case "requireddate":
								case "amountpaid":
								case "cmuuid":
								break;

								case "type":
									$fieldsList .= ",`".$name."`";
									$insertValues .= ",'Order'";
								break;

								case "iscreditmemo":
									$fieldsList .= ",`".$name."`";
									$insertValues .= ",'1'";
								break;

								case "createdby":
								case "modifiedby":
									$fieldsList .= ",`".$name."`";
									$insertValues .= ",'".((int) $createdby)."'";
								break;

								case "uuid":
									$fieldsList .= ",`cmuuid`";
									$insertValues .= ",'".$value."'";
									$fieldsList .= ",`uuid`";
									$newUuid = uuid($invoices->prefix.":");
									$insertValues .= ",'".$newUuid."'";
								break;

								case "creationdate":
								case "modifieddate":
								case "statusdate":
								case "orderdate":
									$fieldsList .= ",`".$name."`";
									$insertValues .= ",NOW()";
								break;

								case "statusid":
									$fieldsList .= ",`".$name."`";
									$insertValues .= ",'".$statusRecord["uuid"]."'";
								break;

								case "discountamount":
								case "totaltni":
								case "totaltaxable":
								case "tax":
								case "shipping":
								case "totalcost":
								case "totalti":
									$fieldsList .= ",`".$name."`";
									$insertValues .= ",'".(-(real)$value)."'";
								break;

								default:
									$fieldsList .= ",`".$name."`";
									$insertValues .= ",'".$value."'";
								break;

							}//end switch

						}//end foreach

						$fieldsList = substr($fieldsList, 1);
						$insertValues = substr($insertValues, 1);

						$insertstatement = "
							INSERT INTO
								`invoices`
							(".$fieldsList.")
							VALUES
							(".$insertValues.")
						";

						$queryresult = $this->db->query($insertstatement);

						$newid = $this->db->insertId();

						//invoice status history
						$statushistoryquery = "
							INSERT INTO
								`invoicestatushistory`
								(
									invoiceid,
									invoicestatusid,
									statusdate,
									assignedtoid
								)
								VALUES
								(
									'".mysql_real_escape_string($newUuid)."',
									'".$statusRecord["uuid"]."',
									NOW(),
									''
								)
						";

						$this->db->query($statushistoryquery);

						// Line items
						$lineitemstatement = "
							SELECT
								lineitems.productid,
								lineitems.taxable,
								-(`lineitems`.`quantity`) AS `quantity`,
								lineitems.unitprice,
								lineitems.unitcost,
								lineitems.unitweight,
								lineitems.memo,
								lineitems.displayorder,
								lineitems.custom1,
								lineitems.custom2,
								lineitems.custom3,
								lineitems.custom4,
								lineitems.custom5,
								lineitems.custom6,
								lineitems.custom7,
								lineitems.custom8,
								lineitems.createdby,
								lineitems.modifiedby,
								lineitems.creationdate,
								lineitems.modifieddate
							FROM
								lineitems
							WHERE
								invoiceid = '".$id."'";

						$lineitemresult = $this->db->query($lineitemstatement);

						while($lineitemrecord = $this->db->fetchArray($lineitemresult)){

							$fieldsList = "";
							$insertValues = "";

							$fieldsList .= "`invoiceid`";
							$insertValues .= "'".$newid."'";

							foreach($lineitemrecord as $name => $value){

								switch($name){

									case "createdby":
									case "modifiedby":
										$fieldsList .= ",`".$name."`";
										$insertValues .= ",'".((int) $createdby)."'";
									break;

									case "creationdate":
									case "modifieddate":
										$fieldsList .= ",`".$name."`";
										$insertValues .= ",NOW()";
									break;

									default:
										$fieldsList .= ",`".$name."`";
										$insertValues .= ",'".$value."'";
									break;

								}//end switch

							}//end foreach

							$lineinsertstatement = "
								INSERT INTO
									`lineitems`
								(".$fieldsList.")
								VALUES
								(".$insertValues.")
							";

							$this->db->query($lineinsertstatement);

						}//end while

					}//end if

				}//end foreach

				$this->buildStatusMessage($count);

		}//end method

	}//end class
}//end if


function obfuscatePayment($variables){

	$variables["ccverification"] = str_repeat("*",strlen($variables["ccverification"]));
	$variables["ccexpiration"] = str_repeat("*",strlen($variables["ccexpiration"]));
	$variables["routingnumber"] = "NULL";
	$variables["accountnumber"] = "NULL";
	$variables["ccnumber"] = str_repeat("*",strlen($variables["ccnumber"] - 4)).substr($variables["ccnumber"], -4);

	return $variables;

}//end function - obfuscatePayment


function defineInvoicesPost(){

	class invoicesPost extends tablePost{

		var $maintable = "invoices";
		var $datefieldname = "invoicedate";
		var $notpostedCriteria = "(invoices.type != 'Invoice' && invoices.type != 'VOID')";

		function prepareWhere($whereclause=NULL){

			$this->whereclause = "";

			if($whereclause)
				$this->whereclause = "(".$whereclause.") AND ";

			$this->whereclause .= "invoices.type='Order' AND readytopost = 1";

		}//end method


		function post($whereclause = NULL, $postsessionid = NULL){

			if($whereclause)
				$this->prepareWhere($whereclause);

			if(!$postsessionid)
				$postsessionid = $this->generatePostingSession("search");

			$querystatement = "
				SELECT
					invoices.id,
					invoices.uuid,
					invoices.clientid,
					invoices.totalti,
					invoices.invoicedate,
					paymentmethods.type,";
			if(ENCRYPT_PAYMENT_FIELDS){
				$paymentfields = "
					".$this->db->decrypt("`invoices`.`ccnumber`")." AS `ccnumber`,
					".$this->db->decrypt("`invoices`.`ccexpiration`")." AS `ccexpiration`,
					".$this->db->decrypt("`invoices`.`ccverification`")." AS `ccverification`,
					".$this->db->decrypt("`invoices`.`routingnumber`")." AS `routingnumber`,
					".$this->db->decrypt("`invoices`.`accountnumber`")." AS `accountnumber`";
			}else{
				$paymentfields = "
					invoices.ccnumber,
					invoices.ccexpiration,
					invoices.ccverification,
					invoices.routingnumber,
					invoices.accountnumber
				";
			}//end if

			$querystatement .= $paymentfields." FROM
					invoices LEFT JOIN paymentmethods ON invoices.paymentmethodid = paymentmethods.uuid
				WHERE
					".$this->whereclause;

			$queryresult = $this->db->query($querystatement);

			$count = 0;

			while($therecord = $this->db->fetchArray($queryresult)){

				$updatestatement = "
					UPDATE
						`invoices`
					SET
						`type` = 'Invoice',
						`postingsessionid` = ".$postsessionid.",";

				if(ENCRYPT_PAYMENT_FIELDS){

					$therecord = obfuscatePayment($therecord);

					if($therecord["ccnumber"])
						$updatestatement .= "ccnumber = '".$therecord["ccnumber"]."', ";

					if($therecord["ccexpiration"])
						$updatestatement .= "ccexpiration = '".$therecord["ccexpiration"]."', ";

					if($therecord["ccverification"])
						$updatestatement .= "ccverification = '".$therecord["ccverification"]."', ";

					if($therecord["routingnumber"])
						$updatestatement .= "routingnumber = NULL, ";

					if($therecord["accountnumber"])
						$updatestatement .= "accountnumber = NULL, ";

				}//endif - ENCRYPT_PAYMENT_FIELDS

				if(!$therecord["invoicedate"] || $therecord["invoicedate"] == "0000-00-00"){
					$therecord["invoicedate"] = dateToString(mktime(0,0,0),"SQL");

					$updatestatement .= "
						`invoicedate` = NOW(), ";

				}//end if

				$updatestatement .= "
						`modifiedby` = ".$this->modifiedby.",
						`modifieddate` = NOW()
					WHERE
						`id` = ".$therecord["id"];

				$updateresult = $this->db->query($updatestatement);

				if($therecord["type"] == "receivable") {
					// if type = AR, create AR item

					$arrecord["type"] = "invoice";
					$arrecord["status"] = "open";
					$arrecord["posted"] = 1;
					$arrecord["amount"] = $therecord["totalti"];
					$arrecord["itemdate"] = dateToString(stringToDate($therecord["invoicedate"],"SQL") );
					$arrecord["clientid"] = $therecord["clientid"];
					$arrecord["relatedid"] = $therecord["uuid"];
					$arrecord["uuid"] = "";

					if(!class_exists("phpbmsTable"))
						include("include/tables.php");

					$aritems = new phpbmsTable($this->db, "tbld:c595dbe7-6c77-1e02-5e81-c2e215736e9c");
					$arrecord = $aritems->prepareVariables($arrecord);

					$aritems->insertRecord($arrecord,$this->modifiedby);

				}//endif

				$count++;

			}//endwhile

			$this->updatePostingSession($postsessionid, $count);

			return $count;

		}//end method

	}//end class invoicePost

}//end function

if(class_exists("tablePost")){
	defineInvoicesPost($db);
}
?>
