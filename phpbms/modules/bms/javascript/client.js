/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
 | All rights reserved.                                                    |
 +-------------------------------------------------------------------------+
 |                                                                         |
 | Redistribution and use in source and binary forms, with or without      |
 | modification, are permitted provided that the following conditions are  |
 | met:                                                                    |
 |                                                                         |
 | - Redistributions of source code must retain the above copyright        |
 |   notice, this list of conditions and the following disclaimer.         |
 |                                                                         |
 | - Redistributions in binary form must reproduce the above copyright     |
 |   notice, this list of conditions and the following disclaimer in the   |
 |   documentation and/or other materials provided with the distribution.  |
 |                                                                         |
 | - Neither the name of Kreotek LLC nor the names of its contributore may |
 |   be used to endorse or promote products derived from this software     |
 |   without specific prior written permission.                            |
 |                                                                         |
 | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS     |
 | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT       |
 | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A |
 | PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
 | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,   |
 | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT        |
 | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,   |
 | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY   |
 | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT     |
 | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE   |
 | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.    |
 |                                                                         |
 +-------------------------------------------------------------------------+
*/

function checkPunc(num) {

    if ((num >=33) && (num <=47)) { return true; }
    if ((num >=58) && (num <=64)) { return true; }
    if ((num >=91) && (num <=96)) { return true; }
    if ((num >=123) && (num <=126)) { return true; }

    return false;
}

function getRandomNum() {

    rndNum = Math.random()
    rndNum = parseInt(rndNum * 1000);
    rndNum = (rndNum % 94) + 33;

    return rndNum;
}

function generateUserAndPass(){
	var username=getObjectFromID("username");
	var password=getObjectFromID("password");
	var firstname=getObjectFromID("firstname");
	var lastname=getObjectFromID("lastname");
	var company=getObjectFromID("company");
	var theusername;
	var thepassword="";
	var numI;
	
	if(company.value)
		theusername=company.value.toLowerCase().replace(/ /g,"").substr(0,32);
	else{
		theusername=firstname.value.toLowerCase().replace(/ /,"").substr(0,1)+lastname.value.toLowerCase().replace(/ /,"");
	}
	username.value=theusername;
	
	for(i=0;i<=8;i++){
		numI = getRandomNum();
		while (checkPunc(numI)) 
			numI = getRandomNum();
		thepassword +=String.fromCharCode(numI).toLowerCase();
	}
	password.value=thepassword
	
}

function changeClientType(theselect){
	var becameclient=getObjectFromID("becameclient");
	var becameclientDiv=getObjectFromID("becameclientDiv");
	var thetitle=getObjectFromID("h1Title");
	
	if(theselect.value=="prospect"){
		becameclientDiv.style.display="none";
		becameclient.value="";
	} else {
		becameclientDiv.style.display="block";
		var today=new Date();
		becameclient.value=dateToString(today);
	}
	var newTitle=theselect.value.substr(0,1).toUpperCase()+theselect.value.substr(1);
	thetitle.innerHTML="<span>"+newTitle+"</span>"
}