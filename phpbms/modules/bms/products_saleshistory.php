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

	if(!hasRights("role:259ead9f-100b-55b5-508a-27e33a6216bf"))
            goURL(APP_PATH."noaccess.php");

	if(!isset($_POST["fromdate"]))
            $_POST["fromdate"] = dateToString(strtotime("-1 year"));

        if(!isset($_POST["todate"]))
            $_POST["todate"] = dateToString(mktime());

	if(!isset($_POST["status"]))
            $_POST["status"] = "Orders and Invoices";

        if(!isset($_POST["command"]))
            $_POST["command"] = "show";

	if(!isset($_POST["date_order"]))
            $_POST["date_order"] = "DESC";

	if($_POST["command"]=="print")	{

            $_SESSION["printing"]["whereclause"]="products.id=".$_GET["id"];
            $_SESSION["printing"]["dataprint"]="Single Record";

            goURL("report/products_saleshistory.php?rid=".urlencode("rpt:a278af28-9c34-da2e-d81b-4caa36dfa29f")."&tid=".urlencode("tbld:7a9e87ed-d165-c4a4-d9b9-0a4adc3c5a34")."&status=".urlencode($_POST["status"])."&fromdate=".urlencode($_POST["fromdate"])."&todate=".urlencode($_POST["todate"]));

	} else {

            $thestatus="(invoices.type =\"";
            switch($_POST["status"]){

		case "Orders and Invoices":
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

            }//endswitch
        $dateOrder = ($_POST['date_order'] == 'DESC') ? 'ASC' : 'DESC';

	$mysqlfromdate=sqlDateFromString($_POST["fromdate"]);
	$mysqltodate=sqlDateFromString($_POST["todate"]);

	$refquery="select partname from products where id=".((int)$_GET["id"]);
	$refquery=$db->query($refquery);
	$refrecord=$db->fetchArray($refquery);

	$querystatement="
            SELECT
                invoices.id AS id,
                IF(invoices.type = 'Invoice', invoices.invoicedate, invoices.orderdate) AS thedate,
                CONCAT('<strong>',IF(clients.lastname != '', CONCAT(clients.lastname,', ', clients.firstname, IF(clients.company != '', CONCAT(' (', clients.company, ')'),'')), clients.company), '</strong>') AS client,
                lineitems.quantity AS qty,
                lineitems.unitprice * lineitems.quantity AS extended,
                lineitems.unitprice AS price,
                lineitems.unitcost AS cost,
                lineitems.unitcost * lineitems.quantity AS extendedcost
            FROM
                ((products INNER JOIN lineitems ON products.uuid = lineitems.productid)
                    INNER JOIN invoices ON lineitems.invoiceid=invoices.id)
                        INNER JOIN clients ON invoices.clientid = clients.uuid
            WHERE
                products.id=".((int)$_GET["id"])."
                AND ".$thestatus."
            HAVING
                thedate >= '".$mysqlfromdate."'
                AND thedate <= '".$mysqltodate."'
            ORDER BY
                thedate " .$dateOrder;

	$queryresult=$db->query($querystatement);

	$numrows = ($queryresult)? $db->numRows($queryresult) : 0;

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

	$phpbms->showTabs("products entry","tab:cd09d4a1-7d32-e08a-bd6e-5850bc9af88e",$_GET["id"]);?><div class="bodyline">
	<h1><span><?php echo $pageTitle ?></span></h1>
	<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record">
	<div class="box">
		<p class="timelineP">
		   <label for="status">type</label><br />
		   <select name="status" id="status">
				<option value="Orders and Invoices" <?php if($_POST["status"]=="Orders and Invoices") echo "selected=\"selected\""?>>Orders and Invoices</option>
				<option value="Invoices" <?php if($_POST["status"]=="Invoices") echo "selected=\"selected\""?>>Invoices</option>
				<option value="Orders" <?php if($_POST["status"]=="Orders") echo "selected=\"selected\""?>>Orders</option>
		   </select>
		</p>

		<p class="timelineP"><?php $theform->showField("fromdate")?></p>

		<p class="timelineP"><?php $theform->showField("todate")?></p>

		<p id="printP"><br /><input id="print" name="command" type="submit" value="print" class="Buttons" /></p>
		<p id="changeTimelineP"><br /><input name="command" type="submit" value="update" class="smallButtons" /></p>
		<input name="date_order" id="date_order" type="hidden" value="<?php echo $_POST["date_order"]; ?>" />
	</div>

   <div class="fauxP">
   <table border="0" cellpadding="3" cellspacing="0" class="querytable">
      <thead>
	<tr>
	 <th align="center" nowrap="nowrap" class="queryheader" colspan="2">ID</th>
	 <th align="center" nowrap="nowrap" class="queryheader">
	 	<a href="#" onclick="javascript:document.getElementById('date_order').value='<?php echo $dateOrder; ?>'; document.record.submit(); return false;">Date</a>
	 </th>
	 <th nowrap="nowrap" class="queryheader" width="100%" align="left">Client</th>
	 <th align="center" nowrap="nowrap" class="queryheader">Qty.</th>
	 <th align="right" nowrap="nowrap" class="queryheader">Unit Cost</th>
	 <th align="right" nowrap="nowrap" class="queryheader">Cost Ext.</th>
	 <th align="right" nowrap="nowrap" class="queryheader">Unit Price</th>
	 <th align="right" nowrap="nowrap" class="queryheader">Price Ext.</th>
	</tr>
     </thead>
    <?php
	$totalextended=0;
	$totalcostextended=0;
	$totalquantity=0;
	$avgprice=0;
	$avgcost=0;
	$row=1;
	ob_start();
	?><tbody><?php
	while ($therecord=$db->fetchArray($queryresult)){
		if($row==1) $row=2;else $row=1;
		$avgcost+=$therecord["cost"];
		$avgprice+=$therecord["price"];
		$totalquantity+=$therecord["qty"];
		$totalextended+=$therecord["extended"];
		$totalcostextended+=$therecord["extendedcost"];
?>
	<tr class="row<?php echo $row?>">
	 <td>
		<button type="button" class="invisibleButtons" onclick="location.href='<?php echo getAddEditFile($db, "tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883") ?>?id=<?php echo $therecord["id"]?>&amp;backurl=<?php echo urlencode($_SERVER["REQUEST_URI"]); ?>'"><img src="<?php echo APP_PATH ?>common/stylesheet/<?php echo STYLESHEET ?>/image/button-edit.png" align="middle" alt="edit" width="16" height="16" border="0" /></button>
	 </td>
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
	<tr class="norecords"><td colspan="9">No Sales Data for Given Timeframe</td></tr>
	<?php }?>
	</tbody>
	<?php $tbody = ob_get_clean(); ?>
	<tfoot>
	<tr class="queryfooter">
	 <td align="center" >&nbsp;</td>
	 <td align="center" >&nbsp;</td>
	 <td align="center" >&nbsp;</td>
	 <td align="center" >&nbsp;</td>
	 <td align="center" ><?php echo number_format($totalquantity,2)?></td>
	 <td align="right" nowrap="nowrap" >avg. = <?php $numrows?$avgcost=$avgcost/$numrows:$avgcost=0; echo numberToCurrency($avgcost)?></td>
	 <td align="right" ><?php echo numberToCurrency($totalcostextended)?></td>
	 <td align="right" nowrap="nowrap" >avg. = <?php $numrows?$avgprice=$avgprice/$numrows:$avgprice=0; echo numberToCurrency($avgprice)?></td>
	 <td align="right" ><?php echo numberToCurrency($totalextended)?></td>
	</tr>
	</tfoot>
        <?php echo $tbody; ?>
   </table></div></form>
</div>
<?php include("footer.php"); }//end if?>
