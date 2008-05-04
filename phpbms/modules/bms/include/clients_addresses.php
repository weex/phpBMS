<?php

	class clientAddress {
	
		function clientAddress($db, $clientid){
				
				$this->db = $db;
				
				$this->clientid = (int) $clientid;
				
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