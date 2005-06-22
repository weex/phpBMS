<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("include/clients_functions.php");

	require_once("../../include/search_class.php");
	require_once("../../include/common_functions.php");	

	//set the table passing stuff
	$reftableid=2;
  	$whereclause="attachedtabledefid=\"".$reftableid."\" and attachedid=".$_GET["refid"];
	$backurl="../bms/clients_notes.php";
	$base="../../";

	$refquery="select firstname,lastname,company from clients where id=".$_GET["refid"];
	$refquery=mysql_query($refquery,$dblink);
	$refrecord=mysql_fetch_array($refquery);
	
	if($refrecord["company"]=="")
		$pageTitle="Client: ".$refrecord["firstname"]." ".$refrecord["lastname"]." : Notes";
	else
		$pageTitle="Client: ".$refrecord["company"]." : Notes";


	function doTabs(){
		client_tabs("Notes",$_GET["refid"]);	
	}
	
	include("../base/notes_records.php");
	//===================================================================================
	//==  THAT's IT                                                                    ==
	//===================================================================================
	
?>