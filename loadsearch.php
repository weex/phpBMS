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
	
	function deleteSearch($id){
		global $dblink;
	
		$querystatement="DELETE FROM usersearches 
						WHERE id=".$id;
		$queryresult = mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(500,"Cannot delete search");
		
		echo "success";
	} 
	
	function saveSearch($name,$tabledefid,$userid){
		global $dblink;
		
		$querystatement="INSERT INTO usersearches (userid,tabledefid,name,type,sqlclause) values (";
		$querystatement.=$userid.", ";
		$querystatement.="\"".$tabledefid."\", ";
		$querystatement.="\"".$name."\", ";
		$querystatement.="\"SCH\", ";		
		$querystatement.="\"".addslashes($_SESSION["tableparams"][$tabledefid]["querywhereclause"])."\")";
		$queryresult = mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(500,"Cannot save search ".$querystatement);
		else echo "search saved";
	}

	function getSearch($id){
		global $dblink;
		
		$querystatement="SELECT sqlclause FROM usersearches 
						WHERE id=".$id;
		$queryresult = mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(500,"Cannot retrieve saved search infromation");
		$therecord=mysql_fetch_array($queryresult);
		
		echo $therecord["sqlclause"];
	
	}

	function displaySavedSearchList($queryresult,$basepath){
		$numrows=mysql_num_rows($queryresult);
		?>
		<select id="LSList" name="LSList" <?php if ($numrows<1) echo "disabled" ?> size="10" style="width:170px;height:160px;" onChange="LSsearchSelect(this,'<?php echo $basepath ?>')">
			<?php if($numrows<1) {?>
				<option value="NA">No Saved Searches</option>
			<?php 
				} else {
					$numglobal=0;
					while($therecord=mysql_fetch_array($queryresult))
						if($therecord["userid"]<1) $numglobal++;
					mysql_data_seek($queryresult,0);				
			?>			
				<?php if($numglobal>0){ ?>
				<option value="NA" style="font-style:italic;font-weight:bold"> -- global searches ---------</option>
				<?PHP
					}//end if
					$userqueryline=true;
					while($therecord=mysql_fetch_array($queryresult)){
						if ($therecord["userid"]> 0 and $userqueryline) {
							$userqueryline=false;						
							?><option value="NA" style="font-style:italic;font-weight:bold"> -- user searches ---------</option><?php 
						}
						?><option value="<?php echo $therecord["id"]?>"><?php echo $therecord["name"]?></option><?php 
					}// end while
				}//end if
			?>
		</select>
		<?php
	}//end function

	function showLoad($tabledefid,$basepath,$userid,$accesslevel){
		global $dblink;
		
		$querystatement="SELECT id,name,userid FROM usersearches 
						WHERE tabledefid=".$tabledefid." AND type=\"SCH\" AND ((userid=0 and accesslevel<=".$accesslevel.") OR userid=\"".$userid."\") ORDER BY userid, name";
		$queryresult = mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(500,"Cannot retrieve saved search infromation");
		
		
		?>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top">
				<label for="LSList" style="float:left;">
					saved searches<br />
					<?php displaySavedSearchList($queryresult,$basepath)?>
				</label>				
				</td>
				<td valign="top" width="100%">
					<label>
						name<br>
						<input type="text" id="LSSelectedSearch" size="10" style="width:98%" readonly="readonly" class="uneditable" />
					</label>
					<label for="LSSQL" style="">
						<textarea id="LSSQL" rows="8" cols="10" style="width:98%;height:127px;" <?php if($_SESSION["userinfo"]["accesslevel"]<30) echo " readonly=\"readonly\""?>></textarea>
					</label>
				</td>
				<td valign="top"><br>
					<div><input id="LSLoad" type="button" onClick="LSRunSearch()" class="Buttons" disabled="true" value="run search" style="width:90px;"/></div>
					<div><input id="LSDelete" type="button" onClick="LSDeleteSearch('<?php echo $basepath ?>')" class="Buttons" disabled="true" value="delete" style="width:90px;"/></div>
					<div id="LSResults">&nbsp;</div>
				</td>
			</tr>
		</table>
		<?php		
	}

	if(isset($_GET["cmd"])){
		switch($_GET["cmd"]){
			case "show":
				showLoad($_GET["tid"],$_GET["base"],$_SESSION["userinfo"]["id"],$_SESSION["userinfo"]["accesslevel"]);
			break;
			case "getsearch":
				getSearch($_GET["id"]);
			break;
			case "savesearch":
				saveSearch($_GET["name"],$_GET["tid"],$_SESSION["userinfo"]["id"]);
			break;
			case "deletesearch":
				deleteSearch($_GET["id"]);
			break;
		}//end switch
	}?>