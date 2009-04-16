<?php
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
