<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("include/products_functions.php");

	require_once("../../include/search_class.php");
	require_once("../../include/common_functions.php");	

	//set the table passing stuff
	$reftableid=4;
  	$whereclause="attachedtabledefid=\"".$reftableid."\" and attachedid=".$_GET["refid"];
	$backurl="../bms/products_notes.php";
	$base="../../";

	$refquery="select partnumber,partname from products where id=".$_GET["refid"];
	$refquery=mysql_query($refquery,$dblink);
	$refrecord=mysql_fetch_array($refquery);	
	
	$pageTitle="Product: ".$refrecord["partname"].": Notes/Messages";

	function doTabs(){
		product_tabs("Notes/Messages",$_GET["refid"]);
	}
	
	include("../base/notes_records.php");
	//===================================================================================
	//==  THAT's IT                                                                    ==
	//===================================================================================
	
?>