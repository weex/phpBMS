<?php
//uncomment if need debug
if(!class_exists("appError"))
	include_once("../../include/session.php");
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
var_dump($response);
echo "<br/><br/>";
exit;
if(isset($response["type"])){
    
    if($response["type"] == "error")
       foreach($response["details"] AS $errorArray){
            
            $message = "MailChimp sync failure: ".$errorArray["message"]." (".$errorArray["code"].")";
            $log = new phpbmsLog($message, "SCHEDULER", NULL, $db);
            
       }//end if
    
}//end if
?>