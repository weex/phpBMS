<?php
//=============================================
//functions
//=============================================
//change status to discontiuned
function delete_record($theids){
	global $session_productinfo;
	//passed variable is array of user ids to be revoked
	foreach($theids as $theid){
		$whereclause=$whereclause." or products.id=".$theid;
	}
	$whereclause=substr($whereclause,3);
	
	$thequery = "update products set status=\"DISCONTINUED\" where ".$whereclause.";";
	$theresult = mysql_query($thequery);
	if (!$theresult) die ("Couldn't delete: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");		
}


?>