<?php 
if(!isset($fromClient)) require("../../../include/session.php");
	
class purchaseHistoryReport{
	
	var $whereclause="";
	var $sortorder=" ORDER BY if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) ";
	var $fromdate;
	var $todate;
	var $view;

	var $clientQuery;
	
	function initialize($variables){
		global $dblink;
		
		$this->fromdate=$variables["fromdate"];
		$this->todate=$variables["todate"];
		$this->view=$variables["status"];
		
		$this->whereclause=$_SESSION["printing"]["whereclause"];
		if(isset($_SESSION["printing"]["sortorder"]))
			if ($_SESSION["printing"]["sortorder"])
				$this->sortorder=$_SESSION["printing"]["sortorder"];

		if($this->whereclause=="") $this->whereclause="WHERE clients.id!=-1";		
		$this->whereclause=" WHERE (".substr($this->whereclause,6).") ";

		$querystatement="SELECT clients.id, if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as thename
					     FROM clients ".$this->whereclause.$this->sortorder;
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(100,"Could not Initialize Client Purchase History Report");

		$this->clientQuery=$queryresult;
	}
	
		
	function showPurchaseHistory($id){
		global $dblink;

		$thestatus="(invoices.type =\"";
		switch($this->view){
			case "Orders/Invoices":
				$thestatus.="Order\" or invoices.type=\"Invoice\")";
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
	
		$temparray=explode("/",$this->fromdate);
		$mysqlfromdate="\"".$temparray[2]."-".$temparray[0]."-".$temparray[1]."\"";
	
		$temparray=explode("/",$this->todate);
		$mysqltodate="\"".$temparray[2]."-".$temparray[0]."-".$temparray[1]."\"";
			
	$querystatement="SELECT invoices.id,
		if(invoices.type=\"Invoice\",invoices.invoicedate,invoices.orderdate) as thedate, 
		if(invoices.type=\"Invoice\",Date_Format(invoices.invoicedate,\"%c/%e/%Y\"),Date_Format(invoices.orderdate,\"%c/%e/%Y\")) as formateddate, 
		invoices.type,
		products.partname as partname, products.partnumber as partnumber,
		lineitems.quantity as qty, lineitems.unitprice*lineitems.quantity as extended,
		lineitems.unitprice as price
		FROM ((clients inner join invoices on clients.id=invoices.clientid) 
				inner join lineitems on invoices.id=lineitems.invoiceid) 
					inner join products on lineitems.productid=products.id
		WHERE clients.id=".$_GET["id"]."   
		and ".$thestatus."		
		HAVING 
		thedate >=".$mysqlfromdate."
		and thedate <=".$mysqltodate."
		ORDER BY thedate,invoices.id;";
		$thequery=mysql_query($querystatement,$dblink);
		if(!$thequery) reportError(100,mysql_error($dblink)." ".$querystatement);
		$thequery? $numrows=mysql_num_rows($thequery): $numrows=0;
?>
	<table border="0" cellpadding="0" cellspacing="0" >
		<TR>
			<th align="left" nowrap colspan="3">invoice</th>
			<th align="left" nowrap colspan="3">product</th>		
			<th align="left" nowrap colspan="2">line item</th>
		</TR>
		<tr>
			<th align="center" nowrap>id</td>
			<th align="left" nowrap >type</td>
			<th align="left" nowrap >date</td>
			<th align="left" nowrap >part num. </td>
			<th width="100%" nowrap align="left">name</td>
			<th align="right" nowrap >price</td>
			<th align="center" nowrap >qty.</td>
			<th align="right" nowrap >ext.</td>
		</tr>
    <?PHP 
	$totalextended=0;		
	while ($therecord=mysql_fetch_array($thequery)){
		$totalextended=$totalextended+$therecord["extended"];
	?>
	<tr>
		<td align="left" nowrap><?PHP echo $therecord["id"]?$therecord["id"]:"&nbsp;" ?></td>
		<td align="left" nowrap><?PHP echo $therecord["type"]?$therecord["type"]:"&nbsp;" ?></td>
		<td align="left" nowrap><?PHP echo $therecord["formateddate"]?$therecord["formateddate"]:"&nbsp;" ?></td>
		<td nowrap><?PHP echo $therecord["partnumber"]?></td>
		<td nowrap><?PHP echo $therecord["partname"]?></td>
		<td align="right" nowrap><?PHP echo "\$".number_format($therecord["price"],2)?></td>
		<td align="center" nowrap><?PHP echo $therecord["qty"]?></td>
		<td align="right" nowrap><?PHP echo "\$".number_format($therecord["extended"],2)?></td>
	</tr>
	<?PHP }//end while ?>
	<tr>
	 <td align="center" class="grandtotals">&nbsp;</td>
	 <td align="center" class="grandtotals">&nbsp;</td>
	 <td align="center" class="grandtotals">&nbsp;</td>
	 <td class="grandtotals">&nbsp;</td>
	 <td class="grandtotals">&nbsp;</td>
	 <td align="right" class="grandtotals">&nbsp;</td>
	 <td align="center" class="grandtotals">&nbsp;</td>
	 <td align="right" class="grandtotals"><?PHP echo "\$".number_format($totalextended,2)?></td>
	</tr>
   </table>	<?php
	}//end fucntion showSalesHistory($id)

	function showReport(){
	?>
<head>
<title>Client Purchase History</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
BODY,TH,TD,H1,H2,h3{
	font-size : 11px;
	font-family : sans-serif;
	color : Black; 
}
H1,H2{
	font-size:18px;
	border-bottom:4px solid black;
	margin:0px;	
}
H2{ font-size:11px; border-bottom-width:2px; margin-bottom:10px;}
H3{ font-size:14px; margin-bottom:2px;}
div {padding:5px;}

UL{margin:0;padding:0;}
LI{float:left;display:inline;padding-left:10px;}

TABLE{border:3px solid black;border-bottom-width:1px;border-right-width:1px;}
TH, TD{ padding:2px; border-right:1px solid black;border-bottom:1px solid black;}
TH {
	background-color:#EEEEEE;
	font-size:12px;
	font-weight: bold;
	border-bottom-width:3px;
}

.grandtotals{font-size:12px; border-top:3px double black; font-weight:bold; padding-top:8px;padding-bottom:8px; background-color:#EEEEEE;}

-->
</style>
</head>
<body>
<h1>Client Purchase History</h1>
<h2>
	<ul>
		<li>
			source:<br>
			<?php echo $_SESSION["printing"]["dataprint"]?>
		</li>
		<li>
			date generated:<br>
			<?php echo date("m/d/Y H:i");?>
		</li>
		<li style="padding-left:30px;padding-right:20px;">
			view:<br>
			<?php echo $this->view?>
		</li>
		<li>
			from:<br>
			<?php echo $this->fromdate?>
		</li>
		<li>
			to:<br>
			<?php echo $this->todate?>
		</li>
	</ul><br><br>
</h2>
<?php while($therecord=mysql_fetch_array($this->clientQuery)){?>
	<h3><?php echo $therecord["thename"]?></h3>
<?php $this->showPurchaseHistory($therecord["id"]);}//end while?>
</body>
</html>
	<?php	
	}
}//end class

if(isset($_POST["command"])){
	$myreport= new purchaseHistoryReport();
	$myreport->initialize($_POST);
	
	$myreport->showReport();
} else {
	require("../../../include/fields.php");
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Client Purchase History</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
	<script language="javascript" src="../../../common/javascript/common.js"></script>
	<script language="javascript" src="../../../common/javascript/fields.js"></script>	
	<script language="javascript" src="../../../common/javascript/datepicker.js"></script>	
</head>

<body>
<div class="bodyline" style="width:550px;padding:4px;">
	<h1>Client Purchase History Options</h1>	
	<form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" name="totals" onSubmit="">
		<div>
			<strong>timeframe</strong><br>			
			<table border=0 cellspacing="0" cellpadding="0">
				<tr>
					<td nowrap>
					   from<br>
					   <?PHP field_datepicker("fromdate",date("m")."/01/".date("Y"),0,"",Array("size"=>"10","maxlength"=>"12"),false);?>
					</td>
					<td style="padding-left:5px;" nowrap>
						to<br>
						<?PHP field_datepicker("todate",date("m/d/Y",mktime(0,0,0,date("m")+1,0,date("Y"))),0,"",Array("size"=>"10","maxlength"=>"12"),false);?>
					</td>
				</tr>
			</table>
		</div>
		<div>
		   invoice status<br>
		   <select name="status" style="">
				<option value="Orders/Invoices" selected>Orders/Invoices</option>
				<option value="Invoices">Invoices</option>
				<option value="Orders">Orders</option>
		   </select>					
		</div>
		<div align="right" class="box">
			<input name="command" type="submit" class="Buttons" id="print" value="print" style="width:75px;margin-right:3px;">
			<input name="cancel" type="button" class="Buttons" id="cancel" value="cancel" style="width:75px;" onClick="window.close();">	 
		</div>
   </form>
</div>

</body>
</html><?php }?>