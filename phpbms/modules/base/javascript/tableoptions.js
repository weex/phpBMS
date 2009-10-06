tableOptions = {

	switchType: function(e){

		var type = 	getObjectFromID("type");
		var ifDiv =	getObjectFromID("ifDiv");
		var acDiv =	getObjectFromID("acDiv");
		var acNote = getObjectFromID("acNote");
		var apiNote = getObjectFromID("apiNote");

		switch(type.value){

			case "0":
				ifDiv.style.display = "block";
				acDiv.style.display = "none";
				apiNote.style.display = "none";
				acNote.style.display = "none";
				break;

			case "1":
				ifDiv.style.display = "none";
				acDiv.style.display = "block";
				apiNote.style.display = "none";
				acNote.style.display = "block";
				break;

			case "2":
				ifDiv.style.display = "none";
				acDiv.style.display = "block";
				apiNote.style.display = "block";
				acNote.style.display = "none";
				break;
		}

	},//end method


	edit: function(e){

		tableOptions._tableButtonClick(e, "edit")

	}, //end method


	del: function(e){

		tableOptions._tableButtonClick(e, "delete")

	},//end method


	_tableButtonClick: function(e, thecommand){

		var id = 		getObjectFromID("id");
		var command = 	getObjectFromID("command");

		var button = e.src();

		var theid = button.id.substr(3);

		id.value = theid;
		command.value = thecommand;

		tableOptions.submitForm(e);

	}, //end method


	submitForm: function(e){

		var command = getObjectFromID("command");
		var theform = getObjectFromID("record")

		if(command.value == "add" || command.value == "update"){

			if(!validateForm(theform)){
				if(e)
					e.stop();
				return false;
			}//endif

		}//end if

		theform.submit();

	}, //end method


	cancel: function(e){

		var command = 	getObjectFromID("command");

		command.value = "cancel";

		tableOptions.submitForm(e);

	}//end method

}//end class

/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

	var type = getObjectFromID("type")
	connect(type, "onchange", tableOptions.switchType);

	var theForm = getObjectFromID("record");
	connect(type, "onsubmit", function(e){e.stop();});

	var delButtons = getElementsByClassName("buttonDelete");
	for(var i=0; i < delButtons.length; i++)
		connect(delButtons[i], "onclick", tableOptions.del);

	var editButtons = getElementsByClassName("buttonEdit");
	for(var i=0; i < editButtons.length; i++)
		connect(editButtons[i], "onclick", tableOptions.edit);

	var cancelButton = getObjectFromID("cancel");
	if(cancelButton)
		connect(cancelButton, "onclick", tableOptions.cancel);

	var saveButton = getObjectFromID("save");
	connect(saveButton, "onclick", tableOptions.submitForm);

	tableOptions.switchType();

});//end connect