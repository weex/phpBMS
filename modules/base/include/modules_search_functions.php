<?php
include("modules/base/include/admin_functions.php");

//=============================================
//functions
//=============================================

//delete reports
function delete_record($theids){
	//null function modules must be deleted from a uninstall script
}


//Need to set this so that we can include tabs in the header for this one.
global $has_header;
$has_header=true;
function display_header(){
	admin_tabs("Modules");
};
?>