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


	function runCommand(command){
		var theURL="installxml.php?command="+command;
		if(command=="updatesettings"){
			var mServer=getObjectFromID("mysqlserver");
			var mDatabase=getObjectFromID("mysqldb");
			var mUser=getObjectFromID("mysqluser");
			var mPassword=getObjectFromID("mysqluserpass");
			theURL+="&ms="+encodeURIComponent(mServer.value);
			theURL+="&mdb="+encodeURIComponent(mDatabase.value);
			theURL+="&mu="+encodeURIComponent(mUser.value);
			theURL+="&mup="+encodeURIComponent(mPassword.value);
		}
		var responseText= getObjectFromID(command+"results");
		loadXMLDoc(theURL,null,false);
		if(req.responseXML)
			response = req.responseXML.documentElement.firstChild.data+"\n";
		else 
			response = req.responseText+"\n";
		responseText.value += response;
	}
	

	function changeModule(){
		var moduleSel=getObjectFromID("modules");
		var installButton=getObjectFromID("installmodule");
		var modinfo = getObjectFromID("moduleInformation");
		
		var modName = getObjectFromID("modulename");
		var modVer = getObjectFromID("moduleversion");
		var modDesc = getObjectFromID("moduledescription");
		var modReq = getObjectFromID("modulerequirements");
		
		if(moduleSel.value!=0){
			installButton.disabled=false;
			
			modName.innerHTML = modules[moduleSel.value]["name"];
			modVer.innerHTML = modules[moduleSel.value]["version"];
			modDesc.innerHTML = modules[moduleSel.value]["description"];
			modReq.innerHTML = modules[moduleSel.value]["requirements"];
			
			modinfo.style.display = "block";						
		}
		else{
			installButton.disabled=true;
			modinfo.style.display = "none";			
		}
	}
	
	function runModuleInstall(){
		var themodule=getObjectFromID("modules");
		var responseText= getObjectFromID("moduleresults");
		if(themodule.value=="")
			alert("You must select a module to install first.");
		else {
			var theURL="../modules/"+themodule.value+"/install/install.php";
			loadXMLDoc(theURL,null,false);
			if(req.responseXML)
				response = req.responseXML.documentElement.firstChild.data+"\n";
			else 
				response = req.responseText;
			responseText.value+=response;
		}
	}