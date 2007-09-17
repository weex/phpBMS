connect(window,"onload", function(){

	var username=getObjectFromID("username");
	var password=getObjectFromID("password");
	var loginButton=getObjectFromID("loginButton");	
	
	username.disabled=false;
	password.disabled=false;
	loginButton.disabled=false;
	

	var sqlbttn=getObjectFromID("moreinfoButton");
	
	if(sqlbttn){
		var sqlDivs = new Array();
		sqlDivs[sqlDivs.length]=getObjectFromID("moreinfo");
	
		var sqlLinks = new Array();
		sqlLinks[sqlLinks.length]=sqlbttn;
	
		var sqlAccordion = new fx.Accordion(sqlLinks, sqlDivs, {opacity: true, duration:250});
	}

	username.focus();		
})
