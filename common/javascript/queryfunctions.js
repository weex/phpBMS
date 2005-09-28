function openWindow(theURL,winName,features) {
	  window.open(theURL,winName,features);
}


//click on row
function clickIt(theTR,theevent,disablectrl){
	var ctrlkeydown=false;
	var shiftkeydown=false;
	// stupid browser incompatibilities...//
	if(!disablectrl) {
		if (navigator.userAgent.toLowerCase().indexOf("msie")!=-1)
			theevent=window.event
		ctrlkeydown=theevent.ctrlKey;
		shiftkeydown=theevent.shiftKey;
	}

	if(!ctrlkeydown && !shiftkeydown) {
		selIDs= new Array();
		var theTable=theTR.parentNode;
		for(i=0;i<theTable.childNodes.length;i++){
			if (theTable.childNodes[i]!=theTR && theTable.childNodes[i].className){
				theTable.childNodes[i].className="qr"+theTable.childNodes[i].className.charAt(theTable.childNodes[i].className.length-1);
			}
		}
	}
	
	var theID=theTR.id.substr(2,theTR.id.length-1);

	if(!ctrlkeydown && shiftkeydown){
		//Need to program shift click in here!
		var curID=null;
		var theTable=theTR.parentNode;
		var searchArray="+"+selIDs.join("+")+"+";
		var point1=null;
		var point2=null;
		
		for(i=0;i<theTable.childNodes.length;i++){
			if (theTable.childNodes[i].className){
				theTable.childNodes[i].className="qr"+theTable.childNodes[i].className.charAt(theTable.childNodes[i].className.length-1);
				curID=theTable.childNodes[i].id.substr(2);
				if(curID==theID)
					point1=i;
				if(searchArray.indexOf("+"+curID+"+")!=-1 && point2==null) {
					point2=i;
				}
			}
		}
		var theStart;
		var theStop;
		if (point1>point2){
			theStart=point2;
			theStop=point1
		} else {
			theStart=point1;
			theStop=point2
		}
		selIDs= new Array();
		for(i=theStart;i<=theStop;i++){
			if (theTable.childNodes[i].className){
				theTable.childNodes[i].className="qh"+theTable.childNodes[i].className.charAt(theTable.childNodes[i].className.length-1);
				selIDs[selIDs.length]=theTable.childNodes[i].id.substring(2);
			}
		}
		
	} else {
	
		// need to find the checkbox that the TR contains
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
}

function setSelIDs(theform){
	if(selIDs.length)
		theform["theids"].value=selIDs.join(",");
}

function setButtonStatus(disabledstatus){
	var formname=getObjectFromID("searchform");
	if(formname["edit"])formname["edit"].disabled=disabledstatus;
	if(formname["delete"])formname["delete"].disabled=disabledstatus;
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

function showMore(moreless){
	var morediv=getObjectFromID("moresearchoptions");
	var lessdiv=getObjectFromID("lesssearchoptions");
	if(moreless=="more"){
		morediv.style.display="block";
		lessdiv.style.display="none";
	} else {
		morediv.style.display="none";
		lessdiv.style.display="block";
	}
	
}

function perfromToSelection(theselect){		
	var thereset=getObjectFromID("reset");
	switch(theselect.value){
		case"":
			theselect.selectedIndex=0;
		break;
		case "selectall":
			selectRecords("All");
			theselect.selectedIndex=0;
		break;
		case "selectnone":
			selectRecords("None");
			theselect.selectedIndex=0;
		break;
		case "keepselected":
			if(selIDs.length>0){
				thereset.value="keep"
				thereset.click();
			} else {
			alert("You must select records first.");
			theselect.selectedIndex=0;			
			}
		break;
		case "omitselected":
			if(selIDs.length>0){
				thereset.value="omit"
				thereset.click();
			} else {
			alert("You must select records first.");
			theselect.selectedIndex=0;			
			}
		break;
	}
}

function changeSelection(optionValue){
	var theselect=getObjectFromID("searchSelection");
	var i;	
	for(i=0;i<theselect.options.length;i++){
		if(theselect.options[i].value==optionValue)
			theselect.selectedIndex=i
	}
	perfromToSelection(theselect);
}