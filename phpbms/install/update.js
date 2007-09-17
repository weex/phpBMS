window.onload = function(){
	var sections = getElementsByClassName("bodyline");
	
	var i;
	for(i=0; i< sections.length; i++){
		if(sections[i].id != "step1"){
			sections[i].style.display = "none";
		}
	}
}

function goSection(direction){
	var sections = getElementsByClassName("bodyline");
	var currSection = 1;
	
	for(i=0; i< sections.length; i++){
		if(sections[i].style.display == "block")			
			currSection = parseInt(sections[i].id.substr(4),10);
	}
	
	if(direction == "next")
		direction = 1;
	else
		direction = -1;
	
	if(currSection + direction == 0)
		return false;
	else{
		var currDiv = getObjectFromID("step"+currSection);
		var newDiv = getObjectFromID("step"+(currSection + direction))

		currDiv.style.display = "none";
		newDiv.style.display = "block";
	}
}//end function


function changeModule(){
	var moduleSel=getObjectFromID("modules");
	var updateButton=getObjectFromID("updatemodule");
	var modinfo = getObjectFromID("moduleInformation");
	
	var modName = getObjectFromID("modulename");
	var modVer = getObjectFromID("moduleversion");
	var modDesc = getObjectFromID("moduledescription");
	var modReq = getObjectFromID("modulerequirements");
	
	if(moduleSel.value!=0){
		updateButton.disabled=false;
		
		modName.innerHTML = modules[moduleSel.value]["name"];
		modVer.innerHTML = modules[moduleSel.value]["version"];
		modDesc.innerHTML = modules[moduleSel.value]["description"];
		modReq.innerHTML = modules[moduleSel.value]["requirements"];
		
		modinfo.style.display = "block";						
	}
	else{
		updateButton.disabled=true;
		modinfo.style.display = "none";			
	}
}


function runCommand(command){
	var theURL="updatexml.php?command="+command;
	var adminName=getObjectFromID("username");
	var adminPass=getObjectFromID("password");
	var version=getObjectFromID("version");
	var theModules=getObjectFromID("modules");
	
	theURL+="&u="+encodeURIComponent(adminName.value);
	theURL+="&p="+encodeURIComponent(adminPass.value);
	theURL+="&v="+encodeURIComponent(version.value);
	
	if(command == "checkModuleUpdate"){
		theURL+="&m="+encodeURIComponent(theModules.value);
		theURL+="&mv="+encodeURIComponent(modules[theModules.value]["version"]);
	}

	var responseText= getObjectFromID(command+"results");
	loadXMLDoc(theURL,null,false);
	if(req.responseXML)
		response = req.responseXML.documentElement.firstChild.data+"\n";
	else 
		response = req.responseText+"\n";
		
	responseText.value+=response;
}


function runModuleUpdate(){
	var themodule=getObjectFromID("modules");
	var responseText= getObjectFromID("checkModuleUpdateresults");
	if(themodule.value=="")
		alert("First, Select a module");
	else {
		var theURL="../modules/"+themodule.value+"/install/update.php";
		var adminName=getObjectFromID("username");
		var adminPass=getObjectFromID("password");
		var theModules=getObjectFromID("modules");

		theURL+="?u="+encodeURIComponent(adminName.value);
		theURL+="&p="+encodeURIComponent(adminPass.value);
		theURL+="&v="+encodeURIComponent(modules[theModules.value]["version"]);
		
		loadXMLDoc(theURL,null,false);
		if(req.responseXML)
			response = req.responseXML.documentElement.firstChild.data+"\n";
		else 
			response = req.responseText;
			
		responseText.value+=response
	}
}