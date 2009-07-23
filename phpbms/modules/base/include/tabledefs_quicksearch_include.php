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

class quickSearches{

	var $db;
	var $uuid;

	/**
	 * Initializes quickSearches object
	 *
	 * @param object $db database object
	 * @param integer $id tabledefs id
	 */
	function quickSearches($db, $id){

		$this->db = $db;

		$querystatement = "
			SELECT
				uuid
			FROM
				tabledefs
			WHERE
				id =".((int) $id);

		$queryresult = $this->db->query($querystatement);

		$therecord = $this->db->fetchArray($queryresult);

		$this->uuid = $therecord["uuid"];

	}//end fnction init


	function getDefaults(){

		$therecord["id"] = NULL;
		$therecord["displayorder"] = NULL;

		$therecord["name"] = "";
		$therecord["roleid"] = "";
		$therecord["search"] = "";

		return $therecord;

	}//end function getDefaults


	/**
	 * gets quick search records
	 *
	 * @param integer $id tabledef's id
	 */
	function get($id = 0){

		$querystatement = "
			SELECT
				tablefindoptions.id,
				tablefindoptions.name,
				tablefindoptions.search,
				tablefindoptions.displayorder,
				tablefindoptions.roleid,
				roles.name as rolename
			FROM
				tablefindoptions LEFT JOIN roles ON tablefindoptions.roleid = roles.uuid
			WHERE
				tablefindoptions.tabledefid = '".$this->uuid."'";

		if($id)
			$querystatement .= "
				AND tablefindoptions.id = ".((int) $id);

		$querystatement .= "
			ORDER BY
				tablefindoptions.displayorder";

		$queryresult = $this->db->query($querystatement);

		return $queryresult;

	}//end function get


	/**
	 * adds quck search item
	 *
	 * @param array $variables post array
	 */
	function add($variables){

		$querystatement = "
			INSERT INTO
				tablefindoptions
				(
				tabledefid,
				name,
				`search`,
				roleid,
				displayorder
			) values (
				'".$this->uuid."',
				'".$variables["name"]."',
				'".$variables["search"]."',
				'".$variables["roleid"]."',
				'".$variables["displayorder"]."'
			)";

		if($this->db->query($querystatement))
			return "Quick Search Item Added";
		else
			return false;

	}//end function add


	/**
	 * updates quick search
	 *
	 * @param array $variables post variables
	 */
	function update($variables){

		$querystatement = "
			UPDATE
				tablefindoptions
			SET
				name = '".$variables["name"]."',
				roleid = '".$variables["roleid"]."',
				`search` = '".$variables["search"]."'
			WHERE
				id = ".((int) $variables["quicksearchid"]);

		if($this->db->query($querystatement))
			return "Quick Search Item Updated";
		else
			return false;

	}//end function update


	/*
	 * delete a quick search item
	 *
	 * @param integer $id
	 */
	function delete($id){

		$querystatement = "
			DELETE FROM
				tablefindoptions
			WHERE
				id=".((int) $id);

		if($this->db->query($querystatement))
			return "Quick Search Item Deleted";
		else
			return false;

	}//end function delete

	/**
	 * reorders quick search item (displayorder)
	 *
	 * @param integer $id quick search id
	 * @param string $direction up/down
	 */
	function move($id, $direction = "up"){

		if($direction == "down")
			$increment="1";
		else
			$increment="-1";

		$querystatement = "
			SELECT
				displayorder
			FROM
				tablefindoptions
			WHERE
				id = ".((int) $id);

		$queryresult = $this->db->query($querystatement);

		$therecord = $this->db->fetchArray($queryresult);

		$querystatement = "
			SELECT
				MAX(displayorder) AS themax
			FROM
				tablefindoptions
			WHERE
				tabledefid = '".$this->uuid."'";

		$queryresult = $this->db->query($querystatement);

		$maxrecord = $this->db->fetchArray($queryresult);

		if(!(($direction=="down" && $therecord["displayorder"] == $maxrecord["themax"]) || ($direction == "up" && $therecord["displayorder"] == "0"))){

			$querystatement = "
				UPDATE
					tablefindoptions
				SET
					displayorder=".$therecord["displayorder"]."
				WHERE
					displayorder = ".($increment + $therecord["displayorder"])."
					AND tabledefid = '".$this->uuid."'";

			$thequery = $this->db->query($querystatement);

			$querystatement = "
				UPDATE
					tablefindoptions
				SET
					displayorder = displayorder + ".$increment."
				WHERE
					id =".((int) $id);
			if($this->db->query($querystatement))
				return "Position Moved";

		}//endif

		return false;

	}//end function move

}//end class quickSearches






	function moveQuicksearch($db,$id,$direction="up",$tabledefid){

		if($direction=="down") $increment="1"; else $increment="-1";

		$querystatement="select displayorder,tabledefid FROM tablefindoptions WHERE id=".$id;
		$thequery=$db->query($querystatement);
		$therecord=$db->fetchArray($thequery);

		$querystatement="select max(displayorder) as themax FROM tablefindoptions WHERE tabledefid=".$tabledefid;
		$thequery=$db->query($querystatement);
		$maxrecord=$db->fetchArray($thequery);

		if(!(($direction=="down" and $therecord["displayorder"]==$maxrecord["themax"]) or ($direction=="up" and $therecord["displayorder"]=="0"))){
			$querystatement="UPDATE tablefindoptions set displayorder=".$therecord["displayorder"]."
								WHERE displayorder=".($increment+$therecord["displayorder"])." AND tabledefid=".$tabledefid;
			$thequery=$db->query($querystatement);

			$querystatement="UPDATE tablefindoptions set displayorder=displayorder+".$increment." WHERE id=".$id;
			$thequery=$db->query($querystatement);
		}// end if

		if(isset($thereturn)) return $thereturn; else return "Position Moved";
	}
?>
