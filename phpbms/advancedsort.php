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

    class advancedSort{

        var $db;
        var $tabledefid;
        var $tabledefuuid;

        function advancedSort($db, $tabledefid){

            $this->db = $db;
            $this->tabledefid = $tabledefid;
            $this->tabledefuuid = getUuid($this->db, "tbld:5c9d645f-26ab-5003-b98e-89e9049f8ac3", ((int) $tabledefid));
            
            $querystatement = "
                SELECT
                    `prefix`
                FROM
                    `tabledefs`
                WHERE
                    `uuid` = '".$this->tabledefuuid."'
            ";
            
            $queryresult = $this->db->query($querystatement);
            
            $therecord = $this->db->fetchArray($queryresult);
            $this->prefix = $therecord["prefix"];

        }//end function init


        function load($id){

            $querystatement = "
                SELECT
                    sqlclause
                FROM
                    usersearches
                WHERE
                    id = ".((int) $id);

            $queryresult = $this->db->query($querystatement);

            $therecord = $this->db->fetchArray($queryresult);

            echo $therecord["sqlclause"];

        }//end function load


        function delete($id){

            $deletestatement = "
                DELETE FROM
                    usersearches
                WERE
                    id = ".((int) $id);

            $this->db->query($deletestatement);

            echo "success";

        }//end function delete


        function showList($queryresult){

            $numrows = $this->db->numRows($queryresult);

            ?>
            <select id="sortSavedList" name="sortSavedList" <?php if ($numrows<1) echo "disabled" ?> size="10" style="width:99%" onchange="sortSavedSelect(this)" />
            <?php

                if($numrows<1){

            ?>
                <option value="NA">No Saved Sorts</option>
            <?php

                } else {

                    $numglobal=0;
                    while($therecord = $this->db->fetchArray($queryresult))
                        if($therecord["userid"] == "")
                            $numglobal++;

                    $this->db->seek($queryresult,0);

                    if($numglobal>0){

                    ?>
                        <option value="NA" style="font-style:italic;font-weight:bold"> -- global sorts ---------</option>
                    <?php

                    }//end if

                    $userqueryline = true;

                    while($therecord = $this->db->fetchArray($queryresult)){

                        if ($therecord["userid"]> 0 and $userqueryline) {

                            $userqueryline = false;
                            ?><option value="NA" style="font-style:italic;font-weight:bold"> -- user sorts---------</option><?php

                        }//end if

                        ?><option value="<?php echo $therecord["id"]?>"><?php echo $therecord["name"]?></option><?php

                    }// end while

                }//end if

            ?>
            </select>
            <?php

        }//end function showList


        function showSaved($userid){

            $querystatement = "
                SELECT
                    id,
                    name,
                    userid
                FROM
                    usersearches
                WHERE
                    tabledefid = '".$this->tabledefuuid."'
                    AND type = 'SRT'
                    AND (userid = '' OR userid IS NULL OR userid = '".$userid."')
                ORDER BY
                    userid,
                    name";

            $queryresult = $this->db->query($querystatement);

            ?>
            <p>
                <label for="sortSavedList">saved sorts</label><br />
                <?php $this->showList($queryresult)?>
            </p>
            <p align="right" class="buttonsRight">
                <input type="button" class="Buttons" style="width:75px;" id="sortSavedDeleteButton" value="delete" disabled="disabled" onclick="sortSavedDelete('<?php echo APP_PATH ?>')"/>
                <input type="button" class="Buttons" style="width:75px;" id="sortSavedLoadButton" value="load" disabled="disabled" onclick="sortSavedLoad('<?php echo APP_PATH ?>')"/>
                <input type="button" class="Buttons" style="width:75px;" id="sortSavedCancelButton" value="cancel" onclick="closeModal()"/>
            </p>
            <?php

        }//end function showSaved


        function save($name, $sqlclause, $userid){

            $insertstatement = "
                INSERT INTO
                    `usersearches`
                (
                    userid,
                    tabledefid,
                    `name`,
                    `type`,
                    `sqlclause`,
                    `uuid`
                ) VALUES (
                    '".mysql_real_escape_string($userid)."',
                    '".mysql_real_escape_string($this->tabledefuuid)."',
                    '".mysql_real_escape_string($name)."',
                    'SRT',
                    '".mysql_real_escape_string($sqlclause)."',
                    '".uuid($this->prefix.":")."'
                )";

            $this->db->query($insertstatement);

            echo "success";

        }//end function save


        function showUI(){

            //First, grab table name from id
            $querystatement = "
                SELECT
                    querytable
                FROM
                    tabledefs
                WHERE
                    uuid = '".$this->tabledefuuid."'";

            $queryresult = $this->db->query($querystatement);

            if(!$queryresult)
                $error = new appError(500,"Cannot retrieve Table Information");

            $thetabledef = $this->db->fetchArray($queryresult);

	    //Grab query for all columns
            $querystatement = "
                SELECT
                    *
                FROM
                    ".$thetabledef["querytable"]."
                LIMIT 1";

		$queryresult = $this->db->query($querystatement);

		if(!$queryresult)
                    $error = new appError(500,"Cannot retrieve Table Information");

		$numfields = $this->db->numFields($queryresult);

		for ($i=0;$i<$numfields;$i++)
                    $fieldlist[] = $this->db->fieldTable($queryresult,$i).".".$this->db->fieldName($queryresult,$i);

		?>
                <table border="0" cellspacing="0" cellpadding="0">
                    <tr>
	    		<td valign=top width="99%">
                            <div id="theSorts">
                                <div id="Sort1">
                                    <select id="Sort1Field" onchange="updateSort()">
                                        <?php
                                            foreach($fieldlist as $field){

                                                echo '<option value="'.$field.'" >'.$field."</option>\n";

                                            }//endforeach?>
                                    </select>
				    <select id="Sort1Order" onchange="updateSort()">
	    				<option value="ASC" selected="selected">Ascending</option>
                                        <option value="DESC">Descending</option>
                                    </select>
                                    <button type="button" id="Sort1Up" class="graphicButtons buttonUpDisabled" onclick="sortMove(this,'up')"><span>up</span></button>
                                    <button type="button" id="Sort1Down" class="graphicButtons buttonDownDisabled" onclick="sortMove(this,'down')"><span>down</span></button>
                                    <button type="button" id="Sort1Minus" class="graphicButtons buttonMinusDisabled" onclick="sortRemoveLine(this)"><span>-</span></button>
                                    <button type="button" id="Sort1Plus" class="graphicButtons buttonPlus" onclick="sortAddLine()"><span>+</span></button>
                                </div>
                            </div>
                            <p>
                                sql order by clause<br/>
                                <textarea id="sortSQL" style="width:98%;height:75px;" cols="57" rows="4" onkeyup="sortEnableButtons(this)" ></textarea>
                            </p>
			</td>
			<td valign=top>
                            <div style="float:right">
                        	<br/>
			    	<p><input id="sortRunSort" type="button" onclick="performAdvancedSort(this)" class="Buttons" disabled="disabled" value="run sort" style="width:90px;" /></p>
                                <p><input id="sortLoadSort" type="button" onclick="sortAskLoad('<?php echo APP_PATH?>')" class="Buttons" value="load sort..." style="width:90px;" /></p>
                                <p><input id="sortSaveSort" type="button" onclick="sortAskSaveName('<?php echo APP_PATH?>')" class="Buttons" disabled="disabled" value="save sort..." style="width:90px;" /></p>
                                <p><input id="sortClearSort" type="button" onclick="clearSort()" class="Buttons" disabled="disabled" value="clear sort" style="width:90px;" /></p>
                            </div>
                        </td>
		    </tr>
		</table>
		<?php

        }//end function showUI

    }//end class advancedSort




/*
 * PROCESSOR
 * =============================================================================
 */
    if(isset($_GET["cmd"])){

        if(!isset($_GET["tid"]))
            $_GET["tid"] = "tbld:5c9d645f-26ab-5003-b98e-89e9049f8ac3";

        $sorts = new advancedSort($db, $_GET["tid"]);

        switch($_GET["cmd"]){

            case "show":
                $sorts->showUI();
                break;

            case "save":
                $sorts->save($_GET["name"], $_GET["clause"], $_SESSION["userinfo"]["uuid"]);
                break;

            case "showSaved":
                $sorts->showSaved($_SESSION["userinfo"]["uuid"]);
                break;

            case "deleteSaved":
                $sorts->delete($_GET["id"]);
                break;

            case "loadSaved":
                $sorts->load($_GET["id"]);
                break;

        }//end switch

    }//endif
?>
