<?php
include("modules/base/include/admin_functions.php");

//=============================================
//functions
//=============================================
function delete_record($theids){
	global $dblink;

	//passed variable is array of user ids to be revoked
	$whereclause="";
	$linkedwhereclause="";
	$relationshipswhereclause="";
	foreach($theids as $theid){
		$whereclause.=" or tabledefs.id=".$theid;
		$linkedwhereclause.=" or tabledefid=".$theid;
		$relationshipswhereclause.=" or fromtableid=".$theid. " or totableid=".$theid;
	}
	$whereclause=substr($whereclause,3);		
	$linkedwhereclause=substr($linkedwhereclause,3);		
	$relationshipswhereclause=substr($relationshipswhereclause,3);		

	$thequery = "DELETE FROM tabledefs WHERE ".$whereclause.";";
	$theresult = mysql_query($thequery,$dblink) or die ("Couldn't Delete: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");

	$thequery = "DELETE FROM tablecolumns WHERE ".$linkedwhereclause.";";
	$theresult = mysql_query($thequery,$dblink) or die ("Couldn't Delete: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");

	$thequery = "DELETE FROM tablefindoptions WHERE ".$linkedwhereclause.";";
	$theresult = mysql_query($thequery,$dblink) or die ("Couldn't Delete: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");

	$thequery = "DELETE FROM tableoptions WHERE ".$linkedwhereclause.";";
	$theresult = mysql_query($thequery,$dblink) or die ("Couldn't Delete: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");

	$thequery = "DELETE FROM tablesearchablefields WHERE ".$linkedwhereclause.";";
	$theresult = mysql_query($thequery,$dblink) or die ("Couldn't Delete: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");

	$thequery = "DELETE FROM usersearches WHERE ".$linkedwhereclause.";";
	$theresult = mysql_query($thequery,$dblink) or die ("Couldn't Delete: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");

	$thequery = "DELETE FROM relationships WHERE ".$relationshipswhereclause.";";
	$theresult = mysql_query($thequery,$dblink) or die ("Couldn't Delete: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");
}

//Need to set this so that we can include tabs in the header for this one.
global $has_header;
$has_header=true;
function display_header(){
	admin_tabs("Tables");
	echo "<table width='100%' cellspacing=0 cellpadding=0 class='untabbedbox' style='border-bottom:0px;padding-top:3px;'><tr><td>";
	admin_table_tabs("Table Definitions");
	echo "</td></tr></table>";	
};
?>