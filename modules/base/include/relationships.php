<?php
/*
 $Rev: 254 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-07 18:38:38 -0600 (Tue, 07 Aug 2007) $
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
if(class_exists("phpbmsTable")){

	class relationships extends phpbmsTable{

		var $_availableTabledefUUIDs = NULL;
		var $_availableTabledefNames = NULL;

		//function populates the tabledef ids
		//and the tabledef names corresponding to those ids
		function populateTabledefArrays(){

			$this->_availableTabledefUUIDs = array();
			$this->_availableTabledefNames = array();

			$querystatement = "
				SELECT
					`uuid`,
					`displayname`
				FROM
					`tabledefs`
				WHERE
					`type` != 'view'
				ORDER BY
					`displayname`;
				";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){

				while($therecord = $this->db->fetchArray($queryresult)){

					$this->_availableTabledefUUIDs[] = $therecord["uuid"];
					$this->_availableTabledefNames[] = $therecord["displayname"];

				}//end while

			}//end if

		}//end method --populateArrays--


		function verifyVariables($variables){

			//cannot be table default ("")
			if(isset($variables["tofield"])){
				if($variables["tofield"] === "" || $variables["tofield"] === NULL)
					$this->verifyErrors[] = "The `tofield` field cannot be blank.";
			}else
				$this->verifyErrors[] = "The `tofield` field must be set.";

			//cannot be table default ("")
			if(isset($variables["fromfield"])){
				if($variables["fromfield"] === "" || $variables["fromfield"] === NULL)
					$this->verifyErrors[] = "The `from` field cannot be blank.";
			}else
				$this->verifyErrors[] = "The `fromfield` field must be set.";

			//cannot be table default
			if(isset($variables["fromtableid"])){

				if($this->_availableTabledefUUIDs === NULL || $this->_availableTabledefNames === NULL)
						$this->populateTableDefArrays();

				if(!in_array($variables["fromtableid"], $this->_availableTabledefUUIDs))
						$this->verifyErrors[] = "The `fromtableid` field does not give an existing/acceptable parent id number.";

			} else
				$this->verifyErrors[] = "The `fromtableid` field must be set.";

			//cannot be table default
			if(isset($variables["totableid"])){

				if($this->_availableTabledefUUIDs === NULL || $this->_availableTabledefNames === NULL)
						$this->populateTableDefArrays();

				if(!in_array($variables["totableid"], $this->_availableTabledefUUIDs))
					$this->verifyErrors[] = "The `totableid` field does not give an existing/acceptable to table id number.";
			} else
				$this->verifyErrors[] = "The `totableid` field must be set.";

			//check boolean
			if(isset($variables["inherit"]))
				if($variables["inherit"] && $variables["inherit"] != 1)
					$this->verifyErrors[] = "The `inherit` field must be a boolean (equivalent to 0 or exactly 1).";

			return parent::verifyVariables($variables);

		}//end method --verifyVariables--


		function displayTables($fieldname,$selectedid){

			if(!count($this->_availableTabledefUUIDs) || !count($this->_availableTabledefNames))
				$this->populateTabledefArrays();

			echo "<select id=\"".$fieldname."\" name=\"".$fieldname."\">\n";

			for($i = 0; $i < count($this->_availableTabledefUUIDs); $i++){
				echo "	<option value=\"".$this->_availableTabledefUUIDs[$i]."\"";
					if($selectedid==$this->_availableTabledefUUIDs[$i]) echo " selected=\"selected\"";
				echo ">".$this->_availableTabledefNames[$i]."</option>\n";
			}

			echo "</select>\n";
		}

	}//end class
}//end if

?>
