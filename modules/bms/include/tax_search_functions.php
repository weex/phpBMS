<?php
//=============================================
//functions
//=============================================
//revoke access
function delete_record($theids){
	global $dblink;

	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"tax.id");
	
	$querystatement = "select invoices.id from invoices inner join tax on invoices.taxareaid=tax.id where ".$whereclause.";";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Couldn't Retrieve Tax Information: ".mysql_error($dblink)." -- ".$querystatement);		
	$numrows=mysql_num_rows($queryresult);
	
	if(!$numrows){
		$querystatement = "delete from tax where ".$whereclause.";";
		$queryresult = mysql_query($querystatement,$dblink);
		if (!$queryresult) reportError(300,"Couldn't Delete: ".mysql_error($dblink)." -- ".$querystatement);		
		
		$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	} else
		$message=buildStatusMessage(0,count($theids));
	$message.=" deleted.";
	return $message;			
}

?>