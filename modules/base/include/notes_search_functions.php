<?php
//=============================================
//functions
//=============================================

function mark_asread($theids){
	global $dblink;

	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"notes.id");
	
	$querystatement = "UPDATE notes SET notes.completed=1 WHERE (".$whereclause.") AND type!=\"SY\";";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Couldn't Mark as Read/Completed: ".mysql_error($dblink)." -- ".$querystatement);		
	
	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" marked as completed/read.";
	return $message;
}


//delete notes
function delete_record($theids){
	global $dblink;
	
	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"notes.id");

	$querystatement = "DELETE FROM notes WHERE (notes.createdby=".$_SESSION["userinfo"]["id"]." or notes.assignedtoid=".$_SESSION["userinfo"]["id"].") and (".$whereclause.") and (notes.repeat!=1);";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Couldn't Delete: ".mysql_error($dblink)."<BR>\n SQL STATEMENT [".$querystatement."]");		

	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" deleted";
	return $message;

}

?>