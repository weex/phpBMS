<?php

	class tablePost{

		var $db;
		var $whereclause = "";
		var $modifiedby = NULL;

		var $maintable = "";
		var $datefieldname = "";

		function tablePost($db, $modifiedby = NULL){

			$this->db = $db;

			if($modifiedby)
				$this->modifiedby = $modifiedby;
			else
				$this->modifiedby = $_SESSION["userinfo"]["id"];

		}//end method init


		function getRecordsToPost($startdate, $enddate){
			// This function creates an array of
			// record ids within the date range given

			$idarray = array();

			$querystatement = "
				SELECT
					`".$this->maintable."`.id
				FROM
					`".$this->maintable."`
				WHERE
					`".$this->maintable."`.readytopost = 1
					AND `".$this->maintable."`.`".$this->datefieldname."` >= '".dateToString($startdate, "SQL")."'
					AND `".$this->maintable."`.`".$this->datefieldname."` <= '".dateToString($enddate, "SQL")."'";

			$queryresult = $this->db->query($querystatement);

			while($thecord = $this->db->fetchArray($queryresult))
				$idarray[] = $therecord["id"];

			//return array of ids within that date range
			return $idarray;

		}//end method getRecordsToPost


		function whereFromIds($idarray){
			// Takes an array of ids ad creates a SQL where clause
			// segment using 'IN'

			$where = "`".$this->maintable."`.id IN";
			if(count($idarray))
				$where .= " IN(".implode(",", $idarray).")";
			else
				$where .= " = -1000";

			return $where;

		}//end method whereFromIds


		function post($whereclause, $postsessionid = NULL){
			// Posts the records.  In almost all cases,
			// this function will be overriden

			if(!$postsessionid)
				$postsessionid = $this->generatePostingSession("search");

			$updatestatement = "
				UPDATE
					`".$this->maintable."`
				SET
					`".$this->maintable."`.posted = 1,
					modifiedby = ".$this->modifiedby.",
					modifieddate = NOW()
				WHERE
					".$whereclause;

			$this->db->query($updatestatement);

			//return the number of records that successfully posted.
			$records = $this->db->affectedRows();

			$this->updatePostingSession($postsessionid, $records);

			return $records;

		}//end method


		function generatePostingSession($source){
			// in case a posting initiates from a search screen, we
			// need to create the session record

			$insertstatement = "
				INSERT INTO
					postingsessions
					(sessiondate, `source`, recordsposted, userid)
				VALUES
					(
					NOW(),
					'".$source."',
					0,
					".$this->modifiedby."
					)";

			$this->db->query($insertstatement);

			return $this->db->insertId();

		}//end function generatePostingSession


		function updatePostingSession($sessionid, $numrecords){

			$updatestatement = "
				UPDATE
					postingsessions
				SET
					recordsposted = recordsposted + ".$numrecords."
				WHERE
					id = ".$sessionid;

			$this->db->query($updatestatement);

		}//end function updatePostingSession

	}//end class

?>
