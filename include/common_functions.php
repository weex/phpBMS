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

//=================================================
//Most Common Functions of the Application go here.
//=================================================

//Create Tabs
//===================================
function create_tabs($tabarray,$selected="none") {
	?>
	<table cellspacing=0 cellpadding=0 border=0><tr>
	<?php
	foreach($tabarray as $theitem){
		if(!isset($theitem["disabled"])) $theitem["disabled"]=false;
		if(!isset($theitem["notify"])) $theitem["notify"]=false;
		
		if ($theitem["name"]==$selected) $theclass="tabselected"; else $theclass="tabs";
		
		?><td class="tabbottoms">&nbsp;</td><td class="<?php echo $theclass?>" align="center" nowrap><?php

		if ($theitem["name"]==$selected || $theitem["disabled"]) echo $theitem["name"];
		else echo "<a href=\"".$theitem["href"]."\">".$theitem["name"]."</a>";
		if ($theitem["notify"]) {
		?> <img src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-note.png" alt="*" align="absmiddle" width="16" height="16" border="0"><?php
		}
		?></td><?php 
	}
	?>
<td width="100%" class="tabbottoms">&nbsp;</td></tr></table>
	<?php
}

//=====================================================================
function getUserName($id=0){
	global $dblink;
	if($id=="") $id=0;
	$querystatement="select concat(firstname,\" \",lastname) as name from users where id=".$id;
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,mysql_error($dblink)." -- ".$querystatement);
	$tempinfo = mysql_fetch_array($queryresult);
	return trim($tempinfo["name"]);
}

// This Function prepares a date for insertion/updaing into a SQL statement
// It will add the quotes for you.
//=====================================================================
function formatToSQLDate($thedate,$allownull=true){
	if($thedate=="" || $thedate=="0/0/0000"){
		if ($allownull)
			$tempdate="NULL";
		else
			$tempdate="\"0000-00-00\"";
	} else{
		$thedate="/".ereg_replace(",.","/",$thedate);
		$temparray=explode("/",$thedate);
		$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
	}//end if
	return $tempdate;
}//end function

function formatToSQLTime($thetime,$allownull=true){
	if($thetime=="" || $thetime=="0:00"){
		if ($allownull)
			$temptime="NULL";
		else
			$temptime="\"00:00:00\"";
	} else{
		if(strpos($thetime,"AM")!==false){
			$thetime=str_replace(" AM","",$thetime);
			$addtime=0;
		}
		else {
			$thetime=str_replace(" PM","",$thetime);
			$addtime=12;
		}
		$timearray=explode(":",$thetime);
		if($timearray[0]!="12")
			$timearray[0]= ((integer) $timearray[0]) + $addtime;
		$temptime="\"".$timearray[0].":".$timearray[1].":00\"";
	}	
	return $temptime;
}

function dateFromSQLDate($sqlDate){
	$thedate="";
	$temparray=explode("-",$sqlDate);
	if(count($temparray)>1)
		$thedate=mktime(0,0,0,$temparray[1],$temparray[2],$temparray[0]);
	return $thedate;
}

function timeFromSQLTime($sqlTime){
	$thetime="";
	$temparray=explode(":",$sqlTime);
	if(count($temparray)>1)
		$thetime=mktime($temparray[0],$temparray[1],$temparray[2]);
	return $thetime;
}

function dateFromSQLTimestamp ($datetime) {
	if($datetime=="")
		return mktime();

	settype($datetime, 'string');
	eregi('(....)(..)(..)(..)(..)(..)',$datetime,$matches);
	array_shift ($matches);	
	foreach (array('year','month','day','hour','minute','second') as
$var) {
		$$var = array_shift($matches);
	}
	return mktime($hour,$minute,$second,$month,$day,$year);
}

function addSlashesToArray($thearray){
	if(!get_magic_quotes_runtime() && !get_magic_quotes_gpc())
		foreach($thearray as $key=>$value)
			$thearray[$key]=addslashes($value);
	return $thearray;
}

function htmlQuotes($string){
	return str_replace("\"","&quot;",$string);
}

function showSaveCancel($ids=1){
	?><div align="right"><input <?php if($ids==1) {?>accesskey="s"<?php }?> title="(alt+s)" id="saveButton<?php echo $ids?>" name="command" type="submit" value="save" class="Buttons" style="width:68px;margin-right:3px;" /><input id="cancelButton<?php echo $ids?>" name="command" type="submit" value="cancel" class="Buttons" style="width:68px;" onClick="this.form.cancelclick.value=true;" <?php if($ids==1) {?>accesskey="x" <?php }?> title="(alt+x)" /></div><?php
}

function buildStatusMessage($affected,$selected){
	switch($affected){
		case "0":
			$message="No records";
		break;
		case "1":
			$message="1 record";		
		break;
		default:
			$message=$affected." records";					
		break;
	}
	if($affected!=$selected)
		$message.=" (of ".$selected." selected)";
	return $message;
}

function buildWhereClause($idArray,$phrase){
	$whereclause="";
	foreach($idArray as $theid){
		$whereclause.=" or ".$phrase."=".$theid;
	}
	$whereclause=substr($whereclause,3);
	return $whereclause;
}

function getAddEditFile($tabledefid,$addedit="edit"){
	global $dblink;
	
	$querystatement="SELECT ".$addedit."file AS thefile FROM tabledefs WHERE id=".$tabledefid;
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,mysql_error($dblink)." -- ".$querystatement);
	$therecord=mysql_fetch_array($queryresult);
	return $_SESSION["app_path"].$therecord["thefile"];
}

function currencyFormat($number){
	if(!is_numeric($number)) return $number;
	if($number <0)
		$thenumber="-$".number_format(abs($number),2);
	else
		$thenumber="$".number_format($number,2);
	return $thenumber;
}

function booleanFormat($bool){
	if($bool==1)
		return "X";
	else
		return"&middot;";
}

function dateFormat($thedate){
	if($thedate) {
		$phpdate=dateFromSQLDate($thedate);
		return strftime("%m/%d/%Y",$phpdate);
	} else return "";
}

function timeFormat($thetime){
	if($thetime) {
		$phptime=timeFromSQLTime($thetime);
		return strftime("%I:%M %p",$phptime);
	} else return "";
}

function formatDateTime($thedatetime,$secs=false){
	$temparray=explode(" ",$thedatetime);
	$thereturn="";
	if (isset($temparray[0])){
		if($temparray[0]){
			$phpdate=dateFromSQLDate($temparray[0]);
			$thereturn.=strftime("%m/%d/%Y",$phpdate);
		}
	}
	if (isset($temparray[1])){
		$phptime=timeFromSQLTime($temparray[1]);		
		if($secs)
			$thereturn.=" ".strftime("%I:%M:%S %p",$phptime);
		else
			$thereturn.=" ".strftime("%I:%M %p",$phptime);
	}
	return $thereturn;
}


function formatTimestamp($timestamp){
	if($timestamp){
		$phptimestamp=dateFromSQLTimestamp($timestamp);
		return strftime("%m/%d/%Y %I:%M:%S %p",$phptimestamp);
	}
}

function formatVariable($value,$format){
	switch($format){
		case "currency":
			$value=currencyFormat($value);
		break;
		case "boolean":
			$value=booleanFormat($value);
		break;
		case "date":
			$value=dateFormat($value);
		break;
		case "time":
			$value=timeFormat($value);
		break;
		case "datetime":
			$value=formatDateTime($value);
		break;
		case "filelink":
			$value="<a href=\"".$_SESSION["app_path"]."servefile.php?i=".$value."\" style=\"display:block;\"><img src=\"".$_SESSION["app_path"]."common/stylesheet/".$_SESSION["stylesheet"]."/button-download.png\" align=\"middle\" alt=\"view\" width=\"16\" height=\"16\" border=\"0\" /></a>";
	}
	return $value;
}
?>