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

function checkTask(id,typw){
	var thediv=getObjectFromID("TS"+id);
	var thecheckbox=getObjectFromID("TSC"+id);
	var isprivate=getObjectFromID("TSprivate"+id);
	var ispastdue=getObjectFromID("TSispastdue"+id);
	
	var theURL="snapshot_xml.php?id="+id+"&ty="+type+"&cm=updateTask&cp=";
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
	alert(theURL);
	loadXMLDoc(theURL,null,false);
	if(req.responseText!="success")
		alert(req.responseText);	
	
}