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
// uber phpbms class for common functions that reference the DB
// it should be instanced in session.php
class phpbms{

	var $db;
	var $modules = array();//array of installed modules
	var $cssIncludes = array();
	var $jsIncludes = array();
	var $topJS = array();
	var $bottomJS = array();
	var $onload = array();

	var $showFooter = true;
	var $showMenu = true;

	function phpbms($db){
		$this->db = $db;

		$this->modules = $this->getModules();
	}


	function showCssIncludes(){
		foreach($this->cssIncludes as $theinclude){
			?><link href="<?php echo APP_PATH ?>common/stylesheet/<?php echo STYLESHEET ."/".$theinclude ?>" rel="stylesheet" type="text/css" />
			<?php
		}
	}

	function showJsIncludes(){
		foreach($this->jsIncludes as $theinclude){
			?><script language="JavaScript" src="<?php echo APP_PATH.$theinclude ?>" type="text/javascript" ></script>
			<?php
		}
	}

	function showExtraJs($array){
		if(count($array)){
		?><script language="JavaScript" type="text/javascript">
		<?php
		foreach($array as $theextra)
			echo $theextra."\n";
		?>
		</script><?php
		}//endid
	}//end method


	function getModules(){
		$modules = array();

		$querystatement = "SELECT * FROM `modules`";
		$queryresult = $this->db->query($querystatement);
		while($therecord = $this->db->fetchArray($queryresult))
			$modules[$therecord["name"]] = $therecord;

		return $modules;
	}


	function displayRights($roleid,$rolename = NULL){
			switch($roleid){

				case 0:
					echo "EVERYONE";
				break;

				case -100:
					echo "Administrators";
				break;

				default:
					if(!$rolename){
						$querystatement = "SELECT name FROM roles WHERE id=".((int) $roleid);
						$queryresult = $this->db->query($querystatement);

						$therecord = $this->db->fetchArray($queryresult);
						$rolename = $therecord["name"];
					}//end if

					echo $rolename;
			}//end case
	}//end method


	function showTabs($tabgroup,$currenttabid,$recordid=0){

		$querystatement="SELECT id,name,location,enableonnew,notificationsql,tooltip,roleid FROM tabs WHERE tabgroup=\"".$tabgroup."\" ORDER BY displayorder";
		$queryresult=$this->db->query($querystatement);

		?><ul class="tabs"><?php
			while($therecord=$this->db->fetchArray($queryresult)){

				if(hasRights($therecord["roleid"])){

					?><li <?php if($therecord["id"]==$currenttabid) echo "class=\"tabsSel\"" ?>><?php
						if($therecord["id"]==$currenttabid || ($recordid==0 && $therecord["enableonnew"]==0)){
							$opener="<div>";
							$closer="</div>";
						} else{
							$opener="<a href=\"".APP_PATH.$therecord["location"]."?id=".$recordid."\">";
							$closer="</a>";
						}
						if($therecord["notificationsql"]!=""){
							$therecord["notificationsql"]=str_replace("{{id}}",((int) $recordid),$therecord["notificationsql"]);
							$notificationresult=$this->db->query($therecord["notificationsql"]);

							if($this->db->numRows($notificationresult)!=0){
								$notificationrecord=$this->db->fetchArray($notificationresult);
								if(isset($notificationrecord["theresult"]))
									if($notificationrecord["theresult"]>0){
										$opener.="<span>";
										$closer="</span>".$closer;
									}
							}
						}

						echo $opener.$therecord["name"].$closer;

					?></li><?php
				}//endif hasRights
			}//end whilt
		?>
		</ul><?php
	}//end method


	function getUserName($id=0){

		$querystatement="select concat(firstname,\" \",lastname) as name from users where id=".((int) $id);
		$queryresult = $this->db->query($querystatement);

		$tempinfo = $this->db->fetchArray($queryresult);
		return trim($tempinfo["name"]);

	}// end method

}// end class


//=================================================
//Most Common Functions of the Application go here.
//=================================================
function uuid($prefix = ''){

	$chars = md5(uniqid(mt_rand(), true));

	$uuid = substr($chars, 0, 8) . '-';
	$uuid .= substr($chars, 8, 4) . '-';
	$uuid .= substr($chars, 12, 4) . '-';
	$uuid .= substr($chars, 16, 4) . '-';
	$uuid .= substr($chars, 20, 12);

	return $prefix.$uuid;

}//end function uuid



function xmlEncode($str){
	$str=str_replace("&","&amp;",$str);
	$str=str_replace("<","&lt;",$str);
	$str=str_replace(">","&gt;",$str);
	return $str;
}

function goURL($url){
	if(headers_sent())
		$error = new appError("450","Could not redirect to: ".$url);
		header("Location: ".$url);
	exit;
}

function hasRights($roleid,$checkForAdmin = true){
	$hasrights=false;

	if($_SESSION["userinfo"]["admin"]==1 && $checkForAdmin)
		$hasrights=true;
	elseif($_SESSION["userinfo"]["admin"] == 1 && $roleid == -100)//-100 is a special value for admin
		$hasrights = true;
	elseif($roleid==0)
		$hasrights=true;
	else
		foreach($_SESSION["userinfo"]["roles"] as $role)
			if($role==$roleid)
				$hasrights=true;

	return $hasrights;
}



// date/time functions
//=====================================================================
function stringToDate($datestring,$format=DATE_FORMAT){
	$thedate=NULL;
	if($datestring){
		switch($format){

			case "SQL":
				$temparray=explode("-",$datestring);
				if(count($temparray)==3)
					$thedate=mktime(0,0,0,(int) $temparray[1],(int) $temparray[2],(int) $temparray[0]);
				else
					return false;
			break;

			case "English, US":
				$datestring="/".ereg_replace(",.","/",$datestring);
				$temparray=explode("/",$datestring);
				if(count($temparray)==4)
					$thedate=mktime(0,0,0,(int) $temparray[1],(int) $temparray[2],(int) $temparray[3]);
				else
					return false;
			break;

			case "English, UK":
				$datestring="/".ereg_replace(",.","/",$datestring);
				$temparray=explode("/",$datestring);
				if(count($temparray)==4)
					$thedate=mktime(0,0,0,(int) $temparray[2],(int) $temparray[1],(int) $temparray[3]);
				else
					return false;
			break;

			case "Dutch, NL":
				$datestring="-".ereg_replace(",.","-",$datestring);
				$temparray=explode("-",$datestring);
				if(count($temparray)==4)
					$thedate=mktime(0,0,0,(int) $temparray[2],(int) $temparray[1],(int) $temparray[3]);
				else
					return false;
			break;

		}
	}
	return $thedate;
}

function stringToTime($timestring, $format=TIME_FORMAT){

	$thetime = NULL;

        if($timestring){
		switch($format){

			case "24 Hour":
				$temparray=explode(":",$timestring);
				if(count($temparray)==3)
					$thetime=mktime($temparray[0],$temparray[1],$temparray[2]);
				else
					return false;
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
				if(count($timearray) == 2){
					if ($timearray[0]==12)
						$timearray[0]=0;
					$timearray[0]= ((integer) $timearray[0]) + $addtime;
					$thetime=mktime($timearray[0],$timearray[1],0);
				}else
					return false;
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

			case "English, UK":
				$datestring=strftime("%d/%m/%Y",$thedate);
			break;

			case "Dutch, NL":
				$datestring=strftime("%d-%m-%Y",$thedate);
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
	$currency.=CURRENCY_SYM.number_format(abs($number),CURRENCY_ACCURACY,DECIMAL_SYMBOL,THOUSANDS_SEPARATOR);
	return $currency;
}

function currencyToNumber($currency){
	$number=str_replace(CURRENCY_SYM,"",$currency);
	$number=str_replace(THOUSANDS_SEPARATOR,"",$number);
	$number=str_replace(DECIMAL_SYMBOL,".",$number);
	$number=((real) $number);

	return $number;
}

// Phone/Email functions
//=====================================================================
function validateEmail($value){

	$thereturn = false;
	$atPos = strpos($value, "@");

	//@ symobol must be after first char
	if($atPos > 0){

		$dotPos = strpos($value, ".", $atPos);
		$length = strlen($value);

		//the dot must be at least 2 chars away from at
		//it also must not be the last char in the string
		if( ($dotPos > ($atPos + 1)) && ($length > ($dotPos + 1)) )
			$thereturn = true;

	}//end if

	return $thereturn;

}//end function --validateEmail--


function validatePhone($number){

	//need to decide on the phone reg ex based upon settings information
	switch(PHONE_FORMAT){

		case "US - Loose":
			$phoneRegEx = "/^(?:[\+]?(?:[\d]{1,3})?(?:\s*[\(\.-]?(\d{3})[\)\.-])?\s*(\d{3})[\.-](\d{4}))(?:(?:[ ]+(?:[xX]|(?:[eE][xX][tT][\.]?)))[ ]?[\d]{1,5})?$/";
		break;

		case "US - Strict":
			$phoneRegEx = "/^[2-9]\d{2}-\d{3}-\d{4}$/";
		break;

		case "UK - Loose":
			$phoneRegEx = "/^((\(?0\d{4}\)?\s?\d{3}\s?\d{3})|(\(?0\d{3}\)?\s?\d{3}\s?\d{4})|(\(?0\d{2}\)?\s?\d{4}\s?\d{4}))(\s?\#(\d{4}|\d{3}))?$/";
		break;

		case "International":
			$phoneRegEx = "/^(\(?\+?[0-9]*\)?)?[0-9_\- \(\)]*$/";
		break;
		case "No Verification":
			$phoneRegEx = "/.*/";
		break;
	}//end switch

	return preg_match($phoneRegEx,$number);

}//end function --validatePhone--

//============================================================================
function ordinal($number) {

    // when fed a number, adds the English ordinal suffix. Works for any
    // number, even negatives

    if ($number % 100 > 10 && $number %100 < 14):
        $suffix = "th";
    else:
        switch($number % 10) {

            case 0:
                $suffix = "th";
                break;

            case 1:
                $suffix = "st";
                break;

            case 2:
                $suffix = "nd";
                break;

            case 3:
                $suffix = "rd";
                break;

            default:
                $suffix = "th";
                break;
        }

    endif;

    return "${number}$suffix";

}


function addSlashesToArray($thearray){

	//This function prepares an array for SQL manipulation.

	if(get_magic_quotes_runtime() || get_magic_quotes_gpc()){

		foreach ($thearray as $key=>$value)
			if(is_array($value))
				$thearray[$key]= addSlashesToArray($value);
			else
				$thearray[$key] = mysql_real_escape_string(stripslashes($value));

	} else
		foreach ($thearray as $key=>$value)
			if(is_array($value))
				$thearray[$key]= addSlashesToArray($value);
			else
				$thearray[$key] = mysql_real_escape_string($value);

	return $thearray;

}//end function


function htmlQuotes($string){

	global $sqlEncoding;
	if(!isset($sqlEncoding))
		$sqlEncoding = "";

	switch ($sqlEncoding){

		case "latin1":
			$encoding = "ISO-8859-15";
			break;

		case "utf8":
		default:
			$encoding = "UTF-8";
			break;

	}//endswitch

	return htmlspecialchars($string, ENT_COMPAT, $encoding);

}



function htmlFormat($string,$quotes=false){
	$trans = get_html_translation_table(HTML_ENTITIES);
	$encoded = strtr($string, $trans);
	return $encoded;
}


function showSaveCancel($ids=1){
	?><div class="saveCancels"><input <?php if($ids==1) {?>accesskey="s"<?php }?> title="Save (alt+s)" id="saveButton<?php echo $ids?>" name="command" type="submit" value="save" class="Buttons" /><input id="cancelButton<?php echo $ids?>" name="command" type="submit" value="cancel" class="Buttons" onclick="this.form.cancelclick.value=true;" <?php if($ids==1) {?>accesskey="x" <?php }?> title="(access key+x)" /></div><?php
}


function getAddEditFile($db,$tabledefid,$addedit="edit"){
	$querystatement="SELECT ".$addedit."file AS thefile FROM tabledefs WHERE id=".((int) $tabledefid);
	$queryresult = $db->query($querystatement);

	$therecord=$db->fetchArray($queryresult);
	return APP_PATH.$therecord["thefile"];
}


function booleanFormat($bool){
	if($bool==1)
		return "X";
	else
		return"&middot;";
}


function formatVariable($value, $format=NULL){
	switch($format){
		case "real":
			$value = number_format($value,2);
			break;

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
			$value="<button class=\"graphicButtons buttonDownload\" type=\"button\" onclick=\"document.location='".APP_PATH."servefile.php?i=".$value."'\"><span>download</span></button>";
			//$value="<a href=\"".APP_PATH."servefile.php?i=".$value."\" style=\"display:block;\"><img src=\"".APP_PATH."common/stylesheet/".STYLESHEET."/image/button-download.png\" align=\"middle\" alt=\"view\" width=\"16\" height=\"16\" border=\"0\" /></a>";
			break;

		case "noencoding":
			$value=$value;
			break;


		case "bbcode":
			$value=htmlQuotes($value);

			// This list needs to be expanded
			$bbcodelist["[b]"] = "<strong>";
			$bbcodelist["[/b]"] = "</strong>";
			$bbcodelist["[br]"] = "<br />";
			$bbcodelist["[space]"] = "&nbsp;";

			foreach($bbcodelist as $bbcode => $translation)
				$value = str_replace($bbcode, $translation, $value);

			break;

		default:
			$value=htmlQuotes($value);
	}
	return $value;
}


//for windows servers, we have no define time constants and nl_langinfo function
//in a limited fashion; some windows servers still show that the function
//exists even though it's not implemented, thus the second check;

$nl_exists = function_exists("nl_langinfo");
if($nl_exists)
	$nl_exists = @ nl_langinfo(CODESET);

if(!$nl_exists){

	function nl_langinfo($constant){

		return $constant;

	}//end function

	function nl_setup(){

		$date = mktime(0,0,0,10,7,2007);

		for($i = 1; $i<=7; $i++){

			define("ABDAY_".$i, date("D", $date));
			define("DAY_".$i, date("l"), $date);

			$date = strtotime("tomorrow", $date);
		}//end for


		for($i = 1; $i<=12; $i++){

			$date = mktime(0, 0, 0, $i, 1, 2007);

			define("ABMON_".$i, date("M", $date));
			define("MON_".$i, date("F"), $date);

		}//end for

	}//end function

	nl_setup();

}//end if
?>
