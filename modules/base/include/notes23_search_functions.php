<?php
//=============================================
//functions
//=============================================

function mark_asread($theids){
	//passed variable is array of user ids to be revoked
	$whereclause="";
	foreach($theids as $theid){
		$whereclause.=" or notes.id=".$theid;
	}
	$whereclause=substr($whereclause,3);
	
	$thequery = "update notes set notes.beenread=1 where (".$whereclause.") and type!=\"System\";";
	$theresult = mysql_query($thequery);
	if (!$theresult) die ("Couldn't mark as read: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");		
}


//delete notes
function delete_record($theids){
	global $dblink;
	
	//passed variable is array of user ids to be revoked
	$whereclause="";
	foreach($theids as $theid){
		$whereclause.=" or id=".$theid;
	}
	$whereclause=substr($whereclause,3);		
	$querystatement = "delete from notes where (createdby=".$_SESSION["userinfo"]["id"]." or assignedtoid=".$_SESSION["userinfo"]["id"].") and (".$whereclause.");";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(1,"Couldn't Update: ".mysql_error()."<BR>\n SQL STATEMENT [".$querystatement."]");		
}

?>