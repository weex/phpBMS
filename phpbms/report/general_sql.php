<?php
/*
 $Rev: 258 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-08 21:59:28 -0600 (Wed, 08 Aug 2007) $
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
	require("../include/session.php");
	
	if(!isset($_GET["tid"])) $error = new appError(200,"URL variable missing: tid");
	if(!is_numeric($_GET["tid"])) $error = new appError(300,"URL variable invalid type: tid");

	if($_SESSION["printing"]["sortorder"])
		$sortorder=$_SESSION["printing"]["sortorder"];
	else
		$sortorder="";
		
	header("Content-type: text/plain");
	header('Content-Disposition: attachment; filename="export.sql"');
	
	$querystatement="SELECT maintable FROM tabledefs WHERE id=".$_GET["tid"];
	$thequery=$db->query($querystatement);                   
	if(!$thequery)	$error = new appError(100,"Could not retrieve table information");
	$therecord=$db->fetchArray($thequery);
	
	$querystatement="SELECT * FROM ".$therecord["maintable"]." ".$_SESSION["printing"]["whereclause"].$sortorder;
	$thequery=$db->query($querystatement);                   

	$statementstart = "INSERT INTO `".$therecord["maintable"]."` (";
	for($i=0; $i<$db->numFields($thequery);$i++)
		$statementstart .= "`".$db->fieldName($thequery,$i)."`, ";
	$statementstart = substr($statementstart,0,strlen($statementstart)-2).") VALUES (";

	while($therecord=$db->fetchArray($thequery)){
		$insertstatement = $statementstart;

		foreach($therecord as $field){
			if($field === NULL)
				$insertstatement .= "NULL, ";
			else
				$insertstatement .= "'".mysql_real_escape_string($field)."', ";
		}

		$insertstatement = substr($insertstatement,0,strlen($insertstatement)-2).");\n";
		echo $insertstatement;
	}
?>