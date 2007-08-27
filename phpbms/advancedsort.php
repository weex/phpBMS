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
	require("include/session.php");

	function loadSavedSort($id,$db){
		$querystatement="SELECT sqlclause FROM usersearches 
						WHERE id=".((int) $id);
		$queryresult = $db->query($querystatement);

		$therecord=$db->fetchArray($queryresult);
		echo $therecord["sqlclause"];
	}

	function deleteSavedSort($id,$db){
		$querystatement="DELETE FROM usersearches 
						WHERE id=".((int) $id);
		$queryresult = $db->query($querystatement);
		echo "success";
	}

	function displaySavedSortList($queryresult,$db){
		$numrows=$db->numRows($queryresult);
		?>
		<select id="sortSavedList" name="sortSavedList" <?php if ($numrows<1) echo "disabled" ?> size="10" style="width:99%" onchange="sortSavedSelect(this)" />
			<?php if($numrows<1) {?>
				<option value="NA">No Saved Sorts</option>
			<?php 
				} else {
					$numglobal=0;
					while($therecord=$db->fetchArray($queryresult))
						if($therecord["userid"]<1) $numglobal++;
					$db->seek($queryresult,0);				
			?>			
				<?php if($numglobal>0){ ?>
				<option value="NA" style="font-style:italic;font-weight:bold"> -- global sorts ---------</option>
				<?php
					}//end if
					$userqueryline=true;
					while($therecord=$db->fetchArray($queryresult)){
						if ($therecord["userid"]> 0 and $userqueryline) {
							$userqueryline=false;						
							?><option value="NA" style="font-style:italic;font-weight:bold"> -- user sorts---------</option><?php 
						}
						?><option value="<?php echo $therecord["id"]?>"><?php echo $therecord["name"]?></option><?php 
					}// end while
				}//end if
			?>
		</select>
		<?php
	}//end function

	function showSavedSorts($tabledefid,$basepath,$userid,$db){
		$querystatement="SELECT id,name,userid FROM usersearches 
						WHERE tabledefid=".$tabledefid." AND type=\"SRT\" AND (userid=0 OR userid=\"".$userid."\") ORDER BY userid, name";
		$queryresult = $db->query($querystatement);
		?><p><label for="sortSavedList">saved sorts</label><br />
			<?php displaySavedSortList($queryresult,$db)?>
		
		</p>
		<p align="right" class="buttonsRight">
			<input type="button" class="Buttons" style="width:75px;" id="sortSavedDeleteButton" value="delete" disabled="disabled" onclick="sortSavedDelete('<?php echo $basepath ?>')"/>
			<input type="button" class="Buttons" style="width:75px;" id="sortSavedLoadButton" value="load" disabled="disabled" onclick="sortSavedLoad('<?php echo $basepath ?>')"/>
			<input type="button" class="Buttons" style="width:75px;" id="sortSavedCancelButton" value="cancel" onclick="closeModal()"/>
		</p>
		<?php
	}

	function saveSort($name,$sqlclause,$tabledefid,$userid,$db){
		$querystatement="insert into usersearches (userid,tabledefid,name,type,sqlclause) values (";
		$querystatement.=$userid.", ";
		$querystatement.="\"".$tabledefid."\", ";
		$querystatement.="\"".$name."\", ";
		$querystatement.="\"SRT\", ";		
		$querystatement.="\"".$sqlclause."\")";

		$queryresult = $db->query($querystatement);

		echo "success";
	}

	function showSort($tabledefid,$basepath,$db){
		//First, grab table name from id	
		$querystatement="SELECT querytable FROM tabledefs WHERE id=".$tabledefid;
		$queryresult = $db->query($querystatement);
		if(!$queryresult) $error = new appError(500,"Cannot retrieve Table Information");
		$thetabledef=$db->fetchArray($queryresult);

		//Grab query for all columns
		$querystatement="SELECT * FROM ".$thetabledef["querytable"]." LIMIT 1";
		$queryresult = $db->query($querystatement);
		if(!$queryresult) $error = new appError(500,"Cannot retrieve Table Information");
		$numfields = $db->numFields($queryresult);
		for ($i=0;$i<$numfields;$i++) $fieldlist[]=$db->fieldTable($queryresult,$i).".".$db->fieldName($queryresult,$i);
		?><table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td valign=top width="99%">
					<div id="theSorts">
						<div id="Sort1">
							<select id="Sort1Field" onchange="updateSort()">
								<?php 
									foreach($fieldlist as $field){
										echo "<option value=\"".$field."\" >".$field."</option>\n";}?>
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
		
	}


	if(isset($_GET["cmd"])){
		switch($_GET["cmd"]){
			case "show":
				showSort($_GET["tid"],$_GET["base"],$db);
			break;
			case "save":
				saveSort($_GET["name"],$_GET["clause"],$_GET["tid"],$_SESSION["userinfo"]["id"],$db);
			break;
			case "showSaved":
				showSavedSorts($_GET["tid"],$_GET["base"],$_SESSION["userinfo"]["id"],$db);
			break;
			case "deleteSaved":
				deleteSavedSort($_GET["id"],$db);
			break;
			case "loadSaved":
				loadSavedSort($_GET["id"],$db);
			break;
		}//end switch
	}
?>
