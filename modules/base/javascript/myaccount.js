function changePass(){
	var theform=getObjectFromID("record");
	var thecommand=getObjectFromID("command");
	var curPass=getObjectFromID("curPass");
	var newPass=getObjectFromID("newPass");
	var confirmPass=getObjectFromID("confirmPass");
	
	var thereturn="";
	
	if(curPass.value=="" || newPass.value=="")
		thereturn="Current and new password cannot be blank.";
	else
		if(newPass.value!=confirmPass.value)
			thereturn="New and re-typed passwords did not match.";

	if(thereturn==""){
		thecommand.value="Change Password";
		theform.submit();
	} else
		alert(thereturn);
}

function changeContact(){
	var theform=getObjectFromID("record");
	var thecommand=getObjectFromID("command");
	if(validateForm(theform)){
		thecommand.value="Update Contact";
		theform.submit();
	}
}

function changeEmail(){
    var theform=getObjectFromID("record");
    var thecommand=getObjectFromID("command");
    if(validateForm(theform)){
        thecommand.value="Update Email";
        theform.submit();
    }
}
