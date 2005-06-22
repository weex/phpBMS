<?php
include("modules/base/include/admin_functions.php");

//=============================================
//functions
//=============================================

//delete reports
function delete_record($theids){
	//passed variable is array of user ids to be revoked
	$whereclause="";
	foreach($theids as $theid){
		$whereclause.=" or reports.id=".$theid;
	}

	$whereclause=substr($whereclause,3);		
	$thequery = "delete from reports where ".$whereclause.";";
	$theresult = mysql_query($thequery);
	if (!$theresult) die ("Couldn't Update: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");		
}


//Need to set this so that we can include tabs in the header for this one.
global $has_header;
$has_header=true;
function display_header(){
	admin_tabs("Tables");
	echo "<table width='100%' cellspacing=0 cellpadding=0 class='untabbedbox' style='border-bottom:0px;padding-top:3px;'><tr><td>";
	admin_table_tabs("Reports");
	echo "</td></tr></table>";	
};
?>