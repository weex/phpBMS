/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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

bmsSnapshot = {

	infoPress: function(e){
		objSrc = e.src();
		
		id = objSrc.id.substr(3);
		
		document.location = "../../search.php?id="+id;
		
	},//end method
	
	receiptLink: function(e){
		
		var row = e.src();
		var theid = row.id.substr(7);
		
		var addedit = getObjectFromID("receiptEdit");
		
		theURL = addedit.value + "?id=" + theid;
		document.location = theURL;
		
	}//end method
	
}//end class


/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

	var invoiceDivs = getElementsByClassName('invoiceDivs');
	var invoiceLinks = getElementsByClassName('invoiceLinks');

	var invoiceAccordion = new fx.Accordion(invoiceLinks, invoiceDivs, {opacity: true, duration:300});

	var clientDivs = getElementsByClassName('clientDivs');
	var clientLinks = getElementsByClassName('clientLinks');

	var clientAccordion = new fx.Accordion(clientLinks, clientDivs, {opacity: true, duration:300});

	var arDivs = getElementsByClassName('arDivs');
	var arLinks = getElementsByClassName('arLinks');

	var arAccordion = new fx.Accordion(arLinks, arDivs, {opacity: true, duration:300});

	invoiceAccordion.showThisHideOpen(invoiceDivs[0]);
	clientAccordion.showThisHideOpen(clientDivs[0]);
	
	var bmsButtons = getElementsByClassName('bmsInfo');
	for(var i=0; i<bmsButtons.length; i++)
		connect(bmsButtons[i],"onclick",bmsSnapshot.infoPress);

	var receipts = getElementsByClassName('receiptLinks');
	for(i=0; i<receipts.length; i++)
		connect(receipts[i], "onclick", bmsSnapshot.receiptLink);


})