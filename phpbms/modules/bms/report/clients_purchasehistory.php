<?php 
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2007, Kreotek LLC                                  |
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

if(!isset($fromClient)) {
	require("../../../include/session.php");
	require("../../../include/common_functions.php");
}
	
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
	
	$mysqlfromdate=sqlDateFromString($_POST["fromdate"]);
	$mysqltodate=sqlDateFromString($_POST["todate"]);
			
	$querystatement="SELECT invoices.id,
		if(invoices.type=\"Invoice\",invoices.invoicedate,invoices.orderdate) as thedate, 
		if(invoices.type=\"Invoice\",invoices.invoicedate,invoices.orderdate) as formateddate, 
		invoices.type,
		products.partname as partname, products.partnumber as partnumber,
		lineitems.quantity as qty, lineitems.unitprice*lineitems.quantity as extended,
		lineitems.unitprice as price
		FROM ((clients inner join invoices on clients.id=invoices.clientid) 
				inner join lineitems on invoices.id=lineitems.invoiceid) 
					inner join products on lineitems.productid=products.id
		WHERE clients.id=".$id."   
		and ".$thestatus."		
		HAVING 
		thedate >=\"".$mysqlfromdate."\"
		and thedate <=\"".$mysqltodate."\"
		ORDER BY thedate,invoices.id;";
		$thequery=mysql_query($querystatement,$dblink);
		if(!$thequery) reportError(100,mysql_error($dblink)." ".$querystatement);
		$thequery? $numrows=mysql_num_rows($thequery): $numrows=0;
?>
	<table border="0" cellpadding="0" cellspacing="0" >
		<tr>
			<th align="left" nowrap="nowrap" colspan="3">invoice</th>
			<th align="left" nowrap="nowrap" colspan="3">product</th>		
			<th align="left" nowrap="nowrap" colspan="2">line item</th>
		</tr>
		<tr>
			<th align="center" nowrap="nowrap">id</th>
			<th align="left" nowrap="nowrap" >type</th>
			<th align="left" nowrap="nowrap" >date</th>
			<th align="left" nowrap="nowrap" >part num.</th>
			<th width="100%" nowrap="nowrap" align="left">name</th>
			<th align="right" nowrap="nowrap" >price</th>
			<th align="center" nowrap="nowrap" >qty.</th>
			<th align="right" nowrap="nowrap" >ext.</th>
		</tr>
    <?php 
	$totalextended=0;		
	while ($therecord=mysql_fetch_array($thequery)){
		$totalextended=$totalextended+$therecord["extended"];
	?>
	<tr>
		<td align="left" nowrap="nowrap"><?php echo $therecord["id"]?$therecord["id"]:"&nbsp;" ?></td>
		<td align="left" nowrap="nowrap"><?php echo $therecord["type"]?$therecord["type"]:"&nbsp;" ?></td>
		<td align="left" nowrap="nowrap"><?php echo $therecord["formateddate"]?$therecord["formateddate"]:"&nbsp;" ?></td>
		<td nowrap="nowrap"><?php echo $therecord["partnumber"]?></td>
		<td nowrap="nowrap"><?php echo $therecord["partname"]?></td>
		<td align="right" nowrap="nowrap"><?php echo "\$".number_format($therecord["price"],2)?></td>
		<td align="center" nowrap="nowrap"><?php echo $therecord["qty"]?></td>
		<td align="right" nowrap="nowrap"><?php echo "\$".number_format($therecord["extended"],2)?></td>
	</tr>
	<?php }//end while ?>
	<tr>
	 <td align="center" class="grandtotals">&nbsp;</td>
	 <td align="center" class="grandtotals">&nbsp;</td>
	 <td align="center" class="grandtotals">&nbsp;</td>
	 <td class="grandtotals">&nbsp;</td>
	 <td class="grandtotals">&nbsp;</td>
	 <td align="right" class="grandtotals">&nbsp;</td>
	 <td align="center" class="grandtotals">&nbsp;</td>
	 <td align="right" class="grandtotals"><?php echo "\$".number_format($totalextended,2)?></td>
	</tr>
   </table>	<?php
	}//end fucntion showSalesHistory($id)

	function showReport(){
	?>
<head>
<title>Client Purchase History</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
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
			source:<br />
			<?php echo $_SESSION["printing"]["dataprint"]?>
		</li>
		<li>
			date generated:<br />
			<?php echo dateToString(mktime())." ".timeToString(mktime());?>
		</li>
		<li style="padding-left:30px;padding-right:20px;">
			view:<br />
			<?php echo $this->view?>
		</li>
		<li>
			from:<br />
			<?php echo $this->fromdate?>
		</li>
		<li>
			to:<br />
			<?php echo $this->todate?>
		</li>
	</ul><br /><br />
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
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Client Purchase History</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?php require("../../../head.php")?>
	<link href="../../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/historyreports.css" rel="stylesheet" type="text/css" />	
	<script language="javascript" src="../../../common/javascript/fields.js" type="text/javascript"></script>
	<script language="javascript" src="../../../common/javascript/datepicker.js" type="text/javascript"></script>	
</head>

<body>
<form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" name="totals">
<div class="bodyline" id="reportOptions">
	<h1 id="topTitle"><span>Product Sales History Options</span></h1>	
		<fieldset>
			<legend>time frame</legend>
			<p id="fromP">
				<label for="fromdate">from</label><br />
				<?php 
				$thedate=mktime(0,0,0,date("m"),1);
				fieldDatePicker("fromdate",dateToString($thedate),0,"",Array("size"=>"10","maxlength"=>"12"),false);?>
			</p>
			<p>
				<label for="todate">to</label><br />
				<?php fieldDatePicker("todate",dateToString(mktime(0,0,0,date("m")+1,0,date("Y"))),0,"",Array("size"=>"10","maxlength"=>"12"),false);?>
			</p>
		</fieldset>

		<p>
			<label for="status">include products from...<br /></label>
		   <select id="status" name="status">
				<option value="Orders/Invoices" selected>Orders/Invoices</option>
				<option value="Invoices">Invoices</option>
				<option value="Orders">Orders</option>
		   </select>					
		</p>

		<div align="right" class="box">
			<input name="command" type="submit" class="Buttons" id="print" value="print" />
			<input name="cancel" type="button" class="Buttons" id="cancel" value="cancel" onclick="window.close();" />
		</div>
</div>
</form>
</body>
</html><?php }?>