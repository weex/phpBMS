<?php
//=============================================
//functions
//=============================================


//delete
function delete_record($theids){
	//passed variable is array of user ids to be revoked
	$whereclause="";
	foreach($theids as $theid){
		$whereclause.=" or clientemailprojects.id=".$theid;
	}
	$whereclause=substr($whereclause,3);		
	$thequery = "DELETE FROM clientemailprojects WHERE ".$whereclause.";";
	$theresult = mysql_query($thequery);
	if (!$theresult) die ("Couldn't Update: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");		
}


?>