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
if(!class_exists("phpbmsReport"))
    include("../../../report/report_class.php");

class totalReport extends phpbmsReport{

    var $selectcolumns;
    var $selecttable;
    var $group = "";
    var $showinvoices = false;
    var $showlineitems = false;
    var $padamount = 20;
    var $title = "Totals";

	function totalReport($db, $reportUUID, $tabledefUUID, $variables = NULL){

            parent::phpbmsReport($db, $reportUUID, $tabledefUUID);

            // first we define the available groups
            $this->addGroup("Invoice Date - Year","YEAR(invoices.invoicedate)"); //0
            $this->addGroup("Invoice Date - Quarter","QUARTER(invoices.invoicedate)"); //1
            $this->addGroup("Invoice Date - Month","MONTH(invoices.invoicedate)"); //2
            $this->addGroup("Invoice Date","invoices.invoicedate","date"); //3

            $this->addGroup("Order Date - Year","YEAR(invoices.orderdate)"); //4
            $this->addGroup("Order Date - Quarter","QUARTER(invoices.orderdate)");//5
            $this->addGroup("Order Date - Month","MONTH(invoices.orderdate)");//6
            $this->addGroup("Order Date","invoices.orderdate","date");//7

            $this->addGroup("Required Date - Year","YEAR(invoices.requireddate)");//8
            $this->addGroup("Required Date - Quarter","QUARTER(invoices.requireddate)");//9
            $this->addGroup("Required Date - Month","MONTH(invoices.requireddate)");//10
            $this->addGroup("Required Date","invoices.requireddate","date");//11

            $this->addGroup("Client","if(clients.lastname!='',concat(clients.lastname,', ',clients.firstname,if(clients.company!='',concat(' (',clients.company,')'),'')),clients.company)");//12

            $this->addGroup("Client Sales Person","concat(salesPerson.firstname,' ',salesPerson.lastname)",NULL, "LEFT JOIN users AS salesPerson ON clients.salesmanagerid = salesPerson.uuid");//13

            $this->addGroup("Client Lead Source","clients.leadsource");//14

            $this->addGroup("Invoice Lead Source","invoices.leadsource");//15

            $this->addGroup("Payment Method","paymentmethods.name");//16

            $this->addGroup("Shipping Method","shippingmethods.name");//17
            $this->addGroup("Invoice Shipping Country","invoices.shiptocountry");//18
            $this->addGroup("Invoice Shipping State / Province","invoices.shiptostate");//19
            $this->addGroup("Invoice Shipping Postal Code","invoices.shiptopostalcode");//20
            $this->addGroup("Invoice Shipping City","invoices.shiptocity");//21

            $this->addGroup("Web Order","invoices.weborder","boolean");//22

            $this->addGroup("Invoice billing Country","invoices.country");//23
            $this->addGroup("Invoice Billing State / Province","invoices.state");//24
            $this->addGroup("Invoice Billing Postal Code","invoices.postalcode");//25
            $this->addGroup("Invoice Billing City","invoices.city");//26


            //next we do the columns
            $this->addColumn("Record Count","count(invoices.id)");//0
            $this->addColumn("Invoice Total","sum(invoices.totalti)","currency");//1
            $this->addColumn("Average Invoice Total","avg(invoices.totalti)","currency");//2

            $this->addColumn("Subtotal","sum(invoices.totaltni)","currency");//3
            $this->addColumn("Average Subtotal","avg(invoices.totaltni)","currency");//4

            $this->addColumn("Tax","sum(invoices.tax)","currency");//5
            $this->addColumn("Average Tax","avg(invoices.tax)","currency");//6

            $this->addColumn("Shipping","sum(invoices.shipping)","currency");//7
            $this->addColumn("Average Shipping","avg(invoices.shipping)","currency");//8

            $this->addColumn("Amount Paid","sum(invoices.amountpaid)","currency");//9
            $this->addColumn("Average Amount Paid","avg(invoices.amountpaid)","currency");//10

            $this->addColumn("Amount Due","sum(invoices.totalti - invoices.amountpaid)","currency");//11
            $this->addColumn("Average Amount Due","avg(invoices.totalti - invoices.amountpaid)","currency");//12

            $this->addColumn("Cost","sum(invoices.totalcost)","currency");//13
            $this->addColumn("Average Cost","avg(invoices.totalcost)","currency");//14

            $this->addColumn("Total Weight","sum(invoices.totalweight)","real");//15
            $this->addColumn("Average Total Weight","avg(invoices.totalweight)","real");//16

            $this->selecttable="((`invoices` INNER JOIN `clients` ON `invoices`.`clientid`=`clients`.`uuid`)
                                LEFT JOIN `shippingmethods` ON `shippingmethods`.`uuid` = `invoices`.`shippingmethodid`)
                                LEFT JOIN `paymentmethods` ON `paymentmethods`.`id`=`invoices`.`paymentmethodid`";

	}//end method


        function processFromPost($variables){

            $tempArray = explode("::", $variables["columns"]);

            foreach($tempArray as $id)
                $this->selectcolumns[] = $this->columns[$id];

            $this->selectcolumns = array_reverse($this->selectcolumns);

            if($variables["groupings"] !== ""){

                $this->group = explode("::",$variables["groupings"]);
                $this->group = array_reverse($this->group);

            } else
                $this->group = array();

            foreach($this->group as $grp)
                if($this->groupings[$grp]["table"])
                    $this->selecttable="(".$this->selecttable." ".$this->groupings[$grp]["table"].")";

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

            if($variables["reporttitle"])
                $this->title = $variables["reporttitle"];

        }//end function processFromPost


        function processFromSettings(){

            foreach($this->settings as $key=>$value)
                if(strpos($key, "column") === 0)
                    $this->selectcolumns[substr($key,6)-1] = $this->columns[$value];

            ksort($this->selectcolumns);
            $this->selectcolumns = array_reverse($this->selectcolumns);

            $this->group = array();

            foreach($this->settings as $key=>$value)
                if(strpos($key, "group") === 0)
                    $this->group[substr($key,5)-1] = $value;

            ksort($this->group);
            $this->group = array_reverse($this->group);

            foreach($this->group as $grp)
                if($this->groupings[$grp]["table"])
                    $this->selecttable="(".$this->selecttable." ".$this->groupings[$grp]["table"].")";


            if(isset($this->settings["showWhat"]))
                $showWhat = $this->settings["showWhat"];
            else
                $showWhat = "";

            switch($showWhat){

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

            if(isset($this->settings["reportTitle"]))
                $this->title = $this->settings["reportTitle"];

        }//end function processFromSettings


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
		$querystatement.=" count(invoices.id) as thecount ";
		$querystatement.=" FROM ".$this->selecttable.$this->whereClause;
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
			if($this->showinvoices)
				$this->showInvoices($where,$indent+$this->padamount);
		} else {
			$groupby = array_pop($group);


			$querystatement="SELECT ";
			foreach($this->selectcolumns as $thecolumn)
				$querystatement.=$thecolumn["field"]." AS `".$thecolumn["name"]."`,";
			$querystatement .= $this->groupings[$groupby]["field"]." AS thegroup, count(invoices.id) as thecount ";
			$querystatement .= " FROM ".$this->selecttable.$this->whereClause.$where." GROUP BY ".$this->groupings[$groupby]["field"];
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
				} elseif($this->showinvoices) {
					if($therecord["thegroup"])
						$this->showInvoices($where." AND (".$this->groupings[$groupby]["field"]."= \"".$therecord["thegroup"]."\")",$indent+$this->padamount);
					else
						$this->showInvoices($where." AND (".$this->groupings[$groupby]["field"]."= \"".$therecord["thegroup"]."\" or isnull(".$this->groupings[$groupby]["field"].") )",$indent+$this->padamount);
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


	function showInvoices($where,$indent){

		$querystatement="SELECT ";
		foreach($this->selectcolumns as $thecolumn)
			$querystatement.=$thecolumn["field"]." AS `".$thecolumn["name"]."`,";
		$querystatement.=" invoices.id as theid, if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as thename,
							invoices.orderdate,invoices.invoicedate";
		$querystatement.=" FROM ".$this->selecttable.$this->whereClause.$where." GROUP BY invoices.id";
		$queryresult=$this->db->query($querystatement);

		while($therecord=$this->db->fetchArray($queryresult)){

			?>
			<tr>
				<td width="100%" style="padding-left:<?php echo ($indent+2)?>px;" class="invoices">
				<?php
					echo $therecord["theid"].": ";
					if($therecord["invoicedate"])
						echo "(".formatFromSQLDate($therecord["invoicedate"]).") ";
					else
						echo "<strong>(".formatFromSQLDate($therecord["orderdate"]).")</strong> ";
					echo $therecord["thename"]?>
				</td>
				<?php
					foreach($this->selectcolumns as $thecolumn){
						if($thecolumn["name"] !="count"){
							?><td align="right" class="invoices"><?php echo formatVariable($therecord[$thecolumn["name"]],$thecolumn["format"])?></td><?php
						} else echo "<td class=invoices>&nbsp;</td>";
					}//end foreach
				?>
			</tr>
			<?php
			if($this->showlineitems) $this->showLineItems($therecord["theid"],$indent+$this->padamount);
		}//end while

	}//end function


	function showLineItems($invoiceid,$indent){

		$querystatement = "
			SELECT
				`products`.`partnumber`,
				`products`.`partname`,
				`quantity`,
				`lineitems`.`unitprice`,
				`quantity`*`lineitems`.`unitprice` AS `extended`
			FROM
				(`lineitems` LEFT JOIN `products` ON `lineitems`.`productid`=`products`.`uuid`)
			WHERE
				`lineitems`.`invoiceid`='".$invoiceid."'";
		$queryresult=$this->db->query($querystatement);

		if($this->db->numRows($queryresult)){
			?>
				<tr><td class="invoices" style="padding-left:<?php echo ($indent+2)?>px;">
					<table border="0" cellspacing="0" cellpadding="0" id="lineitems">
						<tr>
							<th width="65%" align="left">product</th>
							<th width="24%" align="right" nowrap="nowrap">price</th>
							<th width="12%" align="right" nowrap="nowrap">qty.</th>
							<th width="24%" align="right" nowrap="nowrap">ext.</th>
						</tr>
			<?php

			while($therecord=$this->db->fetchArray($queryresult)){
				?>
				<tr>
					<td><?php echo $therecord["partnumber"]?>&nbsp;&nbsp;<?php echo $therecord["partname"]?></td>
					<td align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["unitprice"])?></td>
					<td align="right" nowrap="nowrap"><?php echo formatVariable($therecord["quantity"],"real")?></td>
					<td align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["extended"])?></td>
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

            if(!$this->whereClause)
                $this->whereClause = "invoices.id!=-1";

            $this->whereClause = " WHERE ".$this->whereClause;

            $pageTitle = $this->title;

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

        $pageTitle="Invoice Total";
        $phpbms->showMenu = false;
        $phpbms->cssIncludes[] = "pages/totalreports.css";
        $phpbms->jsIncludes[] = "modules/bms/javascript/totalreports.js";

        include("header.php");

        ?>

        <div class="bodyline">
            <h1>Invoice Total Options</h1>
            <form id="GroupForm" action="<?php echo str_replace("&", "&amp;", $_SERVER["REQUEST_URI"]) ?>" method="post" name="GroupForm">

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

}//end class totalReport


/**
 * PROCESSING
 * =============================================================================
 */
if(!isset($noOutput)){

    require("../../../include/session.php");

    checkForReportArguments();

    $report = new totalReport($db, $_GET["rid"], $_GET["tid"]);

    if(isset($_POST["columns"])){

        $report->setupFromPrintScreen();
        $report->processFromPost($_POST);
        $report->showReport();

    } elseif(isset($report->settings["column1"])){

        $report->setupFromPrintScreen();
        $report->processFromSettings();
        $report->showReport();

    } else {

        $report->showSelectScreen();

    }//endif

}//endif

/**
 * When adding a new report record, the add/edit needs to know what the class
 * name is so that it can instantiate it, and grab it's default settings.
 */
if(isset($addingReportRecord))
    $reportClass ="totalReport";

?>
