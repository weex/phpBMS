function changeClientType(theselect){
	var becameclient=getObjectFromID("becameclient");
	var becameclientDiv=getObjectFromID("becameclientDiv");
	var comments=getObjectFromID("comments");
	if(theselect.value=="prospect"){
		becameclientDiv.style.display="none";
		becameclient.value="";
		comments.rows+=3;
	} else {
		becameclientDiv.style.display="block";
		var today=new Date();
		becameclient.value=(today.getMonth()+1)+"/"+today.getDate()+"/"+today.getFullYear();
		comments.rows-=3;
	}
}