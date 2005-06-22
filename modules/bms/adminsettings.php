		<h1>Business Management System Settings </h1>
		<h2>Shipping Settings</h2>
		<div>
			shipping markup<br>
			<input name="sshipping_markup" type="text" size="10" maxlength="10" value="<?php echo $shipping_markup ?>">
		</div>
		<div>
			shipping zip/postal code<br>
			<input name="sshipping_postalcode" type="text" size="10" maxlength="10" value="<?php echo $shipping_postalcode ?>">
		</div>
		

		<h2>Invoice Settings</h2>
		<div>default printed instructions<br>
			<textarea name="sinvoice_default_printinstruc" cols="60" rows="3" style="width:100%"><?php echo $invoice_default_printinstruc?></textarea>
		</div>
		
		<h2>Client Settings</h2>
		<div>manage saved client e-mail projects<br>
			<input type="button" name="mngclientemail" id="mngclientemail" class="Buttons" value="Manage Client E-Mail Projects" onClick="document.location='../../search.php?id=22'">
		</div>
		