/* Receipt Class ----------------------------------------- */
/* ------------------------------------------------------- */
receipt = {

	submitForm: function(e){

		var theForm = getObjectFromID("record");

		if(!validateForm(theForm)){
			if(e)
				e.stop();
			return false;
		}

		//skip validation if cancel
		cancelClick = getObjectFromID("cancelclick");
		if(cancelClick.value !=0)
			return true;

		var rtpCheckbox = getObjectFromID("readytopost");

		var errorArray = Array();
		if(rtpCheckbox.checked){

			var statusSelect = getObjectFromID("status")
			if(statusSelect.value != "collected")
				errorArray[errorArray.length] = "Receipts marked ready to post must also have a status of 'collected'.";

			var distributionRemaining = getObjectFromID("distributionRemaining");
			if(currencyToNumber(distributionRemaining.value) != 0)
				errorArray[errorArray.length] = "Receipts marked ready to post must be fully distributed";

		}//end if

		if(errorArray.length > 0){

			var content = "<p>The following errors were found:</p><ul>";

			for(var i=0; i < errorArray.length; i++)
				content += "<li>"+errorArray[i]+"</li>";

			content += "</ul>";

			alert(content);

			if(e)
				e.stop();
			return false
		}//end if

		var itemschanged = getObjectFromID("itemschanged");
		if(itemschanged.value == 1)
			aritems.prepareForPost();

		return true;

	},//end method


	switchPayment: function(e){

		var paymentmethodid = getObjectFromID("paymentmethodid");

		var theDivs = getElementsByClassName("paymentTypes");

		for(var i=0; i<theDivs.length; i++)
			if(theDivs[i].id == paymentTypes["s"+paymentmethodid.value]["type"])
				theDivs[i].style.display = "block";
			else
				theDivs[i].style.display = "none";

		var transactionp = getObjectFromID("transactionP");
		var transactionscript = getObjectFromID("processscript");

		if(paymentTypes["s"+paymentmethodid.value]["onlineprocess"] == 1 && paymentTypes["s"+paymentmethodid.value]["processscript"]){

			transactionscript.value = paymentTypes["s"+paymentmethodid.value]["processscript"];
			transactionp.style.display = "block";

		} else {

			transactionscript.value = "";
			transactionp.style.display = "none";

		}

	},//end method


	updateDistributionRemaining: function(e){

		var amount = getObjectFromID("amount");
		var distributionRemaining = getObjectFromID("distributionRemaining");
		var totalapplied = getObjectFromID("totalapplied");

		var remaining = currencyToNumber(amount.value) - currencyToNumber(totalapplied.value)
		if(remaining == 0)
			distributionRemaining.className = "invisibleTextField";
		else
			distributionRemaining.className = "invisibleTextField notes important";

		distributionRemaining.value = numberToCurrency(remaining);

	}, //end method


	calculateTotalDue: function(e){

		var amountsDue = getElementsByClassName("dueFields");

		var amountdue = getObjectFromID("totaldue");

		var newAmount = 0;

		for (var i=0; i<amountsDue.length; i++)
			newAmount += currencyToNumber(amountsDue[i].value);

		amountdue.value = numberToCurrency(newAmount);

	}, //end method


	checkRTP: function(e){

		var checkbox = getObjectFromID("readytopost");
		var statusSelect = getObjectFromID("status");

		if(checkbox.checked)
			statusSelect.selectedIndex = 1;

	} //end method

}//end class


/* AR Items Class ---------------------------------------- */
/* ------------------------------------------------------- */
aritems = {

	LoadOpenListners: Array(),

	prepareForPost: function(){

		var thelist = "[";

		var receiptTRs = getElementsByClassName("receiptTR");

		var theID, ARID, RecID, Type, DocDate, Applied, Discount, TaxAdj;

		for(var i = 0; i < receiptTRs.length; i++){

			theID = receiptTRs[i].id;
			ARID = getObjectFromID(theID+"ARID");
			RecID = getObjectFromID(theID+"RecID");
			Type = getObjectFromID(theID+"Type");
			DocDate = getObjectFromID(theID+"DocDate");
			Applied = getObjectFromID(theID+"Applied");
			Discount = getObjectFromID(theID+"Discount");
			TaxAdj = getObjectFromID(theID+"TaxAdj");
			/**
			  *  memo.value.replace(/([\\"])/g,'\\\"')
			  */
			thelist +=  '{' +
						'"aritemid" : "' + ARID.value + '",' +
						'"relatedid" : "' + RecID.value  + '",' +
						'"type" : "' + Type.value + '",' +
						'"itemdate" : "' + DocDate.value + '",' +
						'"applied" : ' + currencyToNumber(Applied.value) + ',' +
						'"discount" : ' + currencyToNumber(Discount.value) + ',' +
						'"taxadjustment" : ' + currencyToNumber(TaxAdj.value) +
						'}';

			if(i < (receiptTRs.length - 1))
				thelist += ",";

		}//end for

		thelist += "]";

		var itemslist = getObjectFromID("itemslist");
		itemslist.value = thelist;

	}, // end method


	calculateTotalApplied: function(e){
		var theTotal = 0;

		var totalFields = getElementsByClassName("appliedFields");

		for(var i=0; i < totalFields.length; i++)
			if(totalFields[i].value)
				theTotal += currencyToNumber(totalFields[i].value);

		var totalapplied = getObjectFromID("totalapplied");

		totalapplied.value = numberToCurrency( Math.round(theTotal * Math.pow(10,CURRENCY_ACCURACY) ) / Math.pow(10,CURRENCY_ACCURACY) );

		return theTotal;

	},//end method


	showLoadOpenDialog: function(e){

		var clientid = getObjectFromID("clientid");
		if(!clientid.value || clientid.value == ""){

			alert("You must choose a client before you can load all open items");
			return false;

		}//endif

		var appliedFields = getElementsByClassName("appliedFields");
		if(appliedFields.length == 0){

			aritems.loadOpen();
			return true;

		}//end if


		var content = "Loading all current open items for the client will remove any existing items";

		content = "<p>"+content+"</p";
		content += '<p align="right">' +
					'<button class="Buttons" id="loadContinueButton">continue</button> ' +
					'<button class="Buttons" id="loadCancelButton">cancel</button>' +
					'</p>';

		if(aritems.LoadOpenListners.length)
			for(var i=0; i<aritems.LoadOpenListners.length; i++)
				disconnect(aritems.LoadOpenListners[i]);

		showModal(content,"Load Open AR Items",400);

		var continueButton = getObjectFromID("loadContinueButton");
		var cancelButton = getObjectFromID("loadCancelButton");

		aritems.LoadOpenListners = [
			connect(continueButton,"onclick",aritems.loadOpen),
			connect(cancelButton,"onclick",closeModal)
		];

	},//end method


	loadOpen: function(e){

		if(e)
			closeModal();

		var clientid = getObjectFromID("clientid");
		var theURL = "receipts_aritem_ajax.php?cm=getAllOpen&cid="+encodeURIComponent(clientid.value);

		loadXMLDoc(theURL, null, false);

		var theReturn;
		
		try {
			eval("theReturn = "+req.responseText);
		} catch (e) {
			alert("No Open AR Items found for client.")
			return false;
		}

		//remove all items
		aritems._removeAll()

		var key;

		for(key in theReturn){

			//add new items
			aritems.addItem(theReturn[key]);

		}//endfor

		aritems.calculateTotalApplied();
		receipt.updateDistributionRemaining();
		receipt.calculateTotalDue();

	},//end method


	removeItem: function(e){
		if(!e)
			return false;

		var trObj = getObjectFromID(e.src().id.substr(0,2));

		aritems._removeTR(trObj);

		aritems.calculateTotalApplied();
		receipt.updateDistributionRemaining();
		receipt.calculateTotalDue();

		var changed = getObjectFromID("itemschanged");
		changed.value = 1;

	},// end method


	_removeTR: function(trObj){

		var tbody = trObj.parentNode;

		var ident;

		//remove listeners
		var applied = getObjectFromID(trObj.id + "Applied");
		ident = getIdent(applied, "onchange");
		if(ident)
			disconnect(ident);

		var discount = getObjectFromID(trObj.id + "Discount");
		ident = getIdent(discount, "onchange");
		if(ident)
			disconnect(ident);

		var taxAdj = getObjectFromID(trObj.id + "TaxAdj");
		ident = getIdent(taxAdj, "onchange");
		if(ident)
			disconnect(ident);

		var removeButton = getObjectFromID(trObj.id + "RemoveARItemButton");
		ident = getIdent(removeButton, "onclick");
		if(ident)
			disconnect(ident);

		//remove the tr
		tbody.removeChild(trObj);

	}, //endmethod


	_removeAll: function(){

		var tbody = getObjectFromID("itemsTbody");
		var theID, applied, discount, taxAdj, ident;

		for (i=0; i< tbody.childNodes.length; i++){
			if(tbody.childNodes[i].tagName)
				if(tbody.childNodes[i].tagName == "TR"){

					aritems._removeTR(tbody.childNodes[i])
					i--;

				}//end if
		}//endfor

		var changed = getObjectFromID("itemschanged");
		changed.value = 1;

	},//end method


	addItem: function(itemObj){

		var tbody = getObjectFromID("itemsTbody");

		//get nextid to use
		var nextID = 1;
		for (i=0; i< tbody.childNodes.length; i++){
			if(tbody.childNodes[i].tagName)
				if(tbody.childNodes[i].tagName == "TR")
					if(parseInt(tbody.childNodes[i].id.substr(1)) >= nextID)
						nextID = parseInt(tbody.childNodes[i].id.substr(1)) + 1;
		}

		var tempTD, tempINPUT, tempBUTTON;
		var theTR = document.createElement("tr");
		theTR.id = "i" + nextID;
		theTR.className = "receiptTR";

		tempTD = document.createElement("td");

		//AR item UUID
		tempINPUT = document.createElement("input");
		tempINPUT.setAttribute("type","hidden");
		tempINPUT.id = theTR.id+"ARID";
		tempINPUT.value = itemObj.uuid;
		tempTD.appendChild(tempINPUT);

		//Record UUID
		tempINPUT = document.createElement("input");
		tempINPUT.setAttribute("type", "hidden");
		tempINPUT.id = theTR.id+"RecID";
		tempINPUT.value = itemObj.relatedid;
		tempTD.appendChild(tempINPUT);

		//doc ref #
		tempINPUT = document.createElement("input");
		tempINPUT.id = theTR.id+"DocRef";
		tempINPUT.value = itemObj.invoiceid;
		tempINPUT.readOnly = true;
		tempINPUT.size = 4;
		tempINPUT.className = "invisibleTextField";
		tempTD.appendChild(tempINPUT);

		theTR.appendChild(tempTD);

		tempTD = document.createElement("td");

		//aritem type
		tempINPUT = document.createElement("input");
		tempINPUT.id = theTR.id+"Type";
		tempINPUT.value = itemObj.type;
		tempINPUT.readOnly = true;
		tempINPUT.size = 12;
		tempINPUT.className = "invisibleTextField";
		tempTD.appendChild(tempINPUT);

		theTR.appendChild(tempTD);


		tempTD = document.createElement("td");

		//doc date
		tempINPUT = document.createElement("input");
		tempINPUT.id = theTR.id+"DocDate";
		tempINPUT.value = dateToString(stringToDate(itemObj.itemdate,"SQL"));
		tempINPUT.readOnly = true;
		tempINPUT.size = 9;
		tempINPUT.className = "invisibleTextField";
		tempTD.appendChild(tempINPUT);

		theTR.appendChild(tempTD);

		tempTD = document.createElement("td");

		//due date
		tempINPUT = document.createElement("input");
		tempINPUT.id = theTR.id+"DocDate";
		if(itemObj.type == "invoice"){

			var dueDate = new Date( stringToDate(itemObj.itemdate,"SQL").getTime() + (TERM1_DAYS * 24 * 60 * 60 * 1000));
			tempINPUT.value = dateToString(dueDate);

		}//end if
		tempINPUT.readOnly = true;
		tempINPUT.size = 9;
		tempINPUT.className = "invisibleTextField";
		tempTD.appendChild(tempINPUT);

		theTR.appendChild(tempTD);

		tempTD = document.createElement("td");
		tempTD.align = "right";

		//docpaid
		tempINPUT = document.createElement("input");
		tempINPUT.setAttribute("type","hidden");
		tempINPUT.id = theTR.id+"DocPaid";
		tempINPUT.value = itemObj.paid;
		tempTD.appendChild(tempINPUT);

		//doc amount
		tempINPUT = document.createElement("input");
		tempINPUT.id = theTR.id+"DocAmount";
		tempINPUT.value = numberToCurrency(itemObj.amount);
		tempINPUT.readOnly = true;
		tempINPUT.size = 10;
		tempINPUT.className = "invisibleTextField currency";
		tempTD.appendChild(tempINPUT);

		theTR.appendChild(tempTD);

		tempTD = document.createElement("td");

		//doc due
		tempINPUT = document.createElement("input");
		tempINPUT.id = theTR.id+"DocDue";
		tempINPUT.value = numberToCurrency(itemObj.amount - itemObj.paid);
		tempINPUT.readOnly = true;
		tempINPUT.size = 10;
		tempINPUT.className = "invisibleTextField currency dueFields";
		tempTD.appendChild(tempINPUT);

		theTR.appendChild(tempTD);

		tempTD = document.createElement("td");

		//applied
		tempINPUT = document.createElement("input");
		tempINPUT.id = theTR.id+"Applied";
		tempINPUT.value = numberToCurrency(0);
		tempINPUT.size = 10;
		tempINPUT.className = "currency appliedFields";
		tempTD.appendChild(tempINPUT);

		theTR.appendChild(tempTD);

		tempTD = document.createElement("td");

		//discount
		tempINPUT = document.createElement("input");
		tempINPUT.id = theTR.id+"Discount";
		tempINPUT.value = numberToCurrency(0);
		tempINPUT.size = 10;
		tempINPUT.className = "currency";
		tempTD.appendChild(tempINPUT);

		theTR.appendChild(tempTD);

		tempTD = document.createElement("td");

		//tax adj.
		tempINPUT = document.createElement("input");
		tempINPUT.id = theTR.id+"TaxAdj";
		tempINPUT.value = numberToCurrency(0);
		tempINPUT.size = 10;
		tempINPUT.className = "currency";
		tempTD.appendChild(tempINPUT);

		theTR.appendChild(tempTD);

		tempTD = document.createElement("td");

		tempBUTTON = document.createElement("button");
		tempBUTTON.setAttribute("type","button");
		tempBUTTON.id = theTR.id+"RemoveARItemButton";
		tempBUTTON.className = "graphicButtons buttonMinus";
		tempBUTTON.title = "Remove Item";

		var tempSPAN = document.createElement("span");
		tempSPAN.innerHTML = "-";
		tempBUTTON.appendChild(tempSPAN);
		tempTD.appendChild(tempBUTTON);

		theTR.appendChild(tempTD);

		tbody.appendChild(theTR);

		//add the listener for the appropriate fields

		var applied = getObjectFromID(theTR.id+"Applied");
		var discount = getObjectFromID(theTR.id+"Discount");
		var taxAdj = getObjectFromID(theTR.id+"TaxAdj");
		var removeButton = getObjectFromID(theTR.id+"RemoveARItemButton");

		connect(applied, "onchange", aritems.changeAppliedFields);
		connect(discount, "onchange", aritems.changeAppliedFields);
		connect(taxAdj, "onchange", aritems.changeAppliedFields);
		connect(removeButton, "onclick", aritems.removeItem);

		var changed = getObjectFromID("itemschanged");
		changed.value = 1;

		receipt.calculateTotalDue();

	},//end method


	changeAppliedFields: function(e, theID){

		if(e)
			theID = e.src().id.substr(0,2);

		var docAmount = getObjectFromID(theID+"DocAmount");
		var docDue = getObjectFromID(theID+"DocDue");
		var docPaid = getObjectFromID(theID+"DocPaid");
		var applied = getObjectFromID(theID+"Applied");
		var discount = getObjectFromID(theID+"Discount");
		var taxAdj = getObjectFromID(theID+"TaxAdj");

		applied.value = numberToCurrency(currencyToNumber(applied.value));
		discount.value = numberToCurrency(currencyToNumber(discount.value));
		taxAdj.value = numberToCurrency(currencyToNumber(taxAdj.value));

		if(currencyToNumber(docAmount.value) != 0)
			docDue.value = numberToCurrency( Math.round( (currencyToNumber(docAmount.value) - parseFloat(docPaid.value) - currencyToNumber(applied.value) - currencyToNumber(discount.value) - currencyToNumber(taxAdj.value)) * Math.pow(10,CURRENCY_ACCURACY) ) / Math.pow(10,CURRENCY_ACCURACY) );

		var changed = getObjectFromID("itemschanged");
		changed.value = 1;

		aritems.calculateTotalApplied();
		receipt.updateDistributionRemaining();
		receipt.calculateTotalDue();

	},//end method


	autoApply: function(e){

		var amount = getObjectFromID("amount");

		var theTotal = currencyToNumber(amount.value);

		var appliedFields = getElementsByClassName("appliedFields");
		var theID, docDue, amountToApply, refid, theType;

		//first find all non-zero credits and add it to the total;
		for(var i=0; i< appliedFields.length; i++){

			theID = appliedFields[i].id.substr(0,2);
			theType = getObjectFromID(theID+"Type");

			if(theType.value == "credit"){

				docDue = currencyToNumber(getObjectFromID(theID + "DocDue").value) + currencyToNumber(appliedFields[i].value);

				if(docDue != 0)
					theTotal += -1 * docDue;

			}//end if

		}//end for

		for(i=0; i< appliedFields.length; i++){

			theID = appliedFields[i].id.substr(0,2);

			refid = getObjectFromID(theID + "ARID");

			if(refid.value != "")
				docDue = currencyToNumber(getObjectFromID(theID + "DocDue").value) + currencyToNumber(appliedFields[i].value);
			else
				docDue = Math.pow(10, 10);

			if(docDue <= theTotal){

				appliedFields[i].value = docDue;
				theTotal = roundForCurrency(theTotal - docDue);

			} else{

				appliedFields[i].value = theTotal;
				theTotal = 0;

			}//end if

			aritems.changeAppliedFields(false, theID);

		}//end for

	}, //end method


	setInitialListners: function(){

		var appliedFields = getElementsByClassName("appliedFields");
		var theID, discount, taxadj, removeButton;

		for(var i=0; i<appliedFields.length; i++){

			theID = appliedFields[i].id.substr(0,2);

			discount = getObjectFromID(theID + "Discount");
			taxadj = getObjectFromID(theID + "TaxAdj");
			removeButton = getObjectFromID(theID + "RemoveARItemButton");

			connect(appliedFields[i], "onchange", aritems.changeAppliedFields);
			connect(discount, "onchange", aritems.changeAppliedFields);
			connect(taxadj, "onchange", aritems.changeAppliedFields);
			connect(removeButton, "onclick", aritems.removeItem);

		}//endfor

	} //end method

}//end class


/* new Item Dialog Class --------------------------------- */
/* ------------------------------------------------------- */
newItemDialog = {

	listeners: Array(),

	show: function(e) {

		var clientid = getObjectFromID("clientid");
		if(clientid.value == 0 || !clientid.value){

			alert("You must first choose a client before adding items.");
			return false;

		}//end if

		//remove existing listners
		if(newItemDialog.listeners.length){

			for(var i=0; i<newItemDialog.listeners.length; i++)
				disconnect(newItemDialog.listeners[i]);

			newItemDialog.listeners = Array();

		}//end if

		var theURL = "receipts_aritem_ajax.php?cm=getAddNewDialog&cid=" + encodeURIComponent(clientid.value);
		loadXMLDoc(theURL, null, false);

		var content = req.responseText;

		showModal(content, "Add AR Item", 440);

		var newItemCancelButton = getObjectFromID("newItemCancelButton");
		var newItemLoadButton = getObjectFromID("newItemLoadButton");
		var typeCheckBox = getObjectFromID("newItemType");

		newItemDialog.listeners = [
				connect(newItemCancelButton, "onclick", closeModal),
				connect(newItemLoadButton, "onclick", newItemDialog.add),
				connect(typeCheckBox, "onchange", newItemDialog.switchType)
			];

		var creditSelect = getObjectFromID("newItemCreditARID");
		
		if(!creditSelect){

			var creditP = getObjectFromID("newItemCreditExistingP");
			var creditExistingCheckbox = getObjectFromID("newItemCreditExisting");
			creditExistingCheckbox.disabled = true;
			creditP.className = "disabledtext";

		}//endif

	},//end method


	add: function(e){

		theButton = e.src();

		if(theButton.className == "disabledButtons")
			return false;

		var type = getObjectFromID("newItemType");
		var newItemCreditExisting = getObjectFromID("newItemCreditExisting");

		var theSelect = null;

		switch(type.value){
			case "credit":
				if(newItemCreditExisting.checked)
					theSelect = getObjectFromID("newItemCreditARID");
				break;

			case "invoice":
				theSelect = getObjectFromID("newItemInvoiceARID");
				break;

			case "service charge":
				theSelect = getObjectFromID("newItemServiceChargeARID");

		}//endswitch

		var aritem;
		if(theSelect){

			var ARid = theSelect.value;

			//check to see if item is already in list
			var appliedFields = getElementsByClassName("appliedFields")
			var itemID, tempObj;
			for(var i = 0; i < appliedFields.length; i++){

				itemID = appliedFields[i].id.substr(0,2);
				tempObj = getObjectFromID(itemID + "ARID");
				if(tempObj.value == ARid){

					var message = getObjectFromID("newItemMessage");
					message.innerHTML = "AR Item Already Loaded.";
					message.style.display = "block";

					return false;

				}//end if

			}//endfor
			closeModal();

			var theURL = "receipts_aritem_ajax.php?cm=getARItem&arid=" + encodeURIComponent(ARid);

			loadXMLDoc(theURL, null, false);

			var theReturn;
			try {
				eval("theReturn = "+req.responseText);
			} catch (err) {
				console.log(err);
				closeModal();
				return false;
			}

			aritem = theReturn.item1;

		} else {

			closeModal();

			aritem = {
				id: null,
				invoiceid:null,
				uuid:null,
				type: 'credit',
				relatedid: null,
				itemdate: dateToString(new Date(), 'SQL'),
				amount: 0,
				paid: 0
			}

		}//endif

		aritems.addItem(aritem);

	}, //end method


	switchType: function(e){

		checkbox = e.src();

		var newItemCreditFieldset = getObjectFromID("newItemCreditFieldset");
		var newItemInvoiceFieldset = getObjectFromID("newItemInvoiceFieldset");
		var newItemServiceChargeFieldset = getObjectFromID("newItemServiceChargeFieldset");
		var addButton = getObjectFromID("newItemLoadButton");

		switch(checkbox.value){

			case "invoice":
				newItemCreditFieldset.style.display = "none";
				newItemServiceChargeFieldset.style.display = "none";
				newItemInvoiceFieldset.style.display = "block";

				var theSelect = getObjectFromID("newItemInvoiceARID");
				if(!theSelect)
					addButton.className = "disabledButtons";
				else
					addButton.className = "Buttons";

				break;

			case "service charge":
				newItemCreditFieldset.style.display = "none";
				newItemServiceChargeFieldset.style.display = "block";
				newItemInvoiceFieldset.style.display = "none";

				var theSelect = getObjectFromID("newItemServiceChargeARID");
				if(!theSelect)
					addButton.className = "disabledButtons";
				else
					addButton.className = "Buttons";

				break;


				break;

			case "credit":
				newItemCreditFieldset.style.display = "block";
				newItemServiceChargeFieldset.style.display = "none";
				newItemInvoiceFieldset.style.display = "none";

				addButton.className = "Buttons";

				break;

		}//endswitch

	} //end method

}//end class


/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

	var theform = getObjectFromID("record");
	connect(theform, "onsubmit", receipt.submitForm);

	var rtpCheckbox = getObjectFromID("readytopost");
	connect(rtpCheckbox, "onclick", receipt.checkRTP);

	var paymentmethodid = getObjectFromID("paymentmethodid");
	connect(paymentmethodid, "onchange", receipt.switchPayment);

	var amount = getObjectFromID("amount");
	connect(amount, "onchange", receipt.updateDistributionRemaining);

	var loadOpenButton = getObjectFromID("loadOpenButton");
	connect(loadOpenButton, "onclick", aritems.showLoadOpenDialog);

	var autoApplyButton = getObjectFromID("autoApplyButton");
	connect(autoApplyButton, "onclick", aritems.autoApply);

	var addARItemButton = getObjectFromID("addARItemButton");
	connect(addARItemButton, "onclick", newItemDialog.show);

	var posted = getObjectFromID("posted");
	if(posted){
		var savebutton;
		for(var i =1; i<3; i++){
			savebutton = getObjectFromID("saveButton"+i);
			savebutton.disabled = true;
			savebutton.className = "disabledButtons";
		}
	}//end if


	aritems.setInitialListners();
	aritems.calculateTotalApplied();
	receipt.switchPayment();
	receipt.updateDistributionRemaining();
	receipt.calculateTotalDue();

	var toPass ={

		amt: amount,
		cid: getObjectFromID("clientid"),
		tid: getObjectFromID("id"),
		ccn: getObjectFromID("ccnumber"),
		ccexp: getObjectFromID("ccexpiration"),
		ccv: getObjectFromID("ccverification")

	};

	payment.initialize(getObjectFromID("paymentProcessButton"), getObjectFromID("processscript"), toPass, getObjectFromID("transactionid"))

})
