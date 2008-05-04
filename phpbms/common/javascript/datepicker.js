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

function showDP(base,dateFieldID){
	var dateField= getObjectFromID(dateFieldID);
	
	//check to see if a nother box is already showing
	var alreadybox=getObjectFromID("DPCancel");
	if(alreadybox) closeDPBox();

	//get positioning
	var thetop=getTop(dateField)+dateField.offsetHeight;
	var theleft=getLeft(dateField);
	if (theleft+140 > window.innerWidth)
		theleft= theleft-140+dateField.offsetWidth-15;

	showDP.box=document.createElement("div");
	showDP.box.className="bodyline";
	showDP.box.style.display="block";
	showDP.box.style.padding="0";
	showDP.box.style.position="absolute";
	showDP.box.style.top=thetop + "px";
	showDP.box.style.left=theleft + "px";
	
	showDP.datefieldID=dateFieldID;
	
	if(dateField.value){
		selDate=stringToDate(dateField.value);
		year=selDate.getFullYear();
		month=selDate.getMonth()+1;
	} else{		
		selDate=null;
		tdate=new Date();
		year=tdate.getFullYear();
		month=tdate.getMonth()+1;
	}
	loadMonth(base,month,year,dateField.value);
	hideSelectBoxes();
	document.body.appendChild(showDP.box);
}

function loadMonth(base,month,year,selectedDate){
	var content="<div align=\"center\"><img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Loading...</strong></div>";	
	showDP.box.innerHTML=content;
	var theURL=base+"datepicker.php?cm=shw";
	theURL+="&m="+encodeURIComponent(month);
	theURL+="&y="+encodeURIComponent(year);
	if (selectedDate){
		tempdate=stringToDate(selectedDate);
		theURL+="&sd="+encodeURIComponent(dateToString(tempdate, 'SQL'));
	}
	loadXMLDoc(theURL,null,false);
	showDP.box.innerHTML=req.responseText;	
}


function closeDPBox(){
	document.body.removeChild(showDP.box);
	displaySelectBoxes();
	showDP.box=null;	
	showDP.datefieldID=null;	
}

function dpClickDay(year,month,day){
	var thefield=getObjectFromID(showDP.datefieldID);
	var thedate=new Date(parseInt(year,10),parseInt(month,10)-1,parseInt(day,10));
	thefield.value=dateToString(thedate);

	if(thefield.onchange) thefield.onchange.call(thefield);
	trigger(thefield,"onchange");
	
	closeDPBox();
}

function dpHighlightDay(year,month,day){
	var displayinfo=getObjectFromID("dpExp");
	displayinfo.innerHTML=MONTH_NAMES_LONG[month-1]+" "+day+", "+year;
}

function dpUnhighlightDay(){
	var displayinfo=getObjectFromID("dpExp");
	displayinfo.innerHTML="&nbsp;";
}


function formatDateField(thefield){
	
	var formatedDate = stringToDate(thefield.value);
	if(thefield.value != "")
		if(isNaN(formatedDate.getMonth()))
			formatedDate = new Date();
		
	thefield.value = dateToString(formatedDate);
}