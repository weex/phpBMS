<h1 class="newModule">Module: Business Management System</h1>
<fieldset>
	<legend>shipping</legend>
	<p class="notes"><br />
		<strong>Note:</strong> The shipping information below is used when connecting to <br />
		UPS to calculate shipping costs for product.  Current tests show that the UPS <br />
		shipping calculator only works when shipping to and from the Unites States.<br />
	</p>
	
	<p>
		<label for="sshipping_markup">markup</label><br />
		<?PHP field_text("sshipping_markup",$_SESSION["shipping_markup"],0,"","real",Array("size"=>"10","maxlength"=>"10")); ?><br>
		<span class="notes"><strong>Note:</strong> Enter the number to multiply the calculated shipping cost. <br>
		For example to mark up shipping costs by 10%, enter 1.1</span>
	</p>
	
	<p>
		<label for="sshipping_postalcode">shipping orginiation zip/postal code</label><br />
		<?PHP field_text("sshipping_postalcode",$_SESSION["shipping_postalcode"],0,"","",Array("size"=>"32","maxlength"=>"128")); ?>
	</p>
</fieldset>

<fieldset>
	<legend>clients</legend>
	<p><br/>
		<input type="button" name="mngclientemail" id="mngclientemail" class="Buttons" value="Manage Client E-Mail Projects" onClick="document.location='../../search.php?id=22'" />
	</p>
</fieldset>

<fieldset>
	<legend>invoices</legend>
	<p>
		<label for="invoice_default_printinstruc">default printed instructions</label><br/>
		<textarea id="invoice_default_printinstruc" name="sinvoice_default_printinstruc" cols="60" rows="3" ><?php echo $_SESSION["invoice_default_printinstruc"]?></textarea>
	</p>
</fieldset>