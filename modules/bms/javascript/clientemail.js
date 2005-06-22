function getObjectFromID(id){
	var theObject;
	if(document.getElementById)
		theObject=document.getElementById(id);
	else
		theObject=document.all[id];
	return theObject;
}

function showSavedSearches(option){
	var thedisplay="none";					
	var thediv=getObjectFromID("showsavedsearches");	
	if(option.value=="savedsearch") thedisplay="block";	
	thediv.style.display=thedisplay;
}

function showSavedProjects(){
	var thediv=getObjectFromID("loadedprojects");	
	if (thediv.style.display=="none")
		thediv.style.display="block";
}

function hideSavedProjects(){
	var thediv=getObjectFromID("loadedprojects");	
	thediv.style.display="none";
}

function saveProject(theform){
	if(theform["email"].value=="" && theform["ds-email"].value==""){
		alert("From field cannot be blank");
		theform["subject"].value="[ No Subject ]";
		return false;
	}

	if(theform["subject"].value==""){
		alert("Subject cannot be blank");
		theform["subject"].value="[ No Subject ]";
		return false;
	}

	var savedname=prompt("Enter e-mail project name");
	if (!savedname || savedname=="") return false;

	theform["savename"].value=savedname;
	return true;
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

function processEmails(){
		var counterspan=getObjectFromID("amountprocessed");
		var thebutton=getObjectFromID("beginprocessing");
		thebutton.disabled=true;
		var error;
		
		for(var i=0;i<ids.length;i++){
			error=false;
			message="sent";
			if (emails[i]!=""){
				theurl="clients_email_process.php?id="+ids[i];
				loadXMLDoc(theurl,null,false);
				response = req.responseXML.documentElement;
				if(response.getElementsByTagName('result')[0].firstChild.data!="sent"){
					error=true;
					message="Error Sending Email";
				}	
			} else {
				error=true;
				message="blank e-mail address";
			}
			addEntry(i,ids[i],names[i],emails[i],error,message)
			while (counterspan.childNodes[0]) {
			    counterspan.removeChild(counterspan.childNodes[0]);
			}
			counterspan.appendChild(document.createTextNode(" "+(i+1)));
		}//end for
		//done processing shut down processing button
}

function addEntry(num,id,name,email,error,message){
		var thetr;
		var  thetd;
		var theresult=getObjectFromID("results");
		thetr=document.createElement("TR");
			if(error)
				thetr.className="row1";
			else
				thetr.className="row2";
			
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
				thetd.setAttribute("nowrap","true");
			thetr.appendChild(thetd);
			thetd=document.createElement("TD")
				thetd.appendChild(document.createTextNode("\u00A0"+email));
				thetd.setAttribute("nowrap","true");
			thetr.appendChild(thetd);
			thetd=document.createElement("TD")
				thetd.appendChild(document.createTextNode(message));
				thetd.setAttribute("align","right");
			thetr.appendChild(thetd);			
		theresult.appendChild(thetr);
		
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
