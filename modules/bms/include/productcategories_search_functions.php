<?php
//=============================================
//functions
//=============================================
//revoke access
function delete_record($theids){
	global $session_productcategoriesinfo;
	//passed variable is array of user ids to be revoked
	foreach($theids as $theid){
		$whereclause=$whereclause." or productcategories.id=".$theid;
	}
	$whereclause=substr($whereclause,3);
	
	$thequery = "select products.id from products inner join productcategories on products.categoryid=productcategories.id where ".$whereclause.";";
	$theresult = mysql_query($thequery);
	$numrows=mysql_num_rows($theresult);
	
	if(!$numrows){
		$thequery = "delete from productcategories where ".$whereclause.";";
		$theresult = mysql_query($thequery);
	}
			
}


?>