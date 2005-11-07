<h1 style="clear:both;">Module: Business Management System</h1>
<fieldset>
	<legend>shipping</legend>
	<div class="small important"><em>
		The shipping information below is used when connecting to UPS to calculate shipping costs for product.
		Current tests show that the UPS shipping calculator only works when shipping to and from the Unites States.
	</em></div>
	<label for="sshipping_markup">
		shipping multiplier <em>(markup)</em><br />
		<?PHP field_text("sshipping_markup",$_SESSION["shipping_markup"],0,"","real",Array("size"=>"10","maxlength"=>"10")); ?>
	</label>
	<div class="small">
		<em>Enter the number to multiply the calculated shipping cost. For example to mark up shipping costs by 10%, enter 1.1 </em>
	</div>
	<label for="sshipping_postalcode">
		shipping zip/postal code<br />
		<?PHP field_text("sshipping_postalcode",$_SESSION["shipping_postalcode"],0,"","",Array("size"=>"32","maxlength"=>"128")); ?>
	</label>
</fieldset>
<div style="">
	<fieldset>
		<legend>clients</legend>
		<label for="mngclientemail">
			<input type="button" name="mngclientemail" id="mngclientemail" class="Buttons" value="Manage Client E-Mail Projects" onClick="document.location='../../search.php?id=22'" />
		</label>
	</fieldset>
</div>
<fieldset style="clear:both;margin-bottom:15px;">
	<legend>invoices</legend>
	<label for="invoice_default_printinstruc">default printed instructions<br>
		<textarea id="invoice_default_printinstruc" name="sinvoice_default_printinstruc" cols="60" rows="3" style="width:99%"><?php echo $_SESSION["invoice_default_printinstruc"]?></textarea>
	</label>
</fieldset>