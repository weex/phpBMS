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
		alertMessage+="From cannot be blank.<br>";	

	if(subject.value==""){
		subject.value="[ No Subject ]";
		alertMessage+="Subject cannot be blank.<br>";
	}
	
	if(alertMessage!="")
		alert(alertMessage);
	else{
		text="<div><input type=\"text\" id=\"saveInput\" maxlength=\"128\" style=\"width:99%\" onKeyUp=\"updateSaveProject(this)\"></div>";
		text+="<div align=\"right\"><input type=\"button\" class=\"Buttons\" id=\"saveSave\" style=\"width:75px;\" value=\"save\" disabled=\"true\" onClick=\"finishSaveProject();\">&nbsp;<input class=\"Buttons\" type=\"button\" value=\"cancel\" id=\"saveCancel\" onClick=\"closeModal()\" style=\"width:75px;\"></div>";
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
