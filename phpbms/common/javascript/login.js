// Break out of frames if they exist
if(top!=self)top.location=self.location;

function init(){
	var username=getObjectFromID("username");
	var password=getObjectFromID("password");
	var loginButton=getObjectFromID("loginButton");	
	
	username.disabled=false;
	password.disabled=false;
	loginButton.disabled=false;
	
	username.focus();		
}