// Testing
requiredArray= new Array();
integerArray= new Array();
phoneArray= new Array();
emailArray= new Array();
wwwArray= new Array();
realArray= new Array();
dateArray= new Array();
timeArray= new Array();

function validateForm(theform){
	var i;
	var thereturn=true;
	var errorMessage="";
			
	//skip validation if cancel
	if (theform["cancelclick"]){
		if (theform["cancelclick"].value!=0) return true;
	}


	//need to itterate though all fields... if you find
	// --not found-- anywhere.... invlaidate the form
	for(i=0;i<theform.length;i++){
		if(theform[i].value && theform[i].value=="--not found--"){
			errorMessage+="<LI>One or more fields have an invalid input.</LI>";
			thereturn=false;
			break;
		}
	}	
		
	//validate required fields first
	for(i=0;i<requiredArray.length;i++){
		if(!theform[requiredArray[i][0]].value) {
			errorMessage+="<LI>"+requiredArray[i][1]+"</LI>";
			thereturn=false;
		}
	}
	//next integers
	for(i=0;i<integerArray.length;i++){
		var numcheck=theform[integerArray[i][0]].value;
		if(!validateInteger(numcheck)) {
		errorMessage+="<LI>"+integerArray[i][1]+"</LI>";
			thereturn=false;
		}
	}
	//next real numbers
	for(i=0;i<realArray.length;i++){
		var numcheck=theform[realArray[i][0]].value;
		if(numcheck =="") theform[realArray[i][0]].value="0";
		if(numcheck !="" && !validateReal(numcheck)) {
			errorMessage+="<LI>"+realArray[i][1]+"</LI>";
			thereturn=false;
		}
	}

	//next dates
	for(i=0;i<dateArray.length;i++){
		var thedate=theform[dateArray[i][0]].value;
		if(thedate=="0/0/0000") thedate="";
		if(thedate!="" && !validateDate(thedate)) {
			errorMessage+="<LI>"+dateArray[i][1]+"</LI>";
			thereturn=false;
		}
	}

	//next times
	for(i=0;i<timeArray.length;i++){
		var thetime=theform[timeArray[i][0]].value;
		if(thedate!="" && !validateTime(thetime)) {
			errorMessage+="<LI>"+timeArray[i][1]+"</LI>";
			thereturn=false;
		}
	}
	
	//next phone numbers
	for(i=0;i<phoneArray.length;i++){
		var thevalue=theform[phoneArray[i][0]].value;
		if(thevalue && !validatePhone(thevalue)) {
			errorMessage+="<LI>"+phoneArray[i][1]+"</LI>";
			thereturn=false;
		}
	}
	//next email
	for(i=0;i<emailArray.length;i++){
		var thevalue=theform[emailArray[i][0]].value;
		if(thevalue && !validateEmail(thevalue)) {
			errorMessage+="<LI>"+emailArray[i][1]+"</LI>";
			thereturn=false;
		}
	}

	//next web pages
	for(i=0;i<wwwArray.length;i++){
		var thevalue=theform[wwwArray[i][0]].value;
		if(thevalue!="" && thevalue!="http://" && !validateWebpage(thevalue)) {
			errorMessage+="<LI>"+wwwArray[i][1]+"</LI>";
			thereturn=false;
		}
	}
	if(errorMessage!=""){
		errorMessage="<UL>"+errorMessage+"</UL><DIV align=\"right\"><button class=\"Buttons\" onClick=\"closeModal()\">ok</button></DIV>";

		showModal(errorMessage,"Cannot Save",300);
	}
	return thereturn;
	
}

//validate a time (12 hour)
function validateTime(strValue){
	var objRegExp= /^([1-9]|1[0-2]|0[1-9]){1}(:[0-5][0-9]\ [aApP][mM]){1}$/;
	if(!objRegExp.test(strValue))
		return false;
	else
		return true;
}

// validate dates
function validateDate(strValue) {
  var objRegExp = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{2,4}$/

  //check to see if in correct format
  if(!objRegExp.test(strValue))
    return false; //doesn't match pattern, bad date
  else{
  
	for (var i=1;i<strValue.length;i++){
		if (strValue.charAt(i)=="." || strValue.charAt(i)=="/" || strValue.charAt(i)=="-"){
		    var strSeparator = strValue.substring(i,i+1) //find date separator
			break;
		}
	}
    var arrayDate = strValue.split(strSeparator); //split date into month, day, year
    //create a lookup for months not equal to Feb.
    var arrayLookup = { 1 : 31, 3 : 31, 4 : 30, 5 : 31, 6 : 30, 7 : 31,
                        8 : 31, 9 : 30, 10 : 31, 11 : 30, 12 : 31}
    var intDay = parseInt(arrayDate[1],10);
    //check if month value and day value agree
    var intMonth = parseInt(arrayDate[0],10);
    if(arrayLookup[intMonth] != null) {	  
      if(intDay <= arrayLookup[intMonth] && intDay != 0)
        return true; //found in lookup table, good date
    }

    //check for February
    var intYear = parseInt(arrayDate[2],10);
	if (intYear <1000) intYear=intYear+2000;
    if( ((intYear % 4 == 0 && intDay <= 29) || (intYear % 4 != 0 && intDay <=28)) && intDay !=0 && intMonth==2)
      return true; //Feb. had valid number of days
  }
  return false; //any other values, bad date
}

//validate integer
function validateInteger(thevalue){
		while(thevalue.charAt(0)=="0") thevalue=thevalue.substring(1,thevalue.length);
		if(thevalue=="") thevalue="0";
		var newnum=parseInt(thevalue,10).toString();
		if(!(thevalue.length==newnum.length && newnum != "NaN")) return false; else return true;
}

//validate realnumber
function validateReal(thevalue){
	while(thevalue.charAt(thevalue.length-1)=="0" && thevalue.indexOf(".")!=-1) thevalue=thevalue.substring(0,thevalue.length-1);
	if(thevalue.charAt(thevalue.length-1)==".") thevalue=thevalue.substring(0,thevalue.length-1);

	if (thevalue.charAt(0)==".") thevalue="0"+thevalue;
	if (isNaN(parseFloat(thevalue)) || thevalue.length!=((parseFloat(thevalue)).toString()).length) return false; else return true;
}

// validate phone number
function validatePhone(thevalue){
    if (thevalue.length != 12)
        return false;

	//check to make sure all numbers are integers
	if(!validateInteger(thevalue.substring(0,3))) return false;
	if(!validateInteger(thevalue.substring(4,7))) return false;
	if(!validateInteger(thevalue.substring(8,12))) return false;
	if (thevalue.charAt(3) != "-") return false;
	if (thevalue.charAt(7) != "-") return false;
	return true;
}

//look for a valid email address
// this is a loose validation
function validateEmail(thevalue){
  var result = false
  var theStr = new String(thevalue)
  var index = theStr.indexOf("@");
  if (index > 0)
  {
    var pindex = theStr.indexOf(".",index);
    if ((pindex > index+1) && (theStr.length > pindex+1))
	result = true;
  }
  return result;
}

//validate web page... make sure there is a http:// and at least on .
function validateWebpage(thevalue){
  var theaddress=thevalue.substring(8,thevalue.length-1);
  if(thevalue.substring(0,7)=="http://" && theaddress.indexOf(".",0) !=-1 ) return true;
  else return false;
}

// Open an email
function openEmail(thefieldname){
	theemail= document.forms["record"][thefieldname].value;
	if(theemail!="" && validateEmail(theemail)) location.href="mailto:"+theemail;
	else alert("Email is either blank or invalid.");
}

// Open a web page
function openWebpage(thefieldname){
	theweb= document.forms["record"][thefieldname].value;
	if(theweb!="" && validateWebpage(theweb)) {
		window.open(theweb);
	}
	else alert("Web address is either blank or invalid.");
}

// checks and formats a field to dollars
function validateDollar(theitem){
	var thedollar=theitem.value;
	var i;
	var thenumber="";
	var newdollar;
	
	for(i=0;i<thedollar.length;i++){
		if (thedollar.charAt(i)!="$" && thedollar.charAt(i)!="+" && thedollar.charAt(i)!=",") thenumber=thenumber+thedollar.charAt(i);
	}
	//if the first number is a ".", add a 0
	if (thenumber.charAt(0)==".") thenumber="0"+thenumber;

	//get rid of trailing zeros and possibly "."
	while(thenumber.charAt(thenumber.length-1)=="0" && thenumber.indexOf(".")!=-1) thenumber=thenumber.substring(0,thenumber.length-1);
	if(thenumber.charAt(thenumber.length-1)==".") thenumber=thenumber.substring(0,thenumber.length-1);

	theitem.value=formatDollar(thenumber);
	
	//in case the field has an additional onChange code to be run
	if (theitem.thechange) theitem.thechange();
}

function formatDollar(thenumber){
	var newdollar,retval
	
	//check for number		
	if (isNaN(parseFloat(thenumber)) || thenumber.length!=((parseFloat(thenumber)).toString()).length) thenumber="0.00";
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

function  processUniqueReqChange(){
	var response,isunique,thename,thefield,theitem;
	
	 if (req.readyState == 4) {
		// only if "OK"
		if (req.status == 200) {
			
			response = req.responseXML.documentElement;
			thename = response.getElementsByTagName('name')[0].firstChild.data;
			isunique = response.getElementsByTagName('isunique')[0].firstChild.data;
			
			if(isunique==0) {
				alert("This field requires a unique value.");
				theitem=getObjectFromID(thename);
				theitem.value="";
				theitem.focus();
			}
		}
	}
}

function checkUnique(path,thevalue,thename,thetable,thefield,excludeid){
	
	var theurl=path+"checkunique.php?value="+escape(thevalue);
	theurl=theurl+"&table="+thetable;
	theurl=theurl+"&name="+thename;
	theurl=theurl+"&field="+thefield;
	theurl=theurl+"&excludeid="+excludeid;

	loadXMLDoc(theurl,processUniqueReqChange,true);
}
