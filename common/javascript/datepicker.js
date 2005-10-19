function showDP(base,dateFieldID){
	var dateField= getObjectFromID(dateFieldID);
	
	//check to see if a nother box is already showing
	var alreadybox=getObjectFromID("DPCancel");
	if(alreadybox) closeDPBox();

	//get positioning
	var thetop=getTop(dateField)+dateField.offsetHeight;
	var theleft=getLeft(dateField);
	if (theleft+140 > window.innerWidth)
		theleft= theleft-140+dateField.offsetWidth-15;

	showDP.box=document.createElement("div");
	showDP.box.className="bodyline";
	showDP.box.style.siplay="block";
	showDP.box.style.position="absolute";
	showDP.box.style.top=thetop + "px";
	showDP.box.style.left=theleft + "px";
	
	showDP.datefieldID=dateFieldID;
	
	if(dateField.value){
		selDate=stringToDate(dateField.value);
		year=selDate.getFullYear();
		month=selDate.getMonth()
	} else{
		
		selDate=null;
		tdate=new Date();
		year=tdate.getFullYear();
		month=tdate.getUTCMonth()+1;
	}
	loadMonth(base,month,year,dateField.value);
	hideSelectBoxes();
	document.body.appendChild(showDP.box);
}

function loadMonth(base,month,year,selectedDate){
	var content="<div align=\"center\"><img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Loading...</strong></div>";	
	showDP.box.innerHTML=content;
	var theURL=base+"datepicker.php?cm=shw";
	theURL+="&m="+encodeURI(month);
	theURL+="&y="+encodeURI(year);
	if (selectedDate){
		tempdate=stringToDate(selectedDate);
		theURL+="&sd="+encodeURI(tempdate.getFullYear()+"-"+tempdate.getMonth()+"-"+tempdate.getDate());
	}
	loadXMLDoc(theURL,null,false);
	showDP.box.innerHTML=req.responseText;	
}


function closeDPBox(){
	document.body.removeChild(showDP.box);
	displaySelectBoxes();
	showDP.box=null;	
	showDP.datefieldID=null;	
}

function dpClickDay(year,month,day){
	var thefield=getObjectFromID(showDP.datefieldID);
	thefield.value=month+"/"+day+"/"+year;
	if(thefield.onchange) thefield.onchange.call(thefield);
	closeDPBox();
}

function dpHighlightDay(year,month,day){
	var displayinfo=getObjectFromID("dpExp");
	var months=Array("January","February","March","April","May","June","July","August","September","October","November","December");
	displayinfo.innerHTML=months[month-1]+" "+day+", "+year;
}

function stringToDate(sDate){
	var sep="/";
	var month=sDate.substring(0,sDate.indexOf(sep))
	var day=sDate.substring(sDate.indexOf(sep)+1,sDate.indexOf(sep,sDate.indexOf(sep)+1))
	var year=sDate.substring(sDate.lastIndexOf(sep)+1);
	return new Date(year,month,day);
}

function mysqlstringToDate(sDate){
	var sep="-";
	var year=sDate.substring(0,sDate.indexOf(sep))
	var month=sDate.substring(sDate.indexOf(sep)+1,sDate.indexOf(sep,sDate.indexOf(sep)+1))
	var day=sDate.substring(sDate.lastIndexOf(sep)+1);
	
	return Date(year,month,day);
}