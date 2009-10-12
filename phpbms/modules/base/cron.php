<?php

	$loginNoKick=true;
	$loginNoDisplayError=true;

 	include("../../include/session.php");
	/**
	  *  tables.php for push records 
	  */
	include_once("include/tables.php");

	//testing cron access
	if(isset($_GET["t"]) && !APP_DEBUG){

		echo "ztz";
		exit;

	}//endif

	$now = gmdate('Y-m-d H:i', strtotime('now'));

	$querystatement = "
		SELECT
			`scheduler`.id,
			`scheduler`.name,
			`scheduler`.crontab,
			`scheduler`.IF(`pushrecordid` != '', 'pushrecord', 'job') AS `type`,
			`scheduler`.job,
			`scheduler`.`pushrecordid`,
			`scheduler`.startdatetime,
			`scheduler`.enddatetime,
			`tabledefs`.`maintable`
			`modules`.`name` AS `modulename`
		FROM
			((scheduler LEFT JOIN `tabledefs` ON `scheduler`.`pushrecordid` = `tabledefs`.`uuid`) LEFT JOIN `modules` ON `tabledefs`.`moduleid` = `modules`.`uuid`)
		WHERE
			inactive = 0
			AND startdatetime < NOW()
			AND (enddatetime > NOW() OR enddatetime IS NULL)";

	$queryresult=$db->query($querystatement);

	while($schedule_record=$db->fetchArray($queryresult)){

		$datetimearray=explode(" ",$schedule_record["startdatetime"]);
		$schedule_record["startdate"]=stringToDate($datetimearray[0],"SQL");
		$schedule_record["starttime"]=stringToTime($datetimearray[1],"24 Hour");

		if($schedule_record["enddatetime"]){

			$datetimearray=explode(" ",$schedule_record["enddatetime"]);
			$schedule_record["enddate"]=stringToDate($datetimearray[0],"SQL");
			$schedule_record["endtime"]=stringToTime($datetimearray[1],"24 Hour");

		}//endif enddateiem

		$validTimes = getTimes($schedule_record);

		if(is_array($validTimes) && in_array($now, $validTimes)){

			switch($schedule_record["type"]){
				
				case "job":
					$success = @ include($schedule_record["job"]);
					break;
			
				case "pushrecord":
					include_once("modules/api/include/push.php");
					        
					//try to include table specific functions
					$tableFile = "modules/".$schedule_record["modulename"]."/include/".$schedule_record["maintable"].".php";
					
					if(file_exists($tableFile))
						include_once($tableFile);
									
					$push = new push($db, $schedule_record["pushrecordid"]);
					$success = $push->process();
					break;
				
			}//end switch

			if($success){

				$updatestatement = "UPDATE scheduler SET lastrun=NOW() WHERE id=".$schedule_record["id"];
				$db->query($updatestatement);
				$log = new phpbmsLog("Scheduled Job ".$schedule_record["name"]." (".$schedule_record["id"].") completed","SCHEDULER","usr:42e0cc76-3c31-d9b6-ff12-fe4adfd15e75");

			} else {

				$log = new phpbmsLog("Scheduled Job ".$schedule_record["name"]." (".$schedule_record["id"].") returned errors","SCHEDULER","usr:42e0cc76-3c31-d9b6-ff12-fe4adfd15e75");

			}//endif success

		}//endif is_array();

	}//endwhile


	function getTimes($recordarray){

		$dayInt = array('*',1,2,3,4,5,6,7);
		$dayLabel = array('*',"Monday","Tuesday","Wedensday","Thursday","Friday","Saturday","Sunday");
		$monthsInt = array(0,1,2,3,4,5,6,7,8,9,10,11,12);
		$monthsLabel = array(0,"Janurary","Februrary","March","April","May","June","July","August","September","October","November","December");
		$metricsVar = array("*", "/", "-", ",");
		$metricsVal = array(' every ','',' thru ',' and ');

		$dateTimes = array();
		$ints	= explode('::', str_replace(' ','',$recordarray["crontab"]));
		$days	= $ints[4];
		$mons	= $ints[3];
		$dates	= $ints[2];
		$hrs	= $ints[1];
		$mins	= $ints[0];
		$today	= getdate(gmmktime());

		// derive day part
		if($days == '*') {
			//do nothing
		} elseif(strstr($days, '*/')) {
			$theDay = str_replace('*/','',$days);
			$dayName[] = str_replace($dayInt, $dayLabel, $theDay);
		} elseif($days != '*') {
			if(strstr($days, ',')) {
				$exDays = explode(',',$days);
				foreach($exDays as $k1 => $dayGroup) {
					if(strstr($dayGroup,'-')) {
						$exDayGroup = explode('-', $dayGroup);
						for($i=$exDayGroup[0];$i<=$exDayGroup[1];$i++) {
							$dayName[] = str_replace($dayInt, $dayLabel, $i);
						}
					} else { // individuals
						$dayName[] = str_replace($dayInt, $dayLabel, $dayGroup);
					}
				}
			} elseif(strstr($days, '-')) {
				$exDayGroup = explode('-', $days);
				for($i=$exDayGroup[0];$i<=$exDayGroup[1];$i++) {
					$dayName[] = str_replace($dayInt, $dayLabel, $i);
				}
			} else {
				$dayName[] = str_replace($dayInt, $dayLabel, $days);
			}

			// check the day to be in scope:
			if(!in_array($today['weekday'], $dayName)) {
				return false;
			}
		} else {
			return false;
		}


		// derive months part
		if($mons == '*') {
			// do nothing
		} elseif(strstr($mons, '*/')) {
			$mult = str_replace('*/','',$mons);
			$startMon = date(strtotime('m',$recordarray["startdate"]));
			$startFrom = ($startMon % $mult);

			for($i=$startFrom;$i<=12;$i+$mult) {
				$compMons[] = $i+$mult;
				$i += $mult;
			}
			// this month is not in one of the multiplier months
			if(!in_array($today['mon'],$compMons)) {
				return false;
			}
		} elseif($mons != '*') {
			if(strstr($mons,',')) { // we have particular (groups) of months
				$exMons = explode(',',$mons);
				foreach($exMons as $k1 => $monGroup) {
					if(strstr($monGroup, '-')) { // we have a range of months
						$exMonGroup = explode('-',$monGroup);
						for($i=$exMonGroup[0];$i<=$exMonGroup[1];$i++) {
							$monName[] = $i;
						}
					} else {
						$monName[] = $monGroup;
					}
				}
			} elseif(strstr($mons, '-')) {
				$exMonGroup = explode('-', $mons);
				for($i=$exMonGroup[0];$i<=$exMonGroup[1];$i++) {
					$monName[] = $i;
				}
			} else { // one particular month
				$monName[] = $mons;
			}

			// check that particular months are in scope
			if(!in_array($today['mon'], $monName)) {
				return false;
			}
		}


		// derive dates part
		if($dates == '*') {
			//do nothing
		} elseif(strstr($dates, '*/')) {
			$mult = str_replace('*/','',$dates);
			$startDate = date('d', strtotime($recordarray["startdate"]));
			$startFrom = ($startDate % $mult);

			for($i=$startFrom; $i<=31; $i+$mult) {
				$dateName[] = str_pad(($i+$mult),2,'0',STR_PAD_LEFT);
				$i += $mult;
			}

			if(!in_array($today['mday'], $dateName)) {
				return false;
			}
		} elseif($dates != '*') {
			if(strstr($dates, ',')) {
				$exDates = explode(',', $dates);
				foreach($exDates as $k1 => $dateGroup) {
					if(strstr($dateGroup, '-')) {
						$exDateGroup = explode('-', $dateGroup);
						for($i=$exDateGroup[0];$i<=$exDateGroup[1];$i++) {
							$dateName[] = $i;
						}
					} else {
						$dateName[] = $dateGroup;
					}
				}
			} elseif(strstr($dates, '-')) {
				$exDateGroup = explode('-', $dates);
				for($i=$exDateGroup[0];$i<=$exDateGroup[1];$i++) {
					$dateName[] = $i;
				}
			} else {
				$dateName[] = $dates;
			}

			// check that dates are in scope
			if(!in_array($today['mday'], $dateName)) {
				return false;
			}
		}

		// derive hours part
		$currentHour = date('G', strtotime('00:00'));
		if($hrs == '*') {
			for($i=0;$i<24; $i++) {
				$hrName[]=$i;
			}
		} elseif(strstr($hrs, '*/')) {
			$mult = str_replace('*/','',$hrs);
			for($i=0; $i<24; $i) { // weird, i know
				$hrName[]=$i;
				$i += $mult;
			}
		} elseif($hrs != '*') {
			if(strstr($hrs, ',')) {
				$exHrs = explode(',',$hrs);
				foreach($exHrs as $k1 => $hrGroup) {
					if(strstr($hrGroup, '-')) {
						$exHrGroup = explode('-', $hrGroup);
						for($i=$exHrGroup[0];$i<=$exHrGroup[1];$i++) {
							$hrName[] = $i;
						}
					} else {
						$hrName[] = $hrGroup;
					}
				}
			} elseif(strstr($hrs, '-')) {
				$exHrs = explode('-', $hrs);
				for($i=$exHrs[0];$i<=$exHrs[1];$i++) {
					$hrName[] = $i;
				}
			} else {
				$hrName[] = $hrs;
			}
		}

		// derive minutes
		$currentMin = date('i', strtotime($recordarray["starttime"]));
		if(substr($currentMin, 0, 1) == '0') {
			$currentMin = substr($currentMin, 1, 1);
		}
		if($mins == '*') {
			for($i=0; $i<60; $i++) {
				if(($currentMin + $i) > 59) {
					$minName[] = ($i + $currentMin - 60);
				} else {
					$minName[] = ($i+$currentMin);
				}
			}
		} elseif(strstr($mins,'*/')) {
			$mult = str_replace('*/','',$mins);
			$startMin = date('i',strtotime($recordarray["starttime"]));
			$startFrom = ($startMin % $mult);
			for($i=$startFrom; $i<=59; $i) {
				if(($currentMin + $i) > 59) {
					$minName[] = ($i + $currentMin - 60);
				} else {
					$minName[] = ($i+$currentMin);
				}
				$i += $mult;
			}

		} elseif($mins != '*') {
			if(strstr($mins, ',')) {
				$exMins = explode(',',$mins);
				foreach($exMins as $k1 => $minGroup) {
					if(strstr($minGroup, '-')) {
						$exMinGroup = explode('-', $minGroup);
						for($i=$exMinGroup[0]; $i<=$exMinGroup[1]; $i++) {
							$minName[] = $i;
						}
					} else {
						$minName[] = $minGroup;
					}
				}
			} elseif(strstr($mins, '-')) {
				$exMinGroup = explode('-', $mins);
				for($i=$exMinGroup[0]; $i<=$exMinGroup[1]; $i++) {
					$minName[] = $i;
				}
			} else {
				$minName[] = $mins;
			}
		}

		// prep some boundaries - these are not in GMT b/c gmt is a 24hour period, possibly bridging 2 local days
		$timeFromTs = 0;
		$timeToTs = strtotime('+1 da');
		$timeToTs++;
		$lastRunTs = 0;




		/**
		 * initialize return array
		 */
		$validJobTime = array();

		$hourSeen = 0;
		foreach($hrName as $kHr=>$hr) {
			$hourSeen++;
			foreach($minName as $kMin=>$min) {
				if($hr < $currentHour || $hourSeen == 25)
					$theDate = date('Y-m-d', strtotime('+1 day'));
				else
					$theDate = date('Y-m-d');

				$tsGmt = strtotime($theDate.' '.str_pad($hr,2,'0',STR_PAD_LEFT).":".str_pad($min,2,'0',STR_PAD_LEFT).":00"); // this is LOCAL
				$validJobTime[] = gmdate('Y-m-d H:i', $tsGmt);
			}
		}
		sort($validJobTime);

		return $validJobTime;
	}//end function
?>
