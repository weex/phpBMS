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

function showHelp(base){
	var theURL=base+"help/index.php";
	loadXMLDoc(theURL,null,false);
	showModal(req.responseText,"phpBMS Help Resources",550);

	var helpToggles = document.getElementsByClassName("helpLinks");
	var helpStuff = document.getElementsByClassName("helpDivs");
	
	var helpFX = new fx.Accordion(helpToggles, helpStuff, {height:true, opacity:false, duration:400});
		helpFX.showThisHideOpen(helpStuff[0]);

}

function checkExpand(theitem){
	var i,tempDiv;
	var showid=theitem.id.substring(4);
	var doswitch=false;
	for(i=0;i<subMenuArray.length;i++){
		tempdiv=getObjectFromID("submenu"+subMenuArray[i]);
		if(tempdiv.style.display=="block" && subMenuArray[i]!=showid)
			doswitch=true;
	}
	if(doswitch)
		expandMenu(theitem);
	
}

function expandMenu(theitem){
	var i;
	var tempdiv;
	var showid=theitem.id.substring(4);
	var specificdiv=getObjectFromID("submenu"+showid);
	if(specificdiv.style.display)
		if(specificdiv.style.display=="block"){
			specificdiv.style.display="none";
			displaySelectBoxes();
			return false;
		}

	for(i=0;i<subMenuArray.length;i++){
		if(subMenuArray[i]!=showid){
			tempdiv=getObjectFromID("submenu"+subMenuArray[i]);
			tempdiv.style.display="none";
		}
	}
	var thetop=getTop(theitem);
	var theleft=getLeft(theitem);
	specificdiv.style.top=(thetop+theitem.offsetHeight)+"px";
	specificdiv.style.left=(theleft-6)+"px";
	specificdiv.style.display="block";
	hideSelectBoxes()
}