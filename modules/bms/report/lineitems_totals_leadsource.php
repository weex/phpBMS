<?php 
	
	$_POST["command"]="print";
	$_POST["reporttitle"]="Totals Grouped by Invoice Lead Source and Product";
	$_POST["showwhat"]="line items";

	$_POST["showlineitems"]="0";

	$_POST["columnnamelist"]="Extended Price:::Unit Price (average):::Extended Cost:::Unit Cost (average):::Quantity";
	$_POST["columnvaluelist"]=addslashes("concat('$',format(sum(lineitems.unitprice*lineitems.quantity),2)):::concat('$',format(avg(lineitems.unitprice),2)):::concat('$',format(sum(lineitems.unitcost*lineitems.quantity),2)):::concat('$',format(avg(lineitems.unitcost),2)):::format(sum(lineitems.quantity),2)");
	
	$_POST["groupingnamelist"]="Lead Source:::Product";
	$_POST["groupingvaluelist"]="invoices.leadsource:::concat(products.partnumber,' - ',products.partname)";

	require("lineitems_totals.php");
?>