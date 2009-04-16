<?php
    //Quick Links
    class wdgtc0a56726d855744366a27b84f443a84c extends widget{

        var $uuid ="wdgt:c0a56726-d855-7443-66a2-7b84f443a84c";
        var $type = "big";
        var $title = "New Sales Orders";
        var $cssIncludes = array('widgets/bms/recentsalesorders.css');
        var $jsIncludes = array('modules/bms/widgets/recentsalesorders/recentsalesorders.js');

	var $stats = array();

        function displayMiddle(){

            if(date("D")=="Mon")
                    $interval="3 DAY";
            else
                    $interval="1 DAY";

            $whereclause = "invoices.creationdate >= DATE_SUB(NOW(),INTERVAL ".$interval.")
			    AND (
                                invoices.type = 'Order'
                                OR invoices.type = 'Quote'
                                )";

            include_once("include/search_class.php");

            $displayTable= new simpleTable($this->db, 3, "rso");
            $displayTable->querywhereclause = $whereclause;

            $displayTable->issueQuery();

            ?>

                <h3 class="rsoLinks">Recent Sales Orders and Quotes <?php if($displayTable->numrows) echo " (".$displayTable->numrows.")"?></h3>
                <div class = "rsoDivs">
                    <?php $displayTable->show(); ?>
                </div>

            <?php

        }//end function showMiddle

    }//end class workload
?>
