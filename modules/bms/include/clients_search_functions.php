<?php
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

	//passed variable is array of user ids to be revoked
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