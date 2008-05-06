// ADDRESSES CLASS ================================================
//=================================================================
addresses = {
	
	mapIt: function(e){
		
		var theButton = e.src();
		
		var prefix = ""
		if(theButton.id.substr(9) == "Shipping")
			prefix = "shipto";

		var q = "";

		var tempInput = getObjectFromID(prefix + "address1");
		q += encodeURI(tempInput.value)

		var tempInput = getObjectFromID(prefix + "address2");
		if(tempInput.value)
			q += encodeURI(" " + tempInput.value);

		var tempInput = getObjectFromID(prefix + "city");
		if(tempInput.value)
			q += encodeURI(", " + tempInput.value);

		var tempInput = getObjectFromID(prefix + "state");
		if(tempInput.value)
			q += encodeURI(", " + tempInput.value);

		var tempInput = getObjectFromID(prefix + "postalcode");
		if(tempInput.value)
			q += encodeURI(" " + tempInput.value);

		var tempInput = getObjectFromID(prefix + "country");
		if(tempInput.value)
			q += encodeURI(" " + tempInput.value);

		if(q) {
			
			var theurl = "http://maps.google.com/maps?f=q&hl=en&geocode=&ie=UTF8&z=16&iwloc=addr&q=" + q;		
			window.open(theurl);
		
		} else
			alert("No valid address given");
			
	},//end method - mapIt
	
	switchTab: function(e){
		
		var aTag = e.src();
		
		if(aTag.parentNode.className != "tabsSel"){
			
			var billingDiv = getObjectFromID("billingAddressDiv");
			var shippingDiv = getObjectFromID("shiptoAddressDiv");
			var billingTab = getObjectFromID("tabBilling");
			var shippingTab = getObjectFromID("tabShipTo");
			
			switch(aTag.id.substr(3)){
				
				case "Billing":
					billingDiv.style.display = "block";
					shippingDiv.style.display = "none";
					billingTab.parentNode.className = "tabsSel";
					shippingTab.parentNode.className = "";
					break;
					
				case "ShipTo":
					billingDiv.style.display = "none";
					shippingDiv.style.display = "block";
					billingTab.parentNode.className = "";
					shippingTab.parentNode.className = "tabsSel";
					break;
				
			}//endswitch
			
		}//endif
		
		//dont propagate event
		e.stop();
		
	},//end method - switchTab
	
	toggleOptions: function(e){

		var theButton = e.src();		
		var sectionName = theButton.id.substr(13);
		var theDiv = getObjectFromID("addressOptionsDiv" + sectionName);

		if(theButton.innerHTML.substr(6,4) == "more"){
			
			theButton.innerHTML = theButton.innerHTML.replace("more","less");
			theButton.className = "graphicButtons buttonUp showoptions";
			theDiv.style.display = "block";
			
		} else {

			theButton.innerHTML = theButton.innerHTML.replace("less","more");
			theButton.className = "graphicButtons buttonDown showoptions";
			theDiv.style.display = "none";
			
		}//endif - theButton value
		
	},//end method - toggleOptions


	clear: function(e){

		var theButton = e.src();
		var fields, i, tempitem;

		switch(theButton.id.substr(18)){
			 
			case "Billing":
			 	fields = Array('billingaddressid', 'address1', 'address2', 'city', 'state', 'postalcode', 'country');
			 	break;
				
			case "Shipping":
			 	fields = Array( 'shiptoaddressid', 'shiptoname', 'shiptoaddress1', 'shiptoaddress2', 'shiptocity', 'shiptostate', 'shiptopostalcode', 'shiptocountry');
				break;
			 
		}//endswitch - button id

		for(i=0; i< fields.length; i++){
			
			tempitem = getObjectFromID(fields[i])
			tempitem.value = "";
			
		}//endfor
		
	},//end method - clear


	loadConnects: Array(),
	loadSection: "Billing",

	showLoad: function(e){
		
		theButton = e.src();
		
		if(theButton.className.indexOf("disabled") === -1){

			addresses.loadSection = theButton.id.substr(17);
			
			var clientid = getObjectFromID("clientid");
			
			var theurl = "invoices_addresses_ajax.php?w=l&id="+clientid.value;
			
			loadXMLDoc(theurl,null,false);		
			
			var content = req.responseText;
			
			showModal(content, "Load Client Address", 400);
			
			var cancelButton = getObjectFromID("LACancelButton")
			addresses.loadConnects[0] = connect(cancelButton, "onclick", addresses.closeLoad);
			
			var loadButton = getObjectFromID("LALoadButton")
			addresses.loadConnects[1] = connect(loadButton, "onclick", addresses.loadAddress);
	
			var atags = getElementsByClassName("LAPickAs");
			for(var i=0; i<atags.length;i++)
				addresses.loadConnects[addresses.loadConnects.length] = connect(atags[i], "onclick", addresses.pickAddress);
				
		}//endif - disabled
		
	},//end method - showLoad


	closeLoad: function(e){
		
		for(var i=0; i < addresses.loadConnects; i++)
			disconnect(addresses.loadConnects[0]);
		
		closeModal();
		
	},//end method - cancelLoad


	loadAddress: function(e){

		var loadButton = e.src();

		if(loadButton.className.indexOf("disabled") === -1){
			
			var atags = getElementsByClassName("LAPickAs");

			var addressid;
			
			for(var i=0; i<atags.length;i++)
				if(atags[i].className == "LAPickAs LASel")
					addressid = atags[i].id.substr(3);

			var theurl = "invoices_addresses_ajax.php?w=s&id=" + addressid
			
			loadXMLDoc(theurl,null,false);		
			
			try{
				theAddress = eval( "(" + req.responseText+ ")");
			} catch(err){
				reportError("Error Retrieving Address: " + theurl + " response:" + err);
			}
			
			var changeArray = Array();
			switch(addresses.loadSection){
				
				case "Billing":
					changeArray["billingaddressid"] = theAddress.id;
					changeArray["address1"] = theAddress.address1;
					changeArray["address2"] = theAddress.address2;
					changeArray["city"] = theAddress.city;
					changeArray["state"] = theAddress.state;
					changeArray["postalcode"] = theAddress.postalcode;
					changeArray["country"] = theAddress.country;								
					break;
					
				case "Shipping":
					changeArray["shiptoaddressid"] = theAddress.id;
					changeArray["shiptoname"] = theAddress.shiptoname;
					changeArray["shiptoaddress1"] = theAddress.address1;
					changeArray["shiptoaddress2"] = theAddress.address2;
					changeArray["shiptocity"] = theAddress.city;
					changeArray["shiptostate"] = theAddress.state;
					changeArray["shiptopostalcode"] = theAddress.postalcode;
					changeArray["shiptocountry"] = theAddress.country;								
					break;
				
			}//endswitch - loadSection
			
			var tempitem;
			for(prop in changeArray){

				tempitem = getObjectFromID(prop);
				if(tempitem)
					tempitem.value = changeArray[prop];
				
			}//endfor in changeArray
				
			
			addresses.closeLoad();
			
		} //end if - disabled
		
	},//endMethod - loadAddress


	pickAddress: function(e){
		
		var atag = e.src();
		
		var atags = getElementsByClassName("LAPickAs");
		for(var i=0; i<atags.length;i++)
			atags[i].className = "LAPickAs";

		atag.className = "LAPickAs LASel";
		
		var loadButton = getObjectFromID("LALoadButton")
			loadButton.className = "Buttons addressButtons";

	}//end method - pickAddress
	
}//end class - addresses

/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

	//address connects
	var billingTab = getObjectFromID("tabBilling");
	connect(billingTab, "onclick", addresses.switchTab);

	var shippingTab = getObjectFromID("tabShipTo");
	connect(shippingTab, "onclick", addresses.switchTab);

	var billingMore = getObjectFromID("buttonAddressBilling");
	connect(billingMore, "onclick", addresses.toggleOptions);

	var shippingMore = getObjectFromID("buttonAddressShipTo");
	connect(shippingMore, "onclick", addresses.toggleOptions);
	
	var billingLoad = getObjectFromID("addressLoadButtonBilling");
	connect(billingLoad, "onclick", addresses.showLoad);

	var shippingLoad = getObjectFromID("addressLoadButtonShipping");
	connect(shippingLoad, "onclick", addresses.showLoad);

	var billingClear = getObjectFromID("addressClearButtonBilling");
	connect(billingClear, "onclick", addresses.clear);

	var shippingClear = getObjectFromID("addressClearButtonShipping");
	connect(shippingClear, "onclick", addresses.clear);
	
	var mapBilling = getObjectFromID("buttonMapBilling");
	connect(mapBilling, "onclick", addresses.mapIt);

	var mapShipping = getObjectFromID("buttonMapShipping");
	connect(mapShipping, "onclick", addresses.mapIt);

})