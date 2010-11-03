function checkScript(thecheckbox){
	var thescriptp=getObjectFromID("pEstimationscript");
	if(thecheckbox.checked)
		thescriptp.style.display="block";
	else
		thescriptp.style.display="none";
}