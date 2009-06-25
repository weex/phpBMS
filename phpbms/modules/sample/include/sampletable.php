<?php
/*
	Here you can define class overrides, if your defaults, get record, insert, or
	update functions deviate from the phpbmsTable defaults.

	You can also use this class to define any other functions
	that helps in the above functions, or with display.

	This sample will show you how to do some basic overides.
	Remember to instantiate the overrided class on the add/edit page.


*/
if(class_exists("phpbmsTable")){
	class tablename extends phpbmsTable{

		// CLASS OVERRIDES ===================================================================

		function getDefaults(){
			$therecord = parent::getDefaults();

			$therecord["type"]="prospect";
			$therecord["webaddress"]="http://";

			return $therecord;
		}


		function prepareVariables($variables){
			if ($variables["webaddress"]=="http://")
				$variables["webaddress"] = NULL;

			return $variables;
		}


		function updateRecord($variables, $modifiedby = NULL, $useUuid = false){

			$variables = $this->prepareVariables($variables);

			return parent::updateRecord($variables, $modifiedby, $useUuid);
		}


		function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false, $useUuid = false){

			$variables = $this->prepareVariables($variables);

			return parent::insertRecord($variables, $createdby, $overrideID, $replace, $useUuid);
		}//end method

	}//end class
}//end if


/*

	You also use this file to define any search screen (command select box)
	functions for the table, including delete record overrides.

*/
if(class_exists("searchFunctions")){
	class clientsSearchFunctions extends searchFunctions{

		function mark_asclient(){

			//passed variable is array of user ids to be revoked
			$whereclause = $this->buildWhereClause();

			$querystatement = "UPDATE clients SET clients.type=\"client\",modifiedby=\"".$_SESSION["userinfo"]["id"]."\" WHERE (".$whereclause.");";
			$queryresult = $this->db->query($querystatement);

			$message = $this->buildStatusMessage();
			$message.=" converted to client.";
			return $message;
		}


		//Stamp Comments Field with info packet sent
		function stamp_infosent(){

			//passed variable is array of user ids to be revoked
			$whereclause = $this->buildWhereClause();

			$querystatement = "update clients set comments=concat(\"Information Packet Sent\",char(10),comments), modifiedby=".$_SESSION["userinfo"]["id"]." where ".$whereclause.";";
			$queryresult = $this->db->query($querystatement);

			$message = $this->buildStatusMessage();
			$message.=" marked as info packet sent.";
			return $message;
		}


		//remove prospects
		function delete_prospects(){

			//passed variable is array of user ids to be revoked
			$whereclause = $this->buildWhereClause();

			$querystatement = "DELETE FROM clients where (".$whereclause.") and type=\"prospect\";";
			$queryresult = $this->db->query($querystatement);

			$message = $this->buildStatusMessage();
			$message.=" deleted.";
			return $message;
		}


		function massEmail(){
			$_SESSION["emailids"]= $this->idsArray;
			goURL("modules/bms/clients_email.php");
		}


	}//end class
}//end if
?>