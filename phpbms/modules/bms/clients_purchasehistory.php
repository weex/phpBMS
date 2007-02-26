<?php 
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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

	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/clients_functions.php");

	if(!isset($_GET["id"])) reportError(300,"Passed variable not set (id)");
	$clientquerystatement="SELECT firstname,lastname,company FROM clients WHERE id=".$_GET["id"];
	$clientqueryresult=mysql_query($clientquerystatement,$dblink);
	if(!$clientqueryresult) reportError(300,"Could not retrieve client record ".mysql_error($dblink)." -- ".$clientquerystatement);
	$clientrecord=mysql_fetch_array($clientqueryresult);

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

	$temparray=explode("/",$_POST["fromdate"]);
	$mysqlfromdate="\"".$temparray[2]."-".$temparray[0]."-".$temparray[1]."\"";

	$temparray=explode("/",$_POST["todate"]);
	$mysqltodate="\"".$temparray[2]."-".$temparray[0]."-".$temparray[1]."\"";

	//get history
	$querystatement="SELECT invoices.id,
		if(invoices.type=\"Invoice\",invoices.invoicedate,invoices.orderdate) as thedate, 
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
	$queryresult=mysql_query($querystatement);
	if(!$queryresult) reportError(500,"Could Not Retrieve purchase history: ".mysql_error($dblink)." --".$querystatement);

	$numrows=mysql_num_rows($queryresult);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/client.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/datepicker.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>
<?php client_tabs("Purchase History",$_GET["id"]);?><div class="bodyline">
	<h1><?php echo $pageTitle ?></h1>

	<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record">
		<div class="box">
			<p class="timelineP">
			   <label for="status">type</label><br />
			   <select name="status" id="status">
					<option value="Orders/Invoices" <?php if($_POST["status"]=="Orders/Invoices") echo "selected"?>>Orders/Invoices</option>
					<option value="Invoices" <?php if($_POST["status"]=="Invoices") echo "selected"?>>Invoices</option>
					<option value="Orders" <?php if($_POST["status"]=="Orders") echo "selected"?>>Orders</option>
			   </select>								
			</p>
			
			<p class="timelineP">
			   <label for="fromdate">from</label><br />
			   <?php field_datepicker("fromdate",sqlDateFromString($_POST["fromdate"]),0,"",Array("size"=>"10","maxlength"=>"12"),false);?>			
			</p>
	
			<p class="timelineP">
				to<br />
				<?php field_datepicker("todate",sqlDateFromString($_POST["todate"]),0,"",Array("size"=>"10","maxlength"=>"12"),false);?>
			</p>
			<p id="printP"><br /><input id="print" name="command" type="submit" value="print" class="Buttons" /></p>
			<p id="changeTimelineP"><br /><input name="command" type="submit" value="change timeframe/view" class="smallButtons" /></p>
		</div>
	</form>
	<div class="fauxP">
	<table border="0" cellpadding="0" cellspacing="0" class="querytable">
		<tr>
			<th align="left" nowrap class="queryheader" colspan="4">invoice</th>
			<th align="left" nowrap class="queryheader" colspan="3">product</th>		
			<th align="left" nowrap class="queryheader" colspan="2">line item</th>
		</tr>
		<tr>
			<th align="center" nowrap class="queryheader" colspan=2>id</th>
			<th align="left" nowrap class="queryheader">type</th>
			<th align="left" nowrap class="queryheader">date</th>
			<th nowrap class="queryheader" align="left">part num. </th>
			<th width="100%" class="queryheader" align="left">name</th>
			<th align="right" nowrap class="queryheader">price</th>
			<th align="center" nowrap class="queryheader">qty.</th>
			<th align="right" nowrap class="queryheader">ext.</th>
		</tr>
    <?php 
	$totalextended=0;		
	$row=1;
	while ($therecord=mysql_fetch_array($queryresult)){
		$row==1? $row++ : $row--;
		$totalextended=$totalextended+$therecord["extended"];
	?>
	<tr class="row<?php echo $row?>">
		<td >
			<button type="button" class="invisibleButtons" onClick="location.href='<?php echo getAddEditFile(3) ?>?id=<?php echo $therecord["id"]?>'"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image/button-edit.png" align="middle" alt="edit" width="16" height="16" border="0" /></button>
		</td>
		<td align="left" nowrap><?php echo $therecord["id"]?$therecord["id"]:"&nbsp;" ?></td>
		<td align="left" nowrap><?php echo $therecord["type"]?$therecord["type"]:"&nbsp;" ?></td>
		<td align="left" nowrap><?php echo $therecord["thedate"]?formatFromSQLDate($therecord["thedate"]):"&nbsp;" ?></td>
		<td nowrap><?php echo $therecord["partnumber"]?></td>
		<td ><?php echo $therecord["partname"]?></td>
		<td align="right" nowrap><?php echo "\$".number_format($therecord["price"],2)?></td>
		<td align="center" nowrap><?php echo $therecord["qty"]?></td>
		<td align="right" nowrap><?php echo "\$".number_format($therecord["extended"],2)?></td>
	</tr>
    <?php }//end while ?>
    <?php  if(!mysql_num_rows($queryresult)) {?>
	<tr><td colspan="9" align="center" style="padding:0px;"><div class="norecords">No Sales Data for Given Timeframe</div></td></tr>
	<?php }?>	
	<tr>
	 <td align="center" class="queryfooter" colspan=2>&nbsp;</td>
	 <td align="center" class="queryfooter">&nbsp;</td>
	 <td align="center" class="queryfooter">&nbsp;</td>
	 <td class="queryfooter">&nbsp;</td>
	 <td class="queryfooter">&nbsp;</td>
	 <td align="right" class="queryfooter">&nbsp;</td>
	 <td align="center" class="queryfooter">&nbsp;</td>
	 <td align="right" class="queryfooter"><?php echo "\$".number_format($totalextended,2)?></td>
	</tr>
   </table>	
	</div></div><?php include("../../footer.php")?></body>
</html><?php }?>