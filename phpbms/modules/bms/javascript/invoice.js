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

function returnFalse(){
	return false;
}

function initializePage(){
	var taxid=getObjectFromID("taxareaid");
	taxid.onchange=getPercentage;

	var discountid=getObjectFromID("discountid");
	discountid.onchange=getDiscount;

	calculateTotal();
	showPaymentOptions();
	var theid=getObjectFromID("id");
	var clientid=getObjectFromID("clientid");
	if(clientid.value!=""){
		if(theid.value==""){
			clientid.onchange();
		}
	}
	else {
		var displayClient=getObjectFromID("ds-clientid");
		displayClient.focus();
	}
}

// These function are used when redefining the onChange property of 
// a hidden field for the the taxAreaID.  It uses XMLHttpRequest to
// grab the tax percentage.
function getPercentage(){
	var theitem,thevalue,repsponse;
	var taxareaid =getObjectFromID("taxareaid");	

	var base=document.URL;
	base=base.substring(0,base.indexOf("invoices_addedit.php"));
	if(taxareaid.value){
		var theurl=base+"invoices_tax_ajax.php?id="+taxareaid.value;
		//need this to be synchronous, so the window does not close and 
		//yack.
		loadXMLDoc(theurl,null,false);
		response = req.responseXML.documentElement;
		thevalue = response.getElementsByTagName('value')[0].firstChild.data;
		
		theitem=getObjectFromID("taxpercentage");
		theitem.value=thevalue+"%";
	} else {
		theitem=getObjectFromID("taxpercentage");
		theitem.value="";
	}
	calculateTotal();
	return true;
}

function getDiscount(){
	var thevalue,repsponse;
	var discountid =getObjectFromID("discountid");	

	var base=document.URL;
	base=base.substring(0,base.indexOf("invoices_addedit.php"));
	var	theitem=getObjectFromID("discount");
	if(discountid.value){
		var theurl=base+"invoices_discount_ajax.php?id="+discountid.value;
		//need this to be synchronous, so the window does not close and 
		//yack.
		loadXMLDoc(theurl,null,false);
		if(!req.responseXML) {
			alert(req.responseText);
			return false;
		}
		response = req.responseXML.documentElement;
		thevalue = response.getElementsByTagName('value')[0].firstChild.data;
		
		theitem.value=thevalue;
	} else {
		theitem.value="";
	}
	calculateTotal();
	return true;
}

function changeTaxPercentage(){
	var thetaxareaid=getObjectFromID("taxareaid");
	var taxareaidDisplay=getObjectFromID("ds-taxareaid");
	thetaxareaid.value="";
	taxareaidDisplay.value="";
	autofill["taxareaid"]["ch"]="";
	autofill["taxareaid"]["uh"]="";
	autofill["taxareaid"]["vl"]="";	
	calculateTotal();
}

function changeTaxAmount(){
	var taxpercent=getObjectFromID("taxpercentage");
	var thetaxareaid=getObjectFromID("taxareaid");
	var taxareaidDisplay=getObjectFromID("ds-taxareaid");
	taxpercent.value="";
	thetaxareaid.value="";
	taxareaidDisplay.value="";
	autofill["taxareaid"]["ch"]="";
	autofill["taxareaid"]["uh"]="";
	autofill["taxareaid"]["vl"]="";	
	calculateTotal();
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
		var theurl=base+"invoices_client_ajax.php?id="+clientid.value;
		loadXMLDoc(theurl,null,false);
		response = req.responseXML.documentElement;
		for(i=0;i<response.getElementsByTagName('field').length;i++){
			theitem=getObjectFromID(response.getElementsByTagName('field')[i].firstChild.data);
			
			thevalue="";
			if(response.getElementsByTagName('value')[i].firstChild)
				thevalue=response.getElementsByTagName('value')[i].firstChild.data;
			if(!theitem)
				alert("<b>Error</b><br /> Could not find field: "+response.getElementsByTagName('field')[i].firstChild.data);
			else{
			theitem.value=thevalue;
			if(theitem.onchange) theitem.onchange();
			if(theitem.onblur) theitem.onblur();
			}
		}
	} else {
		//blank out current shipping
		theitem=getObjectFromID("address1");
		if((theitem.value!="") && confirm("Do you wish to clear the shipping information?")){
				theitem.value="";
				theitem=getObjectFromID("address2");
				theitem.value="";
				theitem=getObjectFromID("city");
				theitem.value="";
				theitem=getObjectFromID("state");
				theitem.value="";
				theitem=getObjectFromID("postalcode");
				theitem.value="";
				theitem=getObjectFromID("country");
				theitem.value="";
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
		var theurl=base+"invoices_shipping_ajax.php?id="+theid.value;
		var theurl=theurl+"&postalcodeto="+postalcode.value;
		var theurl=theurl+"&shipvia="+encodeURIComponent(shippingmethod.value);

		loadXMLDoc(theurl,null,false);
		response = req.responseXML.documentElement;

		thevalue="0";
		if(response.getElementsByTagName('value')[0].firstChild)
			thevalue = response.getElementsByTagName('value')[0].firstChild.data; 
		var theshipping=getObjectFromID("shipping");
		if(thevalue==0){
			var message="<div class=\"important\">Estimate shipping returned a zero value.</div>";
			message   +="<div>This may be caused because:</div><ol>";
			message   +="<li>The UPS calculating site is not working.</li>";
			message   +="<li>UPS could not ship to the zip/postal code using the shipping method.</li>";
			message   +="<li>The products on the invoice do not contain the proper shipping information.</li>";
			message   +="<li>An unrecognized form of shipping was chosen. (non UPS)</li></ol>";
			alert(message);
		}
		theshipping.value=thevalue;
		theshipping.onchange();
	} else alert("In order to estimate shipping, a shipping zip/postal code must be entered \n and a shipping method must be selected.")
	return true;
}

//this function makes sure that amount due is 0 before allowing changing to an invoice
function checkType(theitem){
	if (theitem.value=="Invoice") {
		var amountdue=getObjectFromID("amountdue");
		var invoicedate=getObjectFromID("invoicedate");
		var shipped=getObjectFromID("statusShipped");
		if(currencyToNumber(amountdue.value)!=0){
			theitem.value="Order";
			alert("The order has not been fully paid. \n Check the 'amount paid' field.");
		} else{
			if(invoicedate.value==""){
				var currentdate= new Date();
				invoicedate.value=(currentdate.getMonth()+1)+"/"+currentdate.getDate()+"/"+currentdate.getFullYear();
			}
			shipped.checked=true;
		}
		
	} 
	
}

//this function opens a page in a new window that will lookup and populate the add line item info based on a choosen partnumber
function populateLineItem(){
	if (this.value!=""){
		var clientid=getObjectFromID("clientid")
		var partnumber=getObjectFromID("partnumber");
		var partnumberDS=getObjectFromID("ds-partnumber");
		var partname=getObjectFromID("partname");
		var partnameDS=getObjectFromID("ds-partname");
		var tempitem;
		
		var base=document.URL;
		base=base.substring(0,base.indexOf("invoices_addedit.php"));

		var theurl=base+"invoices_lineitem_ajax.php?id="+this.value;
		theurl=theurl+"&cid="+clientid.value;
		
		loadXMLDoc(theurl,null,false);
		response = req.responseXML.documentElement;		
		
		if(response.getElementsByTagName('value')[0].firstChild)
			if(response.getElementsByTagName('value')[0].firstChild.data=="Prerequisite Not Met"){
				// did not meet prerequisites
				var message="The product you entered has prerequisite products that must have<br \>";
				message+=	"been purchased by the client prior to ordering this product.<br \><br \>";
				message+= 	"Make sure the client has been entered and that they have purchased<br \>";
				message+= 	"any prerequiste products before adding this product.";
				message+=	"<DIV align=\"right\"><button class=\"Buttons\" onClick=\"closeModal()\" style=\"width:75px\">ok</button></DIV>";

				
				partnumber.value="";
				partnumberDS.value="";
				partname.value="";
				partnameDS.value="";
				var thediv1=getObjectFromID("dd-partnumber");
				var thediv2=getObjectFromID("dd-partname");
				thediv1.style.display="none";
				thediv2.style.display="none";
				partnameDS.focus();
				showModal(message,"Prerequisite Not Met",400,10);

			} else {
				for(i=0;i<response.getElementsByTagName('field').length;i++){
					tempitem=getObjectFromID(response.getElementsByTagName('field')[i].firstChild.data);
					if(!tempitem) alert("Field not found: "+response.getElementsByTagName('field')[i].firstChild.data);
					thevalue="";
					if(response.getElementsByTagName('value')[i].firstChild)
						thevalue=response.getElementsByTagName('value')[i].firstChild.data;
					tempitem.value=thevalue;
					if(tempitem.onchange && tempitem.name=="price") tempitem.onchange();
				}
		
				if(this.form["memo"]) this.form["memo"].focus();
			}
		
	}
	return true;
}


//This function set the line item to be deleted
function deleteLine(thebutton){
	var thetd=thebutton.parentNode;
	var thetr=thetd.parentNode;

	var attribs;
	if(thetd.firstChild.innerHTML)
		attribs= thetd.firstChild.innerHTML.split("[//]",7);
	else
		attribs= thetd.childNodes[1].innerHTML.split("[//]",7);
	
	var unitcost= attribs[1];
	var quantity= attribs[4];
	var unitprice= attribs[3];
	var unitweight= attribs[2];	
	var taxable= attribs[6];	

	//Update Total Taxable
	var totaltaxable=getObjectFromID("totaltaxable");
	totaltaxable.value=totaltaxable.value-(unitprice*quantity*taxable);

	//Update Total Cost
	var totalcost=getObjectFromID("totalcost");
	totalcost.value=Math.round((parseFloat(totalcost.value)-(parseFloat(unitcost)*parseFloat(quantity)))*100)/100;
	
	//Update Total Weight
	var totalweight=getObjectFromID("totalweight");
	totalweight.value=Math.round((parseFloat(totalweight.value)-(parseFloat(unitweight)*parseFloat(quantity)))*1000)/1000;
	
	//Update Totals
	var totalBD=getObjectFromID("totalBD");
	totalBD.value=parseFloat(totalBD.value)-(unitprice*quantity);
	calculateTotal();

	// Remove The Line
	var thetbody=thetr.parentNode;
	thetbody.removeChild(thetr);
	
	var lineitemschanged=getObjectFromID("lineitemschanged");
	lineitemschanged.value=1;
}

var addlinenum=0;
function addLine(thetd){
	var thetable=thetd.parentNode.parentNode;
	var thelastrow=getObjectFromID("LITotals")
	
	var productid=getObjectFromID("partnumber");
	var partnumber=getObjectFromID("ds-partnumber");
	var partname=getObjectFromID("ds-partname");
	var memo=getObjectFromID("memo");
	var unitweight=getObjectFromID("unitweight");
	var unitcost=getObjectFromID("unitcost");
	var unitprice=getObjectFromID("price");
	var quantity=getObjectFromID("qty");
	var extended=getObjectFromID("extended");
	var taxable=getObjectFromID("taxable");
	var imgPath=getObjectFromID("imgpath");

	if(unitcost.value=="")
		unitcost.value="0";
	if(unitweight.value=="")
		unitweight.value="0";
	
	var sep=getObjectFromID("LISep");
	if(!sep){
		var thetr=document.createElement("tr");
		thetr.id="LISep";
		var temptd=document.createElement("td");
		temptd.setAttribute("colSpan",7);
		temptd.className="dottedline lineitemsRight lineitemsLeft";
		temptd.style.fontSize="1px";
		temptd.style.padding="0px";
		temptd.innerHTML="&nbsp;";
		thetr.appendChild(temptd);
		thetable.insertBefore(thetr,thelastrow);	
	}
	//Create the line
	var thetr=document.createElement("tr");
	thetr.id="LINN"+addlinenum++;
	thetr.className="lineitems";
	
	temptd=document.createElement("td");
	temptd.setAttribute("nowrap","nowrap");
	temptd.setAttribute("valign","top");
	temptd.className="small lineitemsLeft important";
	temptd.innerHTML=(partnumber.value=="")?"&nbsp;":partnumber.value;
	thetr.appendChild(temptd);
	
	temptd=document.createElement("td");
	temptd.setAttribute("valign","top");
	temptd.className="small important";
	temptd.innerHTML=(partname.value=="")?"&nbsp;":partname.value;
	thetr.appendChild(temptd);

	temptd=document.createElement("td");
	temptd.setAttribute("valign","top");
	temptd.className="tiny";
	temptd.innerHTML=(memo.value=="")?"&nbsp;":memo.value;
	thetr.appendChild(temptd);

	temptd=document.createElement("td");
	temptd.setAttribute("valign","top");
	temptd.setAttribute("align","right");
	temptd.className="small";
	temptd.innerHTML=(unitprice.value=="")?formatCurrency(0):unitprice.value;
	thetr.appendChild(temptd);

	temptd=document.createElement("td");
	temptd.setAttribute("valign","top");
	temptd.setAttribute("align","center");
	temptd.className="small";
	temptd.innerHTML=(quantity.value=="")?"0":quantity.value;
	thetr.appendChild(temptd);

	temptd=document.createElement("td");
	temptd.setAttribute("valign","top");
	temptd.setAttribute("align","right");
	temptd.className="small";
	temptd.innerHTML=(extended.value=="")?formatCurrency(0):extended.value;
	thetr.appendChild(temptd);

	temptd=document.createElement("td");
	temptd.setAttribute("align","center");
	temptd.style.padding="0px";
	var content="<span style=\"display:none;\">";
	content+=productid.value+"[//]";
	content+=unitcost.value+"[//]";
	content+=unitweight.value+"[//]";
	content+=currencyToNumber(unitprice.value)+"[//]";
	content+=quantity.value+"[//]";
	content+=memo.value+"[//]";
	content+=taxable.value+"</span>";
	content+="<button type=\"button\" class=\"invisibleButtons\" onClick=\"return deleteLine(this)\"><img src=\""+imgPath.value+"/button-minus.png\" align=\"middle\" alt=\"-\" width=\"16\" height=\"16\" border=\"0\" /></button>";
	temptd.innerHTML=content
	thetr.appendChild(temptd);

	thetable.insertBefore(thetr,thelastrow);	
	
	//Update Total Cost
	var totalcost=getObjectFromID("totalcost");
	totalcost.value=Math.round((parseFloat(totalcost.value)+(parseFloat(unitcost.value)*parseFloat(quantity.value)))*100)/100;
	
	//Update Total Weight
	var totalweight=getObjectFromID("totalweight");
	totalweight.value=Math.round((parseFloat(totalweight.value)+(parseFloat(unitweight.value)*parseFloat(quantity.value)))*1000)/1000;
	
	//Update Total taxable
	var totaltaxable=getObjectFromID("totaltaxable");
	totaltaxable.value=parseFloat(totaltaxable.value)+(currencyToNumber(extended.value)*parseFloat(taxable.value));

	//Update Totals
	var totalBD=getObjectFromID("totalBD");
	totalBD.value=parseFloat(totalBD.value)+currencyToNumber(extended.value);
	calculateTotal();
	
	//clear line
	productid.value="";
	partnumber.value="";
	partname.value="";
	memo.value="";
	taxable.value=1;
	unitweight.value=0
	unitcost.value=0
	unitprice.value=formatCurrency(0);
	quantity.value="1";
	extended.value=formatCurrency(0);
	autofill["partname"]["ch"]="";
	autofill["partname"]["uh"]="";
	autofill["partname"]["vl"]="";	
	autofill["partnumber"]["ch"]="";
	autofill["partnumber"]["uh"]="";
	autofill["partnumber"]["vl"]="";

	var lineitemschanged=getObjectFromID("lineitemschanged");
	lineitemschanged.value=1;	
}

function setLineItems(){
	// if lineitemschanged=1
	var changed=getObjectFromID("lineitemschanged");
	var lineitems=getObjectFromID("thelineitems");
	if(changed.value==1){
		// get table (tbody)
		var thetable=getObjectFromID("LIHeader").parentNode;
		//for each line that starts with LIN  get the last childs first child
		var therow;
		var j;
		var attribs;
		lineitems.value="";
		for(var i=0;i<thetable.childNodes.length;i++){
			if(thetable.childNodes[i].tagName){
				therow=thetable.childNodes[i];
				if(therow.id.substring(0,3)=="LIN"){
					for(j=0;j<therow.childNodes.length;j++){
						if(therow.childNodes[j].className==""){
							// set text area "thelineitems" field contents to variable
							if(therow.childNodes[j].firstChild.innerHTML)
								lineitems.value+=therow.childNodes[j].firstChild.innerHTML+"{[]}";
							else
								lineitems.value+=therow.childNodes[j].childNodes[1].innerHTML+"{[]}";
						}
					}					
				}
			}
		}
		if(lineitems.value.length>4)
			lineitems.value=lineitems.value.substring(0,lineitems.value.length-4);
	}
}


//this function sets the default shipped date information for shipping appropriately
function setShipped(){
	var thecheckbox=getObjectFromID("statusShipped");
	var thedate=getObjectFromID("shippeddate");

	if(thecheckbox){
		if(thecheckbox.checked && thecheckbox.disabled==false && thedate.value=="") {
			var currentdate= new Date();
			thedate.value=(currentdate.getMonth()+1)+"/"+currentdate.getDate()+"/"+currentdate.getFullYear();
		} 
	}
}

//this function calulates how much is left to pay
function calculatePaidDue(){
	var paid=document.forms["record"]["amountpaid"].value;
	var total=document.forms["record"]["totalti"].value;

	//first calculate and reformat amountpaid
	var numpaid=currencyToNumber(paid);
	paid=formatCurrency(numpaid);
	document.forms["record"]["amountpaid"].value=paid;

	//Next Calculate Amount Due
	var numtotal=currencyToNumber(total);
	var due=numtotal-numpaid;
	due=formatCurrency(due);
	
	document.forms["record"]["amountdue"].value=due;
}

//this function adds all the tax,shipping,subtotal, and totaling stuff
function calculateTotal(){
	var thetotalBD=getObjectFromID("totalBD");
	var subtotal=getObjectFromID("totaltni");
	var thediscount=getObjectFromID("discountamount");
	var shipping=getObjectFromID("shipping"); 
	var taxpercentage=getObjectFromID("taxpercentage");
	var tax=getObjectFromID("tax");
	var totalti=getObjectFromID("totalti");
	var totaltaxable=getObjectFromID("totaltaxable");
	var discountFromID=getObjectFromID("discount");
	
	//calculate and reformat discount
	var numDiscount,discountValue;
	if(discountFromID.value=="" || discountFromID.value=="0" || discountFromID.value=="0%"){
		numDiscount=currencyToNumber(thediscount.value);
	} else {
		// compute discount from discount id
		if(discountFromID.value.indexOf("%")!=-1){
			numDiscount=parseFloat(thetotalBD.value)*parseFloat(discountFromID.value.substring(0,discountFromID.value.length-1))/100
		} else {
			numDiscount=parseFloat(discountFromID.value);
		}
	}
	discountValue=formatCurrency(numDiscount);
	thediscount.value=discountValue;

	//calculate totaltaxable
	var numTotalTaxable=parseFloat(totaltaxable.value)-numDiscount;

	//calculate and reformat subtotal
	var numsubtotal=parseFloat(thetotalBD.value)-numDiscount;
	var subtotalValue=formatCurrency(numsubtotal);
	subtotal.value=subtotalValue;

	//next calculate and reformat shipping
	var numshipping=currencyToNumber(shipping.value);
	shippingValue=formatCurrency(numshipping);
	shipping.value=shippingValue;

	//next calculate and reformat tax
	var taxpercentagevalue=getNumberFromPercentage(taxpercentage.value)
	if (taxpercentagevalue!=0){
		var numtax=numTotalTaxable*(taxpercentagevalue/100);
		if(numtax<0) numtax=0;
	}
	else {
		var numtax=currencyToNumber(tax.value);
		if(numTotalTaxable>0)
			taxpercentagevalue=(numtax/numTotalTaxable)*100;
		else
			taxpercentagevalue=0;
		taxpercentage.value=taxpercentagevalue;
		validatePercentage(taxpercentage,5);
	}
	taxValue=formatCurrency(numtax);
	tax.value=taxValue;

	//last calculate and format the grand total
	var thetotal=numsubtotal+numshipping+numtax;
	thetotal=formatCurrency(thetotal);
	totalti.value=thetotal;
	
	calculatePaidDue();
}

// This function does ALL the kung foo for calculating the extended amount
function calculateExtended(){

	//=================================
	// First, Check and format the price
	//=================================
	var thecurrency=document.forms["record"]["price"].value;
	theprice=currencyToNumber(thecurrency);
	newdollar=formatCurrency(theprice);
	document.forms["record"]["price"].value=newdollar;
		
	//==================================
	// Next verify that qty is a number
	//=================================	
	var theqty=document.forms["record"]["qty"].value;
	theqty=currencyToNumber(theqty)
	document.forms["record"]["qty"].value=theqty

	//=============================================
	// Last, figure extended and reformat to dollar
	//=============================================
	var extended=(theqty*theprice).toString();
	extended=formatCurrency(extended);
	document.forms["record"]["extended"].value=extended;
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

function viewClient(addeditfile){
	var theclient=getObjectFromID("clientid");
	var theid=getObjectFromID("id");
	if (theclient.value!="" &&  theclient.value!=0){
		location.href=addeditfile+"?id="+theclient.value+"&invoiceid="+theid.value;
	}
}

function doPrint(base,id){
		location.href=(base+"print.php?backurl="+encodeURIComponent(document.location));
}

function disableSaves(theform){
	for(i=0;i<theform.length;i++){
		if(theform[i].type=="submit" && theform[i].value=="save"){			
			theform[i].disabled="true";
		}
	}
}

function showWebConfirmationNum(theitem){
		webdiv=getObjectFromID("webconfirmdiv");
		if (theitem.checked)
			webdiv.style.display="block";
		else
			webdiv.style.display="none";
			
}

function clearDiscount(){
	var discountid=getObjectFromID("discountid");
	var discountDisplay=getObjectFromID("ds-discountid");
	var discount=getObjectFromID("discount");
	discount.value="";
	discountid.value="";
	discountDisplay.value="";
	autofill["discountid"]["ch"]="";
	autofill["discountid"]["uh"]="";
	autofill["discountid"]["vl"]="";	
	autofill["discountid"]["ch"]="";
	autofill["discountid"]["uh"]="";
	autofill["discountid"]["vl"]="";	
}