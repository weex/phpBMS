<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/clients_functions.php");
	$clientquerystatement="SELECT firstname,lastname,company FROM clients WHERE id=".$_GET["id"];
	$clientqueryresult=mysql_query($clientquerystatement,$dblink);
	$clientrecord=mysql_fetch_array($clientqueryresult);

	if(!isset($_POST["fromdate"])) $_POST["fromdate"]=date("m")."/01/".date("Y");
	if(!isset($_POST["todate"])) $_POST["todate"]=date("m/d/Y",mktime(0,0,0,date("m")+1,0,date("Y")));
	if(!isset($_POST["status"])) $_POST["status"]="Orders/Invoices";
	if(!isset($_POST["command"])) $_POST["command"]="show";

	if($_POST["command"]=="print")	{
			$_SESSION["printing"]["whereclause"]="WHERE clients.id=".$_GET["id"];
			$_SESSION["printing"]["dataprint"]="Single Record";
			$fromClient=true;
			require("report/clients_purchasehistory.php");
	} else {

	if($clientrecord["company"]=="")
		$pageTitle="Client: ".$clientrecord["firstname"]." ".$clientrecord["lastname"]." : Purchase History";
	else
		$pageTitle="Client: ".$clientrecord["company"]." : Purchase History";
	
	$thestatus="(invoices.status =\"";
	switch($_POST["status"]){
		case "Orders/Invoices":
			$thestatus.="Order\" or invoices.status=\"Invoice\")";
			$searchdate="orderdate";
		break;
		case "Invoices":
			$thestatus.="Invoice\")";
			$searchdate="invoicedate";
		break;
		case "Orders":
			$thestatus.="Order\")";
			$searchdate="orderdate";
		break;
	}

	$temparray=explode("/",$_POST["fromdate"]);
	$mysqlfromdate="\"".$temparray[2]."-".$temparray[0]."-".$temparray[1]."\"";

	$temparray=explode("/",$_POST["todate"]);
	$mysqltodate="\"".$temparray[2]."-".$temparray[0]."-".$temparray[1]."\"";

	//get history
	$querystatement="SELECT invoices.id,Date_Format(invoices.orderdate,\"%c/%e/%Y\") as orderdate,
		Date_Format(invoices.invoicedate,\"%c/%e/%Y\") as invoicedate,invoices.status,
		products.partname as partname, products.partnumber as partnumber,
		lineitems.quantity as qty, lineitems.unitprice*lineitems.quantity as extended,
		lineitems.unitprice as price
		FROM ((clients inner join invoices on clients.id=invoices.clientid) 
				inner join lineitems on invoices.id=lineitems.invoiceid) 
					inner join products on lineitems.productid=products.id
		WHERE clients.id=".$_GET["id"]."   
		and invoices.".$searchdate.">=".$mysqlfromdate."
		and invoices.".$searchdate."<=".$mysqltodate."
		and ".$thestatus."		
		ORDER BY invoices.invoicedate,invoices.orderdate,invoices.id;";
	$thequery=mysql_query($querystatement);
	if(!$thequery) reportError(500,"Could Not Retrieve purchase history<BR>".$querystatement);

	$numrows=mysql_num_rows($thequery);
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">

<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="../../common/javascript/datepicker.js"></script>
</head>
<body><?php include("../../menu.php")?>
<?php client_tabs("Purchase History",$_GET["id"]);?><div class="untabbedbox" style="padding:4px;">
	<h1><?php echo $pageTitle ?></h1>

	<form action="<?PHP echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record">
		<div class="box" style="vertical-align:middle;">
			<table border=0 cellspacing="0" cellpadding="0">
				<tr>
					<td style="padding-right:20px;">
					   invoice status<br>
					   <select name="status" style="">
							<option value="Orders/Invoices" <?php if($_POST["status"]=="Orders/Invoices") echo "selected"?>>Orders/Invoices</option>
							<option value="Invoices" <?php if($_POST["status"]=="Invoices") echo "selected"?>>Invoices</option>
							<option value="Orders" <?php if($_POST["status"]=="Orders") echo "selected"?>>Orders</option>
					   </select>					
					</td>
					<td nowrap>
					   from<br>
					   <?PHP field_datepicker("fromdate",$_POST["fromdate"],0,"",Array("size"=>"10","maxlength"=>"12"),false);?>
					</td>
					<td style="padding-left:5px;" nowrap>
						to<br>
						<?PHP field_datepicker("todate",$_POST["todate"],0,"",Array("size"=>"10","maxlength"=>"12"),false);?>
					</td>
					<td style="padding-left:20px;"><br>
				       <input name="command" type="submit" value="change timeframe/view" class="smallButtons" style="">					
					</td>
					<td width="100%" align="right">
						<br>
						<input name="command" type="submit" value="print" class="Buttons" style="width:80px;">	
					</td>
				</tr>
			</table>			
		   
		</div>
	</form>
	<div>
   <table border="0" cellpadding="0" cellspacing="0" class="querytable">
	<tr>
	 <th align="center" nowrap class="queryheader">&nbsp;</td>
	 <th align="center" nowrap class="queryheader">invc. id</td>
	 <th align="center" nowrap class="queryheader">order date </td>
	 <th align="center" nowrap class="queryheader">invc. date </td>
	 <th nowrap class="queryheader" align="left">part num. </td>
	 <th width="100%" nowrap class="queryheader" align="left">part name </td>
	 <th align="right" nowrap class="queryheader">price</td>
	 <th align="center" nowrap class="queryheader">qty.</td>
	 <th align="right" nowrap class="queryheader">ext.</td>
	</tr>
    <?PHP 
	$totalextended=0;		
	$row=1;
	while ($therecord=mysql_fetch_array($thequery)){
		$row==1? $row++ : $row--;
		$totalextended=$totalextended+$therecord["extended"];
	?>
	<tr class="row<?php echo $row?>">
	 <td style="padding:0px;margin:0px;" nowrap><input name="goToInvoice" type="button" class="smallButtons" onClick="location.href='invoices_addedit.php?id=<?php echo $therecord["id"]?>'" value="go to <?php echo strtolower($therecord["status"])?>" style="width:80px;"></td>
	 <td align="center" nowrap><?PHP echo $therecord["id"]?$therecord["id"]:"&nbsp;" ?></td>
	 <td align="center" nowrap><?PHP echo $therecord["orderdate"]?$therecord["orderdate"]:"&nbsp;" ?></td>
	 <td align="center" nowrap><?PHP echo $therecord["invoicedate"]?$therecord["invoicedate"]:"&nbsp;" ?></td>
	 <td nowrap><?PHP echo $therecord["partnumber"]?></td>
	 <td nowrap><?PHP echo $therecord["partname"]?></td>
	 <td align="right" nowrap><?PHP echo "\$".number_format($therecord["price"],2)?></td>
	 <td align="center" nowrap><?PHP echo $therecord["qty"]?></td>
	 <td align="right" nowrap><?PHP echo "\$".number_format($therecord["extended"],2)?></td>
	</tr>
    <?PHP }//end while ?>
    <?PHP  if(!mysql_num_rows($thequery)) {?>
	<tr><td colspan="9" align=center style="padding:0px;"><div class="norecords">No Sales Data for Given Timeframe</div></td></tr>
	<?php }?>	
	<tr>
	 <td align="center" class="queryfooter">&nbsp;</td>
	 <td align="center" class="queryfooter">&nbsp;</td>
	 <td align="center" class="queryfooter">&nbsp;</td>
	 <td align="center" class="queryfooter">&nbsp;</td>
	 <td class="queryfooter">&nbsp;</td>
	 <td class="queryfooter">&nbsp;</td>
	 <td align="right" class="queryfooter">&nbsp;</td>
	 <td align="center" class="queryfooter">&nbsp;</td>
	 <td align="right" class="queryfooter"><?PHP echo "\$".number_format($totalextended,2)?></td>
	</tr>
   </table>	
	</div></div></body>
</html><?php }?>