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

class purchaseHistoryReport extends phpbmsReport{

    var $fromDate;
    var $toDate;
    var $view;
    var $clientQueryresult;
    var $dataPrint;

    function purchaseHistoryReport($db, $reportUUID, $tabledefUUID){

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
            $this->sortOrder = " ORDER BY clients.company, clients.firstname, clients.lastname";

        if(!$this->whereClause)
            $this->whereClause = "clients.id!=-1";

        $this->whereClause = " WHERE (".$this->whereClause.") ";

        $querystatement = "
            SELECT
                `clients`.`uuid`,
                IF(clients.company != '', CONCAT(clients.company,IF(clients.lastname != '' OR clients.firstname != '', CONCAT(' (',if(clients.lastname != '', clients.lastname, '{blank}'),', ',if(clients.firstname != '', clients.firstname, '{blank}'),')'), '')), IF(clients.lastname != '' OR clients.firstname != '', CONCAT(if(clients.lastname != '', clients.lastname, '{blank}'),', ',if(clients.firstname != '', clients.firstname, '{blank}')), '')) AS thename
             FROM
                `clients`
            ".$this->whereClause.$this->sortOrder;

        $this->clientQueryresult = $this->db->query($querystatement);

    }//end function initialize


    function generateSingleClientHistory($clientUUID){

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
                invoices.id,
                if(invoices.type = 'Invoice', invoices.invoicedate, invoices.orderdate) AS thedate,
                invoices.type,
                products.partname AS partname,
                products.partnumber AS partnumber,
                lineitems.quantity AS qty,
                lineitems.unitprice*lineitems.quantity AS extended,
                lineitems.unitprice AS price
            FROM
                ((`clients` INNER JOIN `invoices` ON `clients`.`uuid`=`invoices`.`clientid`)
                    INNER JOIN `lineitems` ON `invoices`.`id`=`lineitems`.`invoiceid`)
                    INNER JOIN `products` ON `lineitems`.`productid`=`products`.`uuid`
            WHERE
                `clients`.`uuid`='".$clientUUID."'
                AND ".$theStatus."
            HAVING
                thedate >= '".$mysqlFromDate."'
                AND thedate <= '".$mysqlToDate."'
            ORDER BY
                thedate,
                invoices.id";

        $queryresult = $this->db->query($querystatement);

        ob_start();

        ?>
        <table border="0" cellpadding="0" cellspacing="0">
            <thead>
		<tr>
			<th align="left" colspan="3">invoice</th>
			<th align="left" colspan="3">product</th>
			<th align="left" nowrap="nowrap" colspan="2" class="lastHeader">line item</th>
		</tr>
		<tr>
			<th align="center" nowrap="nowrap">id</th>
			<th align="left" nowrap="nowrap" >type</th>
			<th align="left" nowrap="nowrap" >date</th>
			<th align="left" nowrap="nowrap" >part #</th>
			<th width="100%" nowrap="nowrap" align="left">name</th>
			<th align="right" nowrap="nowrap" >price</th>
			<th align="right" nowrap="nowrap" >qty.</th>
			<th align="right" nowrap="nowrap" class="lastHeader">ext.</th>
                </tr>
            </thead>
            <tbody>
        <?php

            $totalextended = 0;

            while($therecord = $this->db->fetchArray($queryresult)){

		$totalextended += $therecord["extended"];

                ?>
                    <tr>
                        <td align="left" nowrap="nowrap"><?php echo $therecord["id"]?$therecord["id"]:"&nbsp;" ?></td>
                        <td align="left" nowrap="nowrap"><?php echo $therecord["type"]?formatVariable($therecord["type"]):"&nbsp;" ?></td>
                        <td align="left" nowrap="nowrap"><?php echo $therecord["thedate"]?formatFromSQLDate($therecord["thedate"]):"&nbsp;" ?></td>
                        <td nowrap="nowrap"><?php echo formatVariable($therecord["partnumber"]) ?></td>
                        <td nowrap="nowrap"><?php echo formatVariable($therecord["partname"]) ?></td>
                        <td align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["price"])?></td>
                        <td align="right" nowrap="nowrap"><?php echo $therecord["qty"]?></td>
                        <td align="right" nowrap="nowrap"><?php echo numberToCurrency($therecord["extended"])?></td>
                    </tr>
                <?php

            }//endwhile
        ?>
                <tr class="grandTotals">
                    <td colspan="7" align="right">total</td>
                    <td align="right"><?php echo numberToCurrency($totalextended)?></td>
                </tr>

            </tbody>
        </table><?php

        $output = ob_get_contents();
        ob_end_clean();

        return $output;

    }//end function generateSingleClientHistory



    function generate(){


        ob_start();
        ?>

        <h1>Client Purchase History</h1>

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

        while($therecord = $this->db->fetchArray($this->clientQueryresult)){

            $this->reportOutput .= '<h2>'.$therecord["thename"].'</h2>';

            $this->reportOutput .= $this->generateSingleClientHistory($therecord["uuid"]);

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

        $pageTitle = "Client Purchase History";
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
        <form action="<?php echo htmlentities($_SERVER["REQUEST_URI"]); ?>" method="post" name="totals" onsubmit="return validateForm(this)">

            <div class="bodyline" id="reportOptions">

                <h1 id="topTitle"><span>Client Purchase History Options</span></h1>

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

}//end class  purchaseHistoryReport


/**
 * PROCESSING
 * =============================================================================
 */
if(!isset($noOutput)){

    //IE needs caching to be set to private in order to display PDFS
    session_cache_limiter('private');

    require_once("../../../include/session.php");

    checkForReportArguments();

    $report = new purchaseHistoryReport($db, $_GET["rid"], $_GET["tid"]);

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
    $reportClass ="purchaseHistoryReport";
