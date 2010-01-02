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
window.onload=function(){

	var sqlbttn=getObjectFromID("showSQLButton");;

	if(sqlbttn){
		var sqlDivs = new Array();
		sqlDivs[sqlDivs.length]=getObjectFromID("sqlstatement");

		var sqlLinks = new Array();
		sqlLinks[sqlLinks.length]=sqlbttn;

		var sqlAccordion = new fx.Accordion(sqlLinks, sqlDivs, {opacity: true, duration:250, onComplete:function(){switchSqlButtons()}});
	}
}
function switchSqlButtons(){
	var sqlbutton=getObjectFromID("showSQLButton");
	if (sqlbutton.className=="sqlUp")
		sqlbutton.className="sqlDn"
	else
		sqlbutton.className="sqlUp";

return false;
}


function openWindow(theURL,winName,features) {
	  window.open(theURL,winName,features);
}


//click on row
function clickIt(theTR,theevent,disablectrl){
	var ctrlkeydown=false;
	var shiftkeydown=false;
	// stupid browser incompatibilities...//
	if(!disablectrl) {
		if (navigator.userAgent.toLowerCase().indexOf("msie")!=-1)
			theevent=window.event
		ctrlkeydown=theevent.ctrlKey;
		shiftkeydown=theevent.shiftKey;
	}

	if(!ctrlkeydown && !shiftkeydown) {
		selIDs= new Array();
		var theTable=theTR.parentNode;
		for(i=0;i<theTable.childNodes.length;i++){
			if (theTable.childNodes[i]!=theTR && theTable.childNodes[i].className){
				if(theTable.childNodes[i].className != "queryGroup")
					theTable.childNodes[i].className="qr"+theTable.childNodes[i].className.charAt(theTable.childNodes[i].className.length-1);
			}
		}
	}

	var theID=theTR.id.substr(2,theTR.id.length-1);

	if(!ctrlkeydown && shiftkeydown){
		//Need to program shift click in here!
		var curID = null;
		var theTable = theTR.parentNode;
		var searchArray = "+"+selIDs.join("+")+"+";
		var point1 = null;
		var point2 = null;

		for(i=0;i<theTable.childNodes.length;i++){
			if (theTable.childNodes[i].className){
				if(theTable.childNodes[i].className != "queryGroup"){
					theTable.childNodes[i].className = "qr"+theTable.childNodes[i].className.charAt(theTable.childNodes[i].className.length-1);
					curID = theTable.childNodes[i].id.substr(2);
					if(curID == theID)
						point1 = i;
					if(searchArray.indexOf("+"+curID+"+") != -1 && point2==null) {
						point2 = i;
					}
				}
			}
		}
		var theStart;
		var theStop;
		if (point1>point2){
			theStart=point2;
			theStop=point1
		} else {
			theStart=point1;
			theStop=point2
		}
		selIDs= new Array();
		for(i=theStart;i<=theStop;i++){
			if (theTable.childNodes[i].className){
				if(theTable.childNodes[i].className != "queryGroup"){
					theTable.childNodes[i].className="qh"+theTable.childNodes[i].className.charAt(theTable.childNodes[i].className.length-1);
					selIDs[selIDs.length]=theTable.childNodes[i].id.substring(2);
				}
			}
		}

	} else {
		// need to find the checkbox that the TR contains
		var newClass="";
		var i;
		if (theTR.className.charAt(1)=="h"){
			//highlighted... unhighlight it
			newClass="qr"+theTR.className.charAt(theTR.className.length-1);
			for(i=0;i<selIDs.length;i++)
				if(selIDs[i]==theTR.id.substring(2)) selIDs.splice(i,1);
		} else {
			//highlight it
			newClass="qh"+theTR.className.charAt(theTR.className.length-1);
			selIDs[selIDs.length]=theTR.id.substring(2);
		}
		theTR.className=newClass;

		var disabledstatus=(selIDs.length==0);
		setButtonStatus(disabledstatus);
	}
}

function setSelIDs(theform){
	if(selIDs.length)
		theform["theids"].value=selIDs.join(",");
}

function chooseOtherCommand(thevalue, thetext, thesource){

	if(!thesource.className.match(/Disabled/)){

		switch(thevalue){
			case "-1":
				var thediv = getObjectFromID("otherDropDown");
				var confirmcommand=thetext;
				confirmDelete(confirmcommand);
				thediv.style.display = "none";
				break;

			case "-2":
				importRecord();
				break;

			default:
				var otherField = getObjectFromID("othercommands");
				setSelIDs(otherField.form);
				otherField.value = thevalue;
				otherField.form.submit();
				break;
		}//end switch

	}//end if

}

function confirmDelete(deletename){
	var deleteButton = getObjectFromID("deleteRecord");
	if(deleteButton)
		if(deleteButton.className == "deleteRecordDisabled")
			return false;

		var howmany=selIDs.length+" selected record";

		if(selIDs.length!=1)
			howmany+="s"
		var content="<div>Are you sure you want to "+deletename+" the "+howmany+"?</div>";
			content+="<div align=\"right\"><input type=\"button\" class=\"\Buttons\" style=\"width:75px;margin-right:2px;\" value=\"yes\" onclick=\"doDelete()\" /><input type=\"button\" class=\"\Buttons\" style=\"width:75px;\" value=\"no\" onclick=\"closeModal()\" /></div>"
		showModal(content,"Confirm",300) ;
}


function doPrint(){
	var doprint = getObjectFromID("doprint");
	doprint.value = "print";
	setSelIDs(doprint.form);
	doprint.form.submit();
}



function doDelete(){
	var thedelete=getObjectFromID("deleteCommand");
	thedelete.value="delete";
	setSelIDs(thedelete.form);
	thedelete.form.submit();
}


function setButtonStatus(disabledstatus){
	var editButton=getObjectFromID("editRecord");
	var printButton=getObjectFromID("print");
	var deleteButton=getObjectFromID("deleteRecord");
	//var otherCommands=getObjectFromID("otherCommandButton");
	var relationship=getObjectFromID("relationship");
	var select = getElementsByClassName("needselect");
	if(!select.length)
		var select = getElementsByClassName("needselectDisabled");

	if(editButton)
		editButton.className="editRecord"+((disabledstatus)?"Disabled":"");

	if(deleteButton)
		deleteButton.className = "deleteRecord"+((disabledstatus)?"Disabled":"");


	for(i = 0;i < select.length; i++)
		select[i].className = "needselect"+((disabledstatus)?"Disabled":"");


	//if(otherCommands) {
	//	otherCommands.className = "otherCommands"+((disabledstatus)?"Disabled":"");
	//	var otherDropDown = getObjectFromID("otherDropDown");
	//	otherDropDown.style.display = "none";
	//}

	if(relationship) relationship.disabled=disabledstatus;
}


function editButton(){
	var editButton=getObjectFromID("editRecord");
	if(editButton.className == "editRecord")
		editThis();

return false;
}

//double click on row
function editThis(therow){
	var connector;

	if(therow){
		// the row is used for doubleclicking
		var therownum=therow.id.substr(2);
		selIDs=new Array();
		selIDs[0]=therownum;
	}
	if(editFile.indexOf("N/A")==-1){
		if (editFile.indexOf("?")>=0)
			connector="&";
		else
			connector="?";
		editFile+=connector+"id="+selIDs[0];
		if(typeof xtraParamaters != "undefined")
			editFile+="&"+(xtraParamaters);

		document.location=editFile;
	}
}//end function

function addRecord(){
	var connector;
	if (addFile.indexOf("?")>=0)
		connector="&";
	else
		connector="?";
	if(typeof xtraParamaters != "undefined")
		addFile+=connector+(xtraParamaters);
	document.location=addFile;
}

function importRecord(){
	var connector;
	if (importFile.indexOf("?")>=0)
		connector="&";
	else
		connector="?";
	if(typeof xtraParamaters != "undefined")
		importFile+=connector+(xtraParamaters);
	document.location=importFile;
}

// Select All/None Button
function selectRecords(allornone){
	if(allornone=="All") allornone=false; else allornone=true;
	var newClass="qr";
	if(!allornone) newClass="qh";
	selIDs= new Array();

	var tbody = getObjectFromID("resultTbody");
	for(var i=0;i<tbody.childNodes.length;i++){
		if(tbody.childNodes[i].className){
			if(tbody.childNodes[i].className != "queryGroup"){
				tbody.childNodes[i].className=newClass+tbody.childNodes[i].className.charAt(tbody.childNodes[i].className.length-1);
				if(!allornone)
					selIDs[selIDs.length] = tbody.childNodes[i].id.substring(2);
			}
		}
	}
	setButtonStatus(allornone);
}

// Pass the Sort Parameters and submit the form
function doSort(i){
	var theform=document.forms["search"];
	theform["newsort"].value=theform["sortit"+i].value;
	theform.submit();
	return false;
}

// Pass the Sort Parameters and submit the form
function doDescSort(){
	theform=document.forms["search"];
	theform["desc"].value="desc";
	theform.submit();
	return false;
}

function setMainFocus(){
	var startswithfield=getObjectFromID("startswith");
	startswithfield.focus();
}


function showDropDown(whatDD){

	var otherDD;
	var thediv = getObjectFromID(whatDD);

	if(whatDD == "searchSelectionDropDown")
		otherDD = getObjectFromID("otherDropDown");
	else
		otherDD = getObjectFromID("searchSelectionDropDown");

	if(thediv.style.display == "none")
		thediv.style.display = "block";
	else
		thediv.style.display = "none";

	if(otherDD)
		otherDD.style.display = "none";
}


function performToSelection(option){

	var thereset=getObjectFromID("reset");
	var thediv = getObjectFromID("searchSelectionDropDown");

	switch(option){
		case "selectall":
			selectRecords("All");
		break;
		case "selectnone":
			selectRecords("None");
		break;
		case "keepselected":
			if(selIDs.length>0){
				thereset.value="keep"
				thereset.click();
			} else {
			alert("You must select records first.");
			}
		break;
		case "omitselected":
			if(selIDs.length>0){
				thereset.value="omit"
				thereset.click();
			} else {
			alert("You must select records first.");
			}
		break;
	}
	thediv.style.display = "none";

}



function switchSearchTabs(taba,base){
	if(taba.parentNode.className=="tabsSel")
		return false;

	var basicTab=getObjectFromID("basicSearchT");
	var advancedTab=getObjectFromID("advancedSearchT");
	var loadTab=getObjectFromID("loadSearchT");
	var saveTab=getObjectFromID("saveSearchT");
	var sortTab=getObjectFromID("advancedSortT");

	basicTab.className="";
	if(advancedTab) advancedTab.className="";
	loadTab.className="";
	saveTab.className="";
	sortTab.className="";

	taba.parentNode.className="tabsSel";

	basicTab=getObjectFromID("basicSearchTab");
	advancedTab=getObjectFromID("advancedSearchTab");
	loadTab=getObjectFromID("loadSearchTab");
	saveTab=getObjectFromID("saveSearchTab");
	sortTab=getObjectFromID("advancedSortTab");

	basicTab.style.display="none";
	if(advancedTab) advancedTab.style.display="none";
	loadTab.style.display="none";
	saveTab.style.display="none";
	sortTab.style.display="none";

	var theURL;
	switch(taba.parentNode.id){
		case "basicSearchT":
			basicTab.style.display="block";
		break;
		case "advancedSearchT":
			if(advancedTab.innerHTML==""){
				var tabledefid=getObjectFromID("tabledefid");
				advancedTab.innerHTML="<img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Loading...</strong>";
				advancedTab.style.display="block";
				theURL=base+"advancedsearch.php?cmd=show&base="+encodeURIComponent(base)+"&tid="+tabledefid.value;
				loadXMLDoc(theURL,null,false);
				advancedTab.innerHTML=req.responseText;
				ASParams= [1];
			} else
			advancedTab.style.display="block";
		break;
		case "loadSearchT":
			if(loadTab.innerHTML==""){
				var tabledefid=getObjectFromID("tabledefid");
				loadTab.innerHTML="<img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Loading...</strong>";
				loadTab.style.display="block";
				theURL=base+"loadsearch.php?cmd=show&base="+encodeURIComponent(base)+"&tid="+encodeURIComponent(tabledefid.value);
				loadXMLDoc(theURL,null,false);
				loadTab.innerHTML=req.responseText;
			} else
			loadTab.style.display="block";
		break;
		case "saveSearchT":
			var searchbutton=getObjectFromID("saveSearch");
			var searchtext=getObjectFromID("saveSearchName");
			var searchstatus=getObjectFromID("saveSearchReults");
			if(searchstatus.innerHTML!=""){
				searchbutton.disabled=true;
				searchtext.value="";
				searchstatus.className="";
				searchstatus.innerHTML="";
			}
			saveTab.style.display="block";
		break;
		case "advancedSortT":
			if(sortTab.innerHTML==""){
				var tabledefid=getObjectFromID("tabledefid");
				sortTab.innerHTML="<img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Loading...</strong>";
				sortTab.style.display="block";
				theURL=base+"advancedsort.php?cmd=show&base="+encodeURIComponent(base)+"&tid="+tabledefid.value;
				loadXMLDoc(theURL,null,false);
				sortTab.innerHTML=req.responseText;
				SortParams= [1];
				updateSort();
			} else
			sortTab.style.display="block";
		break;
	}
}


// Advanced Search Functions ==========================================
function updateAS(){
	var tempMinus;
	if(ASParams.length>1){
		tempMinus=getObjectFromID("ASC"+ASParams[0]+"minus")
		tempMinus.className="graphicButtons buttonMinus";
	}
	var tempText;
	var tempField;
	var tempOperator;
	var sqlText="";
	var andor=getObjectFromID("ASanyall");
	for(var i=0;i<ASParams.length;i++){
		tempField=getObjectFromID("ASC"+ASParams[i]+"field");
		tempOperator=getObjectFromID("ASC"+ASParams[i]+"operator");
		tempText=getObjectFromID("ASC"+ASParams[i]+"text");
		var myText=tempText.value.replace("\"","\\\"");
		if(myText!="")
			sqlText+=" "+andor.value+" "+tempField.value+" "+tempOperator.value+" \""+myText+"\"";
	}
	sqlText=sqlText.substring(andor.value.length+1);
	var searchButton=getObjectFromID("ASsearchbutton");
	if(sqlText!="")
		searchButton.disabled=false;
	else
		searchButton.disabled=true;
	var sqlBox=getObjectFromID("ASSQL");
	sqlBox.value=sqlText;
}

function ASEnableSave(thetextarea){
	var searchButton=getObjectFromID("ASsearchbutton");
	if (thetextarea.value!="")
		searchButton.disabled=false;
	else
		searchButton.disabled=true;
}

function addlineAS(){
	var tempMinus=getObjectFromID("ASC"+ASParams[0]+"minus");
	if(tempMinus.className=="graphicButtons buttonMinusDisabled"){
		tempMinus.className="graphicButtons buttonMinus";
	}
	var tempDiv=getObjectFromID("ASC"+ASParams[0]);
	var tempContent=tempDiv.innerHTML;
	var REcriteria = new RegExp("ASC"+ASParams[0],"g")
	tempContent=tempContent.replace(REcriteria,"ASC"+(ASParams[ASParams.length-1]+1))

	var newDiv=document.createElement("div");
	newDiv.id="ASC"+(ASParams[ASParams.length-1]+1);
	newDiv.innerHTML=tempContent;

	var containerDiv=getObjectFromID("theASCs");
	containerDiv.appendChild(newDiv);
	var tempText=getObjectFromID("ASC"+(ASParams[ASParams.length-1]+1)+"text");
	tempText.value="";

	ASParams[ASParams.length]=ASParams[ASParams.length-1]+1;
}

function removeLineAS(thebutton){
	if(thebutton.className=="graphicButtons buttonMinusDisabled")
		return false;

	var theDiv=thebutton.parentNode;

	var containerDiv=getObjectFromID("theASCs");
	containerDiv.removeChild(theDiv);
	var theid=theDiv.id.replace(/ASC/g,"");
	theDiv=null;
	for(var i=0;i<ASParams.length;i++){
		if(ASParams[i]==theid){
			ASParams.splice(i,1);
			break;
		}
	}
	if(ASParams.length==1){
		var tempButton=getObjectFromID("ASC"+ASParams[0]+"minus");
		tempButton.className="graphicButtons buttonMinusDisabled";
	}
	updateAS();
}

function performAdvancedSearch(thebutton){
	var tempsqlBox=getObjectFromID("ASSQL");
	var realSQL=getObjectFromID("advancedsearch");
	realSQL.value=tempsqlBox.value;
	thebutton.form.submit();
}

// Load Search Functions ==========================================
function LSsearchSelect(theselect,base){
	var loadbutton= getObjectFromID("LSLoad");
	var deletebutton= getObjectFromID("LSDelete");
	var searchname=getObjectFromID("LSSelectedSearch");
	var sqlbox=getObjectFromID("LSSQL");
	var resultbox=getObjectFromID("LSResults");
	if (theselect.value=="NA"){
		loadbutton.disabled=true;
		deletebutton.disabled=true;
		searchname.value="";
		sqlbox.value="";
	} else {
		resultbox.innerHTML="<img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Loading...</strong>";
		var theURL=base+"loadsearch.php?cmd=getsearch&id="+theselect.value;
		loadXMLDoc(theURL,null,false);
		sqlbox.value=req.responseText;
		searchname.value=theselect.options[theselect.selectedIndex].text;
		loadbutton.disabled=false;
		var userPosStart=5000000;
		for(var i=0;i<theselect.options.length;i++)
			if(theselect.options[i].text.indexOf("user searches")!=-1)
				userPosStart=i;
		if(theselect.selectedIndex>userPosStart)
			deletebutton.disabled=false;
		else
			deletebutton.disabled=true;
		resultbox.innerHTML="";
	}
}

function LSRunSearch(){
	var sqlbox=getObjectFromID("LSSQL");
	var advancedsearch=getObjectFromID("advancedsearch");
	advancedsearch.value=sqlbox.value
	advancedsearch.form.submit();
}
function LSDeleteSearch(base){
	var theselect=getObjectFromID("LSList");
	var resultbox=getObjectFromID("LSResults");

	resultbox.innerHTML="<img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Loading...</strong>";
	var theURL=base+"loadsearch.php?cmd=deletesearch&id="+theselect.value;
	loadXMLDoc(theURL,null,false);
	if(req.responseText=="success"){
		resultbox.innerHTML="";
		theselect.options[theselect.selectedIndex]=null;
		if(theselect.options.length==1){
			theselect.options[0].text="No Saved Searches";
			theselect.disabled=true;
		}
		theselect.selectedIndex=0;
		LSsearchSelect(theselect,base);

	}
}

// Save Search Functions ==========================================

function enableSave(thetext){
	var searchbutton=getObjectFromID("saveSearch");
	if(thetext.value=="")
		searchbutton.disabled=true;
	else
		searchbutton.disabled=false;
}

function saveMySearch(base){
	var searchtext=getObjectFromID("saveSearchName");
	var searchstatus=getObjectFromID("saveSearchReults");
	var tabledefid=getObjectFromID("tabledefid");

	searchstatus.style.display="block";
	searchstatus.className="";
	searchstatus.innerHTML="<img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Loading...</strong>";
	var theURL=base+"loadsearch.php?cmd=savesearch&name="+encodeURIComponent(searchtext.value)+"&tid="+tabledefid.value;
	loadXMLDoc(theURL,null,false);
	searchstatus.innerHTML=req.responseText;
	searchstatus.className="standout";

	loadTab=getObjectFromID("loadSearchTab");
	loadTab.innerHTML="";
}

// Advanced Sort Functions ==========================================
function sortEnableButtons(thetextarea){

	var buttonRunSort=getObjectFromID("sortRunSort");
	var buttonSaveSort=getObjectFromID("sortSaveSort");
	var buttonClearSort=getObjectFromID("sortClearSort");
	if (thetextarea.value!="") {
		buttonRunSort.disabled=false;
		buttonSaveSort.disabled=false;
		buttonClearSort.disabled=false;
	}
	else{
		buttonRunSort.disabled=true;
		buttonSaveSort.disabled=true;
		buttonClearSort.disabled=true;
	}
}

function clearSort(){
	var sqlBox=getObjectFromID("sortSQL");
	sqlBox.value="";
	var containerDiv=getObjectFromID("theSorts");
	containerDiv.style.display="none";
	var tempbutton;
	for(var i=SortParams.length-1;i>0;i--){
		tempbutton=getObjectFromID("Sort"+SortParams[i]+"Minus");
		sortRemoveLine(tempbutton);
	}
	containerDiv.style.display="block";
}

function updateSort(){
	var tempMinus;
	if(SortParams.length>1){
		tempMinus=getObjectFromID("Sort"+SortParams[0]+"Minus")
		tempMinus.className="graphicButtons buttonMinus";
		tempMinus=getObjectFromID("Sort"+SortParams[0]+"Down")
		tempMinus.className="graphicButtons buttonDown";
	}
	var tempField;
	var tempOrder;
	var sqlText="";
	for(var i=0;i<SortParams.length;i++){
		tempField=getObjectFromID("Sort"+SortParams[i]+"Field");
		tempOrder=getObjectFromID("Sort"+SortParams[i]+"Order");
		sqlText+=", "+tempField.value+" "+tempOrder.value;
	}
	sqlText=sqlText.substring(2);

	var sqlBox=getObjectFromID("sortSQL");
	sqlBox.value=sqlText;
	sortEnableButtons(sqlBox);
}

function sortAddLine(){
	var tempMinus=getObjectFromID("Sort"+SortParams[0]+"Minus");
	if(tempMinus.className=="graphicButtons buttonMinusDisabled")
		tempMinus.className="graphicButtons buttonMinus";

	var tempUp=getObjectFromID("Sort"+SortParams[0]+"Up");
	var tempDown=getObjectFromID("Sort"+SortParams[0]+"Down");
	tempDown.className="graphicButtons buttonDownDisabled";
	tempUp.className="graphicButtons buttonUp";

	var tempDiv=getObjectFromID("Sort"+SortParams[0]);
	var tempContent=tempDiv.innerHTML;
	var REcriteria = new RegExp("Sort"+SortParams[0],"g")
	tempContent=tempContent.replace(REcriteria,"Sort"+(SortParams[SortParams.length-1]+1))

	tempDown.className="graphicButtons buttonDown";
	tempUp.className="graphicButtons buttonUpDisabled";

	var newDiv=document.createElement("div");
	newDiv.id="Sort"+(SortParams[SortParams.length-1]+1);
	newDiv.innerHTML=tempContent;

	var containerDiv=getObjectFromID("theSorts");
	containerDiv.appendChild(newDiv);

	SortParams[SortParams.length]=SortParams[SortParams.length-1]+1;
	for(var i=1;i<SortParams.length-1;i++){
		tempDown=getObjectFromID("Sort"+SortParams[i]+"Down");
		tempDown.className="graphicButtons buttonDown";
	}
	updateSort();
}

function sortRemoveLine(thebutton){
	if(thebutton.className=="graphicButtons buttonMinusDisabled")
		return false;

	var theDiv=thebutton.parentNode;

	var containerDiv=getObjectFromID("theSorts");
	containerDiv.removeChild(theDiv);
	var theid=theDiv.id.replace(/Sort/g,"");
	theDiv=null;
	for(var i=0;i<SortParams.length;i++){
		if(SortParams[i]==theid){
			SortParams.splice(i,1);
			break;
		}
	}
	if(SortParams.length==1){
		var tempButton=getObjectFromID("Sort"+SortParams[0]+"Minus");
		tempButton.className="graphicButtons buttonMinusDisabled"
		tempButton=getObjectFromID("Sort"+SortParams[0]+"Up");
		tempButton.className="graphicButtons buttonUpDisabled";
	}
	var tempDown=getObjectFromID("Sort"+SortParams[SortParams.length-1]+"Down");
	tempDown.className="graphicButtons buttonDownDisabled";

	updateSort();
}

function sortMove(thebutton,direction){
	if(thebutton.className=="graphicButtons buttonUpDisabled" || thebutton.className=="graphicButtons buttonDownDisabled")
		return false;
	if(direction=="up")
		direction=-1;
	else
		direction=1;

	var theDiv=thebutton.parentNode;
	var theid=theDiv.id.replace(/Sort/g,"");

	var containerDiv=getObjectFromID("theSorts");
	containerDiv.removeChild(theDiv);

	for(var i=0;i<SortParams.length;i++)
		if(SortParams[i]==theid){
			var mypos=i;
			var movetopos=i+direction;
		}
	var moveto;
	if (direction==1)
		moveto=movetopos+1;
	else
		moveto=movetopos;

	var movetodiv=getObjectFromID("Sort"+SortParams[moveto]);
	containerDiv.insertBefore(theDiv,movetodiv);
	SortParams[mypos]=SortParams[movetopos];
	SortParams[movetopos]=theid;
	updateSort();

	var tempUp=getObjectFromID("Sort"+SortParams[0]+"Up");
	var tempDown=getObjectFromID("Sort"+SortParams[0]+"Down");
	tempUp.className="graphicButtons buttonUpDisabled";
	tempDown.className="graphicButtons buttonDown";

	for(var i=1;i<SortParams.length-1;i++){
		tempUp=getObjectFromID("Sort"+SortParams[i]+"Up");
		tempDown=getObjectFromID("Sort"+SortParams[i]+"Down");
		tempUp.className="graphicButtons buttonUp";
		tempDown.className="graphicButtons buttonDown";
	}

	tempUp=getObjectFromID("Sort"+SortParams[SortParams.length-1]+"Up");
	tempDown=getObjectFromID("Sort"+SortParams[SortParams.length-1]+"Down");
	tempUp.className="graphicButtons buttonUp";
	tempDown.className="graphicButtons buttonDownDisabled";

}

function performAdvancedSort(thebutton){
	var advancedsort=getObjectFromID("advancedsort");
	var sortSQL=getObjectFromID("sortSQL");
	advancedsort.value=sortSQL.value;
	thebutton.form.submit();
}

function sortEnableSave(thetext){
	var savebutton=getObjectFromID("sortDoSave");
	if(thetext.value=="")
		savebutton.disabled=true;
	else
		savebutton.disabled=false;
}
function sortAskSaveName(base){
	var text="<p>name<br /><input id=\"sortSaveName\" type=\"text\" maxlength=\"128\" length=\"40\" onkeyup=\"sortEnableSave(this)\" style=\"width:98%\" /></p>";
	text+="<p align=\"right\"><input type=\"button\" class=\"Buttons\" id=\"sortDoSave\" onclick=\"sortSave('"+base+"')\" value=\"save\" style=\"width:75px\" disabled=\"disabled\"/>";
	text+="<input type=\"button\" class=\"Buttons\" onclick=\"closeModal()\" value=\"cancel\" style=\"margin-left:5px;width:75px\"/></p>"
	showModal(text,"Save Sort As...",250);
}

function sortSave(base){
	var sortName=getObjectFromID("sortSaveName");
	var sql=getObjectFromID("sortSQL");
	var tabledefid=getObjectFromID("tabledefid");
	var theURL=base+"advancedsort.php?cmd=save&name="+encodeURIComponent(sortName.value)+"&clause="+sql.value+"&tid="+tabledefid.value;
	loadXMLDoc(theURL,null,false);
	closeModal();
	if(req.responseText!="success")
	alert("Error:<br />"+req.responseText);
}

function sortAskLoad(base){
	var tabledefid=getObjectFromID("tabledefid");
	var theURL=base+"advancedsort.php?cmd=showSaved&tid="+tabledefid.value+"&base="+encodeURIComponent(base);
	var text="<img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Loading...</strong>";
	showModal(text,"Saved Sorts",350);
	loadXMLDoc(theURL,null,false);
	var modalContent=getObjectFromID("modalContent");
	modalContent.innerHTML=req.responseText;
}

function sortSavedSelect(theselect){
	var deletebutton=getObjectFromID("sortSavedDeleteButton");
	var loadbutton=getObjectFromID("sortSavedLoadButton");
	if(theselect.value!="NA"){
		var userPosStart=5000000;
		for(var i=0;i<theselect.options.length;i++)
			if(theselect.options[i].text.indexOf("user sorts")!=-1)
				userPosStart=i;
		if(theselect.selectedIndex>userPosStart)
			deletebutton.disabled=false;
		else
			deletebutton.disabled=true;
		loadbutton.disabled=false;
	} else{
		deletebutton.disabled=true;
		loadbutton.disabled=true;
	}
}

function sortSavedDelete(base){
	var theselect=getObjectFromID("sortSavedList");
	var modalContent=getObjectFromID("modalContent");
	var theURL=base+"advancedsort.php?cmd=deleteSaved&id="+theselect.value;
	modalContent.innerHTML="<img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Loading...</strong>";
	loadXMLDoc(theURL,null,false);

	if (req.responseText=="success"){
		var tabledefid=getObjectFromID("tabledefid");
		theURL=base+"advancedsort.php?cmd=showSaved&tid="+tabledefid.value+"&base="+encodeURIComponent(base);
		loadXMLDoc(theURL,null,false);
		modalContent.innerHTML=req.responseText;
	} else
		modalContent.innerHTML=req.responseText;
}

function sortSavedLoad(base){
	var theselect=getObjectFromID("sortSavedList");
	var modalContent=getObjectFromID("modalContent");
	var theURL=base+"advancedsort.php?cmd=loadSaved&id="+theselect.value;
	modalContent.innerHTML="<img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Loading...</strong>";

	loadXMLDoc(theURL,null,false);
	var sortSQL=getObjectFromID("sortSQL");
	sortSQL.value=req.responseText;
	var containerDiv=getObjectFromID("theSorts");
	containerDiv.style.display="none";

	closeModal();
}
