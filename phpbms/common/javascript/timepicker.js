/*
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
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

function showTP(base,timeFieldID){
	var dateField= getObjectFromID(timeFieldID);
	
	//check to see if a nother box is already showing
	var alreadybox=getObjectFromID("TPmoreless");
	if(alreadybox) closeTPBox();

	//get positioning
	var thetop=getTop(dateField)+dateField.offsetHeight;
	var theleft=getLeft(dateField);
	if (theleft+230 > window.innerWidth)
		theleft= theleft-230+dateField.offsetWidth-15;

	showTP.box=document.createElement("div");
	showTP.box.className="bodyline";
	showTP.box.style.diplay="block";
	showTP.box.style.padding="0";
	showTP.box.style.position="absolute";
	showTP.box.style.top=thetop + "px";
	showTP.box.style.left=theleft + "px";
	
	showTP.timeField=timeFieldID;

	document.body.appendChild(showTP.box);
	var content="<div align=\"center\"><img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Loading...</strong></div>";	
	showTP.box.innerHTML=content;

	var theURL=base+"timepicker.php?cm=shw";
	loadXMLDoc(theURL,null,false);
	showTP.box.innerHTML=req.responseText;	

}

function switchMinutes(thebutton){
	var lessmin=getObjectFromID("tpMinuteLess");
	var moremin=getObjectFromID("tpMinuteMore");
	if(thebutton.value=="more"){
		thebutton.value="less";		
		lessmin.style.display="none";
		moremin.style.display="table";
	}
	else {
		thebutton.value="more";
		lessmin.style.display="table";
		moremin.style.display="none";
	}
}

function closeTPBox(){
	document.body.removeChild(showTP.box);
	showTP.box=null;	
	showTP.timeFieldID=null;	
}

function tpClickHour(hour){
	var timeField=getObjectFromID(showTP.timeField);
	var ampm;
	if(!validateTime(timeField.value)) timeField.value="12:00 AM";
	
	var minutes=timeField.value.substr(timeField.value.indexOf(":")+1,2)
	
	if(hour>11) {ampm=" PM"; hour=hour-12}else ampm=" AM";
	if (hour==0) hour=12;
	
	timeField.value=hour+":"+minutes+ampm;
	if(timeField.onchange) timeField.onchange();
}
function tpClickMinute(thetd){
	var minutes=thetd.innerHTML;
	var timeField=getObjectFromID(showTP.timeField);
	if(!validateTime(timeField.value)) timeField.value="12:00 AM";

	var hours=timeField.value.substring(0,timeField.value.indexOf(":"));
	var ampm=timeField.value.substr(timeField.value.indexOf(" "));
	timeField.value=hours+minutes+ampm;
	if(timeField.onchange) timeField.onchange();
	closeTPBox();
}


