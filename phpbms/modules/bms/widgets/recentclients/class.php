<?php
    //Quick Links
    class wdgt26936c277b7c07fc1f3597d2410688b5 extends widget{

        var $uuid ="wdgt:26936c27-7b7c-07fc-1f35-97d2410688b5";
        var $type = "big";
        var $title = "New Clients";
        var $cssIncludes = array('widgets/bms/recentclients.css');
        var $jsIncludes = array('modules/bms/widgets/recentclients/recentclients.js');

	var $stats = array();

        function displayMiddle(){

            if(date("D")=="Mon")
                    $interval="3 DAY";
            else
                    $interval="1 DAY";

            $whereclause = "clients.creationdate >= DATE_SUB(NOW(),INTERVAL ".$interval.")
			    AND clients.inactive = 0";

            include_once("include/search_class.php");

            $displayTable= new simpleTable($this->db, 2, "rcl");
            $displayTable->querywhereclause = $whereclause;

            $displayTable->issueQuery();

            ?>

                <h3 class="rclLinks">Recently Added Clients and Prospects <?php if($displayTable->numrows) echo " (".$displayTable->numrows.")"?></h3>
                <div class = "rclDivs">
                    <?php $displayTable->show(); ?>
                </div>

            <?php

        }//end function showMiddle

    }//end class workload
?>
