aging = {

	submitForm: function(e){

		var thebutton = e.src();
		var theform = getObjectFromID("record");

		var command = getObjectFromID("command");

		if(thebutton.id == "runButton"){

			if(!validateForm(theform))
				return false;

			command.value = "run";

		} else
			command.value = "cancel";

		theform.submit();

		return true;

	}//end method

}//end class


/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */

connect(window,"onload",function() {

	var runButton = getObjectFromID("runButton");
	if(runButton)
		connect(runButton, "onclick", aging.submitForm);

	var cancelButton = getObjectFromID("cancelButton");
	if(cancelButton)
		connect(cancelButton, "onclick", aging.submitForm);

	var printStatements = getObjectFromID("printClientStatements");
	if(printStatements){

		var theURL;
		var printSummary = getObjectFromID("printSummary");
		var agingdate = getObjectFromID("agingdate")

		var ts = new Date();
		ts = ts.getTime();

                var tid = "tbld:c595dbe7-6c77-1e02-5e81-c2e215736e9c";
                var clientStatementUUID = "rpt:0df82ecf-5f05-56bd-18c3-e7cb27c0cf8a";
                var summaryUUID = "rpt:e25bdb7a-93be-b1d6-a292-cdec89c0c9fc";

		if(printStatements.value == 1){

			theURL = APP_PATH + "modules/bms/report/aritems_clientstatement.php?cmd=print";
                        theURL += "&sd=" + encodeURIComponent(agingdate.value);
                        theURL += "&tid=" + encodeURIComponent(tid);
                        theURL += "&rid=" + encodeURIComponent(clientStatementUUID);
                        theURL += "&ts=" + encodeURIComponent(ts);
                        theURL += "&ext=.pdf";
			window.open(theURL, 'phpBMSprint1');

		}//endif

		if(printSummary.value == 1){

			theURL = APP_PATH+"modules/bms/report/aritems_summary.php?cmd=print"
                        theURL += "&sd=" + encodeURIComponent(agingdate.value);
                        theURL += "&tid=" + encodeURIComponent(tid);
                        theURL += "&rid=" + encodeURIComponent(summaryUUID);
                        theURL += "&ts=" + encodeURIComponent(ts);
			window.open(theURL, 'phpBMSprint2');

		}//endif

	}//end if

})
