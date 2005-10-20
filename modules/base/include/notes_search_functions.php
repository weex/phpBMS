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

	//we need to check for incomplete repeatable child tasks
	$querystatement="SELECT distinct notes.parentid FROM notes where notes.parentid is not null and notes.completed=0 and (".$whereclause.")";
	$repeatqueryresult = mysql_query($querystatement,$dblink);
	if (!$repeatqueryresult) reportError(300,"Couldn't Delete: ".mysql_error($dblink)."<BR>\n SQL STATEMENT [".$querystatement."]");		

	$querystatement = "DELETE FROM notes WHERE (notes.createdby=".$_SESSION["userinfo"]["id"]." or notes.assignedtoid=".$_SESSION["userinfo"]["id"].") and (".$whereclause.") and (notes.repeat!=1);";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Couldn't Delete: ".mysql_error($dblink)."<BR>\n SQL STATEMENT [".$querystatement."]");		

	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" deleted";
	
	//repeat where applicable
	if (mysql_num_rows($repeatqueryresult)){
		require("modules/base/snapshot_ajax.php");

		while($therecord=mysql_fetch_array($repeatqueryresult)){
			$querystatement="SELECT id FROM notes WHERE completed=1 AND parentid=".$therecord["parentid"]." ORDER BY startdate DESC LIMIT 0,1";
			$queryresult = mysql_query($querystatement,$dblink);
			if(!$queryresult) reportError(300,("Error retrieving last child record".mysql_error($dblink)." ".$querystatement));
			if(mysql_num_rows($queryresult)){
				$completedrecord=mysql_fetch_array($queryresult);
				repeatTask($completedrecord["id"]);
			} else {
				repeatTask($therecord["parentid"]);
			}			
		}
	}
	
	return $message;

}

?>