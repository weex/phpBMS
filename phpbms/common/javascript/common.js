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

function englishTime(thedate){
			var ampm = " AM";
			var hours = thedate.getHours()
			if(hours==0) hours=12;
			if (hours>12){
				var ampm = " PM";
				hours=hours-12
			}
			var minutes=thedate.getMinutes();
			if(minutes<10)
				minutes="0"+minutes;
			return hours+":"+minutes+ampm;
}

function englishDate(thedate){
	return (thedate.getMonth()+1)+"/"+thedate.getDate()+"/"+thedate.getFullYear();	
}

function dateFromField(englishdate,englishtime){
	var theyear= parseInt(englishdate.substring(englishdate.lastIndexOf("/")+1),10);
	var themonth= parseInt(englishdate.substring(0,englishdate.indexOf("/")),10)-1;
	var theday= parseInt(englishdate.substring(englishdate.indexOf("/")+1,englishdate.lastIndexOf("/")),10);
	var thedate= new Date(theyear,themonth,theday);
	if(englishtime){
		var thehour=parseInt(englishtime.substring(0,englishtime.indexOf(":")),10);
		var theminute=parseInt(englishtime.substring(englishtime.indexOf(":")+1,englishtime.indexOf(" ")),10);
		var AMPM=englishtime.substring(englishtime.indexOf(" ")+1);
		if(AMPM=="PM" && thehour!=12)
			thehour+=12;
		else if (AMPM=="AM" && thehour==12)
			thehour=0;
		thedate.setHours(thehour,theminute);
	}
	return thedate;	
}

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
