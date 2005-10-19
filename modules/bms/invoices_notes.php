<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("include/invoices_functions.php");

	require_once("../../include/search_class.php");
	require_once("../../include/common_functions.php");	

	//set the table passing stuff
	$reftableid=3;
  	$whereclause="attachedtabledefid=\"".$reftableid."\" and attachedid=".$_GET["refid"];
	$backurl="../bms/invoices_notes.php";
	$base="../../";

	$refquery="SELECT
			   invoices.id, if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as name from invoices inner join clients on invoices.clientid=clients.id where invoices.id=".$_GET["refid"];
	$refquery=mysql_query($refquery,$dblink);
	$refrecord=mysql_fetch_array($refquery);	
	
	$pageTitle="Order Notes: ".$refrecord["id"].", ".$refrecord["name"];

	function doTabs(){
		invoice_tabs("Notes/Messages",$_GET["refid"]);
	}
	
	include("../base/notes_records.php");
	//===================================================================================
	//==  THAT's IT                                                                    ==
	//===================================================================================
	
?>