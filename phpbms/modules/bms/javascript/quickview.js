/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
 | All rights reserved.                                                    |
 +-------------------------------------------------------------------------+
 |                                                                         |
 | Redistribution and use in source and binary forms, with or without      |
 | modification, are permitted provided that the following conditions are  |
 | met:                                                                    |
 |                                                                         |
 | - Redistributions of source code must retain the above copyright        |
 |   notice, this list of conditions and the following disclaimer.         |
 |                                                                         |
 | - Redistributions in binary form must reproduce the above copyright     |
 |   notice, this list of conditions and the following disclaimer in the   |
 |   documentation and/or other materials provided with the distribution.  |
 |                                                                         |
 | - Neither the name of Kreotek LLC nor the names of its contributore may |
 |   be used to endorse or promote products derived from this software     |
 |   without specific prior written permission.                            |
 |                                                                         |
 | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS     |
 | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT       |
 | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A |
 | PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
 | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,   |
 | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT        |
 | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,   |
 | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY   |
 | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT     |
 | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE   |
 | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.    |
 |                                                                         |
 +-------------------------------------------------------------------------+
*/

window.onload= function(){
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
	selectedNote="";

	var clientrecord=getObjectFromID("clientrecord");
	comboFX=new fx.Combo(clientrecord,{height:true,opacity:true,duration:300})

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
		//clientrecord.style.visibility="hidden";
		clientrecord.innerHTML=req.responseText;
		var goalHeight=clientrecord.offsetHeight;
		comboFX.hide();
		comboFX.toggle();
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
			theid=selectedNote;
		break;
		case "invoice":
			theid=selectedInvoice;
		break;
		case "client":
			theid=clientid.value;
		break;
	}	
	theURL+="?backurl="+encodeURIComponent(currentURL+"?cid="+clientid.value)
	if(newedit=="edit")
		theURL+="&id="+theid;
	else
		theURL+="&cid="+clientid.value;
	document.location=theURL;
}

function selectEdit(thetr,id,noteinvoice){
	var theeditbutton=getObjectFromID(noteinvoice+"edit");	
	var theSelected;
	if(noteinvoice=="note")
		theSelected=selectedNote;
	else
		theSelected=selectedInvoice;
	
	if(theSelected==id){
		theeditbutton.className="graphicButtons buttonEditDisabled";
		theSelected="";
		thetr.className=""
	} else {
		for(var i=0; i<thetr.parentNode.childNodes.length;i++){
			if(thetr.parentNode.childNodes[i].tagName)
				if(thetr.parentNode.childNodes[i].tagName=="TR")
					thetr.parentNode.childNodes[i].className=""
		}
		thetr.className="smallQueryTableSelected";
		theSelected=id;
		theeditbutton.className="graphicButtons buttonEdit";
	}
	if(noteinvoice=="note")
		selectedNote=theSelected;
	else
		selectedInvoice=theSelected;
	
}