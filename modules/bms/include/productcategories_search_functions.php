<?php
//=============================================
//functions
//=============================================
function delete_record($theids){
	global $dblink;

	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"productcategories.id");
	
	$querystatement= "SELECT products.id FROM products INNER JOIN productcategories ON products.categoryid=productcategories.id 
					 WHERE ".$whereclause.";";
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Select for delete failed: ".mysql_error($dblink)." -- ".$querystatement);
	
	if(!mysql_num_rows($queryresult)){
		$querystatement = "DELETE FROM productcategories WHERE ".$whereclause.";";
		$queryresult = mysql_query($querystatement,$dblink);
		if (!$queryresult) reportError(300,"Couldn't Delete: ".mysql_error($dblink)." -- ".$querystatement);		
		
		$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	} else {
		$message=buildStatusMessage(0,count($theids));	
	}
	$message.=" deleted.";
	return $message;			
}


?>