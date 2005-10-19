function editPreviewWebDesc(thebutton){
	var editDiv=getObjectFromID("webDescEdit");
	var previewDiv=getObjectFromID("webDescPreview");
	var webDesc=getObjectFromID("webdescription");
	
	if (thebutton.value=="preview"){
		thebutton.value="edit";
		previewDiv.style.display="block";
		editDiv.style.display="none";
		previewDiv.innerHTML=webDesc.value;
	} else {
		thebutton.value="preview";
		previewDiv.style.display="none";
		editDiv.style.display="block";
	}
}

function calculateMarkUp(){
	var thecost=getObjectFromID("unitcost");
	var theprice=getObjectFromID("unitprice");
	var unitcost="";
	var unitprice="";
	
	for(i=0;i<thecost.value.length;i++){
		if (thecost.value.charAt(i)!="$" && thecost.value.charAt(i)!="+" && thecost.value.charAt(i)!=",") unitcost+=thecost.value.charAt(i);
	}
	unitcost=parseFloat(unitcost);

	for(i=0;i<theprice.value.length;i++){
		if (theprice.value.charAt(i)!="$" && theprice.value.charAt(i)!="+" && theprice.value.charAt(i)!=",") unitprice+=theprice.value.charAt(i);
	}
	unitprice=parseFloat(unitprice);

	if(unitcost!=0 && unitprice!=0){
		
		var markup=getObjectFromID("markup");
		markup.value=(Math.round((unitprice/unitcost -1)*10000)/100)+"%"
		
	}
}

function calculatePrice(){
	var themarkup=getObjectFromID("markup");
	var thecost=getObjectFromID("unitcost");
	var theprice=getObjectFromID("unitprice");
	var unitcost="";
	
	var markup=getNumberFromPercentage(themarkup.value);
	
	for(i=0;i<thecost.value.length;i++){
		if (thecost.value.charAt(i)!="$" && thecost.value.charAt(i)!="+" && thecost.value.charAt(i)!=",") unitcost+=thecost.value.charAt(i);
	}
	unitcost=parseFloat(unitcost);
	var newnumber=(Math.round((unitcost+(markup*unitcost/100))*100)/100).toString();
	theprice.value=formatDollar(newnumber);	
}

function updatePictureStatus(pic,status){
	var thechange=getObjectFromID(pic+"change");
	thechange.value=status;
}

function deletePicture(pic){
	var thepic=getObjectFromID(pic+"pic");
	var deleteDiv=getObjectFromID(pic+"delete");
	var addDiv=getObjectFromID(pic+"add");
	thepic.style.display="none";
	deleteDiv.style.display="none";
	addDiv.style.display="block";
	updatePictureStatus(pic,"delete");
}
