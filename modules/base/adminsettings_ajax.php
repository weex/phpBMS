<?php

class checkUpdate {

    /**
     * function checkUpdate
     * @param object $db database object
     */

    function checkUpdate($db) {
        $this->db = $db;
    }//end method

    /**
     * function needUpdateCheck
     * @param $manual Whether the check was instigated manually or automatically.
     * @return boolean Whether or not to perform an update.
     */

    function needUpdateCheck($manual) {

        if(!$manual){

            $querystatement = "
                SELECT
                    IF((NOW() >= date_add(`value`, INTERVAL 1 DAY)) OR `value` = '' OR `value` IS NULL,1,0) AS `doautocheck`
                FROM
                    `settings`
                WHERE
                    `name` = 'last_update_check'
            ";

            $queryresult = $this->db->query($querystatement);

            $dateCheck = $this->db->fetchArray($queryresult);

            if((int)$dateCheck["doautocheck"] == 0)
                return false;
            else
                return true;

        }else
            return true;

    }//end function


    /**
     * function checkForUpdate
     *
     * @return string A json formated response string.
     */

    function checkForUpdate() {

        $querystatement = "
            SELECT
                `version`,
                NOW() AS `date`
            FROM
                `modules`
            WHERE
                `uuid`='mod:29873ee8-c12a-e3f6-9010-4cd24174ffd7'
        ";

        $queryresult = $this->db->query($querystatement);

        $moduleRecord = $this->db->fetchArray($queryresult);

        $url = "http://kreotek.com/foo.php?v=".htmlspecialchars($moduleRecord["version"]);

        $res = fopen($url,"r");
        if($res !== false){
            $response = stream_get_contents($res);
            fclose($res);
        }//end if

        $response = json_decode($response);
        $response->date = $moduleRecord["date"];
        $response->checked = true;

        $querystatement = "
            UPDATE
                `settings`
            SET
                `value` = '".mysql_real_escape_string($moduleRecord["date"])."'
            WHERE
                `name` = 'last_update_check'
        ";

        $this->db->query($querystatement);

        $response = json_encode($response);
        return $response;

    }//end function

}//end class


/**
 * Processing ==================================================================
 */
if(!isset($noOutput)){

    require_once("../../include/session.php");

    $db->errorFormat = "json";

    if(!isset($_GET["m"]))
        $error = new appError(200, "invalid passed paramaters", "", true, true, "json");

    if(!$_SESSION["userinfo"]["admin"])
        $error = new appError(970, "no rights to function", "", true, true, "json");

    $checkUpdate = new checkUpdate($db);
    $response = array();

    if($checkUpdate->needUpdateCheck($_GET["m"]))
        $response = $checkUpdate->checkForUpdate();
    else{
        $response["checked"] = false;
        $response = json_encode($response);
    }//end if

    echo $response;

}//endif
?>
