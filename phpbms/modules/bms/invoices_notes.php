<?php 
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2007, Kreotek LLC                                  |
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

	include("../../include/session.php");
	include("../../include/common_functions.php");

	require_once("../../include/search_class.php");
	require_once("../../include/common_functions.php");	

	//set the table passing stuff
	$reftableid=3;
	if(isset($_GET["refid"])) $_GET["id"]=$_GET["refid"];
	$refid=(integer) $_GET["id"];
  	$whereclause="attachedtabledefid=\"".$reftableid."\" and attachedid=".$refid;
	$backurl="../bms/invoices_notes.php";
	$base="../../";

	$refquery="SELECT
			   invoices.id, if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as name 
			   FROM invoices INNER JOIN clients ON invoices.clientid=clients.id 
			   WHERE invoices.id=".$refid;
	$refquery=mysql_query($refquery,$dblink);
	$refrecord=mysql_fetch_array($refquery);	
	
	$pageTitle="Notes/Tasks/Events: ".$refrecord["id"].", ".$refrecord["name"];

	$tabgroup="invoices entry";
	$selectedtabid=18;
	
	include("../base/notes_records.php");
	//===================================================================================
	//==  THAT's IT                                                                    ==
	//===================================================================================
	
?>