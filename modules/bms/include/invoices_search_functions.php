<?php
//=============================================
//functions
//=============================================

//mark orders as shipped
function mark_ashipped($theids){
	//passed variable is array of user ids to be revoked
	foreach($theids as $theid){
		$whereclause=$whereclause." or invoices.id=".$theid;
	}
	$whereclause=substr($whereclause,3);
	
	$thequery = "update invoices set invoices.shipped=1,invoices.shippeddate=Now() where (".$whereclause.") and (invoices.status!=\"Invoice\" or invoices.status!=\"VOID\");";
	$theresult = mysql_query($thequery);
	if (!$theresult) die ("Couldn't mark as shipped: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");		
}


//mark as paid in full
function mark_aspaid($theids){
	$whereclause="";
	foreach($theids as $theid){
		$whereclause.=" or invoices.id=".$theid;
	}
	$whereclause=substr($whereclause,3);
	
	$thequery = "update invoices set invoices.amountpaid=invoices.totalti where (".$whereclause.") and (invoices.status!=\"Invoice\" or invoices.status!=\"VOID\")";
	$theresult = mysql_query($thequery);
	if (!$theresult) die ("Couldn't mark as paid: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");				
}

//mark as invoice
function mark_asinvoice($theids){
	$whereclause="";
	foreach($theids as $theid){
		$whereclause.=" or invoices.id=".$theid;
	}
	$whereclause=substr($whereclause,3);
	
	$thequery = "update invoices set invoices.status=\"Invoice\",invoices.invoicedate=ifnull(invoices.invoicedate,Now()) where (".$whereclause.") and (invoices.status!=\"Invoice\" or invoices.status!=\"VOID\") and invoices.amountpaid=invoices.totalti;";
	$theresult = mysql_query($thequery);
	if (!$theresult) die ("Couldn't mark as invoice: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");				
}

//mark as un-invoice
function mark_asuninvoice($theids){
	$whereclause="";
	foreach($theids as $theid){
		$whereclause.=" or invoices.id=".$theid;
	}
	$whereclause=substr($whereclause,3);
	
	$thequery = "update invoices set invoices.status=\"Order\"  where (".$whereclause.") and (invoices.status=\"Invoice\");";
	$theresult = mysql_query($thequery);
	if (!$theresult) die ("Couldn't un-mark as invoice: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");				
}

//void/delete
function delete_record($theids){
	//passed variable is array of user ids to be revoked
	$whereclause="";
	foreach($theids as $theid){
		$whereclause.=" or invoices.id=".$theid;
	}
	$whereclause=substr($whereclause,3);
	
	$thequery = "update invoices set invoices.status=\"VOID\" where (".$whereclause.") and invoices.status!=\"Invoice\";";
	$theresult = mysql_query($thequery);
	if (!$theresult) die ("Couldn't delete: ".mysql_error()."<BR>\n SQL STATEMENT [".$thequery."]");		
}


?>