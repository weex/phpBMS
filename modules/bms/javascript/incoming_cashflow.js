report = {

	submitForm: function(e){

		var theform = getObjectFromID("record");

		if(report.prepareGroupings()){

			if(validateForm(theform))
				theform.submit();

		} else {

		    alert("You must have at least one grouping set, and duplicates are not allowed.");

		}

	},//end method


    prepareGroupings: function (){

	var groupArray = new Array();

        for(var i=0; i<GroupParams.length; i++){

	    var groupField = getObjectFromID("Group"+GroupParams[i]+"Field");
	    if(groupField.value == "0"){

		return false;

	    }else{

		for(var j=0; j<groupArray.length; j++){

		    if(groupField.value == groupArray[j]+1){

			return false;

		    }//endif

		}//endfor

		groupArray[groupArray.length] = groupField.value-1;

	    }//endif

	}//endfor

	var hiddenGroupings = getObjectFromID("groupings");
	hiddenGroupings.value = groupArray.join("::");

	return true;

    },//endmethod


    groupAddLine: function (){

	var tempMinus = getObjectFromID("Group"+GroupParams[0]+"Minus");
	if(tempMinus.className == "graphicButtons buttonMinusDisabled")
    	tempMinus.className = "graphicButtons buttonMinus";

	var tempDiv = getObjectFromID("Group"+GroupParams[0]);
	var tempContent = tempDiv.innerHTML;
	var REcriteria = new RegExp("Group"+GroupParams[0],"g");
        var newid = "Group"+(GroupParams[GroupParams.length-1]+1)
	tempContent = tempContent.replace(REcriteria, newid);

	var newDiv = document.createElement("div");
	newDiv.id = newid;
	newDiv.innerHTML = tempContent;

	var containerDiv = getObjectFromID("theGroups");
	containerDiv.appendChild(newDiv);

	GroupParams[GroupParams.length] = GroupParams[GroupParams.length-1]+1;


        var newPlusButton = getObjectFromID(newid+"Plus");
        connect(newPlusButton, "onclick", report.groupAddLine);

        var newMinusButton = getObjectFromID(newid+"Minus");
        connect(newMinusButton, "onclick", report.groupRemoveLine);


    },//endmethod


    groupRemoveLine: function (e){

        var thebutton = e.src();

	if(thebutton.className=="graphicButtons buttonMinusDisabled")
    	return false;

	var theDiv = thebutton.parentNode;

	var containerDiv = getObjectFromID("theGroups");
	containerDiv.removeChild(theDiv);
	var theid = theDiv.id.replace(/Group/g,"");
	theDiv=null;

	for(var i=0;i<GroupParams.length;i++){

            if(GroupParams[i]==theid){

                GroupParams.splice(i,1);
                break;

            }//endif

	}//endfor

	if(GroupParams.length==1){

            var tempButton = getObjectFromID("Group"+GroupParams[0]+"Minus");
            tempButton.className="graphicButtons buttonMinusDisabled"

	}//endif

	return true;

    }//endmethod

}//end class


/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

	var printButton = getObjectFromID("printButton");
	if(printButton)
		connect(printButton, "onclick", report.submitForm);

	var cancelButton = getObjectFromID("cancelButton");
	if(cancelButton)
		connect(cancelButton, "onclick", function(e){ window.close() });

	GroupParams = [1];

	var plusGroupButton = getObjectFromID("Group1Plus");
	connect(plusGroupButton, "onclick", report.groupAddLine);

	var minusGroupButton = getObjectFromID("Group1Minus");
	connect(minusGroupButton, "onclick", report.groupRemoveLine);

})
