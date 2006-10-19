/*
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

//this function opens a page in a new windoe that will lookup and populate the add line item info based on a choosen partnumber
function populateLineItem(){
	if (this.value!=""){
		var theurl="prereqitemlookup.php?id="+this.value;

		loadXMLDoc(theurl,null,false);
		response = req.responseXML.documentElement;
		
		for(i=0;i<response.getElementsByTagName('field').length;i++){
			theitem=this.form[response.getElementsByTagName('field')[i].firstChild.data];
			if(!theitem) alert(response.getElementsByTagName('field')[i].firstChild.data);
			thevalue="";
			if(response.getElementsByTagName('value')[i].firstChild)
				thevalue=response.getElementsByTagName('value')[i].firstChild.data;
			theitem.value=thevalue;
			if(theitem.onchange && theitem.name=="price") theitem.onchange();
		}				
	}
	return true;
}

//This function set the line item to be deleted
function deleteLine(theid){
	var deleteid=getObjectFromID("deleteid");
	var thecommand=getObjectFromID("command");
	
	deleteid.value=theid;
	thecommand.value="delete";
	thecommand.form.submit();
	return true;
}

function addLine(){
	var thecommand=getObjectFromID("command");
	
	thecommand.value="add";
	thecommand.form.submit();
	return true;
}