scheduler = {
    
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

        var scripttype = getObjectFromID("scripttype");
        var job = getObjectFromID("job");
		var pushrecordid = getObjectFromID("pushrecordid");

		var errorArray = Array();
		if(scripttype.value == "job" && job.value == "")
			errorArray[errorArray.length] = "Scheduled jobs must have a script.";

		if(scripttype.value == "pushrecord" && pushrecordid.value == "")
			errorArray[errorArray.length] = "Scheduled pushes must have a push record.";

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
    
    
    togglePushrecord: function(){
        
        var scripttype = getObjectFromID("scripttype");
        var jobp = getObjectFromID("jobp");
        var pushrecordidp = getObjectFromID("pushrecordidp");
		
		switch(scripttype.value){
            
            case "job":
                jobp.style.display = "block";
                pushrecordidp.style.display = "none";
            break;
        
            case "pushrecord":
                jobp.style.display = "none";
                pushrecordidp.style.display = "block";
            break;
            
        }//end switch
        
    }//end function
    
}//end object

connect(window, "onload", function(){
	
	scheduler.togglePushrecord();
	
	var theForm = getObjectFromID("record");
	connect(theForm, "onsubmit", scheduler.submitForm);
	
	var scripttype = getObjectFromID("scripttype");
	connect(scripttype, "onchange", scheduler.togglePushrecord);
	
});