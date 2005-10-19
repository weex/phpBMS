function showTypeDetails(){
	var radio1=getObjectFromID("type1");
	
	var detailsFieldset=getObjectFromID("details");
	var tabledefLabel=getObjectFromID("thetabledef");
	var linkLabel=getObjectFromID("thelink");
	if(radio1.checked){
		detailsFieldset.style.display="none";
	} else {
		var radio2=getObjectFromID("type2");
		if(radio2.checked){
			detailsFieldset.style.display="none";
			tabledefLabel.style.display="block"
			linkLabel.style.display="none";
			detailsFieldset.style.display="block";
		} else {
			var radio3=getObjectFromID("type3");
			if(radio3.checked){
				detailsFieldset.style.display="none";
				linkLabel.style.display="block";
				tabledefLabel.style.display="none"
				detailsFieldset.style.display="block";
			}
		}
	}
}