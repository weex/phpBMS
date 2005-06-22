<?php 
	
	$_POST["command"]="print";
	$_POST["reporttitle"]="Totals Grouped by Client Account Manager";
	$_POST["showwhat"]="invoices";

	$_POST["showinvoices"]="1";
	$_POST["showlineitems"]="";

	$_POST["columnnamelist"]="Amt. Due:::Invoice Total:::Subtotal";
	$_POST["columnvaluelist"]=addslashes("concat('$',format(sum(invoices.totalti-invoices.amountpaid),2)):::concat('$',format(sum(invoices.totalti),2)):::concat('$',format(sum(invoices.totaltni),2))");
	
	$_POST["groupingnamelist"]="Client Account Manager";
	$_POST["groupingvaluelist"]="concat(users2.firstname,' ',users2.lastname)";

	require("invoices_totals.php");
?>