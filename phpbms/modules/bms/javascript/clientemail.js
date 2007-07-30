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

function showSavedSearches(option){
	var thedisplay="none";					
	var thediv=getObjectFromID("showsavedsearches");	
	if(option.value=="savedsearch") thedisplay="block";	
	thediv.style.display=thedisplay;
}

function showSavedProjects(){
	var thediv=getObjectFromID("loadedprojects");
	savedText=thediv.innerHTML;
	thediv.innerHTML="";
	showModal(savedText,"Load E-mail Project",300);
}

function hideSavedProjects(){
	var thediv=getObjectFromID("loadedprojects");	
	closeModal();
	thediv.innerHTML=savedText
}

function loadProject(){
	var theselect=getObjectFromID("savedprojects");
	var projectid=getObjectFromID("projectid");
	var othercommand=getObjectFromID("othercommand");
	projectid.value=theselect.value;
	othercommand.value="load project";
	closeModal();
	othercommand.click();
}

function deleteProject(){
	var theselect=getObjectFromID("savedprojects");
	var projectid=getObjectFromID("projectid");
	var othercommand=getObjectFromID("othercommand");
	projectid.value=theselect.value;
	othercommand.value="delete project";
	closeModal();
	othercommand.click();
}

function updateSavedProjects(theselect){
	var deletebutton=getObjectFromID("deleteproject");
	var loadbutton=getObjectFromID("loadproject");
	if (theselect.value!="NA"){
		deletebutton.disabled=false;
		loadbutton.disabled=false;
	} else {
		deletebutton.disabled=true;
		loadbutton.disabled=true;
	}
}

function saveProject(theform){
	var theemail=getObjectFromID("email");
	var theemail2=getObjectFromID("ds-email");
	var subject=getObjectFromID("subject");
	var alertMessage="";
	
	if(theemail.value=="" && theemail2.value=="")
		alertMessage+="From cannot be blank.<br />";	

	if(subject.value==""){
		subject.value="[ No Subject ]";
		alertMessage+="Subject cannot be blank.<br />";
	}
	
	if(alertMessage!="")
		alert(alertMessage);
	else{
		text="<p><input type=\"text\" id=\"saveInput\" maxlength=\"128\"onkeyup=\"updateSaveProject(this)\" /></p>";
		text+="<p align=\"right\"><input type=\"button\" class=\"Buttons\" id=\"saveSave\" value=\"save\" disabled=\"disabled\" onclick=\"finishSaveProject();\" />";
		text+="&nbsp;<input class=\"Buttons\" type=\"button\" value=\"cancel\" id=\"saveCancel\" onclick=\"closeModal()\" /></>";
		showModal(text,"Save Project As...",300);
	}
}

function updateSaveProject(theText){
	var savebutton=getObjectFromID("saveSave");
	if(theText.value=="")
		savebutton.disabled=true;
	else
		savebutton.disabled=false;
}

function finishSaveProject(){
	var saveInput=getObjectFromID("saveInput");
	var savename=getObjectFromID("savename");
	var othercommand=getObjectFromID("othercommand");
	savename.value=saveInput.value;
	othercommand.value="save project";
	closeModal();
	othercommand.click();
}

function addField(){
	var thefield=getObjectFromID("choosefield");
	insertAtCursor("body",("[["+thefield.value+"]]"));
}

function insertAtCursor(myFieldName, myValue) {
	var myField=getObjectFromID(myFieldName);
	//IE support
	if (document.selection) {
	myField.focus();
	sel = document.selection.createRange();
	sel.text = myValue;
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
	var startPos = myField.selectionStart;
	var endPos = myField.selectionEnd;
	myField.value = myField.value.substring(0, startPos)
	+ myValue
	+ myField.value.substring(endPos, myField.value.length);
	} else {
	myField.value += myValue;
	}
}

i = 0;

function sendMailButton(){

	timer = setTimeout("sendEmail()", 1000);

}

function sendEmail(){
		var counterspan=getObjectFromID("amountprocessed");
		var thebutton=getObjectFromID("beginprocessing");
		thebutton.disabled=true;
		var error;
		
		if(i<ids.length){
			error=false;
			message="Email Sent";
			
			if (emails[i]!=""){
			
				theurl="clients_email_process.php?id="+ids[i];
				loadXMLDoc(theurl,null,false);
				
				if(!req.responseXML){
					alert(theurl);
					return false;
				}
				response = req.responseXML.documentElement;
				if(response.getElementsByTagName('result')[0].firstChild.data!="sent"){
					error=true;
					message="Error Sending Email";
				}
					
			} else {
				error=true;
				message="blank e-mail address";
			}
			
			addEntry(i,ids[i],names[i],emails[i],error,message);
			
			while (counterspan.childNodes[0]) {
			    counterspan.removeChild(counterspan.childNodes[0]);
			}
			
			counterspan.appendChild(document.createTextNode(" "+(i+1)));
			
			i++;
			
			var timeout = 5;
			if((i%3) == 0)
				timeout = 1000;
			
			timer = setTimeout("sendEmail()", timeout);
			
		}//done processing shut down processing button
		
}

function addEntry(num,id,name,email,error,message){
		var thetr;
		var  thetd;
		var theresult=getObjectFromID("tablereference").parentNode.parentNode;
		var lastrow=getObjectFromID("lastrow");
		thetr=document.createElement("TR");
			if(error)
				thetr.className="qr1 noselects";
			else
				thetr.className="qr2 noselects";
			
			thetd=document.createElement("TD")
				thetd.appendChild(document.createTextNode(num));
				thetd.setAttribute("nowrap","true");
			thetr.appendChild(thetd);
			thetd=document.createElement("TD")
				thetd.appendChild(document.createTextNode(id));
				thetd.setAttribute("nowrap","true");
			thetr.appendChild(thetd);
			thetd=document.createElement("TD")
				thetd.appendChild(document.createTextNode("\u00A0"+name));
				thetd.setAttribute("nowrap","nowrap");
				thetd.setAttribute("align","left");
			thetr.appendChild(thetd);
			thetd=document.createElement("TD")
				thetd.appendChild(document.createTextNode("\u00A0"+email));
				thetd.setAttribute("nowrap","true");
				thetd.setAttribute("align","left");
			thetr.appendChild(thetd);
			thetd=document.createElement("TD")
				thetd.appendChild(document.createTextNode(message));
				thetd.setAttribute("align","left");
				thetd.className="Important"
			thetr.appendChild(thetd);
			
		theresult.insertBefore(thetr,lastrow);
		
}

function loadXMLDoc(url,readyStateFunction,async) 
{
	// branch for native XMLHttpRequest object
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
		req.onreadystatechange = readyStateFunction;
		req.open("GET", url, async);
		req.send(null);
	// branch for IE/Windows ActiveX version
	} else if (window.ActiveXObject) {
		req = new ActiveXObject("Microsoft.XMLHTTP");
		if (req) {
			if(readyStateFunction) req.onreadystatechange = readyStateFunction;
			req.open("GET", url, async);
			req.send();
		}
	}
}
