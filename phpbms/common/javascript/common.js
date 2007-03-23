/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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

document.getElementsByClassName = function(clsName){
    var retVal = new Array();
    var elements = document.getElementsByTagName("*");
    for(var i = 0;i < elements.length;i++){
        if(elements[i].className.indexOf(" ") >= 0){
            var classes = elements[i].className.split(" ");
            for(var j = 0;j < classes.length;j++){
                if(classes[j] == clsName)
                    retVal.push(elements[i]);
            }
        }
        else if(elements[i].className == clsName)
            retVal.push(elements[i]);
    }
    return retVal;
}

// php equivilant to htmlEntitties
String.prototype.htmlEntities = function()
{	
	newString = this;
	var chars = new Array();
	var charCode
	for(i=0;i<newString.length;i++){
	  charCode=newString.charCodeAt(i);
	  if(charCode==38 || charCode==60 || charCode==62 || charCode==96 || charCode>125)
		chars[chars.length]=newString[i]
	}

	//this if was put in cuz IE is retarded
	if(chars[0])
		for (var i = 0; i < chars.length; i++){
			myRegExp = new RegExp();
			myRegExp.compile(chars[i],'g');
	
			newString = newString.replace (myRegExp, '&#' + chars[i].charCodeAt(0) + ';');
		}
	
	return newString;
}

//Returns an object given an id
function getObjectFromID(id){
	var theObject;
	if(document.getElementById)
		theObject=document.getElementById(id);
	else
		theObject=document.all[id];
	return theObject;
}


// This Function returns the Top position of an object
function getTop(theitem){
	var offsetTrail = theitem;
	var offsetTop = 0;
	while (offsetTrail) {
		offsetTop += offsetTrail.offsetTop;
		offsetTrail = offsetTrail.offsetParent;
	}
	if (navigator.userAgent.indexOf("Mac") != -1 && typeof document.body.leftMargin != "undefined") 
		offsetLeft += document.body.TopMargin;
	return offsetTop;
}

// This Function returns the Left position of an object
function getLeft(theitem){
	var offsetTrail = theitem;
	var offsetLeft = 0;
	while (offsetTrail) {
		offsetLeft += offsetTrail.offsetLeft;
		offsetTrail = offsetTrail.offsetParent;
	}
	if (navigator.userAgent.indexOf("Mac") != -1 && typeof document.body.leftMargin != "undefined") 
		offsetLeft += document.body.leftMargin;
	return offsetLeft;
}

function loadXMLDoc(url,readyStateFunction,async) 
{
	// branch for native XMLHttpRequest object
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
		req.onreadystatechange = readyStateFunction;
		req.open("GET", url, async);
		req.send(null);
	// branch for IE/Windows ActiveX version
	} else if (window.ActiveXObject) {
		req = new ActiveXObject("Microsoft.XMLHTTP");
		if (req) {
			if(readyStateFunction) req.onreadystatechange = readyStateFunction;
			req.open("GET", url, async);
			req.send();
		}
	}
}

function addEvent(obj, evType, fn){
 if (obj.addEventListener){
    obj.addEventListener(evType, fn, true);
    return true;
 } else if (obj.attachEvent){
    var r = obj.attachEvent("on"+evType, fn);
    return r;
 } else {
    return false;
 }
}
function removeEvent(obj, evType, fn, useCapture){
  if (obj.removeEventListener){
    obj.removeEventListener(evType, fn, useCapture);
    return true;
  } else if (obj.detachEvent){
    var r = obj.detachEvent("on"+evType, fn);
    return r;
  } else {
    window.status=("Handler could not be removed");
  }
}

function getChildHeights(theObj){
	var totalHeight=0;
	var i;
	for(i=0;i<theObj.childNodes.length;i++)
		if(theObj.childNodes[i].offsetHeight)
			totalHeight+=theObj.childNodes[i].offsetHeight;
	return totalHeight;	
}

function getViewportHeight() {
	if (window.innerHeight!=window.undefined) {
		return window.innerHeight;
		//var i=document.body.clientHeight;
		//return i;
	}
	if (document.compatMode=='CSS1Compat') return document.documentElement.clientHeight;
	if (document.body) return document.body.clientHeight; 
	return window.undefined; 
}
function getViewportWidth() {
	if (window.innerWidth!=window.undefined) {
		return window.innerWidth;
		//var i=document.body.offsetWidth;
		//return i;
	}
	if (document.compatMode=='CSS1Compat') return document.documentElement.clientWidth; 
	if (document.body) return document.body.clientWidth; 
	return window.undefined; 
}

function disableSave(){
	var tempButton=getObjectFromID("saveButton1");
	if(tempButton)
		tempButton.disabled=true;
	tempButton=getObjectFromID("saveButton2");
	if(tempButton)
		tempButton.disabled=true;		
}


/* DATE AND TIME --------------------------------------------------------------- */
/* ----------------------------------------------------------------------------- */
function stringToDatetime(sDate,sTime){
	thedate=stringToDate(sDate)
	if(sTime){
		var thetime=stringToTime(sTime);
		thedate.setHours(thetime.getHours());
		thedate.setMinutes(thetime.getMinutes());
		thedate.setSeconds(thetime.getSeconds());
	}
	return thedate;
}

function stringToDate(sDate,format){
	if (!format) format=DATE_FORMAT;
	var thedate="";

	if(sDate){
		var sep;
		var month;
		var day;
		var year;
		switch(format){
			case "SQL":
				sep="-";
				year=parseInt(sDate.substring(0,sDate.indexOf(sep)),10);
				month=parseInt(sDate.substring(sDate.indexOf(sep)+1,sDate.indexOf(sep,sDate.indexOf(sep)+1)),10)-1;
				day=parseInt(sDate.substring(sDate.lastIndexOf(sep)+1),10);
			break;
			case "English, US":
				sep="/";
				month=parseInt(sDate.substring(0,sDate.indexOf(sep)),10)-1;
				day=parseInt(sDate.substring(sDate.indexOf(sep)+1,sDate.indexOf(sep,sDate.indexOf(sep)+1)),10);
				year=parseInt(sDate.substring(sDate.lastIndexOf(sep)+1),10);
				if(year<100) year+=2000;
			break;
		}
		thedate=new Date(year,month,day);
	}
	return thedate
}


function dateToString(thedate,format){
	if(!format) format=DATE_FORMAT;
	var sdate="";
	
	if(thedate){
		var sep;
		var month;
		var day;
		switch(format){
			case "SQL":
				sep="-";			
				month=thedate.getMonth()+1;
				if(month<10) month="0"+month;
				day=thedate.getDate();
				if(day<10) day="0"+day;					
				sdate= thedate.getFullYear()+sep+month+sep+day;
			break;
			
			case "English, US":
				sep="/";
				month=thedate.getMonth()+1;
				if(month<10) month="0"+month;
				day=thedate.getDate();
				if(day<10) day="0"+day;					
				sdate= month+sep+day+sep+thedate.getFullYear();
			break;
		}
	}
	return sdate;
}

function stringToTime(sTime,format){
	if(!format) format=TIME_FORMAT;
	var thetime="";
	if(sTime){
		var timeArray;
		switch(format){
			case "24 Hour":
				timeArray=sTime.split(":");
				if(timeArray.length=3)
					thetime=new Date(0,0,0,parseInt(timeArray[0],10),parseInt(timeArray[1],10),parseInt(timeArray[2],10));
			break;
			
			case "12 Hour":
				timeadd=0;
				if(sTime.indexOf(" PM")!=-1)
					timeadd=12;
				sTime=sTime.replace(/ AM/,"");
				sTime=sTime.replace(/ PM/,"");
				timeArray=sTime.split(":");
				if(timeArray.length=2){
					var hour=parseInt(timeArray[0],10);
					if (hour!=12 && timeadd==12)
						hour=hour+timeadd;
					else 
						if(hour==12)
							hour=0;
					thetime=new Date(0,0,0,hour,parseInt(timeArray[1],10));						
				}
			break;
		}
	}
	return thetime;
}

function timeToString(thetime,format){
	sTime="";
	if(!format) format=TIME_FORMAT;
	if(thetime){
		var hours=thetime.getHours();
		var minutes=thetime.getMinutes();
		var seconds=thetime.getSeconds();
		var sep=":"
		switch(format){
			case "24 Hour":
				if(hours<10) hours="0"+hours;
				if(minutes<10) minutes="0"+minutes;
				if(seconds<10) seconds="0"+seconds;
				sTime=hours+sep+minutes+sep+seconds;
			break;
			
			case "12 Hour":
				var ampm=" AM";
				if(hours>11)
					ampm=" PM";
				if(hours>12)
					hours=hours-12;
				if (hours==0) hours=12;
				if(minutes<10) minutes="0"+minutes;				
				sTime=""+hours+sep+minutes+ampm;
			break;
		}
	}
	return sTime;
}
/* CURRENCY -------------------------------------------------------------------- */
/* ----------------------------------------------------------------------------- */
function numberToCurrency(number){
	var currency="";
	if(isNaN(parseFloat(number))) number=0;

	if(number<0)
		currency+="-";
	number=Math.abs(number);
	currency+=CURRENCY_SYMBOL;
	if(number>0 && number <0)
		currency+="0";

	var withThousands=parseInt(number).toString();
  	var objRegExp  = new RegExp('(-?[0-9]+)([0-9]{3})');
	while(objRegExp.test(withThousands))
       withThousands = withThousands.replace(objRegExp, '$1'+THOUSANDS_SEPARATOR+'$2');

	var lessthanone=Math.round((number-parseInt(number))*(Math.pow(10,CURRENCY_ACCURACY))).toString();
	while(lessthanone.length<CURRENCY_ACCURACY)
		lessthanone="0"+lessthanone;
	currency+=withThousands;
	if(CURRENCY_ACCURACY!=0)
		currency+=DECIMAL_SYMBOL+lessthanone;
	return currency;
}

function currencyToNumber(currency){
	var number=0;
	var thousSep=THOUSANDS_SEPARATOR;
	if(thousSep=="." || thousSep=="*" || thousSep=="[" || thousSep=="]" || thousSep=="-" || thousSep=="+")
		thousSep="\\"+thousSep;
	var objRegExp  = new RegExp(thousSep,"g");
	currency=currency.replace(objRegExp,"");
	currency=currency.replace(CURRENCY_SYMBOL,"");
	currency=currency.replace(DECIMAL_SYMBOL,".");
	if(currency)
		number=parseFloat(currency);
	return number
}

/* MODAL ----------------------------------------------------------------------- */
/* ----------------------------------------------------------------------------- */

function showModal(content,title,thewidth,thetop){
	if(thetop==null) thetop=75;
		showModal.thetop=thetop;
	if(title==null) title="&nbsp";
	var alreadyModal=getObjectFromID("modalTitle");
	if(alreadyModal) return false;

	showModal.mask=document.createElement("div");
	showModal.mask.id="modalMask";
	showModal.mask.innerHtml="&nbsp;";

	showModal.box=document.createElement("div");
	showModal.box.id="modalBox";
	if(thewidth!= null){
		showModal.box.style.width=thewidth+"px";
	}
	showModal.box.width="400px";
	
	var tempDiv=document.createElement("div");
	tempDiv.id="modalTitle";
	tempDiv.innerHTML=title;
	showModal.box.appendChild(tempDiv);

        var tempDiv=document.createElement("div");
        tempDiv.id="modalContent";
        tempDiv.innerHTML=content;
        showModal.box.appendChild(tempDiv);

	hideSelectBoxes();
	showModal.boxFade=new fx.Opacity(showModal.box,{duration:100});
	showModal.maskFade=new fx.Opacity(showModal.mask,{duration:100,onComplete:function(){showModal.boxFade.custom(0,1)}});

	showModal.maskFadeOut=new fx.Opacity(showModal.mask,{duration:75,onComplete:function(){cleanupModal()}});
	showModal.boxFadeOut=new fx.Opacity(showModal.box,{duration:75,onComplete:function(){showModal.maskFadeOut.custom(.7,0)}});

	showModal.maskFade.setOpacity(0);
	document.body.appendChild(showModal.mask);
	
	showModal.boxFade.setOpacity(0);
	document.body.appendChild(showModal.box);
	
	showModal.maskFade.custom(0,0.7);
			
	centerModal();
	
	addEvent(window, "resize", centerModal);
	window.onscroll=centerModal;
}


function closeModal(){
	showModal.boxFadeOut.custom(1,0);
}

function cleanupModal(){
	removeEvent(window,"resize",centerModal,true);
	window.onscroll=null;

	document.body.removeChild(showModal.mask);
	document.body.removeChild(showModal.box);
	displaySelectBoxes();

	showModal.mask=null;
	showModal.box=null;
}

function centerModal(){
	if(showModal.mask){

		var fullHeight = getViewportHeight();
		var fullWidth = getViewportWidth();
	
		var theBody = document.documentElement;
	
		var scTop = parseInt(theBody.scrollTop,10);
		var scLeft = parseInt(theBody.scrollLeft,10);
	
		showModal.mask.style.height = fullHeight + "px";
		showModal.mask.style.width = fullWidth + "px";
		showModal.mask.style.top = scTop + "px";
		showModal.mask.style.left = scLeft + "px";
		if(window.innerHeight!=window.undefined){
		if(showModal.mask.scrollWidth>fullWidth)
			showModal.mask.style.height=(fullHeight-20)+"px";	
		if(document.body.scrollHeight>fullHeight)
			showModal.mask.style.width=(fullWidth-20)+"px";
		}
		showModal.box.style.top = scTop +showModal.thetop+"px";
		showModal.box.style.left =  (scLeft + ((fullWidth - showModal.box.offsetWidth) / 2)) + "px";
	} ;
}

function hideSelectBoxes() {
	var brsVersion = parseInt(window.navigator.appVersion.charAt(0), 10);
	if (brsVersion <= 6 && window.navigator.userAgent.indexOf("MSIE") > -1) {		
		for(var i = 0; i < document.all.length; i++) {
			if(document.all[i].tagName)
				if(document.all[i].tagName == "SELECT") 
					document.all[i].style.visibility="hidden";
		}
	}
}
function displaySelectBoxes() {
	var brsVersion = parseInt(window.navigator.appVersion.charAt(0), 10);
	if (brsVersion <= 6 && window.navigator.userAgent.indexOf("MSIE") > -1) {		
        for(var i = 0; i < document.all.length; i++) {
                if(document.all[i].tagName)
                        if(document.all[i].tagName == "SELECT")
                                document.all[i].style.visibility="visible";
        }
	}
}

function modalAlert(text){
	text=""+text;
	text.replace("\n","<br />");
	text+="<DIV align=\"right\"><button id=\"modalOK\" accesskey=\"o\" type=\"button\" class=\"Buttons\" onclick=\"closeModal()\" style=\"width:75px\"> ok </button></DIV>";
	showModal(text,"Alert",250);
}
window.alert = function(txt) {modalAlert(txt);}
