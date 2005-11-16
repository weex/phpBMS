/*
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

function showHelp(base){
	window.open(base+"help");
}

function checkExpand(theitem){
	var i,tempDiv;
	var showid=theitem.id.substring(4);
	var doswitch=false
	for(i=0;i<subMenuArray.length;i++){
		tempdiv=getObjectFromID("submenu"+subMenuArray[i]);
		if(tempdiv.style.display!="none" && subMenuArray[i]!=showid)
			doswitch=true;
	}
	if(doswitch)
		expandMenu(theitem);
	
}

function expandMenu(theitem){
	var i;
	var tempdiv;
	var tempImage;
	var showid=theitem.id.substring(4);
	var specificdiv=getObjectFromID("submenu"+showid);
	var menuImage=getObjectFromID("menuImage"+showid)
	if(specificdiv.style.display)
		if(specificdiv.style.display=="block"){
			specificdiv.style.display="none";
			menuImage.src=downArrow.src;
			return false;
		}

	for(i=0;i<subMenuArray.length;i++){
		if(subMenuArray[i]!=showid){
			tempdiv=getObjectFromID("submenu"+subMenuArray[i]);
			tempdiv.style.display="none";
			tempImage=getObjectFromID("menuImage"+subMenuArray[i])
			tempImage.src=downArrow.src;
		}
	}
	var thetop=getTop(theitem);
	var theleft=getLeft(theitem);
	specificdiv.style.top=(thetop+theitem.offsetHeight)+"px";
	specificdiv.style.left=theleft+"px";
	specificdiv.style.display="block";
	menuImage.src=upArrow.src;
}

function showUserInfo(base){
	var content="<div align=\"center\"><img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Loading...</strong></div>";
	showModal(content,"Change Password",250);
	var modalContent=getObjectFromID("modalContent");
	
	var theURL=base+"changepassword.php?cm=shw&base="+encodeURIComponent(base);
	loadXMLDoc(theURL,null,false);
	modalContent.innerHTML=req.responseText;
}

function changePassword(thebase){
	var currpass=getObjectFromID("userCurPass");
	var newpass=getObjectFromID("userNewPass");
	var new2pass=getObjectFromID("userNew2Pass");
	var cpStatus=getObjectFromID("cpStatus")
	
	cpStatus.className="";
	cpStatus.innerHTML="<div align=\"center\"><img src=\""+thebase+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Processing...</strong></div>";
	if(userCheckPassword(currpass.value,thebase)){
		if(newpass.value==new2pass.value && newpass.value!=""){
			if(userUpdatePassword(newpass.value,thebase)){
				cpStatus.className="standout";
				cpStatus.innerHTML="Password Updated";
			} else {
				cpStatus.className="standout";
				cpStatus.innerHTML="Error";
			}
		}else{
			cpStatus.className="standout";
			cpStatus.innerHTML="Re-typed password does not match <br/> or blank password entered.";
		alert ("") ;
		}
	} else	{
		cpStatus.className="standout";
		cpStatus.innerHTML="Current password incorrect.";
	}
}

function userCheckPassword(thepass,base){
	var theURL=base+"changepassword.php?cm=chk";
	theURL+="&pass="+thepass
	loadXMLDoc(theURL,null,false);
	if(req.responseText!="ok") {return false};
	return true;
}

function userUpdatePassword(thepass,base){
	var theURL=base+"changepassword.php?cm=upd&";
	theURL+="&pass="+thepass
	loadXMLDoc(theURL,null,false);
	if(req.responseText!="ok") return false;
	return true;
}
