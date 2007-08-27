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
	include("../../include/session.php");
	include("include/fields.php");
	
	if(!hasRights(30)) goURL(APP_PATH."noaccess.php");
	
	if(!isset($_POST["fromdate"])) $_POST["fromdate"]=dateToString(strtotime("-1 year"));
	if(!isset($_POST["todate"])) $_POST["todate"]=dateToString(mktime());
	if(!isset($_POST["status"])) $_POST["status"]="Orders/Invoices";
	if(!isset($_POST["command"])) $_POST["command"]="show";

	if($_POST["command"]=="print")	{
			$_SESSION["printing"]["whereclause"]="WHERE products.id=".$_GET["id"];
			$_SESSION["printing"]["dataprint"]="Single Record";
			$fromProduct=true;
			require("report/products_saleshistory.php");
	} else {
	$thestatus="(invoices.type =\"";
	switch($_POST["status"]){
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

	$refquery="select partname from products where id=".$_GET["id"];
	$refquery=$db->query($refquery);
	$refrecord=$db->fetchArray($refquery);
	
	$querystatement="SELECT invoices.id as id, 
		if(invoices.type=\"Invoice\",invoices.invoicedate,invoices.orderdate) as thedate, 
		if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as client,
		lineitems.quantity as qty, 
		lineitems.unitprice*lineitems.quantity as extended,
		lineitems.unitprice as price, lineitems.unitcost as cost, 
		lineitems.unitcost*lineitems.quantity as extendedcost
		FROM((products inner join lineitems on products.id=lineitems.productid) 
				inner join invoices on lineitems.invoiceid=invoices.id) 
					inner join clients on invoices.clientid=clients.id
		WHERE products.id=".$_GET["id"]." 
		AND ".$thestatus."
		HAVING thedate >=\"".$mysqlfromdate."\"
		and thedate <=\"".$mysqltodate."\" ORDER BY thedate";
	$queryresult=$db->query($querystatement);

	$queryresult? $numrows=$db->numRows($queryresult): $numrows=0;

	$pageTitle="Product Sales History: ".$refrecord["partname"];
	
	$phpbms->cssIncludes[] = "pages/products.css";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();
		
		$theinput = new inputDatePicker("fromdate",sqlDateFromString($_POST["fromdate"]), "from" ,true);
		$theform->addField($theinput);
				
		$theinput = new inputDatePicker("todate",sqlDateFromString($_POST["todate"]), "to" ,true);
		$theform->addField($theinput);
		
		$theform->jsMerge();
		//==============================================================
		//End Form Elements
	
	include("header.php");
	
	$phpbms->showTabs("products entry",12,$_GET["id"]);?><div class="bodyline">
	<h1><span><?php echo $pageTitle ?></span></h1>
	<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record">		
	<div class="box">
		<p class="timelineP">
		   <label for="status">type</label><br />
		   <select name="status" id="status">
				<option value="Orders/Invoices" <?php if($_POST["status"]=="Orders/Invoices") echo "selected=\"selected\""?>>Orders/Invoices</option>
				<option value="Invoices" <?php if($_POST["status"]=="Invoices") echo "selected=\"selected\""?>>Invoices</option>
				<option value="Orders" <?php if($_POST["status"]=="Orders") echo "selected=\"selected\""?>>Orders</option>
		   </select>								
		</p>
		
		<p class="timelineP"><?php $theform->showField("fromdate")?></p>

		<p class="timelineP"><?php $theform->showField("todate")?></p>
		
		<p id="printP"><br /><input id="print" name="command" type="submit" value="print" class="Buttons" /></p>
		<p id="changeTimelineP"><br /><input name="command" type="submit" value="change timeframe/view" class="smallButtons" /></p>
	</div>

   <div class="fauxP">   
   <table border="0" cellpadding="3" cellspacing="0" class="querytable">
	<tr>
	 <th align="center" nowrap="nowrap" class="queryheader">ID</th>
	 <th align="center" nowrap="nowrap" class="queryheader">Order Date</th>
	 <th nowrap="nowrap" class="queryheader" width="100%" align="left">Client</th>
	 <th align="center" nowrap="nowrap" class="queryheader">Qty.</th>
	 <th align="right" nowrap="nowrap" class="queryheader">Unit Cost</th>
	 <th align="right" nowrap="nowrap" class="queryheader">Cost Ext.</th>
	 <th align="right" nowrap="nowrap" class="queryheader">Unit Price</th>
	 <th align="right" nowrap="nowrap" class="queryheader">Price Ext.</th>
	</tr>
    <?php 	
	$totalextended=0;
	$totalcostextended=0;
	$totalquantity=0;
	$avgprice=0;
	$avgcost=0;
	$row=1;
	while ($therecord=$db->fetchArray($queryresult)){
		if($row==1) $row=2;else $row=1;
		$avgcost+=$therecord["cost"];
		$avgprice+=$therecord["price"];
		$totalquantity+=$therecord["qty"];
		$totalextended+=$therecord["extended"];
		$totalcostextended+=$therecord["extendedcost"];
?>
	<tr class="row<?php echo $row?>">
	 <td align="center" nowrap="nowrap"><?php echo $therecord["id"]?></td>
	 <td align="center" nowrap="nowrap"><?php echo $therecord["thedate"]?formatFromSQLDate($therecord["thedate"]):"&nbsp;" ?></td>
	 <td nowrap="nowrap"><?php echo $therecord["client"]?></td>
	 <td align="center" nowrap="nowrap"><?php echo number_format($therecord["qty"],2)?></td>
	 <td align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["cost"])?></td>
	 <td align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["extendedcost"])?></td>
	 <td align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["price"])?></td>
	 <td align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["extended"])?></td>
	</tr>
    <?php } if(!$db->numRows($queryresult)) {?>
	<tr><td colspan="9" align="center" style="padding:0px;"><div class="norecords">No Sales Data for Given Timeframe</div></td></tr>
	<?php }?>
	<tr>
	 <td align="center" class="queryfooter">&nbsp;</td>
	 <td align="center" class="queryfooter">&nbsp;</td>
	 <td class="queryfooter">&nbsp;</td>
	 <td align="center" class="queryfooter"><?php echo number_format($totalquantity,2)?></td>
	 <td align="right" nowrap="nowrap" class="queryfooter">avg. = <?php $numrows?$avgcost=$avgcost/$numrows:$avgcost=0; echo numberToCurrency($avgcost)?></td>
	 <td align="right" class="queryfooter"><?php echo numberToCurrency($totalcostextended)?></td>
	 <td align="right" nowrap="nowrap" class="queryfooter">avg. = <?php $numrows?$avgprice=$avgprice/$numrows:$avgprice=0; echo numberToCurrency($avgprice)?></td>
	 <td align="right" class="queryfooter"><?php echo numberToCurrency($totalextended)?></td>
	</tr>
   </table></div></form>	
</div>
<?php include("footer.php"); }//end if?>