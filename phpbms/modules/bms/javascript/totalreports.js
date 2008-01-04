/*
 $Rev: 316 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-10-09 13:58:11 -0600 (Tue, 09 Oct 2007) $
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

report = {
    
    cancel: function(){
	
	window.close();
	
    },//endmethod
    
    validateForm: function (){
	
	if(report.prepareGroupings() && report.prepareColumns()){
	    
	    var theForm = getObjectFromID("GroupForm");
	    theForm.submit();
	    
	}else{
	    
	    alert("Make sure you have selected unique groups and columns for each line.");
	    
	}
	
    },//endmethod
    
    prepareGroupings: function (){
        
	var groupArray = new Array();
	
        for(var i=0; i<GroupParams.length; i++){
	    
	    var groupField = getObjectFromID("Group"+GroupParams[i]+"Field");
	    if(groupField.value == "0"){
		
		
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
    
    prepareColumns: function (){
        
	var columnArray = new Array();
	
        for(var i=0; i<ColumnParams.length; i++){
            
	    var columnField = getObjectFromID("Column"+ColumnParams[i]+"Field");
            
	    if(columnField.value == "0"){
		
                return false;
		
	    }else{
		
		for(var j=0; j<columnArray.length; j++){
		    
		    if(columnField.value == columnArray[j]+1){
			
			return false;
			
		    }//endif
		    
		}//endfor
		
		columnArray[columnArray.length] = columnField.value-1;
		
	    }//endif
	    
	}//endfor
        
	var hiddenColumns = getObjectFromID("columns");
	hiddenColumns.value = columnArray.join("::");
	
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
        
    },//endmethod
    
    columnAddLine: function (){
        
	var tempMinus = getObjectFromID("Column"+ColumnParams[0]+"Minus");
	if(tempMinus.className == "graphicButtons buttonMinusDisabled")
    	tempMinus.className = "graphicButtons buttonMinus";

	var tempDiv = getObjectFromID("Column"+ColumnParams[0]);
	var tempContent = tempDiv.innerHTML;
	var REcriteria = new RegExp("Column"+ColumnParams[0],"g");
        var newid = "Column"+(ColumnParams[ColumnParams.length-1]+1)
        
	tempContent = tempContent.replace(REcriteria, newid);
        
	var newDiv = document.createElement("div");
	newDiv.id = newid;
	newDiv.innerHTML = tempContent;
	
	var containerDiv = getObjectFromID("theColumns");
	containerDiv.appendChild(newDiv);
	
	ColumnParams[ColumnParams.length] = ColumnParams[ColumnParams.length-1]+1;
	
        
        var newPlusButton = getObjectFromID(newid+"Plus");
        connect(newPlusButton, "onclick", report.columnAddLine);
        
        var newMinusButton = getObjectFromID(newid+"Minus");
        connect(newMinusButton, "onclick", report.columnRemoveLine);
        
        
    },//endmethod
    
    columnRemoveLine: function (e){
        
        var thebutton = e.src();
        
	if(thebutton.className=="graphicButtons buttonMinusDisabled")
    	return false;
	
	var theDiv = thebutton.parentNode;
		
	var containerDiv = getObjectFromID("theColumns");
	containerDiv.removeChild(theDiv);
	var theid = theDiv.id.replace(/Column/g,"");
	theDiv=null;
        
	for(var i=0;i<ColumnParams.length;i++){
            
            if(ColumnParams[i] == theid){
                
                ColumnParams.splice(i,1);
                break;
                
            }//endif
            
	}//endfor
        
	if(ColumnParams.length == 1){
            
            var tempButton = getObjectFromID("Column"+ColumnParams[0]+"Minus");
            tempButton.className="graphicButtons buttonMinusDisabled"
            
	}//endif
        
    }//endmethod
    
}//end class


/*listner*/

connect(window,"onload",function() {
	
    GroupParams = [1];
    ColumnParams = [1];
    
    var plusGroupButton = getObjectFromID("Group1Plus");
    connect(plusGroupButton, "onclick", report.groupAddLine);
        
    var minusGroupButton = getObjectFromID("Group1Minus");
    connect(minusGroupButton, "onclick", report.groupRemoveLine);
    
    var plusColumnButton = getObjectFromID("Column1Plus");
    connect(plusColumnButton, "onclick", report.columnAddLine);
        
    var minusColumnButton = getObjectFromID("Column1Minus");
    connect(minusColumnButton, "onclick", report.columnRemoveLine);
    
    var printButton = getObjectFromID("print");
    connect(printButton, "onclick", report.validateForm);
    
    var cancelButton = getObjectFromID("cancel");
    connect(cancelButton, "onclick", report.cancel);
})