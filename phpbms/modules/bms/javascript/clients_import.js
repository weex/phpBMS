clientImport = {
	
	switchInfo: function(){
		
		var importType = getObjectFromID("importType");
		var infoClass = getElementsByClassName("info");
		
		for(i = 0; i < infoClass.length; i++){
			
			if(infoClass[i].id == "info"+importType.value)
				infoClass[i].style.display = "block";
			else
				infoClass[i].style.display = "none";
			
		}//end for
		
	}//end method
	
}//end class

/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {
	
	clientImport.switchInfo();
	
	var importType = getObjectFromID("importType");
	connect(importType, "onchange", clientImport.switchInfo);
})