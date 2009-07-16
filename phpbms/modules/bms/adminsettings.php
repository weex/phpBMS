<?php
	//if we had specific update code for the module, we would create a class
	//called [module]Update with a method called updateSettings($variables)
	class bmsUpdate{

		function bmsUpdate($db){

			$this->db = $db;

		}//end method


		/**
		 * function isValidPath
		 * @param string $path Absolute Server path
		 * @return boolean True if the path is valid, False if not.
		 */

		function isValidPath($path) {

			$theReturn = false;

			if(is_file($path))
				if(is_readable($path))
					$theReturn = true;

			return $theReturn;

		}//end if --isValidPath--

		/**
		 * function obfuscatePaymentInformation
		 *
		 * obfuscates payment information for invoices.  Process is irreversable
		 * (unlike encryption).
		 */

		function obfuscatePaymentInformation() {

			$querystatment = "
				UPDATE
					`invoices`
				SET
					`ccverification` = REPEAT('*',LENGTH(`ccverification`)),
					`ccexpiration` =  REPEAT('*',LENGTH(`ccexpiration`)),
					`routingnumber` =  NULL,
					`accountnumber` =  NULL,
					`ccnumber` = LPAD(SUBSTRING(`ccnumber`,-4),LENGTH(`ccnumber`),'*')
				WHERE
					`type` != 'Order'
					AND
					`type` != 'Quote'
			";

			$this->db->query($querystatment);

			$querystatment = "
				UPDATE
					`receipts`
				SET
					`ccverification` = REPEAT('*',LENGTH(`ccverification`)),
					`ccexpiration` =  REPEAT('*',LENGTH(`ccexpiration`)),
					`routingnumber` =  NULL,
					`accountnumber` =  NULL,
					`ccnumber` = LPAD(SUBSTRING(`ccnumber`,-4),LENGTH(`ccnumber`),'*')
				WHERE
					`posted` = '1'";

			$this->db->query($querystatment);

		}//end method --obfuscatePaymentInformation--

		/**
		  *  function encryptPaymentInformation
		  *
		  *  Encrypt all payment information in invoices and receipts with
		  *  `type` of Order.  The current key (if any) is used by default.
		  *
		  *  @param string $encryptionKey An overriding encryption key.
		  */

		function encyptPaymentInformation($encryptionKey = NULL){

			if($encryptionKey !== NULL)
				$encryptionKey = mysql_real_escape_string($encryptionKey);

			$querystatment = "
				UPDATE
					`invoices`
				SET
					`ccnumber` = ".$this->db->encrypt("`ccnumber`", $encryptionKey).",
					`ccexpiration` = ".$this->db->encrypt("`ccexpiration`", $encryptionKey).",
					`ccverification` = ".$this->db->encrypt("`ccverification`", $encryptionKey).",
					`accountnumber` = ".$this->db->encrypt("`accountnumber`", $encryptionKey).",
					`routingnumber` = ".$this->db->encrypt("`routingnumber`", $encryptionKey)."
				WHERE
					`type` = 'Order' OR `type` = 'Quote'
			";

			$queryresult = $this->db->query($querystatment);

			$querystatment = "
				UPDATE
					`receipts`
				SET
					`ccnumber` = ".$this->db->encrypt("`ccnumber`", $encryptionKey).",
					`ccexpiration` = ".$this->db->encrypt("`ccexpiration`", $encryptionKey).",
					`ccverification` = ".$this->db->encrypt("`ccverification`", $encryptionKey).",
					`accountnumber` = ".$this->db->encrypt("`accountnumber`", $encryptionKey).",
					`routingnumber` = ".$this->db->encrypt("`routingnumber`", $encryptionKey)."
				WHERE
					`posted` = '0'
			";

			$queryresult = $this->db->query($querystatment);

		}//end method --encryptPaymentInformation--

		/**
		  *  function decryptPaymentInformation
		  *
		  *  Decrypt all payment information in invoices and receipts with
		  *  `type` of Order.  The current key (if any) is used.
		  *
		  *  @param string $encryptionKey An overriding encryption key.
		  */

		function decryptPaymentInformation($encryptionKey = NULL){

			if($encryptionKey !== NULL)
				$encryptionKey = mysql_real_escape_string($encryptionKey);


			$querystatment = "
				UPDATE
					`invoices`
				SET
					`ccnumber` = ".$this->db->decrypt("`ccnumber`", $encryptionKey).",
					`ccexpiration` = ".$this->db->decrypt("`ccexpiration`", $encryptionKey).",
					`ccverification` = ".$this->db->decrypt("`ccverification`", $encryptionKey).",
					`accountnumber` = ".$this->db->decrypt("`accountnumber`", $encryptionKey).",
					`routingnumber` = ".$this->db->decrypt("`routingnumber`", $encryptionKey)."
				WHERE
					`type` = 'Order' OR `type`='Quote'
			";

			$queryresult = $this->db->query($querystatment);

			$querystatment = "
				UPDATE
					`receipts`
				SET
					`ccnumber` = ".$this->db->decrypt("`ccnumber`", $encryptionKey).",
					`ccexpiration` = ".$this->db->decrypt("`ccexpiration`", $encryptionKey).",
					`ccverification` = ".$this->db->decrypt("`ccverification`", $encryptionKey).",
					`accountnumber` = ".$this->db->decrypt("`accountnumber`", $encryptionKey).",
					`routingnumber` = ".$this->db->decrypt("`routingnumber`", $encryptionKey)."
				WHERE
					`posted` = '0'
			";

			$queryresult = $this->db->query($querystatment);

		}

		function updateSettings($variables){

			if(!isset($variables["default_hascredit"]))
				$variables["default_hascredit"] = 0;

			if(!isset($variables["prospects_on_orders"]))
				$variables["prospects_on_orders"] = 0;

			if(!isset($variables["encrypt_payment_fields"]))
				$variables["encrypt_payment_fields"] = 0;

			$variables["default_creditlimit"] = currencyToNumber($variables["default_creditlimit"]);

			//if($variables["encrypt_payment_fields"] && !defined("ENCRYPTION_KEY") && !$variables["encryption_key_path"])
			//	$variables["encrypt_payemnt_fields"] = 0;

			/**
			  *  Need to encrypt/obfuscate if changing from no encryption and
			  *  decrypt if changing from encryption.
			  */
			if($variables["encryptionStatusChanged"]){

				if($variables["encrypt_payment_fields"] == 0){

					if(defined("ENCRYPTION_KEY")){

						$this->decryptPaymentInformation();

						if($variables["encryptionPathChanged"]){

							/**
							  *  If the path has changed, we need to make sure
							  *  that it is a valid one.
							  */

							if(!$this->isValidPath($variables["encryption_key_path"])){
								unset($variables["encryption_key_path"]);
								$this->updateErrorMessage = "Invalid encryption key path";
							}//end if --not valid path?--

						}//end if --path changed--

					}else{

						unset($variables["encrypt_payment_fields"]);
						$this->updateErrorMessage = "ENCRYPTION_KEY undefined";
						if($variables["encryptionPathChanged"])
							if(!$this->isValidPath($variables["encryption_key_path"])){
								unset($variables["encryption_key_path"]);
								$this->updateErrorMessage .= " and the encryption key path is invalid";
							}//end if

					}//end if --existing key?--

				}else{

					if($variables["encryptionPathChanged"]){

						if($this->isValidPath($variables["encryption_key_path"])){

							$res = fopen($variables["encryption_key_path"], "r");
							$key = fread($res, filesize($variables["encryption_key_path"]));
							$key = trim($key);

							$this->encyptPaymentInformation($key);
							$this->obfuscatePaymentInformation();

						}else{

							$this->updateErrorMessage = "Invalid encryption key path";
							unset($variables["encrypt_payment_fields"]);
							unset($variables["encryption_key_path"]);

						}//end if -- valid path?--

					}else{

						if(defined("ENCRYPTION_KEY")){
							$this->encyptPaymentInformation();
							$this->obfuscatePaymentInformation();
						}else{
							$this->updateErrorMessage = "ENCRYPTION_KEY undefined";
							unset($variables["encrypt_payment_fields"]);
						}//end if --encryption key defined--

					}//end if -- path changed?--

				}//end if --encrypt fields?--

			}else{

				if($variables["encryptionPathChanged"]){

					if($this->isValidPath($variables["encryption_key_path"])){

						if($variables["encrypt_payment_fields"]){

							if(defined("ENCRYPTION_KEY")){

								$this->decryptPaymentInformation();

								$res = fopen($variables["encryption_key_path"], "r");
								$key = fread($res, filesize($variables["encryption_key_path"]));
								$key = trim($key);

								$this->encyptPaymentInformation($key);
								$this->obfuscatePaymentInformation();
							}else{
								//new appError(-500, "No existing ENCRYPTION_KEY", "error");
								$this->updateErrorMessage = "ENCRYPTION_KEY undefined";
							}//end if --ENCRYPTION KEY defined--

						}//end if --encrypt fields?--

					}else{
						unset($variables["encryption_key_path"]);
						$this->updateErrorMessage = "Invalid encryption key path";
					}//end if --is valid path--

				}//end if --encryption path changed--

			}//end  if -- status changed--

			return $variables;

		}//end method


	}//end class

	class bmsDisplay{

		function getFields($therecord){

			global $db;

			$theinput = new inputField("shipping_markup",$therecord["shipping_markup"],"shipping markup",false,"real",4,4);
			$fields[] = $theinput;

			$theinput = new inputField("shipping_postalcode",$therecord["shipping_postalcode"],"shipping orginiation zip/postal code",false,NULL,32,128);
			$fields[] = $theinput;

			$theinput = new inputDataTableList($db, "default_payment",$therecord["default_payment"],"paymentmethods","uuid","name",
									"inactive=0 AND (`type` != 'receivable' OR `type` IS NULL)", "priority,name", true, "default payment method", true, "");
			$fields[] = $theinput;

			$theinput = new inputDataTableList($db, "default_shipping",$therecord["default_shipping"],"shippingmethods","uuid","name",
									"inactive=0", "priority,name", true, "default shipping method", true, "");
			$fields[] = $theinput;

			$theinput = new inputDataTableList($db, "default_discount",$therecord["default_discount"],"discounts","uuid","name",
									"inactive=0", "name", true, "default discount", true, "");
			$fields[] = $theinput;

			$theinput = new inputDataTableList($db, "default_taxarea",$therecord["default_taxarea"],"tax","uuid","name",
									"inactive=0", "name", true, "default tax area", true, "");
			$fields[] = $theinput;

			$theinput = new inputBasicList("default_clienttype",$therecord["default_clienttype"],array("prospect"=>"prospect","client"=>"client"), "default type");
			$fields[] = $theinput;

			$theinput = new inputCheckbox("default_hascredit",$therecord["default_hascredit"],"has credit by default");
			$fields[] = $theinput;

			$theinput = new inputCurrency("default_creditlimit", $therecord["default_creditlimit"], "default credit limit");
			$fields[] = $theinput;

			$theinput = new inputField("term1_days",$therecord["term1_days"],"term 1 length",false,"integer",4,4);
			$theinput->setAttribute("class","important");
			$fields[] = $theinput;

			$theinput = new inputField("term1_percentage",$therecord["term1_percentage"],"term 1 percentage",false,"real",4,4);
			$fields[] = $theinput;

			$theinput = new inputField("term2_days",$therecord["term2_days"],"term 2 length",false,"integer",4,4);
			$theinput->setAttribute("class","important");
			$fields[] = $theinput;

			$theinput = new inputField("term2_percentage",$therecord["term2_percentage"],"term 2 percentage",false,"real",4,4);
			$fields[] = $theinput;

			$theinput = new inputField("term3_days",$therecord["term3_days"],"term 3 length",false,"integer",4,4);
			$theinput->setAttribute("class","important");
			$fields[] = $theinput;

			$theinput = new inputField("term3_percentage",$therecord["term3_percentage"],"term 3 percentage",false,"real",4,4);
			$fields[] = $theinput;

			$theinput = new inputCheckbox("prospects_on_orders",$therecord["prospects_on_orders"],"allow prospects on sales orders");
			$fields[] = $theinput;

			$theinput = new inputCheckbox("encrypt_payment_fields", $therecord["encrypt_payment_fields"], "encrypt/obfuscate payment information");
			$fields[] = $theinput;

			$theinput = new inputField("encryption_key_path", $therecord["encryption_key_path"], "absolute server path to a file containing the encryption key", false, NULL, 64);
			$fields[] = $theinput;

			return $fields;
		}

		function display($theform,$therecord){
?>
<div class="moduleTab" title="clients">
<fieldset>
	<legend>clients</legend>

	<p><?php echo $theform->showField("default_clienttype");?></p>

	<p><?php echo $theform->showField("default_hascredit");?></p>

	<p><?php echo $theform->showField("default_creditlimit");?></p>

    </fieldset>
    <p class="updateButtonP"><button type="button" class="Buttons UpdateButtons">save</button></p>
</div>

<div class="moduleTab" title="sales orders">
    <fieldset>
	<legend>sales orders</legend>

	<p><?php $theform->showField("prospects_on_orders");?></p>

	<p>
		<label for="invoice_default_printinstruc">default printed instructions</label><br/>
		<textarea id="invoice_default_printinstruc" name="invoice_default_printinstruc" cols="60" rows="3" ><?php echo $therecord["invoice_default_printinstruc"]?></textarea>
	</p>

	<p><?php $theform->showField("default_payment");?></p>

	<p><?php $theform->showField("default_shipping");?></p>

	<p><?php $theform->showField("default_discount");?></p>

	<p><?php $theform->showField("default_taxarea");?></p>

    </fieldset>
    <p class="updateButtonP"><button type="button" class="Buttons UpdateButtons">save</button></p>
</div>

<div class="moduleTab" title="shipping">
    <fieldset>
	<legend>shipping</legend>
	<p class="notes">
            <strong>Note:</strong> The shipping information below is used when connecting to
            UPS to calculate shipping costs for product.  Current tests show that the UPS
            shipping calculator only works when shipping to and from the Unites States.
	</p>

	<p><?php $theform->showField("shipping_markup");?></p>

        <p class="notes"><strong>Note:</strong> Enter the number to multiply the calculated shipping cost. For example to mark up shipping costs by 10%, enter 1.1</p>

	<p><?php $theform->showField("shipping_postalcode");?></p>

    </fieldset>
    <p class="updateButtonP"><button type="button" class="Buttons UpdateButtons">save</button></p>
</div>

<div class="moduleTab" title="accounts receivable">
    <fieldset>
	<legend>Accounts Receivable</legend>

	<p><?php echo $theform->showField("term1_days");?> days</p>

	<p><?php echo $theform->showField("term1_percentage");?> %</p>

	<p><?php echo $theform->showField("term2_days");?> days</p>

	<p><?php echo $theform->showField("term2_percentage");?> %</p>

	<p><?php echo $theform->showField("term3_days");?> days</p>

	<p><?php echo $theform->showField("term3_percentage");?> %</p>

    </fieldset>
    <p class="updateButtonP"><button type="button" class="Buttons UpdateButtons">save</button></p>
</div>

<div class="moduleTab" title="payment information encryption">
    <fieldset>
	<legend>Payment Information Encryption</legend>

	<p class="notes">
		Changing these settings can potentially take a while as they update
		most or all records in sales orders and receipts.
	</p>


	<input type="hidden" name="encryptionStatusChanged" id="encryptionStatusChanged" value="0" />
	<p><?php echo $theform->showField("encrypt_payment_fields");?></p>
	<p class="notes">
		Withe this option enabled, sensitive payment information in sales orders and
		receipts with a `type` of 'Order' will be encrypted.<br/>

		Also, payment fields in sales orders and receipts records of `type`
		'Invoice' will be removed or obfuscated when a sales order or receipt
		is posted as an invoice or voided/deleted.  <strong>This type of obfuscation,
		is not reversible</strong>

	</p>

	<input type="hidden" name="encryptionPathChanged" id="encryptionPathChanged" value="0" />
	<p><?php echo $theform->showField("encryption_key_path");?></p>

    </fieldset>
    <p class="updateButtonP"><button type="button" class="Buttons UpdateButtons">save</button></p>
</div>

<?php
		}//end method
	}//end class
?>
