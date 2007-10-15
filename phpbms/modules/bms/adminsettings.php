<?php 
	//if we had specific update code for the module, we would create a class
	//called [module]Update with a method called updateSettings($variables)
	class bmsUpdate{
	
		function bmsUpdate($db){
			
			$this->db = $db;
			
		}//end method
		
		
		
		function updateSettings($variables){
		
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

			$theinput = new inputDataTableList($db, "default_payment",$therecord["default_payment"],"paymentmethods","id","name",
									"inactive=0 AND `type` != 'receivable'", "priority,name", true, "default payment method");
			$fields[] = $theinput;
	
			$theinput = new inputDataTableList($db, "default_shipping",$therecord["default_shipping"],"shippingmethods","id","name",
									"`inactive`=0", "priority,name", true, "default shipping method");
			$fields[] = $theinput;
			
			$theinput = new inputDataTableList($db, "default_discount",$therecord["default_discount"],"discounts","id","name",
									"inactive=0", "name", true, "default discount");
			$fields[] = $theinput;
	
			$theinput = new inputDataTableList($db, "default_taxarea",$therecord["default_taxarea"],"tax","id","name",
									"inactive=0", "name", true, "default tax area");
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
<p class="updateButtonP"><input name="command" type="submit" class="Buttons" value="update settings" /></p>

<fieldset>
	<legend>sales orders</legend>
	<p>
		<label for="invoice_default_printinstruc">default printed instructions</label><br/>
		<textarea id="invoice_default_printinstruc" name="invoice_default_printinstruc" cols="60" rows="3" ><?php echo $therecord["invoice_default_printinstruc"]?></textarea>
	</p>
	<p><?php $theform->showField("default_payment");?></p>

	<p><?php $theform->showField("default_shipping");?></p>

	<p><?php $theform->showField("default_discount");?></p>

	<p><?php $theform->showField("default_taxarea");?></p>
</fieldset>
<p class="updateButtonP"><input name="command" type="submit" class="Buttons" value="update settings" /></p>

<fieldset>
	<legend>clients</legend>
	
	<p><?php echo $theform->showField("default_clienttype");?></p>

	<p><?php echo $theform->showField("default_hascredit");?></p>

	<p><?php echo $theform->showField("default_creditlimit");?></p>

</fieldset>
<p class="updateButtonP"><input name="command" type="submit" class="Buttons" value="update settings" /></p>

<fieldset>
	<legend>Accounts Receivable</legend>
	
	<p><?php echo $theform->showField("term1_days");?> days</p>

	<p><?php echo $theform->showField("term1_percentage");?> %</p>

	<p><?php echo $theform->showField("term2_days");?> days</p>

	<p><?php echo $theform->showField("term2_percentage");?> %</p>

	<p><?php echo $theform->showField("term3_days");?> days</p>

	<p><?php echo $theform->showField("term3_percentage");?> %</p>

</fieldset>
<p class="updateButtonP"><input name="command" type="submit" class="Buttons" value="update settings" /></p>

<?php
		}//end method
	}//end class
?>
