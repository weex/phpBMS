function switchFile(){
	var radio1=getObjectFromID("newfile");
	
	var newlabel=getObjectFromID("fileidlabel");
	var existinglabel=getObjectFromID("uploadlabel");
	var accesslevel=getObjectFromID("accesslevellabel");
	var descriptionlabel=getObjectFromID("descriptionlabel");

	if(radio1.checked){
		newlabel.style.display="none";
		existinglabel.style.display="block";
		accesslevel.style.display="block";
		descriptionlabel.style.display="block";
	} else {
		newlabel.style.display="block";
		existinglabel.style.display="none";
		accesslevel.style.display="none";
		descriptionlabel.style.display="none";
	}
}