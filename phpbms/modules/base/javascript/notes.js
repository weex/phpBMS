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

window.onload=function(){
	initializeOnClicks();
	changeType()
	completedCheck();
	
	var attachedField=getObjectFromID("attachedid");
	if(attachedField)
		if(attachedField.value!="" && attachedField.value!=0){
			var associatedDiv=getObjectFromID("theassociated");
			var content=getObjectFromID("content");
			associatedDiv.style.display="block";
			content.rows+=7;
		}
	var theid=getObjectFromID("id");
	var thetype=getObjectFromID("thetype");
	if(theid.value)
		thetype.disabled=true;
	setEnglishDates();	
	
	var thetitle=getObjectFromID("subject");
	if (thetitle.value=="") thetitle.focus();
}

function returnFalse(){
	return false;
}

function timeStamp(){
	var theDate= new Date()
	var theform=document.forms["record"]
	theform["content"].value=theform["content"].value+"[ "+theform["username"].value+" - "+theDate.toLocaleString()+" ]"
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
	var repeatdiv=getObjectFromID("therepeat");
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
			if(!parentid.value){
				repeatdiv.style.display="block";
				doRepeat();
			}
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
			if(!parentid.value){
				repeatdiv.style.display="block";
				doRepeat();
			}
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
	var leftside=getObjectFromID("leftSideDiv");
	var timestamp=getObjectFromID("timeStampDiv");
	var content=getObjectFromID("content");
	var newHeight=leftside.offsetHeight-timestamp.offsetHeight-38;
	content.style.height=newHeight+"px"
	
	dateChecked("start");
	dateChecked("end");

}

function completedCheck(){
	var checkbox=getObjectFromID("completed");
	var completedDate=getObjectFromID("completeddate");
	var completedDateButton=getObjectFromID("completeddateButton");
	if(checkbox.checked){
		
		completedDate.setAttribute("readonly",null);
		completedDate.className=null;
		completedDateButton.onclick=CDBOC;
		if(!completedDate.value){
			var today=new Date();
			completedDate.value=dateToString(today);
		}
	} else {
		completedDate.setAttribute("readonly","readonly");
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
	var repeat=getObjectFromID("repeat");

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
	var repeatUntilDateButton=getObjectFromID("repeatuntildateButton");
	RUDB=repeatUntilDateButton.onclick;
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

function doRepeat(){
	var rpdiv=getObjectFromID("therepeat");
	var startcheck = getObjectFromID("startcheck");
	var rpspan=getObjectFromID("repeatoptions");
	var rpchk=getObjectFromID("repeat");
	if(rpdiv.style.display!="none"){
		var rpFreq=getObjectFromID("repeatfrequency");
		var rpType=getObjectFromID("repeattype");
		var rpUntilrdF=getObjectFromID("rprduntilforever");
		var rpUntilrdT=getObjectFromID("rprduntilftimes");
		var rpUntilTimes=getObjectFromID("repeattimes");
		var rpUntilrdD=getObjectFromID("rprduntildate");
		var rpUntilDate=getObjectFromID("repeatuntildate");
		var rpUntilDateButton=getObjectFromID("repeatuntildateButton");
		var rpWO=getObjectFromID("weeklyoptions");
		var rpMO=getObjectFromID("monthlyoptions");
		if(rpchk.checked){
			if(!startcheck.checked){
				alert("Setting up recurring tasks requires a start date.");
				rpchk.checked=false;
				return false;
			}
			rpspan.className="";
			rpFreq.removeAttribute("readonly");
			rpFreq.className="";
			rpType.disabled=false;
			rpUntilrdF.disabled=false;
			rpUntilrdT.disabled=false;
			rpUntilrdD.disabled=false;
			if(rpUntilrdT.checked){
				rpUntilTimes.removeAttribute("readonly");
				rpUntilTimes.className="";
			}
			if(rpUntilrdD.checked){
				rpUntilDate.removeAttribute("readonly");
				rpUntilDate.className="";
				rpUntilDateButton.onclick=RUDB;
			}
			if(rpType.value=="Weekly")
				rpWO.style.display="block";
			else if(rpType.value=="Monthly"){
				rpMO.style.display="block";	
				var rpMOdate = getObjectFromID("rpmobdt");
				var rpMOday = getObjectFromID("rpmobda");
				if(!rpMOdate.checked && !rpMOday.checked)
					rpMOdate.checked=true;
			}
		} else
		{
			rpspan.className="disabledtext";			
			rpFreq.setAttribute("readonly","readonly");
			rpFreq.className="uneditable";
			rpType.disabled=true;
			rpUntilrdF.disabled=true;
			rpUntilrdT.disabled=true;
			rpUntilrdD.disabled=true;
			rpUntilTimes.setAttribute("readonly","readonly");
			rpUntilTimes.className="uneditable";
			rpUntilDate.setAttribute("readonly","readonly");
			rpUntilDate.className="uneditable";
			rpUntilDateButton.onclick=returnFalse;
			if(rpType.value=="Weekly")
				rpWO.style.display="none";			
			else if(rpType.value=="Monthly")
				rpMO.style.display="none";			
		}
	}
}

function changeRepeatType(){
	var rpType=getObjectFromID("repeattype");	
	var rpWO=getObjectFromID("weeklyoptions");
	var rpMO=getObjectFromID("monthlyoptions");
	
	switch(rpType.value){
		case "Daily":
			rpWO.style.display="none";
			rpMO.style.display="none";
		break;
		case "Weekly":
			rpWO.style.display="block";			
			rpMO.style.display="none";
		break;
		case "Monthly":
			setEnglishDates();
			rpWO.style.display="none";
			rpMO.style.display="block";
			var rpMOdate = getObjectFromID("rpmobdt");
			var rpMOday = getObjectFromID("rpmobda");
			if(!rpMOdate.checked && !rpMOday.checked)
				rpMOdate.checked=true;
		break;
		case "Yearly":
			rpWO.style.display="none";
			rpMO.style.display="none";
		break;
	}
}

function setEnglishDates(){
	var byDateText= getObjectFromID("rpmobydate");
	var byDayText= getObjectFromID("rpmobyday");	
	var startdate= getObjectFromID("startdate");
	if(startdate.value=="") return false;
	var thedate= stringToDatetime(startdate.value)
	var theday= parseInt(startdate.value.substring(startdate.value.indexOf("/")+1,startdate.value.lastIndexOf("/")),10);
	
	var dayending="th";
	switch(thedate.getDate()){
		case 1:
		case 21:
		case 31:
			dayending="st";
		break;
		case 2:
		case 22:
			dayending="nd";
		case 3:
		case 23:
			dayending="rd";
	}
	
	byDateText.innerHTML=thedate.getDate()+dayending;
	
	
	var whichday=Math.floor((thedate.getDate()-1)/7);
	var dayname;
	var which;
	switch (thedate.getDay()){
		case 0:
			dayname="Sunday";
		break;
		case 1:
			dayname="Monday";
		break;
		case 2:
			dayname="Tuesday";
		break;
		case 3:
			dayname="Wednesday";
		break;
		case 4:
			dayname="Thursday";
		break;
		case 5:
			dayname="Friday";
		break;
		case 6:
			dayname="Saturday";
		break;		
	};
	switch(whichday){
		case 0:
			which="First";
		break;
		case 1:
			which="Second";
		break;
		case 2:
			which="Third";
		break;
		case 3:
			which="Fourth";
		break;
		case 4:
			which="Last";
		break;
		
	}
	byDayText.innerHTML=which+" "+dayname;
}

function updateRepeatUntil(){
	var rpUntilrdF=getObjectFromID("rprduntilforever");
	var rpUntilrdT=getObjectFromID("rprduntilftimes");
	var rpUntilTimes=getObjectFromID("repeattimes");
	var rpUntilrdD=getObjectFromID("rprduntildate");
	var rpUntilDate=getObjectFromID("repeatuntildate");
	var rpUntilDateButton=getObjectFromID("repeatuntildateButton");

	if(rpUntilrdF.checked){
		rpUntilTimes.setAttribute("readonly","readonly");
		rpUntilTimes.className="uneditable";
		rpUntilDate.setAttribute("readonly","readonly");
		rpUntilDate.className="uneditable";
		rpUntilDateButton.onclick=returnFalse;
	} else if(rpUntilrdT.checked) {
		rpUntilTimes.removeAttribute("readonly");
		rpUntilTimes.className="";
		rpUntilDate.setAttribute("readonly","readonly");
		rpUntilDate.className="uneditable";
		rpUntilDateButton.onclick=returnFalse;
		rpUntilTimes.focus()
	} else if(rpUntilrdD.checked) {
		rpUntilTimes.setAttribute("readonly","readonly");
		rpUntilTimes.className="uneditable";
		rpUntilDate.removeAttribute("readonly");
		rpUntilDate.className="";
		rpUntilDateButton.onclick=RUDB;
		var today=new Date();
		var theday= new Date(today.valueOf()+(1000*60*60*24));
		rpUntilDate.value=dateToString(theday);
		
		rpUntilDate.focus()
	}
}

function goParent(addeditfile){
	var parentid=getObjectFromID("parentid");
	var theback=getObjectFromID("thebackurl");
	var theURL=addeditfile+"?id="+parentid.value;
	if(theback.value!="")
		theURL+="&backurl="+theback.value;
	document.location=theURL;
}

function sendEmailNotice(base){
	var content="<div align=\"center\" class=\"important\"><img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Processing...</strong></div>";
	showModal(content,"Sending Email",300);
	var theid=getObjectFromID("id");
	var theURL=base+"modules/base/notes_ajax.php?cm=sendemail&id="+theid.value;
	loadXMLDoc(theURL,null,false);
	content=req.responseText+"<DIV align=\"right\"><button class=\"Buttons\" onClick=\"closeModal()\" style=\"width:75px\"> ok </button></DIV>";
	var modalcontent=getObjectFromID("modalContent");
	modalcontent.innerHTML=content;
}