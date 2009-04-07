<?php

//uncomment for debug purposes
//if(!class_exists("appError"))
//	include_once("../../include/session.php");

class cleanImports{

	function cleanImports($db){
		$this->db = $db;
	}//end method --cleanImports--

	function removeTempCSV(){

		$querystatement = "
			DELETE FROM
				`files`
			WHERE
				`type` = 'phpbms/temp'
				AND
				`creationdate` <= NOW() - INTERVAL '30' MINUTE
				;
			";

		$queryresult = $this->db->query($querystatement);

	}//end method --_removeTempCSV--

}//end class --cleanImports--

if(!isset($noProcess)){
	$clean = new cleanImports($db);
	$clean->removeTempCSV();
}//end if
?>