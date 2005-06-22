// These function are used when redefining the onChange property of 
// a hidden field for the the taxAreaID.  It uses XMLHttpRequest to
// grab the tax percentage.
function getPercentage(){
	var theitem,thevalue,repsponse;
	var taxareaid =getObjectFromID("taxareaid");	

	var base=document.URL;
	base=base.substring(0,base.indexOf("invoices_addedit.php"));
	if(taxareaid.value){
		var theurl=base+"invoicetaxpercentage.php?id="+taxareaid.value;
		//need this to be synchronous, so the window does not close and 
		//yack.
		loadXMLDoc(theurl,null,false);
		response = req.responseXML.documentElement;
		thevalue = response.getElementsByTagName('value')[0].firstChild.data;
		
		theitem=getObjectFromID("taxpercentage");
		theitem.value=thevalue;
		theitem=getObjectFromID("displaytaxpercentage");
		theitem.value=thevalue+"%";
	} else {
		theitem=getObjectFromID("taxpercentage");
		theitem.value="0";
		theitem=getObjectFromID("displaytaxpercentage");
		theitem.value="0%";
	}
	theitem=getObjectFromID("tax");
	theitem.onchange();
	return true;
}

// This function is used when redefining the onChange property of 
// a hidden field for the the client ID.  It will then open a small window
// passing the client ID, and retrieve the appropriate shipping info
function populateShipping(){
	var clientid=getObjectFromID("clientid");	
	var theitem,thevalue;
	var base=document.URL;
	base=base.substring(0,base.indexOf("invoices_addedit.php"));
	
	if(clientid.value!="") {
		var theurl=base+"invoiceclientlookup.php?id="+clientid.value;
		loadXMLDoc(theurl,null,false);
		response = req.responseXML.documentElement;
		for(i=0;i<response.getElementsByTagName('field').length;i++){
			theitem=getObjectFromID(response.getElementsByTagName('field')[i].firstChild.data);
			
			thevalue="";
			if(response.getElementsByTagName('value')[i].firstChild)
				thevalue=response.getElementsByTagName('value')[i].firstChild.data;
			theitem.value=thevalue;
			if(theitem.onchange) theitem.onchange();
		}
	}
	return true;
}

function estimateShipping(){
	var theitem,thevalue,repsponse;	
	var theid=getObjectFromID("id");
	var postalcode=getObjectFromID("postalcode");
	var shippingmethod=getObjectFromID("shippingmethod");

	var base=document.URL;
	base=base.substring(0,base.indexOf("invoices_addedit.php"));

	if(postalcode.value!="" && shippingmethod.value!="") {
		var theurl=base+"invoiceestimateshipping.php?id="+theid.value;
		var theurl=theurl+"&postalcodeto="+postalcode.value;
		var theurl=theurl+"&shipvia="+escape(shippingmethod.value);

		loadXMLDoc(theurl,null,false);
		response = req.responseXML.documentElement;

		thevalue="0";
		if(response.getElementsByTagName('value')[0].firstChild)
			thevalue = response.getElementsByTagName('value')[0].firstChild.data; 
		var theshipping=getObjectFromID("shipping");
		if(thevalue==0){
			var message="Estimate shipping returned a zero value. \n";
			message   +="This may be caused because:\n\n";
			message   +="1) The site UPS is not working or could not ship to the zip/postal code using the shipping method.\n";
			message   +="2) The products on the invoice do not contain the proper shipping information.\n";
			message   +="3) A unrecognized form of shipping was chosen.";
			alert(message);
		}
		theshipping.value=thevalue;
		theshipping.onchange();
	} else alert("In order to estimate shipping, a shipping zip/postal code must be entered \n and a shipping method must be selected.")
	return true;
}

//this function makes sure that amount due is 0 before allowing changing to an invoice
function checkStatus(theitem){
	if (theitem.value=="Invoice" && document.forms["record"]["amountdue"].value!="$0.00"){
		alert("The order has not been fully paid. \n Check the 'amount paid' field.");
		theitem.value="Order";
		document.forms["record"]["amountpaid"].focus();
	}
}

//this function opens a page in a new window that will lookup and populate the add line item info based on a choosen partnumber
function populateLineItem(){
	if (this.value!=""){
		var mainform=parent.document.forms["record"];
		var base=document.URL;
		base=base.substring(0,base.indexOf("invoices_addedit.php"));

		var theurl=base+"invoicelineitemlookup.php?id="+this.value;
		var theurl=theurl+"&cid="+mainform["clientid"].value;
		
		loadXMLDoc(theurl,null,false);
		response = req.responseXML.documentElement;		
		
		if(response.getElementsByTagName('value')[0].firstChild)
			if(response.getElementsByTagName('value')[0].firstChild.data=="Prerequisite Not Met"){
				// did not meet prerequisites
				var message=		"The product you entered has prerequisite products that must have \n";
				message=message+	"been purchased by the client prior to ordering this product. \n\n";
				message=message+ 	"Make sure the client has been entered and that they have purchased\n";
				message=message+ 	"any prerequiste products before adding this product.";
				
				alert(message);
				this.form["partnumber"].value="";
				this.form["ds-partnumber"].value="";
				this.form["ds-partname"].value="";
				this.form["partname"].value="";
				var thediv1=getObjectFromID("dd-partnumber");
				var thediv2=getObjectFromID("dd-partname");
				thediv1.style.display="none";
				thediv2.style.display="none";
				this.form["ds-partnumber"].focus();

			} else {
				for(i=0;i<response.getElementsByTagName('field').length;i++){
					theitem=this.form[response.getElementsByTagName('field')[i].firstChild.data];
					if(!theitem) alert(response.getElementsByTagName('field')[i].firstChild.data);
					thevalue="";
					if(response.getElementsByTagName('value')[i].firstChild)
						thevalue=response.getElementsByTagName('value')[i].firstChild.data;
					theitem.value=thevalue;
					if(theitem.onchange && theitem.name=="price") theitem.onchange();
				}
		
				if(this.form["memo"]) this.form["memo"].focus();
			}
		
	}
	return true;
}


//This function set the line item to be deleted
function deleteLine(theid,theitem){
	var theform=theitem.form;
	theform["deleteid"].value=theid;
	return true;
}

//this function sets the default shipped date information for shipping appropriately
function setShipped(theitem){
	var thecheckbox=document.forms["record"]["shipped"]
	var thedate=document.forms["record"]["shippeddate"]

	if (theitem.name=="shipped"){
		if(thecheckbox.checked) {
			var currentdate= new Date();
			thedate.value=(currentdate.getMonth()+1)+"/"+currentdate.getDate()+"/"+currentdate.getFullYear();
		} else
		thedate.value="";
	}
	else thecheckbox.checked=true;
}

//this function calulates how much is left to pay
function calculatePaidDue(){
	var paid=document.forms["record"]["amountpaid"].value;
	var total=document.forms["record"]["totalti"].value;

	//first calculate and reformat amountpaid
	var numpaid=dollartoNumber(paid);
	paid=formatDollar(numpaid);
	document.forms["record"]["amountpaid"].value=paid;

	//Next Calculate Amount Due
	var numtotal=dollartoNumber(total);
	var due=numtotal-numpaid;
	due=formatDollar(due);
	
	document.forms["record"]["amountdue"].value=due;
}

//this function adds all the tax,shipping,subtotal, and totaling stuff
function calculateTotal(){
	var subtotal=document.forms["record"]["totaltni"].value;
	var shipping=document.forms["record"]["shipping"].value;
	var taxpercentage=document.forms["record"]["taxpercentage"].value/100;
	var tax=document.forms["record"]["tax"].value;
	
	//first calculate and reformat subtotal
	var numsubtotal=dollartoNumber(subtotal);
	subtotal=formatDollar(numsubtotal);
	document.forms["record"]["totaltni"].value=subtotal;

	//next calculate and reformat shipping
	var numshipping=dollartoNumber(shipping);
	shipping=formatDollar(numshipping);
	document.forms["record"]["shipping"].value=shipping;

	//next calculate and reformat tax
	if (taxpercentage!=0)
		var numtax=numsubtotal*taxpercentage;
	else 
		var numtax=dollartoNumber(tax);
	tax=formatDollar(numtax);
	document.forms["record"]["tax"].value=tax;

	//last calculate and format the grand total
	var thetotal=numsubtotal+numshipping+numtax;
	thetotal=formatDollar(thetotal);
	document.forms["record"]["totalti"].value=thetotal;
	
	calculatePaidDue();
}

// This function does ALL the kung foo for calculating the extended amount
function calculateExtended(){

	//=================================
	// First, Check and format the price
	//=================================
	var thedollar=document.forms["record"]["price"].value;
	theprice=dollartoNumber(thedollar);
	newdollar=formatDollar(theprice);
	document.forms["record"]["price"].value=newdollar;
		
	//==================================
	// Next verify that qty is a number
	//=================================	
	var theqty=document.forms["record"]["qty"].value;
	theqty=dollartoNumber(theqty)
	document.forms["record"]["qty"].value=theqty

	//=============================================
	// Last, figure extended and reformat to dollar
	//=============================================
	var extended=(theqty*theprice).toString();
	extended=formatDollar(extended);
	document.forms["record"]["extended"].value=extended;
}

function formatDollar(thenumber){
	var newdollar;
	thenumber=thenumber.toString();
	
	// add the dollar sign... remember that if it is a negative number, the minus sign goes in front
	if(thenumber.charAt(0)=="-") {
			newdollar="-$";
			thenumber=thenumber.substring(1,thenumber.length);
		} else newdollar="$";

	var big_string = ""+(Math.round(100*(Math.abs(thenumber))))  //rounding the absolute value times 100
	var biglen = big_string.length                            //how the string gets handled depends on its length
	if (biglen == 0)                   //null
		{retval = "0.00"} 
	else if (biglen == 1)              //1 to 9 (.01 to .09 cents)
		{retval = "0.0"+big_string}
	else if (biglen == 2)              //10 to 99 (.10 to .99 cents)
		{retval = "0."+big_string}
	else  { 						  //all cases above 100 ($1.00)
			//The substring method returns all characters in the string
			// starting with and including the the first argument,
			// up to but not including the second argument.  
			var hundredths_digit = big_string.substring(biglen-1,biglen)  
			var tenths_digit = big_string.substring(biglen-2,biglen-1)    
			var integer_digits = big_string.substring(0,biglen-2)
			// commafy,  borrowed from Danny Goodman, "Javascript Bible"
			var re = /(-?\d+)(\d{3})/
			while (re.test(integer_digits))  {
				integer_digits = integer_digits.replace(re, "$1,$2")
			}
			retval = integer_digits + "." + tenths_digit + hundredths_digit
	}  
    newdollar = newdollar+retval;
	return newdollar;
}

function dollartoNumber(thedollar){
	var i;
	var thenumber="";
	for(i=0;i<thedollar.length;i++){
		if (thedollar.charAt(i)!="$" && thedollar.charAt(i)!="+" && thedollar.charAt(i)!=",") thenumber=thenumber+thedollar.charAt(i);
	}
	//if the first number is a ".", add a 0
	if (thenumber.charAt(0)==".") thenumber="0"+thenumber;

	//get rid of trailing zeros and possibly "."
	while(thenumber.charAt(thenumber.length-1)=="0" && thenumber.indexOf(".")!=-1) thenumber=thenumber.substring(0,thenumber.length-1);
	if(thenumber.charAt(thenumber.length-1)==".") thenumber=thenumber.substring(0,thenumber.length-1);
	
	if (isNaN(parseFloat(thenumber)) || thenumber.length!=((parseFloat(thenumber)).toString()).length) thenumber="0.00";	
	
	thenumber=parseFloat(thenumber);
	return thenumber;
}

function getObjectFromID(id){
	var theObject;
	if(document.getElementById)
		theObject=document.getElementById(id);
	else
		theObject=document.all[id];
	return theObject;
}

function showPaymentOptions(){
	var theform=document.forms["record"];
	var checkinfo=getObjectFromID("checkpaymentinfo");
	var ccinfo=getObjectFromID("ccpaymentinfo");
	switch(theform["paymentmethod"].value){
		case "Personal Check":
		case "Check":
		case "Cashiers Check":
		case "check":
			checkinfo.style.display="block";
			ccinfo.style.display="none";
		break;
		case "VISA":
		case "VISA - Debit": 
		case "American Express":
		case "Master Card":
		case "Discover Card":
			checkinfo.style.display="none";
			ccinfo.style.display="block";
		break;
		default:
			checkinfo.style.display="none";
			ccinfo.style.display="none";
		break;
	}//end switch
}

function viewClient(theform){
	if (theform["clientid"].value!="" &&  theform["clientid"].value!=0){
		location.href="clients_addedit.php?id="+theform["clientid"].value+"&invoiceid="+theform["id"].value;
	}
}

function doPrint(id){
		location.href=("../../print.php?backurl="+escape("modules/bms/invoices_addedit.php?id="+id));
}

function disableSaves(theform){
	for(i=0;i<theform.length;i++){
		if(theform[i].type=="submit" && theform[i].value=="save"){			
			theform[i].disabled="true";
		}
	}
}