function checkPassword(){
	var pass1=getObjectFromID("password");
	var pass2=getObjectFromID("password2");
	
	if(pass1.value!=pass2.value && pass1.form["command"].value!="cancel"){
		alert("Password values did not match");
		return false;
	} else
		return true;
}

function moveRole(frombox,tobox){
	var i;
	
	var fromselect=getObjectFromID(frombox);
	var toselect=getObjectFromID(tobox);
	
	var myresult = "";
	for(i=0;i<fromselect.length;i++){
		if (fromselect.options[i].selected){
			toselect.options[toselect.options.length]= new Option(fromselect.options[i].text,fromselect.options[i].value)
			fromselect.options[i] = null;
			i=-1;
		}//end if
	}// end for
	
	getObjectFromID("roleschanged").value=1;
}


function submitForm(theform){
	if (theform["cancelclick"]){
		if (theform["cancelclick"].value!=0) return true;
	}
	
	var thereturn=true;
	if(!checkPassword())
		thereturn=false;
	if(!validateForm(theform)) 
		thereturn=false;

	var roleschanged=getObjectFromID("roleschanged");
	if(roleschanged)
		if(roleschanged.value==1){
			var i;
			var newroles=getObjectFromID("newroles");
			var assignedroles=getObjectFromID("assignedroles");		
			for(i=0;i<assignedroles.options.length;i++)
				newroles.value+=assignedroles.options[i].value+",";
		}
	
	return thereturn;
}