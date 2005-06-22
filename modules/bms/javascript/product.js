function calculateMarkUp(){
	var thecost=getObjectFromID("unitcost");
	var theprice=getObjectFromID("unitprice");
	var unitcost="";
	var unitprice="";
	
	for(i=0;i<thecost.value.length;i++){
		if (thecost.value.charAt(i)!="$" && thecost.value.charAt(i)!="+" && thecost.value.charAt(i)!=",") unitcost+=thecost.value.charAt(i);
	}
	unitcost=parseFloat(unitcost);

	for(i=0;i<theprice.value.length;i++){
		if (theprice.value.charAt(i)!="$" && theprice.value.charAt(i)!="+" && theprice.value.charAt(i)!=",") unitprice+=theprice.value.charAt(i);
	}
	unitprice=parseFloat(unitprice);

	if(unitcost!=0 && unitprice!=0){
		
		var markup=getObjectFromID("markup");
		markup.value=(Math.round(unitprice/unitcost*100)-100)+"%"
		
	}
}

function calculatePrice(){
	var themarkup=getObjectFromID("markup");
	var thecost=getObjectFromID("unitcost");
	var theprice=getObjectFromID("unitprice");
	var unitcost="";
	
	var markup=getNumberFromPercentage(themarkup.value);
	
	for(i=0;i<thecost.value.length;i++){
		if (thecost.value.charAt(i)!="$" && thecost.value.charAt(i)!="+" && thecost.value.charAt(i)!=",") unitcost+=thecost.value.charAt(i);
	}
	unitcost=parseFloat(unitcost);
	var newnumber=(Math.round((unitcost+(markup*unitcost/100))*100)/100).toString();
	theprice.value=formatDollar(newnumber);	
}

function changeMarkup(){
	var themarkup=getObjectFromID("markup");
	var markup=getNumberFromPercentage(themarkup.value);
	
	themarkup.value=""+(Math.round(markup*10)/10);
	if(themarkup.value.indexOf(".")==-1) themarkup.value+=".0";
	themarkup.value+="%";
}

function getNumberFromPercentage(thenumber){
	var markupnumber="";
	for(i=0;i<thenumber.length;i++){
		if (thenumber.charAt(i)!="%" && thenumber.charAt(i)!="+" && thenumber.charAt(i)!=",") markupnumber+=thenumber.charAt(i);
	}
	
	//get rid of trailing zeros and possibly "."
	while(markupnumber.charAt(markupnumber.length-1)=="0" && markupnumber.indexOf(".")!=-1) markupnumber=markupnumber.substring(0,markupnumber.length-1);
	if(markupnumber.charAt(markupnumber.length-1)==".") markupnumber=markupnumber.substring(0,markupnumber.length-1);

	if (isNaN(parseFloat(markupnumber)) || markupnumber.length!=((parseFloat(markupnumber)).toString()).length) markupnumber="0";
	markupnumber=parseFloat(markupnumber);
	return markupnumber;
}