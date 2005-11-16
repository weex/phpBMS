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

function init(){
	var acc1=getObjectFromID("accordianSec1");
	var acc2=getObjectFromID("accordianSec2");
	var acc3=getObjectFromID("accordianSec3");
	var theaccordian=getObjectFromID("accordian");
	var accordianContainter=getObjectFromID("accordianContainer");
	
	if(theaccordian.offsetHeight<accordianContainter.offsetHeight-10){
		var tempheight=theaccordian.offsetHeight-(acc1.offsetHeight+acc2.offsetHeight+acc3.offsetHeight);
		acc1.style.display="none";
		acc2.style.display="none";
		theaccordian.style.height=(accordianContainter.offsetHeight-6)+"px";
		accordianHeight=theaccordian.offsetHeight-tempheight;
	} else {
		accordianHeight=Math.max(acc1.offsetHeight,acc2.offsetHeight,acc3.offsetHeight);
		acc1.style.display="none";
		acc2.style.display="none";
	}	

}

function accordian(theimg,accordianName,numSlats){
	var clickedID=theimg.id.substr(theimg.id.length-1,1);
	var theDiv,theImg,openID;
	for(var i=1;i<=numSlats;i++){
		theDiv=getObjectFromID(accordianName+"Sec"+i);
		theImg=getObjectFromID(accordianName+"Img"+i);
		if(i==clickedID){
			theDiv.style.height="0px";
			theDiv.style.display="block";
		} else {
			if(theDiv.style.display=="block"){
				openID=i;
			}
		}
	}//end for
	switchAccordian(clickedID,openID,accordianName);
}
function switchAccordian(toID,fromID,accordianName){
	var fromDiv=getObjectFromID(accordianName+"Sec"+fromID);
	var toDiv=getObjectFromID(accordianName+"Sec"+toID);


	var step=Math.round(accordianHeight/8);
	if(toDiv.offsetHeight<=accordianHeight-step){
			fromDiv.style.height=Math.abs(fromDiv.offsetHeight-step)+"px";
			toDiv.style.height=Math.abs(toDiv.offsetHeight+step)+"px";
			setTimeout("switchAccordian("+toID+","+fromID+",'"+accordianName+"')",40);
	} else {
		toDiv.style.height=accordianHeight+"px";
		fromDiv.style.display="none";
		var toImg=getObjectFromID(accordianName+"Img"+toID);
		toImg.parentNode.style.display="none";
		var fromImg=getObjectFromID(accordianName+"Img"+fromID);
		fromImg.parentNode.style.display="block";
	}
}

function hideSection(theimg,sectionid){
	var thediv=getObjectFromID(sectionid);
	if (thediv.style.display=="none"){
		thediv.style.display="block";
		theimg.src=chevronup.src;
	}
	else {
		thediv.style.display="none";
		theimg.src=chevrondown.src;
	}
}

function showContent(id){
	thediv=getObjectFromID("SMT"+id);
	thegraphic=getObjectFromID("SMG"+id);
	if (thediv){
		if(thediv.style.display=="block"){
			thediv.style.display="none";
			thegraphic.src="../../common/image/left_arrow.gif";
		} else {
			thediv.style.display="block";
			thegraphic.src="../../common/image/down_arrow.gif";
		}
	}
}

function checkTask(id,type){
	var thediv=getObjectFromID("TS"+id);
	var thecheckbox=getObjectFromID("TSC"+id);
	var isprivate=getObjectFromID("TSprivate"+id);
	var ispastdue=getObjectFromID("TSispastdue"+id);
	
	var theURL="snapshot_ajax.php?id="+id+"&ty="+type+"&cm=updateTask&cp=";
	if(thecheckbox.checked){
		theURL+="1";
		thediv.className="small taskCompleted";
	} else {
		theURL+="0";
		var classname=" small task";
		if(isprivate.value==1)
			classname+=" taskPrivate";
		if(ispastdue.value==1)
			classname="small taskPastDue";
		thediv.className=classname;
	}
	loadXMLDoc(theURL,null,false);
	if(req.responseText!="success")
		alert("Error: <br>"+req.responseText);	
	
}