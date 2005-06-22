<?php 
	
	$_POST["command"]="print";
	$_POST["reporttitle"]="Subtotals, Totals, and Amount Due";
	$_POST["showwhat"]="invoices";

	$_POST["showinvoices"]="1";
	$_POST["showlineitems"]="";

	$_POST["columnnamelist"]="Amt. Due:::Invoice Total:::Subtotal";
	$_POST["columnvaluelist"]=addslashes("concat('$',format(sum(invoices.totalti-invoices.amountpaid),2)):::concat('$',format(sum(invoices.totalti),2)):::concat('$',format(sum(invoices.totaltni),2))");
	
	$_POST["groupingnamelist"]="";
	$_POST["groupingvaluelist"]="";

	require("invoices_totals.php");
?>