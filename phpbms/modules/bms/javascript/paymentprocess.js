payment = {
	// Need to set the button object, and the processScript Object, an assoc array 
	// of object to pass to the process script, and a return object to place results
	
	passObjs: null, //associative array (struct) of object of what to pull
	dialogHTML: "",
	listeners: Array(),
	buttonObj: null,
	processScriptObj: null,
	returnObj: null,
	
	initialize: function(buttonObj, processScriptObj, passObjs, returnObj){

		payment.buttonObj = buttonObj;
		payment.processScriptObj = processScriptObj;
		payment.passObjs = passObjs;
		payment.returnObj = returnObj;
		
		connect(payment.buttonObj, "onclick", payment.show);
		
	},//end method
	
	show: function(e){
						
		if(!payment.processScriptObj || !payment.buttonObj || !payment.passObjs || !payment.returnObj)
			return false;
		
		if(payment.buttonObj.className.indexOf("Disabled") != -1)
			return false;
		
		if(!payment.dialogHTML){
			
			theURL = APP_PATH+"modules/bms/paymentprocess_dialog.php";
			loadXMLDoc(theURL,null,false);
			payment.dialogHTML = req.responseText;
			
		}
		
		if(payment.listeners.length > 0)
			for(var i=0; i< payment.listeners.length; i++)
				disconnect(payment.listeners[i]);
		
		payment.listeners = Array();
		
		showModal(payment.dialogHTML,"Online Payment Processing",400,10);
		
		payment.listeners[payment.listeners.length] = connect(getObjectFromID("paymentProcessButton"),"onclick",payment.process);
		payment.listeners[payment.listeners.length] = connect(getObjectFromID("paymentCancelButton"),"onclick",payment.cancel);
		
	},//end method


	process: function(e){

		if(!payment.processScriptObj || !payment.buttonObj || !payment.passObjs || !payment.returnObj)
			return false;
			
		var resultsArea = getObjectFromID("paymentNoticeResults");
		var currentPayment = getObjectFromID("paymentmethodid").value;
		var theURL = APP_PATH + payment.processScriptObj.value + "?";
		var therespond="";
		
		for(key in payment.passObjs)
			theURL += key + "=" + encodeURI(payment.passObjs[key].value) + "&";
		
		var today=new Date();
		theURL+="rand="+today.getTime();
		
		resultsArea.value="Starting Script (this may take a moment)\n";

		loadXMLDoc(theURL,null,false);
		if(req.responseXML){
			var newTransactionid = req.responseXML.documentElement.getElementsByTagName('value')[0].firstChild.data;
			
			if(newShippingAmount==0)
				therespond="Process returned no transaction id."
			else{
				payment.respondObj.value=newTransactionid;
				therespond="Process Succeeded.  Transaction id recorded";
			}
		} else
		therespond = req.responseText;
	
		resultsArea.value+="Script Response:\n"+therespond+"\n";
		
	},//end method
	
	
	cancel: function(e){
		closeModal();
	}//end method
	
}//end class