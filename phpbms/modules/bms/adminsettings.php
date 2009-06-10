<?php
	//if we had specific update code for the module, we would create a class
	//called [module]Update with a method called updateSettings($variables)
	class bmsUpdate{

		function bmsUpdate($db){

			$this->db = $db;

		}//end method



		function updateSettings($variables){

			if(!isset($variables["default_hascredit"]))
				$variables["default_hascredit"] = 0;

			if(!isset($variables["prospects_on_orders"]))
				$variables["prospects_on_orders"] = 0;

			if(!isset($variables["clear_payment_on_invoice"]))
				$variables["clear_payment_on_invoice"] = 0;

			$variables["default_creditlimit"] = currencyToNumber($variables["default_creditlimit"]);
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

			$theinput = new inputCheckbox("clear_payment_on_invoice",$therecord["clear_payment_on_invoice"],"clear payment information when posting");
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

	<p><?php $theform->showField("clear_payment_on_invoice");?></p>
	<p class="notes">
		With this option enabled, sensitive payment information will be removed or obfuscated when
		a sales order or receipt is posted as an invoice or voided/deleted.
	</p>

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

<?php
		}//end method
	}//end class
?>
