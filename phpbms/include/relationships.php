<?php
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

//Perform the relationship lookup to construct join clause
function perform_relationship($relationshipid,$theids){
	global $db;
	
	$_SESSION["passedjoinclause"]="";
	$_SESSION["passedjoinwhere"]="";
	
	$querystatement="SELECT 
		fromtable.maintable as fromtable, relationships.totableid, totable.maintable as totable, 
		relationships.tofield, relationships.fromfield, relationships.inherint
		FROM
		(relationships inner join tabledefs as fromtable on relationships.fromtableid=fromtable.id) 
		inner join tabledefs as totable on relationships.totableid=totable.id	
		WHERE relationships.id=".$relationshipid;
	$queryresult=$db->query($querystatement);

	$therelationship=$db->fetchArray($queryresult);
	
	//if the relationship is inherent (already exists due to the display nature of the table)
	// then the join clause is not needed
	if($therelationship["inherint"]=="0"){
		// build the join clause
		$_SESSION["passedjoinclause"]=" INNER JOIN ".$therelationship["fromtable"]." on ".$therelationship["totable"].".";
		$_SESSION["passedjoinclause"].=$therelationship["tofield"]."=".$therelationship["fromtable"].".".$therelationship["fromfield"];
	}
	
	// make the passed where clause for passed id's	 this will pass the saved where clause.
	foreach($theids as $theid){
		$_SESSION["passedjoinwhere"].=" or ".$therelationship["fromtable"].".id=".$theid;
	}
	$_SESSION["passedjoinwhere"]=substr($_SESSION["passedjoinwhere"],3);

	return "search.php?id=".$therelationship["totableid"];
}
?>