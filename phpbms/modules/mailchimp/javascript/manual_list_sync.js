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
        }else{
            resultPic.className = "error";
            resultPic.innerHTML = "Error (see debug)";
        }//end if
        
        list.reportResult(JSONresponse);
        
    },//end function
    
    reportResult:function(response){
        
        var resultText = getObjectFromID("resultText");
console.log(response);
        if(response.type && response.details){
            
            if(response.type == "success"){
                resultText.innerHTML = "Success";
            }else{
                resultText.innerHTML = "Errror:";
                for(var error in response.details){
                    
                    result.innerHTML += "\n";
                    result.innerHTML += error.message+"( "+error.code+" )"
                    
                }//end for
                
            }//end if
            
        }else{
            
            result.innerHTML = "Fatal Error: No response from script.";
            
        }//end if
        
    }//end function
    
}

connect(window, "onload", function(){
   
   var sync = getObjectFromID("sync")
   connect(sync, "onclick", list.sync);
   
    
});