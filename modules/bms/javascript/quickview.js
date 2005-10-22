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
	selectedInvoice="";
	selectedNote=""
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
		var theURL=base+"modules/bms/quickview_ajax.php?cm=showClient&id="+clientid.value+"&base="+encodeURIComponent(base);
		loadXMLDoc(theURL,null,false);
		clientrecord.innerHTML=req.responseText;
	}
}

function addEditRecord(newedit,what,addeditfile){
	var lookuptype=getObjectFromID("lookupby");
	var clientid=getObjectFromID(lookuptype.value);	
	var theURL=addeditfile;
	var currentURL=""+document.location;
	currentURL = currentURL.substring(0,currentURL.indexOf(".php")+4);
	var theid="";
	switch(what){
		case "note":
			theURL+="?backurl="+encodeURIComponent(currentURL+"?cid="+clientid.value)
			theid=selectedNote;
		break;
		case "invoice":
			theURL+="?backurl="+encodeURIComponent(currentURL+"?cid="+clientid.value)
			theid=selectedInvoice;
		break;
		case "client":
			if(newedit=="edit")
				theURL+="?backurl="+encodeURIComponent(currentURL+"?cid="+clientid.value);
			else
				theURL+="?backurl="+encodeURIComponent(currentURL);
		break;
	}	
	if(newedit=="edit")
		if(theid!="")
			theURL+="&id="+theid;
		else
			theURL+="&id="+clientid.value;
	else{
		theURL+="&cid="+clientid.value;
	}
	document.location=theURL;
}

function selectEdit(thetr,id,noteinvoice){
	var theeditbutton=getObjectFromID(noteinvoice+"edit");	
	if(selectedInvoice==id){
		theeditbutton.firstChild.src=editButtonDisabled.src;
		if(noteinvoice="note")
			selectedNote="";
		else
			selectedInvoice="";
		thetr.className=""
	} else {
		for(var i=0; i<thetr.parentNode.childNodes.length;i++){
			if(thetr.parentNode.childNodes[i].tagName)
				if(thetr.parentNode.childNodes[i].tagName=="TR")
					thetr.parentNode.childNodes[i].className=""
		}
		thetr.className="smallQueryTableSelected";
		if(noteinvoice="note")
			selectedNote=id;
		else
			selectedInvoice=id;
		theeditbutton.firstChild.src=editButton.src;
	}
}