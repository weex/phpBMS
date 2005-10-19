<?php
include("modules/base/include/admin_functions.php");
//Need to set this so that we can include tabs in the header for this one.
global $has_header;
$has_header=true;
function display_header(){
	admin_tabs("Nav. Menu");
};

//=============================================
//functions
//=============================================
function delete_record($theids){
	global $dblink;
	
	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"notes.id");
	$verifywhereclause=buildWhereClause($theids,"menu.parentid.id");
	
	$querystatement = "SELECT id FROM menu WHERE ".$verifywhereclause;
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Verify Children Failed: ".mysql_error($dblink)." -- ".$querystatement);
	if (!mysql_num_rows($queryresult)){
		$querystatement = "DELETE FROM menu WHERE ".$whereclause;
		$queryresult = mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(300,"Delete Failed: ".mysql_error($dblink)." -- ".$querystatement);

		$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	} else
		$message=buildStatusMessage(0,count($theids));
	$message.=" deleted.";
	return $message;
}
?>