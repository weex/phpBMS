//Set up all switching array
theReport=new Array();

function switchReport(theitem){
	var theform=theitem.form;
	for(var i=0;i<theReport.length;i++){
		if (theReport[i][0]==theitem.value){
			theform["reportid"].value=theReport[i][0];
			theform["reportfile"].value=theReport[i][1];
			theform["name"].value=theReport[i][2];
			theform["type"].value=theReport[i][3];
			theform["description"].value=theReport[i][4];
		}
	}
}

function showSavedSearches(option){
	var thedisplay="none";					
	var thediv=getObjectFromID("showsavedsearches");	
	if(option.value=="savedsearch") thedisplay="block";	
	thediv.style.display=thedisplay;
}

function showSortOptions(theoption){
		saveddiv=getObjectFromID("savedsortdiv");	
		singlediv=getObjectFromID("singlesortdiv");	
		switch(theoption.value){
			case "savedsort":
				saveddiv.style.display="block";
				singlediv.style.display="none";
			break;
			case "single":
				saveddiv.style.display="none";
				singlediv.style.display="block";
			break;
			case "default":
				saveddiv.style.display="none";
				singlediv.style.display="none";
			break;
		}//end case
}
