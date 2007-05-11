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

require("../../../include/session.php");
require("../../../include/common_functions.php");
	
class totalReport{
	
	var $selectcolumns;
	var $selecttable;
	var $whereclause="";
	var $group="";
	var $showinvoices=false;
	var $showlineitems=false;
	var $padamount=20;

	function initialize($variables){
		$columnnames=explode(":::",stripslashes($variables["columnnamelist"]));
		$columnvalues=explode(":::",stripslashes($variables["columnvaluelist"]));
		for($i=0;$i<count($columnnames);$i++)
			$this->selectcolumns[$columnnames[$i]]=$columnvalues[$i];
		$this->selectcolumns=array_reverse($this->selectcolumns);
		
		$this->selecttable="((invoices inner join clients on invoices.clientid=clients.id) LEFT JOIN shippingmethods ON shippingmethods.id = invoices.shippingmethodid) LEFT JOIN paymentmethods ON paymentmethods.id=invoices.paymentmethodid";

		if($variables["groupingvaluelist"]) {
			$this->group=explode(":::",stripslashes($variables["groupingvaluelist"]));
			$this->group=array_reverse($this->group);
		}
		$groupnames=explode(":::",stripslashes($variables["groupingnamelist"]));
		foreach($groupnames as $grpname){
			switch($grpname){
				case "Processed by":
					$this->selecttable="(".$this->selecttable." inner join users as users1 on invoices.modifiedby=users1.id)";
				break;
				case "Client Account Manager":
					$this->selecttable="(".$this->selecttable." left join users as users2 on clients.salesmanagerid=users2.id)";
				break;
			}
		}

		$this->whereclause=$_SESSION["printing"]["whereclause"];
		if($this->whereclause=="") $this->whereclause="WHERE invoices.id!=-1";
		if($variables["showinvoices"])$this->showinvoices=true;
		if($variables["showlineitems"])$this->showlineitems=true;
		
		if($this->whereclause!="") $this->whereclause=" WHERE (".substr($this->whereclause,6).") ";
	}
	
		
	function showReportTable(){
		?><table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<th>&nbsp;</th>
		<?php
			foreach($this->selectcolumns as $name=>$column){
				?><th align=right nowrap="nowrap"><?php echo $name?></td><?php
			}//end foreach
		?>
		</tr>
		<?php $this->showGroup($this->group,"",0);?>
		<?php $this->showGrandTotals();?>		
		</table>
		<?php
	}
	
	function showGrandTotals(){
		global $dblink;
		$querystatement="SELECT ";
		foreach($this->selectcolumns as $name=>$column)
			$querystatement.=$column." AS `".$name."`,";
		$querystatement.=" count(invoices.id) as thecount ";
		$querystatement.=" FROM ".$this->selecttable.$this->whereclause;		
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(500,"Bad SQL:".mysql_error($dblink)."<br /><br />".$querystatement);
		$therecord=mysql_fetch_array($queryresult);
		?>
		<tr>
			<td class="grandtotals" align="right">Totals: (<?php echo $therecord["thecount"]?>)</td>
			<?php
				foreach($this->selectcolumns as $name=>$column){
					?><td align="right" class="grandtotals"><?php echo $therecord[$name]?></td><?php
				}//end foreach
			?>
		</tr>
		<?php
	}
	
	function showGroup($group,$where,$indent){
		global $dblink;
		if(!$group){
			if($this->showinvoices)
				$this->showInvoices($where,$indent+$this->padamount);
		} else {
			$groupby=array_pop($group);
				
			$querystatement="SELECT ";
			foreach($this->selectcolumns as $name=>$column)
				$querystatement.=$column." AS `".$name."`,";
			$querystatement.=$groupby." AS thegroup, count(invoices.id) as thecount ";
			$querystatement.=" FROM ".$this->selecttable.$this->whereclause.$where." GROUP BY ".$groupby;
			$queryresult=mysql_query($querystatement,$dblink);
			if(!$queryresult) reportError(500,"Bad SQL:".mysql_error($dblink)."<br /><br />".$querystatement);
			
			while($therecord=mysql_fetch_array($queryresult)){
				
				$showbottom=true;
				if($group or $this->showinvoices) {
					$showbottom=false;
					?>
					<tr><td colspan="<?php echo (count($this->selectcolumns)+1)?>" class="group<?php echo ($indent/$this->padamount)?>" style="padding-left:<?php echo ($indent+2)?>px;"><?php echo $therecord["thegroup"]?>&nbsp;</td></tr>
					<?php }
					
				if($group) {
					$whereadd=$where." AND (".$groupby."= \"".$therecord["thegroup"]."\")";
					$this->showGroup($group,$whereadd,$indent+$this->padamount);
				} elseif($this->showinvoices) {
					if($therecord["thegroup"])
						$this->showInvoices($where." AND (".$groupby."= \"".$therecord["thegroup"]."\")",$indent+$this->padamount);
					else
						$this->showInvoices($where." AND (".$groupby."= \"".$therecord["thegroup"]."\" or isnull(".$groupby.") )",$indent+$this->padamount);
				}
				
				?>
				<tr>
					<td width="100%" style="padding-left:<?php echo ($indent+2)?>px;" class="group<?php echo ($indent/$this->padamount)?>">
						<?php if($showbottom and $therecord["thegroup"]) echo $therecord["thegroup"];else echo "&nbsp;"?>
					</td>
					<?php
						foreach($this->selectcolumns as $name=>$column){
							?><td align="right" class="group<?php echo ($indent/$this->padamount)?>"><?php echo $therecord[$name]?></td><?php
						}//end foreach
					?>
				</tr>
				<?php
			}//end while
		}//endif		
	}//end function
	
	function showInvoices($where,$indent){
		global $dblink;
		
		$querystatement="SELECT ";
		foreach($this->selectcolumns as $name=>$column)
			$querystatement.=$column." AS `".$name."`,";
		$querystatement.=" invoices.id as theid, if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as thename, invoices.invoicedate";
		$querystatement.=" FROM ".$this->selecttable.$this->whereclause.$where." GROUP BY invoices.id";		
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(500,"Bad SQL:".mysql_error($dblink)."<br /><br />".$querystatement);	

		while($therecord=mysql_fetch_array($queryresult)){			
			
			?>
			<tr>
				<td width="100%" style="padding-left:<?php echo ($indent+2)?>px;" class="invoices">
				<?php echo $therecord["theid"]?> -
				<?php echo formatFromSQLDate($therecord["invoicedate"])?> - 
				<?php echo $therecord["thename"]?>					
				</td>
				<?php
					foreach($this->selectcolumns as $name=>$column){
						if($name!="count"){
							?><td align="right" class="invoices"><?php echo $therecord[$name]?></td><?php
						} else echo "<td class=invoices>&nbsp;</td>";
					}//end foreach
				?>
			</tr>
			<?php
			if($this->showlineitems) $this->showLineItems($therecord["theid"],$indent+$this->padamount);
		}//end while

	}//end function
	
	function showLineItems($invoiceid,$indent){
		global $dblink;
		
		$querystatement="SELECT products.partnumber,products.partname,quantity,lineitems.unitprice,quantity*lineitems.unitprice as extended
							FROM (lineitems left join products on lineitems.productid=products.id)
						WHERE lineitems.invoiceid=".$invoiceid;
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(500,"Bad SQL:".mysql_error($dblink)."<br /><br />".$querystatement);	
				
		?>
			<tr><td colspan="<?php echo (count($this->selectcolumns)+1)?>" class="invoices" style="padding-right:40px;padding-left:<?php echo ($indent+2)?>px;">
				<table border="0" cellspacing="0" cellpadding="0" style="border:0px;">
		<?php 
		
		while($therecord=mysql_fetch_array($queryresult)){			
			?>
			<tr>
				<td width="65%" class="lineitems" nowrap="nowrap"><?php echo $therecord["partnumber"]?>&nbsp;&nbsp;<?php echo $therecord["partname"]?></td>
				<td width="24%" class="lineitems" align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["unitprice"])?></td>
				<td width="12%" class="lineitems" align="center" nowrap="nowrap"><?php echo numberToCurrency($therecord["quantity"])?></td>
				<td width="24%" class="lineitems" align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["extended"])?></td>
			</tr>
			<?php
		}
		
		?></table></td></tr><?php 

	}

	function showReport(){
	?>
<head>
<title>Invoice Totals</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css">
<!--
BODY,TH,TD,H1,H2{
	font-size : 10px;
	font-family : sans-serif;
	color : Black; 
}
H1,H2{
	font-size:18px;
	border-bottom:4px solid black;
	margin:0px;	
}
H2{ font-size:12px; border-bottom-width:2px; margin-bottom:10px;}
div {padding:5px;}

TABLE{border:3px solid black;border-bottom-width:1px;border-right-width:1px;}
TH, TD{ padding:2px; border-right:1px solid black;border-bottom:1px solid black;}
TH {
	background-color:#EEEEEE;
	font-size:16px;
	font-weight: bold;
	border-bottom-width:3px;
}
.group0{font-size:14px;border-bottom-width:2px; border-top:1px solid black; font-weight:bold; padding-bottom:5px;}
.group1{font-size:14px;}
.group2{font-size:12px; font-weight:bold;}
.group3{font-size:12px;}
.group4{font-size:10px; font-weight:bold;}
.group5{font-size:10px; font-weight:bold;font-style::italic}

.grandtotals{font-size:14px; border-top:3px double black; font-weight:bold; padding-top:8px;padding-bottom:8px; background-color:#EEEEEE;}

.invoices{font-size:10px; border-bottom-style:dotted; border-bottom-width:2px;}
.lineitems{font-size:9px;border-bottom-style:dotted; border-bottom-width:1px; border-right-width:0px;}
-->
</style>
</head>
<body>
<h1><?php echo $_POST["reporttitle"]?></h1>
<h2>
	<div>
	source:<br />
	<?php echo $_SESSION["printing"]["dataprint"]?>
	</div>
	<div>
	date generated:<br />
	<?php echo dateToString(mktime())." ".timeToString(mktime())?>
	</div>
</h2>
<?php $this->showReportTable();?>
</body>
</html>
	<?php	
	}
}//end class

if(isset($_POST["command"])){
	$myreport= new totalReport();
	$myreport->initialize($_POST);
	
	$myreport->showReport();
} else {
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Invoice Totals</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?php require("../../../head.php")?>
	<script language="JavaScript" type="text/javascript"><!--
		function moveItem(id,direction,theform){
			var additem,removeitem,tempText,tempValue;
			
			if(direction=="to"){
				additem="selected"+id;
				removeitem="available"+id;
			}else{
				removeitem="selected"+id;
				additem="available"+id;
			}
			
			for(i=0;i<theform[removeitem].length;i++)	{
				if (theform[removeitem].options[i].selected) {
					tempText=theform[removeitem].options[i].text;
					tempValue=theform[removeitem].options[i].value;
					theform[removeitem].options[i]=null;
					theform[additem].options[theform[additem].options.length]= new Option(tempText,tempValue);
					i=-1;
				}
			}			
		}//end function
		
		function submitForm(theform){
			var thereturn=true;
			
			if(theform["showwhat"].value=="invoices")theform["showinvoices"].value=1;
			if(theform["showwhat"].value=="lineitems"){
				theform["showinvoices"].value=1;
				theform["showlineitems"].value=1;
			}
			
			for(i=0;i<theform["selectedcolumns"].length;i++)	{
				theform["columnnamelist"].value=theform["columnnamelist"].value+theform["selectedcolumns"].options[i].text+":::";
				theform["columnvaluelist"].value=theform["columnvaluelist"].value+theform["selectedcolumns"].options[i].value+":::";
			}//end for
			theform["columnnamelist"].value=theform["columnnamelist"].value.substring(0,(theform["columnnamelist"].value.length-3));
			theform["columnvaluelist"].value=theform["columnvaluelist"].value.substring(0,(theform["columnvaluelist"].value.length-3));

			for(i=0;i<theform["selectedgroupings"].length;i++)	{
				theform["groupingnamelist"].value=theform["groupingnamelist"].value+theform["selectedgroupings"].options[i].text+":::";
				theform["groupingvaluelist"].value=theform["groupingvaluelist"].value+theform["selectedgroupings"].options[i].value+":::";
			}//end for
			theform["groupingnamelist"].value=theform["groupingnamelist"].value.substring(0,(theform["groupingnamelist"].value.length-3));
			theform["groupingvaluelist"].value=theform["groupingvaluelist"].value.substring(0,(theform["groupingvaluelist"].value.length-3));
			
			if(theform["columnnamelist"].value==""){
				alert("You must have at least one column to display");
				thereturn=false;
			}
			return thereturn;
		}//end function
		// -->
	</script>
	
	<style type="text/css">
		.bodyline{width:550px;padding:4px;margin:10px auto;}
		#selectedgroupings,#availablegroupings,#selectedcolumns,#availablecolumns{width:100%}
		#print{width:75px;margin-right:3px;}
		#cancel{width:75px;}
	</style>
</head>

<body>
<div class="bodyline" style="">
	<h1>Invoice Total Options</h1>	
	<form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" name="totals" onsubmit="return submitForm(this)">
		<p>	
			<label for="reporttitle">report title</label><br />			
			<input type="text" name="reporttitle" id="reporttitle" size="45"/>
		</p>
		<fieldset>
			<legend>Grouping</legend>
			<div class="fauxP">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="50%">
						<label for="selectedgroupings">selected groupings</label><br />
						<select id="selectedgroupings" name="selectedgroupings" size="5" multiple="multiple">
						</select>
						<input type="hidden" name="postedgroupings" />
					</td>
					<td>
						<p><br />
						<input type="button" value="&lt;&lt;" class="Buttons" onclick="moveItem('groupings','to',this.form);" /></p>
						<p><input type="button" value="&gt;&gt;" class="Buttons" onclick="moveItem('groupings','from',this.form);" /></p>
					</td>
					<td width="50%">
						<label for="availablegroupings">available groupings</label><br />
						<select id="availablegroupings" name="availablegroupings" size="5" multiple="multiple">
							<option value="invoices.invoicedate">Invoice Date</option>
							<option value="concat(lpad(month(invoices.invoicedate),2,'0'),' - ',date_format(invoices.invoicedate,'%b'))">Invoice Date - Month</option>
							<option value="concat(quarter(invoices.invoicedate),' - ',year(invoices.invoicedate))">Invoice Date - Quarter</option>
							<option value="year(invoices.invoicedate)">Invoice Date - Year</option>
							<option value="invoices.orderdate">Invoice Date</option>
							<option value="concat(lpad(month(invoices.orderdate),2,'0'),' - ',date_format(invoices.orderdate,'%b'))">Order Date - Month</option>
							<option value="concat(quarter(invoices.orderdate),' - ',year(invoices.orderdate))">Order Date - Quarter</option>
							<option value="year(invoices.orderdate)">Order Date - Year</option>
							<option value="concat(users1.firstname,' ',users1.lastname)">Processed by</option>
							<option value="if(clients.lastname!='',concat(clients.lastname,', ',clients.firstname,if(clients.company!='',concat(' (',clients.company,')'),'')),clients.company)">Client Name / Company</option>
							<option value="concat(users2.firstname,' ',users2.lastname)">Client Account Manager</option>
							<option value="clients.leadsource">Client Lead Source</option>
							<option value="invoices.leadsource">Lead Source</option>
							<option value="paymentmethods.name">Payment Method</option>
							<option value="shippingmethods.name">Shipping Method</option>
							<option value="invoices.shipcountry">Shipping Country</option>
							<option value="invoices.shipstate">Shipping State</option>
							<option value="invoices.shipcity">Shipping City</option>
							<option value="invoices.weborder">Web Orders</option>						
						</select>
						<input type="hidden" name="groupingnamelist" />
						<input type="hidden" name="groupingvaluelist" />
					</td>
				</tr>
			</table></div>

		</fieldset>
		<fieldset>
			<legend>Columns</legend>
			<div class="fauxP">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="50%">
						<label for="selectedcolumns">shown columns</label>
						<select name="selectedcolumns" id="selectedcolumns" size="7">
							<option value="concat('$',format(sum(invoices.totalti),2))">Invoice Total</option>						
						</select>
						<input type="hidden" name="postedcolumns" />
					</td>
					<td>
						<p><br /><input type="button" value="&lt;&lt;" class="Buttons" onclick="moveItem('columns','to',this.form);" /></p>
						<p><input type="button" value="&gt;&gt;" class="Buttons" onclick="moveItem('columns','from',this.form);" /></p>
					</td>
					<td width="50%">
						<label for="availablecolumns">available columns</label><br />
						<select name="availablecolumns" id="availablecolumns" size="7">
							<option value="count(invoices.id)">count</option>						
							<option value="concat('$',format(avg(invoices.totalti),2))">Invoice Total (average)</option>						
							<option value="concat('$',format(sum(invoices.totaltni),2))">Subtotal</option>						
							<option value="concat('$',format(avg(invoices.totaltni),2))">Subtotal (average)</option>						
							<option value="concat('$',format(sum(invoices.tax),2))">Tax</option>						
							<option value="concat('$',format(avg(invoices.tax),2))">Tax (average)</option>						
							<option value="concat('$',format(sum(invoices.shipping),2))">Shipping</option>						
							<option value="concat('$',format(avg(invoices.shipping),2))">Shipping (average)</option>						
							<option value="concat('$',format(sum(invoices.amountpaid),2))">Amount Paid</option>						
							<option value="concat('$',format(avg(invoices.amountpaid),2))">Amount Paid (average)</option>						
							<option value="concat('$',format(sum(invoices.totalti-invoices.amountpaid),2))">Amount Due</option>						
							<option value="concat('$',format(avg(invoices.totalti-invoices.amountpaid),2))">Amount Due (average)</option>						
							<option value="format(sum(invoices.totalweight),2)">Weight</option>						
							<option value="format(avg(invoices.totalwieght),2)">Weight (average)</option>						
							<option value="concat('$',format(sum(invoices.totalcost),2))">Cost</option>						
							<option value="concat('$',format(avg(invoices.totalcost),2))">Cost (average)</option>						
						</select>
						<input type="hidden" name="columnnamelist" />
						<input type="hidden" name="columnvaluelist" />
					</td>
				</tr>
			</table>
			</div>
		</fieldset>
		<fieldset>
			<legend>Options</legend>
			<p>
			<label for="showwhat">information shown</label><br />
			<select name="showwhat" id="showwhat">
				<option selected="selected" value="totals">Totals Only</option>
				<option value="invoices">Invoices</option>
				<option value="lineitems">Invoices &amp; Line Items</option>
			</select>
			<input type="hidden" name="showinvoices"  />
			<input type="hidden" name="showlineitems" />
			</p>
		</fieldset>

		<p align="right">
			<input name="command" type="submit" class="Buttons" id="print" value="print" />
			<input name="cancel" type="button" class="Buttons" id="cancel" value="cancel" onclick="window.close();" />
		</p>
   </form>
</div>

</body>
</html><?php }?>