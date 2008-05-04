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
	
class totalReport{
	
	var $selectcolumns;
	var $selecttable;
	var $whereclause="";
	var $group="";
	var $showinvoices=false;
	var $showlineitems=false;
	var $padamount=20;

	function totalReport($db,$variables = NULL){
		$this->db = $db;

		// first we define the available groups
		$this->addGroup("Invoice ID","invoices.id"); //0
		$this->addGroup("Product","concat(products.partnumber,' - ',products.partname)"); //1
		$this->addGroup("Product Category","concat(productcategories.id,' - ',productcategories.name)",NULL,"INNER JOIN productcategories ON products.categoryid=productcategories.id"); //2

		$this->addGroup("Invoice Date - Year","YEAR(invoices.invoicedate)"); //3
		$this->addGroup("Invoice Date - Quarter","QUARTER(invoices.invoicedate)"); //4
		$this->addGroup("Invoice Date - Month","MONTH(invoices.invoicedate)"); //5
		$this->addGroup("Invoice Date","invoices.invoicedate","date"); //6

		$this->addGroup("Order Date - Year","YEAR(invoices.orderdate)"); //7
		$this->addGroup("Order Date - Quarter","QUARTER(invoices.orderdate)");//8
		$this->addGroup("Order Date - Month","MONTH(invoices.orderdate)");//9
		$this->addGroup("Order Date","invoices.orderdate","date");//10

		$this->addGroup("Client","if(clients.lastname!='',concat(clients.lastname,', ',clients.firstname,if(clients.company!='',concat(' (',clients.company,')'),'')),clients.company)");//11

		$this->addGroup("Client Sales Person","concat(salesPerson.firstname,' ',salesPerson.lastname)",NULL, "LEFT JOIN users AS salesPerson ON clients.salesmanagerid = salesPerson.id");//12

		$this->addGroup("Client Lead Source","clients.leadsource");//13

		$this->addGroup("Invoice Lead Source","invoices.leadsource");//14
		
		$this->addGroup("Payment Method","paymentmethods.name");//15
		
		$this->addGroup("Shipping Method","shippingmethods.name");//16
		$this->addGroup("Invoice Shipping Country","invoices.shiptocountry");//17
		$this->addGroup("Invoice Shipping State / Province","invoices.shiptostate");//18
		$this->addGroup("Invoice Shipping Postal Code","invoices.shiptopostalcode");//19
		$this->addGroup("Invoice Shipping City","invoices.shiptocity");//20

		$this->addGroup("Web Order","invoices.weborder","boolean");//21

		$this->addGroup("Invoice billing Country","invoices.country");//22
		$this->addGroup("Invoice Billing State / Province","invoices.state");//23
		$this->addGroup("Invoice Billing Postal Code","invoices.postalcode");//24
		$this->addGroup("Invoice Billing City","invoices.city");//25

		//next we do the columns		
		$this->addColumn("Record Count","count(lineitems.id)");//0
		$this->addColumn("Extended Price","sum(lineitems.unitprice*lineitems.quantity)","currency");//1
		$this->addColumn("Average Extended Price","avg(lineitems.unitprice*lineitems.quantity)","currency");//2
		$this->addColumn("Unit Price","sum(lineitems.unitprice)","currency");//3
		$this->addColumn("Average Unit Price","avg(lineitems.unitprice)","currency");//4
		$this->addColumn("Quantity","sum(lineitems.quantity)","real");//5
		$this->addColumn("Average Quantity","avg(lineitems.quantity)","real");//6
		$this->addColumn("Unit Cost","sum(lineitems.unitcost)","currency");//7
		$this->addColumn("Average Unit Cost","avg(lineitems.unitcost)","currency");//8
		$this->addColumn("Extended Cost","sum(lineitems.unitcost*lineitems.quantity)","currency");//9
		$this->addColumn("Average Extended Cost","avg(lineitems.unitcost*lineitems.quantity)","currency");//10
		$this->addColumn("Unit Weight","sum(lineitems.unitweight)","real");//11
		$this->addColumn("Average Unit Weight","avg(lineitems.unitweight)","real");//12
		$this->addColumn("Extended Unit Weight","sum(lineitems.unitweight*lineitems.quantity)","real");//13
		$this->addColumn("Extended Average Unit Weight","avg(lineitems.unitweight*lineitems.quantity)","real");//14
		
						
		if($variables){
			$tempArray = explode("::", $variables["columns"]);

			foreach($tempArray as $id)
				$this->selectcolumns[] = $this->columns[$id];				
			$this->selectcolumns = array_reverse($this->selectcolumns);
						
			//change
			$this->selecttable="(((((lineitems left join products on lineitems.productid=products.id) 
									inner join invoices on lineitems.invoiceid=invoices.id) 
									inner join clients on invoices.clientid=clients.id) 
									LEFT JOIN shippingmethods ON shippingmethods.name=invoices.shippingmethodid)
									LEFT JOIN paymentmethods ON paymentmethods.name=invoices.paymentmethodid)
									";
	
			if($variables["groupings"] !== ""){
				$this->group = explode("::",$variables["groupings"]);
				$this->group = array_reverse($this->group);
			} else
				$this->group = array();
			
			foreach($this->group as $grp){
				if($this->groupings[$grp]["table"])
					$this->selecttable="(".$this->selecttable." ".$this->groupings[$grp]["table"].")";
			}
	
			$this->whereclause=$_SESSION["printing"]["whereclause"];
			if($this->whereclause=="") $this->whereclause="WHERE invoices.id!=-1";
			
			switch($variables["showwhat"]){
				case "invoices":
					$this->showinvoices = true;
					$this->showlineitems = false;
					break;
					
				case "lineitems":
					$this->showinvoices = true;
					$this->showlineitems = true;
					break;
					
				default:
					$this->showinvoices = false;
					$this->showlineitems = false;
			}// endswitch
						
			if($this->whereclause!="") $this->whereclause=" WHERE (".substr($this->whereclause,6).") ";
		}// endif
	}//end method
	
		
	function addGroup($name, $field, $format = NULL, $tableAddition = NULL){
		$temp = array();
		$temp["name"] = $name;
		$temp["field"] = $field;
		$temp["format"] = $format;
		$temp["table"] = $tableAddition;
		
		$this->groupings[] = $temp;
	}//end method
	
	
	function addColumn($name, $field, $format = NULL){
		$temp = array();
		$temp["name"] = $name;
		$temp["field"] = $field;
		$temp["format"] = $format;
		
		$this->columns[] = $temp;
	}//end method


	function showReportTable(){
		?><table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<th>&nbsp;</th>
		<?php
			foreach($this->selectcolumns as $thecolumn){
				?><th align="right"><?php echo $thecolumn["name"]?></th><?php
			}//end foreach
		?>
		</tr>
		<?php $this->showGroup($this->group,"",10);?>
		<?php $this->showGrandTotals();?>		
		</table>
		<?php
	}
	
	function showGrandTotals(){

		$querystatement="SELECT ";
		foreach($this->selectcolumns as $thecolumn)
			$querystatement.=$thecolumn["field"]." AS `".$thecolumn["name"]."`,";
		$querystatement.=" count(lineitems.id) as thecount ";
		$querystatement.=" FROM ".$this->selecttable.$this->whereclause;		
		$queryresult=$this->db->query($querystatement);

		$therecord=$this->db->fetchArray($queryresult);
		?>
		<tr>
			<td class="grandtotals" align="right">Totals: (<?php echo $therecord["thecount"]?>)</td>
			<?php
				foreach($this->selectcolumns as $thecolumn){
					?><td align="right" class="grandtotals"><?php echo formatVariable($therecord[$thecolumn["name"]],$thecolumn["format"])?></td><?php
				}//end foreach
			?>
		</tr>
		<?php
	}
	
	function showGroup($group,$where,$indent){

		if(!$group){
			if($this->showlineitems)
				$this->showLineItems($where,$indent+$this->padamount);
		} else {
			$groupby = array_pop($group);
			
				
			$querystatement="SELECT ";
			foreach($this->selectcolumns as $thecolumn)
				$querystatement.=$thecolumn["field"]." AS `".$thecolumn["name"]."`,";
			$querystatement .= $this->groupings[$groupby]["field"]." AS thegroup, count(lineitems.id) as thecount ";
			$querystatement .= " FROM ".$this->selecttable.$this->whereclause.$where." GROUP BY ".$this->groupings[$groupby]["field"];
			$queryresult=$this->db->query($querystatement);

			while($therecord=$this->db->fetchArray($queryresult)){
				
				$showbottom=true;
				if($group or $this->showinvoices) {
					$showbottom=false;
					?>
					<tr><td colspan="<?php echo (count($this->selectcolumns)+1)?>" class="group" style="padding-left:<?php echo ($indent+2)?>px;"><?php echo $this->groupings[$groupby]["name"].": <strong>".formatVariable($therecord["thegroup"],$this->groupings[$groupby]["format"])."</strong>"?>&nbsp;</td></tr>
					<?php 
				}//endif
					
				if($group) {
					$whereadd = $where." AND (".$this->groupings[$groupby]["field"]."= \"".$therecord["thegroup"]."\"";					
					if(!$therecord["thegroup"])
						$whereadd .= " OR ISNULL(".$this->groupings[$groupby]["field"].")";
					$whereadd .= ")";
					$this->showGroup($group,$whereadd,$indent+$this->padamount);
				} elseif($this->showlineitems) {
					if($therecord["thegroup"])
						$this->showLineItems($where." AND (".$this->groupings[$groupby]["field"]."= \"".$therecord["thegroup"]."\")",$indent+$this->padamount);
					else
						$this->showLineItems($where." AND (".$this->groupings[$groupby]["field"]."= \"".$therecord["thegroup"]."\" or isnull(".$this->groupings[$groupby]["field"].") )",$indent+$this->padamount);
				}//endif
				
				?>
				<tr>
					<td width="100%" style=" <?php 
						echo "padding-left:".($indent+2)."px";
					?>" class="groupFooter">
						<?php echo $this->groupings[$groupby]["name"].": <strong>".formatVariable($therecord["thegroup"],$this->groupings[$groupby]["format"])."</strong>&nbsp;";?>
					</td>					
					<?php
						foreach($this->selectcolumns as $thecolumn){
							?><td align="right" class="groupFooter"><?php echo formatVariable($therecord[$thecolumn["name"]],$thecolumn["format"])?></td><?php
						}//end foreach
					?>
				</tr>
				<?php
			}//end while
		}//endif		
	}//end function

	
	function showLineItems($where,$indent){
		
		$querystatement="SELECT lineitems.invoiceid, 
						if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as thename, 
						invoices.invoicedate, invoices.orderdate,
						lineitems.id,products.partnumber,products.partname,quantity,lineitems.unitprice,quantity*lineitems.unitprice as extended
						FROM ".$this->selecttable.$this->whereclause.$where." GROUP BY lineitems.id ";
		$queryresult=$this->db->query($querystatement);

		if($this->db->numRows($queryresult)){
			?>
				<tr><td class="invoices" style="padding-left:<?php echo ($indent+2)?>px;">
					<table border="0" cellspacing="0" cellpadding="0" id="lineitems">
						<tr>
							<th align="left">id</th>
							<th align="left">date</th>
							<th width="20%" align="left" >client</th>
							<th width="60%" align="left">product</th>
							<th width="9%" align="right" nowrap="nowrap">price</th>
							<th width="8%" align="right" nowrap="nowrap">qty.</th>
							<th width="7%" align="right" nowrap="nowrap">ext.</th>
						</tr>
			<?php 
			
			while($therecord=$this->db->fetchArray($queryresult)){			
				?>
				<tr>			
					<td nowrap="nowrap"><?php echo $therecord["invoiceid"]?></td>
					<td nowrap="nowrap"><?php if($therecord["invoicedate"]) echo formatFromSQLDate($therecord["invoicedate"]); else echo "<strong>".formatFromSQLDate($therecord["orderdate"])."</strong>";?></td>
					<td><?php echo $therecord["thename"]?></td>
					<td width="60%" nowrap="nowrap"><?php echo $therecord["partnumber"]?>&nbsp;&nbsp;<?php echo $therecord["partname"]?></td>
					<td width="9%" align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["unitprice"])?></td>
					<td width="8%" align="center" nowrap="nowrap"><?php echo formatVariable($therecord["quantity"],"real")?></td>
					<td width="7%" align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["extended"])?></td>
				</tr>
				<?php
			}// endwhile
			
			?></table></td>
			<?php 
				for($i=1;$i < count($this->selectcolumns); $i++)
					echo "<td>&nbsp;</td>"
			?>
			</tr><?php 
		}// endif
	
	}//end method


	function showReport(){
		
		if($_POST["reporttitle"])
			$pageTitle = $_POST["reporttitle"];
		else			
			$pageTitle = "Line Item Totals";
			
		
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo APP_PATH ?>common/stylesheet/<?php echo STYLESHEET ?>/pages/totalreports.css" rel="stylesheet" type="text/css" />
<title><?php echo $pageTitle?></title>
</head>

<body>
	<div id="toprint">
		<h1><span><?php echo $pageTitle?></span></h1>
		<h2>Source: <?php echo $_SESSION["printing"]["dataprint"]?></h2>
		<h2>Date: <?php echo dateToString(mktime())." ".timeToString(mktime())?></h2>
	
		<?php $this->showReportTable();?>
	</div>
</body>
</html><?php	

	}// end method


	function showOptions($what){
		?><option value="0">----- Choose One -----</option>
		<?php
		$i=0;
		
		foreach($this->$what as $value){
			?><option value="<?php echo $i+1; ?>"><?php echo $value["name"];?></option>
			<?php
			$i++;
		}// endforeach
		
	}//end mothd

	
	function showSelectScreen(){
    
        global  $phpbms;

        $pageTitle="Line Items Total";
        $phpbms->showMenu = false;		
        $phpbms->cssIncludes[] = "pages/totalreports.css";		
        $phpbms->jsIncludes[] = "modules/bms/javascript/totalreports.js";
        
        include("header.php");

        ?>

        <div class="bodyline">
            <h1>Line Items Total Options</h1>
            <form id="GroupForm" action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" name="GroupForm">
            
                <fieldset>
                    
                    <legend>report</legend>
                    <p>	
                        <label for="reporttitle">report title</label><br />			
                        <input type="text" name="reporttitle" id="reporttitle" size="45"/>
                    </p>
                
		</fieldset>
                
                <fieldset>
                    
                    <legend>groupings</legend>
                    <input id="groupings" type="hidden" name="groupings"/>
                    <div id="theGroups">
                        <div id="Group1">
                            <select id="Group1Field">
                                <?php $this->showOptions("groupings")?>
                            </select>
                            <button type="button" id="Group1Minus" class="graphicButtons buttonMinusDisabled"><span>-</span></button>
                            <button type="button" id="Group1Plus" class="graphicButtons buttonPlus"><span>+</span></button>
                        </div>
                    </div>
                    
                </fieldset>
		
		<fieldset>
			
			<legend>columns</legend>
			<input id="columns" type="hidden" name="columns"/>
			<div id="theColumns">
				<div id="Column1">
					<select id="Column1Field">
						<?php $this->showOptions("columns")?>
					</select>
					<button type="button" id="Column1Minus" class="graphicButtons buttonMinusDisabled"><span>-</span></button>
					<button type="button" id="Column1Plus" class="graphicButtons buttonPlus"><span>+</span></button>
				</div>
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
			</p>
		</fieldset>
                
                <p align="right">
                    <button id="print" type="button" class="Buttons">Print</button>
                    <button id="cancel" type="button" class="Buttons">Cancel</button>
                </p>
                
            </form>
        </div>

        <?php
        
        include("footer.php");
    }//end method
	
}//end class


// Processing ===================================================================================================================
if(!isset($dontProcess)){
	if(isset($_POST["columns"])){
		$myreport= new totalReport($db,$_POST);	
		$myreport->showReport();
	} else {
		$myreport = new totalReport($db);	
		$myreport->showSelectScreen();
	}
}?>