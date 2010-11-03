function checkScript(thecheckbox){
	var thescriptp=getObjectFromID("pProcessscript");
	if(thecheckbox.checked)
		thescriptp.style.display="block";
	else
		thescriptp.style.display="none";
}
