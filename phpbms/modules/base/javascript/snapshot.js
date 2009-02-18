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

theEvent = {

	idents: Array(),

	getWeek: function(e){

		var eventDate;

		if(e){
			var srcObj = e.src();

			for(var i=0; i<theEvent.idents.length; i++)
				disconnect(theEvent.idents[i]);

			switch(srcObj.id) {

				case "eventLastWeek":
					eventDate = getObjectFromID("eventDateLast").value;
					break;

				case "eventToday":
					eventDate = getObjectFromID("eventDateToday").value;
					break;

				case "eventNextWeek":
					eventDate = getObjectFromID("eventDateNext").value;
					break;

			}//endswitch
		}//endif

		var theURL = "snapshot_ajax.php?cm=getWeek";
		if(eventDate)
			theURL += "&d="+eventDate;

		var weekContainer = getObjectFromID("eventsBox");
		loadXMLDoc(theURL,null,false);
		weekContainer.innerHTML = req.responseText;

		theEvent.idents[theEvent.idents.length] = connect(getObjectFromID("eventLastWeek"),"onclick",theEvent.getWeek);
		theEvent.idents[theEvent.idents.length] = connect(getObjectFromID("eventToday"),"onclick",theEvent.getWeek);
		theEvent.idents[theEvent.idents.length] = connect(getObjectFromID("eventNextWeek"),"onclick",theEvent.getWeek);

	}//end method

}//end class


task = {

	check: function(e){
		var srcObj = e.src();

		var id = srcObj.id.substr(5);
		var type =  srcObj.id.substr(2,2);
		var section = srcObj.id.substr(0,2);

		var checkBox = srcObj;
		var containerP = srcObj.parentNode;

		var theURL = "snapshot_ajax.php?id="+id+"&ty="+type+"&cm=updateTask&cp=";

		if(checkBox.checked){

			theURL += 1;

			containerP.className += " complete";

		} else {

			theURL += 0;

			containerP.className = containerP.className.replace(/complete/g, "");

		}//end if

		loadXMLDoc(theURL,null,false);

	}//end method
}//end class


/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {
	//SystemMessage Accordian
	//we define two arrays, containing our toggles and divs.
	var taskChecks = getElementsByClassName('taskChecks');
	for(var i=0; i<taskChecks.length; i++)
		connect(taskChecks[i],"onclick",task.check);

	var systemMessageDivs = getElementsByClassName('systemMessages');
	var systemMessageLinks = getElementsByClassName('systemMessageLinks');

	var taskDivs = getElementsByClassName('tasksDivs');
	var taskLinks = getElementsByClassName('tasksLinks');

	var systemMessageAccordion = new fx.Accordion(systemMessageLinks, systemMessageDivs, {opacity: true, duration:150});
	var taskAccordion = new fx.Accordion(taskLinks, taskDivs, {opacity: true, duration:300});
	taskAccordion.showThisHideOpen(taskDivs[1]);

	theEvent.getWeek();
});
