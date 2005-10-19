<?php
//=============================================
//functions
//=============================================
if($_SESSION["userinfo"]["accesslevel"]<90) header("Location: ".$_SESSION["app_path"]."noaccess.html");

//delete
function delete_record($theids){
	global $dblink;

	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"clientemailprojects.id");

	$querystatement = "DELETE FROM clientemailprojects WHERE ".$whereclause.";";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Couldn't Delete: ".mysql_error($dblink)." -- ".$querystatement);		

	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" deleted.";
	return $message;
}


?>