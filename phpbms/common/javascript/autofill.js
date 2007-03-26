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
			key="";
			function captureKey(e){
				 //first line is for IE
				 if(!e) e=window.event;
				 key = e.keyCode;
			}
			
			function autofillChange(theitem){
				
				var basename=theitem.name.substring(3);
				var changeitem=autofill[basename]["ch"];
				var uhitem=autofill[basename]["uh"];
				var tabledefid=autofill[basename]["td"];
				var displayfield=autofill[basename]["fl"];
				var xtrafield=autofill[basename]["xt"];
				var whereclause=autofill[basename]["wc"];

				var thediv=getObjectFromID("dd-"+basename);
				
				if(key){
					if (key==40 || key==38 || key==37 || key==39){
						if(thediv){
								if(key==40) {
									moveHighlight(thediv,"dn")
									return false;}
								else if(key==38){
									moveHighlight(thediv,"up")
									return false;}
							}
						if(theitem.value.toLowerCase()==changeitem.toLowerCase())
							return true;
					}
				}
				if (theitem.value.toLowerCase()!=changeitem.toLowerCase() && theitem.value!=""){
					
					var enteredlength=theitem.value.length
					
					var theURL=appPath+"autofill.php?l=" + encodeURIComponent(theitem.value);
					theURL=theURL+"&tid=" +tabledefid;
					theURL=theURL+"&fl=" +displayfield;
					theURL=theURL+"&xt=" +xtrafield;
					theURL=theURL+"&wc=" +whereclause;
					loadXMLDoc(theURL,null,false);
					if(req && req.responseXML)
						response = req.responseXML.documentElement;
					else{
						alert(req.responseText);
						return false;
					}
						
					
					var restofname="";
					var numrecs=response.getElementsByTagName('numrec')[0].firstChild.data
					if(numrecs>0){
						if(response.getElementsByTagName('val')[0].firstChild){
							restofname=response.getElementsByTagName('val')[0].firstChild.data							
						}
						//more than one result... need to display div
						var displays=new Array();
						var xtras=new Array();
						var thevalue;
						for(i=0;i<response.getElementsByTagName('fld').length;i++){
							thevalue="";
							if(response.getElementsByTagName('val')[i].firstChild)
								thevalue=response.getElementsByTagName('val')[i].firstChild.data;
							if(response.getElementsByTagName('fld')[i].firstChild.data=="display")
								displays[displays.length]=thevalue;
							 else
								xtras[xtras.length]=thevalue;								
						}//end if
						createDropDown(basename,theitem,displays,xtras);
					} else {
						// If the DropDown exists, remove it.
						removeDropDown(basename);
					}
										
					var lower=theitem.value.toLowerCase();
					if(lower.indexOf("%")==-1){
						//highlight, and fill the rest of the stuff.. but only
						//if there is no wildcard character
						//stoopid ie crap
						if(document.selection){
							var temp=document.selection.createRange()
							var isend=(temp.text=="");
						}
						else
							var isend=(theitem.selectionStart==theitem.value.length);
						
						if(changeitem.substring(0,changeitem.length-1)!=theitem.value && isend && (lower!=uhitem)){
							if(restofname){
								//stoopid ie crap
								theitem.value+=restofname.substring(theitem.value.length);
								if(document.selection){
								   var oRange = theitem.createTextRange();
								   oRange.moveStart("character", enteredlength);
								   oRange.moveEnd("character", theitem.value.length );      
									oRange.select(); 							   
								} else {
									theitem.setSelectionRange(enteredlength,theitem.value.length);						
								}
							} 
						}
					} //end % if
					autofill[basename]["uh"]=lower;
					autofill[basename]["ch"]=theitem.value;
				}
				if (theitem.value==""){
					autofill[basename]["ch"]="";
					autofill[basename]["uh"]="";
					//if(thediv)
					//thediv.style.display="none";
				}
				return true;
			}//end function
			
			function lastLookup(theitem){
				var basename=theitem.name.substring(3);
				var thefield=getObjectFromID(basename);			

				if(theitem.value==""){
					thefield.value="";
				} else {
					var tabledefid=autofill[basename]["td"];
					var displayfield=autofill[basename]["fl"];
					var xtrafield=autofill[basename]["xt"];
					var whereclause=autofill[basename]["wc"];
					var getfield=autofill[basename]["gf"];
				
					var theURL=appPath+"autofill.php?l=" + encodeURIComponent(theitem.value);
					theURL=theURL+"&tid=" +tabledefid;
					theURL=theURL+"&fl=" +displayfield;
					theURL=theURL+"&xt=" +xtrafield;
					theURL=theURL+"&gf=" +getfield;
					theURL=theURL+"&wc=" +whereclause;
					loadXMLDoc(theURL,null,false);
					if(req.responseXML)
						response = req.responseXML.documentElement;
					if(response.getElementsByTagName('val').length){
						theitem.value=response.getElementsByTagName('val')[0].firstChild.data;											
						thefield.value=response.getElementsByTagName('val')[2].firstChild.data;
					} else {
						if(autofill[basename]["bo"])
							theitem.value="";
						thefield.value="";
					}


				}
				autofill[basename]["vl"]=theitem.value;
				if(thefield.onchange) thefield.onchange();
			}
			

			function blurAutofill(basename){
				var thedisplay=getObjectFromID("ds-"+basename);

				var thediv=getObjectFromID("dd-"+basename);
				if(thediv){
					if(thediv.hasfocus){
						// this means the focus is in the dropdown
						setTimeout("removeDropDown(\""+basename+"\")",1000);
					} else
					removeDropDown(basename);
				} 

				if(autofill[basename]["vl"]!=thedisplay.value)
					lastLookup(thedisplay);
				return true;
			}


			function removeDropDown(basename){
				var thediv=getObjectFromID("dd-"+basename);
				if(thediv)
					document.body.removeChild(thediv);
				displaySelectBoxes();
			}

			function createDropDown(basename,textfield,displays,extras){
				
				//Let's seet if the div's already there
				
				var thediv=getObjectFromID("dd-"+basename);
				if(!thediv){
					//get coordinates of textfield to place box underneath
					var thetop=getTop(textfield)+textfield.offsetHeight;
					var theleft=getLeft(textfield);
					
					thediv=document.createElement("div");
					thediv.id="dd-"+basename
					thediv.className="AFDropDowns";
					thediv.style.top=thetop + "px";
					thediv.style.left=theleft + "px";
					thediv.hasfocus=false;
					thediv.onmouseover=function(){this.hasfocus=true;}
					thediv.onmouseout=function(){this.hasfocus=false;}
					hideSelectBoxes();
					document.body.appendChild(thediv);
				}				
				
				var i;
				while (thediv.childNodes[0]) 
					thediv.removeChild(thediv.childNodes[0]);
					
				var theUL=document.createElement("ul");
				var theLI, theA, theInner;
				for(i=0; i<displays.length;i++){
					theLI=document.createElement("li");
					theA=document.createElement("a");
					theA.href=displays[i];
					theA.onclick=function(){dropDownItemClick(this);return false;}
					theA.onmouseover=function(){dropDownItemOver(this)}
					theInner=displays[i].htmlEntities();
					if(extras[i])
						theInner+="<br /><span>"+extras[i].htmlEntities()+"</span>"
					theA.innerHTML=theInner;

					theLI.appendChild(theA)
					theUL.appendChild(theLI)
				}
				thediv.appendChild(theUL);
				
			}//end function


			function highlightListItem(theLI){				
				var theUL=theLI.parentNode;
				var thediv=theUL.parentNode;
				var thedisplay=getObjectFromID("ds-"+thediv.id.substring(3));

				var i;
				for(i=0;i<theUL.childNodes.length;i++) 
					theUL.childNodes[i].firstChild.className="";
				theLI.firstChild.className="AFDropDownItems";
				thedisplay.value=theLI.firstChild.firstChild.data;
			}
		
			function moveHighlight(thediv,direction){
				var thedisplay=getObjectFromID("ds-"+thediv.id.substring(3));				
				var theUL=thediv.childNodes[0];
				var highlightnumber=-1;
				var dnum;
				if(direction=="up") dnum=-1; else dnum=1;
				
				for(i=0;i<theUL.childNodes.length;i++)
					if(theUL.childNodes[i].firstChild.className=="AFDropDownItems")
						highlightnumber=i+dnum;
						
				if(highlightnumber==-1 && direction=="dn") highlightnumber=0;
				if(highlightnumber>-1 && highlightnumber<theUL.childNodes.length)
					highlightListItem(theUL.childNodes[highlightnumber]);
	
			};
			
			function dropDownItemOver(theitem){
				var theUL=theitem.parentNode.parentNode;
				for(i=0;i<theUL.childNodes.length;i++) 
					theUL.childNodes[i].firstChild.className="";				
			}
			
			function dropDownItemClick(theitem){
				var theUL=theitem.parentNode.parentNode;
				var thediv=theUL.parentNode;
				var basename=thediv.id.substring(3);
				var thedisplay=getObjectFromID("ds-"+basename);
				thedisplay.value=theitem.firstChild.data;
				thediv.hasfocus=false;
				blurAutofill(basename)
			}
			
autofill=new Array();