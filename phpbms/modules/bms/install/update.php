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

// Need custom class for upgrade because
// some BMS upgrades require more than just
// version SQL file processing
class updateBMS extends updateModuleAjax{

	function updateBMS($db, $phpbmsSession, $moduleName, $pathToModule){

		$this->updateModuleAjax($db, $phpbmsSession, $moduleName, $pathToModule);

	}//end function init

	//override function for custom upgrading
	function update(){

		if(!$this->db->connect())
			return $this->returnJSON(false, "Could not connect to database ".$this->db->getError());

		if(!$this->db->selectSchema())
			return $this->returnJSON(false, "Could not open database schema '".MYSQL_DATABASE."'");

		$updater = new installer($this->db);

		include("../modules/bms/version.php");
		$newVersion = $modules["bms"]["version"];
		$currentVersion = $this->currentVersion;

		//next we loop through each upgrade process
		while($currentVersion != $newVersion){

			switch($currentVersion){

				// ================================================================================================
				case 0.8:

					$version = 0.9;
					//Processing Data Structure Changes
					$thereturn = $updater->processSQLfile("../modules/bms/install/updatev".$version.".sql");
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					//Updating Module Table
					$thereturn = $this->updateModuleVersion("bms", $version);
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					$currentVersion = $version;

					break;

				// ================================================================================================
				case 0.9:

					$version = 0.92;
					//Processing Data Structure Changes
					$thereturn = $updater->processSQLfile("../modules/bms/install/updatev".$version.".sql");
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					//Updating Module Table
					$thereturn = $this->updateModuleVersion("bms", $version);
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					$currentVersion = $version;

					break;

				// ================================================================================================
				case 0.92:

					$version = 0.94;
					//Processing Data Structure Changes
					$thereturn = $updater->processSQLfile("../modules/bms/install/updatev".$version.".sql");
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					//Updating Module Table
					$thereturn = $this->updateModuleVersion("bms", $version);
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					$currentVersion = $version;

					break;

				// ================================================================================================
				case 0.94:

					$version = 0.96;
					//Processing Data Structure Changes
					$thereturn = $updater->processSQLfile("../modules/bms/install/updatev".$version.".sql");
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					if(!$this->v096updateInvoiceAddresses())
						return $this->returnJSON(false, "v0.96 Invoice Addresses Movement Failed");

					if(!$this->v096transferClientAddresses())
						return $this->returnJSON(false, "v0.96 Client Addresses Movement Failed");

					//Updating Module Table
					$thereturn = $this->updateModuleVersion("bms", $version);
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					$currentVersion = $version;

					break;

				// ================================================================================================
				case 0.96:

					$version = 0.98;

					//Processing Data Structure Changes
					$thereturn = $updater->processSQLfile("../modules/bms/install/updatev".$version.".sql");
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					//Create first posting session record
					$this->v098UpdatePostingSession();

					//Updating Module Table
					$thereturn = $this->updateModuleVersion("bms", $version);
					if($thereturn !== true)
						return $this->returnJSON(false, $thereturn);

					$currentVersion = $version;

					break;

			}//endswitch currentVersion

		}//endwhile currentversion/newversion


		return $this->returnJSON(true, "Module '".$this->moduleName."' Updated");

	}//end function update


//==== v0.96 Specific Function =================================================

	function v096updateInvoiceAddresses(){

		$querystatement = "
			SELECT
				invoices.id,
				clients.address1,
				clients.address2,
				clients.city,
				clients.state,
				clients.postalcode,
				clients.country
			FROM
				invoices INNER JOIN clients ON invoices.clientid = clients.id";

		$queryresult = $this->db->query($querystatement);

		while($therecord = $this->db->fetchArray($queryresult)){

			$updatestatement = "
				UPDATE
					invoices
				SET
					shiptoaddress1 = address1,
					shiptoaddress2 = address2,
					shiptocity = city,
					shiptostate = state,
					shiptopostalcode = postalcode,
					shiptocountry = country,
					address1 = 	'".$therecord["address1"]."',
					address2 =	'".$therecord["address2"]."',
					city = 		'".$therecord["city"]."',
					state = 	'".$therecord["state"]."',
					postalcode ='".$therecord["postalcode"]."',
					country = 	'".$therecord["country"]."'
				WHERE
					id = ".$therecord["id"];

				$this->db->query($updatestatement);

		}//endwhile - record

		return true;

	}//end function - v096updateInvoiceAddresses


	function v096transferClientAddresses(){

		//retrieve all client records with ship to addresses
		$querystatement = "
			SELECT
				id,
				address1,
				address2,
				city,
				state,
				postalcode,
				country,
				shiptoaddress1,
				shiptoaddress2,
				shiptocity,
				shiptostate,
				shiptopostalcode,
				shiptocountry
			FROM
				clients";

		$queryresult = $this->db->query($querystatement);

		while($therecord = $this->db->fetchArray($queryresult)){

			// for each client with a ship to, we need to create
			// an address record (and addresstorecord record)

			// Create the address record
			$address["title"] = "Primary";
			$address["address1"] = $therecord["address1"];
			$address["address2"] = $therecord["address2"];
			$address["city"] = $therecord["city"];
			$address["state"] = $therecord["state"];
			$address["postalcode"] = $therecord["postalcode"];
			$address["country"] = $therecord["country"];

			$newid = $this->v096insertAddress($address);

			$a2r["clientid"] = $therecord["id"];
			$a2r["addressid"] = $newid;
			$a2r["primary"] = 1;

			if($therecord["shiptoaddress1"]){

				$a2r["defaultshipto"] = 0;
				$this->v096insertA2R($a2r);

				$address["title"] = "Shipping";
				$address["address1"] = $therecord["shiptoaddress1"];
				$address["address2"] = $therecord["shiptoaddress2"];
				$address["city"] = $therecord["shiptocity"];
				$address["state"] = $therecord["shiptostate"];
				$address["postalcode"] = $therecord["shiptopostalcode"];
				$address["country"] = $therecord["shiptocountry"];

				$newid = insertAddress($db, $address);

				$a2r["addressid"] = $newid;
				$a2r["primary"] = 0;
				$a2r["defaultshipto"] = 1;

			} else {

				$a2r["defaultshipto"] = 1;

			}//endif - shiptoaddress1

			$this->v096insertA2R($db, $a2r);

		}//endwhile

		//Lastly, we need to remove the shipto fields
		$alterstatement = "
			ALTER TABLE `clients`
				DROP COLUMN `address1`,
				DROP COLUMN `address2`,
				DROP COLUMN `city`,
				DROP COLUMN `state`,
				DROP COLUMN `postalcode`,
				DROP COLUMN `country`,
				DROP COLUMN `shiptoaddress1`,
				DROP COLUMN `shiptoaddress2`,
				DROP COLUMN `shiptocity`,
				DROP COLUMN `shiptostate`,
				DROP COLUMN `shiptopostalcode`,
				DROP COLUMN `shiptocountry`";

		$this->db->query($alterstatement);

	}//end function


	function v096insertA2R($variables){

		// Create the relation record
		$insertstatement = "
			INSERT INTO
				addresstorecord
			(
				tabledefid,
				recordid,
				addressid,
				`primary`,
				defaultshipto,
				createdby,
				creationdate,
				modifiedby,
				modifieddate
			) VALUES (
				2,
				".$variables["clientid"].",
				".$variables["addressid"].",
				".$variables["primary"].",
				".$variables["defaultshipto"].",
				1,
				NOW(),
				1,
				NOW()
			)";

		$this->db->query($insertstatement);

	}//end function - insertA2R


	function v096insertAddress($variables){

			$insertaddress = "
				INSERT INTO
					addresses
				(
					title,
					address1,
					address2,
					city,
					state,
					postalcode,
					country,
					createdby,
					creationdate,
					modifiedby,
					modifieddate
				) VALUES (
					'".$variables["title"]."',
					'".$variables["address1"]."',
					'".$variables["address2"]."',
					'".$variables["city"]."',
					'".$variables["state"]."',
					'".$variables["postalcode"]."',
					'".$variables["country"]."',
					1,
					NOW(),
					1,
					NOW()
				)
			";

			$this->db->query($insertaddress);

			//make sure to get the new address id
			return $this->db->insertId();

	}//end function - insertAddress


	//==== v0.98 Specific Function =================================================

	function v098UpdatePostingSession(){

		$records = 0;

		// Update invoices with to-be-created posting session id of 1
		$updatestatement = "
			UPDATE
				invoices
			SET
				postingsessionid = 1
			WHERE
				type = 'Invoice'";

		$this->db->query($updatestatement);

		$records += (int) $this->db->affectedRows();

		// Update receipts with to-be-created posting session id of 1
		$updatestatement = "
			UPDATE
				reciepts
			SET
				postingsessionid = 1
			WHERE
				posted = 1";

		$this->db->query($updatestatement);

		$records += (int) $this->db->affectedRows();

		//Create the posting session id if there were any posted records
		if($records){

			$insertstatement = "
				INSERT INTO
					postingsessions
					(id, sessiondate, `source`, recordsposted, userid)
				VALUES
					(
					1,
					NOW(),
					'Initial Upgrade',
					".$records.",
					1
					)";

			$this->db->query($insertstatement);

		}//endif records

	}//end function v098UpdatePostingSession

}//end class updateBMS

// Processor
//=========================================================
$theModule = new updateBMS($this->db, $this->phpbmsSession, "bms", "../modules/bms/install/");
