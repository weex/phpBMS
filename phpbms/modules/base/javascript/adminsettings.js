function toggleEncryptionEdit(seedcheck){
	var seedinput=getObjectFromID("sencryption_seed");
	var currpassinput=getObjectFromID("currentpassword");
	var updatebutton=getObjectFromID("updateSettings3");
	if (seedcheck.checked){
		seedinput.removeAttribute("readOnly");
		currpassinput.removeAttribute("readOnly");
		updatebutton.disabled=false;
		seedinput.className="";
		currpassinput.className="";
		seedinput.focus();
	} else {
		updatebutton.disabled=true;
		seedinput.setAttribute("readOnly","readonly");		
		currpassinput.setAttribute("readOnly","readonly");		
		seedinput.className="uneditable";
		currpassinput.className="uneditable";
	}
}


function processForm(theform){
	var changeseed=getObjectFromID("changeseed");
	var $thereturn=false;
	var doencryptionupdate = getObjectFromID("doencryptionupdate")
	
	if(changeseed.checked && doencryptionupdate.value!=1)
		alert("Encryption Seed Must be updated separately from other settings.");
	else {
		if(doencryptionupdate.value==1){
			var seedinput=getObjectFromID("sencryption_seed");
			if(seedinput.value==""){
				alert("Encryption seed cannot be blank.");
				$thereturn=false;
			}
			else
			$thereturn=true;
		}
		else{
			$thereturn=validateForm(theform);
		}
	} 
	return $thereturn;
}