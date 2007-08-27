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


		function save($name,$tabledefid,$userid){
			$querystatement="INSERT INTO usersearches (userid,tabledefid,name,type,sqlclause) values (";
			$querystatement.=((int) $userid).", ";
			$querystatement.="\"".$tabledefid."\", ";
			$querystatement.="\"".$name."\", ";
			$querystatement.="\"SCH\", ";		
			$querystatement.="\"".addslashes($_SESSION["tableparams"][$tabledefid]["querywhereclause"])."\")";

			$queryresult = $this->db->query($querystatement);
			
			echo "search saved";
		}
		
		function get($id){
			$querystatement="SELECT sqlclause FROM usersearches 
							WHERE id=".((int) $id);
			$queryresult = $this->db->query($querystatement);

			$therecord=$this->db->fetchArray($queryresult);
			
			echo $therecord["sqlclause"];
		
		}
		
		
		function showSavedSearchList($queryresult,$basepath){
			
			$numrows=$this->db->numRows($queryresult);
			
			?>
			<select id="LSList" name="LSList" <?php if ($numrows<1) echo "disabled" ?> size="10" style="width:170px;height:160px;" onchange="LSsearchSelect(this,'<?php echo $basepath ?>')">
				<?php if($numrows<1) {?>
					<option value="NA">No Saved Searches</option>
				<?php 
					} else {
						$numglobal=0;
						while($therecord=$this->db->fetchArray($queryresult))
							if($therecord["userid"]<1) $numglobal++;
						$this->db->seek($queryresult,0);				
				?>			
					<?php if($numglobal>0){ ?>
					<option value="NA" style="font-style:italic;font-weight:bold"> -- global searches ---------</option>
					<?php
						}//end if
						$userqueryline=true;
						while($therecord=$this->db->fetchArray($queryresult)){
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

		
		function showLoad($tabledefid,$basepath,$userid,$securitywhere){
	
			$querystatement="SELECT id,name,userid FROM usersearches 
							WHERE tabledefid=".$tabledefid." AND type=\"SCH\" AND ((userid=0 ".$securitywhere.") OR userid=\"".$userid."\") ORDER BY userid, name";
			$queryresult = $this->db->query($querystatement);
			if(!$queryresult) $error = new appError(500,"Cannot retrieve saved search infromation");
			
	
			$querystatement="SELECT advsearchroleid FROM tabledefs WHERE id=".$tabledefid ;
			$tabledefresult = $this->db->query($querystatement);
			if(!$tabledefresult) $error = new appError(500,"Cannot retrieve table definition information.");
			$tableinfo=$this->db->fetchArray($tabledefresult);
			
			?>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td valign="top">
					<p>
					<label for="LSList">saved searches</label><br />
						<?php $this->showSavedSearchList($queryresult,$basepath)?>				
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
						<p><input id="LSDelete" type="button" onclick="LSDeleteSearch('<?php echo $basepath ?>')" class="Buttons" disabled="disabled" value="delete"/></p>
						<div id="LSResults">&nbsp;</div>
					</td>
				</tr>
			</table>
			<?php		
		}
	}//end class
	
	
	





	if(isset($_GET["cmd"])){
		
		$thesearch = new savedSearch($db);
		
		switch($_GET["cmd"]){
			case "show":
				$securitywhere="";
				if ($_SESSION["userinfo"]["admin"]!=1 && count($_SESSION["userinfo"]["roles"])>0)
					$securitywhere=" AND roleid IN (".implode(",",$_SESSION["userinfo"]["roles"]).",0)";			
				$thesearch->showLoad($_GET["tid"],$_GET["base"],$_SESSION["userinfo"]["id"],$securitywhere);
			break;
			case "getsearch":
				$thesearch->get($_GET["id"]);
			break;
			case "savesearch":
				$thesearch->save($_GET["name"],$_GET["tid"],$_SESSION["userinfo"]["id"]);
			break;
			case "deletesearch":
				$thesearch->delete($_GET["id"]);
			break;
		}//end switch
	}?>