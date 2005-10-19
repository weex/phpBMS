<?php
include("modules/base/include/admin_functions.php");

//=============================================
//functions
//=============================================
function delete_record($theids){
	global $dblink;
	
	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"relationships.id");
	$querystatement = "delete from relationships where ".$whereclause.";";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Couldn't Delete: ".mysql_error($dblink)." -- ".$querystatement);		
	
	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" deleted";
	return $message;
}


//Need to set this so that we can include tabs in the header for this one.
global $has_header;
$has_header=true;
function display_header(){
	admin_tabs("Tables");
	echo "<table width='100%' cellspacing=0 cellpadding=0 class='bodyline' style='border-bottom:0px;margin-bottom:0px;background-image:none;padding-top:3px;-moz-border-radius-bottomleft:0px;-moz-border-radius-bottomright:0px;'><tr><td>";
	admin_table_tabs("Relationships");
	echo "</td></tr></table>";	
};
?>