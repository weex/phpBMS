<?php
/*
 $Rev: 311 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-10-02 19:51:27 -0600 (Tue, 02 Oct 2007) $
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

            $displayTable= new simpleTable($this->db, "tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883", "rso");
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
