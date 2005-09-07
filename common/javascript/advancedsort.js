

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

function dosort(theform){
	opener.document.forms["search"]["advancedsort"].value=theform["constructedquery"].value;
	opener.document.forms["search"].submit();
	window.close();
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

function togglesavedsorts(){
	var thebutton=getObjectFromID("savedsorts");
	var thediv=getObjectFromID("savedsortsbox");

	if (thebutton.value=="saved sorts..."){
		thebutton.value="*saved sorts...";
		thediv.style.display="block";
		self.resizeBy(0,100);		
	} else {
		thebutton.value="saved sorts...";
		thediv.style.display="none";
		self.resizeBy(0,-100);		
	}
}

function checkForCustom(fieldvalue){
	var thediv=getObjectFromID("sqlsortby");
	if(fieldvalue=="**CUSTOM**"){
		if (thediv.style.display=="none"){
			thediv.style.display="block";
			self.resizeBy(0,50);		
		}
	}else{
		if (thediv.style.display=="block"){
			thediv.style.display="none";
			self.resizeBy(0,-50);		
		}
	}
}

function getname(theform){
	var savedname=prompt("Enter saved search name");
	if (savedname=="") return false;
	theform.savename.value=savedname;
	return true;
}

function addToSort(theform,maintable){
		var thedisplay=theform["sortby"].value;
		var theorderbyfield=maintable+"."+thedisplay;
		var theorder="";
		if (thedisplay=="**CUSTOM**"){
			thedisplay=theform["freetextsortby"].value;
			theorderbyfield=thedisplay;
		}
		if(theform["order"].value=="DESC")
			theorder=" (Descending)"
		theform["englishsortby"].options[theform["englishsortby"].options.length]= new
			Option(thedisplay.substring(0,60) + theorder,theorderbyfield + " " + theform["order"].value);		

		buildSQLSort(theform);
}	

function buildSQLSort(theform){
	var i;
	var thesql="";
	for(i=0;i<theform["englishsortby"].length;i++)	
		if (theform["englishsortby"].options[i].value)
			thesql=thesql+", "+theform["englishsortby"].options[i].value
	
	theform["constructedquery"].value=thesql.substring(1);
}

function removeItem(theform){
	for(i=0;i<theform["englishsortby"].length;i++)	
		if (theform["englishsortby"].options[i].selected)
			theform["englishsortby"].options[i]=null;
		buildSQLSort(theform);
}

function moveItem(theform,direction){
	var tempText,temptempValue;
	
	for(i=0;i<theform["englishsortby"].length;i++)	{
		if (theform["englishsortby"].options[i].selected) {
			tempText=theform["englishsortby"].options[i].text;
			tempValue=theform["englishsortby"].options[i].value;
			if(direction=="up" && i!=0){
				theform["englishsortby"].options[i].value=theform["englishsortby"].options[i-1].value;
				theform["englishsortby"].options[i].text =theform["englishsortby"].options[i-1].text;
				theform["englishsortby"].options[i-1].text =tempText;
				theform["englishsortby"].options[i-1].value =tempValue;
				theform["englishsortby"].options[i-1].selected =true;
				i=500;
			} if(direction=="down" && i!=theform["englishsortby"].length-1){
				theform["englishsortby"].options[i].value=theform["englishsortby"].options[i+1].value;
				theform["englishsortby"].options[i].text =theform["englishsortby"].options[i+1].text;
				theform["englishsortby"].options[i+1].text =tempText;
				theform["englishsortby"].options[i+1].value =tempValue;
				theform["englishsortby"].options[i+1].selected =true;
				i=500;
			}
		}
	}

	buildSQLSort(theform);
	
}//end function

function clearSort(theform){
	for(i=theform["englishsortby"].length-1;i>=0;i--)	
		theform["englishsortby"].options[i]=null;
	buildSQLSort(theform);
}