<?php 
/*
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
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

	function loadSavedSort($id){
		global $dblink;
		
		$querystatement="SELECT sqlclause FROM usersearches 
						WHERE id=".$id;
		$queryresult = mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(500,"Cannot load saved sort");		
		$therecord=mysql_fetch_array($queryresult);
		echo $therecord["sqlclause"];
	}

	function deleteSavedSort($id){
		global $dblink;
		
		$querystatement="DELETE FROM usersearches 
						WHERE id=".$id;
		$queryresult = mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(500,"Cannot delete saved sort<br/>".$querystatement);		
		echo "success";
	}

	function displaySavedSortList($queryresult){
		$numrows=mysql_num_rows($queryresult);
		?>
		<select id="sortSavedList" name="sortSavedList" <?php if ($numrows<1) echo "disabled" ?> size="10" style="width:99%" onChange="sortSavedSelect(this)" />
			<?php if($numrows<1) {?>
				<option value="NA">No Saved Sorts</option>
			<?php 
				} else {
					$numglobal=0;
					while($therecord=mysql_fetch_array($queryresult))
						if($therecord["userid"]<1) $numglobal++;
					mysql_data_seek($queryresult,0);				
			?>			
				<?php if($numglobal>0){ ?>
				<option value="NA" style="font-style:italic;font-weight:bold"> -- global sorts ---------</option>
				<?PHP
					}//end if
					$userqueryline=true;
					while($therecord=mysql_fetch_array($queryresult)){
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

	function showSavedSorts($tabledefid,$basepath,$userid){
		global $dblink;

		$querystatement="SELECT id,name,userid FROM usersearches 
						WHERE tabledefid=".$tabledefid." AND type=\"SRT\" AND (userid=0 OR userid=\"".$userid."\") ORDER BY userid, name";
		$queryresult = mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(500,"Cannot retrieve saved sort infromation");
		?><p><label for="sortSavedList">saved sorts</label><br />
			<?php displaySavedSortList($queryresult,$basepath)?>
		
		</p>
		<p align="right" class="buttonsRight">
			<input type="button" class="Buttons" style="width:75px;" id="sortSavedDeleteButton" value="delete" disabled="true" onClick="sortSavedDelete('<?php echo $basepath ?>')"/>
			<input type="button" class="Buttons" style="width:75px;" id="sortSavedLoadButton" value="load" disabled="true" onClick="sortSavedLoad('<?php echo $basepath ?>')"/>
			<input type="button" class="Buttons" style="width:75px;" id="sortSavedCancelButton" value="cancel" onClick="closeModal()"/>
		</p>
		<?php
	}

	function saveSort($name,$sqlclause,$tabledefid,$userid){
		global $dblink;
		
		$querystatement="insert into usersearches (userid,tabledefid,name,type,sqlclause) values (";
		$querystatement.=$userid.", ";
		$querystatement.="\"".$tabledefid."\", ";
		$querystatement.="\"".$name."\", ";
		$querystatement.="\"SRT\", ";		
		$querystatement.="\"".$sqlclause."\")";
		$queryresult = mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(500,"Cannot Save Sort");
		echo "success";
	}

	function showSort($tabledefid,$basepath){
		global $dblink;
		
		//First, grab table name from id	
		$querystatement="SELECT querytable FROM tabledefs WHERE id=".$tabledefid;
		$queryresult = mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(500,"Cannot retrieve Table Information");
		$thetabledef=mysql_fetch_array($queryresult);

		//Grab query for all columns
		$querystatement="SELECT * FROM ".$thetabledef["querytable"]." LIMIT 1";
		$queryresult = mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(500,"Cannot retrieve Table Information");
		$numfields = mysql_num_fields($queryresult);
		for ($i=0;$i<$numfields;$i++) $fieldlist[]=mysql_field_table($queryresult,$i).".".mysql_field_name($queryresult,$i);
		?><table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td valign=top width="99%">
					<div id="theSorts">
						<div id="Sort1">
							<select id="Sort1Field" onChange="updateSort()">
								<?php 
									foreach($fieldlist as $field){
										echo "<option value=\"".$field."\" >".$field."</option>\n";}?>
							</select>
							<select id="Sort1Order" onChange="updateSort()">
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
						<textarea id="sortSQL" style="width:98%;height:75px;" cols="57" rows="4" onKeyUp="sortEnableButtons(this)" ></textarea>		
					</p>
				</td>
				<td valign=top>
					<div style="float:right">
				    <br/>
					<p><input id="sortRunSort" type="button" onClick="performAdvancedSort(this)" class="Buttons" disabled="true" value="run sort" style="width:90px;" /></p>
					<p><input id="sortLoadSort" type="button" onClick="sortAskLoad('<?php echo $_SESSION["app_path"]?>')" class="Buttons" value="load sort..." style="width:90px;" /></p>
					<p><input id="sortSaveSort" type="button" onClick="sortAskSaveName('<?php echo $_SESSION["app_path"]?>')" class="Buttons" disabled="true" value="save sort..." style="width:90px;" /></p>
					<p><input id="sortClearSort" type="button" onClick="clearSort()" class="Buttons" disabled="true" value="clear sort" style="width:90px;" /></p>
					</div>
				</td>
			</tr>
		</table>
		<?php		
		
	}


	if(isset($_GET["cmd"])){
		switch($_GET["cmd"]){
			case "show":
				showSort($_GET["tid"],$_GET["base"]);
			break;
			case "save":
				saveSort($_GET["name"],$_GET["clause"],$_GET["tid"],$_SESSION["userinfo"]["id"]);
			break;
			case "showSaved":
				showSavedSorts($_GET["tid"],$_GET["base"],$_SESSION["userinfo"]["id"]);
			break;
			case "deleteSaved":
				deleteSavedSort($_GET["id"]);
			break;
			case "loadSaved":
				loadSavedSort($_GET["id"]);
			break;
		}//end switch
	}
?>
