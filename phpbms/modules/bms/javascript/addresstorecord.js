addresstorecord = {
	
	switchCreate: function(e){
		
		var newAddressDiv = getObjectFromID("newAddressDiv");
		var selectExistingP = getObjectFromID("selectExistingP");
		var radio1 = getObjectFromID('newAddressRadio');
		
		if(radio1.checked){
			
			newAddressDiv.style.display = "block";
			selectExistingP.style.display = "none";
			
		} else {

			newAddressDiv.style.display = "none";
			selectExistingP.style.display = "block";

		}//endif - radio1checked
	
	},//end method - swtichCreate


	mapIt: function(){

		var q = "";

		var tempInput = getObjectFromID("address1");
		q += encodeURI(tempInput.value)

		var tempInput = getObjectFromID("address2");
		if(tempInput.value)
			q += encodeURI(" " + tempInput.value);

		var tempInput = getObjectFromID("city");
		if(tempInput.value)
			q += encodeURI(", " + tempInput.value);

		var tempInput = getObjectFromID("state");
		if(tempInput.value)
			q += encodeURI(", " + tempInput.value);

		var tempInput = getObjectFromID("postalcode");
		if(tempInput.value)
			q += encodeURI(" " + tempInput.value);

		var tempInput = getObjectFromID("country");
		if(tempInput.value)
			q += encodeURI(" " + tempInput.value);

		if(q) {
			
			var theurl = "http://maps.google.com/maps?f=q&hl=en&geocode=&ie=UTF8&z=16&iwloc=addr&q=" + q;		
			window.open(theurl);
		
		} else
			alert("No valid address given");
			
	}//end method - mapIt

}//end struct

/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {


	var radio1 = getObjectFromID('newAddressRadio');
	if(radio1){
				
		var radio2 = getObjectFromID('linkExistingRadio');

		connect(radio1, "onclick", addresstorecord.switchCreate);
		connect(radio2, "onclick", addresstorecord.switchCreate);
		
	}//endif radio1

	var mapButton = getObjectFromID("buttonMap");
	connect(mapButton, "onclick", addresstorecord.mapIt);
})