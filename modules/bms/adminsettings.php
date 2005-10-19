<h1 style="clear:both;">Module: Business Management System</h1>
<div style="clear:both;float:left;width:44%;padding:0px;margin:0px;">
	<fieldset>
		<legend>shipping</legend>
		<label for="sshipping_markup">
			shipping multiplier <em>(markup)</em><br />
			<?PHP field_text("sshipping_markup",$_SESSION["shipping_markup"],1,"Shipping multiplier cannot be blank and must be a valid number.","real",Array("size"=>"10","maxlength"=>"10")); ?>
		</label>
		<div class="small">
			<em>Enter the number to multiply the calculated shipping cost. For example to mark up shipping costs by 10%, enter 1.1 </em>
		</div>
		<label for="sshipping_postalcode">
			shipping zip/postal code<br />
			<?PHP field_text("sshipping_postalcode",$_SESSION["shipping_postalcode"],1,"Zaip/Postal code cannot be blank.","",Array("size"=>"32","maxlength"=>"128")); ?>
		</label>
	</fieldset>
</div>
<div style="padding:0px;margin:0px;margin-left:45%;">
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