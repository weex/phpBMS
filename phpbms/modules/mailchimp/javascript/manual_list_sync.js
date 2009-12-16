list = {
    
    sync:function(){
        
        var theURL = "manual_list_sync_ajax.php";
        
        var resultPic = getObjectFromID("resultPic");
        
        resultPic.className = "running";
		resultPic.innerHTML = "Running...";

		loadXMLDoc(theURL,null,false);

		var JSONresponse;
		JSONresponse = eval("("+ req.responseText + ")");
        
        if(JSONresponse.type == "success"){
            resultPic.className = "success";
            resultPic.innerHTML = "Success";
        }else if(JSONresponse.type == "warning"){
            resultPic.className = "warning";
            resultPic.innerHTML = "Minor Errors (see results)";
        }else{
			resultPic.className = "fail";
            resultPic.innerHTML = "Fatal Error";
		}//endif
        
        list.reportResult(JSONresponse);
        
    },//end function
    
    reportResult:function(response){
        
        var resultText = getObjectFromID("resultText");

        if(response.type && response.details){
            
            if(response.type == "success"){
                resultText.innerHTML = "Success";
            }else{
                resultText.innerHTML = "The following errors were found:";
                for(var error in response.details){
                    
                    result.innerHTML += "\n";
                    result.innerHTML += capitalize(error.type)+": "+error.message+"( "+error.code+" )"
                    
                }//end for
                
            }//end if
            
        }else{
            
            result.innerHTML = "Fatal Error: No response from script.";
            
        }//end if
        
    },//end function
    
    loadOptionAccordion:function(){
		
		var optionsDivs = new Array();
		optionsDivs[optionsDivs.length]=getObjectFromID("resultDiv");
	
		var optionsLinks = new Array();
		optionsLinks[optionsLinks.length]=getObjectFromID("showResults");
	
		var optionsAccordion = new fx.Accordion(optionsLinks, optionsDivs, {opacity: true, duration:250, onComplete:function(){list.expandResultText()}});
		
	},//end function
    
    expandResultText:function(){
        
        var switchButton=getObjectFromID("showResults");
        
		if(switchButton.className=="graphicButtons buttonDown"){
			switchButton.className="graphicButtons buttonUp"
			switchButton.firstChild.innerHTML="hide results";
		} else {
			switchButton.className="graphicButtons buttonDown"
			switchButton.firstChild.innerHTML="show results";
		}//end if
        
    },//end function
	
	goToSearchPage:function(){
		document.location = "../../search.php?id=tbld%3A6d290174-8b73-e199-fe6c-bcf3d4b61083"
	}//end function
    
}
function capitalize(string){
	
	var theReturn = string.substring(0,1).toUpperCase();
	theReturn += string.substring(1, string.length);
	return theReturn;
	
}//end function

connect(window, "onload", function(){
   
   list.loadOptionAccordion();
   
   var sync = getObjectFromID("sync");
   connect(sync, "onclick", list.sync);
   
   var cancelButton = getObjectFromID("cancelButton");
   connect(cancelButton, "onclick", list.goToSearchPage);
    
});