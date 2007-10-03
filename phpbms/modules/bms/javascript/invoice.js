/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2007, Kreotek LLC                                  |
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

// STATUS CLASS ===================================================
//=================================================================
invoice = {
	
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
		
		var readytopost = getObjectFromID("readytopost");
		var amountdue = getObjectFromID("amountdue");
		var payinfull = getObjectFromID("payinfull");
		var totalti = getObjectFromID("totalti");
		var creditleft = getObjectFromID("creditleft");
		var invoicedate = getObjectFromID("invoicedate")
		
		var errorArray = Array();
		if(readytopost.checked && invoicedate.value == "")
			errorArray[errorArray.length] = "Orders marked ready to post must have an invoice date";
		
		if(readytopost.checked && currencyToNumber(amountdue.value)!= 0 && payinfull.style.display != "none")
			errorArray[errorArray.length] = "Orders marked ready to post and not charged to accounts receivable mut be paid in full.";
		
		if(payinfull.style.display == "none" && currencyToNumber(creditleft.value) < currencyToNumber(totalti.value))
			errorArray[errorArray.length] = "Orders ammount exceeds credit left. ("+creditleft.value+")";

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

		setLineItems();

	}//end method
	
}//end class

// STATUS CLASS ===================================================
//=================================================================
theStatus = {

	statusChosen: function(e){
		
		var status=getObjectFromID("statusid");
		var assignedto=getObjectFromID("ds-assignedtoid");

		//update assignedto
		if(statuses[status.value]["firstname"] || statuses[status.value]["lastname"]){
			
			assignedto.value = (statuses[status.value]["firstname"]+" "+statuses[status.value]["lastname"]).replace(/^\s+|\s+$/g,"");
			lastLookup(assignedto);
			
		}//endif
		
		var readytopost = getObjectFromID("readytopost");
		
		if(statuses[status.value]["readytopost"] = 1){
			
			var invoicedate = getObjectFromID("invoicedate");
			if(!invoicedate.value)
				invoicedate.value = dateToString(new Date());
			readytopost.checked = true;
			
		} else {
			
			readytopost.checked = false;
			
		}//endif
					
		theStatus.statusChange();
		
	},//end mehtod


	checkRTP: function(e){
		
		var readytopost = getObjectFromID("readytopost")
		
		if(readytopost.checked){
			
			var invoicedate = getObjectFromID("invoicedate");
			if(!invoicedate.value)
				invoicedate.value = dateToString(new Date());
		}//endif
		
	}, //end method


	statusChange: function(e){

		var statuschanged=getObjectFromID("statuschanged");
		statuschanged.value=1;
		
	},//end method
	
	
	updateDate: function(e){
		
		var statusdate=getObjectFromID("statusdate");
		var today=new Date();
		statusdate.value=dateToString(today);
		
		theStatus.statusChange();
	}//end method
	
}//end class


// CLIENT CLASS ===================================================
//=================================================================
client = {

	getInfo: function(e){
				
	
		var clientid=getObjectFromID("clientid");	
		var theitem, thevalue, fieldName, ident;
		var base=document.URL;
		base=base.substring(0,base.indexOf("invoices_addedit.php"));
		
		if(clientid.value!="") {
			var theurl=base+"invoices_client_ajax.php?id="+clientid.value;
			loadXMLDoc(theurl,null,false);
			response = req.responseXML.documentElement;
			
			for(i=0;i<response.getElementsByTagName('field').length;i++){
				
				fieldName = response.getElementsByTagName('field')[i].firstChild.data;
				
				theitem = getObjectFromID(fieldName);
				
				if(response.getElementsByTagName('value')[i].firstChild)
					thevalue=response.getElementsByTagName('value')[i].firstChild.data;
				else
					thevalue="";
					
				if(!theitem)
					alert("<b>Error</b><br /> Could not find field: "+response.getElementsByTagName('field')[i].firstChild.data);
				else{							
					
					theitem.value = thevalue;
					
					if(theitem.id != "taxareaid" && theitem.id != "discountid"){
						
						//legeacy
						if(theitem.onchange) theitem.onchange();
						
						ident = getIdent(theitem, "onchange");
						if(ident)
							ident[2]();
						
						//legacy
						if(theitem.onblur) theitem.onblur();
						
						ident = getIdent(theitem, "onblur");
						if(ident)
							ident[2]();
							
					}//end if

				}//endif
			}//endfor
			
			//now need to run taxarea and discount on changes (due to AJAX calls)
			tempitem=getObjectFromID("discountid");
			tempitem.onchange();		
			var tempitem=getObjectFromID("taxareaid");
			tempitem.onchange();		
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
			}//endif
		}//end if
		
		return true;
		
	}//end method


}//end class



function payInFull(){

	var amtpaid = getObjectFromID("amountpaid");
	var totalti = getObjectFromID("totalti");

	amtpaid.value=totalti.value;
	
	calculatePaidDue();
}

// These function are used when redefining the onchange property of 
// a hidden field for the the taxAreaID.  It uses XMLHttpRequest to
// grab the tax percentage.
function getPercentage(){
	var theitem,thevalue,repsponse;
	var taxareaid =getObjectFromID("taxareaid");	
	var parentax=getObjectFromID("parenTax");
	var taxbox=getObjectFromID("tax")
	
	var base=document.URL;
	base=base.substring(0,base.indexOf("invoices_addedit.php"));
	
	if(taxareaid.value!=0){
		var theurl=base+"invoices_tax_ajax.php?id="+taxareaid.value;
		//need this to be synchronous, so the window does not close and 
		//yack.
		loadXMLDoc(theurl,null,false);
		response = req.responseXML.documentElement;
		thevalue = response.getElementsByTagName('value')[0].firstChild.data;
		theitem=getObjectFromID("taxpercentage");		
		theitem.value=thevalue+"%";
		parentax.innerHTML="("+taxareaid.options[taxareaid.selectedIndex].text+")";
	} else {
		theitem=getObjectFromID("taxpercentage");
		theitem.value="";
		taxbox.value="0";
		parentax.innerHTML="&nbsp;";
	}
	calculateTotal();

	return true;
}


function getDiscount(){
	var thevalue,repsponse;
	var discountid=getObjectFromID("discountid");	
	var parendiscount=getObjectFromID("parenDiscount");

	if(discountid.value==0)
		parendiscount.innerHTML="&nbsp;";
	else
		parendiscount.innerHTML="("+discountid.options[discountid.selectedIndex].text+")";
	
	var base=document.URL;
	base=base.substring(0,base.indexOf("invoices_addedit.php"));
	var	theitem=getObjectFromID("discount");

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
	var thediscount=getObjectFromID("discountamount");
	thediscount.value=numberToCurrency(0);
	
	calculateTotal();
	return true;
}


function clearTaxareaid(){
	var taxpercent=getObjectFromID("taxpercentage");
	var thetaxareaid=getObjectFromID("taxareaid");
	var parentax=getObjectFromID("parenTax");	
	thetaxareaid.selectedIndex=0;
	calculateTotal();
	parentax.innerHTML="("+taxpercent.value+")";
}


function changeTaxAmount(){
	var taxpercent=getObjectFromID("taxpercentage");
	var thetaxareaid=getObjectFromID("taxareaid");
	var parentax=getObjectFromID("parenTax");	
	taxpercent.value="";
	thetaxareaid.selectedIndex=0;
	calculateTotal();
	parentax.innerHTML="("+taxpercent.value+")";
}


function changeShipping(){
	var theselect = getObjectFromID("shippingmethodid");
	var estimateShippingButton=getObjectFromID("estimateShippingButton");

	var newClass="graphicButtons buttonShipDisabled";
	var parenShipping=getObjectFromID("parenShipping");
	
	if(theselect.value!=0){
		parenShipping.innerHTML="("+theselect.options[theselect.selectedIndex].text+")";
		if(shippingMethods[theselect.value]["canestimate"]==1){
			newClass="graphicButtons buttonShip";
		}
	} else
		parenShipping.innerHTML="&nbsp;";
		
	estimateShippingButton.className = newClass;
}


shippingNotice="";
function startEstimateShipping(){
	
	if(vTab.timeout!=0)
		vTab.clearTO();
	
	var thebutton = getObjectFromID("estimateShippingButton");
	
	if(thebutton.className.indexOf("Disabled") == -1){
		
		if(shippingNotice=="") {
			var noticeHolder=getObjectFromID("shippingNotice")
			shippingNotice=noticeHolder.innerHTML;
			noticeHolder.innerHTML="";
		}
		
		showModal(shippingNotice,"Estimate Shipping",400,10);		
		
	}//end if
	
}//end function


function performShippingEstimate(base){
	var resultsArea=getObjectFromID("shippingNoticeResults");

	var currentShipping=getObjectFromID("shippingmethodid").value;
	var theURL=base+shippingMethods[currentShipping]["estimationscript"];
	var shiptozip=getObjectFromID("postalcode");	
	var therespond="";

	resultsArea.value="Starting Script (this may take a moment)\n";
	
	theURL+="?shipvia="+encodeURI(shippingMethods[currentShipping]["name"]);
	
		// get table (tbody)
		var thetable=getObjectFromID("LIHeader").parentNode;
		//for each line that starts with LIN  get the last childs first child
		var therow;
		var j;
		var attribs;
		var lipair="";
		for(var i=0;i<thetable.childNodes.length;i++){
			if(thetable.childNodes[i].tagName){
				therow=thetable.childNodes[i];
				if(therow.id.substring(0,3)=="LIN"){
					for(j=0;j<therow.childNodes.length;j++){
						if(therow.childNodes[j].className==""){
							var k=0;
							for(k=0;k<therow.childNodes[j].childNodes.length;k++)
								if(therow.childNodes[j].childNodes[k])
									if(therow.childNodes[j].childNodes[k].className=="LIRealInfo")
										theURL+="&LI"+i+"="+encodeURI(therow.childNodes[j].childNodes[k].innerHTML);
						}
					}					
				}
			}
		}	

	theURL+="&shiptozip="+encodeURI(shiptozip.value);
	//timestamp for client caching
	var today=new Date();
	theURL+="&rand="+today.getTime();
	
	loadXMLDoc(theURL,null,false);
	if(req.responseXML){
		var newShippingAmount = req.responseXML.documentElement.getElementsByTagName('value')[0].firstChild.data;
		
		if(newShippingAmount==0)
			therespond="Estimation returned 0.  Check the client's postal code, and the line item products shipping setup."
		else{
			var shipping=getObjectFromID("shipping");
			shipping.value=newShippingAmount;
			calculateTotal();
			therespond="Shipping Amount Updated";
		}
	} else
	therespond = req.responseText

	resultsArea.value+="Script Response:\n"+therespond+"\n";

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
				var message="The product you entered has prerequisite products that must have<br />";
				message+=	"been purchased by the client prior to ordering this product.<br /><br />";
				message+= 	"Make sure the client has been entered and that they have purchased<br />";
				message+= 	"any prerequiste products before adding this product.";
				message+=	"<DIV align=\"right\"><button class=\"Buttons\" onclick=\"closeModal()\" style=\"width:75px\">ok</button></DIV>";

				
				partnumber.value="";
				partnumberDS.value="";
				partname.value="";
				partnameDS.value="";
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
	if (totalweight.value == "") totalweight.value = 0;
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
		temptd.className="lineitemsRight lineitemsLeft";
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
	temptd.className="lineitemsLeft important";
	temptd.innerHTML=(partnumber.value=="")?"&nbsp;":partnumber.value;
	thetr.appendChild(temptd);
	
	temptd=document.createElement("td");
	temptd.setAttribute("valign","top");
	temptd.className="important";
	temptd.innerHTML=(partname.value=="")?"&nbsp;":partname.value;
	thetr.appendChild(temptd);

	temptd=document.createElement("td");
	temptd.setAttribute("valign","top");
	temptd.innerHTML=(memo.value=="")?"&nbsp;":memo.value;
	thetr.appendChild(temptd);

	temptd=document.createElement("td");
	temptd.setAttribute("valign","top");
	temptd.setAttribute("align","right");
	temptd.innerHTML=(unitprice.value=="")?numberToCurrency(0):unitprice.value;
	thetr.appendChild(temptd);

	temptd=document.createElement("td");
	temptd.setAttribute("valign","top");
	temptd.setAttribute("align","center");
	temptd.innerHTML=(quantity.value=="")?"0":quantity.value;
	thetr.appendChild(temptd);

	temptd=document.createElement("td");
	temptd.setAttribute("valign","top");
	temptd.setAttribute("align","right");
	temptd.innerHTML=(extended.value=="")?numberToCurrency(0):extended.value;
	thetr.appendChild(temptd);

	temptd=document.createElement("td");
	temptd.setAttribute("align","center");
	temptd.style.padding="0px";
	var content="<span class=\"LIRealInfo\">";
	content+=productid.value+"[//]";
	content+=unitcost.value+"[//]";
	content+=unitweight.value+"[//]";
	content+=currencyToNumber(unitprice.value)+"[//]";
	content+=quantity.value+"[//]";
	content+=memo.value+"[//]";
	content+=taxable.value+"</span>";
	content+="<button type=\"button\" onclick=\"return deleteLine(this)\" class=\"graphicButtons buttonMinus\"><span>-</span></button>";
	temptd.innerHTML=content
	thetr.appendChild(temptd);

	thetable.insertBefore(thetr,thelastrow);	
	
	//Update Total Cost
	var totalcost=getObjectFromID("totalcost");
	totalcost.value=Math.round((parseFloat(totalcost.value)+(parseFloat(unitcost.value)*parseFloat(quantity.value)))*100)/100;
	
	//Update Total Weight
	var totalweight=getObjectFromID("totalweight");
	if (totalweight.value == "") totalweight.value = 0;
	totalweight.value=Math.round((parseFloat(totalweight.value)+(parseFloat(unitweight.value)*parseFloat(quantity.value)))*1000)/1000;
	
	//Update Total taxable
	var totaltaxable=getObjectFromID("totaltaxable");
	if(totaltaxable.value == "") totaltaxable.value = 0;
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
	unitprice.value=numberToCurrency(0);
	quantity.value="1";
	extended.value=numberToCurrency(0);
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
	var changed=getObjectFromID("lineitemschanged");
	var lineitems=getObjectFromID("thelineitems");
	if(changed.value==1){
		var allRows=getElementsByClassName("LIRealInfo");
		if(allRows.length){
			for(var i=0;i<allRows.length;i++)
				if(allRows[i].innerHTML!="")
					lineitems.value+=allRows[i].innerHTML+"{[]}";
			if(lineitems.value.length>4)
				lineitems.value=lineitems.value.substring(0,lineitems.value.length-4);
		}
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
	paid=numberToCurrency(numpaid);
	document.forms["record"]["amountpaid"].value=paid;

	//Next Calculate Amount Due
	var numtotal=currencyToNumber(total);
	var due=numtotal-numpaid;
	due=numberToCurrency(due);
	
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
	discountValue=numberToCurrency(numDiscount);
	thediscount.value=discountValue;

	//calculate totaltaxable
	if(totaltaxable.value == "")
		totaltaxable.value = 0;
	var numTotalTaxable=parseFloat(totaltaxable.value)-numDiscount;

	//calculate and reformat subtotal
	var numsubtotal=parseFloat(thetotalBD.value)-numDiscount;
	var subtotalValue=numberToCurrency(numsubtotal);
	subtotal.value=subtotalValue;

	//next calculate and reformat shipping
	var numshipping=currencyToNumber(shipping.value);
	shippingValue=numberToCurrency(numshipping);
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

	taxValue=numberToCurrency(numtax);
	tax.value=taxValue;

	//last calculate and format the grand total
	var thetotal=numsubtotal+numshipping+numtax;
	thetotal=numberToCurrency(thetotal);
	totalti.value=thetotal;
	
	calculatePaidDue();
}

// This function does ALL the kung foo for calculating the extended amount
function calculateExtended(){

	// First, Check and format the price
	var thecurrency = getObjectFromID("price");	
	var theprice = currencyToNumber(thecurrency.value);
	thecurrency.value = numberToCurrency(theprice);
			
	// Next verify that qty is a number
	var quantity = getObjectFromID("qty");
	var qty = parseFloat(quantity.value);
	if(qty == "NaN")
		qty = 0;
	quantity.value = qty;

	// Last, figure extended and reformat to dollar
	var extField = getObjectFromID("extended");
	var extended = roundForCurrency(qty * theprice);
	extField.value = numberToCurrency(extended);
	
}//end function


function showPaymentOptions(){
	var paymentmethodid = getObjectFromID("paymentmethodid");

	var checkinfo=getObjectFromID("checkpaymentinfo");
	var ccinfo=getObjectFromID("ccpaymentinfo");
	var receivableinfo = getObjectFromID("receivableinfo");
	
	var amountpaid = getObjectFromID("amountpaid");
	var payinfull = getObjectFromID("payinfull");
	var amountdue = getObjectFromID("amountdue");
	var totalti = getObjectFromID("totalti");

	var theType;
	if(parseInt(paymentmethodid.value) == 0)
		theType="";
	else
		theType=paymentMethods[parseInt(paymentmethodid.value)]["type"];

	//display appropriate payment details
	switch(theType){

		case "draft":
			checkinfo.style.display = "block";
			ccinfo.style.display = "none";
			receivableinfo.style.display = "none";
			amountpaid.className = "important fieldCurrency fieldTotal";
			amountpaid.readOnly = false;
			payinfull.style.display = "inline";
			break;

		case "charge":
			checkinfo.style.display = "none";
			receivableinfo.style.display = "none";
			ccinfo.style.display = "block";
			amountpaid.className = "important fieldCurrency fieldTotal";
			amountpaid.readOnly = false;
			payinfull.style.display = "inline";
			break;

		case "receivable":
			//first let's check to make sure they can charge to AR
			var hascredit = getObjectFromID("hascredit");
			var creditleft = getObjectFromID("creditleft");
			var totalti = getObjectFromID("totalti");
			var clientid = getObjectFromID("clientid");
			
			var error = "";
			if(!clientid.value)
				error = "Receivable payment method cannot be set until a client is chosen";
			
			if(hascredit.value == 0 && error == "")
				error = "This client has not been setup with a line of credit.";

			if(currencyToNumber(creditleft.value) < currencyToNumber(totalti.value) && error == "")
				error = "Order amount is greater than client's credit limit ("+creditleft.value+" left)";
			
			if(error){
				
				paymentmethodid.selectedIndex = 0;
				alert(error);
				showPaymentOptions();
				return false;
				
			}//endif
			
			receivableinfo.style.display = "block";
			checkinfo.style.display = "none";
			ccinfo.style.display = "none";
			amountpaid.value = numberToCurrency(0);
			amountdue.value = totalti.value;
			amountpaid.className = "important fieldCurrency fieldTotal uneditable";
			amountpaid.readOnly = true;
			payinfull.style.display = "none";
			break;
			
		default:
			receivableinfo.style.display = "none";
			checkinfo.style.display="none";
			ccinfo.style.display="none";
			amountpaid.className = "important fieldCurrency fieldTotal";
			amountpaid.readOnly = false;
			payinfull.style.display = "inline";
			
	}//endswtich
	
	//update parentesis display
	var parenPayment=getObjectFromID("parenPayment");
	if(parseInt(paymentmethodid.value) == 0)
		parenPayment.innerHTML="&nbsp;";
	else
		parenPayment.innerHTML="("+paymentMethods[parseInt(paymentmethodid.value)]["name"]+")";
		
	//next onlinceprocessing
	var online;
	var transactionid=getObjectFromID("pTransactionid");
	var paymentButton=getObjectFromID("paymentProcessButton");

	if(parseInt(paymentmethodid.value) == 0)
		online = 0;
	else
		online = paymentMethods[parseInt(paymentmethodid.value)]["onlineprocess"];
	
	var processscript = getObjectFromID("processscript");

	if(online==1){

		processscript.value = paymentMethods[parseInt(paymentmethodid.value)]["processscript"];
		transactionid.style.display="block";
		paymentButton.className="graphicButtons buttonMoney";
		
	} else {
		
		processscript.value = "";
		transactionid.style.display="none";
		paymentButton.className="graphicButtons buttonMoneyDisabled";
		
	}//endif
	
}//endfunction


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
			theform[i].disabled="disabled";
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
	var discount=getObjectFromID("discount");
	discount.value="";
	discountid.selectedIndex=0;	
	discountid.value="";
}


// VERTICAL TABS CLASS ============================================
//=================================================================
vTab = {
	timeout: 0,
	
	over: function(e){
		
		thetab = e.src();

		//cancel any timeouts
		if(vTab.timeout !=0 )
			vTab.clearTO()
		
		//onhover any tabs that are active
		var i;
		var othertab;
		var othercontent;

		for(i=1;i<5;i++){

			if("vTab"+i != thetab.id){
				othertab=getObjectFromID("vTab"+i);
				othercontent=getObjectFromID("vContent"+i)			
				othertab.className="invoiceTotalLabels vTabs";
				othercontent.style.display="none";
			} else {
				var thecontent=getObjectFromID("vContent"+i);
			}//end if
			
		}//endfor
		
		var pareninfo=getObjectFromID("parenInfo");
		pareninfo.style.display="none";
		
		//change to hover class
		thetab.className="invoiceTotalLabels vTabsHover";
		thecontent.style.display="block";
		thecontent.style.height=(thecontent.parentNode.offsetHeight-16)+"px";
		
		for(i=0;i<thecontent.childNodes.length;i++)
			if(thecontent.childNodes[i].tagName=="FIELDSET")
				thecontent.childNodes[i].style.height=(thecontent.offsetHeight-34)+"px";	
				
	},//end method
	
	
	out: function(e){

		var i;
		var thetab;
		
		for(i=1;i<5;i++){
			thetab=getObjectFromID("vTab"+i);
			if(thetab.className=="invoiceTotalLabels vTabsHover"){
				thetab.className="invoiceTotalLabels vTabs";
				thecontent=getObjectFromID("vContent"+i)
				thecontent.style.display="none";
			}
		}
		
		var pareninfo=getObjectFromID("parenInfo");
		pareninfo.style.display="block";
		vTab.timeout=0;

	},//end method


	clearTO: function(e){
		
		window.clearTimeout(vTab.timeout);
		vTab.timeout = 0;
		
	},//end method


	setTO: function(e){
		
		vTab.timeout = window.setTimeout("vTab.out()",1000);
		
	}//end method
	
}//end class


/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

	calculateTotal();
	showPaymentOptions();

	var displayClient=getObjectFromID("ds-clientid");
	displayClient.focus();
	
	var theForm = getObjectFromID("record");
	connect(theForm, "onsubmit", invoice.submitForm);
	
	var clientid = getObjectFromID("clientid");
	connect(clientid,"onchange",client.getInfo);
	
	var assignedtoid = getObjectFromID("assignedtoid");
	connect(assignedtoid, "onchange", theStatus.statusChange);
	
	var statusdate = getObjectFromID("statusdate");
	connect(statusdate, "onchange", theStatus.statusChange);
	
	var statusid = getObjectFromID("statusid");
	connect(statusid,"onchange", theStatus.statusChosen);
	
	var readytopost = getObjectFromID("readytopost");
	connect(readytopost, "onclick", theStatus.checkRTP);
	
	var vTabContents = getElementsByClassName("vContent");
	
	for(var i=0; i< vTabContents.length; i++){
		connect(vTabContents[i],"onmouseover",vTab.clearTO);
		connect(vTabContents[i],"onmouseout",vTab.setTO);
	}

	var vTabs = getElementsByClassName("vTabs");
	for(var i=0; i< vTabs.length; i++){
		connect(vTabs[i],"onmouseover",vTab.over);
		connect(vTabs[i],"onmouseout",vTab.setTO);
	}
	
	var ccnumber1 = getObjectFromID("ccnumber");
	if(ccnumber1){
		var toPass ={
			amt: getObjectFromID("amountpaid"),
			cid: getObjectFromID("clientid"),
			tid: getObjectFromID("id"),
			ccn: ccnumber1,
			ccexp: getObjectFromID("ccexpiration"),
			ccv: getObjectFromID("ccverification")
		};
	
		payment.initialize(getObjectFromID("paymentProcessButton"), getObjectFromID("processscript"), toPass, getObjectFromID("transactionid"))
		
	}
})