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

/**
 * handles basic functions of adminsettings.php page
 *
 */
baseAdminSettings = {

    /**
     * Processes form submission from save buttons
     */
    processForm: function(){

        var theForm = getObjectFromID("record");

 	var changeseed = getObjectFromID("changeseed");

	var thereturn = false;

	if(changeseed.checked)
	    alert("The 'change seed' check box has been checked. Encryption Seed Must be updated separately from other settings.");
	else
            if(validateForm(theForm))
                theForm.submit();

    },//end function processForm

    /**
     * toggles the encryption seed editing status
     */
    toggleEncryptionEdit:function(){

	var seedinput = getObjectFromID("encryption_seed");
	var currpassinput = getObjectFromID("currentpassword");
	var updatebutton = getObjectFromID("updateEncryptionButton");
        var seedcheckbox = getObjectFromID("changeseed");

	if (seedcheckbox.checked){

            updatebutton.disabled = false;
            seedinput.removeAttribute("readOnly");
            currpassinput.removeAttribute("readOnly");
            seedinput.className = "";
            currpassinput.className = "";
            seedinput.focus();

	} else {

		updatebutton.disabled = true;
		seedinput.setAttribute("readOnly", "readonly");
		currpassinput.setAttribute("readOnly", "readonly");
		seedinput.className = "uneditable";
		currpassinput.className = "uneditable";

	}//endif

    },//end function toggleEncryptionEdit

    /**
     * submits the form for encryption seed updating
     */
    submitEncryptionSeed: function(){

	var seedinput = getObjectFromID("encryption_seed");

        if(!seedinput.value)
            alert ("Encryption seed cannot be blank.");
        else {

            var theForm = getObjectFromID("record");
            var commandinput = getObjectFromID("command");

            commandinput.value = "encryption seed";
            theForm.submit();

        }//endif


    },//end function submitEncryptionSeed


    checkCronStatus: function(){

        var theurl="cron.php?t=y";
        loadXMLDoc(theurl, null, false);

        if(req.responseText.substring(0,3) == "ztz")
            baseAdminSettings._showWarningStatus("cronWarning");

    },//end function checkCronStatus


    _showWarningStatus: function(warningID){

        var configWarnings = getObjectFromID("configWarnings");
        configWarnings.style.display = "block";

        var warning = getObjectFromID(warningID);
        warning.style.display = "list-item";

    },//end _showWarningStatus

}//end class baseAdminSettings



/**
 * Handles creation, display and interaction of inner tabs
 */
settingsTabs = {

    /**
     *array of module sections
     *@var array
     */
    sections: Array(),

    initialize: function(){

        var moduleSections = getElementsByClassName('moduleSections');
        var containerDiv;
        var constructorObject;

        for(var i = 0; i < moduleSections.length; i++){


            containerDiv = settingsTabs._getContainerDiv(moduleSections[i]);
            if(containerDiv){

                constructorObject = new Object();
                constructorObject.container = containerDiv;
                constructorObject.tabs = settingsTabs._getTabDivs(containerDiv)

                settingsTabs.sections[settingsTabs.sections.length] = constructorObject;

                if(constructorObject.tabs.length){

                    settingsTabs._createTabs(containerDiv);

                    settingsTabs._highlightTab(containerDiv, constructorObject.tabs[0].title);

                }//endif

            }//endif

        }//end for

    },//end function createTabs

    _createTabs: function(container){

        var tabs = Array();

        var tabDiv = document.createElement("ul");
        tabDiv.className = "tabs";

        for(var i = 0; i< settingsTabs.sections.length; i++)
            if(settingsTabs.sections[i].container == container)
                tabs = settingsTabs.sections[i].tabs

        var theHTML = "";

        for(var i = 0; i< tabs.length; i++)
            theHTML += '<li title="' + tabs[i].title + '"><a href="#">' + tabs[i].title + '</a></li>';

        tabDiv.innerHTML = theHTML;

        container.insertBefore(tabDiv, container.firstChild);

        for(var i = 0; i< tabDiv.childNodes.length; i++)
            connect(tabDiv.childNodes[i].childNodes[0], "onclick", settingsTabs.clickTab);

    },//endFunction createtabs


    clickTab: function(e){

        var theATag = e.src();
        var theLI = theATag.parentNode;

        var container = theLI.parentNode.parentNode;

        settingsTabs._highlightTab(container, theLI.title);

    }, //end clickTab


    _getContainerDiv: function(div){

        var theReturn = null;

        for(var i = 0; i< div.childNodes.length; i++)
            if(div.childNodes[i].tagName == "DIV")
                if(div.childNodes[i].className == "containers")
                    theReturn = div.childNodes[i];

        return theReturn;

    },//end function _getContainerDiv


    _getTabDivs: function(div){

        var theReturn = Array();

        for(var i = 0; i< div.childNodes.length; i++)
            if(div.childNodes[i].tagName == "DIV")
                if(div.childNodes[i].className == "moduleTab")
                    theReturn[theReturn.length] = div.childNodes[i];

        return theReturn;

    },//end function _getTabDivs


    _getTabLinkContainer: function(div){

        var theReturn = null;

        for(var i = 0; i< div.childNodes.length; i++)
            if(div.childNodes[i].tagName == "UL")
                if(div.childNodes[i].className == "tabs")
                    theReturn = div.childNodes[i];

        return theReturn;


    },//end function _getTabLinkContainer


    _highlightTab: function(section, tabTitle){

        for(var i = 0; i< settingsTabs.sections.length; i++){

            if(settingsTabs.sections[i].container == section){

                for(var j = 0; j<settingsTabs.sections[i].tabs.length; j++ )
                    if(settingsTabs.sections[i].tabs[j].title === tabTitle)
                        settingsTabs.sections[i].tabs[j].style.display = "block";
                    else
                        settingsTabs.sections[i].tabs[j].style.display = "none";

                var linkContainer = settingsTabs._getTabLinkContainer(section);

                for(var j = 0; j<linkContainer.childNodes.length; j++ )
                    if(linkContainer.childNodes[j].title == tabTitle)
                        linkContainer.childNodes[j].className = "tabsSel";
                    else
                        linkContainer.childNodes[j].className = "";

            }//endif

        }//endfor

    }//end function _highlightTab

}//end class settingsTabs

/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

    var moduleSections = getElementsByClassName('moduleSections');
    var moduleButtons = getElementsByClassName('moduleButtons');

    var moduleAccordion = new fx.Accordion(moduleButtons, moduleSections, {opacity: true, duration:500});
    moduleAccordion.showThisHideOpen(moduleSections[0]);

    var seedcheckbox = getObjectFromID("changeseed");
    connect(seedcheckbox, "onclick", baseAdminSettings.toggleEncryptionEdit);

    var updateEncryptionButton = getObjectFromID("updateEncryptionButton");
    connect(updateEncryptionButton, "onclick", baseAdminSettings.submitEncryptionSeed);

    var saveButtons = getElementsByClassName('UpdateButtons');
    for(var i = 0; i < saveButtons.length; i++)
        connect(saveButtons[i], "onclick", baseAdminSettings.processForm);

    settingsTabs.initialize();

    baseAdminSettings.checkCronStatus();

    var cronRun = getObjectFromID("cronRun");

    if(cronRun)
        baseAdminSettings._showWarningStatus("cronRun");

});
