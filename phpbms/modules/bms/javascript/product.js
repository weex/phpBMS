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
window.onload=function(){
	
	var webDivs = new Array();
	webDivs[webDivs.length]=getObjectFromID("webstuff");

	var webLinks = new Array();
	webLinks[webLinks.length]=getObjectFromID("webenabled");

	var webAccordion = new fx.Accordion(webLinks, webDivs,{opacity: false, duration:200});	
	if(webLinks[0].checked)
		webAccordion.showThisHideOpen(webDivs[0]);

	var thepn=getObjectFromID("partname");
		thepn.focus();
		
	if(numcats==0){
		var title="No Product Categories Created";
		var content="You need to assign new products to a product category. Currently, there ";
		content+="are no product categories created.<br /><br />";
		content+="<strong>Would you like to create a product category now?</strong><br /><br />";
		content+="<div align=\"right\"><button class=\"Buttons\" type=\"button\" style=\"width:75px;margin-right:2px;\" onclick=\"goProductCategories()\"><span>yes</span></button>";
		content+="<button class=\"Buttons\" type=\"button\" style=\"width:75px;\" onclick=\"noProductCategories()\"><span>no</span></button></div>";
		showModal(content,title,"300");
	}
}
function goProductCategories(){
	closeModal();
	document.location="productcategories_addedit.php";
}
function noProductCategories(){
	closeModal();
	var cancelButton=getObjectFromID("cancelButton1");
	cancelButton.click();
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
	
	unitcost=currencyToNumber(thecost.value);
	unitprice=currencyToNumber(theprice.value);

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
	
	unitcost=currencyToNumber(thecost.value);
	var newnumber=(Math.round((unitcost+(markup*unitcost/100))*100)/100).toString();
	theprice.value=formatCurrency(newnumber);	
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
