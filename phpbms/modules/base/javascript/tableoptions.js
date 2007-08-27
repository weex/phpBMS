window.onload=function(){
	switchType();
}

function switchType(){
	var oc1=getObjectFromID("oc1");
	var pdFields=getObjectFromID("pdList");
	var other=getObjectFromID("other");
	if(oc1.checked){
		pdFields.style.display="block";
		other.style.display="none";
	} else {
		pdFields.style.display="none";
		other.style.display="block";
	}
}