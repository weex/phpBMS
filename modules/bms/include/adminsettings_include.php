<?php
	if ($invoice_default_printinstruc!=$sinvoice_default_printinstruc){
		$writesettings[]=Array(
			"name"=>"invoice_default_printinstruc",
			"value"=>$sinvoice_default_printinstruc
		);		
		$invoice_default_printinstruc=$sinvoice_default_printinstruc;
	}

	if ($shipping_markup!=$sshipping_markup){
		$writesettings[]=Array(
			"name"=>"shipping_markup",
			"value"=>$sshipping_markup
		);		
		$shipping_markup=$sshipping_markup;
	}



	if ($shipping_postalcode!=$sshipping_postalcode){
		$writesettings[]=Array(
			"name"=>"shipping_postalcode",
			"value"=>$sshipping_postalcode
		);		
		$shipping_postalcode=$sshipping_postalcode;
	}
?>