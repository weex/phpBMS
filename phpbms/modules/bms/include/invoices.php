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

		var $availableClientIDs = array();
		var $availableUserIDs = array();
		var $availableStatusIDs = array();

		function showClientType($id){

			if(((int) $id) != 0){

				$querystatement="
					SELECT
						type
					FROM
						clients
					WHERE
						id=".((int) $id);

				$therecord = $this->db->fetchArray($this->db->query($querystatement));

			} else {

				$therecord["type"] = "client";

			}//endif id

			?><input type="hidden" id="clienttype" name="clienttype"  <?php if($id) echo 'value="'.$therecord["type"].'"'?> /><?php

		}//end method


		function updateStatus($invoiceid,$statusid,$statusdate,$assignedtoid){

			$querystatement="DELETE FROM invoicestatushistory WHERE invoiceid=".$invoiceid." AND invoicestatusid=".$statusid;
			$queryresult=$this->db->query($querystatement);

			$querystatement="INSERT INTO invoicestatushistory (invoiceid,invoicestatusid,statusdate,assignedtoid) values(";
			$querystatement.=((int) $invoiceid).", ";
			$querystatement.=((int) $statusid).", ";

			if($statusdate=="" || $statusdate=="0/0/0000") $tempdate="NULL";
			else{
				$tempdate="\"".sqlDateFromString($statusdate)."\"";
			}
			$querystatement.=$tempdate.",";
			if($assignedtoid=="")
				$querystatement.="NULL";
			else
				$querystatement.=((int) $assignedtoid);

			$querystatement.=")";

			$queryresult=$this->db->query($querystatement);
		}


		function showPaymentSelect($id,$paymentMethods){
			?><select name="paymentmethodid" id="paymentmethodid" onchange="showPaymentOptions()">
				<option value="0" <?php if($id==0) echo "selected=\"selected\""?>>&lt;none&gt;</option>
			<?php foreach($paymentMethods as $method){?>
				<option value="<?php echo $method["id"]?>" <?php if($id==$method["id"]) echo "selected=\"selected\""?>><?php echo $method["name"]?></option>
			<?php } ?>
			</select>
			<?php
		}


		function getDefaultStatus(){

			$querystatement="SELECT id FROM invoicestatuses WHERE invoicedefault=1";
			$queryresult=$this->db->query($querystatement);

			$therecord=$this->db->fetchArray($queryresult);

			return $therecord["id"];
		}


		function getStatuses($statusid){

			global $phpbms;

			$querystatement="
				SELECT
					invoicestatuses.id,
					invoicestatuses.name,
					invoicestatuses.invoicedefault,
					invoicestatuses.setreadytopost,
					invoicestatuses.defaultassignedtoid,
					users.firstname,
					users.lastname
				FROM
					(invoicestatuses LEFT JOIN users ON invoicestatuses.defaultassignedtoid=users.id)
				WHERE
					invoicestatuses.inactive=0
					OR invoicestatuses.id =".((int) $statusid)."
				ORDER BY
					invoicestatuses.priority,
					invoicestatuses.name";

			$queryresult=$this->db->query($querystatement);

			$thereturn = array();

			$phpbms->topJS[] = 'statuses=Array();';


			while($therecord = $this->db->fetchArray($queryresult)){

				$thereturn[] = $therecord;

				$phpbms->topJS[] = 'statuses['.$therecord["id"].']=Array();';
				$phpbms->topJS[] = 'statuses['.$therecord["id"].']["name"]="'.htmlQuotes($therecord["name"]).'";';
				$phpbms->topJS[] = 'statuses['.$therecord["id"].']["setreadytopost"]='.$therecord["setreadytopost"].';';
				$phpbms->topJS[] = 'statuses['.$therecord["id"].']["userid"]="'.htmlQuotes($therecord["defaultassignedtoid"]).'";';
				$phpbms->topJS[] = 'statuses['.$therecord["id"].']["firstname"]="'.htmlQuotes($therecord["firstname"]).'";';
				$phpbms->topJS[] = 'statuses['.$therecord["id"].']["lastname"]="'.htmlQuotes($therecord["lastname"]).'";';

			}//endwhile

			return $thereturn;

		}//end function


		function displayStatusDropDown($statusid,$statuses){

			?><select id="statusid" name="statusid" class="important">
				<?php

				foreach($statuses as $therecord){
					?><option value="<?php echo $therecord["id"]?>" <?php if($statusid==$therecord["id"]) echo "selected=\"selected\""?>><?php echo $therecord["name"]?></option><?php
				}//endforeach

				?>
			</select><?php

		}//end method


		function getPayments($paymentmethodid){

			$querystatement="
				SELECT
					id,
					name,
					type,
					onlineprocess,
					processscript
				FROM
					paymentmethods
				WHERE
					inactive=0
					OR id=".((int) $paymentmethodid)."
				ORDER BY
					priority,
					name";

			$queryresult=$this->db->query($querystatement);

			$thereturn = array();

			global $phpbms;

			$phpbms->topJS[] = 'paymentMethods=Array();';

			while($therecord = $this->db->fetchArray($queryresult)) {
				$thereturn[$therecord["id"]]=$therecord;

				$phpbms->topJS[] = 'paymentMethods['.$therecord["id"].']=Array();';
				$phpbms->topJS[] = 'paymentMethods['.$therecord["id"].']["name"]="'.htmlQuotes($therecord["name"]).'";';
				$phpbms->topJS[] = 'paymentMethods['.$therecord["id"].']["type"]="'.htmlQuotes($therecord["type"]).'";';
				$phpbms->topJS[] = 'paymentMethods['.$therecord["id"].']["onlineprocess"]="'.htmlQuotes($therecord["onlineprocess"]).'";';
				$phpbms->topJS[] = 'paymentMethods['.$therecord["id"].']["processscript"]="'.htmlQuotes($therecord["processscript"]).'";';
			}

			return $thereturn;

		}//end function


		function showShippingSelect($id,$shippingMethods){

			?><select name="shippingmethodid" id="shippingmethodid" onchange="changeShipping()">
				<option value="0" <?php if($id==0) echo "selected=\"selected\""?>>&lt;none&gt;</option>
			<?php foreach($shippingMethods as $method){?>
				<option value="<?php echo $method["id"]?>" <?php if($id==$method["id"]) echo "selected=\"selected\""?>><?php echo $method["name"]?></option>
			<?php } ?>
			</select>
			<?php

		}//end function


		function getShipping($shippingmethodid){

			$querystatement="
				SELECT
					id,
					name,
					canestimate,
					estimationscript
				FROM
					shippingmethods
				WHERE
					inactive=0
					OR id=".((int) $shippingmethodid)."
				ORDER BY
					priority,
					name";

			$queryresult=$this->db->query($querystatement);

			$thereturn=array();

			global $phpbms;

			$phpbms->topJS[] = 'shippingMethods=Array();';

			while($therecord = $this->db->fetchArray($queryresult)) {
				$thereturn[$therecord["id"]]=$therecord;

				$phpbms->topJS[] = 'shippingMethods['.$therecord["id"].']=Array();';
				$phpbms->topJS[] = 'shippingMethods['.$therecord["id"].']["name"]="'.htmlQuotes($therecord["name"]).'";';
				$phpbms->topJS[] = 'shippingMethods['.$therecord["id"].']["canestimate"]='.((int) $therecord["canestimate"]).';';
				$phpbms->topJS[] = 'shippingMethods['.$therecord["id"].']["estimationscript"]="'.htmlQuotes($therecord["estimationscript"]).'";';
			}

			return $thereturn;

		}//end function


		function showTaxSelect($id){

			$id=(int) $id;
			$querystatement="SELECT id,name,percentage FROM tax WHERE inactive=0 OR id=".$id." ORDER BY name";
			$queryresult=$this->db->query($querystatement);

			?><select name="taxareaid" id="taxareaid" onchange="getPercentage()" size="5">
				<option value="0" <?php if($id==0) echo "selected=\"selected\""?>>&lt;none&gt;</option>
				<?php
					while($therecord = $this->db->fetchArray($queryresult)){
						?><option value="<?php echo $therecord["id"]?>" <?php if($id==$therecord["id"]) echo "selected=\"selected\""?>><?php echo $therecord["name"].": ".$therecord["percentage"]."%"?></option><?php
					}
				?>
			</select><?php

		}//end function


		function showDiscountSelect($id){

			$id=(int) $id;
			$querystatement="SELECT id,name,type,value FROM discounts WHERE inactive!=1 ORDER BY name";
			$queryresult=$this->db->query($querystatement);

			?><select name="discountid" id="discountid" size="8">
				<option value="0" <?php if($id==0) echo 'selected="selected"'?>>&lt;none&gt;</option>
				<?php
					while($therecord=$this->db->fetchArray($queryresult)){
						if($therecord["type"]=="amount")
							$therecord["value"]=numberToCurrency($therecord["value"]);
						else
							$therecord["value"].="%";
						?><option value="<?php echo $therecord["id"]?>" <?php if($id==$therecord["id"]) echo "selected=\"selected\""?>><?php echo $therecord["name"].": ".$therecord["value"]?></option><?php
					}
				?>
			</select><?php

		}//end function


		function getDiscount($id){

			$therecord["name"]="";
			$therecord["value"]=0;

			if(((int) $id)!=0){
				$querystatement="SELECT name,type,value FROM discounts WHERE id=".$id;
				$queryresult=$this->db->query($querystatement);

				$therecord=$this->db->fetchArray($queryresult);
				if($therecord["type"]!="amount"){
					$therecord["value"].="%";
					$therecord["name"].=": ".$therecord["value"];
				} else
					$therecord["name"].=": ".numberToCurrency($therecord["value"]);
			}

			$therecord["name"]=htmlQuotes($therecord["name"]);

			return $therecord;

		}//end function


		function getTax($id){

			$therecord["name"]="";

			if(((int) $id)!=0){

				$querystatement="SELECT name,percentage FROM tax WHERE id=".$id;
				$queryresult=$this->db->query($querystatement);

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
					id=".$clientid;

			$this->db->query($updatestatement);

		}//end function

		// CLASS OVERRIDES ======================================================================================

		function getDefaults(){
			$therecord = parent::getDefaults();

			if(isset($_GET["cid"]))
				$therecord["clientid"] = $_GET["cid"];
			else
				$therecord["clientid"]="";

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
			return $therecord;
		}


		function getRecord($id){

			$therecord = parent::getRecord($id);

			$discountinfo=$this->getDiscount($therecord["discountid"]);
			$therecord["discountname"]=$discountinfo["name"];
			$therecord["discount"]=$discountinfo["value"];

			$taxinfo = $this->getTax($therecord["taxareaid"]);
			$therecord["taxname"]=$taxinfo["name"];

			$therecord["amountdue"] = $therecord["totalti"] - $therecord["amountpaid"];

			$querystatement = "SELECT hascredit, creditlimit FROM clients WHERE id=".$therecord["clientid"];
			$queryresult = $this->db->query($querystatement);

			$therecord = array_merge($this->db->fetchArray($queryresult), $therecord);

			if($therecord["hascredit"]){
				$querystatement = "SELECT SUM(`amount` - `paid`) AS amtopen FROM aritems WHERE `status` = 'open' AND clientid=".$therecord["clientid"]." AND posted=1";
				$queryresult = $this->db->query($querystatement);

				$arrecord = $this->db->fetchArray($queryresult);

				$therecord["creditleft"] = $therecord["creditlimit"] - $arrecord["amtopen"];
			} else
				$therecord["creditleft"] =0;

			return $therecord;
		}

		/*------[verification related functions]---------------------------*/

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
			}else{
				$this->availableClientIDs[] = "none";
			}//end if

		}//end method --populateClientArray--


		function populateUserArray(){

			$this->availableUserIDs = array();

			$querystatement = "
				SELECT
					`id`
				FROM
					`users`;
				";

			$queryresult = $this->db->query($querystatement);

			$this->availableUserIDs[] = 0;//for none

			while($therecord = $this->db->fetchArray($queryresult))
				$this->availableUserIDs[] = $therecord["id"];

		}//end method  --populateUserArray--


		function populateInvoiceStatusArray(){

			$this->availableStatusIDs = array();

			$querystatement = "
				SELECT
					`id`
				FROM
					`invoicestatuses`;
				";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){
				while($therecord = $this->db->fetchArray($queryresult))
					$this->availableStatusIDs[] = $therecord["id"];
			}else{
				$this->availableStatusIDs[] = "none";
			}//end if

		}//end method --populateInvoiceStatusArray--


		function verifyVariables($variables){

			//must have a client
			if(isset($variables["clientid"])){

				//must be numeric and positive
				if(!$variables["clientid"] || (int)$variables["clientid"] > 0){

					if(!count($this->availableClientIDs))
						$this->populateClientArray();

					if(!in_array(((int)$variables["clientid"]),$this->availableClientIDs))
						$this->verifyErrors[] = "The `clientid` field does not give an existing/acceptable client id number.";
				}else
					$this->verifyErrors[] = "The `clientid` field must be a non-negative number or equivalent to 0.";

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

				//assignedtoid needs to be a non-negative number or equivalent to 0
				if( !$variables["assignedtoid"] || ((int)$variables["assignedtoid"]) > 0 ){

					if(!count($this->availableUserIDs))
						$this->populateUserArray();

					if(!in_array(((int)$variables["assignedtoid"]),$this->availableUserIDs))
						$this->verifyErrors[] = "The `assignedtoid` field does not give an existing/acceptable user id number.";
				}else
					$this->verifyErrors[] = "The `assignedtoid` field must be a non-negative number or equivalent to 0.";

			}//end if

			//check status id
			if(isset($variables["statusid"])){

				//assignedtoid needs to be a non-negative number or equivalent to 0
				if( !$variables["statusid"] || ((int)$variables["statusid"]) > 0 ){

					if(!count($this->availableStatusIDs))
						$this->populateInvoiceStatusArray();

					if(!in_array(((int)$variables["statusid"]),$this->availableStatusIDs))
						$this->verifyErrors[] = "The `statusid` field does not give an existing/acceptable status id number.";
				}else
					$this->verifyErrors[] = "The `statusid` field must be a non-negative number or equivalent to 0.";

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
			//check addresss ids
			//check secondary line item ids

			return parent::verifyVariables($variables);

		}//end method


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

			return $variables;
		}

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

						$address = new addresses($this->db, 306);

						$addressRecord = $address->getRecord($variables[$idprefix."addressid"]);

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
					$addresstorecord = new addresstorecord($this->db,306);

					if($varprefix == "shipto")
						$addressRecord["shiptoname"] = $variables["shiptoname"];
					$atrRecord["address1"] = $variables[$varprefix."address1"];
					$atrRecord["address2"] = $variables[$varprefix."address2"];
					$atrRecord["city"] = $variables[$varprefix."city"];
					$atrRecord["state"] = $variables[$varprefix."state"];
					$atrRecord["postalcode"] = $variables[$varprefix."postalcode"];
					$atrRecord["country"] = $variables[$varprefix."country"];
					$atrRecord["recordid"] = $variables["clientid"];
					$atrRecord["tabledefid"] = 2;
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
							".$idprefix."addressid = ".$atrRecord["id"]."
						WHERE
							id = ".$invoiceid;

					$this->db->query($updatestatement);

					break;

			}//endswitch - saveoptions

		}//end method - addressUpdate


		function updateRecord($variables, $modifiedby = NULL){

			//if($modifiedby === NULL)
			//	$modifiedby = $_SESSION["userinfo"]["id"];


			if($variables["oldType"]=="Invoice")
				return false;

			//$variables = $this->prepareVariables($variables);

			if(!hasRights(20)){

				unset($this->fields["paymentmethodid"]);
				unset($this->fields["checkno"]);
				unset($this->fields["bankname"]);
				unset($this->fields["ccnumber"]);
				unset($this->fields["ccexpiration"]);
				unset($this->fields["accountnumber"]);
				unset($this->fields["routingnumber"]);
				unset($this->fields["transactionid"]);

			}//endif

			if(parent::updateRecord($variables, $modifiedby)){

				if($variables["lineitemschanged"]==1){

					$lineitems = new lineitems($this->db, $variables["id"]);
					$lineitems->set($variables["thelineitems"], $modifiedby);

				}//endif

				if($variables["statuschanged"]==1)
					$this->updateStatus($variables["id"],$variables["statusid"],$variables["statusdate"],$variables["assignedtoid"]);

				// Check to see if we need to update/create the client addresses from the
				// billing address
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
		}



		function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false){

			if($createdby === NULL)
				$createdby = $_SESSION["userinfo"]["id"];

			//$variables = $this->prepareVariables($variables);

			$newid = parent::insertRecord($variables, $createdby, $overrideID, $replace);

			if($variables["billingsaveoptions"] != "orderOnly" || $variables["shiptosaveoptions"] != "orderOnly"){

				require_once("addresses.php");
				require_once("addresstorecord.php");

				$this->addressUpdate($variables, $newid, $createdby, "billing");
				$this->addressUpdate($variables, $newid, $createdby, "shipping");

			}//end if

			if($variables["lineitemschanged"]==1){

				$lineitems = new lineitems($this->db, $newid);
				$lineitems->set($variables["thelineitems"],$createdby);

			}//end if

			if($variables["statuschanged"]==1)
				$this->updateStatus($newid,$variables["statusid"],$variables["statusdate"],$variables["assignedtoid"]);

			if($variables["clienttype"] == "prospect" && $variables["type"] == "Order")
				$this->prospectToClient($variables["clientid"]);

			return $newid;

		}//end method - insertRecord

	}//end class


	// LINE ITEMS CLASS
	// ==============================================================================================
	class lineitems{

		var $queryresult = NULL;

		function lineitems($db, $invoiceid, $invoicetype = "Order"){

			$this->db = $db;
			$this->invoiceid = ((int) $invoiceid);
			$this->invoicetype = $invoicetype;

		}//end method


		function get(){

			$querystatement = "
				SELECT
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
					lineitems LEFT JOIN products ON lineitems.productid = products.id
				WHERE
					invoiceid = ".$this->invoiceid."
				ORDER BY
					lineitems.displayorder";

			$this->queryresult = $this->db->query($querystatement);

		}//end method


		function show(){

			if($this->queryresult === NULL)
				$this->get();

			$count = 1;
			while($therecord = $this->db->fetchArray($this->queryresult)){

				?><tr id="li<?php echo $count?>" class="lineitems">

					<td colspan="2" class="lineitemsLeft" <?php if($this->invoicetype == "Void" || $this->invoicetype == "Invoice") echo 'nowrap="nowrap"'?>>
						<input type="hidden" id="li<?php echo $count?>ProductID" value="<?php echo $therecord["productid"]?>"/>
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

			}//endwhile

		}//end method


		function set($itemlist, $userid = NULL){

			if(!$userid)
				$userid = $_SESSION["userinfo"]["id"];

			$deletestatement = "
				DELETE FROM
					lineitems
				WHERE
					invoiceid = ".$this->invoiceid;

			$this->db->query($deletestatement);

			$itemsArray = explode(";;", $itemlist);

			$count = 0;

			foreach($itemsArray as $item){

				$itemRecord = explode("::", $item);
				if(count($itemRecord) > 1){

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
							".((int) $itemRecord[0]).",
							'".mysql_real_escape_string($itemRecord[1])."',
							".((int) $itemRecord[2]).",
							".((real) $itemRecord[3]).",
							".((real) $itemRecord[4]).",
							".((real) $itemRecord[5]).",
							".((real) $itemRecord[6]).",
							".$count.",
							".$userid.",
							NOW(),
							".$userid.",
							NOW()
						)";

					$this->db->query($insertstatement);

					$count++;

				}//end if

			}//endforeach

		}//end method

	}//end class

}// end if


if(class_exists("searchFunctions")){
	class invoicesSearchFunctions extends searchFunctions{

		function email_invoice(){

				if(DEMO_ENABLED == "true")
					return "Functionality disabled in demo.";

				$this->db->setEncoding("latin1");

				include("modules/bms/report/invoices_pdf_class.php");

				$processed = 0;

				foreach($this->idsArray as $id){

					$report = new invoicePDF($this->db, 'P', 'in', 'Letter');

					$report->generate("invoices.id = ".$id);

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


		function email_quote(){

				if(DEMO_ENABLED == "true")
					return "Functionality disabled in demo.";

				$this->db->setEncoding("latin1");

				$noOutput = true;
				include("modules/bms/report/invoices_pdf_class.php");
				include("modules/bms/report/invoices_pdfquote.php");

				$processed = 0;

				foreach($this->idsArray as $id){

					$report = new quotePDF($this->db, 'P', 'in', 'Letter');

					$report->generate("invoices.id = ".$id);

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


		function _mark_as_status($statusid){

			//Look up shippings defaults
			$querystatement="SELECT defaultassignedtoid,setreadytopost FROM invoicestatuses WHERE id = ".$statusid;
			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){
				$therecord = $this->db->fetchArray($queryresult);

				if($therecord["defaultassignedtoid"]!="")
					$assignedtoid = $therecord["defaultassignedtoid"];
				else
					$assignedtoid="NULL";

				$readytopost = $therecord["setreadytopost"];
			} else {
				return "No status with id ".$statusid." found.";
			}

			$whereclause=$this->buildWhereClause();
			$whereclause="(".$whereclause.") AND invoices.type!='Invoice' AND invoices.type!='VOID' AND invoices.statusid !=".$statusid;

			// since marking RTP is dependent on the payment method type,
			// items must be updated individually
			$querystatement = "
				SELECT
					invoices.id,
					paymentmethods.type,
					invoices.invoicedate
				FROM
					invoices LEFT JOIN paymentmethods ON invoices.paymentmethodid = paymentmethods.id
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
						assignedtoid=".$assignedtoid.",
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
						invoices.statusid=".$statusid.",
						modifieddate=NOW()
					WHERE
						id =".$therecord["id"];

				$updateresult = $this->db->query($updatestatement);

				//delete conlflicting history
				$querystatement="DELETE FROM invoicestatushistory WHERE invoiceid=".$therecord["id"]." AND invoicestatusid=".$statusid;
				$deleteresult = $this->db->query($querystatement);

				//insert new history
				$querystatement="INSERT INTO invoicestatushistory (invoiceid,invoicestatusid,statusdate,assignedtoid) values (";
				$querystatement.=$therecord["id"].",".$statusid.",NOW(),";
				$querystatement.=$assignedtoid;
				$querystatement.=")";
				$insertresult = $this->db->query($querystatement);

				$count++;

			}//endwhile

			$message = $this->buildStatusMessage($count);

			return $message;

		}//end private method


		function mark_ashipped(){

			$statusid = 4; //The default id for "shipped";

			$message = $this->_mark_as_status($statusid);

			return $message." marked as shipped.";

		}//end method


		function mark_rtp(){

			$whereclause=$this->buildWhereClause();
			$whereclause="(".$whereclause.") AND invoices.type='Order'";

			// since marking RTP is dependent on the payment method type,
			// items must be updated individually
			$querystatement = "
				SELECT
					invoices.id,
					paymentmethods.type,
					invoices.invoicedate
				FROM
					invoices LEFT JOIN paymentmethods ON invoices.paymentmethodid = paymentmethods.id
				WHERE
					".$whereclause;

			$queryresult = $this->db->query($querystatement);

			$count=0;
			while($therecord = $this->db->fetchArray($queryresult)){
				$updatestatement ="
					UPDATE
						invoices
					SET
						modifiedby=".$_SESSION["userinfo"]["id"].", ";

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


		function mark_aspaid(){

			$whereclause = $this->buildWhereClause();
			$whereclause = trim("
				invoices.type='Order'
				AND paymentmethods.type != 'receivable'
				AND (".$whereclause.")");

			$querystatement ="
				SELECT
					invoices.id
				FROM
					invoices LEFT JOIN paymentmethods ON invoices.paymentmethodid = paymentmethods.id
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


		function mark_asinvoice(){

			$whereclause = $this->buildWhereClause();

			include_once("include/post_class.php");
			defineInvoicesPost();

			$invoicesPost = new invoicesPost($this->db);

			$count = $invoicesPost->post($whereclause);

			$message = $this->buildStatusMessage($count);
			$message .= " posted as invoice";

			return $message;
		}//end method


		function delete_record(){

			//passed variable is array of user ids to be revoked
			$whereclause = $this->buildWhereClause();

			$querystatement = "
				UPDATE
					invoices
				SET
					invoices.type='VOID',";

			if(CLEAR_PAYMENT_ON_INVOICE){
				$querystatement .="
					ccverification = REPEAT('*',LENGTH(ccverification)),
					ccexpiration =  REPEAT('*',LENGTH(ccexpiration)),
					routingnumber =  NULL,
					accountnumber =  NULL,
					ccnumber = LPAD(SUBSTRING(ccnumber,-4),LENGTH(ccnumber),'*'),
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
		}

	}//end class
}//end if


function obfuscatePayment($variables){

	$variables["ccverification"] = str_repeat("*",strlen($variables["ccverification"]));
	$variables["ccexpiration"] = str_repeat("*",strlen($variables["ccexpiration"]));
	$variables["routingnumber"] = "NULL";
	$variables["accountnumber"] = "NULL";
	$variables["ccnumber"] = str_repeat("*",strlen($variables["ccnumber"] -4 )).substr($variables["ccnumber"], -4);

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
					invoices.clientid,
					invoices.totalti,
					invoices.invoicedate,
					paymentmethods.type,
					invoices.ccnumber,
					invoices.ccexpiration,
					invoices.ccverification,
					invoices.routingnumber,
					invoices.accountnumber
				FROM
					invoices LEFT JOIN paymentmethods ON invoices.paymentmethodid = paymentmethods.id
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

				if(CLEAR_PAYMENT_ON_INVOICE){

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

				}//endif - CLEAR_PAYMENT_ON_INVOICE

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
					$arrecord["relatedid"] = $therecord["id"];

					if(!class_exists("phpbmsTable"))
						include("include/tables.php");

					$aritems = new phpbmsTable($this->db, 303);

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
