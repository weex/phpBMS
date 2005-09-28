function changePass(base){
	dialogWindow=window.open(base+"changepassword.php","changepassword","resize=yes,status=no,scrollbars=no,width=375,height=220,modal=yes");
	return true;
}
var supported = (document.getElementById || document.all);

if (supported)
{
	document.write("<STYLE TYPE=\"text/css\">");
	document.write(".submenuitems {display: none}");
	document.write("</STYLE>");

	var max = 7; // # of menus
	var shown = new Array();
	for (var i=1;i<=max;i++)
	{
		shown[i+1] = false;
	}
}

function expand(i)
{
	shown[i] = (shown[i]) ? false : true;
	current = (shown[i]) ? 'block' : 'none';


	if (document.getElementById) {
		document.getElementById('sub'+i).style.display = current;
		var oldImage = document.getElementById('right' + i);
		base=oldImage.src.substring(0,oldImage.src.indexOf("common/image"));
		if (shown[i]) {
			oldImage.src=base+'common/image/down_arrow.gif';
		} else {
			oldImage.src=base+'common/image/left_arrow.gif';
		}
	} else if (document.all) {
		document.all['sub'+i].style.display = current;
		var oldImage ="document.right" + i;
		base=oldImage.src.substring(0,oldImage.src.indexOf("common/image"));
		if (shown[i]) {
			oldImage.src=base+'common/image/down_arrow.gif';
		} else {
			oldImage.src=base+'common/image/left_arrow.gif';
		}
	}
	return false;
}

function showUserInfo(base){
	var content="<div align=\"center\"><img src=\""+base+"common/image/spinner.gif\" alt=\"0\" width=\"16\" height=\"16\" align=\"absmiddle\"> <strong>Loading...</strong></div>";
	showModal(content,"Change Password",250);
	var modalContent=getObjectFromID("modalContent");
	
	var theURL=base+"changepassword.php?cm=shw&base="+escape(base);
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