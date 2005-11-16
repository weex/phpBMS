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

function mark_asread($theids){
	global $dblink;

	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"notes.id");
	
	$querystatement = "UPDATE notes SET notes.completed=1,modifiedby=\"".$_SESSION["userinfo"]["id"]."\" WHERE (".$whereclause.") AND type!=\"SY\";";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Couldn't Mark as Read/Completed: ".mysql_error($dblink)." -- ".$querystatement);		
	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" marked as completed/read.";
	
	//for repeatable tasks, need to repeat dem!
	$querystatement="SELECT id FROM notes WHERE type=\"TS\" AND ((parentid IS NOT NULL AND parentid!=0 ) OR `repeat`=1) AND (".$whereclause.")";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Could not retrieve repeatable tasks for repeat: ".mysql_error($dblink)." -- ".$querystatement);		
	if (mysql_num_rows($queryresult)){
		require("modules/base/snapshot_ajax.php");
		while($therecord=mysql_fetch_array($queryresult))
			repeatTask($therecord["id"]);
	}

	
	return $message;
}


//delete notes
function delete_record($theids){
	global $dblink;
	
	//passed variable is array of user ids to be revoked
	$whereclause=buildWhereClause($theids,"notes.id");

	//we need to check for incomplete repeatable child tasks
	$querystatement="SELECT distinct notes.parentid FROM notes where notes.parentid is not null and notes.completed=0 and (".$whereclause.")";
	$repeatqueryresult = mysql_query($querystatement,$dblink);
	if (!$repeatqueryresult) reportError(300,"Couldn't Delete: ".mysql_error($dblink)."<BR>\n SQL STATEMENT [".$querystatement."]");		

	$querystatement = "DELETE FROM notes WHERE ((notes.createdby=".$_SESSION["userinfo"]["id"]." or notes.assignedtoid=".$_SESSION["userinfo"]["id"].") OR (".$_SESSION["userinfo"]["accesslevel"]." >=90)) and (".$whereclause.") and (notes.`repeat`!=1);";
	$queryresult = mysql_query($querystatement,$dblink);
	if (!$queryresult) reportError(300,"Couldn't Delete: ".mysql_error($dblink)."<BR>\n SQL STATEMENT [".$querystatement."]");		

	$message=buildStatusMessage(mysql_affected_rows($dblink),count($theids));
	$message.=" deleted";
	
	//repeat where applicable
	if (mysql_num_rows($repeatqueryresult)){
		require("modules/base/snapshot_ajax.php");

		while($therecord=mysql_fetch_array($repeatqueryresult)){
			$querystatement="SELECT id FROM notes WHERE completed=1 AND parentid=".$therecord["parentid"]." ORDER BY startdate DESC LIMIT 0,1";
			$queryresult = mysql_query($querystatement,$dblink);
			if(!$queryresult) reportError(300,("Error retrieving last child record".mysql_error($dblink)." ".$querystatement));
			if(mysql_num_rows($queryresult)){
				$completedrecord=mysql_fetch_array($queryresult);
				repeatTask($completedrecord["id"]);
			} else {
				repeatTask($therecord["parentid"]);
			}			
		}
	}
	
	return $message;

}

?>