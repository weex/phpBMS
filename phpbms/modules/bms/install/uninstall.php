<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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
function loadSettings() {
	$settingsfile = fopen("../../../settings.php","r") or die ("Couldn't open Settings File");
	while( !feof($settingsfile)) {
		$line=fgets($settingsfile,1024);
		if($line[0]!="[" && $line[0]!="//" && strlen($line) > 2) {
			$thevariable=strpos($line,chr(9));
			$thevariable=substr($line,0,$thevariable);
			$thevalue=strpos(strrev($line),"\" =".chr(9));
			$thevalue=strrev(substr(strrev($line),2,$thevalue-2));
			$_SESSION[$thevariable]=$thevalue;			
		}
	}
	fclose($settingsfile);
}


function deleteTables(){
	global $dbtlink;
	
	$thereturn=true;
	
	$deletestatement="";
	$createfile = fopen("uninstall.sql","r") or die ("Couldn't open Uninstall SQL File");
	while( !feof($createfile)) {
		$deletestatement.=fgets($createfile,1024);
		if(strpos($deletestatement,";")){
			$theresult=mysql_query(trim($deletestatement),$dbtlink); 
			if(!$theresult){
				echo "<div style=\"font-size:10px;\">".mysql_error($dbtlink)." -- '".$deletestatement."'<br>&nbsp;</div>";
				$thereturn="false";
			}
			$deletestatement="";
		}
	}
	return $thereturn;
}//end function

function deleteTableDefinitions($theids){
	global $dbtlink;

	//passed variable is array of user ids to be revoked
	$whereclause="";
	$linkedwhereclause="";
	$relationshipswhereclause="";
	foreach($theids as $theid){
		$whereclause.=" or tabledefs.id=".$theid;
		$linkedwhereclause.=" or tabledefid=".$theid;
		$relationshipswhereclause.=" or fromtableid=".$theid. " or totableid=".$theid;
	}
	$whereclause=substr($whereclause,3);		
	$linkedwhereclause=substr($linkedwhereclause,3);		
	$relationshipswhereclause=substr($relationshipswhereclause,3);		

	$thequery = "DELETE FROM tabledefs WHERE ".$whereclause.";";
	$theresult = mysql_query($thequery,$dbtlink);
	if(!$theresult) die(mysql_query()." -- ".$thequery);

	$thequery = "DELETE FROM tablecolumns WHERE ".$linkedwhereclause.";";
	$theresult = mysql_query($thequery,$dbtlink);
	if(!$theresult) die(mysql_query()." -- ".$thequery);

	$thequery = "DELETE FROM tablefindoptions WHERE ".$linkedwhereclause.";";
	$theresult = mysql_query($thequery,$dbtlink);
	if(!$theresult) die(mysql_query()." -- ".$thequery);

	$thequery = "DELETE FROM tableoptions WHERE ".$linkedwhereclause.";";
	$theresult = mysql_query($thequery,$dbtlink);
	if(!$theresult) die(mysql_query()." -- ".$thequery);

	$thequery = "DELETE FROM tablesearchablefields WHERE ".$linkedwhereclause.";";
	$theresult = mysql_query($thequery,$dbtlink);
	if(!$theresult) die(mysql_query()." -- ".$thequery);

	$thequery = "DELETE FROM usersearches WHERE ".$linkedwhereclause.";";
	$theresult = mysql_query($thequery,$dbtlink);
	if(!$theresult) die(mysql_query()." -- ".$thequery);

	$thequery = "DELETE FROM relationships WHERE ".$relationshipswhereclause.";";
	$theresult = mysql_query($thequery,$dbtlink);
	if(!$theresult) die(mysql_query()." -- ".$thequery);
}

function deleteMenu(){
	global $dbtlink;

	$thequery = "DELETE FROM menu WHERE";
	$thequery.="(id > 1 and id <4) or";
	$thequery.="(id > 5 and id < 14)";
	$theresult = mysql_query($thequery,$dbtlink);
	if(!$theresult) die(mysql_query()." -- ".$thequery);
}


if($_POST["command"]){

	session_start();
	loadSettings();
	
	if(!isset($dbtlink)){
		$dbtlink = mysql_pconnect($_SESSION["mysql_server"],$_SESSION["mysql_user"],$_SESSION["mysql_userpass"]);
		if (!$dbtlink) die("No Link to MySQL possible");
		mysql_select_db($_SESSION["mysql_database"]) or die("Couldn't open $database: ".mysql_error($dblink));
	}
	
	
	deleteTables();
	deleteTableDefinitions(array(2,3,4,5,6,7,8,18));
	deleteMenu();

}//end if
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Uninstall</title>
<link href="../../../common/stylesheet/mozilla/base.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  border="0" cellpadding="4" cellspacing="0" class="bodyline">
	<tr>
		<td ><h1>Uninstall BMS Module</h1>
		    
		<?php if (!$_POST["command"]){?>
		<p>Uninstalling the BMS module will <strong>not</strong> delete the module directory module or files form the server, but it will uninstall the entries from the phpBMS applications and drop all the tables that were installed by the module.</p>
	    <div align="center"><strong class="large">Before clicking the uninstall module, make certain this the action you want to take.</strong><br>
	    <form name="form1" method="post" action="<?php echo $_SERVER["PHP_SELF"]?>">
		    	<input name="command" type="submit" class="Buttons" value="Uninstall BMS Module">
	    </form></div>
		<?php 
		}else{
			session_destroy();?>
		    <div align="center"><strong class="large">Uninstall Complete </strong></div>
			<p>&nbsp;</p></td>
		<?php }?>
	</tr>
</table>
</body>
</html>