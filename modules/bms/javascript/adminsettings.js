bmsSettings ={

	hasCreditCheck: function(e){
		var checkbox = getObjectFromID("default_hascredit");
		var creditlimit = getObjectFromID("default_creditlimit");

		if(checkbox.checked){
			creditlimit.readOnly = false;
			creditlimit.className = "";
		} else {
			creditlimit.readOnly = true;
			creditlimit.className = "disabledtext";
		}//endif
	},//end method

	toggleStatusChanged: function(){

		var encryptionStatusChanged = getObjectFromID("encryptionStatusChanged");

		if(encryptionStatusChanged.value == 0 || encryptionStatusChanged.value == "0"){

			encryptionStatusChanged.value = 1;

		}else
			encryptionStatusChanged.value = 0;

	},//end method

	setPathChanged: function(){

		var encryptionPathChanged = getObjectFromID("encryptionPathChanged");

		encryptionPathChanged.value = 1;

	}//end method

}// end class


/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

	bmsSettings.hasCreditCheck()

	var checkbox = getObjectFromID("default_hascredit");
	connect(checkbox, "onclick", bmsSettings.hasCreditCheck);

	var encrypt_payment_fields = getObjectFromID("encrypt_payment_fields");
	connect(encrypt_payment_fields, "onchange", bmsSettings.toggleStatusChanged);


	var encryption_key_path = getObjectFromID("encryption_key_path");
	connect(encryption_key_path, "onchange", bmsSettings.setPathChanged);
});