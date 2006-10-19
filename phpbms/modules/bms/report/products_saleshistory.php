<?php 
/*
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
 | All rights reserved.                                                    |
 +-------------------------------------------------------------------------+
 |                                                                         |
 | Redistribution and use in source and binary forms, with or without      |
 | modification, are permitted provided that the following conditions are  |
 | met:                                                                    |
 |                                                                         |
 | - Redistributions of source code must retain the above copyright        |
 |   notice, this list of conditions and the following disclaimer.         |
 |                                                                         |
 | - Redistributions in binary form must reproduce the above copyright     |
 |   notice, this list of conditions and the following disclaimer in the   |
 |   documentation and/or other materials provided with the distribution.  |
 |                                                                         |
 | - Neither the name of Kreotek LLC nor the names of its contributore may |
 |   be used to endorse or promote products derived from this software     |
 |   without specific prior written permission.                            |
 |                                                                         |
 | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS     |
 | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT       |
 | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A |
 | PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
 | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,   |
 | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT        |
 | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,   |
 | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY   |
 | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT     |
 | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE   |
 | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.    |
 |                                                                         |
 +-------------------------------------------------------------------------+
*/

if(!isset($fromProduct)) require("../../../include/session.php");
	
class salesHistoryReport{
	
	var $whereclause="";
	var $sortorder=" ORDER BY products.partnumber ";
	var $fromdate;
	var $todate;
	var $view;

	var $productQuery;
	
	function initialize($variables){
		global $dblink;
		
		$this->fromdate=$variables["fromdate"];
		$this->todate=$variables["todate"];
		$this->view=$variables["status"];
		
		$this->whereclause=$_SESSION["printing"]["whereclause"];
		if(isset($_SESSION["printing"]["sortorder"]))
			if($_SESSION["printing"]["sortorder"])
				$this->sortorder=$_SESSION["printing"]["sortorder"];

		if($this->whereclause=="") $this->whereclause="WHERE products.id!=-1";		
		$this->whereclause=" WHERE (".substr($this->whereclause,6).") ";

		$querystatement="SELECT products.id,products.partnumber, products.partname FROM products ".$this->whereclause.$this->sortorder;
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(100,"Could not Initialize Product Sales History Report");

		$this->productQuery=$queryresult;
	}
	
		
	function showSalesHistory($id){
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
			
		$querystatement="select invoices.id as id, Date_Format(invoices.orderdate,\"%c/%e/%Y\") as orderdate,
			Date_Format(invoices.invoicedate,\"%c/%e/%Y\") as invoicedate,
			if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as client,
			lineitems.quantity as qty, lineitems.unitprice*lineitems.quantity as extended,
			lineitems.unitprice as price, lineitems.unitcost as cost, lineitems.unitcost*lineitems.quantity as extendedcost
			from ((products inner join lineitems on products.id=lineitems.productid) 
					inner join invoices on lineitems.invoiceid=invoices.id) 
						inner join clients on invoices.clientid=clients.id
			where products.id=".$id."
			and invoices.".$searchdate.">=".$mysqlfromdate."
			and invoices.".$searchdate."<=".$mysqltodate."
			and ".$thestatus."
			order by invoices.invoicedate, invoices.orderdate;";
		$thequery=mysql_query($querystatement,$dblink);
		if(!$thequery) reportError(100,mysql_error($dblink)." ".$querystatement);
		$thequery? $numrows=mysql_num_rows($thequery): $numrows=0;
?>
   <table border="0" cellpadding="3" cellspacing="0">
	<tr>
	 <th align="center" nowrap >ID</td>
	 <th align="center" nowrap >Order Date</td>
	 <th align="center" nowrap >Invc. Date</td>
	 <th nowrap  width="100%" align="left">Client</td>
	 <th align="center" nowrap >Qty.</td>
	 <th align="right" nowrap >Unit Cost</td>
	 <th align="right" nowrap >Cost Ext.</td>
	 <th align="right" nowrap >Unit Price</td>
	 <th align="right" nowrap >Price Ext.</td>
	</tr>
    <?PHP 	
	$totalextended=0;
	$totalcostextended=0;
	$totalquantity=0;
	$avgprice=0;
	$avgcost=0;
	while ($therecord=mysql_fetch_array($thequery)){
		$avgcost+=$therecord["cost"];
		$avgprice+=$therecord["price"];
		$totalquantity+=$therecord["qty"];
		$totalextended+=$therecord["extended"];
		$totalcostextended+=$therecord["extendedcost"];
?>
	<tr>
	 <td align="center" nowrap><?PHP echo $therecord["id"]?></td>
	 <td align="center" nowrap><?PHP echo $therecord["orderdate"]?$therecord["orderdate"]:"&nbsp;" ?></td>
	 <td align="center" nowrap><?PHP echo $therecord["invoicedate"]?$therecord["invoicedate"]:"&nbsp;" ?></td>
	 <td nowrap><?PHP echo $therecord["client"]?></td>
	 <td align="center" nowrap><?PHP echo number_format($therecord["qty"],2)?></td>
	 <td align="right" nowrap><?PHP echo "\$".number_format($therecord["cost"],2)?></td>
	 <td align="right" nowrap><?PHP echo "\$".number_format($therecord["extendedcost"],2)?></td>
	 <td align="right" nowrap><?PHP echo "\$".number_format($therecord["price"],2)?></td>
	 <td align="right" nowrap><?PHP echo "\$".number_format($therecord["extended"],2)?></td>
	</tr>
    <?PHP } ?>
	<tr>
	 <td align="center" class="grandtotals">&nbsp;</td>
	 <td align="center" class="grandtotals">&nbsp;</td>
	 <td class="grandtotals">&nbsp;</td>
	 <td class="grandtotals">&nbsp;</td>
	 <td align="center" class="grandtotals"><?PHP echo number_format($totalquantity,2)?></td>
	 <td align="right" nowrap class="grandtotals">avg. = <?PHP $numrows?$avgcost=$avgcost/$numrows:$avgcost=0; echo "\$".number_format($avgcost,2)?></td>
	 <td align="right" class="grandtotals"><?PHP echo "\$".number_format($totalcostextended,2)?></td>
	 <td align="right" nowrap class="grandtotals">avg. = <?PHP $numrows?$avgprice=$avgprice/$numrows:$avgprice=0; echo "\$".number_format($avgprice,2)?></td>
	 <td align="right" class="grandtotals"><?PHP echo "\$".number_format($totalextended,2)?></td>
	</tr>
   </table>
<?php
	}//end fucntion showSalesHistory($id)

	function showReport(){
	?>
<head>
<title>Product Sales History</title>
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

.invoices{font-size:10px; border-bottom-style:dotted; border-bottom-width:2px;}
.lineitems{font-size:9px;border-bottom-style:dotted; border-bottom-width:1px; border-right-width:0px;}
-->
</style>
</head>
<body>
<h1>Product Sales History</h1>
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
<?php while($therecord=mysql_fetch_array($this->productQuery)){?>
	<h3><?php echo $therecord["partnumber"]." - ".$therecord["partname"] ?></h3>
<?php $this->showSalesHistory($therecord["id"]);}//end while?>
</body>
</html>
	<?php	
	}
}//end class

if(isset($_POST["command"])){
	$myreport= new salesHistoryReport();
	$myreport->initialize($_POST);
	
	$myreport->showReport();
} else {
	require("../../../include/fields.php");
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Product Sales History </title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<?php require("../../../head.php")?>
	<script language="javascript" src="../../../common/javascript/common.js"></script>
<script language="javascript" src="../../../common/javascript/fields.js"></script>
	<script language="javascript" src="../../../common/javascript/datepicker.js"></script>		
</head>

<body>
<div class="bodyline" style="width:550px;padding:4px;">
	<h1>Product Sales History Options</h1>	
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