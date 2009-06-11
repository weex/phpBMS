<?php

	class clientAddress {

		var $clientuuid;
		var $clientid;

		function clientAddress($db, $clientid){

			$this->db = $db;

			$this->clientid = (int) $clientid;

			$querystatement = "
				SELECT
					`uuid`
				FROM
					`clients`
				WHERE
					`id` = '".$this->clientid."'
			";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){
				$therecord = $this->db->fetchArray($queryresult);
				$this->clientuuid = $therecord["uuid"];
			}else{
				$this->clientuuid = "";
			}

		}//end method


		function getPageTitle(){

			//set the page title (need tr grab client information)
			$querystatement = "
				SELECT
					firstname,
					lastname,
					company
				FROM
					clients
				WHERE
					id=".$this->clientid;

			$queryresult = $this->db->query($querystatement);
			$refrecord = $this->db->fetchArray($queryresult);

			$pageTitle = "Addresses: ";

			if($refrecord["company"]=="")
				$pageTitle.=$refrecord["firstname"]." ".$refrecord["lastname"];
			else
				$pageTitle.=$refrecord["company"];

			$pageTitle = htmlQuotes($pageTitle);

			return $pageTitle;

		}//end method

	}//end class


?>