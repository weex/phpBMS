<?php
/*
 $Rev: 384 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2008-05-04 17:18:08 -0600 (Sun, 04 May 2008) $
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
    require("../../../report/report_class.php");

class totalReport extends phpbmsReport{

	var $selectcolumns;
        var $tableClause = array();
	var $whereClauses = array();
	var $group = "";
	var $showItems = false;
	var $padamount = 20;
        var $fromDate;
        var $toDate;


	function totalReport($db, $reportUUID, $tabledefUUID){

            $this->db = $db;

            parent::phpbmsReport($db, $reportUUID, $tabledefUUID);

            // first we define the available groups
            $this->addGroup("Year","YEAR(docdate)"); //0
            $this->addGroup("Quarter","QUARTER(docdate)"); //1
            $this->addGroup("Month","DATE_FORMAT(docdate, '%m - %b')"); //2
            $this->addGroup("Week","WEEK(docdate)"); //3
            $this->addGroup("Payment Method","paymentmethods.name"); //4
            $this->addGroup("Document Type","doctype"); //5

            //next we do the columns
            $this->addColumn("Record Count","COUNT(id)");//0
            $this->addColumn("Total","SUM(doctotal)","currency");//1

            $this->tableClause["invoices"] = "(invoices INNER JOIN paymentmethods ON invoices.paymentmethodid = paymentmethods.uuid)";
            $this->tableClause["receipts"] = "(receipts INNER JOIN paymentmethods ON receipts.paymentmethodid = paymentmethods.uuid)";

	}//end method


        function processFromPost($variables){

            $this->selectcolumns = $this->columns;

            $this->fromDate = $variables["fromdate"];
            $this->toDate = $variables["todate"];

            if($variables["groupings"] !== ""){

                $this->group = explode("::",$variables["groupings"]);
                $this->group = array_reverse($this->group);

            } else
                $this->group = array();

            foreach($this->group as $grp){

                if($this->groupings[$grp]["table"]){

                    foreach($this->tableClause as $key => $value);
                        $this->tableClause[$key] = "(".$this->tableClause[$key]." ".$this->groupings[$grp]["table"].")";

                }//endif

            }//endforeach

            $this->whereClauses["invoices"] = "
                WHERE
                    (invoices.type = 'Invoice'
                    AND paymentmethods.type != 'receivable'
                    AND invoicedate >= '".sqlDateFromString($variables["fromdate"])."'
                    AND invoicedate <= '".sqlDateFromString($variables["todate"])."')
                ";

            $this->whereClauses["receipts"] = "
                WHERE
                    (receipts.posted = 1
                    AND receiptdate >= '".sqlDateFromString($variables["fromdate"])."'
                    AND receiptdate <= '".sqlDateFromString($variables["todate"])."')
                ";

            $this->showItems = isset($variables["showitems"]);

        }//end function processFromPost


        function processFromSettings(){

            $variables["fromdate"] = $this->settings["fromDate"];
            $variables["todate"] = $this->settings["toDate"];

            $variables["groupings"] = "";
            foreach($this->settings as $key=>$value)
                if(strpos($key, "group") === 0)
                    $variables["groupings"] .= "::".$value;

            if($variables["groupings"])
                $variables["groupings"] = substr($variables["groupings"], 2);

            $variables["showitems"] = isset($this->settings["showItems"]);

            $this->processFromPost($variables);

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


        function typeSubstitute($value, $type){

                switch($type){

                    case "invoices":
                        $value = str_replace("doctype", "'Sales Order'", $value);
                        $value = str_replace("docdate", "invoicedate", $value);
                        $value = str_replace("(id)", "(invoices.id)", $value);
                        $value = str_replace("doctotal", "totaltni", $value);
                        break;

                    case "receipts":
                        $value = str_replace("doctype", "'Receipt'", $value);
                        $value = str_replace("docdate", "receiptdate", $value);
                        $value = str_replace("(id)", "(receipts.id)", $value);
                        $value = str_replace("doctotal", "amount", $value);
                        break;

                }//endswitch

                return $value;

        }//end function typeSubstitute


        function generateColumns($type){

            $columns = "";

            foreach($this->selectcolumns AS $column){

                $column["field"] = $this->typeSubstitute($column["field"], $type);

                $columns .= ", ".$column["field"]." AS `".$column["name"]."`";

            }//endforeach

            $columns = substr($columns, 1);

            return $columns;

        }//end function generateColumns


	function showGrandTotals(){

                $querystatement = "(SELECT";
                $querystatement .= $this->generateColumns("invoices").", COUNT(invoices.id) AS thecount ";
                $querystatement .= "FROM ".$this->tableClause["invoices"]." ".$this->whereClauses["invoices"].")";
                $querystatement .= " UNION ";
                $querystatement .= "(SELECT";
                $querystatement .= $this->generateColumns("receipts").", COUNT(receipts.id) AS thecount ";
                $querystatement .= "FROM ".$this->tableClause["receipts"]." ".$this->whereClauses["receipts"].")";


                $queryresult = $this->db->query($querystatement);

                $totals["thecount"] = 0;
                foreach($this->selectcolumns as $column)
                    $totals[$column["name"]] = 0;

		while($therecord = $this->db->fetchArray($queryresult)){

                    $totals["thecount"] += $therecord["thecount"];

                    foreach($this->selectcolumns as $column)
                        $totals[$column["name"]] += $therecord[$column["name"]];

                }//endwhile

		?>
		<tr>
			<td class="grandtotals" align="right">Totals: (<?php echo $totals["thecount"]?>)</td>
			<?php
				foreach($this->selectcolumns as $thecolumn){
					?><td align="right" class="grandtotals"><?php echo formatVariable($totals[$thecolumn["name"]],$thecolumn["format"])?></td><?php
				}//end foreach
			?>
		</tr>
		<?php

	}//end function showGrandTotals


	function showGroup($group, $where, $indent){

		if(!$group){

			if($this->showItems)
				$this->showItems($where, $indent + $this->padamount);

		} else {

			$groupby = array_pop($group);


                        $querystatement = "(SELECT";
                        $querystatement .= $this->generateColumns("invoices").", COUNT(invoices.id) AS `thecount` ";
                        $querystatement .= ", ".$this->typeSubstitute($this->groupings[$groupby]["field"], "invoices")." AS `thegroup`";
                        $querystatement .= "FROM ".$this->tableClause["invoices"]." ".$this->whereClauses["invoices"].$this->typeSubstitute($where, "invoices");
                        $querystatement .= " GROUP BY `thegroup`)";
                        $querystatement .= " UNION ";
                        $querystatement .= "(SELECT";
                        $querystatement .= $this->generateColumns("receipts").", COUNT(receipts.id) AS thecount ";
                        $querystatement .= ", ".$this->typeSubstitute($this->groupings[$groupby]["field"], "receipts")." AS `thegroup`";
                        $querystatement .= "FROM ".$this->tableClause["receipts"]." ".$this->whereClauses["receipts"].$this->typeSubstitute($where, "receipts");
                        $querystatement .= " GROUP BY `thegroup`)";
                        $querystatement .= " ORDER BY `thegroup`";

			$queryresult = $this->db->query($querystatement);

                        $currentGroup = "||START||";

                        $totals = array();
                        foreach($this->selectcolumns as $column)
                            $totals[$column["name"]] = 0;

			while($therecord = $this->db->fetchArray($queryresult)){

                                if($currentGroup == "||START||")
                                    $currentGroup = $therecord["thegroup"];

                                if($currentGroup == $therecord["thegroup"]){

                                    foreach($this->selectcolumns as $column)
                                        $totals[$column["name"]] += $therecord[$column["name"]];

                                } else {

                                    $showbottom=true;

                                    if($group or $this->showItems) {

                                            $showbottom = false;
                                            ?>
                                            <tr>
                                                <td colspan="<?php echo (count($this->selectcolumns)+1)?>" class="group" style="padding-left:<?php echo ($indent+2)?>px;">
                                                    <?php echo $this->groupings[$groupby]["name"].": <strong>".formatVariable($currentGroup, $this->groupings[$groupby]["format"])."</strong>"?>&nbsp;
                                                </td>
                                            </tr>
                                            <?php

                                    }//endif

                                    if($group) {

                                            $whereadd = $where." AND (".$this->groupings[$groupby]["field"]."= '".$currentGroup."'";

                                            if(!$therecord["thegroup"])
                                                    $whereadd .= " OR ISNULL(".$this->groupings[$groupby]["field"].")";

                                            $whereadd .= ")";

                                            $this->showGroup($group,$whereadd,$indent+$this->padamount);

                                    } elseif($this->showItems) {

                                            if($currentGroup)
                                                    $this->showItemLines($where." AND (".$this->groupings[$groupby]["field"]."= '".$currentGroup."')",$indent+$this->padamount);
                                            else
                                                    $this->showItemLines($where." AND (".$this->groupings[$groupby]["field"]."= '".$currentGroup."' or isnull(".$this->groupings[$groupby]["field"].") )",$indent+$this->padamount);

                                    }//endif

                                    ?>
                                    <tr>
                                            <td width="100%" style=" <?php
                                                    echo "padding-left:".($indent+2)."px";
                                            ?>" class="groupFooter">
                                                    <?php echo $this->groupings[$groupby]["name"].": <strong>".formatVariable($currentGroup, $this->groupings[$groupby]["format"])."</strong>&nbsp;";?>
                                            </td>
                                            <?php
                                                foreach($this->selectcolumns as $thecolumn){

                                                    ?><td align="right" class="groupFooter"><?php echo formatVariable($totals[$thecolumn["name"]], $thecolumn["format"])?></td><?php

                                                    $totals[$thecolumn["name"]] = $therecord[$thecolumn["name"]];

                                                }//end foreach
                                            ?>
                                    </tr>

                                    <?php

                                    $currentGroup = $therecord["thegroup"];

                                }//end if current

			}//end while

                        //This does the last in the group
                        //slightly different from code above.
                        if($currentGroup != "||START||") {

                            $showbottom = true;

                            if($group or $this->showItems) {

                                    $showbottom = false;
                                    ?>
                                    <tr>
                                        <td colspan="<?php echo (count($this->selectcolumns)+1)?>" class="group" style="padding-left:<?php echo ($indent+2)?>px;">
                                            <?php echo $this->groupings[$groupby]["name"].": <strong>".formatVariable($currentGroup, $this->groupings[$groupby]["format"])."</strong>"?>&nbsp;
                                        </td>
                                    </tr>
                                    <?php

                            }//endif

                            if($group) {

                                    $whereadd = $where." AND (".$this->groupings[$groupby]["field"]."= '".$currentGroup."'";
                                    if(!$therecord["thegroup"])
                                            $whereadd .= " OR ISNULL(".$this->groupings[$groupby]["field"].")";
                                    $whereadd .= ")";
                                    $this->showGroup($group,$whereadd,$indent+$this->padamount);

                            } elseif($this->showItems) {

                                    if($therecord["thegroup"])
                                            $this->showItemLines($where." AND (".$this->groupings[$groupby]["field"]."= '".$currentGroup."')",$indent+$this->padamount);
                                    else
                                            $this->showItemLines($where." AND (".$this->groupings[$groupby]["field"]."= '".$currentGroup."' or isnull(".$this->groupings[$groupby]["field"].") )",$indent+$this->padamount);

                            }//endif

                            ?>
                            <tr>
                                    <td width="100%" style=" <?php
                                            echo "padding-left:".($indent+2)."px";
                                    ?>" class="groupFooter">
                                            <?php echo $this->groupings[$groupby]["name"].": <strong>".formatVariable($currentGroup,$this->groupings[$groupby]["format"])."</strong>&nbsp;";?>
                                    </td>
                                    <?php
                                            foreach($this->selectcolumns as $thecolumn){

                                                    ?><td align="right" class="groupFooter"><?php echo formatVariable($totals[$thecolumn["name"]], $thecolumn["format"])?></td><?php

                                                    $totals[$thecolumn["name"]] = 0;

                                            }//end foreach
                                    ?>
                            </tr>

                            <?php

                        }//endif last itteration

		}//endif

	}//end function


	function showItemLines($where, $indent){

                $querystatement = "
                    (SELECT
                        ".$this->generateColumns("invoices").",
                        invoices.id AS theid,
                        if(clients.lastname!='', concat(clients.lastname,', ',clients.firstname,if(clients.company!='',concat(' (',clients.company,')'),'')),clients.company) AS `thename`,
                        invoices.invoicedate AS `docdate`
                    FROM
                        (".$this->tableClause["invoices"].") INNER JOIN clients ON invoices.clientid = clients.uuid
                        ".$this->whereClauses["invoices"].$this->typeSubstitute($where, "invoices")."
                    GROUP BY `theid`)
                    UNION
                    (SELECT
                        ".$this->generateColumns("receipts").",
                        receipts.id AS `theid`,
                        if(clients.lastname!='', concat(clients.lastname,', ',clients.firstname,if(clients.company!='',concat(' (',clients.company,')'),'')),clients.company) AS `thename`,
                        receipts.receiptdate AS `docdate`
                    FROM
                        (".$this->tableClause["receipts"].") INNER JOIN clients ON receipts.clientid = clients.uuid
                        ".$this->whereClauses["receipts"].$this->typeSubstitute($where, "receipts")."
                    GROUP BY `theid`)
                    ORDER BY `docdate`";

		$queryresult = $this->db->query($querystatement);

		while($therecord = $this->db->fetchArray($queryresult)){

			?>
			<tr>
				<td width="100%" style="padding-left:<?php echo ($indent+2)?>px;" class="invoices">
				<?php
					echo '<div style="float:right">'.$therecord["thename"].'</div>';
					echo formatFromSQLDate($therecord["docdate"])."  (".$therecord["theid"].")";
                                ?>
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

		}//end while

	}//end function showItems


	function showReport(){

            if(!isset($this->settings["reportTitle"]))
                $this->settings["reportTitle"] = "Incoming Cash Flow";


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo APP_PATH ?>common/stylesheet/<?php echo STYLESHEET ?>/pages/totalreports.css" rel="stylesheet" type="text/css" />
<title><?php echo $pageTitle?></title>
</head>

<body>
	<div id="toprint">
		<h1><span><?php echo formatVariable($this->settings["reportTitle"]); ?></span></h1>
		<h2>Dates: <?php echo $this->fromDate ?> - <?php echo $this->toDate ?></h2>

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

		include("include/fields.php");
		global $phpbms;
		$db = &$this->db;

		$phpbms->cssIncludes[] = "pages/bms/incoming_cashflow.css";
		$phpbms->jsIncludes[] = "modules/bms/javascript/incoming_cashflow.js";
		$phpbms->showMenu = false;

		$formSubmit = str_replace("&","&amp;",$_SERVER['REQUEST_URI']);

		$theform = new phpbmsForm();

		$theinput = new inputDatePicker("fromdate", dateToString(mktime(0,0,0, date("m"), 1), "SQL"), "from", true);
		$theform->addField($theinput);

		$theinput = new inputDatePicker("todate", dateToString(mktime(0,0,0), "SQL"), "to", true);
		$theform->addField($theinput);

		$theinput = new inputCheckbox("showitems", false , "Show individual items");
		$theform->addField($theinput);

		$theform->jsMerge();

		include("header.php");

        ?>

        <div class="bodyline" id="dialog">
            <h1>Incoming Cash Flow</h1>
	    <form action="<?php echo $formSubmit ?>" id="record" method="post">

                <fieldset>

                    <legend>time period</legend>

                    <p id="fromdateP"><?php $theform->showField("fromdate")?></p>

                    <p><?php $theform->showField("todate")?></p>

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
			<legend>Options</legend>
                        <p><?php $theform->showField("showitems")?></p>
		</fieldset>

                <p align="right">
                    <button id="printButton" type="button" class="Buttons">print</button>
                    <button id="cancelButton" type="button" class="Buttons">cancel</button>
                </p>

            </form>
        </div>

        <?php

        include("footer.php");
    }//end method

}//endclass


/**
 * PROCESSING
 * =============================================================================
 */
if(!isset($noOutput)){

    //IE needs caching to be set to private in order to display PDFS
    session_cache_limiter('private');

    //set encoding to latin1 (fpdf doesnt like utf8)
    $sqlEncoding = "latin1";
    require_once("../../../include/session.php");

    checkForReportArguments();

    $report = new totalReport($db, $_GET["rid"], $_GET["tid"]);

    if(isset($_POST["fromdate"])){

        $report->processFromPost($_POST);
        $report->showReport();

    } elseif(isset($report->settings["fromdate"]) && isset($report->settings["todate"]) && isset($report->settings["groupings"])){

        $report->processFromSettings();
        $report->showReport();

    }else
        $report->showSelectScreen();

}//end if

/**
 * When adding a new report record, the add/edit needs to know what the class
 * name is so that it can instantiate it, and grab it's default settings.
 */
if(isset($addingReportRecord))
    $reportClass ="totalReport";

?>
