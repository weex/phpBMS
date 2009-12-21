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
                         NULL,
                         MAILCHIMP_SECURE
                         );

$response = $listSync->process();

if(isset($response["type"])){
    
    if($response["type"] != "success")
       foreach($response["details"] AS $errorArray){
            
            $message = "MailChimp sync ".$response["type"].": ".$errorArray["message"]." (".$errorArray["code"].")";
            $log = new phpbmsLog($message, "SCHEDULER", NULL, $db);
            
       }//end if
    
}//end if
?>