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

		if(printStatements.value == 1){

			theURL = APP_PATH+"modules/bms/report/aritems_clientstatement.php?cmd=print&sd="+encodeURIComponent(agingdate.value)+"&ts="+ts+"&ext=.pdf";
			window.open(theURL, 'phpBMSprint1');

		}//endif

		if(printSummary.value == 1){

			theURL = APP_PATH+"modules/bms/report/aritems_summary.php?cmd=print&sd="+encodeURIComponent(agingdate.value)+"&ts="+ts;
			window.open(theURL, 'phpBMSprint2');

		}//endif

	}//end if

})
