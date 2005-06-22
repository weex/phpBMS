<?php
//=============================================
//functions
//=============================================
//revoke access
function delete_record($theids){
	global $session_taxinfo;
	//passed variable is array of user ids to be revoked
	foreach($theids as $theid){
		$whereclause=$whereclause." or tax.id=".$theid;
	}
	$whereclause=substr($whereclause,3);
	
	$thequery = "select invoices.id from invoices inner join tax on invoices.taxareaid=tax.id where ".$whereclause.";";
	$theresult = mysql_query($thequery);
	$numrows=mysql_num_rows($theresult);
	
	if(!$numrows){
		$thequery = "delete from tax where ".$whereclause.";";
		$theresult = mysql_query($thequery);
	}
			
	if (!$theresult) die ("Couldn't delete: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");		
}


?>