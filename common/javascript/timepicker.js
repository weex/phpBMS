function showTP(base,timeFieldID){
	var dateField= getObjectFromID(timeFieldID);
	
	//check to see if a nother box is already showing
	var alreadybox=getObjectFromID("TPmoreless");
	if(alreadybox) closeTPBox();

	//get positioning
	var thetop=getTop(dateField)+dateField.offsetHeight;
	var theleft=getLeft(dateField);
	if (theleft+230 > window.innerWidth)
		theleft= theleft-230+dateField.offsetWidth-15;

	showTP.box=document.createElement("div");
	showTP.box.className="bodyline";
	showTP.box.style.siplay="block";
	showTP.box.style.position="absolute";
	showTP.box.style.top=thetop + "px";
	showTP.box.style.left=theleft + "px";
	
	showTP.timeField=timeFieldID;

	document.body.appendChild(showTP.box);
	var content="<div align=\"center\"><img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Loading...</strong></div>";	
	showTP.box.innerHTML=content;

	var theURL=base+"timepicker.php?cm=shw";
	loadXMLDoc(theURL,null,false);
	showTP.box.innerHTML=req.responseText;	

}

function switchMinutes(thebutton){
	var lessmin=getObjectFromID("tpMinuteLess");
	var moremin=getObjectFromID("tpMinuteMore");
	if(thebutton.value=="more"){
		thebutton.value="less";		
		lessmin.style.display="none";
		moremin.style.display="table";
	}
	else {
		thebutton.value="more";
		lessmin.style.display="table";
		moremin.style.display="none";
	}
}

function closeTPBox(){
	document.body.removeChild(showTP.box);
	showTP.box=null;	
	showTP.timeFieldID=null;	
}

function tpClickHour(hour){
	var timeField=getObjectFromID(showTP.timeField);
	var ampm;
	if(!validateTime(timeField.value)) timeField.value="12:00 AM";
	
	var minutes=timeField.value.substr(timeField.value.indexOf(":")+1,2)
	
	if(hour>11) {ampm=" PM"; hour=hour-12}else ampm=" AM";
	if (hour==0) hour=12;
	
	timeField.value=hour+":"+minutes+ampm;
	
}
function tpClickMinute(thetd){
	var minutes=thetd.innerHTML;
	var timeField=getObjectFromID(showTP.timeField);
	if(!validateTime(timeField.value)) timeField.value="12:00 AM";

	var hours=timeField.value.substring(0,timeField.value.indexOf(":"));
	var ampm=timeField.value.substr(timeField.value.indexOf(" "));
	timeField.value=hours+minutes+ampm;
	closeTPBox();
}


