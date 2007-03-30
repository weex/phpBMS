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

//=================================================
//Most Common Functions of the Application go here.
//=================================================
function sendLog($dblink,$type,$value,$userid="NULL"){
	if($userid!="NULL") $userid= (int) $userid;
	$ip=$_SERVER["REMOTE_ADDR"];

	$querystatement="INSERT INTO `log` (`type`,`value`,`userid`,`ip`) VALUES (";
	$querystatement.="\"".mysql_real_escape_string($type)."\", ";
	$querystatement.="\"".mysql_real_escape_string($value)."\", ";
	$querystatement.=$userid.", ";
	$querystatement.="\"".$ip."\")";
	@ mysql_query($querystatement,$dblink) or die(mysql_error($dblink).":".$querystatement);
	
}

function goURL($url){
	if(headers_sent())
		reportError("450","Could not redirect to: ".$url);
		header("Location: ".$url);
	exit;
}

function hasRights($roleid){
	$hasrights=false;
	if($_SESSION["userinfo"]["admin"]==1)
		$hasrights=true;
	elseif($roleid==0)
		$hasrights=true;
	else
		foreach($_SESSION["userinfo"]["roles"] as $role)
			if($role==$roleid)
				$hasrights=true;

	return $hasrights;
}

function displayRights($roleid,$rolename,$dblink=false){
	 	switch($roleid){
			case 0:
				echo "EVERYONE";
			break;
			case -100:
				echo "Administrators";
			break;
			default:
				if(!$rolename){
					if(!$dblink)
						reportError(400,"displayRights needs a database link if rolename is not specified");
					$querystatement="SELECT name FROM roles WHERE id=".$roleid;
					$queryresult=mysql_query($querystatement,$dblink);
					if(!queryresult) reportError(400,"displayRights could not retrieve role name");
					$therecord=mysql_fetch_array($queryresult);
					$rolename=$therecord["name"];
				}
				echo $rolename;
		}
}

//Create Tabs
//===================================
function displayTabs($tabarray,$selected="none") {
	?>
	<ul class="tabs">
	<?php
	foreach($tabarray as $theitem){
		if(!isset($theitem["disabled"])) $theitem["disabled"]=false;
		if(!isset($theitem["notify"])) $theitem["notify"]=false;
				
		?><li <?php if($theitem["name"]==$selected) echo "class=\"tabsSel\"" ?>><?php

		if ($theitem["name"]==$selected || $theitem["disabled"]) {
			$opener="<div>";
			$closer="</div>";
		}
		else {
			$opener="<a href=\"".$theitem["href"]."\">";
			$closer="</a>";
		}

		if ($theitem["notify"]) {
			$opener.="<span>";
			$closer="</span>".$closer;
		}
		
		echo $opener.$theitem["name"].$closer;
		
		?></li><?php 
	}
	?>
	</ul>
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


// date/time functions
//=====================================================================
define("DATE_FORMAT",$_SESSION["date_format"]);
define("TIME_FORMAT",$_SESSION["time_format"]);

function stringToDate($datestring,$format=DATE_FORMAT){
	$thedate=NULL;
	if($datestring){
		switch($format){

			case "SQL":
				$temparray=explode("-",$datestring);
				if(count($temparray)>1)
					$thedate=mktime(0,0,0,(int) $temparray[1],(int) $temparray[2],(int) $temparray[0]);
			break;

			case "English, US":
				$datestring="/".ereg_replace(",.","/",$datestring);
				$temparray=explode("/",$datestring);
				if(count($temparray)>1)
					$thedate=mktime(0,0,0,(int) $temparray[1],(int) $temparray[2],(int) $temparray[3]);
			break;

		}
	}
	return $thedate;
}

function stringToTime($timestring,$format=TIME_FORMAT){
	$thetime=NULL;
	if($timestring){
		switch($format){

			case "24 Hour":
				$temparray=explode(":",$timestring);
				if(count($temparray)>1)
					$thetime=mktime($temparray[0],$temparray[1],$temparray[2]);
			break;

			case "12 Hour":
				if(strpos($timestring,"AM")!==false){
					$timestring=str_replace(" AM","",$timestring);
					$addtime=0;
				}
				else {
					$timestring=str_replace(" PM","",$timestring);
					$addtime=12;
				}
				$timearray=explode(":",$timestring);
				if ($timearray[0]==12)
					$timearray[0]=0;
				$timearray[0]= ((integer) $timearray[0]) + $addtime;
				$thetime=mktime($timearray[0],$timearray[1],0);
			break;
		}
	}
	return $thetime;
}

function dateToString($thedate,$format=DATE_FORMAT){
	$datestring="";
	if($thedate){
		switch($format){

			case "SQL":
				$datestring=strftime("%Y-%m-%d",$thedate);
			break;
			
			case "English, US":
				$datestring=strftime("%m/%d/%Y",$thedate);
			break;

		}
	}
	return $datestring;
}

function timeToString($thetime,$format=TIME_FORMAT){
	$timestring="";
	if($thetime){
		switch($format){
			case "24 Hour":
				$timestring=strftime("%H:%M:%S",$thetime);
			break;
			case "12 Hour":
				$timestring=trim(strftime(HOUR_FORMAT.":%M %p",$thetime));
			break;
		}
	}
	return $timestring;
}

function formatFromSQLDate($sqldate,$format=DATE_FORMAT){
	$datestring="";
	if($sqldate!="")
		if($format=="SQL")
			$datestring=$sqldate;
		else
			$datestring=dateToString(stringToDate($sqldate,"SQL"),$format);
	return $datestring;
}

function formatFromSQLTime($sqltime,$format=TIME_FORMAT){
	$timestring="";
	if($sqltime!="")
		if($format=="24 Hour")
			$timestring=$sqltime;
		else 
			$timestring=timeToString(stringToTime($sqltime,"24 Hour"),$format);
	return $timestring;
}

function dateFromSQLDatetime($sqldatetime){
		$thedatetime=false;
		$datetimearray=explode(" ",$sqldatetime);
		if(count($datetimearray)==2){
			$tempdatearray=explode("-",$datetimearray[0]);
			$temptimearray=explode(":",$datetimearray[1]);
			if(count($tempdatearray)>1 && count($temptimearray)>1)
				$thedatetime=mktime((int) $temptimearray[0],(int) $temptimearray[1],(int) $temptimearray[2],(int) $tempdatearray[1],(int) $tempdatearray[2],(int) $tempdatearray[0]);
		}		
		return $thedatetime;
}

function formatFromSQLDatetime($sqldatetime,$dateformat=DATE_FORMAT,$timeformat=TIME_FORMAT){
	$datetimestring="";
	$timestring="";
	if($sqldatetime!=""){
		$datetimearray=explode(" ",$sqldatetime);
		
		$datestring=trim($datetimearray[0]);
		if($dateformat=="SQL")
			$datestring=$datestring;
		else 
			$datestring=dateToString(stringToDate($datestring,"SQL"),$dateformat);
		if(isset($datetimearray[1])){
			$timestring=$datetimearray[1];
			if($timeformat=="24 Hour")
				$timestring=$timestring;
			else 
				$timestring=timeToString(stringToTime($timestring,"24 Hour"),$timeformat);
		}
		$datetimestring=trim($datestring." ".$timestring);
	}
	return $datetimestring;
}

function formatFromSQLTimestamp ($datetime,$dateformat=DATE_FORMAT,$timeformat=TIME_FORMAT) {
	if($datetime=="")
		return mktime();
	$hour=0;
	$minute=0;
	$second=0;
	$month=1;
	$day=1;
	$year=1974;
	settype($datetime, 'string');
	eregi('(....)(..)(..)(..)(..)(..)',$datetime,$matches);
	array_shift ($matches);	
	foreach (array('year','month','day','hour','minute','second') as $var) {
		$$var = (int) array_shift($matches);
	}
	
	
	$thedatetime=mktime($hour,$minute,$second,$month,$day,$year);
	
	return trim(dateToString($thedatetime,$dateformat)." ".timeToString($thedatetime,$timeformat));
}

function sqlDateFromString($datestring,$format=DATE_FORMAT){
	$sqldate="0000-00-00";
	if($datestring){
		if($format=="SQL")
			$sqldate=$datestring;
		else
			$sqldate=dateToString(stringToDate($datestring,$format),"SQL");
	}
	return $sqldate;
}

function sqlTimeFromString($timestring,$format=TIME_FORMAT){
	$sqltime="0000-00-00";
	if($timestring){
		if($format=="24 Hour")
			$sqltime=$timestring;
		else
			$sqltime=timeToString(stringToTime($timestring,$format),"24 Hour");
	}
	return $sqltime;
}

// Currency functions
//=====================================================================
function numberToCurrency($number){
	$currency="";
	if($number<0)
		$currency.="-";
	$currency.=$_SESSION["currency_symbol"].number_format(abs($number),$_SESSION["currency_accuracy"],$_SESSION["decimal_symbol"],$_SESSION["thousands_separator"]);
	return $currency;
}

function currencyToNumber($currency){
	$number=str_replace($_SESSION["currency_symbol"],"",$currency);
	$number=str_replace($_SESSION["thousands_separator"],"",$number);
	$number=str_replace($_SESSION["decimal_symbol"],".",$number);
	$number=((real) $number);
	
	return $number;
}



//============================================================================
function addSlashesToArray($thearray){
	if(get_magic_quotes_runtime() || get_magic_quotes_gpc())
		foreach ($thearray as $key=>$value) 
			$thearray[$key] = stripslashes($value);
	
	foreach ($thearray as $key=>$value)
		$thearray[$key] = mysql_real_escape_string($value);
	
	return $thearray;
}

function htmlQuotes($string){
	return htmlspecialchars($string,ENT_COMPAT,"UTF-8");
}

function htmlFormat($string,$quotes=false){
	$trans = get_html_translation_table(HTML_ENTITIES);
	$encoded = strtr($string, $trans);
	return $encoded;
}

function showSaveCancel($ids=1){
	?><div class="saveCancels"><input <?php if($ids==1) {?>accesskey="s"<?php }?> title="Save (alt+s)" id="saveButton<?php echo $ids?>" name="command" type="submit" value="save" class="Buttons" /><input id="cancelButton<?php echo $ids?>" name="command" type="submit" value="cancel" class="Buttons" onclick="this.form.cancelclick.value=true;" <?php if($ids==1) {?>accesskey="x" <?php }?> title="(access key+x)" /></div><?php
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

function booleanFormat($bool){
	if($bool==1)
		return "X";
	else
		return"&middot;";
}


function formatVariable($value,$format){
	switch($format){
		case "currency":
			$value=htmlQuotes(numberToCurrency($value));
		break;
		case "boolean":
			$value=booleanFormat($value);
		break;
		case "date":
			$value=formatFromSQLDate($value);
		break;
		case "time":
			$value=formatFromSQLTime($value);
		break;
		case "datetime":
			$value=formatFromSQLDatetime($value);
		break;
		case "filelink":
			$value="<button class=\"graphicButtons buttonDownload\" type=\"button\" onclick=\"document.location='".$_SESSION["app_path"]."servefile.php?i=".$value."'\"><span>download</span></button>";
			//$value="<a href=\"".$_SESSION["app_path"]."servefile.php?i=".$value."\" style=\"display:block;\"><img src=\"".$_SESSION["app_path"]."common/stylesheet/".$_SESSION["stylesheet"]."/image/button-download.png\" align=\"middle\" alt=\"view\" width=\"16\" height=\"16\" border=\"0\" /></a>";
		break;
		case "noencoding":
			$value=$value;
		break;
		default:
			$value=htmlQuotes($value);
	}
	return $value;
}
?>