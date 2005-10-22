function init(){
	changeType();
}


function changeType(){
	var typeP=getObjectFromID("typePercentage");
	var typeA=getObjectFromID("typeAmount");
	var aLabel=getObjectFromID("aValue");
	var pLabel=getObjectFromID("pValue");
	if(typeP.checked){
		pLabel.style.display="block";
		aLabel.style.display="none";
	}
	if(typeA.checked){
		aLabel.style.display="block";
		pLabel.style.display="none";
	}
}