installer = {

	testConnection: function(){

		var response = installer.runCommand("testconnection");
		var testConnectionNoDebug = getObjectFromID("testConnectionNoDebug");

		if(response.success === true){
			testConnectionNoDebug.className = "success"
			testConnectionNoDebug.innerHTML = "Connection Successful";
		} else {
			testConnectionNoDebug.className = "fail"
			testConnectionNoDebug.innerHTML = "Connection Failed";
		}

	},//endfunction testConnection


	createDatabase: function(){

		var response = installer.runCommand("createdatabase");
		var noDebug = getObjectFromID("createDatabaseNoDebug");

		if(response.success === true){

			noDebug.className = "success"
			noDebug.innerHTML = "Database Schema Created";

		} else {

			noDebug.className = "fail"
			noDebug.innerHTML = "Database Schema Creation Failed";

		}//endif

	}, // endfunction createdatabase


	coreDataInstall: function(){

		var noDebug = getObjectFromID("coreDataNoDebug");

		noDebug.className = "running";
		noDebug.innerHTML = "Running...";


		//we pass the entered application name and e-mail address as a "::" separated pair
		var appname = getObjectFromID("appname");
		var email = getObjectFromID("email");

		var extras = encodeURIComponent(appname.value + "::" + email.value)

		var response = installer.runCommand("coredatainstall", extras);

		if(response.success === true){

			noDebug.className = "success";
			noDebug.innerHTML = "Core Data Installed Succefully";

			var pass2 = getObjectFromID("pass2")

			pass2.innerHTML = response.extras;

		} else {

			noDebug.className = "fail";
			noDebug.innerHTML = "Core Data Installation Failed";

		}//endif

	}, // endfunction coreDataInstall


	moduleInstall: function(e){

		var theButton = e.src();

		var foo = "";

		var module = theButton.id.substring(12);

		var noDebug = getObjectFromID("Results"+module);

		noDebug.className = "running";
		noDebug.innerHTML = "Running...";

		var response = installer.runCommand("moduleinstall", module);

		if(response.success === true){

			noDebug.className = "success";
			noDebug.innerHTML = "Module Installed";

		} else {

			noDebug.className = "fail";
			noDebug.innerHTML = "Installation Failed";

		}//endif

	},//end function moduleInstall


	runCommand: function(command, extras){

		if(typeof(extras) == "undefined")
			extras = "";
		else
			extras = "&extras=" + extras

		var theURL = "installajax.php?command=" + command + extras;

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

	}//end function toggleDebug

}//end class installer



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


	}

}

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
	connect(debug, "onchange", installer.toggleDebug);

	var testConnectionButton = getObjectFromID("testConnectionButton");
	connect(testConnectionButton, "onclick", installer.testConnection);

	var createDatabaseButton = getObjectFromID("createDatabaseButton");
	connect(createDatabaseButton, "onclick", installer.createDatabase)

	var coreDataButton = getObjectFromID("coreDataButton");
	connect(coreDataButton, "onclick", installer.coreDataInstall);

	moduleButtons = getElementsByClassName("moduleButtons");
	for(i = 0; i < moduleButtons.length; i++)
		connect(moduleButtons[i], "onclick", installer.moduleInstall);

})
