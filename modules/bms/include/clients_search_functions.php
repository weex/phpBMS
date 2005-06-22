<?php
//=============================================
//functions
//=============================================

function mark_asclient($theids){
	//passed variable is array of user ids to be revoked
	$whereclause="";
	foreach($theids as $theid){
		$whereclause.=" or clients.id=".$theid;
	}
	$whereclause=substr($whereclause,3);
	
	$thequery = "update clients set clients.type=\"client\" where (".$whereclause.");";
	$theresult = mysql_query($thequery);
	if (!$theresult) die ("Couldn't convert to clients: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");		
}


//Stamp Comments Field with info packet sent
function stamp_infosent($theids){
	//passed variable is array of user ids to be revoked
	$whereclause="";
	foreach($theids as $theid){
		$whereclause.=" or clients.id=".$theid;
	}
	$whereclause=substr($whereclause,3);		
	$thequery = "update clients set comments=concat(\"Information Packet Sent\",char(10),comments), modifiedby=".$_SESSION["userinfo"]["id"]." where ".$whereclause.";";
	$theresult = mysql_query($thequery);
	if (!$theresult) die ("Couldn't Update: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");		
}

//inactivate
function delete_record($theids){
	//passed variable is array of user ids to be revoked
	$whereclause="";
	foreach($theids as $theid){
		$whereclause.=" or clients.id=".$theid;
	}
	$whereclause=substr($whereclause,3);		
	$thequery = "update clients set inactive=1,modifiedby=".$_SESSION["userinfo"]["id"]." where ".$whereclause.";";
	$theresult = mysql_query($thequery);
	if (!$theresult) die ("Couldn't Update: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");		
}

//remove prospects
function delete_prospects($theids){
	//passed variable is array of user ids to be revoked
	$whereclause="";
	foreach($theids as $theid){
		$whereclause.=" or clients.id=".$theid;
	}
	$whereclause=substr($whereclause,3);		
	$thequery = "DELETE FROM clients where (".$whereclause.") and type=\"prospect\";";
	$theresult = mysql_query($thequery);
	if (!$theresult) die ("Couldn't Delete: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");		
}

function massEmail($theids){
	$_SESSION["emailids"]=$theids;
	header("Location: modules/bms/clients_email.php");
}

?>