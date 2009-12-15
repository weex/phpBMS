<?php
require("../../include/session.php");
include("include/MCAPI.class.php");//for MCAPI CLASS (listSync class uses it)
include("include/list_sync.php");//for listSync class


$listSync = new listSync(
                         $db,
                         MAILCHIMP_APIKEY,
                         MAILCHIMP_LIST_ID,
                         MAILCHIMP_LAST_SYNC_DATE,
                         MAILCHIMP_BATCH_LIMIT,
                         MAILCHIMP_SECURE
                         );

$response = $listSync->process();

if(!isset($response["type"])){
    $response = array();
    $response["type"] = "error";
    $response["details"] = array("message"=>"Fatal error: no valid response from script.", "code"=>NULL);
}//end if

$response = json_encode($response);
echo($response);
exit;
?>