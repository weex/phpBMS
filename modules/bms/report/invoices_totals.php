<?php 
require("../../../include/session.php");
	
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
		
		$this->selecttable="(invoices inner join clients on invoices.clientid=clients.id)";

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
		?><table border=0 cellspacing=0 cellpadding=0>
		<tr>
			<th>&nbsp;</th>
		<?php
			foreach($this->selectcolumns as $name=>$column){
				?><th align=right nowrap><?php echo $name?></td><?php
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
		if(!$queryresult) reportError(500,"Bad SQL:".mysql_error()."<br><br>".$querystatement);
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
			if(!$queryresult) reportError(500,"Bad SQL:".mysql_error()."<br><br>".$querystatement);
			
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
		$querystatement.=" invoices.id as theid, if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as thename, Date_Format(invoices.invoicedate,\"%c/%e/%Y\") as thedate";
		$querystatement.=" FROM ".$this->selecttable.$this->whereclause.$where." GROUP BY invoices.id";		
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(500,"Bad SQL:".mysql_error()."<br><br>".$querystatement);	

		while($therecord=mysql_fetch_array($queryresult)){			
			
			?>
			<tr>
				<td width="100%" style="padding-left:<?php echo ($indent+2)?>px;" class="invoices">
				<?php echo $therecord["theid"]?> -
				<?php echo $therecord["thedate"]?> - 
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
		if(!$queryresult) reportError(500,"Bad SQL:".mysql_error()."<br><br>".$querystatement);	
				
		?>
			<tr><td colspan="<?php echo (count($this->selectcolumns)+1)?>" class="invoices" style="padding-right:40px;padding-left:<?php echo ($indent+2)?>px;">
				<table border=0 cellspacing=0 cellpadding=0 style="border:0px;">
		<?php 
		
		while($therecord=mysql_fetch_array($queryresult)){			
			?>
			<tr>
				<td width="65%" class="lineitems" nowrap><?php echo $therecord["partnumber"]?>&nbsp;&nbsp;<?php echo $therecord["partname"]?></td>
				<td width="24%" class="lineitems" align="right" nowrap><?php echo "\$".number_format($therecord["unitprice"],2)?></td>
				<td width="12%" class="lineitems" align="center" nowrap><?php echo number_format($therecord["quantity"],2)?></td>
				<td width="24%" class="lineitems" align="right" nowrap><?php echo "\$".number_format($therecord["extended"],2)?></td>
			</tr>
			<?php
		}
		
		?></table></td></tr><?php 

	}

	function showReport(){
	?>
<head>
<title>Invoice Totals</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
	source:<br>
	<?php echo $_SESSION["printing"]["dataprint"]?>
	</div>
	<div>
	date generated:<br>
	<?php echo date("m/d/Y H:i");?>
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
//	echo "<PRE>";
//	var_dump($_POST);
//	echo "</PRE>";
	
	$myreport->showReport();
} else {
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Invoice Totals</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
	<script language="javascript">
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
	</script>
	
</head>

<body>
<div class="bodyline" style="width:550px;padding:4px;">
	<div class="searchtitle">Invoice Total Options</div>	
	<form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" name="totals" onSubmit="return submitForm(this)">
		<div>
			report title<br>			
			<input type="text" name="reporttitle" value="" style="width:100%">
		</div>
		<div class="box">
			<strong>Grouping</strong><br>
			<table border=0 cellspacing=0 cellpadding=0>
				<tr>
					<td width="50%">
						selected groupings<br>
						<select name="selectedgroupings" size="5" style="width:100%" multiple>
						</select>
						<input type="hidden" name="postedgroupings" value="">
					</td>
					<td>
						<div><br>
							<input type="button" value="&lt;&lt;" class="Buttons" onClick="moveItem('groupings','to',this.form);"><br><br>
							<input type="button" value="&gt;&gt;" class="Buttons" onClick="moveItem('groupings','from',this.form);">							
						</div>
					</td>
					<td width="50%">
						available groupings<br>
						<select name="availablegroupings" size="5" style="width:100%" multiple>
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
							<option value="invoices.paymentmethod">Payment Method</option>
							<option value="invoices.shippingmethod">Shipping Method</option>
							<option value="invoices.shipcountry">Shipping Country</option>
							<option value="invoices.shipstate">Shipping State</option>
							<option value="invoices.shipcity">Shipping City</option>
							<option value="invoices.status">Invoice Status</option>
							<option value="invoices.weborder">Web Orders</option>						
						</select>
						<input type="hidden" name="groupingnamelist" value="">
						<input type="hidden" name="groupingvaluelist" value="">
					</td>
				</tr>
			</table>
		</div>
		<div class="box">
			<strong>Columns</strong><br>
			<table border=0 cellspacing=0 cellpadding=0>
				<tr>
					<td width="50%">
						shown columns<br>
						<select name="selectedcolumns" size="7" style="width:100%">
							<option value="concat('$',format(sum(invoices.totalti),2))">Invoice Total</option>						
						</select>
						<input type="hidden" name="postedcolumns" value="">
					</td>
					<td>
						<div><br>
							<input type="button" value="&lt;&lt;" class="Buttons" onClick="moveItem('columns','to',this.form);"><br><br>
							<input type="button" value="&gt;&gt;" class="Buttons" onClick="moveItem('columns','from',this.form);">							
						</div>
					</td>
					<td width="50%">
						available columns<br>
						<select name="availablecolumns" size="7" style="width:100%">
							<option value="count(invoices.id)">count</option>						
							<option value="concat('$',format(avg(invoices.totalti),2))">Invoice Total (average)</option>						
							<option value="concat('$',format(sum(invoices.totaltni),2))">Subtotal</option>						
							<option value="concat('$',format(avg(invoices.totaltni),2))">Subtotal (average)</option>						
							<option value="concat('$',format(sum(invoices.tax),2))">Tax</option>						
							<option value="concat('$',format(avg(invoices.tax),2))">Tax (average)</option>						
							<option value="concat('$',format(sum(invoices.tax),2))">Shipping</option>						
							<option value="concat('$',format(avg(invoices.tax),2))">Shipping (average)</option>						
							<option value="concat('$',format(sum(invoices.amountpaid),2))">Amount Paid</option>						
							<option value="concat('$',format(avg(invoices.amountpaid),2))">Amount Paid (average)</option>						
							<option value="concat('$',format(sum(invoices.totalti-invoices.amountpaid),2))">Amount Due</option>						
							<option value="concat('$',format(avg(invoices.totalti-invoices.amountpaid),2))">Amount Due (average)</option>						
							<option value="format(sum(invoices.totalweight),2)">Weight</option>						
							<option value="format(avg(invoices.totalwieght),2)">Weight (average)</option>						
							<option value="concat('$',format(sum(invoices.totalcost),2))">Cost</option>						
							<option value="concat('$',format(avg(invoices.totalcost),2))">Cost (average)</option>						
						</select>
						<input type="hidden" name="columnnamelist" value="">
						<input type="hidden" name="columnvaluelist" value="">
					</td>
				</tr>
			</table>
		</div>
		<div class=box>
			<strong>Additional Options</strong><br>
			information shown<br>
			<select name="showwhat">
				<option selected value="totals">Totals Only</option>
				<option value="invoices">Invoices</option>
				<option value="lineitems">Invoices &amp; Line Items</option>
			</select>
			<input type="hidden" name="showinvoices" value="">
			<input type="hidden" name="showlineitems" value="">
		</div>

		<div align="right" class="recordbottom">
			<input name="command" type="submit" class="Buttons" id="print" value="print" style="width:75px;margin-right:3px;">
			<input name="cancel" type="button" class="Buttons" id="cancel" value="cancel" style="width:75px;" onClick="window.close();">	 
		</div>
   </form>
</div>

</body>
</html><?php }?>