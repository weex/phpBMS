payment = {
	// Need to set the button object, and the processScript Object, an assoc array
	// of object to pass to the process script, and a return object to place results

	passObjs: null, //associative array (struct) of object of what to pull
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

            var dialog = '<p>Payment processing requires saving of order.</p>';
            dialog +=   '<p align="right"><button id="paymentSaveAndProcessButton" class="Buttons" type="button">save and process</button> <button id="paymentCancelButton" class="Buttons" style="width: 75px;" type="button">cancel</button></p>'

            if(payment.listeners.length > 0)
                for(var i=0; i< payment.listeners.length; i++)
                    disconnect(payment.listeners[i]);

            payment.listeners = Array();

            showModal(dialog, "Online Payment Processing",400,10);

            payment.listeners[payment.listeners.length] = connect(getObjectFromID("paymentSaveAndProcessButton"), "onclick", payment.saveAndProcess);
            payment.listeners[payment.listeners.length] = connect(getObjectFromID("paymentCancelButton"), "onclick", payment.cancel);

            return false;

	},//end method


        saveAndProcess: function(e){

            var setProcessing = getObjectFromID("saveandprocess");
            setProcessing.value = payment.processScriptObj.value;

            var saveButton = getObjectFromID("saveButton1");

            saveButton.click();

        },//end function saveAndProcess


	process: function(){

            theURL = APP_PATH+"modules/bms/paymentprocess_dialog.php";
            loadXMLDoc(theURL,null,false);
            var dialogHTML = req.responseText;

            showModal(dialogHTML, "Online Payment Processing",400,10);

            connect(getObjectFromID("ppProcessButton"), "onclick", payment.performProcess);
            connect(getObjectFromID("ppCancelButton"), "onclick", payment.cancel);

	},//end method


        performProcess: function(){

            var theURL = APP_PATH + getObjectFromID("processPayment").value;
            var resultsArea = getObjectFromID("paymentNoticeResults");
            var processButton = getObjectFromID("ppProcessButton")

            processButton.disabled = true;
            processButton.className = "disabledButtons";

            var uuid = getObjectFromID("uuid");

            theURL += "?soid=" + encodeURIComponent(uuid.value);


            resultsArea.value  = "Begin Processing...\n";

            loadXMLDoc(theURL,null,false);


            var response = eval("(" + req.responseText + ")");

            if(response.result){

                switch(response.result){

                    case "success":

                        var transacionid = getObjectFromID("transactionid");
                        transacionid.value = response.transactionid;

                        var ppCancelButton = getObjectFromID("ppCancelButton");
                        ppCancelButton.innerHTML = "done";

                        resultsArea.value += "Payment Processed Successfully\nTransaction ID recorded as: " + response.transactionid;
                        break;

                    case "declined":

                        resultsArea.value += "Payment Processed Returned Declined \nDetails are as follows:\n\n" + response.details + "\n\n";

                        processButton.disabled = false;
                        processButton.className = "Buttons";

                        break;

                    case "error":

                        resultsArea.value += "**Payment Processing Returned Errors** \nDetails are as follows:\n\n" + response.details + "\n\n";

                        processButton.disabled = false;
                        processButton.className = "Buttons";

                        break;

                    default:

                        resultsArea.value += "** Serious Errors Occurred ** \nDetails are as follows:\n\n" + req.responseText + "\n\n";

                        processButton.disabled = false;
                        processButton.className = "Buttons";

                }//endswitch

            } else {

                resultsArea.value += "Error in processing script \n" + req.responseText + "\n\n";
                processButton.disabled = false;
                processButton.className = "Buttons";

            }//endif

        }, //end function performProcess


	cancel: function(e){

	    closeModal();

	}//end method

}//end class
