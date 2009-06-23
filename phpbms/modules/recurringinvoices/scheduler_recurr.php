<?php
//uncomment if need debug
//if(!class_exists("appError"))
//	include_once("../../include/session.php");


class recurr{

	function recurr($db){
		$this->db = $db;
	}


	function getInvoicesToRepeat($dateToCheck = NULL){
		if($dateToCheck == NULL)
			$dateToCheck = mktime(0,0,0);

		$invoiceList = array();

		$querystatement = "
			SELECT
				`invoiceid`,
				`invoices`.`invoicedate`,
				`firstrepeat`,
				`lastrepeat`,
				`recurringinvoices`.`type`,
				`eachlist`,`every`,
				`ontheday`,`ontheweek`
			FROM
				`recurringinvoices` INNER JOIN `invoices` ON `recurringinvoices`.`invoiceid` = `invoices`.`uuid`
			WHERE
				`invoices`.`invoicedate` <= '".dateToString($dateToCheck,"SQL")."'
				AND
				(`recurringinvoices`.`until` IS NULL OR `recurringinvoices`.`until` >= '".dateToString($dateToCheck,"SQL")."')
				AND
				(`recurringinvoices`.`times` IS NULL OR `recurringinvoices`.`times` > `recurringinvoices`.`timesrepeated`)";

		$queryresult = $this->db->query($querystatement);

		while($therecord = $this->db->fetchArray($queryresult)){

			if($therecord["lastrepeat"])
				$startDate = stringToDate($therecord["lastrepeat"],"SQL");
			else
				$startDate = stringToDate($therecord["invoicedate"],"SQL");

			$dateArray = $this->getValidInRange($startDate,$dateToCheck,$therecord);

			if( in_array($dateToCheck, $dateArray))
				$invoiceList[] = $therecord["invoiceid"];

		}//end while

		return $invoiceList;

	}//end method


	function getValidInRange($startDate,$endDate,$therecord){
		$nextDate = $startDate;

		//should pad the end date to make sure we get all weekly repeats
		$endDate = strtotime("+7 days",$endDate);

		$validDates = array();

		while($nextDate <= $endDate){

			switch($therecord["type"]){
				case "Daily":
					//==================================================================================
					$validDates[] = $nextDate;
					$nextDate = strtotime("+".$therecord["every"]." days",$nextDate);
					break;

				case "Weekly":
					//==================================================================================
					$weekDayArray = explode("::",$therecord["eachlist"]);

					//need to start from the sunday of the current week
					$tempDate = strtotime(nl_langinfo( constant("DAY_1") ),$nextDate);
					$tempDate = strtotime("-7 days",$tempDate);

					foreach($weekDayArray as $weekday){
						if($weekday == 7)
							$validDates[]=$tempDate;
						else{
							$weekday++;
							$validDates[] = strtotime(nl_langinfo( constant("DAY_".$weekday) ),$tempDate);
						}
					}// endforeach


					$nextDate = strtotime("+".$therecord["every"]." week",$nextDate);

					break;

				case "Monthly":
					//==================================================================================
					$dateArray = localtime($nextDate,true);

					if($therecord["eachlist"]){
						$dayArray = explode("::",$therecord["eachlist"]);

						foreach($dayArray as $theday)
							$validDates[] = mktime(0,0,0,$dateArray["tm_mon"]+1,$theday,$dateArray["tm_year"]+1900);

					} else{
						// check for things like second tuesday or last friday;
						$tempDate = mktime(0,0,0,$dateArray["tm_mon"]+1,1,$dateArray["tm_year"]+1900);
						$weekday = $therecord["ontheday"];
						$weekday = ($weekday == 7)? 1: ($weekday+1);
						if($therecord["ontheday"] != strftime("%u",$tempDate));
							$tempDate = strtotime(nl_langinfo( constant("DAY_".$weekday) ),$tempDate);

						while(date("n",$tempDate) == ($dateArray["tm_mon"]+1)){

							if($therecord["ontheweek"] == 5){
								// 5 is the "last" option, so we just need to see if
								// the date falls in the last 6 days
								if($daysInMonth - date("d",$tempDate) < 7)
									$validDates[] = $tempDate;

							} else {
								if( ceil(date("d",$tempDate)/7) == $therecord["ontheweek"])
									$validDates[] = $tempDate;
							}// endif

							$tempDate = strtotime("+7 days",$tempDate);

						}// endwhile
					}//endif

					$nextDate = strtotime("+".$therecord["every"]." months",$nextDate);
					break;

				case "Yearly":
					//==================================================================================
					$monthArray = explode("::",$therecord["eachlist"]);
					foreach($monthArray as $monthNum){
						$dateArray = localtime($nextDate,true);
						$daysInMonth = date("d", mktime(0,0,0,$monthNum,0,$dateArray["tm_year"]+1900) );

						if(!$therecord["ontheday"]){
							$tempDay = ($dateArray["tm_mday"] > $daysInMonth)? $daysInMonth :$dateArray["tm_mday"];
							$validDates[] = mktime(0,0,0,$monthNum,$tempDay,$dateArray["tm_year"]+1900);

						} else {
							// check for things like second tuesday or last friday;
							$tempDate = mktime(0,0,0,$monthNum,1,$dateArray["tm_year"]+1900);

							$weekday = $therecord["ontheday"];
							$weekday = ($weekday == 7)? 1: ($weekday+1);
							if($therecord["ontheday"] != strftime("%u",$tempDate));
								$tempDate = strtotime(nl_langinfo( constant("DAY_".$weekday) ),$tempDate);


							while(date("n",$tempDate) == $monthNum){
								if($therecord["ontheweek"] == 5){
									// 5 is the "last" option, so we just need to see if
									// the date falls in the last 6 days
									if($daysInMonth - date("d",$tempDate) < 7)
										$validDates[] = $tempDate;

								} else {
									if( ceil(date("d",$tempDate)/7) == $therecord["ontheweek"])
										$validDates[] = $tempDate;
								}// endif

								$tempDate = strtotime("+7 days",$tempDate);

							}// endwhile

						}//endif

					}//endforeach

					$nextDate = strtotime("+".$therecord["every"]." years",$nextDate);

					break;
			}//endswitch

		}//end while

		return $validDates;

	}//end method


	function copyInvoice($invoiceid){
		$querystatement = "
			SELECT
				invoices.*,
				firstrepeat,
				includepaymenttype,
				includepaymentdetails,
				recurringinvoices.id AS recurrid,
				recurringinvoices.statusid AS newstatusid,
				recurringinvoices.assignedtoid AS newassignedtoid,
				notificationroleid
			FROM
				invoices INNER JOIN recurringinvoices ON invoices.uuid = recurringinvoices.invoiceid
			WHERE
				invoices.uuid = '".$invoiceid."'
		";

		$queryresult = $this->db->query($querystatement);

		$therecord = $this->db->fetchArray($queryresult);

		$fieldList = array();
		foreach($therecord as $name=>$value){
			switch($name){
				case "id":
				case "notificationroleid":
				case "includepaymenttype":
				case "includepaymentdetails":
				case "modifiedby":
				case "modifieddate":
				case "createdby":
				case "creationdate":
				case "newstatusid":
				case "newassignedtoid":
				case "firstrepeat":
				case "recurrid":
					break;

				case "uuid":
					$fieldlist[] = "uuid";
					$therecord["uuid"] = uuid(getUuidPrefix($this->db, "tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883").":");
					break;

				case "checkno":
				case "webconfirmationno":
				case "trackingno":
				case "weborder":
				case "transactionid":
				case "invoicedate":
					$fieldlist[] = $name;
					$therecord[$name] = NULL;
					break;

				case "statusdate":
				case "orderdate":
					$fieldlist[] = $name;
					$therecord[$name] = dateToString(mktime(),"SQL");
					break;

				case "bankname":
				case "ccnumber":
				case "routingnumber":
				case "ccexpiration":
				case "ccverification":
				case "accountnumber":
					$fieldlist[] = $name;
					if(!$therecord["includepaymentdetails"])
						$therecord[$name] = NULL;
					break;

				case "paymenttypeid":
					$fieldlist[] = $name;
					if(!$therecord["includepaymenttype"])
						$therecord[$name] = NULL;
					break;

				case "statusid":
					$fieldlist[] = $name;
					$therecord[$name] = $therecord["newstatusid"];
					break;

				case "assignedtoid":
					$fieldlist[] = $name;
					$therecord[$name] = $therecord["newassignedtoid"];
					break;

				case "amountpaid":
					$fieldlist[] = $name;
					$therecord[$name] = 0;
					break;

				case "type":
					$fieldlist[] = $name;
					$therecord[$name] = "Order";
					break;

				case "readytopost":
					$fieldlist[] = $name;
					$therecord[$name] = 0;
					break;

				default:
					$fieldlist[] = $name;
			}//endswitch
		}//endforeach

		$insertstatement = $this->prepareInsert("invoices",$fieldlist,$therecord);

		$this->db->query($insertstatement);

		$theid = $this->db->insertId();

		$this->copyLineItems($therecord["uuid"],$theid);
		$this->insertHistory($theid,$therecord["statusid"],$therecord["statusdate"],$therecord["assignedtoid"]);

		$this->updateReccurence($therecord["recurrid"],$therecord["firstrepeat"]);

		if($therecord["notificationroleid"])
			$this->sendNotification($therecord["notificationroleid"],$theid);

	}//end method


	function copyLineItems($oldInvoiceID, $newInvoiceID){

		$querystatement = "SELECT * FROM lineitems WHERE invoiceid = '".$oldInvoiceID."'";
		$queryresult = $this->db->query($querystatement);

		while($therecord = $this->db->fetchArray($queryresult)){

			$fieldlist = array();

			foreach($therecord as $name=>$value){
				switch($name){
					case "id":
					case "modifiedby":
					case "modifieddate":
					case "createdby":
					case "creationdate":
						break;

					case "invoiceid":
						$therecord[$name] = "'".$newInvoiceID."'";
						$fieldlist[] = $name;
						break;

					default:
						$fieldlist[] = $name;
				}// endswitch
			}// endforeach

			$insertstatement = $this->prepareInsert("lineitems",$fieldlist,$therecord);

			$this->db->query($insertstatement);

		}// endwhile

	}//end method


	function prepareInsert($tablename, $fieldlist, $therecord){
		$insertstatement = "INSERT INTO ".$tablename." (";

		foreach($fieldlist as $name)
			$insertstatement .= "`".$name."`, ";

		$insertstatement .= " createdby,creationdate) VALUES (";

		foreach($fieldlist as $name)
			if($therecord[$name] !== NULL)
				$insertstatement .= "'".$therecord[$name]."', ";
			else
				$insertstatement .= "NULL, ";

		$insertstatement .="-3, NOW());";

		return $insertstatement;
	}//end method


	function insertHistory($invoiceid, $statusid, $statusdate, $assignedtoid){
		$insertstatement = "INSERT INTO invoicestatushistory (invoiceid, invoicestatusid, statusdate, assignedtoid) VALUES (";
		$insertstatement .= "'".$invoiceid."', ";
		$insertstatement .= "'".$statusid."', ";
		$insertstatement .= "'".$statusdate."', ";
		$insertstatement .= "'".$assignedtoid."')";

		$this->db->query($insertstatement);

	}//end method


	function updateReccurence($recurrid, $firstrepeat){
		$updatestatement = "UPDATE recurringinvoices SET timesrepeated = timesrepeated+1, lastrepeat=NOW()";
		if(!$firstrepeat)
			$updatestatement .= ", firstrepeat=NOW()";

		$updatestatement .= " WHERE id = ".$recurrid;

		$this->db->query($updatestatement);

	}


	function sendNotification($roleid, $newInvoiceID){
		if($roleid == -100)
			$whereclause = "users.admin = 1";
		else
			$whereclause = "rolestousers.roleid = '".$roleid."'";

		$querystatement = "SELECT email FROM rolestousers INNER JOIN users ON rolestousers.userid = users.uuid
							WHERE email != '' AND ".$whereclause;

		$queryresult = $this->db->query($querystatement);

		$subject = APPLICATION_NAME." recurring invoice notification.";
		$message = APPLICATION_NAME." has created a new order from a recurring invoice.  The new order id is ".$newInvoiceID;

		while($therecord = $this->db->fetchArray($queryresult)){
			$to = $therecord["email"];
			$headers = "From: ".$to;

			@ mail ($to,$subject,$message,$headers);
		}// endwhile

	}//end method
}//end class



//PROCESSOR
//=============================================================================================
if(!isset($noProcess)){
	$recurr = new recurr($db);
	$invoiceArray = $recurr->getInvoicesToRepeat();
	foreach($invoiceArray as $invoiceid)
		$recurr->copyInvoice($invoiceid);
}
?>
