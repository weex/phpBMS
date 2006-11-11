function moveUser(frombox,tobox){
	var i;
	
	var fromselect=getObjectFromID(frombox);
	var toselect=getObjectFromID(tobox);
	
	var myresult = "";
	for(i=0;i<fromselect.length;i++){
		if (fromselect.options[i].selected){
			toselect.options[toselect.options.length]= new
			Option(fromselect.options[i].text,fromselect.options[i].value)
			fromselect.options[i] = null;
			i=-1;
		}//end if
	}// end for
	
	getObjectFromID("userschanged").value=1;
}

function submitForm(theform){
	var thereturn=true;
	if(!validateForm(theform)) 
		thereturn=false;

	var userschanged=getObjectFromID("userschanged");
	if(userschanged)
		if(userschanged.value==1){
			var i;
			var newusers=getObjectFromID("newusers");
			var assignedusers=getObjectFromID("assignedusers");		
			for(i=0;i<assignedusers.options.length;i++)
				newusers.value+=assignedusers.options[i].value+",";
		}
	
	return thereturn;
}