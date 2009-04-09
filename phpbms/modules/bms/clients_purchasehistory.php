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

	include("../../include/fields.php");

	if(!isset($_GET["id"])) $error = new appError(300,"Passed variable not set (id)");
	$clientquerystatement="SELECT firstname,lastname,company FROM clients WHERE id=".$_GET["id"];
	$clientqueryresult=$db->query($clientquerystatement);

	$clientrecord=$db->fetchArray($clientqueryresult);

	if(!isset($_POST["fromdate"])) $_POST["fromdate"]=dateToString(strtotime("-1 year"));
	if(!isset($_POST["todate"])) $_POST["todate"]=dateToString(mktime());
	if(!isset($_POST["status"])) $_POST["status"]="Orders/Invoices";
	if(!isset($_POST["command"])) $_POST["command"]="show";

	if($_POST["command"]=="print")	{
			$_SESSION["printing"]["whereclause"]="WHERE clients.id=".$_GET["id"];
			$_SESSION["printing"]["dataprint"]="Single Record";
			$fromClient=true;
			require("report/clients_purchasehistory.php");
	} else {

	$pageTitle="Client Purchase History: ";
	if($clientrecord["company"]=="")
		$pageTitle.=$clientrecord["firstname"]." ".$clientrecord["lastname"];
	else
		$pageTitle.=$clientrecord["company"];

	$thestatus="(invoices.type =\"";
	switch($_POST["status"]){
		case "Orders/Invoices":
			$thestatus.="Order\" or invoices.type=\"Invoice\")";
		break;
		case "Invoices":
			$thestatus.="Invoice\")";
		break;
		case "Orders":
			$thestatus.="Order\")";
		break;
	}

	$mysqlfromdate=sqlDateFromString($_POST["fromdate"]);
	$mysqltodate=sqlDateFromString($_POST["todate"]);

	//get history
	$querystatement="SELECT
		invoices.id,
		if(invoices.type=\"Invoice\",invoices.invoicedate,invoices.orderdate) as thedate,
		invoices.type,
		products.partname as partname,
		products.partnumber as partnumber,
		lineitems.quantity as qty,
		lineitems.unitprice*lineitems.quantity as extended,
		lineitems.unitprice as price
		FROM ((clients inner join invoices on clients.id=invoices.clientid)
				inner join lineitems on invoices.id=lineitems.invoiceid)
					inner join products on lineitems.productid=products.id
		WHERE clients.id=".$_GET["id"]."
		and ".$thestatus."
		HAVING
		thedate >=\"".$mysqlfromdate."\"
		and thedate <=\"".$mysqltodate."\"
		ORDER BY thedate,invoices.id;";
	$queryresult=$db->query($querystatement);

	$numrows=$db->numRows($queryresult);

	$phpbms->cssIncludes[] = "pages/client.css";

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

	$phpbms->showTabs("clients entry",7,$_GET["id"]);?><div class="bodyline">

	<h1><?php echo $pageTitle ?></h1>

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
	</form>
	<div class="fauxP">
	<table border="0" cellpadding="0" cellspacing="0" class="querytable">
		<thead>
				<tr>
					<th align="left" nowrap="nowrap" class="queryheader" colspan="4">invoice</th>
					<th align="left" nowrap="nowrap" class="queryheader" colspan="3">product</th>
					<th align="left" nowrap="nowrap" class="queryheader" colspan="2">line item</th>
				</tr>
				<tr>
					<th align="center" nowrap="nowrap" class="queryheader" colspan="2">id</th>
					<th align="left" nowrap="nowrap" class="queryheader">type</th>
					<th align="left" nowrap="nowrap" class="queryheader">date</th>
					<th nowrap="nowrap" class="queryheader" align="left">part num. </th>
					<th width="100%" class="queryheader" align="left">name</th>
					<th align="right" nowrap="nowrap" class="queryheader">price</th>
					<th align="center" nowrap="nowrap" class="queryheader">qty.</th>
					<th align="right" nowrap="nowrap" class="queryheader">ext.</th>
				</tr>
		</thead>

    <?php
	$totalextended=0;
	$row=1;
	ob_start();
	?><tbody><?php
	while ($therecord=$db->fetchArray($queryresult)){
		$row==1? $row++ : $row--;
		$totalextended=$totalextended+$therecord["extended"];
	?>
	<tr class="row<?php echo $row?>">
		<td >
			<button type="button" class="invisibleButtons" onclick="location.href='<?php echo getAddEditFile($db,3) ?>?id=<?php echo $therecord["id"]?>'"><img src="<?php echo APP_PATH ?>common/stylesheet/<?php echo STYLESHEET ?>/image/button-edit.png" align="middle" alt="edit" width="16" height="16" border="0" /></button>
		</td>
		<td align="left" nowrap="nowrap"><?php echo $therecord["id"]?$therecord["id"]:"&nbsp;" ?></td>
		<td align="left" nowrap="nowrap"><?php echo $therecord["type"]?$therecord["type"]:"&nbsp;" ?></td>
		<td align="left" nowrap="nowrap"><?php echo $therecord["thedate"]?formatFromSQLDate($therecord["thedate"]):"&nbsp;" ?></td>
		<td nowrap="nowrap"><?php echo $therecord["partnumber"]?></td>
		<td ><?php echo $therecord["partname"]?></td>
		<td align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["price"])?></td>
		<td align="center" nowrap="nowrap"><?php echo $therecord["qty"]?></td>
		<td align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["extended"])?></td>
	</tr>
    <?php }//end while ?>
    <?php  if(!$db->numRows($queryresult)) {?>
	<tr class="norecords"><td colspan="9" align="center" >No Sales Data for Given Timeframe</td></tr>
	<?php }?>
	</tbody>
    <?php
		$tbody = ob_get_clean();
    ?>
	<tfoot>
	<tr class="queryfooter">
	 <td align="center" colspan="2">&nbsp;</td>
	 <td align="center" >&nbsp;</td>
	 <td align="center" >&nbsp;</td>
	 <td >&nbsp;</td>
	 <td >&nbsp;</td>
	 <td align="right" >&nbsp;</td>
	 <td align="center" >&nbsp;</td>
	 <td align="right" ><?php echo numberToCurrency($totalextended)?></td>
	</tr>
	</tfoot>
	<?php echo $tbody; ?>
   </table>
	</div></div><?php include("footer.php"); } //end if?>