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

snapshot = {

    showConfigure: function(e){

        var box = e.src();
        var configure = getObjectFromID(box.id + "Configure");
        configure.style.display = "inline";

    },//end function showConfigure


    hideConfigure: function(e){

        var box = e.src();
        var section = getObjectFromID(box.id + "ConfigureDropdown");

        if(section.style.display != "block"){

            var configure = getObjectFromID(box.id + "Configure");
            configure.style.display = "none";

        }//endif section display block

    },//end function hideConfigure


    showWidgetOptions: function(e){

        var widget = e.src();
        var options = getObjectFromID(widget.id + "Options");
        options.style.display = "block";

    },//end function showWdigetOptions


    hideWidgetOptions: function(e){

        var widget = e.src();
        var options = getObjectFromID(widget.id + "Options");
        options.style.display = "none";

    },//end function hideWdigetOptions


    removeWidget: function(e){

        var button = e.src();

        var uuid = button.id.replace("RemoveButton", "");

        var widget = getObjectFromID(uuid);

        //need to get widget's name
        var widgetTitle = "";

        for(var i=0; i<widget.childNodes.length; i++)
            if(widget.childNodes[i].nodeName == "H2")
                if(widget.childNodes[i].className == "widgetTitles")
                    widgetTitle = widget.childNodes[i].innerHTML;

        var parentDiv = widget.parentNode;

        //remove widget from page
        parentDiv.removeChild(widget);

        //remove widget from session
        var theURL = "snapshot_ajax.php?uuid=" + encodeURIComponent(uuid);
        loadXMLDoc(theURL,null,false);

        // add widget to configure "add" select
        var addSelect = getObjectFromID(parentDiv.id + "AddWidget");

        // if there is not optgroup titled "recently removed", then add it
        var optgroup = null;

        for(var i=0; i<addSelect.childNodes.length; i++)
            if(addSelect.childNodes[i].nodeName == "OPTGROUP")
                if(addSelect.childNodes[i].label == "Recently Removed")
                    optgroup = addSelect.childNodes[i];

        if(!optgroup){

            var noWidgets = null;

            for(var i=0; i<addSelect.childNodes.length; i++)
                if(addSelect.childNodes[i].nodeName == "OPTION")
                    noWidgets = addSelect.childNodes[i];

            if(noWidgets)
                addSelect.removeChild(noWidgets);

            optgroup = document.createElement('optgroup');
            optgroup.label = "Recently Removed";

            addSelect.appendChild(optgroup);

        }//endif no optgrouop

        var newOption = document.createElement('option');
        newOption.value = uuid;

        optgroup.appendChild(newOption);
        newOption.innerHTML = widgetTitle;

        //remove widget from "after" select
        var afterSelect = getObjectFromID(parentDiv.id + "AfterWidget");

        var optionRemove = null;

        for(var i=0; i<afterSelect.childNodes.length; i++)
            if(afterSelect.childNodes[i].nodeName == "OPTION")
                if(afterSelect.childNodes[i].value == uuid)
                    optionRemove = afterSelect.childNodes[i];

        afterSelect.removeChild(optionRemove);

        if(!afterSelect.value){

            newOption = document.createElement("option");
            newOption.value = "first";
            afterSelect.appendChild(newOption);
            newOption.innerHTML = "place first";

        }//endif

    },//end function remove widget


    configureButtonClick: function(e){

        var button = e.src();

        var section = getObjectFromID(button.id + "Dropdown");

        if(section.style.display == "block")
            section.style.display = "none";
        else
            section.style.display = "block";

    }, //end function configureButtonClick


    widgetAdd: function(e){

        //check the configure form and hen submit the form
        var button = e.src();
        var theform = button.form;

        var area = button.id.replace("AddButton", "");

        var addSelect = getObjectFromID(area + "AddWidget");

        var afterSelect = getObjectFromID(area + "AfterWidget");

        if(addSelect.value && afterSelect.value)
            theform.submit();

    }, //end function widgetAdd


    widgetCancel: function(e){

        var button = e.src();
        var options = getObjectFromID(button.id.replace("CancelButton", "") + "ConfigureDropdown");

        options.style.display = "none";

   }//end function widgetCancel

}//end class snapshot



/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

        //system message accordian
	var systemMessageDivs = getElementsByClassName('systemMessages');
	var systemMessageLinks = getElementsByClassName('systemMessageLinks');

	var systemMessageAccordion = new fx.Accordion(systemMessageLinks, systemMessageDivs, {opacity: true, duration:150});

        var bigArea = getObjectFromID("bigArea")
        connect(bigArea, "onmouseover", snapshot.showConfigure);
        connect(bigArea, "onmouseout", snapshot.hideConfigure);

        var littleArea = getObjectFromID("littleArea")
        connect(littleArea, "onmouseover", snapshot.showConfigure);
        connect(littleArea, "onmouseout", snapshot.hideConfigure);

        var widgets = getElementsByClassName('widgets');
	for(var i=0; i<widgets.length; i++){

		connect(widgets[i], "onmouseover", snapshot.showWidgetOptions);
		connect(widgets[i], "onmouseout", snapshot.hideWidgetOptions);

	}//endfor

        var widgetRemoveButtons = getElementsByClassName('widgetRemoves');
	for(var i=0; i<widgetRemoveButtons.length; i++)
		connect(widgetRemoveButtons[i], "onclick", snapshot.removeWidget);

        var configureButtons = getElementsByClassName('configureButtons');
	for(var i=0; i<configureButtons.length; i++)
		connect(configureButtons[i], "onclick", snapshot.configureButtonClick);

        var widgetAddButtons = getElementsByClassName('widgetAddButtons');
	for(var i=0; i<widgetAddButtons.length; i++)
		connect(widgetAddButtons[i], "onclick", snapshot.widgetAdd);

        var widgetCancelButtons = getElementsByClassName('widgetCancelButtons');
	for(var i=0; i<widgetCancelButtons.length; i++)
		connect(widgetCancelButtons[i], "onclick", snapshot.widgetCancel);

});
