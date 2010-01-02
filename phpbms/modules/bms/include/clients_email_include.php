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

	function showSavedSearches($db,$selected){

		$querystatment="SELECT id,name,userid FROM usersearches WHERE tabledefid=2 and type=\"SCH\" and(userid=0 or userid=\"".$_SESSION["userinfo"]["id"]."\") order by userid";
		$thequery = $db->query($querystatment);

		$numrows=$db->numRows($thequery);
		?>
		<select id="savedsearches" name="savedsearches" <?php if ($numrows<1) echo 'disabled="disabled"' ?> >
			<?php if($numrows<1) {?>
				<option value="NA">None Saved</option>
			<?php
				} else {
					$numglobal=0;
					while($therecord=$db->fetchArray($thequery))
						if($therecord["userid"]<1) $numglobal++;
					$db->seek($thequery,0);
			?>
				<?php if($numglobal>0){ ?>
				<option value="NA">----- global -----</option>
				<?php
					}//end if
					$userqueryline=true;
					while($therecord=$db->fetchArray($thequery)){
						if ($therecord["userid"]> 0 and $userqueryline) {
							$userqueryline=false;
							?><option value="NA">----- user ------</option><?php
						}
						?><option value="<?php echo $therecord["id"]?>" <?php if($therecord["id"]==$selected) echo "selected=\"selected\""?>><?php echo $therecord["name"]?></option><?php
					}// end while
				}//end if
			?>
		</select>
		<?php
	}


	function showClientFields($db){

		$querystatement="describe clients";
		$queryresult=$db->query($querystatement);
		?><select name="choosefield" id="choosefield">
		<?php
			while($therecord=$db->fetchArray($queryresult))
				echo "<option value=\"".$therecord["Field"]."\">".$therecord["Field"]."</option>";
		?>
		</select><?php
	}


	function showSavedProjects($db){

		$querystatement="SELECT id,name,userid FROM clientemailprojects WHERE userid=0 or userid=\"".$_SESSION["userinfo"]["id"]."\" order by userid";
		$thequery = $db->query($querystatement);
		if(!$thequery) $error = new appError(300,$querystatement);

		$numrows=$db->numRows($thequery);
		?>
		<select name="savedprojects" id="savedprojects" <?php if ($numrows<1) echo "disabled" ?> style="width:99%;" size="9" onclick="updateSavedProjects(this)">
			<?php if($numrows<1) {?>
				<option value="NA">None Saved</option>
			<?php
				} else {
					$numglobal=0;
					while($therecord=$db->fetchArray($thequery))
						if($therecord["userid"]<1) $numglobal++;
					$db->seek($thequery,0);
			?>
				<?php if($numglobal>0){ ?>
				<option value="NA">----- global -----</option>
				<?php
					}//end if
					$userqueryline=true;
					while($therecord=$db->fetchArray($thequery)){
						if ($therecord["userid"]> 0 and $userqueryline) {
							$userqueryline=false;
							?><option value="NA">----- user ------</option><?php
						}
						?><option value="<?php echo $therecord["id"]?>"><?php echo $therecord["name"]?></option><?php
					}// end while
				}//end if
			?>
		</select>
		<?php
	}


	function saveProject($db,$variables){

		$sqlstatement=	"INSERT INTO clientemailprojects (name,userid,emailto,emailfrom,subject,body) VALUES (";
		$sqlstatement.=	"\"".$variables["savename"]."\", ";
		$sqlstatement.=	$_SESSION["userinfo"]["id"].", ";
		if($variables["therecords"]=="savedsearch")
			$sqlstatement.=	"\"".$variables["savedsearches"]."\", ";
		else
			$sqlstatement.=	"\"".$variables["therecords"]."\", ";
		if(!$variables["email"])
			$sqlstatement.=	"\"".$variables["ds-email"]."\", ";
		else
			$sqlstatement.=	"\"".$variables["email"]."\", ";
		$sqlstatement.=	"\"".$variables["subject"]."\", ";
		$sqlstatement.=	"\"".$variables["body"]."\") ";

		$db->query($sqlstatement);
		return $db->insertId();
	}

	function loadProject($db,$id){

		$sqlstatement="SELECT id,name,emailto,emailfrom,subject,body FROM clientemailprojects WHERE id=".((int) $id);
		$queryresult=$db->query($sqlstatement);
		return $db->fetchArray($queryresult);

	}

	function deleteProject($db,$id){

		$sqlstatement="DELETE FROM clientemailprojects WHERE id=".((int) $id)." and userid=".$_SESSION["userinfo"]["id"];
		$queryresult=$db->query($sqlstatement);

	}
?>