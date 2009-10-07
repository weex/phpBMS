/*
 $Rev: 643 $ | $LastChangedBy: nate $
 $LastChangedDate: 2009-09-02 14:00:56 -0600 (Wed, 02 Sep 2009) $
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
pushrecords = {

    submitForm: function(e){

		var theForm = getObjectFromID("record");

		if(!validateForm(theForm)){
			if(e)
				e.stop();
			return false;
		}

        //skip validation if cancel
		cancelClick = getObjectFromID("cancelclick");
		if(cancelClick.value !=0)
			return true;

        var useCustomDestUuid = getObjectFromID("usecustomdestuuid");
        var customDestUuid = getObjectFromID("customdestuuid");

        var whereSelection = getObjectFromID("whereselection");
        var customWhere = getObjectFromID("customwhere");

		var errorArray = Array();
		if(useCustomDestUuid.checked && customDestUuid.value == "")
			errorArray[errorArray.length] = "Records marked as using a custom destination uuid must have a custom destination uuid.";

		if(whereSelection.value == "custom" && customWhere.value == "")
			errorArray[errorArray.length] = "Records marked as using a custom saved search must have a custom saved search.";


		if(errorArray.length > 0){

			var content = "<p>The following errors were found:</p><ul>";

			for(var i=0; i < errorArray.length; i++)
				content += "<li>"+errorArray[i]+"</li>";

			content += "</ul>";
			
			alert(content);

			if(e)
				e.stop();
			return false;

		}//end if

        return true;

    },//end function

    toggleCustomCommand: function(){

        var apiCommand = getObjectFromID("apicommand")
        var customCommandSpan = getObjectFromID("customcommandspan");

        if(apiCommand.value == "custom")
            customCommandSpan.style.display = "block";
        else
            customCommandSpan.style.display = "none";


    },//end function

    toggleCustomUuid: function(){

        var useCustomDestUuid = getObjectFromID("usecustomdestuuid");
        var customDestUuidSpan = getObjectFromID("customdestuuidspan");
        var destUuidSpan = getObjectFromID("destuuidspan");

        if(useCustomDestUuid.checked){
            customDestUuidSpan.style.display = "block";
            destUuidSpan.style.display = "none";
        }else{
            customDestUuidSpan.style.display = "none";
            destUuidSpan.style.display = "block";
        }//end if

    },//end function

    toggleCustomWhere: function(){

        var whereSelection = getObjectFromID("whereselection");
        var customWhereSpan = getObjectFromID("customwherespan");

        switch(whereSelection.value){

            case "custom":
                customWhereSpan.style.display = "block";
                break;

            default:
                if(customWhereSpan.style.display != "none")
                    customWhereSpan.style.display = "none";
                break;

        }//end switch

    },//end function

    syncDestUuid: function(){

        var originUuid = getObjectFromID("originuuid");
        var destUuid = getObjectFromID("destuuid");

        destUuid.value = originUuid.value;

    },//end function
	
	populateCustomWhere: function(){
		
		var whereselection = getObjectFromID("whereselection");
		
		if(whereselection.value == "custom"){
			
			var originUuid = getObjectFromID("originuuid");
			var customWhere = getObjectFromID("customwhere");
			
			var theurl = "push_records_ajax.php?id=" + originUuid.value;
			
			loadXMLDoc(theurl,null,false);
			
			try {
	
				var searches = eval( "(" +req.responseText + ")" );
	
			} catch(err) {
	
				alert(err);
	
			}//end try/catch
			
			if(searches.length){
				
				/**
				  *  Tear it down 
				  */
				
				var childNode;
				while(childNode = customWhere.firstChild)
					customWhere.removeChild(childNode);
					
				/**
				  *   Build it up
				  */
				
				var tempoption;
				for(var i = 0; i < searches.length; i++){
					
					
					tempoption = document.createElement("option");
					tempoption.setAttribute("value", searches[i].uuid);
					tempoption.innerHTML = searches[i].name;
					customWhere.appendChild(tempoption);
					
				}//end for
				
			}//end if
			
		}//end if
		
	},//end function
	
	loadOptionAccordion:function(){
		
		var optionsDivs = new Array();
		optionsDivs[optionsDivs.length]=getObjectFromID("moreoptions");
	
		var optionsLinks = new Array();
		optionsLinks[optionsLinks.length]=getObjectFromID("showoptions");
	
		var optionsAccordion = new fx.Accordion(optionsLinks, optionsDivs, {opacity: true, duration:250, onComplete:function(){pushrecords.switchOptions()}});
		
	},//end function
	
	switchOptions: function(){
		
		var switchButton=getObjectFromID("showoptions");
		if(switchButton.className=="graphicButtons buttonDown"){
			switchButton.className="graphicButtons buttonUp"
			switchButton.firstChild.innerHTML="simple options";
			switchButton.style.width = "110px";
		} else {
			switchButton.className="graphicButtons buttonDown"
			switchButton.firstChild.innerHTML="advanced options";
			switchButton.style.width = "125px";
		}
		
	}//end function

}//end object --pushrecords--

connect(window, "onload", function(){

    pushrecords.toggleCustomUuid();
    pushrecords.toggleCustomWhere();
    pushrecords.toggleCustomCommand();
	pushrecords.loadOptionAccordion();

    var theForm = getObjectFromID("record");
    connect(theForm, "onsubmit", pushrecords.submitForm);

    var useCustomDestUuid = getObjectFromID("usecustomdestuuid");
    connect(useCustomDestUuid, "onchange", pushrecords.toggleCustomUuid);

    var apiCommand = getObjectFromID("apicommand");
    connect(apiCommand, "onchange", pushrecords.toggleCustomCommand);

    var whereSelection = getObjectFromID("whereselection");
    connect(whereSelection, "onchange", pushrecords.toggleCustomWhere);
	connect(whereSelection, "onchange", pushrecords.populateCustomWhere);

    var originUuid = getObjectFromID("originuuid");
    connect(originUuid, "onchange", pushrecords.syncDestUuid);
	connect(originUuid, "onchange", pushrecords.populateCustomWhere);

});