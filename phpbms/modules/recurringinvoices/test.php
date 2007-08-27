<?php 
	include("../../include/session.php");

	$dateArray = localtime(mktime(),true);
	$tempDate = mktime(0,0,0,$dateArray["tm_mon"]+1,1,$dateArray["tm_year"]+1900);
	$tempDate = strtotime("+3 days");
	
	echo "<pre>";
	var_dump($dateArray);
	echo "</pre>";
	
	echo "<pre>";
	var_dump(strftime("%Y-%m-%d",$tempDate));
	echo "</pre>";
?>