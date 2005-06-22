<?php
//=============================================
//functions
//=============================================


//inactivate
function delete_record($theids){
	//passed variable is array of user ids to be revoked
	foreach($theids as $theid){
		$whereclause=$whereclause." or id=".$theid;
	}
	$whereclause=substr($whereclause,3);		
	$thequery = "update clients set inactive=1,modifiedby=".$_SESSION["userinfo"]["id"]." where ".$whereclause.";";
	$theresult = mysql_query($thequery);
	if (!$theresult) die ("Couldn't Update: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");		
}

//remove prospects
function delete_prospects($theids){
	global $session_dupclientinfo;
	//passed variable is array of user ids to be revoked
	foreach($theids as $theid){
		$whereclause=$whereclause." or id=".$theid;
	}
	$whereclause=substr($whereclause,3);		
	$thequery = "DELETE FROM clients where (".$whereclause.") and type=\"prospect\";";
	$theresult = mysql_query($thequery);
	if (!$theresult) die ("Couldn't Delete: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");		
}


?>