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
	require("include/session.php");

	class savedSearch{

		var $db;

		function savedSearch($db){
			$this->db=$db;
		}

		function delete($id){
			$querystatement="DELETE FROM usersearches
							WHERE id=".((int) $id);
			$queryresult = $this->db->query($querystatement);

			echo "success";
		}


		/**
		 * saves current search
		 *
		 * @param string $name name to save search as
		 * @param integer $tabledefid table definition's id
		 * @param string $userid uuid of user
		 */
		function save($name,$tabledefid,$userid){

			$uuid = getUuid($this->db, "tbld:5c9d645f-26ab-5003-b98e-89e9049f8ac3", $tabledefid);

			$querystatement = "
                SELECT
                    `prefix`
                FROM
                    `tabledefs`
                WHERE
                    `uuid` = '".$uuid."'
            ";

            $queryresult = $this->db->query($querystatement);

            $therecord = $this->db->fetchArray($queryresult);
            $prefix = $therecord["prefix"];

			$insertstatement = "
				INSERT INTO
					usersearches
				(
					userid,
					tabledefid,
					name,
					`type`,
					sqlclause,
					`uuid`
				) VALUES (
					'".mysql_real_escape_string($userid)."',
					'".mysql_real_escape_string($uuid)."',
					'".mysql_real_escape_string($name)."',
					'SCH',
					'".addslashes($_SESSION["tableparams"][$tabledefid]["querywhereclause"])."',
					'".uuid($prefix.":")."'
				)";

			$this->db->query($insertstatement);

			echo "search saved";

		}//endfunction save


		/**
		 * displays sql clause for saved search
		 *
		 * @param integer $id savedsearch id
		 */
		function get($id){

		    $querystatement="
				SELECT
					sqlclause
				FROM
					usersearches
				WHERE id=".((int) $id);

		    $queryresult = $this->db->query($querystatement);

		    $therecord = $this->db->fetchArray($queryresult);

		    echo $therecord["sqlclause"];

		}//end function


		/**
		 * generates the select input of saved searches
		 *
		 * @param mysql query result $queryresult
		 */
		function showSavedSearchList($queryresult){

			$numrows = $this->db->numRows($queryresult);

			?>
			<select id="LSList" name="LSList" <?php if ($numrows<1) echo "disabled" ?> size="10" style="width:170px;height:160px;" onchange="LSsearchSelect(this,'<?php echo APP_PATH ?>')">
				<?php if($numrows<1) {?>

					<option value="NA">No Saved Searches</option>

				<?php
					} else {

						$numglobal=0;

						while($therecord=$this->db->fetchArray($queryresult))
							if($therecord["userid"]<1) $numglobal++;

						$this->db->seek($queryresult,0);

						 if($numglobal>0){ ?>
							<option value="NA" style="font-style:italic;font-weight:bold"> -- global searches ---------</option>
						<?php
						}//end if

						$userqueryline = true;

						while($therecord=$this->db->fetchArray($queryresult)){

							if ($therecord["userid"] != '' and $userqueryline) {

								$userqueryline = false;

								?><option value="NA" style="font-style:italic;font-weight:bold"> -- user searches ---------</option><?php

							}//endif

							?><option value="<?php echo $therecord["id"]?>"><?php echo $therecord["name"]?></option><?php

						}// end while

					}//end if
				?>
			</select>
			<?php

		}//end function showSavedSearchList


		/**
		 * displays the load box for saved searches
		 *
		 * @param integer $tabledefid id of tabledef
		 * @param string $userid uuid of user
		 * @param string $securitywhere additional security based where clause to pass
		 */
		function showLoad($tabledefid,$userid,$securitywhere){

			$uuid = getUuid($this->db, "tbld:5c9d645f-26ab-5003-b98e-89e9049f8ac3", $tabledefid);

			$querystatement = "
				SELECT
					id,
					name,
					userid
				FROM
					usersearches
				WHERE
					tabledefid = '".$uuid."'
					AND type='SCH'
					AND (
						(userid = '' ".$securitywhere.")
						OR userid = '".$userid."')
				ORDER BY
					userid,
					name";

			$queryresult = $this->db->query($querystatement);

			if(!$queryresult)
				$error = new appError(500,"Cannot retrieve saved search information");

			$querystatement="
				SELECT
					advsearchroleid
				FROM
					tabledefs
				WHERE id= '".$tabledefid."'";

			$tabledefresult = $this->db->query($querystatement);

			if(!$tabledefresult)
				$error = new appError(500,"Cannot retrieve table definition information.");

			$tableinfo=$this->db->fetchArray($tabledefresult);

			?>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td valign="top">
						<p>
							<label for="LSList">saved searches</label><br />
							<?php $this->showSavedSearchList($queryresult)?>
						</p>
					</td>
					<td valign="top" width="100%">
						<p>
							<label for="LSSelectedSearch">name</label><br />
							<input type="text" id="LSSelectedSearch" size="10" readonly="readonly" class="uneditable" />
						</p>
						<p>
							<textarea id="LSSQL" rows="8" cols="10" <?php if(!hasRights($tableinfo["advsearchroleid"])) echo " readonly=\"readonly\""?>></textarea>
						</p>
					</td>
					<td valign="top">
						<p><br/><input id="LSLoad" type="button" onclick="LSRunSearch()" class="Buttons" disabled="disabled" value="run search"/></p>
						<p><input id="LSDelete" type="button" onclick="LSDeleteSearch('<?php echo APP_PATH ?>')" class="Buttons" disabled="disabled" value="delete"/></p>
						<div id="LSResults">&nbsp;</div>
					</td>
				</tr>
			</table>
			<?php

		}//end function showLoad

	}//end class




if(isset($_GET["cmd"])){

    $thesearch = new savedSearch($db);

    switch($_GET["cmd"]){

            case "show":

                $securitywhere = "";

                if ($_SESSION["userinfo"]["admin"]!=1 && count($_SESSION["userinfo"]["roles"])>0){

                    $securitywhere = "";

                    foreach($_SESSION["userinfo"]["roles"] as $role)
                        $securitywhere .= ", '".$role."'";

                    $securitywhere = " AND (`roleid` IN (''".$securitywhere.") OR `roleid` IS NULL)";

                }//endif

                if(!isset($_GET["tid"]))
                    $error = new appError(200, "passed parameters not set");

                $thesearch->showLoad($_GET["tid"], $_SESSION["userinfo"]["uuid"], $securitywhere);
                break;

            case "getsearch":

                if(!isset($_GET["id"]))
                    $error = new appError(200, "passed parameters not set");

                $thesearch->get($_GET["id"]);
                break;

            case "savesearch":

                if(!isset($_GET["tid"]) || !isset($_GET["name"]))
                    $error = new appError(200, "passed parameters not set");

                $thesearch->save($_GET["name"] ,$_GET["tid"], $_SESSION["userinfo"]["uuid"]);
                break;

            case "deletesearch":

                if(!isset($_GET["id"]))
                    $error = new appError(200, "passed parameters not set");

                $thesearch->delete($_GET["id"]);
                break;

    }//end switch

}//endif
?>
