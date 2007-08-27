<?php 
	//if we had specific update code for the module, we would create a class
	//called [module]Update with a method called updateSettings($variables)

	class bmsDisplay{
	
		function getFields($therecord){
			
			global $db;
			
			$theinput = new inputField("shipping_markup",$therecord["shipping_markup"],"shipping markup",false,"real",4,4);
			$fields[] = $theinput;
			
			$theinput = new inputField("shipping_postalcode",$therecord["shipping_postalcode"],"shipping orginiation zip/postal code",false,NULL,32,128);
			$fields[] = $theinput;

			$theinput = new inputDataTableList($db, "default_payment",$therecord["default_payment"],"paymentmethods","id","name",
									"inactive=0", "priority,name", true, "default payment method");
			$fields[] = $theinput;
	
			$theinput = new inputDataTableList($db, "default_shipping",$therecord["default_shipping"],"shippingmethods","id","name",
									"inactive=0", "priority,name", true, "default shipping method");
			$fields[] = $theinput;
			
			$theinput = new inputDataTableList($db, "default_discount",$therecord["default_discount"],"discounts","id","name",
									"inactive=0", "name", true, "default discount");
			$fields[] = $theinput;
	
			$theinput = new inputDataTableList($db, "default_taxarea",$therecord["default_taxarea"],"tax","id","name",
									"inactive=0", "name", true, "default tax area");
			$fields[] = $theinput;


		
			return $fields;
		}
		
		function display($theform,$therecord){
?>
<h1 class="newModule">Module: Business Management System</h1>
<fieldset>
	<legend>shipping</legend>
	<p class="notes"><br />
		<strong>Note:</strong> The shipping information below is used when connecting to <br />
		UPS to calculate shipping costs for product.  Current tests show that the UPS <br />
		shipping calculator only works when shipping to and from the Unites States.<br />
	</p>
	
	<p>
		<?php $theform->showField("shipping_markup");?>
		<br />
		<span class="notes"><strong>Note:</strong> Enter the number to multiply the calculated shipping cost. <br />
		For example to mark up shipping costs by 10%, enter 1.1</span>
	</p>
	
	<p>
		<?php $theform->showField("shipping_postalcode");?>
	</p>
</fieldset>

<fieldset>
	<legend>invoices</legend>
	<p>
		<label for="invoice_default_printinstruc">default printed instructions</label><br/>
		<textarea id="invoice_default_printinstruc" name="invoice_default_printinstruc" cols="60" rows="3" ><?php echo $therecord["invoice_default_printinstruc"]?></textarea>
	</p>
	<p><?php $theform->showField("default_payment");?></p>

	<p><?php $theform->showField("default_shipping");?></p>

	<p><?php $theform->showField("default_discount");?></p>

	<p><?php $theform->showField("default_taxarea");?></p>
</fieldset>
<?php
		}
	}
?>
