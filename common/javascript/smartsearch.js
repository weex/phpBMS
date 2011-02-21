/*
 $Rev: 204 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-03-26 15:07:58 -0600 (Mon, 26 Mar 2007) $
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

smartSearch = {

	displayValue: Array(),

	triggerLookup: Array(),

	searchBox: Array(),

	searchBoxConnects: Array(),

	committedDisplayValue: Array(),

	changeDisplay: function(e){
		//this sets the time out to do an ajax lookup when they are done typing their search criteria.

		var display = e.src();
		var ssID = display.id.substr(3);

		if(!smartSearch.displayValue[ssID])
			smartSearch.displayValue[ssID] = "";

		var lookupValue = display.value.trim();

		if (lookupValue != smartSearch.displayValue[ssID] && lookupValue != ""){

			if(smartSearch.triggerLookup[ssID])
				window.clearTimeout(smartSearch.triggerLookup[ssID]);

			smartSearch.triggerLookup[ssID] = window.setTimeout("smartSearch.lookup('"+ssID+"')",500);

		} else {
			//it is possible they hit the down, up arrow, or the return button

			var key = e.event().keyCode;

			switch(key){

				case 40:
					//move highlight down
					smartSearch.moveSearchHighlight(ssID, "down");
					break;

				case 38:
					//move highlight down
					smartSearch.moveSearchHighlight(ssID, "up");
					break;

				case 27:
					//cancel
					smartSearch.cancelSearch(null, ssID);

			}//endswitch

		}//endif

	},//end method


	searchResultsConnects: Array(),

	lookup: function(ssID, offset){

		// We need to do an ajax lookup based on the criteria.
		// Fisrt we need to see if the holding box is visible.
		// if not we need to create it
		if (!smartSearch.searchBox[ssID])
			smartSearch.createSearchBox(ssID);


		var sbResults = getObjectFromID("SBResults-" + ssID);

		if(!offset){
			offset = 0;
			sbResults.style.height="";
		}

		var closeButton = getObjectFromID("SBCloseButton-" + ssID);
		closeButton.className = "graphicButtons buttonSpinner";

		var searchDisplay = getObjectFromID("ds-" + ssID);
		smartSearch.displayValue[ssID] = searchDisplay.value;

		var sdbid = getObjectFromID("sdbid-" + ssID);


		var theurl = APP_PATH + "smartsearch.php?sdbid=" + encodeURI(sdbid.value) + "&t=" + encodeURI(searchDisplay.value.trim());

		if(offset != 0)
			theurl += "&o=" + offset;

		if(!smartSearch.searchResultsConnects[ssID])
			smartSearch.searchResultsConnects[ssID] = Array();

		for(var i=0; i<smartSearch.searchResultsConnects[ssID].length; i++)
			disconnect(smartSearch.searchResultsConnects[ssID][i])

		loadXMLDoc(theurl,null,false);

		var response;
		try {
			response = eval( "(" + req.responseText + ")" );
		}
		catch(excep){
			alert(excep);
		}

		var numRecords = response.resultRecords.length

		var totalRecords = response.totalRecords;

		var newText="";

		if(numRecords) {
			for(var i=0; i<numRecords; i++){

				newText += '\
					<a href="#" class="SBSearchItems ' + "SBSI-" + ssID + ' ' + response.resultRecords[i].classname + '" id="SB-' + response.resultRecords[i].value + '" tabindex="4000">\
						<span class="SBMain">' + response.resultRecords[i].display + '</span>\
						<span class="SBExtra">' + response.resultRecords[i].secondary + '</span>\
					</a>';


			}//endfor

			if(parseInt(offset) + parseInt(numRecords) < parseInt(totalRecords)){

				//add more button for searches that have lots of records
				newText += '\
				<div id="SBMoreDiv-' + ssID + '">\
					<button type="button" id="SBMoreButton-' + ssID +'" class="smallButtons" name="' + (parseInt(offset) + parseInt(numRecords)) + '">more results...</button>\
				</div>';

			}//end if

		} else {

			newText = '<div>No Records Found Matching Criteria</div>';

		}//endif

		if(!offset)
			sbResults.innerHTML =  newText;
		else
			sbResults.innerHTML += newText;

		var searchItems = getElementsByClassName("SBSI-" + ssID);
		smartSearch.searchResultsConnects[ssID] = Array();

		for(i=0; i < searchItems.length; i++)
			smartSearch.searchResultsConnects[ssID][smartSearch.searchResultsConnects[ssID].length] = connect(searchItems[i], "onclick", smartSearch.clickSearchResult);

		var moreButton = getObjectFromID("SBMoreButton-" + ssID);
		if(moreButton)
			smartSearch.searchResultsConnects[ssID][smartSearch.searchResultsConnects[ssID].length] = connect(moreButton, "onclick", smartSearch.getMoreResults);

		closeButton.className = "graphicButtons buttonX";
	},//end method


	createSearchBox: function(ssID){

		//need to grab search box for reference x and ys
		var searchdisplay = getObjectFromID("ds-"+ssID);

		if(!smartSearch.searchBoxConnects[ssID])
			smartSearch.searchBoxConnects[ssID] = Array();

		if(smartSearch.searchBoxConnects[ssID][0])
			disconnect(smartSearch.searchBoxConnects[ssID][0]);

		if(smartSearch.searchBoxConnects[ssID][1])
			disconnect(smartSearch.searchBoxConnects[ssID][1]);

		smartSearch.searchBox[ssID] = document.createElement("div");
		smartSearch.searchBox[ssID].id = "searchBox-" + ssID;
		smartSearch.searchBox[ssID].className = "smartSearchBox";

		var thetop = getTop(searchdisplay) + searchdisplay.offsetHeight;
		var thewidth = searchdisplay.offsetWidth;
		var theleft = getLeft(searchdisplay);

		smartSearch.searchBox[ssID].style.top = thetop + "px";
		smartSearch.searchBox[ssID].style.left = theleft + "px";
		smartSearch.searchBox[ssID].style.width = thewidth + "px";

		var newDiv = document.createElement("div");
		newDiv.id = "SBHeader-" + ssID;
		newDiv.className = "SBHeader";
		newDiv.innerHTML = '<button type="button" id="SBCloseButton-' + ssID + '" class="SBCloseButton graphicButtons buttonSpinner" tabindex="4000"><span>close</span></button>';
		smartSearch.searchBox[ssID].appendChild(newDiv)

		newDiv = document.createElement("div");
		newDiv.id = "SBResults-" + ssID;
		newDiv.className = "SBResults";
		smartSearch.searchBox[ssID].appendChild(newDiv)

		newDiv = document.createElement("div");
		newDiv.id = "SBFooter-" + ssID;
		newDiv.id = "SBFooter";
		smartSearch.searchBox[ssID].appendChild(newDiv)

		searchdisplay.parentNode.appendChild(smartSearch.searchBox[ssID]);

		var closeButton = getObjectFromID("SBCloseButton-" + ssID);

		smartSearch.searchBoxConnects[ssID][0] = connect(smartSearch.searchBox[ssID],"onmousedown", smartSearch.mouseDownDropDown);
		smartSearch.searchBoxConnects[ssID][1] = connect(closeButton,"onclick",smartSearch.cancelSearch);

	},//end method

	inDropDown: Array(),

	mouseDownDropDown: function(e){

		var box = e.src();
		var ssID = box.id.substr(10)

		smartSearch.inDropDown[ssID] = true;

	},//end method - clickDropDown



	blurIdent: Array(),

	blurDisplay: function(e){

		var display = e.src();
		var ssID = display.id.substr(3);

		if(!smartSearch.inDropDown[ssID])
			smartSearch.blurIdent[ssID] = window.setTimeout("smartSearch._blurDisplay('" + ssID + "')",200);

		smartSearch.inDropDown[ssID] = false;

	},//end method


	_blurDisplay: function(ssID){

		var searchDisplay = getObjectFromID("ds-" + ssID);

		if(smartSearch.searchBox[ssID] || searchDisplay.value != smartSearch.committedDisplayValue[ssID]){

			var highlight = getElementsByClassName("SBSel-"+ssID);

			if(highlight.length !== 0){

				smartSearch.chooseSearchItem(highlight[0])

			} else {

				if(searchDisplay.value != "")
					smartSearch.cancelSearch(null, ssID);
				else
					smartSearch.blankSearch(ssID);

			}//end if

		}//end if

		smartSearch.blurIdent[ssID] = false;

	},//end method


	blankSearch: function(ssID){

		var searchDisplay = getObjectFromID("ds-" + ssID);
		var valueField = getObjectFromID(ssID);

		valueField.value = ""
		smartSearch.displayValue[ssID] = "";
		smartSearch.committedDisplayValue[ssID] = "";
		searchDisplay.value = "";
		//lastly, if the value field has an onchange we need to trgger it
		//First we check for legacy on changes
		if (valueField.onchange)
			valueField.onchange.call(valueField);

		//next we try for listners...
		trigger(valueField, "onchange");

	},//end method - blankSearch


	cancelSearch: function(e, ssID){

		if(e){

			var thebutton = e.src()
			ssID = thebutton.id.substr(14);

		}//endif - e

		var searchDisplay = getObjectFromID("ds-" + ssID);
		var freeForm = getObjectFromID("sff-" + ssID);

		if(smartSearch.searchBox[ssID])
			searchDisplay.parentNode.removeChild(smartSearch.searchBox[ssID]);

		if(freeForm.value == 0){

			searchDisplay.value = smartSearch.committedDisplayValue[ssID];
			smartSearch.displayValue[ssID] = smartSearch.committedDisplayValue[ssID];

		}//end free forming wipes

		smartSearch.searchBox[ssID] = null;

		smartSearch.inDropDown[ssID] = false;
		//searchDisplay.blur();
		//searchDisplay.focus();

	},//end method


	clickSearchResult: function(e){

		var theItem = e.src();
		var classes = theItem.className.split(" ");
		var ssID = classes[1].substr(5);

		window.clearTimeout(smartSearch.blurIdent[ssID]);
		smartSearch.blurIdent[ssID] = false;

		smartSearch.chooseSearchItem(theItem);
		e.stop();

	},//end method


	chooseSearchItem: function(atag){

		var classes = atag.className.split(" ");
		var ssID = classes[1].substr(5);

		var valueField = getObjectFromID(ssID);
		var searchDisplay = getObjectFromID("ds-"+ssID);

		valueField.value = atag.id.substr(3);

		for(var i=0; i< atag.childNodes.length; i++)
			if(atag.childNodes[i].className)
				if(atag.childNodes[i].className == "SBMain")
					searchDisplay.value = htmlDecode(atag.childNodes[i].innerHTML);

		smartSearch.committedDisplayValue[ssID] = searchDisplay.value;
		smartSearch.displayValue[ssID] = searchDisplay.value;

		//remove the box
		searchDisplay.parentNode.removeChild(smartSearch.searchBox[ssID]);

		smartSearch.searchBox[ssID] = null;

		//lastly, if the value field has an onchange we need to trgger it
		//First we check for legacy on changes
		if (valueField.onchange)
			valueField.onchange.call(valueField);

		//next we try for listners...
		trigger(valueField, "onchange");

	},//end method


	moveSearchHighlight: function(ssID, direction){

		var sbResults = getObjectFromID("SBResults-" + ssID);
		var currentItem = 0;
		var classes, newClassName;
		var hasSelected = false;

		if(direction=="down")
			direction = 1;
		else
			direction =-1;

		//cycle through results
		for(var i=0; i < sbResults.childNodes.length; i++){

			//look at their classname
			if(sbResults.childNodes[i].className){
				//if they have a classname coresponding to selected:do stuff
				if(sbResults.childNodes[i].className.indexOf("SBSelected") !== -1){

					hasSelected = true;
					currentItem = i;
					newClassName = "";
					classes = sbResults.childNodes[i].className.split(" ");

					//strip out the classes corresponding to selected
					for(var j=0; j < classes.length; j++)
						if(classes[j] != "SBSelected" && classes[j].indexOf("SBSel-") === -1)
							newClassName += " " + classes[j];

					newClassName = newClassName.substr(1);

					//set the class to the old class name minus those relating to selection
					sbResults.childNodes[i].className = newClassName;
				}//endif - SBSELECTED
			}//endif - clasname

		}//endfor

		//find index of newitem
		var newItem = currentItem
		if(hasSelected)
			newItem += direction;


		//loop through til we find something with an existing class name making sure we have
		//some length to the result children
		while(newItem >= 0 && newItem < sbResults.childNodes.length){

			if(sbResults.childNodes[newItem].className){

				sbResults.childNodes[newItem].className += " SBSelected SBSel-"+ssID;
				newItem = -1000;

			}else
				newItem += direction;

		}//endwhile

	}, //end method


	getMoreResults: function(e){

		var button = e.src();
		var ssID = button.id.substr(13);

		window.clearTimeout(smartSearch.blurIdent[ssID]);
		smartSearch.blurIdent[ssID] = false;

		var thediv = button.parentNode;

		thediv.parentNode.removeChild(thediv);

		var sbResults = getObjectFromID("SBResults-" + ssID);
		sbResults.style.height = "330px";

		var offset = button.name
		button.id = "invalid-removed";

		smartSearch.lookup(ssID, offset);

		var searchDisplay = getObjectFromID("ds-" + ssID);
		searchDisplay.focus();

	}//end method

}//end struct

/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

	//grab all smart search boxes
	var smartSearches = getElementsByClassName("inputSmartSearch");
	var ssID;
	for(i=0; i<smartSearches.length; i++){

		smartSearches[i].setAttribute('autocomplete','off');
		connect(smartSearches[i], "onkeyup", smartSearch.changeDisplay);
		connect(smartSearches[i], "onblur", smartSearch.blurDisplay);

		ssID = smartSearches[i].id.substr(3);

		smartSearch.displayValue[ssID] = smartSearches[i].value;
		smartSearch.committedDisplayValue[ssID] = smartSearches[i].value;

	}//endfor smartSearches


})
