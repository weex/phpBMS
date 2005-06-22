<?php
	if ($_SESSION["invoice_default_printinstruc"]!=$_POST["sinvoice_default_printinstruc"]){
		$writesettings[]=Array(
			"name"=>"invoice_default_printinstruc",
			"value"=>$_POST["sinvoice_default_printinstruc"]
		);		
		$_SESSION["invoice_default_printinstruc"]=$_POST["sinvoice_default_printinstruc"];
	}

	if ($_SESSION["shipping_markup"]!=$_POST["sshipping_markup"]){
		$writesettings[]=Array(
			"name"=>"shipping_markup",
			"value"=>$_POST["sshipping_markup"]
		);		
		$_SESSION["shipping_markup"]=$_POST["sshipping_markup"];
	}



	if ($_SESSION["shipping_postalcode"]!=$_POST["sshipping_postalcode"]){
		$writesettings[]=Array(
			"name"=>"shipping_postalcode",
			"value"=>$_POST["sshipping_postalcode"]
		);		
		$_SESSION["shipping_postalcode"]=$_POST["sshipping_postalcode"];
	}
?>