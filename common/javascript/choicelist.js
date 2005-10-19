MLsets= new Array();

function setInitialML(thelist){
	MLsets[thelist.id]=thelist.selectedIndex;
}

function changeChoiceList(thelist,base,listname,blankvalue){
	if(thelist.value=="*mL*"){
		thelist.selectedIndex=MLsets[thelist.id];
		modifyList(thelist,base,listname,blankvalue);
	} else {
		MLsets[thelist.id]=thelist.selectedIndex;
		if(thelist.onchange2)
			thelist.onchange2();
	} 
}


function  modifyList(thelist,base,listname,blankvalue){
	var content="<div align=\"center\"><img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Loading...</strong></div>";
	showModal(content,"Modify List",300);
	var modalContent=getObjectFromID("modalContent");
		
	var theURL=base+"choicelist.php?cm=shw";
	theURL+="&ln="+encodeURI(listname);
	theURL+="&bv="+encodeURI(blankvalue);
	theURL+="&lid="+thelist.id;
	loadXMLDoc(theURL,null,false);
	
	modalContent.innerHTML=req.responseText;
}

function closeBox(listid){
	var thelist=getObjectFromID(listid);
	thelist.disabled=false;
	closeModal();
}

function clickOK(base,listid,listname){
	saveList(base,listname);
	var thelist=getObjectFromID(listid);
	loadList(base,thelist,listname);
	closeModal();
}

function saveList(base,listname){
	var thelist=getObjectFromID("MLlist");
	var thestatus=getObjectFromID("MLStatus");
	var i;

	thestatus.innerHTML="<img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Processing...</strong>";

	//delete current list
	var theURL=base+"choicelist.php?cm=del";
	theURL+="&ln="+encodeURI(listname);
	loadXMLDoc(theURL,null,false);
	if (req.responseText!="ok")
		alert(theURL+"\n\n"+req.responseText);
	
	//add each new item in.
	for(i=0;i<thelist.options.length;i++){
		theURL=base+"choicelist.php?cm=add";
		theURL+="&ln="+encodeURI(listname);
		theURL+="&val="+encodeURI(thelist.options[i].value);			
		loadXMLDoc(theURL,null,false);
		if (req.responseText!="ok")
			alert(theURL+"\n\n"+req.responseText);
	}
}

function loadList(base,thelist,listname){
	//remove current list
	var selValue=thelist.options[thelist.selectedIndex].value;
	var selText=thelist.options[thelist.selectedIndex].text;
	
	for(i=thelist.options.length-1;i>0;i--){
		thelist.options[0]=null;
	}
	var newlist=getObjectFromID("MLlist");
	
	//copy the new list over to the drop down
	var inList=false;
	for(i=0;i<newlist.options.length;i++){
		thelist.options[thelist.options.length]=new Option(newlist.options[i].text,newlist.options[i].value);
		if (thelist.options[thelist.options.length-1].value=="")
			thelist.options[thelist.options.length-1].className="choiceListBlank";
		if(thelist.options[thelist.options.length-1].value==selValue) {
			thelist.options[thelist.options.length-1].selected=true;
			inList=true;
		}
	}
	//if the previously selected value was not in the list, add it back in
	if(!inList){
		thelist.options[thelist.options.length]=new Option(selText,selValue);
		if (thelist.options[thelist.options.length-1].value=="")
			thelist.options[thelist.options.length-1].className="choiceListBlank";
		thelist.options[thelist.options.length-1].selected=true;
	}
	
	thelist.options[thelist.options.length]=new Option (thelist.options[0].text,thelist.options[0].value);
	thelist.options[thelist.options.length-1].className=thelist.options[0].className;
	thelist.options[0]=null;
	
}

function updateML(thelist){
	var delbutton=getObjectFromID("MLDelete");
	var addeditbutton=getObjectFromID("MLaddeditbutton");	
	var addedit=getObjectFromID("MLaddedit");	
	addeditbutton.value="edit";
	delbutton.disabled=false;
	addedit.value=thelist.value;
}

function insertML(){
	var thelist=getObjectFromID("MLlist");
	var delbutton=getObjectFromID("MLDelete");
	var addeditbutton=getObjectFromID("MLaddeditbutton");	
	var addedit=getObjectFromID("MLaddedit");	
	addeditbutton.value="add";
	delbutton.disabled=true;
	addedit.value="";
	thelist.selectedIndex=-1;
	addedit.focus();
}

function addeditML(blankvalue){
	var thelist=getObjectFromID("MLlist");
	var delbutton=getObjectFromID("MLDelete");
	var addeditbutton=getObjectFromID("MLaddeditbutton");	
	var addedit=getObjectFromID("MLaddedit");
	
	var newValue=addedit.value;
	var newText=newValue;
	
	//check for blank values;
	if(newValue==blankvalue || newValue=="<"+blankvalue+">" || newValue==""){
		newValue="";
		newText="<"+blankvalue+">";
	}

	if (addeditbutton.value=="add"){
		thelist.options[thelist.options.length]= new Option(newText,newValue);
		if(thelist.options[thelist.options.length-1].value=="")
			thelist.options[thelist.options.length-1].className="choiceListBlank";
	} else{
		thelist.options[thelist.selectedIndex].value=newValue;
		thelist.options[thelist.selectedIndex].text=newText;
		if(thelist.options[thelist.selectedIndex].value=="")
			thelist.options[thelist.selectedIndex].className="choiceListBlank";
	}
}

function delML(){
	var thelist=getObjectFromID("MLlist");
	var delbutton=getObjectFromID("MLDelete");
	var addeditbutton=getObjectFromID("MLaddeditbutton");	
	var addedit=getObjectFromID("MLaddedit");	
	thelist.options[thelist.selectedIndex]=null;
	addeditbutton.value="add";
	delbutton.disabled=true;
	addedit.value="";
	thelist.selectedIndex=-1;
}
