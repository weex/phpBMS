<?PHP
	include("../../include/session.php");
	include("../../include/fields.php");
$id=-2;
if(isset($_POST["id"])) if($_POST["id"]) $id=$_POST["id"];
if(isset($_GET["id"])) if($_GET["id"]) $id=$_GET["id"];

if(isset($_POST["command"])){
	switch($_POST["command"]){
		case"del":
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

  	$thequery="select products.partnumber as partnumber, products.partname as partname,
				templineitems.id as id, templineitems.quantity as quantity, concat(\"\$\",format(templineitems.unitprice,2)) as unitprice, templineitems.unitprice as numprice,
				templineitems.unitcost as unitcost, templineitems.unitweight as unitweight, templineitems.memo as memo,
				concat(\"\$\",format((templineitems.unitprice*templineitems.quantity),2)) as extended 
				from templineitems left join products on templineitems.productid=products.id where templineitems.invoiceid=".$id." and templineitems.sessionid=\"".session_id()."\" 
				and sessionid=\"".session_id()."\";";
	$theresult=mysql_query($thequery,$dblink);
	if(!$theresult) reportError(1,$thequery." - ".mysql_error($dblink));
	$subtotal=0;
	$totalweight=0;
	$totalcost=0;


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" >
<html>
<head>
<title>Line Items</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
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
<body style="margin:0px;">
<form name="record" method="post" action="invoices_lineitems.php">
<input name="id" type="hidden" value="<?PHP echo $id ?>">
<input name="deleteid" type="hidden" value="0">
<input name="unitweight" type="hidden" value="0">
<input name="unitcost" type="hidden" value="0">
 <table border="0" cellpadding="0" cellspacing="0" class="bodyline" style="border:0px;">
  <tr>
   <th nowrap class="queryheader" align="left">part number</td>
   <th nowrap class="queryheader" align="left">part name</td>
   <th width="100%" nowrap class="queryheader" align="left">memo</td>
   <th align="right" nowrap class="queryheader">price</td>
   <th align="center" nowrap class="queryheader">qty.</td>
   <th align="right" nowrap class="queryheader">extended</td>
   <th nowrap class="queryheader">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
  </tr>
  <tr>
   <td nowrap>
	<?PHP autofill("partnumber","",4,"products.id","products.partnumber","products.partname","products.status=\"In Stock\"",Array("size"=>"11","maxlength"=>"32","style"=>"border-left-width:0px;"),false,"") ?>
   	<script language="JavaScript">
		  	document.forms["record"]["partnumber"].onchange=populateLineItem;
	</script>
   </td>
   <td nowrap>
	<?PHP autofill("partname","",4,"products.id","products.partname","products.partnumber","products.status=\"In Stock\"",Array("size"=>"18","maxlength"=>"64","style"=>"border-left-width:0px;"),false,"") ?>
   	<script language="JavaScript">
		  	document.forms["record"]["partname"].onchange=populateLineItem;
	</script>
   </td>
   <td nowrap><input name="memo" type="text" id="memo" size="64" maxlength="255" style="width:100%;border-left-width:0px;"></td>
   <td align="right" nowrap><input name="price" type="text" id="price" value="$0.00" size="9" maxlength="16" onChange="calculateExtended()" style="text-align:right;border-left:0px;"></td>
   <td align="center" nowrap><input name="qty" type="text" id="qty" value="1" size="3" maxlength="16" onChange="calculateExtended()" style="text-align:center; border-left:0px;"></td>
   <td align="right" nowrap><input name="extended" type="text" id="extended" value="$0.00" size="9" maxlength="16" readonly="true" style="text-align:right;border-left:0px;"></td>
   <td nowrap><input name="command" type="submit" class="smallButtons" value="add" style="width:35px;"></td>
  </tr><tr><td colspan="7" style="font-size:1px;padding:0px;" class="dottedline">&nbsp;</td></tr>
  <?PHP 
  	$therow=1;
	while($therecord=mysql_fetch_array($theresult)){
		$subtotal+=($therecord["numprice"]*$therecord["quantity"]);
		$totalweight+=($therecord["unitweight"]*$therecord["quantity"]);
		$totalcost+=($therecord["unitcost"]*$therecord["quantity"]);
  ?>
  <tr class="invoicelinetiems">
   <td nowrap class="small" valign="top"><strong><?PHP if($therecord["partnumber"]) echo $therecord["partnumber"]; else echo "&nbsp;";?></strong></td>
   <td class="small" valign="top"><strong><?PHP if($therecord["partname"]) echo $therecord["partname"]; else echo "&nbsp;";?></strong></td>
   <td class="tiny" valign="top"><?PHP if($therecord["memo"]) echo $therecord["memo"]; else echo "&nbsp;"?></td>
   <td align="right" nowrap class="small" valign="top"><?PHP echo $therecord["unitprice"]?></td>
   <td align="center" nowrap class="small" valign="top"><?PHP echo $therecord["quantity"]?></td>
   <td align="right" nowrap class="small" valign="top" style="border-right:0px;"><?PHP echo $therecord["extended"]?></td>
   <td valign="top" style="padding:0px;border-right:0px;"><input name="command" type="submit" class="smallButtons" value="del" style="width:35px;" onClick="return deleteLine(<?PHP echo $therecord["id"] ?>,this)"></td>
  </tr>
  <?PHP } ?>
  </table>
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
