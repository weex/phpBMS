clientStatements ={
	
	submitForm: function(e){
		
		theform = getObjectFromID("record");
		
		
		
		if(validateForm(theform)){
			
			var command = getObjectFromID("command")
			command.value = "print";
			
			theform.submit();
			
		}//edn if
		
	}//end method
	
}//end class

/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {
								 
	
	var printButton = getObjectFromID("printButton");
	if(printButton)
		connect(printButton, "onclick", clientStatements.submitForm);
				
	var cancelButton = getObjectFromID("cancelButton");
	if(cancelButton)
		connect(cancelButton, "onclick", function(e){ window.close() });
})