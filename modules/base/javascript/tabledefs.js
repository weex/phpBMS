function toggleDefaultSearch(){
	var dsn=getObjectFromID("defaultsearchtypeNone");
	var theDiv=getObjectFromID("defaultQuickSearch");
	if (dsn.checked)
		theDiv.style.display="none";
	else
		theDiv.style.display="block";
}