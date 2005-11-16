<?php
/*
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
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

//=============================================
//functions
//=============================================

//mark orders as shipped
function mark_ashipped($theids){
	global $dblink;

	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"invoices.id");
	
	$querystatement = "UPDATE invoices SET invoices.status=\"Shipped\",invoices.shippeddate=Now(),modifiedby=\"".$_SESSION["userinfo"]["id"]."\" WHERE (".$whereclause.") AND (invoices.type!=\"Invoice\" or invoices.type!=\"VOID\");";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Couldn't Mark As Shipped: ".mysql_error($dblink)." -- ".$querystatement);		
	
	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" marked as shipped.";

	return $message;
}


//mark as paid in full
function mark_aspaid($theids){
	global $dblink;

	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"invoices.id");
	
	$querystatement = "UPDATE invoices SET invoices.amountpaid=invoices.totalti,modifiedby=\"".$_SESSION["userinfo"]["id"]."\" WHERE (".$whereclause.") AND (invoices.type!=\"Invoice\" OR invoices.type!=\"VOID\")";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Couldn't Mark As Paid In Full: ".mysql_error($dblink)." -- ".$querystatement);		
	
	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" marked as paid in full.";

	return $message;
}

//mark as invoice
function mark_asinvoice($theids){
	global $dblink;

	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"invoices.id");
	
	$querystatement = "UPDATE invoices SET invoices.type=\"Invoice\",invoices.status=\"shipped\",invoices.invoicedate=ifnull(invoices.invoicedate,Now()),modifiedby=\"".$_SESSION["userinfo"]["id"]."\" WHERE (".$whereclause.") AND (invoices.type!=\"Invoice\" OR invoices.type!=\"VOID\") AND invoices.amountpaid=invoices.totalti;";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Could not convert to client: ".mysql_error($dblink)." -- ".$querystatement);		
	
	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" converted to invoice.";

	return $message;
}

//mark as un-invoice
function mark_asuninvoice($theids){
	global $dblink;

	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"invoices.id");
	
	$querystatement = "UPDATE invoices SET invoices.type=\"Order\",modifiedby=\"".$_SESSION["userinfo"]["id"]."\"  WHERE (".$whereclause.") AND (invoices.type=\"Invoice\");";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Couldn't Reset Invoice Status: ".mysql_error($dblink)." -- ".$querystatement);		
	
	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" reset invoice status.";

	return $message;
}

//void/delete
function delete_record($theids){
	global $dblink;

	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"invoices.id");
	
	$querystatement = "UPDATE invoices SET invoices.type=\"VOID\",modifiedby=\"".$_SESSION["userinfo"]["id"]."\" WHERE (".$whereclause.") AND invoices.type!=\"Invoice\";";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Couldn't Void: ".mysql_error($dblink)." -- ".$querystatement);		
	
	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" voided.";

	return $message;
}


?>