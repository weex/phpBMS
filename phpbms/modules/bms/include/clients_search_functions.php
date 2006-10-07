<?php
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

//=============================================
//functions
//=============================================

function mark_asclient($theids){
	global $dblink;

	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"clients.id");	

	$querystatement = "UPDATE clients SET clients.type=\"client\",modifiedby=\"".$_SESSION["userinfo"]["id"]."\" WHERE (".$whereclause.");";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Couldn't Mark as Client: ".mysql_error($dblink)." -- ".$querystatement);		
	
	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" converted to client.";
	return $message;
}


//Stamp Comments Field with info packet sent
function stamp_infosent($theids){
	global $dblink;

	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"clients.id");

	$querystatement = "update clients set comments=concat(\"Information Packet Sent\",char(10),comments), modifiedby=".$_SESSION["userinfo"]["id"]." where ".$whereclause.";";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Couldn't Mark Info Packect: ".mysql_error($dblink)." -- ".$querystatement);		
	
	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" marked as info packet sent.";
	return $message;
}

//inactivate
function delete_record($theids){
	global $dblink;

	$whereclause=buildWhereClause($theids,"clients.id");

	$querystatement = "UPDATE clients SET inactive=1,modifiedby=".$_SESSION["userinfo"]["id"]." WHERE ".$whereclause.";";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Couldn't Mark Inactive : ".mysql_error($dblink)." -- ".$querystatement);		
	
	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" marked inactive.";
	return $message;
}

//remove prospects
function delete_prospects($theids){
	global $dblink;

	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"clients.id");
	
	$querystatement = "DELETE FROM clients where (".$whereclause.") and type=\"prospect\";";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Couldn't Run Prospect Delete : ".mysql_error($dblink)." -- ".$querystatement);		

	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" deleted.";
	return $message;	
}

function massEmail($theids){
	$_SESSION["emailids"]=$theids;
	header("Location: modules/bms/clients_email.php");
}

?>