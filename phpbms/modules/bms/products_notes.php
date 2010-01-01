<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
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


	require_once("../../include/search_class.php");


	//set the table passing stuff
	$reftableid = 'tbld:7a9e87ed-d165-c4a4-d9b9-0a4adc3c5a34';

	if(isset($_GET["refid"])) $_GET["id"]=$_GET["refid"];

	$refid=(integer) $_GET["id"];

	$refquery = "
            SELECT
                uuid,
                partnumber,
                partname
            FROM
                products
            WHERE
                id=".$refid;

	$refquery=$db->query($refquery);
	$refrecord=$db->fetchArray($refquery);

        $refuuid = $refrecord["uuid"];

        $whereclause="attachedtabledefid = '".$reftableid."' AND attachedid = '".$refuuid."'";
	$backurl="../bms/products_notes.php";
	$base="../../";


	$pageTitle="Notes/Tasks/Events: ".$refrecord["partname"];

	$tabgroup="products entry";
	$selectedtabid="tab:d62cf7eb-fd2a-948a-6279-8a61d02390ae";

	include("../base/notes_records.php");
	//===================================================================================
	//==  THAT's IT                                                                    ==
	//===================================================================================

?>
