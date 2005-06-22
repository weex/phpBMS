<?php 
	
	$_POST["command"]="print";
	$_POST["reporttitle"]="Totals Grouped by Shipping Method";
	$_POST["showwhat"]="invoices";

	$_POST["showinvoices"]="1";
	$_POST["showlineitems"]="";

	$_POST["columnnamelist"]="Invoice Total:::Shipping";
	$_POST["columnvaluelist"]=addslashes("concat('$',format(sum(invoices.totalti),2)):::concat('$',format(sum(invoices.tax),2))");
	
	$_POST["groupingnamelist"]="Shipping Method";
	$_POST["groupingvaluelist"]="invoices.shippingmethod";

	require("invoices_totals.php");
?>