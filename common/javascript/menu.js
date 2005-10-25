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
