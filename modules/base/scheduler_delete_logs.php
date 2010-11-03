<?php

//uncomment for debug purposes
//if(!class_exists("appError"))
//	include_once("../../include/session.php");

class cleanSysLog{

	function cleanSysLog($db){
		$this->db = $db;
	}//end method --cleanImports--

	//This method should remove any logs older than the 2000th
	//when ordering by creationdate (desc)
	function removeExcessLogs(){

		$querystatement = "
			SELECT
				`id`,
				`stamp`
			FROM
				`log`
			ORDER BY
				`stamp` DESC
			LIMIT
				2000,1;
			";

		$queryresult = $this->db->query($querystatement);

		if($this->db->numRows($queryresult)){

			$therecord = $this->db->fetchArray($queryresult);

			$querystatement = "
				DELETE FROM
					`log`
				WHERE
					`stamp` > '".$therecord["stamp"]."';
				";

			$queryresult = $this->db->query($querystatement);

		}//end if

	}//end method --removeExcessLogs-

}//end class --cleanImports--

if(!isset($noOutput) && isset($db)){

    $clean = new cleanSysLog($db);
    $clean->removeExcessLogs();

}//end if
?>
