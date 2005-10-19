function pageInit(){
	var namecid=getObjectFromID("namecid");
	if(namecid.value!=""){
		var viewButton=getObjectFromID("dolookup");
		viewButton.disabled=false;
		viewButton.click();
	} else{
		var focusobject=getObjectFromID("ds-namecid");
		focusobject.focus();
	}
}

function updateLookup(theselect){
	var nameDiv=getObjectFromID("lookupNameLabel");
	var emailDiv=getObjectFromID("lookupEmailLabel");
	var workPhoneDiv=getObjectFromID("lookupWorkPhoneLabel");
	var homePhoneDiv=getObjectFromID("lookupHomePhoneLabel");
	var mobilePhoneDiv=getObjectFromID("lookupMobilePhoneLabel");
	var mainAddressDiv=getObjectFromID("lookupMainAddressLabel");
	nameDiv.style.display="none";
	emailDiv.style.display="none";
	workPhoneDiv.style.display="none";
	homePhoneDiv.style.display="none";
	mobilePhoneDiv.style.display="none";
	mainAddressDiv.style.display="none";
	switch(theselect.value){
		case "namecid":
			nameDiv.style.display="block";
		break;
		case "emailcid":
			emailDiv.style.display="block";
		break;
		case "workphonecid":
			workPhoneDiv.style.display="block";
		break;
		case "homephonecid":
			homePhoneDiv.style.display="block";
		break;
		case "mobilephonecid":
			mobilePhoneDiv.style.display="block";
		break;
		case "mainaddresscid":
			mainAddressDiv.style.display="block";
		break;
	}
}

function updateViewButton(){
	var lookuptype=getObjectFromID("lookupby");
	var viewButton=getObjectFromID("dolookup");
	var clientid=getObjectFromID(lookuptype.value);

	if(clientid.value=="")
		viewButton.disabled=true;
	else {
		viewButton.disabled=false;
		viewButton.focus();
	}
}
function viewClient(base){
	var lookuptype=getObjectFromID("lookupby");
	var clientid=getObjectFromID(lookuptype.value);
	if (clientid.value!=""){
		var clientrecord=getObjectFromID("clientrecord");
		clientrecord.innerHTML="<div align=\"center\"><img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Loading...</strong></div>";
		var theURL=base+"modules/bms/quickview_ajax.php?cm=showClient&id="+clientid.value+"&base="+encodeURI(base);
		loadXMLDoc(theURL,null,false);
		clientrecord.innerHTML=req.responseText;
	}
}

function selectEditEnable(theselect){
	var theditbutton=getObjectFromID(theselect.id+"edit");	
	if (theselect.value=="")
		theditbutton.firstChild.src=editButtonDisabled.src;
	else
		theditbutton.firstChild.src=editButton.src;
}

function addEditRecord(newedit,what,addeditfile){
	var theselect=getObjectFromID(what);
	var lookuptype=getObjectFromID("lookupby");
	var clientid=getObjectFromID(lookuptype.value);	
	var theURL=addeditfile;
	var currentURL=""+document.location;
	currentURL = currentURL.substring(0,currentURL.indexOf(".php")+4);
	switch(what){
		case "note":
		case "invoice":
			theURL+="?backurl="+encodeURI(currentURL+"?cid="+clientid.value)
		break;
		case "client":
			if(newedit=="edit")
				theURL+="?backurl="+encodeURI(currentURL+"?cid="+clientid.value);
			else
				theURL+="?backurl="+encodeURI(currentURL);
		break;
	}	
	if(newedit=="edit")
		if(theselect)
			theURL+="&id="+theselect.value;
		else
			theURL+="&id="+clientid.value;
	else{
		theURL+="&cid="+clientid.value;
	}
	document.location=theURL;
}