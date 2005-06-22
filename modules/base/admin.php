<?php 
	require("../../include/session.php");
	include("include/admin_functions.php");
	
	if(!isset($_SESSION["admintab"])) $_SESSION["admintab"]="General";
	if(!isset($_SESSION["tabletab"])) $_SESSION["tabletab"]="Reports";
	
	foreach($admintabs as $theadmin){
		if ($theadmin["name"]==$_SESSION["admintab"]){
			if ($_SESSION["admintab"]=="Tables") {
				foreach($tabletabs as $thetable){
				 	if($thetable["name"]==$_SESSION["tabletab"]) $location=$thetable["href"];
				}
			} else $location=$theadmin["href"];
		}
	} //end foreach
	
	header("Location: ".$location);
?>