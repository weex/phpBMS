<?PHP
	include("../../include/session.php");
	include("../../include/fields.php");
$id=-2;
if(isset($_POST["id"])) if($_POST["id"]) $id=$_POST["id"];
if(isset($_GET["id"])) if($_GET["id"]) $id=$_GET["id"];

if(isset($_POST["command"])){
	switch($_POST["command"]){
		case"delete":
			$querystatement="delete from templineitems where id=".$_POST["deleteid"]." and sessionid=\"".session_id()."\"";
			$queryresult=mysql_query($querystatement,$dblink);
			if(!$queryresult) reportError(500,("SQL Error: ".mysql_error($dblink)."<BR><BR>".$querystatement));
		break;
		case"add":
			if ($_POST["memo"]!="Prerequisite not met") {
				$price=ereg_replace("\\\$|,","",$price);
				$querystatement="INSERT INTO templineitems (invoiceid,productid,unitprice,quantity,unitcost,unitweight,memo,sessionid)
							VALUES(".$_POST["id"].",\"".$_POST["partnumber"]."\",".ereg_replace("\\\$|,","",$_POST["price"]).",".$_POST["qty"].",".$_POST["unitcost"].",".$_POST["unitweight"].",\"".$_POST["memo"]."\",\"".session_id()."\")";
				$queryresult=mysql_query($querystatement,$dblink); 
				if(!$queryresult) reportError(500,("SQL Error: ".mysql_error($dblink)."<BR><BR>".$querystatement));
				}// end if
		break;
	}//end switch
}//end if

  	$querystatement="select products.partnumber as partnumber, products.partname as partname,
				templineitems.id as id, templineitems.quantity as quantity, concat(\"\$\",format(templineitems.unitprice,2)) as unitprice, templineitems.unitprice as numprice,
				templineitems.unitcost as unitcost, templineitems.unitweight as unitweight, templineitems.memo as memo,
				concat(\"\$\",format((templineitems.unitprice*templineitems.quantity),2)) as extended 
				from templineitems left join products on templineitems.productid=products.id where templineitems.invoiceid=".$id." and templineitems.sessionid=\"".session_id()."\" 
				and sessionid=\"".session_id()."\";";
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,("Error Retreiving Line Items ".$querystatement." - ".mysql_error($dblink)));
	$subtotal=0;
	$totalweight=0;
	$totalcost=0;


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Line Items</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../common/javascript/common.js"></script>
<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js"></script>
<script language="JavaScript" src="javascript/invoice.js"></script>
<style>
	.invoicelinetiems td{
		padding-left:2px;
		padding-right:2px;
		border-right:1px solid #CCCCCC;
		border-bottom:1px solid #CCCCCC;
	}
</style>
</head>
<body style="margin:0px;padding:0px;">
<form name="record" method="post" action="invoices_lineitems.php" style="margin:0px;padding:0px;">
<input name="id" type="hidden" value="<?PHP echo $id ?>">
<input id="deleteid" name="deleteid" type="hidden" value="0">
<input name="unitweight" type="hidden" value="0">
<input name="unitcost" type="hidden" value="0">
<input id="command" name="command" type="hidden" value="0">

  <input name="subtotal" type="hidden" value="<?PHP echo $subtotal?>">
  <input name="totalweight" type="hidden" value="<?PHP echo $totalweight?>">
  <input name="totalcost" type="hidden" value="<?PHP echo $totalcost?>">
  <script language="JavaScript">
  	parentform=parent.document.forms["record"];
	myform=document.forms["record"]
	parentform["totalweight"].value=myform["totalweight"].value;
	parentform["totalcost"].value=myform["totalcost"].value;
	parentform["totaltni"].value=myform["subtotal"].value;
	parentform["totalti"].onchange();
  </script>
</form>
</body>
</html>
