<?php
class ical {

	var $db;
	var $userinfo = array();
	var $vEvents = array();

	function ical($db,$userid){
		$this->db = $db;
		
		$this->_getUserInfo($userid);
		
	}

	function verifyUser($hash){
		return (md5("phpBMS".$this->userinfo["firstname"].$this->userinfo["lastname"].$this->userinfo["id"].$this->userinfo["decpass"]) == $hash);
	}
	
	function _toCalDate($sqldate){
		$phpDate = stringToDate($sqldate,"SQL");
		return date("Ymd",$phpDate);
	}
	
	function _toCalTime($sqltime){
		$phpTime = stringToTime($sqltime,"24 Hour");
		return date("His",$phpTime);
	}

	function _getUserInfo($userid){
	
		$querystatement = "SELECT id,firstname,lastname, DECODE(password,'".ENCRYPTION_SEED."') as decpass FROM users WHERE id=".((int) $userid);
		$queryresult = $this->db->query($querystatement);

		$this->userinfo = $this->db->fetchArray($queryresult);
	
	}//end method
	
	
	function _sendHeaders(){
		header( 'Content-Type: text/calendar; charset=utf-8' );
		header("Content-Disposition: inline; filename=phpBMScalendar.ics");			
	}
	
	function _startCal(){
?>BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//phpBMS v0.81//EN
X-WR-CALNAME:<?php echo APPLICATION_NAME; ?> For user <?php echo $this->userinfo["firstname"]." ".$this->userinfo["lastname"]."\n"?>
CALSCALE:GREGORIAN
METHOD:PUBLISH
<?php
	}
	
	
	function addEvents($startdate,$enddate = NULL){
		$querystatement = "SELECT id, subject, content, startdate, starttime, enddate, endtime,
							DATE_FORMAT(creationdate,'%Y%m%dT%H%i%sZ') AS creationdate, location,
							DATE_FORMAT(modifieddate,'%Y%m%dT%H%i%sZ') AS modifieddate,
							repeating, repeatevery, repeattype, repeateachlist, repeatontheday, repeatontheweek, repeattimes, 
							repeatuntil
							FROM notes
							WHERE (notes.private = 0 OR notes.createdby=".$this->userinfo["id"].") AND notes.type='EV' AND
							(startdate <= '".datetoString($startdate,"SQL")."'";
		if($enddate)
			$querystatement .= " AND enddate >= '".datetoString($startdate,"SQL")."'";
		
		$querystatement .= " OR repeating = 1)";

		$queryresult = $this->db->query($querystatement);

		while($therecord = $this->db->fetchArray($queryresult))
			$this->vEvents[] = $therecord;

	}//end method
	
	
	function  _showEvents(){
		foreach($this->vEvents as $event){
?>BEGIN:VEVENT
CREATED:<?php echo $event["creationdate"]."\n"?>
LAST-MODIFIED:<?php echo $event["modifieddate"]."\n"?>
DTSTAMP:<?php echo $event["creationdate"]."\n"?>
UID:
 <?php echo "BB-".$event["id"]."\n"?>
SUMMARY:<?php echo $event["subject"]."\n"?>
DTSTART:<?php 
	echo $this->_toCalDate($event["startdate"]);
	if($event["starttime"])
		echo "T".$this->_toCalTime($event["starttime"])."\n";
	else
		echo "T000000"."\n";
 ?>
DTEND:<?php 
	echo $this->_toCalDate($event["enddate"]);
	if($event["starttime"])
		echo "T".$this->_toCalTime($event["endtime"])."\n";
	else
		echo "T000000"."\n";
		
	if($event["location"])echo "LOCATION:".$event["location"]."\n";
	if($event["content"])echo "DESCRIPTION:".str_replace("\n","",$event["content"])."\n";
	
	if($event["repeating"]){		
		$rrule = "RRULE:FREQ=".strtoupper($event["repeattype"]).";INTERVAL=".$event["repeatevery"].";";
		
		switch($event["repeattype"]){

			case "Weekly":
				$rrule .= "BYDAY=";
				$bydayArray = explode("::",$event["repeateachlist"]);
				foreach($bydayArray as $day){
					$tempday = ($day != 7)?($day+1):(1);
					$rrule .= strtoupper(substr(nl_langinfo(constant("ABDAY_".$tempday)), 0 ,2)).", ";
				}
				
				$rrule = substr($rrule,0,(strlen($rrule)-2) ).";";
				break;
			
			case "Monthly":
				if($event["repeateachlist"]){
					$rrule .= "BYMONTHDAY=";
					$bydayArray = explode("::",$event["repeateachlist"]);
				
					foreach($bydayArray as $day)
						$rrule .=  $day.", ";
											
					$rrule = substr($rrule,0,(strlen($rrule)-2) ).";";
				} else {
					if($event["repeatontheweek"] == 5)
						$event["repeatontheweek"] = -1;

					$tempday = ($event["repeatontheday"] != 7)?($event["repeatontheday"]+1):(1);

					$rrule .= "BYDAY=".$event["repeatontheweek"].strtoupper(substr(nl_langinfo(constant("ABDAY_".$tempday)), 0 ,2)).";";
				}//end if
				break;
			
			case "Yearly":
				$rrule .= "BYMONTH=";
				$bymonthArray = explode("::",$event["repeateachlist"]);

				foreach($bymonthArray as $month)
					$rrule .=  $month.", ";
					
				$rrule = substr($rrule,0,(strlen($rrule)-2) ).";";
				if($event["repeatontheday"]){
					if($event["repeatontheweek"] == 5)
						$event["repeatontheweek"] = -1;

					$tempday = ($event["repeatontheday"] != 7)?($event["repeatontheday"]+1):(1);

					$rrule .= "BYDAY=".$event["repeatontheweek"].strtoupper(substr(nl_langinfo(constant("ABDAY_".$tempday)), 0 ,2)).";";
				}//endif
				break;
				
		}//endcase
		
		if($event["repeattimes"])
			$rrule .= "COUNT=".$event["repeattimes"].";";
		elseif($event["repeatuntil"])
			$rrule = "UNTIL=".$this->_toCalDate($event["repeatuntil"])."T000000;";
		
		echo substr($rrule,0,strlen($rrule)-1)."\n";		
	}//endif repeating
 ?>
END:VEVENT
<?php
		}//endforeach
	}//end method
	
	
	function showCalendar(){
		$this->_sendHeaders();

		$this->_startCal();
		
		$this->_showEvents();
		
		$this->_endCal();
	}
	
	
	function _endCal(){
?>END:VCALENDAR
<?php
	}//end methdo
	
}//end class

//==============================================================================================================
if (isset($_GET["u"]) && isset($_GET["h"]) && !isset($dontProcess)){
	$loginNoKick=true;
	$loginNoDisplayError=true;

	include("../../include/session.php");
	
	$theCal = new ical($db,$_GET["u"]);
	
	if($theCal->verifyUser($_GET["h"]))
	
	$theCal->addEvents(strtotime("-1 month",mktime(0,0,0)));
	
	$theCal->showCalendar();

}
?>