function init(){
	var acc1=getObjectFromID("accordianSec1");
	var acc2=getObjectFromID("accordianSec2");
	var acc3=getObjectFromID("accordianSec3");
	var accContainer=getObjectFromID("accordian");
	accordianHeight=Math.max(acc1.offsetHeight,acc2.offsetHeight,acc3.offsetHeight);
	acc1.style.display="none";
	acc2.style.display="none";
	accContainer.style.height=accContainer.offsetHeight+"px";
}

function accordian(theimg,accordianName,numSlats){
	var clickedID=theimg.id.substr(theimg.id.length-1,1);
	var theDiv,theImg,openID;
	for(var i=1;i<=numSlats;i++){
		theDiv=getObjectFromID(accordianName+"Sec"+i);
		theImg=getObjectFromID(accordianName+"Img"+i);
		if(i==clickedID){
			theDiv.style.height="0px";
			theDiv.style.display="block";
		} else {
			if(theDiv.style.display=="block"){
				openID=i;
			}
		}
	}//end for
	switchAccordian(clickedID,openID,accordianName);
}
function switchAccordian(toID,fromID,accordianName){
	var fromDiv=getObjectFromID(accordianName+"Sec"+fromID);
	var toDiv=getObjectFromID(accordianName+"Sec"+toID);


	if(toDiv.offsetHeight<accordianHeight){
			var step=Math.round(accordianHeight/8);
			fromDiv.style.height=Math.abs(fromDiv.offsetHeight-step)+"px";
			toDiv.style.height=Math.abs(toDiv.offsetHeight+step)+"px";
			setTimeout("switchAccordian("+toID+","+fromID+",'"+accordianName+"')",40);
	} else {
		toDiv.style.height=accordianHeight+"px";
		fromDiv.style.display="none";
		var toImg=getObjectFromID(accordianName+"Img"+toID);
		toImg.parentNode.style.display="none";
		var fromImg=getObjectFromID(accordianName+"Img"+fromID);
		fromImg.parentNode.style.display="block";
	}
}

function hideSection(theimg,sectionid){
	var thediv=getObjectFromID(sectionid);
	if (thediv.style.display=="none"){
		thediv.style.display="block";
		theimg.src=chevronup.src;
	}
	else {
		thediv.style.display="none";
		theimg.src=chevrondown.src;
	}
}

function showContent(id){
	thediv=getObjectFromID("SMT"+id);
	thegraphic=getObjectFromID("SMG"+id);
	if (thediv){
		if(thediv.style.display=="block"){
			thediv.style.display="none";
			thegraphic.src="../../common/image/left_arrow.gif";
		} else {
			thediv.style.display="block";
			thegraphic.src="../../common/image/down_arrow.gif";
		}
	}
}

function checkTask(id,type){
	var thediv=getObjectFromID("TS"+id);
	var thecheckbox=getObjectFromID("TSC"+id);
	var isprivate=getObjectFromID("TSprivate"+id);
	var ispastdue=getObjectFromID("TSispastdue"+id);
	
	var theURL="snapshot_ajax.php?id="+id+"&ty="+type+"&cm=updateTask&cp=";
	if(thecheckbox.checked){
		theURL+="1";
		thediv.className="small taskCompleted";
	} else {
		theURL+="0";
		var classname=" small task";
		if(isprivate.value==1)
			classname+=" taskPrivate";
		if(ispastdue.value==1)
			classname="small taskPastDue";
		thediv.className=classname;
	}
	loadXMLDoc(theURL,null,false);
	if(req.responseText!="success")
		alert("Error: <br>"+req.responseText);	
	
}