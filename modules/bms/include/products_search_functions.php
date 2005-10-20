<?php
//=============================================
//functions
//=============================================
//change status to discontiuned
function delete_record($theids){
	global $dblink;

	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"products.id");
	
	$querystatement = "UPDATE products SET inactive=1 WHERE ".$whereclause.";";
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Update Failed: ".mysql_error($dblink)." -- ".$querystatement);

	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" marked inactive.";
	return $message;
}
?>