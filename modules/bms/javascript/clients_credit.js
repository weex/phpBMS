clientCredit = {
	
	checkCredit: function(e){
		
		var checkbox = getObjectFromID("hascredit");
		var creditlimit = getObjectFromID("creditlimit");
		
		creditlimit.readOnly = !checkbox.checked;

	},//end method


	submitForm: function(e){
		
		theForm = getObjectFromID("record");
		
		if(validateForm(theForm))
			theForm.submit();
			
	},//end method
	
	
	updateLimit: function(e){
		
		var creditLimit = getObjectFromID("creditlimit");
		var outstanding = getObjectFromID("outstanding");
		var creditLeft = getObjectFromID("creditleft");
		
		creditLeft.value = numberToCurrency( currencyToNumber(creditLimit.value) - outstanding.value );
		
	}//end method
	
}//end class


/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

	var update1 = getObjectFromID("update1");
	var update2 = getObjectFromID("update2");
	
	connect(update1,"onclick",clientCredit.submitForm);
	connect(update2,"onclick",clientCredit.submitForm);
	
	var theForm = getObjectFromID("record")
	connect(theForm,"onsubmit",function(e){e.stop();});
	
	var hascredit = getObjectFromID("hascredit");
	connect(hascredit,"onclick",clientCredit.checkCredit);
	
	var creditLimit = getObjectFromID("creditlimit");
	connect(creditLimit,"onchange",clientCredit.updateLimit)
	
	clientCredit.checkCredit();
})