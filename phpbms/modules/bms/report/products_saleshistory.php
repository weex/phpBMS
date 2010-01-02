<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
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

class salesHistoryReport extends phpbmsReport{

    var $fromDate;
    var $toDate;
    var $view;
    var $productQueryresult;
    var $dataPrint;

    function salesHistoryReport($db, $reportUUID, $tabledefUUID){

        parent::phpbmsReport($db, $reportUUID, $tabledefUUID);

        //$this->checkForDefaultSettings();

    }//end function init


    function initialize($variables){

        if(!isset($variables["fromdate"]) || !isset($variables["todate"]) || !isset($variables["status"]))
            $error = new appError(300, "Missing Passed Parameters");

        $this->fromDate = $variables["fromdate"];
        $this->toDate = $variables["todate"];
        $this->view = $variables["status"];

        $this->dataPrint = $_SESSION["printing"]["dataprint"];

        if(!$this->sortOrder)
            $this->sortOrder = "  ORDER BY products.partnumber ";

        if(!$this->whereClause)
            $this->whereClause = "products.id!=-1";

        $this->whereClause = " WHERE (".$this->whereClause.") ";

        $querystatement = "
            SELECT
                products.uuid,
                products.partnumber,
                products.partname
            FROM
                products
            ".$this->whereClause.$this->sortOrder;

        $this->productQueryresult = $this->db->query($querystatement);

    }//end function initialize


    function generateSingleHistory($productUUID){

        $theStatus = "(invoices.type = '";

        switch($this->view){

            case "Orders and Invoices":
                $theStatus .= "Order' OR invoices.type ='Invoice')";
                $searchDate = "orderdate";
                break;

            case "Invoices":
                $theStatus .= "Invoice')";
                $searchDate = "invoicedate";
                break;

            case "Orders":
                $theStatus .= "Order')";
                $searchDate = "orderdate";
                break;

        }//endswitch

        $mysqlFromDate = sqlDateFromString($this->fromDate);
	$mysqlToDate = sqlDateFromString($this->toDate);

	$querystatement = "
            SELECT
                `invoices`.`id`,
                `invoices`.`orderdate`,
                `invoices`.`invoicedate`,
                IF(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) AS `client`,
                `lineitems`.`quantity` AS `qty`,
                `lineitems`.`unitprice`*`lineitems`.`quantity` AS `extended`,
                `lineitems`.`unitprice` AS `price`,
                `lineitems`.`unitcost` AS `cost`,
                `lineitems`.`unitcost`*`lineitems`.`quantity` AS extendedcost
            FROM
                ((products INNER JOIN lineitems on products.uuid=lineitems.productid)
                INNER JOIN `invoices` ON lineitems.invoiceid=invoices.id)
                INNER JOIN `clients` on `invoices`.`clientid`=`clients`.`uuid`
            WHERE
                `products`.`uuid`='".$productUUID."'
                AND
                `invoices`.".$searchDate.">='".$mysqlFromDate."'
                AND
                `invoices`.".$searchDate."<='".$mysqlToDate."'
                AND
                ".$theStatus."
            ORDER BY
                `invoices`.`invoicedate`,
                `invoices`.`orderdate`
	    ";

        $queryresult = $this->db->query($querystatement);

        ob_start();

        ?>
        <table border="0" cellpadding="3" cellspacing="0">
            <thead>
                <tr>
                    <th align="center" nowrap="nowrap" >ID</th>
                    <th align="center" nowrap="nowrap" >Order Date</th>
                    <th align="center" nowrap="nowrap" >Invoice. Date</th>
                    <th nowrap="nowrap"  width="100%" align="left">Client</th>
                    <th align="center" nowrap="nowrap" >Qty.</th>
                    <th align="right" nowrap="nowrap" >Unit Cost</th>
                    <th align="right" nowrap="nowrap" >Cost Ext.</th>
                    <th align="right" nowrap="nowrap" >Unit Price</th>
                    <th align="right" nowrap="nowrap">Price Ext.</th>
                </tr>
            </thead>
        <?php

            $totalextended = 0;
            $totalcostextended = 0;
            $totalquantity = 0;
            $avgprice = 0;
            $avgcost = 0;

            $numrows = $this->db->numRows($queryresult);

            while ($therecord = $this->db->fetchArray($queryresult)){

                $avgcost += $therecord["cost"];
                $avgprice += $therecord["price"];
                $totalquantity += $therecord["qty"];
                $totalextended += $therecord["extended"];
                $totalcostextended += $therecord["extendedcost"];
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
          <?php }//endwhile ?>
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

        $output = ob_get_contents();
        ob_end_clean();

        return $output;

    }//end function generateSingleHistory



    function generate(){


        ob_start();
        ?>

        <h1>Product Sales History</h1>

	<ul>
	    <li>
                source:<br />
                <?php echo formatVariable($this->dataPrint); ?>
            </li>
            <li>
                date generated:<br />
                <?php echo dateToString(mktime())." ".timeToString(mktime()); ?>
            </li>
            <li>
                view:<br />
                <?php echo $this->view; ?>
            </li>
            <li>
                from:<br />
                <?php echo $this->fromDate; ?>
            </li>
            <li>
                to:<br />
                <?php echo $this->toDate; ?>
            </li>
	</ul>

        <?php

        $this->reportOutput = ob_get_contents();
        ob_end_clean();

        while($therecord = $this->db->fetchArray($this->productQueryresult)){

            $this->reportOutput .= '<h2>'.$therecord["partnumber"].'<br />'.$therecord["partname"].'</h2>';

            $this->reportOutput .= $this->generateSingleHistory($therecord["uuid"]);

        }//endwhile

    }//end function generate


    function output(){

        global $phpbms;
        $db = &$this->db;

        $phpbms->cssIncludes[] = "reports.css";
        $phpbms->cssIncludes[] = "pages/bms/clienthistoryreport.css";

        $phpbms->showMenu = false;
        $phpbms->showFooter = false;

        include("header.php");

        echo $this->reportOutput;

        include("footer.php");

    }//end function output


    function displayOptions(){

        global $phpbms;
        $db = &$this->db;

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
        <form action="<?php echo str_replace("&", "&amp;", $_SERVER["REQUEST_URI"]); ?>" method="post" name="totals" onsubmit="return validateForm(this)">

            <div class="bodyline" id="reportOptions">

                <h1 id="topTitle"><span>Product Sales History Options</span></h1>

                <fieldset>
                    <legend>time frame</legend>

                    <p id="fromP"><?php $theform->showField("fromdate");?></p>

                    <p><?php $theform->showField("todate");?></p>
                </fieldset>

                <p>
                    <label for="status">include products from...<br /></label>
                    <select id="status" name="status">
                        <option value="Orders and Invoices" selected="selected">Orders and Invoices</option>
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

    }//end function displayOptions

}//end class  salesHistoryReport


/**
 * PROCESSING
 * =============================================================================
 */
if(!isset($noOutput)){

    //IE needs caching to be set to private in order to display PDFS
    session_cache_limiter('private');

    require_once("../../../include/session.php");

    checkForReportArguments();

    $report = new salesHistoryReport($db, $_GET["rid"], $_GET["tid"]);

    if(!isset($_POST["command"]) && !isset($_GET["status"]))
        $report->displayOptions();
    else{

        if(isset($_GET["status"])){

            $_POST["status"] = $_GET["status"];
            $_POST["fromdate"] = $_GET["fromdate"];
            $_POST["todate"] = $_GET["todate"];

        }//endif

        //need to set post variables here

        $report->setupFromPrintScreen();
        $report->initialize($_POST);
        $report->generate();
        $report->output();

    }//endif


}//end if

/**
 * When adding a new report record, the add/edit needs to know what the class
 * name is so that it can instantiate it, and grab it's default settings.
 */
if(isset($addingReportRecord))
    $reportClass ="salesHistoryReport";









class s_salesHistoryReport{

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

		$querystatement = "
			SELECT
				products.id,
				products.partnumber,
				products.partname
			FROM
				products ".$this->whereclause.$this->sortorder;
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

		$querystatement = "
			SELECT
				`invoices`.`id`,
				`invoices`.`orderdate`,
				`invoices`.`invoicedate`,
				IF(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) AS `client`,
				`lineitems`.`quantity` AS `qty`,
				`lineitems`.`unitprice`*`lineitems`.`quantity` AS `extended`,
				`lineitems`.`unitprice` AS `price`,
				`lineitems`.`unitcost` AS `cost`,
				`lineitems`.`unitcost`*`lineitems`.`quantity` AS extendedcost
			FROM
				((products INNER JOIN lineitems on products.uuid=lineitems.productid)
					INNER JOIN `invoices` ON lineitems.invoiceid=invoices.id)
						INNER JOIN `clients` on `invoices`.`clientid`=`clients`.`uuid`
			WHERE
				`products`.`id`=".$id."
				AND
				`invoices`.".$searchdate.">=\"".$mysqlfromdate."\"
				AND
				`invoices`.".$searchdate."<=\"".$mysqltodate."\"
				AND
				".$thestatus."
			ORDER BY
				`invoices`.`invoicedate`,
				`invoices`.`orderdate`
		";

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
	}//end method
}//end class
?>
