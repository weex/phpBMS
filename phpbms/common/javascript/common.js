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
if (typeof(phpBMS) == 'undefined') {
    phpBMS = {};
}



/* BASE OBJECT FUNCTION -------------------------------------------------------- */
/* ----------------------------------------------------------------------------- */
if (typeof(phpBMS.base) == 'undefined') {
    phpBMS.base = {};
}

phpBMS.base.update = function (self, obj/*, ... */) {
    if (self === null) {
        self = {};
    }
    for (var i = 1; i < arguments.length; i++) {
        var o = arguments[i];
        if (typeof(o) != 'undefined' && o !== null) {
            for (var k in o) {
                self[k] = o[k];
            }
        }
    }
    return self;
};

phpBMS.base.update(phpBMS.base, {

	loadXMLDoc: function(url,readyStateFunction,async) {
	
		if(!readyStateFunction)
			readyStateFunction= null;
	
		if(!async)
			async = false;
		
		// branch for native XMLHttpRequest object
		if (window.XMLHttpRequest) {
			req = new XMLHttpRequest();
			if(req.onreadystatechange && readyStateFunction)
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
	},//end function loadXMLDoc
	
    nameFunctions: function (namespace) {
        var base = namespace.NAME;
        if (typeof(base) == 'undefined') {
            base = '';
        } else {
            base = base + '.';
        }
        for (var name in namespace) {
            var o = namespace[name];
            if (typeof(o) == 'function' && typeof(o.NAME) == 'undefined') {
                try {
                    o.NAME = base + name;
                } catch (e) {
                    // pass
                }
            }
        }
    },//endfunction
	
	
	htmlDecode: function(string){

			var ret, tarea = document.createElement('textarea');
			tarea.innerHTML = string;
			ret = tarea.value;
			return ret;

	},//end function


	reportError: function(error){
		
		if(console){
			if(console.log)
				console.log(error);
		} else
			alert(error);
		
	}//end method - reportError


});//end update

phpBMS.base.EXPORT = [
	"update",
	"nameFunctions",
	"loadXMLDoc",
	"htmlDecode",
	"reportError"
];

phpBMS.base._exportFunctions = function (globals, module) {

	var all = module.EXPORT;
	
    for (var i = 0; i < all.length; i++) {
        globals[all[i]] = module[all[i]];
    }
};

phpBMS.base.__new__ = function () {
	var m = this;
	
	m.nameFunctions(this);
}

phpBMS.base.__new__();
phpBMS.base._exportFunctions(this,phpBMS.base);

/* DOM HANDLEING --------------------------------------------------------------- */
/* ----------------------------------------------------------------------------- */
if (typeof(phpBMS.dom) == 'undefined') {
    phpBMS.dom = {};
}

phpBMS.base.update(phpBMS.dom, {
	
	getObjectFromID: function(id){
		var theObject;
		
		if(document.getElementById)
			theObject=document.getElementById(id);
		else
			theObject=document.all[id];
		return theObject;
	},//end method
	
	
	getElementsByClassName: function(clsName){
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
	}//endMethod	
	
})//end update

phpBMS.dom.EXPORT = [
	"getObjectFromID",
	"getElementsByClassName"
];
phpBMS.dom.__new__ = function (win) {
	var m = phpBMS.base;
    this._document = document;
    this._window = win;
	
	m.nameFunctions(this);
}

phpBMS.dom.__new__(this);
phpBMS.base._exportFunctions(this,phpBMS.dom);


/* EVENT SIGNALING ------------------------------------------------------------- */
/* ----------------------------------------------------------------------------- */
if (typeof(phpBMS.signal) == 'undefined') {
    phpBMS.signal = {};
}

phpBMS.signal._observers = [];

phpBMS.signal.e = function (src, e) {
    this._event = e || window.event;
    this._src = src;
};

phpBMS.base.update(phpBMS.signal.e.prototype,{
	
	    src: function () {
        return this._src;
    },

    event: function () {
        return this._event;
    },

    type: function () {
        return this._event.type || undefined;
    },

    target: function () {
        return this._event.target || this._event.srcElement;
    },

    relatedTarget: function () {
        if (this.type() == 'mouseover') {
            return (this._event.relatedTarget ||
                this._event.fromElement);
        } else if (this.type() == 'mouseout') {
            return (this._event.relatedTarget ||
                this._event.toElement);
        }
        return undefined;
    },
	
    stop: function () {
        this.stopPropagation();
        this.preventDefault();
    },

    stopPropagation: function () {
        if (this._event.stopPropagation) {
            this._event.stopPropagation();
        } else {
            this._event.cancelBubble = true;
        }
    },

    preventDefault: function () {
        if (this._event.preventDefault) {
            this._event.preventDefault();
        } else {
            this._event.returnValue = false;
        }
    }
	
})//end subclass

phpBMS.base.update(phpBMS.signal, {

	getIdent: function(obj, eventName){
		var self = phpBMS.signal;
        var observers = self._observers;

		for(var i = 0; i<observers.length;i++)
			if(observers[i][0] == obj && observers[i][1] == eventName)
				return observers[i];
			
		return false;
		
	},//end method

	_unloadCache: function(){

		var self = phpBMS.signal;
        var observers = self._observers;
        
        for (var i = 0; i < observers.length; i++) {
            self._disconnect(observers[i]);
        }
        
        delete self._observers;
        
        try {
            window.onload = undefined;
        } catch(e) {
            // pass
        }

        try {
            window.onunload = undefined;
        } catch(e) {
            // pass
        }
		
	},//end method


	_listener: function(srcObj, func){
        var E = phpBMS.signal.e;
		
		if(!srcObj || !func){
			
			reportError("ListnerError srcObj:" + srcObj + " func:" + func);
			return false;
			
		}//endif
			
		return function (nativeEvent) {
			return func.apply(srcObj, [new E(srcObj, nativeEvent)]);
		};
	},//end method


	connect: function(srcObj, eventName, func){
		
		if(!srcObj || !eventName || !func){
			
			var err = "Invalid Entry for connect: srcObj:" + srcObj + " eventName:" + eventName + " func:" + func
			reportError(err);			
			
			return false;
			
		}//endif 
		
        var self = phpBMS.signal;
		
		var listener = self._listener(srcObj, func);
		
		if (srcObj.addEventListener) {
			srcObj.addEventListener(eventName.substr(2), listener, false);
		}else if (srcObj.attachEvent) {
			srcObj.attachEvent(eventName, listener); // useCapture unsupported
		}//end if
		
        var ident = [srcObj, eventName, listener];
        self._observers.push(ident);
        return ident;
		
	},


	_disconnect: function(ident){

		var src = ident[0];
        var sig = ident[1];
        var listener = ident[2];

		if (src.removeEventListener) {
            src.removeEventListener(sig.substr(2), listener, false);
        } else if (src.detachEvent) {
            src.detachEvent(sig, listener); // useCapture unsupported
        } 	
	},


	disconnect: function(ident){
        var self = phpBMS.signal;
        var observers = self._observers;
		
        for (var i = 0; i < observers.length; i++) {
            var pident = observers[i];
			if(pident == ident){


				self._disconnect(ident);
				observers.splice(i, 1)
				return true;
			}
		}
		return false;
	},

	trigger: function(src, sig){
		
        var self = phpBMS.signal;
        var observers = self._observers;

        var E = phpBMS.signal.e;
        for (var i = 0; i < observers.length; i++) {
            var ident = observers[i];
            if (ident[0] === src && ident[1] === sig) {
                try {
                    ident[2].apply([new E(src, sig)]);
                } catch (err) {
					reportError(err)
                }
            }//endif
        }//endfor
		
	}//end method - signal
	
})//end class

phpBMS.signal.EXPORT = [
	"connect",
	"disconnect",
	"getIdent",
	"trigger"
]

phpBMS.signal.__new__ = function (win) {
	var m = phpBMS.base;
    this._document = document;
    this._window = win;

    try {
        this.connect(window, 'onunload', this._unloadCache);
    } catch (e) {
        // pass: might not be a browser
    }


	m.nameFunctions(this);
}

phpBMS.signal.__new__(this);

phpBMS.base._exportFunctions(this,phpBMS.signal);




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


function setLoginRefresh(){	
	window.setInterval(doRefresh,(LOGIN_REFRESH*60*1000));
}

function doRefresh(){
	loadXMLDoc((APP_PATH+"include/session.php"),null,false)
	if(req.responseText != "")
		alert("The session has timed out due to inactivity.  You will need to reload the page, and log back in.");
}


function initialiseGetData(){
  window.location.get = new Object();
  if (window.location.search && window.location.search.length > 1){
    var getDataArray =
        window.location.search.substr(1).replace('+', ' ').split(/[&;]/g);
    for (var i = 0; i < getDataArray.length; i++){
      var keyValuePair = getDataArray[i].split('=');
      window.location.get[unescape(keyValuePair[0])]
           = keyValuePair.length == 1
           ? ''
           : unescape(keyValuePair[1]);
    }
  }
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
			case "English, UK":
				sep="/";
				day=parseInt(sDate.substring(0,sDate.indexOf(sep)),10);
				month=parseInt(sDate.substring(sDate.indexOf(sep)+1,sDate.indexOf(sep,sDate.indexOf(sep)+1)),10)-1;
				year=parseInt(sDate.substring(sDate.lastIndexOf(sep)+1),10);
				if(year<100) year+=2000;
			break;
			case "Dutch, NL":
				sep="-";
				day=parseInt(sDate.substring(0,sDate.indexOf(sep)),10);
				month=parseInt(sDate.substring(sDate.indexOf(sep)+1,sDate.indexOf(sep,sDate.indexOf(sep)+1)),10)-1;
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
			
			case "English, UK":
				sep="/";
				month=thedate.getMonth()+1;
				if(month<10) month="0"+month;
				day=thedate.getDate();
				if(day<10) day="0"+day;					
				sdate= day+sep+month+sep+thedate.getFullYear();
			break;
			
			case "Dutch, NL":
				sep="-";
				month=thedate.getMonth()+1;
				if(month<10) month="0"+month;
				day=thedate.getDate();
				if(day<10) day="0"+day;					
				sdate= day+sep+month+sep+thedate.getFullYear();
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
				if(timeArray.length == 3)
					thetime=new Date(0,0,0,parseInt(timeArray[0],10),parseInt(timeArray[1],10),parseInt(timeArray[2],10));
			break;
			
			case "12 Hour":
				timeadd=0;
				if(sTime.indexOf(" PM")!=-1)
					timeadd=12;
				sTime=sTime.replace(/ AM/,"");
				sTime=sTime.replace(/ PM/,"");
				timeArray=sTime.split(":");
				if(timeArray.length==2){
					var hour=parseInt(timeArray[0],10);
					if(hour==12) hour=0;					
					hour=hour+timeadd;
							
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
function roundForCurrency(number){
	
	return Math.round( number * Math.pow(10,CURRENCY_ACCURACY) ) /	Math.pow(10,CURRENCY_ACCURACY);
	
}


function numberToCurrency(number){
	
	var currency="";
	
	if(isNaN(parseFloat(number))) number=0;

	if(number<0)
		currency+="-";

	number = Math.abs(number);
	currency += CURRENCY_SYM;
	
	if(number>0 && number <0)
		currency+="0";

	var lessthanone = Math.round( (number-parseInt(number)) * (Math.pow(10,CURRENCY_ACCURACY)) );
	number = parseInt(number);
	
	if(lessthanone >= Math.pow(10,CURRENCY_ACCURACY)){

		number++;
			
		lessthanone -= Math.pow(10,CURRENCY_ACCURACY);
		
	}//end if
	
	lessthanone = lessthanone.toString()
	
	while(lessthanone.length<CURRENCY_ACCURACY)
		lessthanone = "0" + lessthanone;		

	var withThousands = parseInt(number).toString();
  	var objRegExp  = new RegExp('(-?[0-9]+)([0-9]{3})');

	while(objRegExp.test(withThousands))
       withThousands = withThousands.replace(objRegExp, '$1'+THOUSANDS_SEPARATOR+'$2');
		
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
	currency=currency.replace(CURRENCY_SYM,"");
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
	
	showModal.listeners =[
		connect(window,"onresize",centerModal),
		connect(window,"onscroll",centerModal)		  
	]
}


function closeModal(){
	showModal.boxFadeOut.custom(1,0);
}

function cleanupModal(){

	for(var i=0; i<showModal.listeners.length; i++)
		disconnect(showModal.listeners[i])
		
	document.body.removeChild(showModal.mask);
	document.body.removeChild(showModal.box);

	displaySelectBoxes();

	delete showModal.mask;
	delete showModal.box;	
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
	if (typeof document.body.style.maxHeight == "undefined") {
		for(var i = 0; i < document.all.length; i++) {
			if(document.all[i].tagName)
				if(document.all[i].tagName == "SELECT") 
					document.all[i].style.visibility="hidden";
		}
	}	
}
function displaySelectBoxes() {
	if (typeof document.body.style.maxHeight == "undefined") {
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
	
	var okButton = getObjectFromID("modalOK");
	try{
	    okButton.focus();
	}catch(er){
	    // stupid IE
	}//end try
}

/* Function Overloads and Extensions --------------------- */
/* ------------------------------------------------------- */
String.prototype.trim = function() {
	a = this.replace(/^\s+/, '');
	return a.replace(/\s+$/, '');
};

window.alert = function(txt) {modalAlert(txt);}

/* OnLoad Listener --------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {


if(typeof(APP_PATH) != "undefined"){

	spinner = new Image;
	spinner.src = APP_PATH+"common/image/spinner.gif";		

}

	
	
	
})//end listner