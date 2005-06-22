	function updatequery(theitem) {
		formname=theitem.form;

		formname["andorand2"].checked ? andor2="and" : andor2="or";
		formname["andorand3"].checked ? andor3="and" : andor3="or";
		formname["andorand4"].checked ? andor4="and" : andor4="or";
		formname["andorand5"].checked ? andor5="and" : andor5="or";

		thetext="";
		thetext=thetext + formname["tablename"].value + "." + formname["field1"].value + " " + formname["operator1"].value + " \"" + formname["thetext1"].value + "\"";
		if (formname["line2"].checked) {
			thetext=thetext + " " + andor2 + " " + formname["tablename"].value + "." + formname["field2"].value + " " + formname["operator2"].value + " \"" + formname["thetext2"].value + "\"";
		}
		if (formname["line3"].checked) {
			thetext=thetext + " " + andor3 + " " + formname["tablename"].value + "." + formname["field3"].value + " " + formname["operator3"].value + " \"" + formname["thetext3"].value + "\"";
		}
		if (formname["line4"].checked) {
			thetext=thetext + " " + andor4 + " " + formname["tablename"].value + "." + formname["field4"].value + " " + formname["operator4"].value + " \"" + formname["thetext4"].value + "\"";
		}
		if (formname["line5"].checked) {
			thetext=thetext + " " + andor5 + " " + formname["tablename"].value + "." + formname["field5"].value + " " + formname["operator5"].value + " \"" + formname["thetext5"].value + "\"";
		}
		formname["constructedquery"].value=thetext;
	}

	function unlockLine(i,theitem) {
		formname=theitem.form
		if (formname["line"+i].checked) {
			formname["andorand"+i].disabled=false;
			formname["andoror"+i].disabled=false;
			formname["field"+i].disabled=false;
			formname["operator"+i].disabled=false;
			formname["andorand"+i].disabled=false;
			formname["thetext"+i].disabled=false;
		} else {
			formname["andorand"+i].disabled=true;
			formname["andoror"+i].disabled=true;
			formname["field"+i].disabled=true;
			formname["operator"+i].disabled=true;
			formname["andorand"+i].disabled=true;
			formname["thetext"+i].disabled=true;
		}
		updatequery(theitem);
	}

function dosearch(theform){
	opener.document.forms["search"].advancedsearch.value=theform["constructedquery"].value;
	opener.document.forms["search"].submit();
	window.close();
}

function getObjectFromID(id){
	var theObject;
	if(document.getElementById)
		theObject=document.getElementById(id);
	else
		theObject=document.all[id];
	return theObject;
}

function toggletips(){
	var thebutton=getObjectFromID("showtips");
	var thediv=getObjectFromID("tipsbox");

	if (thebutton.value=="show tips"){
		thebutton.value="hide tips";
		thediv.style.display="block";
		self.resizeBy(0,90);		
	} else {
		thebutton.value="show tips";
		thediv.style.display="none";
		self.resizeBy(0,-90);		
	}
}

function togglesql(){
	var thebutton=getObjectFromID("showsql");
	var thediv=getObjectFromID("sqlbox");

	if (thebutton.value=="show SQL"){
		thebutton.value="hide SQL";
		thediv.style.display="block";
		self.resizeBy(0,110);		
	} else {
		thebutton.value="show SQL";
		thediv.style.display="none";
		self.resizeBy(0,-110);		
	}
}

function togglesavedsearches(){
	var thebutton=getObjectFromID("savedsearches");
	var thediv=getObjectFromID("savedsearchesbox");

	if (thebutton.value=="saved searches..."){
		thebutton.value="*saved searches...";
		thediv.style.display="block";
		self.resizeBy(0,100);		
	} else {
		thebutton.value="saved searches...";
		thediv.style.display="none";
		self.resizeBy(0,-100);		
	}
}

function getname(theform){
	var savedname=prompt("Enter saved search name");
	if (savedname && savedname=="") return false;
	theform.savename.value=savedname;
	return true;
}
