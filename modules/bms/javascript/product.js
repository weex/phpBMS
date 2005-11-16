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

function init(){
	var thepn=getObjectFromID("partname");
	thepn.focus();
}

function showWeb(thecheckbox){
	var thewebstuff=getObjectFromID("webstuff");
	if(thecheckbox.checked)
		thewebstuff.style.display="block";
	else
		thewebstuff.style.display="none";
}

function editPreviewWebDesc(thebutton){
	var editDiv=getObjectFromID("webDescEdit");
	var previewDiv=getObjectFromID("webDescPreview");
	var webDesc=getObjectFromID("webdescription");
	
	if (thebutton.value=="preview"){
		thebutton.value="edit";
		previewDiv.style.display="block";
		editDiv.style.display="none";
		previewDiv.innerHTML=webDesc.value;
	} else {
		thebutton.value="preview";
		previewDiv.style.display="none";
		editDiv.style.display="block";
	}
}

function calculateMarkUp(){
	var thecost=getObjectFromID("unitcost");
	var theprice=getObjectFromID("unitprice");
	var unitcost="";
	var unitprice="";
	
	for(i=0;i<thecost.value.length;i++){
		if (thecost.value.charAt(i)!="$" && thecost.value.charAt(i)!="+" && thecost.value.charAt(i)!=",") unitcost+=thecost.value.charAt(i);
	}
	unitcost=parseFloat(unitcost);

	for(i=0;i<theprice.value.length;i++){
		if (theprice.value.charAt(i)!="$" && theprice.value.charAt(i)!="+" && theprice.value.charAt(i)!=",") unitprice+=theprice.value.charAt(i);
	}
	unitprice=parseFloat(unitprice);

	if(unitcost!=0 && unitprice!=0){
		
		var markup=getObjectFromID("markup");
		markup.value=(Math.round((unitprice/unitcost -1)*10000)/100)+"%"
		
	}
}

function calculatePrice(){
	var themarkup=getObjectFromID("markup");
	var thecost=getObjectFromID("unitcost");
	var theprice=getObjectFromID("unitprice");
	var unitcost="";
	
	var markup=getNumberFromPercentage(themarkup.value);
	
	for(i=0;i<thecost.value.length;i++){
		if (thecost.value.charAt(i)!="$" && thecost.value.charAt(i)!="+" && thecost.value.charAt(i)!=",") unitcost+=thecost.value.charAt(i);
	}
	unitcost=parseFloat(unitcost);
	var newnumber=(Math.round((unitcost+(markup*unitcost/100))*100)/100).toString();
	theprice.value=formatDollar(newnumber);	
}

function updatePictureStatus(pic,status){
	var thechange=getObjectFromID(pic+"change");
	thechange.value=status;
}

function deletePicture(pic){
	var thepic=getObjectFromID(pic+"pic");
	var deleteDiv=getObjectFromID(pic+"delete");
	var addDiv=getObjectFromID(pic+"add");
	thepic.style.display="none";
	deleteDiv.style.display="none";
	addDiv.style.display="block";
	updatePictureStatus(pic,"delete");
}
