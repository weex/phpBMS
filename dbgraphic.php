<?php 
	require_once("include/session.php");
	if(!isset($_GET["t"]) or !isset($_GET["r"]) or !isset($_GET["f"]) or !isset($_GET["mf"])) die("Invlaid Paramateers Set");
	
	$querystatement="SELECT ".$_GET["f"].",".$_GET["mf"]." FROM ".$_GET["t"]." WHERE id=".$_GET["r"];
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) die("bad query".$querystatement);
	if(mysql_num_rows($queryresult)){
		$therecord=mysql_fetch_array($queryresult);
		header('Content-type: '.$therecord[$_GET["mf"]]);
	
		echo $therecord[$_GET["f"]];
	}
?>