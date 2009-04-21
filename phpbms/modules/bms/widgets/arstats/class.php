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
    class wdgt06a30e0455ad75da7bd60c4203210ac8 extends widget{

        var $uuid ="wdgt:06a30e04-55ad-75da-7bd6-0c4203210ac8";
        var $type = "little";
        var $title = "Accounts Receivable";
        //var $cssIncludes = array('widgets/bms/arstats.css');

        function displayMiddle(){

            $querystatement="
                    SELECT
                        SUM((1-(aged1 * aged2 * aged3)) * (amount-paid)) AS current,
                        SUM((amount-paid)*aged1*(1-aged2)) AS term1,
                        SUM((amount-paid)*aged2*(1-aged3)) AS term2,
                        SUM((amount-paid)*aged3) AS term3,
                        SUM(amount-paid) AS due
                    FROM
                        aritems
                    WHERE
                        `status` = 'open'
                        AND posted=1";

            $queryresult = $this->db->query($querystatement);

            $therecord = $this->db->fetchArray($queryresult);

            ?>
                <table>
                    <tbody>
                        <tr>
                            <td align="right"><p>current</p></td>
                            <td><p><?php echo formatVariable($therecord["current"], "currency")?></p></td>
                        </tr>
                        <tr>
                            <td align="right"><p><?php echo (TERM1_DAYS+1)." - ".TERM2_DAYS?></p></td>
                            <td><p><?php echo formatVariable($therecord["term1"], "currency")?></p></td>
                        </tr>
                        <tr>
                            <td align="right"><p><?php echo (TERM2_DAYS+1)." - ".TERM3_DAYS?></p></td>
                            <td><p><?php echo formatVariable($therecord["term2"], "currency")?></p></td>
                        </tr>
                        <tr>
                            <td align="right"><p><?php echo (TERM3_DAYS+1)."+"?></p></td>
                            <td><p><?php echo formatVariable($therecord["term3"], "currency")?></p></td>
                        </tr>
                        <tr>
                            <td align="right" class="important"><p>total</p></td>
                            <td class="important"><p><?php echo formatVariable($therecord["due"], "currency")?></p></td>
                        </tr>
                    </tbody>
                </table>
            <?php

        }//end function showMiddle

    }//end class workload
?>
