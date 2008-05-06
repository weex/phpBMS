/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2007, Kreotek LLC                                  |
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

function addClient(){
	var clientaddedit = getObjectFromID("addeditfile");
	addEditRecord("new","client",clientaddedit.value);
}

function addEditRecord(newedit,what,addeditfile){

	var clientid=getObjectFromID("clientid");	
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
		theeditbutton.disabled = true;
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
		theeditbutton.disabled = false;
	}
	if(noteinvoice=="note")
		selectedNote=theSelected;
	else
		selectedInvoice=theSelected;
	
}






quickView = {

	changeLookup: function(e){
		
		var dropDown = e.src();
		var smartSearchID = getObjectFromID("sdbid-clientid");
		
		smartSearchID.value = dropDown.value;
		
	},//end method - changeLookup
	
	
	changeClient: function(e){
		
		var clientid = e.src();
		var viewButton = getObjectFromID("viewButton");
		
		if(clientid.value)
			viewButton.className = "Buttons"
		else
			viewButton.className = "dsiabledButtons";
		
	},//end method - changeClient
	

	viewClient: function(){
		
		var clientid = getObjectFromID("clientid");
		var viewButton = getObjectFromID("viewButton");
		
		if (clientid.value != "" && viewButton.className != "disabledButtons"){
			
			var clientrecord = getObjectFromID("clientrecord");
			
			clientrecord.innerHTML = '<div align="center"><img src="' + APP_PATH + 'common/image/spinner.gif" alt="0" width="16" height="16" align="absmiddle"><strong>Loading...</strong></div>';

			var theURL = APP_PATH + "modules/bms/quickview_ajax.php?cm=showClient&id=" + clientid.value;
			
			loadXMLDoc(theURL,null,false);

			clientrecord.innerHTML = req.responseText;
			
			var goalHeight = clientrecord.offsetHeight;
			
			if(document.comboFX){
				
				document.comboFX.hide();
				document.comboFX.toggle();
				
			}//endif
			
		}//endif - clientid
		
	}//end method - viewClient

}//end class

/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

	var clientrecord = getObjectFromID("clientrecord");
	document.comboFX = new fx.Combo(clientrecord,{height:true,opacity:true,duration:300});

	var lookupby = getObjectFromID("lookupby");
	connect(lookupby, "onchange", quickView.changeLookup);

	var clientid = getObjectFromID("clientid");
	connect(clientid, "onchange", quickView.changeClient);
	
	if(clientid.value != ""){
		
		var viewButton = getObjectFromID("viewButton");

		viewButton.className = "Buttons";

		quickView.viewClient();
		
	} else{
		
		var focusobject = getObjectFromID("ds-clientid");

		focusobject.focus();
		
	}//endif - clientid
	
	var viewButton = getObjectFromID("viewButton");
	connect(viewButton, "onclick", quickView.viewClient)
	
	var addButton = getObjectFromID("addButton");
	connect(addButton, "onclick", addClient);
	
	selectedInvoice = "";
	selectedNote = "";
	
})