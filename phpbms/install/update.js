updater = {

	coreDataUpdate: function(){

		var noDebug = getObjectFromID("coreDataNoDebug");

		noDebug.className = "running";
		noDebug.innerHTML = "Running...";

		var response = updater.runCommand("coredataupdate");

		if(response.success === true){

			noDebug.className = "success";
			noDebug.innerHTML = "Core Program Updated Succefully";

		} else {

			noDebug.className = "fail";
			noDebug.innerHTML = "Core Program Update Failed";

		}//endif

	}, // endfunction coreDataInstall


	moduleUpdate: function(e){

		var theButton = e.src();

		var foo = "";

		var module = theButton.id.substring(12);

		var noDebug = getObjectFromID("Results" + module);

		noDebug.className = "running";
		noDebug.innerHTML = "Running...";

		var response = updater.runCommand("moduleupdate", module);

		if(response.success === true){

			noDebug.className = "success";
			noDebug.innerHTML = "Update Successful";

		} else {

			noDebug.className = "fail";
			noDebug.innerHTML = "Update Failed";

		}//endif

	},//end function moduleInstall


	runCommand: function(command, extras){

		if(typeof(extras) == "undefined")
			extras = "";
		else
			extras = "&extras=" + extras

		var theURL = "updateajax.php?command=" + command + extras;

		loadXMLDoc(theURL,null,false);

		var JSONresponse;
		eval("JSONresponse = (" + req.responseText +")");

		var responseText = getObjectFromID(command + "results");
		if(typeof(responseText) != "undefined"){

			if(responseText.value)
				responseText.value += "\n";

			responseText.value += JSONresponse.details;

		}//endif

		return JSONresponse

	},//endfunction runCommand


	toggleDebug: function(){

		var debug = getObjectFromID("debug");
		var display = "none";
		if(debug.checked)
			display = "block";

		var debugDisplays = getElementsByClassName("debugResults");

		for(var i = 0; i < debugDisplays.length; i ++)
			debugDisplays[i].style.display = display;

	},//end function toggleDebug

        /**
         * Special v0.98 Update
         */
        generateUUIDs: function(){

		var noDebug = getObjectFromID("updateUUIDsNoDebug");

		noDebug.className = "running";
		noDebug.innerHTML = "Running...";

		var theURL = "generateuuids.php";

		loadXMLDoc(theURL,null,false);

		var JSONresponse;
		eval("JSONresponse = (" + req.responseText +")");

		var responseText = getObjectFromID("updateUUIDsResult");
		if(typeof(responseText) != "undefined"){

			if(responseText.value)
				responseText.value += "\n";

			responseText.value += JSONresponse.details;

		}//endif

		if(JSONresponse.success === true){

			noDebug.className = "success";
			noDebug.innerHTML = "UUID Generation Successful";

		} else {

			noDebug.className = "fail";
			noDebug.innerHTML = "UUID Generation Failed!";

		}//endif

        }//endfunction generateUUIDs

}//end class updater



stepsNav = {

	currentSection: 1,
	sections: null,

	navNext: function(){

		if(stepsNav.currentSection + 1 <= stepsNav.sections.length){
			stepsNav.navTo(stepsNav.currentSection + 1);
		}
	},


	navPrev: function(){
		if(stepsNav.currentSection - 1 > 0)
			stepsNav.navTo(stepsNav.currentSection - 1);
	},


	navTo: function(section){

		for(var i=0; i< stepsNav.sections.length; i++){

			if(stepsNav.sections[i].id != "step" + section){
				stepsNav.sections[i].style.display = "none";
			} else {
				stepsNav.sections[i].style.display = "block";
			}//endif

		}//endfor

		navBar = getObjectFromID("navSelect");
		for(i=0; i < navBar.options.length; i++){
			if(navBar.options[i].value == section){
				navBar.options[i].selected = true;
			}
		}//endfor
		//navBar.selectedIndex = section

		stepsNav.currentSection = section;

	},//end function navTo


	navLeft: function(){

		var navSelect = getObjectFromID("navSelect");
		stepsNav.navTo(parseInt(navSelect.value));


	}//endfunction navLeft

}//end class stepsNav

// ====== Init Listeners =======================================================

connect(window,"onload",function() {

	stepsNav.sections = getElementsByClassName("steps");
	stepsNav.navTo(1);

	var nextButtons = getElementsByClassName("nextButtons");
	for(var i=0; i< nextButtons.length; i++)
		connect(nextButtons[i], "onclick", stepsNav.navNext);

	var prevButtons = getElementsByClassName("prevButtons");
	for(var i=0; i< prevButtons.length; i++)
		connect(prevButtons[i], "onclick", stepsNav.navPrev);

	var navSelect = getObjectFromID("navSelect");
	connect(navSelect, "onchange", stepsNav.navLeft);

	var debug = getObjectFromID("debug");
	connect(debug, "onchange", updater.toggleDebug);

	var updatecoreButton = getObjectFromID("updatecoreButton");
	if(updatecoreButton)
		connect(updatecoreButton,"onclick", updater.coreDataUpdate);


        //special v0.98 generator
        var updateUUIDsButton = getObjectFromID("updateUUIDsButton");
                connect(updateUUIDsButton, "onclick", updater.generateUUIDs);

	moduleButtons = getElementsByClassName("moduleButtons");
	for(i = 0; i < moduleButtons.length; i++)
		connect(moduleButtons[i], "onclick", updater.moduleUpdate);

})
