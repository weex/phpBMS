function moveItem(id,direction,theform){
	var additem,removeitem,tempText,tempValue;
	
	if(direction=="to"){
		additem="selected"+id;
		removeitem="available"+id;
	}else{
		removeitem="selected"+id;
		additem="available"+id;
	}
	
	for(i=0;i<theform[removeitem].options.length;i++)	{
		if (theform[removeitem].options[i].selected) {
			tempText=theform[removeitem].options[i].text;
			tempValue=theform[removeitem].options[i].value;
			theform[removeitem].options[i]=null;
			theform[additem].options[theform[additem].options.length]= new Option(tempText,tempValue);
			i=-1;
		}
	}			
}//end function



function submitForm(){

	var groupingsSelect = getObjectFromID("selectedgroupings");
	var columnsSelect = getObjectFromID("selectedcolumns");
	var i;

	if(groupingsSelect.options.length){
		var groupings = getObjectFromID("groupings");
		groupings.value = "";
		
		for(i=0; i<groupingsSelect.options.length; i++)
			groupings.value += "" + groupingsSelect.options[i].value + "::";

		groupings.value = groupings.value.substr(0,groupings.value.length-2);		
	}
	

	if(columnsSelect.options.length){
		var columns = getObjectFromID("columns");
		columns.value = "";

		for(i=0; i<columnsSelect.options.length; i++)
			columns.value += "" + columnsSelect.options[i].value + "::";

		columns.value = columns.value.substr(0,columns.value.length-2);		
	}


	if(columnsSelect.options.length){
	} else {
		alert("At least one column is needed to run the report");
		return false;
	}
	
	groupingsSelect.form.submit();
	
}//end function