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
error_reporting(E_ALL);
define("APP_DEBUG",false);
define("noStartup",true);

include("../../../install/install_include.php");
include("../../../include/session.php");

	function doUpdate($db) {
		$thereturn="Updating Business Management System Module\n";
		
		if(!verifyAdminLogin($db,$_GET["u"],$_GET["p"])){
			$thereturn="Update Requires Administrative Access.\n\n";
			return $thereturn;
		}
				
		$newVersion = $_GET["v"];
		
		$querystatement="SELECT version FROM modules WHERE name=\"bms\"";
		$queryresult=$db->query($querystatement);
		if(!$queryresult) {
			$thereturn="Error Accessing module table in database.\n\n";
			return $thereturn;
		}
		
		$ver=$db->fetchArray($queryresult);

		while($ver["version"] != $newVersion){
			switch($ver["version"]){
				// ================================================================================================
				case "0.5":
					$thereturn.="Updating BMS Module to 0.51\n";
		
					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.51\" WHERE name=\"bms\";";
					$queryresult=$db->query($querystatement);
					$thereturn.=" - modified bms record in modules table\n";
					
					$thereturn.="Update to 0.51 Finished\n\n";
			
					$ver["version"]="0.51";
				break;
				// ================================================================================================
				case "0.51":
					$thereturn.="Updating BMS Module to 0.6\n";
							
					$thereturn.=processSQLfile($db,"updatev0.6.sql");
					
					$thereturn.=importData($db,"choices");
					$thereturn.=importData($db,"menu");
					$thereturn.=importData($db,"reports");
					$thereturn.=importData($db,"tablecolumns");
					$thereturn.=importData($db,"tabledefs");
					$thereturn.=importData($db,"tablefindoptions");
					$thereturn.=importData($db,"tableoptions");
					$thereturn.=importData($db,"tablesearchablefields");

					$querystatement="SELECT clients.id,DATE_FORMAT(clients.creationdate,\"%Y-%m-%d\") as creationdate,max(invoices.orderdate) as orderdate
									FROM `clients` LEFT JOIN invoices on clients.id=invoices.clientid 
									WHERE clients.type=\"client\" GROUP BY clients.id;";
					$queryresult=$db->query($querystatement);

					while($therecord=$db->fetchArray($queryresult,$dblink)){
						$querystatement="UPDATE clients set becameclient=\"";
						if($therecord["orderdate"])
							$querystatement.=$therecord["orderdate"];
						else
							$querystatement.=$therecord["creationdate"];
						$querystatement.="\" WHERE id=".$therecord["id"];
						$updateresult=$db->query($querystatement);
					}
					$thereturn.=" - set intitial client becamclient field\n";
					

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.6\" WHERE name=\"bms\";";
					$updateresult=$db->query($querystatement);
					$thereturn.=" - modified bms record in modules table\n";

					$thereturn.="Update to 0.6 Finished\n\n";
			
					$ver["version"]="0.6";
				break;
				// ================================================================================================
				case "0.6";
					$thereturn.="Updating BMS Module to 0.601\n";

					$querystatement="SELECT invoices.id,tax.percentage FROM invoices INNER JOIN tax on invoices.taxareaid=tax.id";
					$queryresult=$db->query($querystatement);
					
					
					while($therecord=$db->fetchArray($queryresult)){
						$querystatement="UPDATE invoices SET taxpercentage=".$therecord["percentage"]."WHERE id=".$therecord["id"];
						$updateresult=$db->query($querystatement);
					}
					$thereturn.=" - set taxpercentage on invoices\n";

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.601\" WHERE name=\"bms\";";
					$updateresult=$db->query($querystatement);
					$thereturn.=" - modified bms record in modules table\n";


					$thereturn.="Update to 0.601 Finished\n\n";
			
					$ver["version"]="0.601";

				break;
				// ================================================================================================
				case "0.601";
					$thereturn.="Updating BMS Module to 0.602\n";

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.602\" WHERE name=\"bms\";";
					$updateresult=$db->query($querystatement);
					$thereturn.=" - modified bms record in modules table\n";

					$thereturn.="Update to 0.602 Finished\n\n";
			
					$ver["version"]="0.602";
				// ================================================================================================
				case "0.602";
					$thereturn.="Updating BMS Module to 0.61\n";

					$thereturn.=processSQLfile($db,"updatev0.61.sql");

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.61\" WHERE name=\"bms\";";
					$updateresult=$db->query($querystatement);
					$thereturn.=" - modified bms record in modules table\n";

					$thereturn.="Update to 0.61 Finished\n\n";
			
					$ver["version"]="0.61";
				break;
				// ================================================================================================
				case "0.61";
					$thereturn.="Updating BMS Module to 0.62\n";

					$thereturn.=processSQLfile($db,"updatev0.62.sql");

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.62\" WHERE name=\"bms\";";
					$updateresult=$db->query($querystatement);
					$thereturn.=" - modified bms record in modules table\n";

					$thereturn.="Update to 0.62 Finished\n\n";
			
					$ver["version"]="0.62";
				break;
				// ================================================================================================
				case "0.62";
					$thereturn.="Updating BMS Module to 0.7\n";
					
					$thereturn.=processSQLfile($db,"updatev0.70.sql");
					
					//update to new status system
					$result=updateInvoiceStatus($db);
					if($result===true)
						$thereturn.=" - Updated to new invoice status system\n";
					else
						$thereturn.=" - Failed to updated to new invoice status system\n".$result."\n\n";					
					
					//Update shipping from invoices
					$result=moveShipping($db);
					if($result===true)
						$thereturn.=" - Created default Shipping Methods\n";
					else
						$thereturn.=" - Failed to create default shipping methods\n".$result."\n\n";
					
					//update payment From invoices
					$result=movePayments($db);
					if($result===true)
						$thereturn.=" - Created default payment methods\n";
					else
						$thereturn.=" - Failed to create default payment Methods\n".$result."\n\n";
					
					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.7\" WHERE name=\"bms\";";
					$updateresult=$db->query($querystatement);
					$thereturn.=" - Updated bms module record with new version\n";

					$thereturn.="Update to 0.7 Finished\n\n";
			
					$ver["version"]="0.7";
				break;

				// ================================================================================================
				case "0.7";

					$thereturn.= processSQLfile($db,"updatev0.80.sql");

					//Updating Module Table
					$querystatement="UPDATE modules SET version=\"0.8\" WHERE name=\"bms\";";
					$updateresult=$db->query($querystatement);

					$thereturn.="Update of Business Management System Module to 0.8 Finished\n\n";
					$ver["version"]="0.8";
				break;

				// ================================================================================================
				case "0.8";

					$thereturn.= processSQLfile($db,"updatev0.90.sql");

					//Updating Module Table
					$querystatement = "
						UPDATE 
							modules 
						SET 
							version='0.9' 
						WHERE 
							name='bms';";

					$updateresult = $db->query($querystatement);

					$thereturn .= "Update of Business Management System Module to 0.9 Finished\n\n";

					$ver["version"] = "0.9";

					break;
					
			}//end switch
		}//end while
		return $thereturn;

	}//end update		

	function moveShipping($db){
		$querystatement="SELECT DISTINCT shippingmethod FROM invoices WHERE shippingmethod!=\"\" ORDER BY shippingmethod";
		$queryresult=$db->query($querystatement);
		
		while($therecord=$db->fetchArray($queryresult)){
			$querystatement="INSERT INTO `shippingmethods` (name,createdby,creationdate) VALUES (\"".$therecord["shippingmethod"]."\",1,NOW())";
			$updatequery=$db->query($querystatement);
		}

		$querystatement="SELECT id,name FROM shippingmethods";
		$queryresult=$db->query($querystatement);

		while($therecord=$db->fetchArray($queryresult)){
			$querystatement="UPDATE invoices SET shippingmethodid=".$therecord["id"]."
							WHERE shippingmethod=\"".$therecord["name"]."\"";
			$updatequery=$db->query($querystatement);
		}
		$querystatement="ALTER TABLE invoices DROP shippingmethod";
		$updatequery=$db->query($querystatement);
		
		return true; 		 
	}

	function movePayments($db){
		$querystatement="SELECT DISTINCT paymentmethod FROM invoices WHERE paymentmethod!=\"\" ORDER BY paymentmethod";
		$queryresult=$db->query($querystatement);

		while($therecord=$db->fetchArray($queryresult)){
			switch($therecord["paymentmethod"]){
				case "VISA":
				case "VISA - Debit": 
				case "American Express":
				case "Master Card":
				case "MasterCard":
				case "Discover Card":
					$type="\"charge\"";
				break;
				
				case "Personal Check":
				case "Check":
				case "Cashiers Check":
				case "check":
					$type="\"draft\"";
				break;				
				
				default:
					$type="NULL";
				break;
			}
			
			$querystatement="INSERT INTO `paymentmethods` (name,`type`,createdby,creationdate) VALUES (\"".$therecord["paymentmethod"]."\",".$type.",1,NOW())";
			$updatequery=$db->query($querystatement);
		}

		$querystatement="SELECT id,name FROM paymentmethods";
		$queryresult=$db->query($querystatement);
		while($therecord=$db->fetchArray($queryresult)){
			$querystatement="UPDATE invoices SET paymentmethodid=".$therecord["id"]."
							WHERE paymentmethod=\"".$therecord["name"]."\"";
			$updatequery=$db->query($querystatement);
		}
		$querystatement="ALTER TABLE invoices DROP paymentmethod";
		$updatequery=$db->query($querystatement);
		
		return true;
	}

	function updateInvoiceStatus($db){
		$querystatement="SELECT id,status,statusdate,orderdate,invoicedate,type FROM invoices";
		$queryresult=$db->query($querystatement);
		
		while($therecord=$db->fetchArray($queryresult)){
			$newstatus=1;
			switch($therecord["status"]){
				case "Open":
					$newstatus=1;
					$statusdate=$therecord["orderdate"];
				break;
				case "Committed":
					$newstatus=2;
					$statusdate=$therecord["orderdate"];					
				break;
				case "Packed":
					$newstatus=3;
				break;
				case "Shipped":
					$newstatus=4;
					if($therecord["statusdate"])
						$statusdate=$therecord["statusdate"];
					elseif($therecord["invoicedate"])
						$statusdate=$therecord["invoicedate"];
					else
						$statusdate=$therecord["orderdate"];
				break;
				if($therecord["type"]=="Invoice")
					$statusdate=$therecord["invoicedate"];
			}
			$querystatement="UPDATE invoices SET statusid=".$newstatus.", statusdate=\"".$statusdate."\" WHERE id=".$therecord["id"];
			$updatequery=$db->query($querystatement);

			//now create the history
			$querystatement="INSERT INTO invoicestatushistory (invoiceid,invoicestatusid,statusdate)VALUES(".$therecord["id"].",".$newstatus.",\"".$statusdate."\")";
			$insertquery=$db->query($querystatement);
			
		}
		$querystatement="ALTER TABLE `invoices` DROP COLUMN `status`";
		$dropcolumnquery=$db->query($querystatement);
		
		return true;
	}//end funtion

	
	$phpbmsSession = new phpbmsSession;
	$success = $phpbmsSession->loadDBSettings(false);

	include_once("include/db.php");
	$db = new db(false);
	$db->stopOnError = false;
	$db->showError = false;
	$db->logError = false;

	if($success !== false){
		
		if(!$db->connect())
			$thereturn = "Could Not Establish Connection To MySQL Server: Check server, user name, and password."; 			
		else {
			if(!$db->selectSchema())
				$thereturn = "Database (schema) ".MYSQL_DATABASE." could not be selected";			
			else {

				$phpbmsSession->db = $db;
				$phpbmsSession->loadSettings();
				
				$thereturn=doUpdate($db);	

			}		
		}
	} else
		$thereturn = "Could not access settings.php";	
				


		header('Content-Type: text/xml');
		?><?php echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'; ?>
<response><?php echo $thereturn?></response>