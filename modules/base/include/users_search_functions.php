<?php
include("modules/base/include/admin_functions.php");
//=============================================
//functions
//=============================================
//revoke access
function delete_record($theids){
	//passed variable is array of user ids to be revoked
	foreach($theids as $theid){
		$whereclause=$whereclause." or users.id=".$theid;
	}
	$whereclause=substr($whereclause,3);		
	$thequery = "update users set revoked=1,modifiedby=".$_SESSION["userinfo"]["id"]." where ".$whereclause.";";
	$theresult = mysql_query($thequery);
	if (!$theresult) die ("Couldn't Update: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");		
}



//Need to set this so that we can include tabs in the header for this one.
global $has_header;
$has_header=true;

function display_header(){
	admin_tabs("Users");
};
?>