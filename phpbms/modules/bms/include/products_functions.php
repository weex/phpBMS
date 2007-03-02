<?php
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

//format tabs for products.
function product_tabs($selected="none",$id=0) {
	global $dblink;
	
	$querystatement="select id from notes where 
						attachedtabledefid=4 and attachedid=".$id;
	$thequery=mysql_query($querystatement,$dblink);
	$thequery? $numrows=mysql_num_rows($thequery): $numrows=0;

	$querystatement="SELECT id FROM attachments where 
						tabledefid=4 and recordid=".$id;
	$queryresult=mysql_query($querystatement,$dblink);
	$queryresult? $numfilerows=mysql_num_rows($queryresult): $numfilerows=0;

	$thetabs=array(
		array(
			"name"=>"General",
			"href"=>($id)?"products_addedit.php?id=".$id:"products_addedit.php"
		),
		array(
			"name"=>"Prerequisites",
			"href"=>(($id)?"products_prereq.php?id=".$id:"N/A"),
			"disabled"=>(($id)?false:true)
		)
	);
	
	if(hasRights(30)){
		array_push($thetabs,array(
			"name"=>"Sales History",
			"href"=>(($id)?"products_saleshistory.php?id=".$id:"N/A"),
			"disabled"=>(($id)?false:true)
		));}
		array_push($thetabs,array(
			"name"=>"Attachments",
			"href"=>(($id)?"products_attachments.php?refid=".$id:"N/A"),
			"disabled"=>(($id)?false:true),
			"notify"=>($numfilerows?true:false)
		));
		array_push($thetabs,array(
			"name"=>"Notes/Tasks/Events",
			"href"=>(($id)?"products_notes.php?refid=".$id:"N/A"),
			"disabled"=>(($id)?false:true),
			"notify"=>($numrows?true:false)
		));
	displayTabs($thetabs,$selected);
}
?>