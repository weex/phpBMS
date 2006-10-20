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
//Set up all switching array
theReport=new Array();


window.onload=function(){
	var optionsDivs = new Array();
	optionsDivs[optionsDivs.length]=getObjectFromID("moreoptions");

	var optionsLinks = new Array();
	optionsLinks[optionsLinks.length]=getObjectFromID("showoptions");

	var optionsAccordion = new fx.Accordion(optionsLinks, optionsDivs, {opacity: true, duration:250, onComplete:function(){switchOptions()}});	
}

function switchOptions(){
	var switchButton=getObjectFromID("showoptions");
	if(switchButton.className=="graphicButtons buttonDown"){
		switchButton.className="graphicButtons buttonUp"
		switchButton.firstChild.innerHTML="less options";
	} else {
		switchButton.className="graphicButtons buttonDown"
		switchButton.firstChild.innerHTML="more options";
	}
}
function switchReport(theitem){
	var theform=theitem.form;
	for(var i=0;i<theReport.length;i++){
		if (theReport[i][0]==theitem.value){
			theform["reportid"].value=theReport[i][0];
			theform["reportfile"].value=theReport[i][1];
			theform["name"].value=theReport[i][2];
			theform["type"].value=theReport[i][3];
			theform["description"].value=theReport[i][4];
		}
	}
}

function showSavedSearches(option){
	var thedisplay="none";					
	var thediv=getObjectFromID("showsavedsearches");	
	if(option.value=="savedsearch") thedisplay="block";	
	thediv.style.display=thedisplay;
}

function showSortOptions(theoption){
		saveddiv=getObjectFromID("savedsortdiv");	
		singlediv=getObjectFromID("singlesortdiv");	
		switch(theoption.value){
			case "savedsort":
				saveddiv.style.display="block";
				singlediv.style.display="none";
			break;
			case "single":
				saveddiv.style.display="none";
				singlediv.style.display="block";
			break;
			case "default":
				saveddiv.style.display="none";
				singlediv.style.display="none";
			break;
		}//end case
}

function showMoreOptions(thelink){
	var linkGraphic=getObjectFromID("moreOptionsGraphic");
	var theDiv=getObjectFromID("moreoptions");
	if(thelink.lastChild.data==" more options"){
		thelink.lastChild.data=" less options";
		linkGraphic.src=buttonUp.src;
		theDiv.style.display="block";
	}
	else {
		thelink.lastChild.data=" more options";
		linkGraphic.src=buttonDown.src;
		theDiv.style.display="none";
	}
}
