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


var initArray=new Array();

initArray[initArray.length]=function(){
	//SystemMessage Accordian
	//we define two arrays, containing our toggles and divs.
	var systemMessageDivs = document.getElementsByClassName('systemMessages');
	var systemMessageLinks = document.getElementsByClassName('systemMessageLinks');

	var taskDivs = document.getElementsByClassName('tasksDivs');
	var taskLinks = document.getElementsByClassName('tasksLinks');

	var systemMessageAccordion = new fx.Accordion(systemMessageLinks, systemMessageDivs, {opacity: true, duration:150});
	var taskAccordion = new fx.Accordion(taskLinks, taskDivs, {opacity: true, duration:300});
	taskAccordion.showThisHideOpen(taskDivs[2]);
}

window.onload= function() {
	var i;
	for(i=0;i<initArray.length;i++)
		initArray[i].call();
}


function checkTask(id,type){
	var thediv=getObjectFromID("TS"+id);
	var thecheckbox=getObjectFromID("TSC"+id);
	var isprivate=getObjectFromID("TSprivate"+id);
	var ispastdue=getObjectFromID("TSispastdue"+id);
	
	var theURL="snapshot_ajax.php?id="+id+"&ty="+type+"&cm=updateTask&cp=";
	if(thecheckbox.checked){
		theURL+="1";
		thediv.className="small taskCompleted";
	} else {
		theURL+="0";
		var classname=" small task";
		if(isprivate.value==1)
			classname+=" taskPrivate";
		if(ispastdue.value==1)
			classname="small taskPastDue";
		thediv.className=classname;
	}
	loadXMLDoc(theURL,null,false);
	if(req.responseText!="success")
		alert("Error: <br />"+req.responseText);	
	
}