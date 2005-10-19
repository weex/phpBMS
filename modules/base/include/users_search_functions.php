<?php
include("modules/base/include/admin_functions.php");
//=============================================
//functions
//=============================================
//revoke access
function delete_record($theids){
	global $dblink;
	
	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"users.id");

	$querystatement = "update users set revoked=1,modifiedby=".$_SESSION["userinfo"]["id"]." where ".$whereclause.";";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Couldn't revoke access: ".mysql_error($dblink)." -- ".$querystatement);		

	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" revoked access.";
	return $message;	
}



//Need to set this so that we can include tabs in the header for this one.
global $has_header;
$has_header=true;

function display_header(){
	admin_tabs("Users");
};
?>