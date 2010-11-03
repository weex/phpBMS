/*
 $Rev: 308 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-09-17 12:30:10 -0600 (Mon, 17 Sep 2007) $
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
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
aritem = {
	
	viewRecord: function(e){
		
		var type = getObjectFromID("type");
		var relatedid = getObjectFromID("editrelatedid")
		var id = getObjectFromID("id");
		
		var invoiceEdit = getObjectFromID("invoiceEdit");
		var receiptEdit = getObjectFromID("receiptEdit");		
		
		var theURL = "";
		
		if(type.value == "credit")
			theURL += receiptEdit.value;
		else
			theURL += invoiceEdit.value;
			
		theURL += "?id=" + relatedid.value + "&backurl=" + encodeURIComponent(APP_PATH + "modules/bms/aritems_view.php?id=" + id.value);
		document.location = theURL;
		
	},//end method
	
	viewClient: function(e) {
		
		var clientEdit = getObjectFromID("clientEdit");
		var clientID = getObjectFromID("clientid");
		var id = getObjectFromID("id");
		
		var theURL = clientEdit.value + "?id=" + clientID.value + "&backurl=" + encodeURIComponent(APP_PATH + "modules/bms/aritems_view.php?id=" + id.value)
		document.location = theURL;
		
	}//end method
	
}//end class


/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {
								 
	//disable saves
	var saveButton = getObjectFromID("saveButton1");
	saveButton.className = "disabledButtons";
	saveButton.disabled = true;
	
	saveButton = getObjectFromID("saveButton2");
	saveButton.className = "disabledButtons";
	saveButton.disabled = true;
	
	var viewRelatedButton = getObjectFromID("viewRelatedButton");
	connect(viewRelatedButton, "onclick", aritem.viewRecord);
	
	var viewClient = getObjectFromID("viewClient")
	connect(viewClient, "onclick", aritem.viewClient)
								 
})