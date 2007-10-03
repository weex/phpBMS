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
	}//end method
	
}// end class


/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {
	
	bmsSettings.hasCreditCheck()

	checkbox = getObjectFromID("default_hascredit");
	connect(checkbox, "onclick", bmsSettings.hasCreditCheck);
	
});