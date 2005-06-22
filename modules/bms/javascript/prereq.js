//this function opens a page in a new windoe that will lookup and populate the add line item info based on a choosen partnumber
function populateLineItem(){
	if (this.value!=""){
		var theurl="prereqitemlookup.php?id="+this.value;

		loadXMLDoc(theurl,null,false);
		response = req.responseXML.documentElement;
		
		for(i=0;i<response.getElementsByTagName('field').length;i++){
			theitem=this.form[response.getElementsByTagName('field')[i].firstChild.data];
			if(!theitem) alert(response.getElementsByTagName('field')[i].firstChild.data);
			thevalue="";
			if(response.getElementsByTagName('value')[i].firstChild)
				thevalue=response.getElementsByTagName('value')[i].firstChild.data;
			theitem.value=thevalue;
			if(theitem.onchange && theitem.name=="price") theitem.onchange();
		}				
	}
	return true;
}

//This function set the line item to be deleted
function deleteLine(theid,theitem){
	var theform=theitem.form;
	theform["deleteid"].value=theid;
	return true;
}