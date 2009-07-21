window.onload = function(){

	var reccurcheck = getObjectFromID("recurr");

	if(reccurcheck){
		var recurrDivs = new Array();
		recurrDivs[recurrDivs.length]=getObjectFromID("recurrDetails");

		var checkLinks = new Array();
		checkLinks[checkLinks.length] = reccurcheck;

		var sqlAccordion = new fx.Accordion(checkLinks, recurrDivs, {opacity: true, duration:250, onComplete:function(){/* function goes here */}});

		var theid = getObjectFromID("id");
		if(theid.value)

		reccurcheck.click();
		changeType();
		changeEnd();
	}

}


function changeType(){
	var dropDown = getObjectFromID("type");
	var i;

	for(i=0;i<dropDown.options.length;i++){
		var theDiv = getObjectFromID(dropDown.options[i].value+"Div");
		if(dropDown.options[i].selected)
			theDiv.style.display = "block";
		else
			theDiv.style.display = "none";
	}

	var typetext = getObjectFromID("typeText");
	switch(dropDown.value){
		case "Daily":
			typetext.innerHTML = "day(s)";
		break;
		case "Weekly":
			typetext.innerHTML = "week(s) on:";
		break;
		case "Monthly":
			typetext.innerHTML = "month(s)";
		break;
		case "Yearly":
			typetext.innerHTML = "year(s) in:";
		break;

	}

}//end function


function daySelect(thebutton){
	if(thebutton.className == "Buttons")
		thebutton.className = "pressedButtons";
	else{
		var noneSelected = true;
		var i;
		var tempButton;

		for(i=1;i<=7;i++){
			tempButton = getObjectFromID("dayOption"+i);
			if(tempButton.className == "pressedButtons" && tempButton != thebutton){
				noneSelected = false;
				break;
			}
		}//end for

		if(!noneSelected)
			thebutton.className = "Buttons";
	}
}


function monthDaySelect(thebutton){
	if(thebutton.className == "Buttons monthDays")
		thebutton.className = "pressedButtons monthDays";
	else{
		var noneSelected = true;
		var i;
		var tempButton;

		for(i=1;i<=31;i++){
			tempButton = getObjectFromID("monthDayOption"+i);
			if(tempButton.className == "pressedButtons monthDays" && tempButton != thebutton){
				noneSelected = false;
				break;
			}
		}//end for

		if(!noneSelected)
			thebutton.className = "Buttons monthDays";
	}
}

function monthlyChange(){

	var firstRadio = getObjectFromID("monthlyEach");
	var dayButton;
	var ontheday = getObjectFromID("monthlyontheday");
	var ontheweek = getObjectFromID("monthlyontheweek");
	var i;

	if(firstRadio.checked){
		//enable each day button
		for(i=1; i<32; i++){
			dayButton = getObjectFromID("monthDayOption"+i);
			dayButton.disabled = false;
		}
		//disable onthe buttons.
		ontheday.disabled = true;
		ontheweek.disabled = true;
	} else {
		//disable each day button
		for(i=1; i<32; i++){
			dayButton = getObjectFromID("monthDayOption"+i);
			dayButton.disabled = true;
		}

		//enable onthe buttons.
		ontheday.disabled = false;
		ontheweek.disabled = false;
	}

}


function yearlyMonthSelect(thebutton){
	if(thebutton.className == "Buttons yearlyMonths")
		thebutton.className = "pressedButtons yearlyMonths";
	else{
		var noneSelected = true;
		var i;
		var tempButton;

		for(i=1;i<=12;i++){
			tempButton = getObjectFromID("yearlyMonthOption"+i);
			if(tempButton.className == "pressedButtons yearlyMonths" && tempButton != thebutton){
				noneSelected = false;
				break;
			}
		}//end for

		if(!noneSelected)
			thebutton.className = "Buttons yearlyMonths";
	}
}


function yearlyOnTheChecked(){
	var thecheck = getObjectFromID("yearlyOnThe");
	var ontheday = getObjectFromID("yearlyontheday");
	var ontheweek = getObjectFromID("yearlyontheweek");

	ontheday.disabled = !thecheck.checked;
	ontheweek.disabled = !thecheck.checked;
}


function changeEnd(){
	var theselect = getObjectFromID("end");
	var afterSpan = getObjectFromID("afterSpan");
	var ondatespan = getObjectFromID("ondateSpan");

	switch(theselect.value){
		case "never":
			afterSpan.style.display = "none";
			ondatespan.style.display = "none";
		break;
		case "after":
			afterSpan.style.display = "inline";
			ondatespan.style.display = "none";
		break;
		case "on date":
			afterSpan.style.display = "none";
			ondatespan.style.display = "inline";
		break;
	}
}


function submitForm(command){

	var thecommand = getObjectFromID("command");
	if(!command)
		command = "cancel";

	thecommand.value = command;

	if(command == "cancel"){
		var referrer = getObjectFromID("referrer");
		document.location = referrer.value;
		return false;
	}

	var theform = getObjectFromID("record");
	if(!validateForm(theform))
		return false;

	var typeSelect = getObjectFromID("type");
	var tempButton;
	var eachlistArray = Array();
	var i;

	//first let's set the eachlist if necassary
	switch(typeSelect.value){
		case "Weekly":
			for(i=1; i<=7; i++){
				tempButton = getObjectFromID("dayOption"+i);
				if(tempButton.className == "pressedButtons")
					eachlistArray[eachlistArray.length] = tempButton.value;
			}
		break;

		case "Monthly":
			var monthlyEach = getObjectFromID("monthlyEach");
			if(monthlyEach.checked){
				for(i=1; i<=31; i++){
					tempButton = getObjectFromID("monthDayOption"+i);
					if(tempButton.className == "pressedButtons monthDays")
						eachlistArray[eachlistArray.length] = tempButton.value;
				}
			}
		break;

		case "Yearly":
			for(i=1; i<=12; i++){
				tempButton = getObjectFromID("yearlyMonthOption"+i);
				if(tempButton.className == "pressedButtons yearlyMonths")
					eachlistArray[eachlistArray.length] = tempButton.value;
			}
		break;
	}//end switch

	if(eachlistArray.length > 0){
		var tempeachlist = "";
		for(i=0; i < eachlistArray.length; i++)
			tempeachlist += eachlistArray[i]+"::";
		tempeachlist = tempeachlist.substr(0,tempeachlist.length-2);

		var eachlist = getObjectFromID("eachlist");
		eachlist.value = tempeachlist
	}

	thecommand.form.submit();

}