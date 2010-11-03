/*
 $Rev: 290 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-27 18:15:00 -0600 (Mon, 27 Aug 2007) $
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
report = {

    submitForm: function(e){

        //skip validation if cancel
        var cancelClick = getObjectFromID("cancelclick");
        if(cancelClick.value !=0)
                return true;

        var theForm = getObjectFromID("record");

        if(!validateForm(theForm)){

                if(e)
                    e.stop();

                return false;

        }//endif

        var theid = getObjectFromID("id");

        if(theid.value) {

            //first prep the del list
            var delList = getObjectFromID("rsDelList")
            if(!delList.value)
                delList.value = "[]";

            //next the update list
            var updateList = getObjectFromID("rsUpdates");
            if(!updateList.value)
                updateList.value = "[]";


            //last, we need to build the addList
            var jsonString = "[";
            var addNames = getElementsByClassName("rsNewNames");
            var addValue;
            var addID;

            for(var i=0; i < addNames.length; i++){

                addID = addNames[i].id.substring(9);
                addValue = getObjectFromID("rsNewValue" + addID);

                jsonString = jsonString + '{ "name": "' + addNames[i].value.replace(/\"/g,'\\"') + '", "value": "' + addValue.value.replace(/\"/g,'\\"')+ '"},';

            }//endfor

            if(jsonString.length > 1)
                jsonString = jsonString.substring(0,jsonString.length-1);

            jsonString += "]";

            var addsList = getObjectFromID("rsAdds");
            addsList.value = jsonString;

        }//endif

        return true;

    }//end function verify

}//end struct report



reportSettings = {

    newSettings: 1,

    add: function(){

        var tbody = getObjectFromID("rsTbody");
        var footer = getObjectFromID("rsFooterTr");
        var row = tbody.childNodes.length%2 + 1;
        var tempInput;
        var tempTd

        var theName = getObjectFromID("rsAddName");
        var theValue = getObjectFromID("rsAddValue");

        var noSettings = getObjectFromID("noSettings");
        if(noSettings)
            tbody.removeChild(noSettings);

        var theTr = document.createElement("tr");
        theTr.className = "qr" + row + " rsRows";
        theTr.id = "rsNewRow" + reportSettings.newSettings;

        tempInput = document.createElement("input");
        tempInput.id = "rsNewName" + reportSettings.newSettings;
        tempInput.className = "rsNewNames";
        tempInput.type = "hidden";
        tempInput.value = theName.value;

        tempTd = document.createElement("td");
        tempTd.align = "right";
        tempTd.innerHTML = "<strong>"+ theName.value + "</strong>";
        tempTd.appendChild(tempInput);

        theTr.appendChild(tempTd);


        tempInput = document.createElement("input");
        tempInput.id = "rsNewValue" + reportSettings.newSettings;
        tempInput.className = "rsNewValues";
        tempInput.size = 32;
        tempInput.value = theValue.value;

        tempTd = document.createElement("td");
        tempTd.appendChild(tempInput);

        theTr.appendChild(tempTd);


        tempTd = document.createElement("td");
        tempTd.innerHTML = "string";

        theTr.appendChild(tempTd);


        tempTd = document.createElement("td");
        tempTd.innerHTML = "user added setting";

        theTr.appendChild(tempTd);


        tempTd = document.createElement("td");
        tempTd.innerHTML = '<button type="button" id="rsNewDelButton' + reportSettings.newSettings + '" class="graphicButtons buttonMinus rsDelButtons" title="Remove Setting"><span>-</span></button>';

        theTr.appendChild(tempTd);

        tbody.insertBefore(theTr, footer);

        var theButton = getObjectFromID("rsNewDelButton" + reportSettings.newSettings);
        connect(theButton, "onclick", reportSettings.del);

        theName.value = "";
        theValue.value = "";

        reportSettings.newSettings++;

    },//end function add


    changedExisting: function(e){

        var srcObj = e.src();
        var theTR= srcObj.parentNode.parentNode;

        var theID = theTR.id.substring(7);
        var value = srcObj.value;

        var updatesInput = getObjectFromID("rsUpdates");
        var updates = Array();
        var inList = false;

        if(updatesInput.value)
            updates = eval(updatesInput.value)
        else
            updates = Array();

        for(var i = 0; i<updates.length; i++)
            if(updates[i]["id"] == theID){

                updates[i]["value"] = value;
                inList = true;
                i = updates.length;

            }//endif

        if(!inList)
            updates[updates.length] = {"id": theID, "value": value};

        var jsonString = "[";

        for(i = 0; i<updates.length; i++)
            jsonString = jsonString + '{ "id":' + updates[i].id + ',"value": "' + updates[i].value.replace(/\"/g,'\\"') + '"},';

        jsonString = jsonString.substring(0,jsonString.length-1) + "]";

        updatesInput.value = jsonString;

    },//end function changedExisting


    del: function(e){

        var srcObj = e.src();
        var theTR= srcObj.parentNode.parentNode;
        var theTbody = theTR.parentNode;

        if(theTR.id.substring(0,7) == "rsExRow"){

            var theID = theTR.id.substring(7);
            var delInput = getObjectFromID("rsDelList");
            var delList;

            if(delInput.value)
                delList = eval(delInput.value)
            else
                delList = Array();

            delList[delList.length] = theID;

            var jsonString = "[";

            for(i = 0; i<delList.length; i++)
                jsonString = jsonString + delList[i] + ',';

            jsonString = jsonString.substring(0,jsonString.length-1) + "]";

            delInput.value = jsonString

        }//endif

        theTbody.removeChild(theTR);

    }//end function delete

}//end struct reportSettings

/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

    var theForm = getObjectFromID("record");
    connect(theForm, "onsubmit", report.submitForm);

    var theID = getObjectFromID("cmId");

    /**
     * we initialize report settings stuff only if we are editing
     * an existing record (id preent)
     */
    if(theID.innerHTML){

        /**
         * add button hookup
         */
        var theAddButton = getObjectFromID("rsButtonAdd");
        connect(theAddButton, "onclick", reportSettings.add);

        /**
         * on change for existing setting's onchange
         */
        var inputs = getElementsByClassName("rsValues");
        for(var i = 0; i<inputs.length; i++)
            connect(inputs[i], "onchange", reportSettings.changedExisting);

        /**
         * delete button initial hook ups
         */
        var inputs = getElementsByClassName("rsDelButtons");
        for(var i = 0; i<inputs.length; i++)
            connect(inputs[i], "onclick", reportSettings.del);


    }//endif theID

});
