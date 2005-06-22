<?php
include("modules/base/include/admin_functions.php");

//=============================================
//functions
//=============================================
//revoke access
function delete_record($theids){
	global $dblink;
	
	//passed variable is array of user ids to be revoked
	$verifywhereclause="";
	$whereclause="";
	foreach($theids as $theid){
		$whereclause.=" or menu.id=".$theid;
		$verifywhereclause.=" or menu.parentid=".$theid;
	}
	$whereclause=substr($whereclause,3);		
	$verifywhereclause=substr($verifywhereclause,3);;
	
	$thequerystatement = "SELECT id FROM menu WHERE ".$verifywhereclause;
	$thequery = mysql_query($thequery,$dblink) or die(mysql_error().$thequerystatement);
	if (mysql_num_rows($thequery)){
		$thequerystatement = "DELETE FROM menu WHERE ".$whereclause;
		$thequery = mysql_query($thequery,$dblink) or die(mysql_error().$thequerystatement);
	}
	
}



//Need to set this so that we can include tabs in the header for this one.
global $has_header;
$has_header=true;
function display_header(){
	admin_tabs("Nav. Menu");
};
?>