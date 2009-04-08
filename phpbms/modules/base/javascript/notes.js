/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2007, Kreotek LLC                                  |
 | All rights reserved.                                                    |
 +-------------------------------------------------------------------------+
 |                                                                         |
 | Redistribution and use in source and binary forms, with or without      |
 | modification, are permitted provided that the following conditions are  |
 | met:                                                                    |
 |                                                                         |
 | - Redistributions of source code must retain the above copyright        |
 |   notice, this list of conditions and the following disclaimer.         |
 |                                                                         |
 | - Redistributions in binary form must reproduce the above copyright     |
 |   notice, this list of conditions and the following disclaimer in the   |
 |   documentation and/or other materials provided with the distribution.  |
 |                                                                         |
 | - Neither the name of Kreotek LLC nor the names of its contributore may |
 |   be used to endorse or promote products derived from this software     |
 |   without specific prior written permission.                            |
 |                                                                         |
 | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS     |
 | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT       |
 | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A |
 | PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
 | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,   |
 | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT        |
 | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,   |
 | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY   |
 | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT     |
 | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE   |
 | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.    |
 |                                                                         |
 +-------------------------------------------------------------------------+
*/

function returnFalse(){
	return false;
}


function changeType(){
	var thetype=getObjectFromID("thetype");

	var thestatus=getObjectFromID("thestatus");
	var endtext=getObjectFromID("endtext");
	var datediv=getObjectFromID("thedates");
	var comptext=getObjectFromID("completedtext");
	var compdiv=getObjectFromID("thecompleted");
	var startcheck=getObjectFromID("startcheck");
	var endcheck=getObjectFromID("endcheck");
	var repeatdiv = getObjectFromID("repeatDiv");
	var parentid=getObjectFromID("parentid");
	var prviatecheck=getObjectFromID("private");
	var theid=getObjectFromID("id");

	switch(thetype.value){
		case "NT":
			datediv.style.display="none";
			compdiv.style.display="block";
			comptext.innerHTML="read";
			thestatus.style.display="none";
			repeatdiv.style.display="none";
			prviatecheck.disabled=false;
		break;

		case "TS":
			datediv.style.display="block";
			compdiv.style.display="block";
			comptext.innerHTML="completed";
			startcheck.disabled=false;
			endcheck.disabled=false;
			endtext.innerHTML="due date"
			thestatus.style.display="none";
			repeatdiv.style.display="block";
			prviatecheck.disabled=false;
			if(!theid.value) prviatecheck.checked=true;
		break;

		case "EV":
			datediv.style.display="block";
			compdiv.style.display="block";
			comptext.innerHTML="done";
			startcheck.checked=true;
			endcheck.checked=true;
			startcheck.disabled=true;
			endcheck.disabled=true;
			endtext.innerHTML="end"
			thestatus.style.display="block";
			repeatdiv.style.display="block";
			prviatecheck.disabled=false;
			if(!theid.value) prviatecheck.checked=true;
		break;

		case "SM":
			thestatus.style.display="none";
			datediv.style.display="none";
			compdiv.style.display="none";
			repeatdiv.style.display="none";
			prviatecheck.disabled=true;
			if(!theid.value) prviatecheck.checked=false;
		break;
	}

	var rightside=getObjectFromID("rightSideDiv");
	var timestamp=getObjectFromID("timeStampP");
	var content=getObjectFromID("content");
	var newHeight=rightside.offsetHeight - timestamp.offsetHeight - 61;

	content.style.height=newHeight+"px"

	dateChecked("start");
	dateChecked("end");

}

function completedCheck(){
	var checkbox=getObjectFromID("completed");
	var completedDate=getObjectFromID("completeddate");
	var completedDateButton=getObjectFromID("completeddateButton");
	if(checkbox.checked){
		completedDate.readOnly = false;
		completedDate.className=null;
		completedDateButton.onclick=CDBOC;
		if(!completedDate.value){
			var today=new Date();
			completedDate.value=dateToString(today);
		}
	} else {
		completedDate.readOnly = true;
		completedDate.className="uneditable";
		completedDateButton.onclick=returnFalse;
	}
}

function checkEndDate(){
	var endEnabled=getObjectFromID("endcheck");
	if(endEnabled.checked){
		var startdatefield=getObjectFromID("startdate");
		var enddatefield=getObjectFromID("enddate");
		var endtimefield=getObjectFromID("endtime");
		var starttimefield=getObjectFromID("starttime");
		var thestart=stringToDatetime(startdatefield.value,starttimefield.value);
		var theend=stringToDatetime(enddatefield.value,endtimefield.value);
		if (thestart>theend){
			theend=thestart;
			theend.setHours(theend.getHours()+1);
			enddatefield.value=dateToString(theend);
			if(starttimefield.value)
				endtimefield.value=timeToString(theend);
		}
	}
}

function dateChecked(type){
	var checkbox=getObjectFromID(type+"check");

	var thedate=getObjectFromID(type+"date");
	var thetime=getObjectFromID(type+"time");
	var thedateButton=getObjectFromID(type+"dateButton");
	var thetimeButton=getObjectFromID(type+"timeButton");
	var thetext=getObjectFromID(type+"text");
	var repeat=getObjectFromID("repeating");

	if(!checkbox.checked){
		if(type=="start" && repeat.checked){
			alert("Repeatable tasks must have a start date.");
			checkbox.checked=true;
			return false;
		}
		thetext.className="disabledtext";
		thedate.value="";
		thetime.value="";
		thedate.setAttribute("readonly","readonly");
		thetime.setAttribute("readonly","readonly");
		thedate.className="uneditable";
		thetime.className="uneditable";
		thetimeButton.onclick=returnFalse;
		thedateButton.onclick=returnFalse;
	} else {

		if(!thedate.value){
			var today=new Date();
			if(type=="end")
				today.setHours(today.getHours()+1);
			thedate.value= dateToString(today);
			thetime.value= timeToString(today);
		}
		thetext.className=null;
		thedate.removeAttribute("readonly");
		thetime.removeAttribute("readonly");
		thedate.className=null;
		thetime.className=null;
		if(type=="end"){
			thetimeButton.onclick=ETBOC;
			thedateButton.onclick=EDBOC;
		} else{
			thetimeButton.onclick=STBOC;
			thedateButton.onclick=SDBOC;
		}
	}

	return true;
}

function initializeOnClicks(){
	var startDateButton=getObjectFromID("startdateButton");
	SDBOC=startDateButton.onclick;
	var startTimeButton=getObjectFromID("starttimeButton");
	STBOC=startTimeButton.onclick;
	var endDateButton=getObjectFromID("enddateButton");
	EDBOC=endDateButton.onclick;
	var endTimeButton=getObjectFromID("endtimeButton");
	ETBOC=endTimeButton.onclick;
	var completedDateButton=getObjectFromID("completeddateButton");
	CDBOC=completedDateButton.onclick;
}

function addS(freqfield){
	var rpType=getObjectFromID("repeattype");
	var plural="";

	if(freqfield.value>1)
		plural="s";
	rpType.options[0].text="Day"+plural;
	rpType.options[1].text="Week"+plural;
	rpType.options[2].text="Month"+plural;
	rpType.options[3].text="Year"+plural;
}


function goParent(addeditfile){
	var parentid=getObjectFromID("parentid");
	var theback=getObjectFromID("thebackurl");
	var theURL=addeditfile+"?id="+parentid.value;
	if(theback.value!="")
		theURL+="&backurl="+theback.value;
	document.location=theURL;
}


function sendEmailNotice(){
	var content="<div align=\"center\" class=\"important\"><img src=\""+APP_PATH+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Processing...</strong></div>";
	showModal(content,"Sending Email",300);
	var theid=getObjectFromID("id");
	var theURL=APP_PATH+"modules/base/notes_ajax.php?cm=sendemail&id="+theid.value;
	loadXMLDoc(theURL,null,false);
	content=req.responseText+"<DIV align=\"right\"><button class=\"Buttons\" onclick=\"closeModal()\" style=\"width:75px\"> ok </button></DIV>";
	var modalcontent=getObjectFromID("modalContent");
	modalcontent.innerHTML=content;
}


function submitForm(theform,bypass){

	if (theform["cancelclick"])
		if (theform["cancelclick"].value!=0)
			return true;

	if(!validateForm(theform))
		return false;

	if(theform["repeating"].checked){

		var typeSelect = getObjectFromID("repeattype");
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
			eachlist.value = tempeachlist;
		}

	}//end if

	var lastrepeat = getObjectFromID("lastrepeat");
	var thetype = getObjectFromID("thetype");
	var bypass = getObjectFromID("bypass");

	if(thetype.value == "TS" && lastrepeat.value && !bypass.value){
		content = '<p><strong>Warning</strong>: Changing a repeatable task that has already been repeated will cause the repeating';
		content +=' to reset. The connection between the current tasks already created form these repeat connections will be erased.</p>';
		content +='<p>You may want to adjust the start date to compensate.</p>';
		content +='<p align="right"><input type="button" class="Buttons" value="continue save" onclick="continueSubmit()" > <input type="button" class="Buttons" value="cancel" onclick="closeModal()" style="width:70px"></p>';

		showModal(content,"Confrim Change of Repeatable Task",400);
		return false;
	}else
		return true;

}//end function


function continueSubmit(){
	var bypass = getObjectFromID("bypass");
	bypass.value =1;
	var saveButton = getObjectFromID('saveButton1');
	saveButton.click()
}

//repeat functions
//=========================================================================================================
function checkRepeat(){

	var startCheckbox = getObjectFromID("startcheck");
	var repeatCheckbox = getObjectFromID("repeating");

	var repeatOptions = getObjectFromID("repeatOptions");
	var repeatEnd = getObjectFromID("repeatEnding");

	if(repeatCheckbox.checked){

		if(startCheckbox.checked){

			repeatOptions.style.display = "block";
			repeatEnd.style.display = "block";

		} else {

			repeatCheckbox.checked=false;
			alert("You must set a start date before setting repeat options");

		}//end if

	} else {

		repeatOptions.style.display = "none";
		repeatEnd.style.display = "none";

	}//endif
}//end function


function changeRepeatType(){
	var dropDown = getObjectFromID("repeattype");
	var i;

	for(i=0;i<dropDown.options.length;i++){
		var theDiv = getObjectFromID(dropDown.options[i].value+"Div");
		if(dropDown.options[i].selected)
			theDiv.style.display = "block";
		else
			theDiv.style.display = "none";
	}

	var typetext = getObjectFromID("repeatTypeText");
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


function changeRepeatEnd(){
	var theselect = getObjectFromID("repeatend");
	var afterSpan = getObjectFromID("repeatAfterSpan");
	var ondatespan = getObjectFromID("repeatOndateSpan");

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
	}//endswitch
}//endfunction

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


/* New Code ---------------------------------------------- */
/* ------------------------------------------------------- */
notes = {

	contentHasFocus: false,

	addTimeStamp: function(){
		// This function called when time stamp button is pushed
		// adds a time stamp to the memo field


		var theDate= new Date();
		var contentTextArea = getObjectFromID("content");
		var username = getObjectFromID("username");

		if(contentTextArea.value != "")
			contentTextArea.value += "\n";

		contentTextArea.value = contentTextArea.value + "[ " + username.value + " - " + theDate.toLocaleString() + " ]\n"

		if(notes.contentHasFocus){

			contentTextArea.focus();

		}//end if

	},//end function addTimeStamp


	contentFocus: function(){

		notes.contentHasFocus = true;

	},//end function contentFocus


	contentBlur: function(){

		notes.contentHasFocus = false;

	}//end function contentFocus

}//end struct notes

/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

	initializeOnClicks();
	changeType()
	completedCheck();
	changeRepeatType();
	changeRepeatEnd();

	var attachedField = getObjectFromID("attachedid");

	if(attachedField)
		if(attachedField.value!="" && attachedField.value!=0){

			var associatedDiv=getObjectFromID("theassociated");
			associatedDiv.style.display="block";

			var rightside = getObjectFromID("rightSideDiv");
			var timestamp = getObjectFromID("timeStampP");
			var content = getObjectFromID("content");
			var newHeight = leftside.offsetHeight - timestamp.offsetHeight-61;

			content.style.height=newHeight+"px"

		}//endif

	var theid=getObjectFromID("id");
	var thetype=getObjectFromID("thetype");
	if(theid.value)
		thetype.disabled=true;

	var thetitle=getObjectFromID("subject");
	if (thetitle.value=="") thetitle.focus();


	var timeStampButton = getObjectFromID("timeStampButton");
	connect(timeStampButton, "onclick", notes.addTimeStamp);

	var contentTextArea = getObjectFromID("content");
	connect(contentTextArea, "onfocus", notes.contentFocus);
	connect(contentTextArea, "onblue", notes.contentBlur);

});//end connect
