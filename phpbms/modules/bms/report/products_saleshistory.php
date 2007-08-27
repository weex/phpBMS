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

if(!isset($fromProduct)) {
	require_once("../../../include/session.php");
}
	
class salesHistoryReport{
	
	var $whereclause="";
	var $sortorder=" ORDER BY products.partnumber ";
	var $fromdate;
	var $todate;
	var $view;

	var $productQuery;
	
	function initialize($variables,$db){
		
		$this->db = $db;

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
		$queryresult = $this->db->query($querystatement);

		$this->productQuery=$queryresult;
	}
	
		
	function showSalesHistory($id){

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
			
		$querystatement="select invoices.id as id, invoices.orderdate,
			invoices.invoicedate,
			if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as client,
			lineitems.quantity as qty, lineitems.unitprice*lineitems.quantity as extended,
			lineitems.unitprice as price, lineitems.unitcost as cost, lineitems.unitcost*lineitems.quantity as extendedcost
			from ((products inner join lineitems on products.id=lineitems.productid) 
					inner join invoices on lineitems.invoiceid=invoices.id) 
						inner join clients on invoices.clientid=clients.id
			where products.id=".$id."
			and invoices.".$searchdate.">=\"".$mysqlfromdate."\"
			and invoices.".$searchdate."<=\"".$mysqltodate."\"
			and ".$thestatus."
			order by invoices.invoicedate, invoices.orderdate;";

		$thequery=$this->db->query($querystatement);

		$thequery? $numrows = $this->db->numRows($thequery): $numrows=0;
?>
   <table border="0" cellpadding="3" cellspacing="0">
	<tr>
	 <th align="center" nowrap="nowrap" >ID</th>
	 <th align="center" nowrap="nowrap" >Order Date</th>
	 <th align="center" nowrap="nowrap" >Invc. Date</th>
	 <th nowrap="nowrap"  width="100%" align="left">Client</th>
	 <th align="center" nowrap="nowrap" >Qty.</th>
	 <th align="right" nowrap="nowrap" >Unit Cost</th>
	 <th align="right" nowrap="nowrap" >Cost Ext.</th>
	 <th align="right" nowrap="nowrap" >Unit Price</th>
	 <th align="right" nowrap="nowrap">Price Ext.</th>
	</tr>
    <?php 	
	$totalextended=0;
	$totalcostextended=0;
	$totalquantity=0;
	$avgprice=0;
	$avgcost=0;
	while ($therecord = $this->db->fetchArray($thequery)){
		$avgcost+=$therecord["cost"];
		$avgprice+=$therecord["price"];
		$totalquantity+=$therecord["qty"];
		$totalextended+=$therecord["extended"];
		$totalcostextended+=$therecord["extendedcost"];
?>
	<tr>
	 <td align="center" nowrap="nowrap"><?php echo $therecord["id"]?></td>
	 <td align="center" nowrap="nowrap"><?php echo $therecord["orderdate"]?formatFromSQLDate($therecord["orderdate"]):"&nbsp;" ?></td>
	 <td align="center" nowrap="nowrap"><?php echo $therecord["invoicedate"]?formatFromSQLDate($therecord["invoicedate"]):"&nbsp;" ?></td>
	 <td nowrap="nowrap"><?php echo $therecord["client"]?></td>
	 <td align="center" nowrap="nowrap"><?php echo number_format($therecord["qty"],2)?></td>
	 <td align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["cost"])?></td>
	 <td align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["extendedcost"])?></td>
	 <td align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["price"])?></td>
	 <td align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["extended"])?></td>
	</tr>
    <?php } ?>
	<tr>
	 <td align="center" class="grandtotals">&nbsp;</td>
	 <td align="center" class="grandtotals">&nbsp;</td>
	 <td class="grandtotals">&nbsp;</td>
	 <td class="grandtotals">&nbsp;</td>
	 <td align="center" class="grandtotals"><?php echo number_format($totalquantity,2)?></td>
	 <td align="right" nowrap="nowrap"class="grandtotals">avg. = <?php $numrows?$avgcost=$avgcost/$numrows:$avgcost=0; echo numberToCurrency($avgcost)?></td>
	 <td align="right" class="grandtotals"><?php echo numberToCurrency($totalcostextended)?></td>
	 <td align="right" nowrap="nowrap" class="grandtotals">avg. = <?php $numrows?$avgprice=$avgprice/$numrows:$avgprice=0; echo numberToCurrency($avgprice)?></td>
	 <td align="right" class="grandtotals"><?php echo numberToCurrency($totalextended)?></td>
	</tr>
   </table>
<?php
	}//end fucntion showSalesHistory($id)

	function showReport(){
	?>
<head>
<title>Product Sales History</title>
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
			source:<br />
			<?php echo $_SESSION["printing"]["dataprint"]?>
		</li>
		<li>
			date generated:<br />
			<?php echo dateToString(mktime())." ".timeToString(mktime())?>
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
<?php while($therecord=$this->db->fetchArray($this->productQuery)){?>
	<h3><?php echo $therecord["partnumber"]." - ".$therecord["partname"] ?></h3>
<?php $this->showSalesHistory($therecord["id"]);}//end while?>
</body>
</html>
	<?php	
	}
}//end class

if(isset($_POST["command"])){
	$myreport= new salesHistoryReport();
	$myreport->initialize($_POST,$db);
	
	$myreport->showReport();
} else {
	
		require("include/fields.php");
		
		$pageTitle = "Product Sales History";
		$phpbms->cssIncludes[] = "pages/historyreports.css";
		$phpbms->showMenu = false;
	
		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();
		
		
		$thedate = dateToString( mktime(0,0,0,date("m"),1),"SQL" );
		$theinput = new inputDatePicker("fromdate", $thedate, "from",true);
		$theform->addField($theinput);
		
		$thedate = dateToString( mktime(0,0,0,date("m")+1,0,date("Y")), "SQL" );
		$theinput = new inputDatePicker("todate", $thedate, "to",true);
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements

		include("header.php");
?>
<form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" name="totals" onsubmit="return validateForm(this)">
<div class="bodyline" id="reportOptions">
	<h1 id="topTitle"><span>Product Sales History Options</span></h1>	
		<fieldset>
			<legend>time frame</legend>

			<p id="fromP"><?php $theform->showField("fromdate");?></p>

			<p><?php $theform->showField("todate");?></p>
		</fieldset>

		<p>
			<label for="status">include line items from...<br /></label>
		   <select id="status" name="status">
				<option value="Orders/Invoices" selected="selected">Orders/Invoices</option>
				<option value="Invoices">Invoices</option>
				<option value="Orders">Orders</option>
		   </select>					
		</p>

		<div align="right">
			<input name="command" type="submit" class="Buttons" id="print" value="print" />
			<input name="cancel" type="button" class="Buttons" id="cancel" value="cancel" onclick="window.close();" />
		</div>
</div>
</form>
<?php 
include("footer.php");
}?>