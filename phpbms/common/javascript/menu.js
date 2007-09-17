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

menu = {
	
	subMenuArray: Array(),
	
	showHelp: function(){
		var theURL = APP_PATH + "help/index.php";

		loadXMLDoc(theURL,null,false);
		showModal(req.responseText,"phpBMS Help Resources",550);
	},//endmethod
	
	
	checkExpand: function(e){
		srcObj = e.src();
				
		var i,tempDiv;
		
		var showid = srcObj.id.substring(4);
		
		var doswitch = false;
		
		for(i=0;i<menu.subMenuArray.length;i++){
			tempdiv=getObjectFromID("submenu"+menu.subMenuArray[i]);
			if(tempdiv.style.display=="block" && menu.subMenuArray[i]!=showid)
				doswitch=true;
		}
		if(doswitch)
			menu.expandMenu(e);

	},//end method
	
	
	expandMenu: function(e){
		srcObj = e.src();
				
		var i;
		var tempdiv;
		var tempMenu;
		var showid = srcObj.id.substring(4);
		
		var specificdiv = getObjectFromID("submenu"+showid);

		if(specificdiv.style.display)
			if(specificdiv.style.display=="block"){
				specificdiv.style.display="none";
				srcObj.className = "topMenus";
				displaySelectBoxes();
				e.stop();				
				return false;
			}//endif
	
		for(i=0;i<menu.subMenuArray.length;i++){
			if(menu.subMenuArray[i]!=showid){
				tempdiv=getObjectFromID("submenu"+menu.subMenuArray[i]);
				tempdiv.style.display="none";
				
				tempMenu = getObjectFromID("menu"+menu.subMenuArray[i]);
				tempMenu.className = "topMenus";
			}//endif
		}//endfor
		
		var theMenuUL = getObjectFromID("menuBar")
		for(i=0;i<theMenuUL.childNodes.length;i++){
			if(theMenuUL.childNodes[i].tagName)
				if(theMenuUL.childNodes[i].tagName == "LI"){
					if(theMenuUL.childNodes[i].childNodes.length)
						if(theMenuUL.childNodes[i].childNodes[0].className == "hovered")
							theMenuUL.childNodes[i].childNodes[0].className = "";
				}
				
		}
		srcObj.className = "topMenus hovered";
		
		var thetop=getTop(srcObj);
		var theleft=getLeft(srcObj);
		specificdiv.style.top=(thetop+srcObj.offsetHeight)+"px";
		specificdiv.style.left=(theleft-6)+"px";
		specificdiv.style.display="block";
		hideSelectBoxes();

		e.stop();
	}//end method			
	
}//end class



/* OnLoad Listener --------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {
	var topMenus = getElementsByClassName('topMenus');
	
	var subMenus = getElementsByClassName('submenuitems');
	for(var i=0; i<subMenus.length; i++)
		menu.subMenuArray[menu.subMenuArray.length] = subMenus[i].id.substr(7);
	
	for(var i=0; i<topMenus.length; i++){
		
		connect(topMenus[i],"onmouseover",menu.checkExpand);
		connect(topMenus[i],"onclick",menu.expandMenu);
		
	}//endfor
})//end listner