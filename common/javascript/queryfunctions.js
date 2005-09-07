// This function handles keyboard shortcuts
function keyhandler(e) {

	if (navigator.userAgent.toLowerCase().indexOf("msie")!=-1) {
		var thekeydown=window.event.ctrlKey;
		var shiftkeydown=window.event.shiftKey;
		var Key = window.event.keyCode;
	}
	else {	
		var thekeydown=(e.altKey);
		var shiftkeydown=e.shiftKey;
		var Key = e.which;
	}
	
	if (thekeydown){
		thekey=String.fromCharCode(Key)
		switch(thekey){
			case "E":
				if (document.forms["search"]["edit"]) 
					document.forms["search"]["edit"].click();
				return false;
			break;
			case "D":
				if (document.forms["search"]["advanced"]) document.forms["search"]["advanced"].click();
				return false;
			break;
			case "T":
				if (document.forms["search"]["reset"]) document.forms["search"]["reset"].click();
				return false;
			break;
			case "N":
				if (document.forms["search"]["new"]) document.forms["search"]["new"].click();
				return false;
			break;
			case "S":
				if (document.forms["search"]["searchbutton"]) document.forms["search"]["searchbutton"].click();
				return false;
			break;
			case "K":
				if (document.forms["search"]["keep"]) document.forms["search"]["keep"].click();
				return false;
			break;
			case "O":
				if (document.forms["search"]["omit"]) document.forms["search"]["omit"].click();
				return false;
			break;
			case "P":
				if (document.forms["search"]["print"]) document.forms["search"]["print"].click();
				return false;
			break;
			case "A":
				if (shiftkeydown){
					if (document.forms["search"]["None"]) document.forms["search"]["None"].click();
				}
				else {
					if (document.forms["search"]["All"]) document.forms["search"]["All"].click();
				}
				return false;
			break;
		}
	}
}
document.onkeydown = keyhandler;

function openWindow(theURL,winName,features) {
	  window.open(theURL,winName,features);
}


//click on row
function clickIt(theTR,theevent,disablectrl){
	var ctrlkeydown=false;
	// stupid browser incompatibilities...//
	if(!disablectrl) 
		if (navigator.userAgent.toLowerCase().indexOf("msie")!=-1) 
			ctrlkeydown=window.event.ctrlKey;
		else 	
			ctrlkeydown=theevent.ctrlKey;

	if(!ctrlkeydown) {
		selIDs= new Array();
		var theTable=theTR.parentNode;
		for(i=0;i<theTable.childNodes.length;i++){
			if (theTable.childNodes[i]!=theTR && theTable.childNodes[i].className){
				theTable.childNodes[i].className="qr"+theTable.childNodes[i].className.charAt(theTable.childNodes[i].className.length-1);
			}
		}
	}

	// need to find the checkbox that the TR contains
	var theID=theTR.id.substr(2,theTR.id.length-1);
	var newClass="";
	var i;
	if (theTR.className.charAt(1)=="h"){
		//highlighted... unhighlight it
		newClass="qr"+theTR.className.charAt(theTR.className.length-1);
		for(i=0;i<selIDs.length;i++)
			if(selIDs[i]==theTR.id.substring(2)) selIDs.slice(i,1);
	} else {
		//highlight it
		newClass="qh"+theTR.className.charAt(theTR.className.length-1);
		selIDs[selIDs.length]=theTR.id.substring(2);
	}
	theTR.className=newClass;

	var disabledstatus=(selIDs.length==0);
	setButtonStatus(disabledstatus);
}

function setSelIDs(theform){
	if(selIDs.length)
		theform["theids"].value=selIDs.join(",");
}

function setButtonStatus(disabledstatus){
	var formname=getObjectFromID("searchform");
	if(formname["edit"])formname["edit"].disabled=disabledstatus;
	if(formname["delete"])formname["delete"].disabled=disabledstatus;
	if(formname["keep"])formname["keep"].disabled=disabledstatus;
	if(formname["omit"])formname["omit"].disabled=disabledstatus;
	if(formname["relationship"])formname["relationship"].disabled=disabledstatus;
	if(formname["othercommands"])formname["othercommands"].disabled=disabledstatus;
	if(formname["print"])formname["print"].disabled=disabledstatus;
}

//double click on row
function editThis(theitem){
	//simulate click on editbutton
	var therownum=theitem.id.substr(2);
	selIDs=new Array();
	selIDs[0]=therownum;
	var theform=getObjectFromID("searchform");
	theform["edit"].click();
}


// Select All/None Button
function selectRecords(allornone){
	if(allornone=="All") allornone=false; else allornone=true;
	var newClass="qr";
	if(!allornone) newClass="qh";
	selIDs= new Array();
	
	theTable=getObjectFromID("queryresults").firstChild;
	for(var i=0;i<theTable.childNodes.length;i++){
		if(theTable.childNodes[i].className){
			theTable.childNodes[i].className=newClass+theTable.childNodes[i].className.charAt(theTable.childNodes[i].className.length-1);
			if(!allornone)
				selIDs[selIDs.length]=theTable.childNodes[i].id.substring(2);
		}
	}
	setButtonStatus(allornone);
}

// Pass the Sort Parameters and subit the form
function doSort(i){
	var theform=document.forms["search"];
	theform["newsort"].value=theform["sortit"+i].value;
	theform.submit();
	return false;
}

// Pass the Sort Parameters and subit the form
function doDescSort(){
	theform=document.forms["search"];
	theform["desc"].value="desc";
	theform.submit();
	return false;
}

function setMainFocus(){
	var startswithfield=getObjectFromID("startswith");
	startswithfield.focus();
}