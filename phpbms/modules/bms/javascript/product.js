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

var product = {

    submitForm: function(){

        var theform = getObjectFromID("record");
        var hiddenCommand = getObjectFromID("hiddenCommand");

        if(!validateForm(theform))
            return false;

        hiddenCommand.value = "save";

        //check to see if there has been changes to the additional categories
        var addcats = getObjectFromID("addcats");
        var catschanged = getObjectFromID("catschanged");
        if(catschanged.value)
            addcats.value = product.prepCategories();

        theform.submit();

        return true;

    },//end function submitForm


    cancelForm: function(){

        var theform = getObjectFromID("record");
        var hiddenCommand = getObjectFromID("hiddenCommand");
        var cancelclick= getObjectFromID("cancelclick");

        cancelclick.value = true;
        hiddenCommand.value ="cancel";

        theform.submit();

        return true;

    },//end function cancelForm


    checkPartNumber: function(){

        var partnumber = getObjectFromID("partnumber");
        var excludeid = getObjectFromID("id");

        if(!checkUnique(4, "partnumber", partnumber.value, parseInt(excludeid.value))){

            alert("Part number must be unique.");
            partnumber.value="";
            partnumber.focus();

        }//endif

    },//end function checkPartNumber


    //calculate the markup percentage based on the unit price and cost fields
    // this function is called when changes to price or cost occur.
    calculateMarkUp: function(){

        var thecost = getObjectFromID("unitcost");
        var theprice = getObjectFromID("unitprice");
        var unitcost = "";
        var unitprice = "";

        unitcost = currencyToNumber(thecost.value);
        unitprice = currencyToNumber(theprice.value);

        if(unitcost!=0 && unitprice!=0){

                var markup = getObjectFromID("markup");
                markup.value=(Math.round((unitprice/unitcost -1)*10000)/100)+"%"

        }//endif

    },//end function calculateMarkup


    //calculates and sets the unit price based on the unit cost and the
    // markup percentage.
    calculatePrice: function(){

        var themarkup = getObjectFromID("markup");
        var thecost = getObjectFromID("unitcost");
        var theprice = getObjectFromID("unitprice");
        var unitcost = "";

        var markup = getNumberFromPercentage(themarkup.value);

        unitcost = currencyToNumber(thecost.value);

        var newnumber = (Math.round((unitcost+(markup*unitcost/100))*100)/100).toString();

        theprice.value = numberToCurrency(newnumber);

    },//end function calculatePrice


    //toggles the web description between edit mode and previewing
    editPreviewWebDesc: function(){

        var editDiv = getObjectFromID("webDescEdit");
        var previewDiv = getObjectFromID("webDescPreview");
        var webDesc = getObjectFromID("webdescription");
        var thebutton = getObjectFromID("buttonWebPreview");

        if (thebutton.innerHTML == "preview"){

            thebutton.innerHTML = "edit";
            previewDiv.style.display = "block";
            editDiv.style.display = "none";
            previewDiv.innerHTML = webDesc.value;

        } else {

            thebutton.innerHTML = "preview";
            previewDiv.style.display = "none";
            editDiv.style.display = "block";

        }//endif

    },//end function editPreviewWebDesc


    // generic function setting hidden value associated with a picture
    // to a precise status
    _updatePicStatus: function(picture, status){

	var thechange=getObjectFromID(picture+"change");
	thechange.value=status;

    },//end function _updatePicStatus


    updateThumb: function(){

        product._updatePicStatus("thumb","upload");

    },//end function updateThumb


    updatePic: function(){

        product._updatePicStatus("picture","upload");

    },//end function updatePic


    _deletePicture: function (picture){

	var thepic = getObjectFromID(pic+"pic");
	var deleteDiv = getObjectFromID(pic+"delete");
	var addDiv = getObjectFromID(pic+"add");
	thepic.style.display = "none";
	deleteDiv.style.display = "none";
	addDiv.style.display = "block";

        product._updatePicStatus(pic,"delete");

    },//end function _deletePicture


    deleteThumb: function(){

        product._deletePicture("thumb");

    },//end function deleteThumb


    deletePic: function(){

        product._deletePicture("picture");

    },//end function deletePic


    // add a category from the smart search to the "list"
    addCategory: function(){

        var display = getObjectFromID("ds-morecategories");
        var idToAdd = getObjectFromID("morecategories");

        if(idToAdd.value){

            //first we need to check to see if that id has already been added
            if(!product._duplicateCategories(idToAdd.value)){

                var newId = product._getNextId();

                var theDiv = document.createElement("div");
                theDiv.id ="AC" + newId;
                theDiv.className = "moreCats";

                var containerDiv = getObjectFromID("catDiv");

                containerDiv.appendChild(theDiv);

                theDiv.innerHTML = ' \
                    <input type="text" value="' + display.value + '" id="AC-' + newId + '" size="30" readonly="readonly"/>\
                    <input type="hidden" id="AC-CatId-' + newId + '" value="' + idToAdd.value + '" class="catIDs"/>\
                    <button type="button" id="RM-CatID-' + newId + '" class="graphicButtons buttonMinus catButtons" title="Remove Category"><span>-</span></button>\
                ';


                var buttonId = "RM-CatID-" + newId;
                var newMinusButton = getObjectFromID(buttonId);
                connect(newMinusButton, "onclick", product.removeCategory);

                //flag a change for processing
                var catschanged = getObjectFromID("catschanged");
                catschanged.value = "1";

            }//endif

        }//endif

    },//end function addCategory


    removeCategory: function(e){

        var theButton = e.src();

        var theDiv = theButton.parentNode;

        var parentDiv = theDiv.parentNode;

        parentDiv.removeChild(theDiv);

        var catschanged = getObjectFromID("catschanged");
        catschanged.value = "1"

    },//end function deleteCategory


    //calculates the next id necessary for additional categories box
    _getNextId: function(){

        var theid = 0

        var catDivs = getElementsByClassName("moreCats");
        for(var i = 0; i< catDivs.length; i++)
                if(parseInt(catDivs[i].id.substr(2)) > theid)
                        theid = parseInt(catDivs[i].id.substr(2));

        theid++;

        return theid;

    },//end function _getNextID


    //checks to see if the category has already been added,
    _duplicateCategories: function(idToCheck){

        var theIds = getElementsByClassName("catIDs");

        for(var i = 0; i < theIds.length; i++)
            if(theIds[i].value == idToCheck)
                return true;

        return false;

    },//end function _duplicateCategories


    prepCategories: function(){

        var catList = "[";

        var catIDs = getElementsByClassName("catIDs");
        for(var i=0; i<catIDs.length; i++){

            catList += '{' +
                        '"productcategoryuuid" : "' +catIDs[i].value + '"' +
                        '}';

            if(i < ( catIDs.length - 1))
                catList += ",";

        }//end for

        catList += "]";

        return catList;

    }//end function prepCategories

}//end class products



/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

    var webDivs = new Array();
    webDivs[webDivs.length] = getObjectFromID("webstuff");

    var webLinks = new Array();
    webLinks[webLinks.length] = getObjectFromID("webenabled");

    var webAccordion = new fx.Accordion(webLinks, webDivs,{opacity: false, duration:200});
    if(webLinks[0].checked)
        webAccordion.showThisHideOpen(webDivs[0]);

    var partnumber = getObjectFromID("partnumber");
    connect(partnumber, "onchange", product.checkPartNumber);

    var unitprice = getObjectFromID("unitprice");
    connect(unitprice, "onchange", product.calculateMarkUp);

    var unitcost = getObjectFromID("unitcost");
    connect(unitcost, "onchange", product.calculateMarkUp);

    var updatePriceButton = getObjectFromID("updatePrice");
    connect(updatePriceButton, "onclick", product.calculatePrice);

    var theButton = getObjectFromID("buttonWebPreview");
    connect(theButton, "onclick", product.editPreviewWebDesc);

    var delThumbButton = getObjectFromID("deleteThumbButton");
    connect(delThumbButton, "onclick", product.deleteThumb);

    var thumbnailupload = getObjectFromID("thumbnailupload");
    connect(thumbnailupload, "onchange", product.updateThumb);

    var delPicButton = getObjectFromID("deletePictureButton");
    connect(delPicButton, "onclick", product.deletePic);

    var pictureupload = getObjectFromID("pictureupload");
    connect(pictureupload, "onchange", product.updatePic);

    var addCatButton = getObjectFromID("addCatButton");
    connect(addCatButton, "onclick", product.addCategory);

    var removeCatButtons = getElementsByClassName("catButtons");
    for(var i=0; i<removeCatButtons.length; i++)
        connect(removeCatButtons[i], "onclick", product.removeCategory);

    var saveButton = getObjectFromID("saveButton1");
    connect(saveButton, "onclick", product.submitForm);

    var saveButton = getObjectFromID("saveButton2");
    connect(saveButton, "onclick", product.submitForm);

    var cancelButton = getObjectFromID("cancelButton1");
    connect(cancelButton, "onclick", product.cancelForm);

    var cancelButton = getObjectFromID("cancelButton2");
    connect(cancelButton, "onclick", product.cancelForm);

    //set the initial focus
    var partname = getObjectFromID("partname");
        partname.focus();

})
