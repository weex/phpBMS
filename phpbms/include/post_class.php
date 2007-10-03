<?php

	class tablePost{
		
		var $db;
		var $whereclause = "";
		var $modifiedby = NULL;
		
		function tablePost($db, $modifiedby = NULL){
			
			$this->db = $db;
			
			if($modifiedby)
				$this->modifiedby = $modifiedby;
			else
				$this->modifiedby = $_SESSION["userinfo"]["id"];
			
		}//end method
	
	}//end class

?>