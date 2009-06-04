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

	class printer{
		var $tableid;
		var $theids;
		var $reports;
		var $maintable;
		var $openwindows="";
		var $savedSearches;
		var $savedSorts;

		var $db;

		function printer($db,$tableid,$theids){
			$this->db = $db;
			$this->tableid = $tableid;
			$this->theids = $theids;

			$querystatement = "
				SELECT
					`maintable`
				FROM
					`tabledefs`
				WHERE
					`uuid` = '".$this->tableid."'
				";
			$queryresult = $this->db->query($querystatement);
			if(!$queryresult) $error = new appError(500,"Error retreving table info.");
			$therecord = $this->db->fetchArray($queryresult);
			$this->maintable = $therecord["maintable"];

			$securitywhere="";
			if ($_SESSION["userinfo"]["admin"]!=1 && count($_SESSION["userinfo"]["roles"])>0){

				foreach($_SESSION["userinfo"]["roles"] as $roleUUID)
					$securitywhere .= ",'".$roleUUID."'";

				$securitywhere=" AND( `roleid` IN (''".$securitywhere.") OR `roleid` IS NULL)";
			}

			$querystatement = "
				SELECT
					`id`,
					`name`,
					`reportfile`,
					`type`,
					`description`,
					`displayorder`
				FROM
					`reports`
				WHERE
					(
						`tabledefid` = ''
						OR
						`tabledefid` = '".$this->tableid."'
					) ".$securitywhere."
				ORDER BY
					`tabledefid` DESC,
					`displayorder` DESC,
					`name`
				";

			$queryresult = $this->db->query($querystatement);
			if(!$queryresult) $error = new appError(500,"Error retreving reports.");
			$this->reports = $queryresult;

			$this->savedSearches = $this->getSaved($_SESSION["userinfo"]["id"],"SCH");
			$this->savedSorts = $this->getSaved($_SESSION["userinfo"]["id"],"SRT");
		}

		function saveVariables(){
			$_SESSION["printing"]["tableid"]=$this->tableid;
			$_SESSION["printing"]["maintable"]=$this->maintable;
			$_SESSION["printing"]["theids"]=$this->theids;
		}


		function getSaved($userid,$type){

			$securitywhere="";
			if ($_SESSION["userinfo"]["admin"]!=1 && count($_SESSION["userinfo"]["roles"])>0){

				foreach($_SESSION["userinfo"]["roles"] as $roleUUID)
					$securitywhere .= ",'".$roleUUID."'";

				$securitywhere = substr($securitywhere, 1);

				$securitywhere=" AND roleid IN (".$securitywhere.")";
			}
			$querystring = "
				SELECT
					`id`,
					`name`,
					`userid`
				FROM
					`usersearches`
				WHERE
					`tabledefid`='".$this->tableid."'
					AND
					type=\"".$type."\"
					AND
					(
						(userid = '0' ".$securitywhere.")
						OR
						userid=\"".$userid."\"
					)
				ORDER BY
					`userid`,
					`name`";

			$thequery = $this->db->query($querystring);
			return $thequery;
		}//end function


		function donePrinting($backurl){
			if(!$backurl)
				goURL("search.php?id=".$this->tableid);
			else
				goURL($backurl);
		}


		function showJavaScriptArray(){
			$thereturn = "";
			if($this->db->numRows($this->reports)){
				$this->db->seek($this->reports,0);

				while($therecord=$this->db->fetchArray($this->reports))
					$thereturn .= "theReport[theReport.length]=new Array(".$therecord["id"].",\"".$therecord["reportfile"]."\",\"".addslashes($therecord["name"])."\",\"".$therecord["type"]."\",\"".addcslashes(addslashes($therecord["description"]),"\r\n")."\");";

			} else {
				$thereturn= "theReport[theReport.length]=new Array(0,\"\",\"No Reports Available\",\"\",\"\");";
			}

			return $thereturn;
		}//end method

		function displayReportList(){
			?>
		   <select name="choosereport[]" id="choosereport" size="12" multiple="multiple" onchange="switchReport(this)">
			<?php
				if($this->db->numRows($this->reports)){
					$this->db->seek($this->reports,0);
					$displayorder=-1;
					while($therecord=$this->db->fetchArray($this->reports)){
						if ($displayorder!=$therecord["displayorder"]){
							if($displayorder>0)
								echo "<option value=\"\">----------------------------------------------------------------</option>\n";
							$displayorder=$therecord["displayorder"];
						}
						echo "<option value=\"".$therecord["id"]."\">".$therecord["name"]."</option>\n";
					}
				} else {?><option value="0">No Reports Available</option><?php }
		   ?>
		   </select>
		   <?php
		   	$phpbms->bottomJS[] = "var thechoice=getObjectFromID(\"choosereport\");thechoice.focus();thechoice.options[0].selected=true;";
		}


	function showSaved($thequery,$selectname){
		$numrows=$this->db->numRows($thequery);
		?>
		<select name="<?php echo $selectname?>" id="<?php echo $selectname?>" <?php if ($numrows<1) echo "disabled=\"disabled\"" ?>>
			<?php if($numrows<1) {?>
				<option value="NA">None Saved</option>
			<?php
				} else {
					$numglobal=0;
					while($therecord=$this->db->fetchArray($thequery))
						if($therecord["userid"]<1) $numglobal++;
					$this->db->seek($thequery,0);
			?>
				<?php if($numglobal>0){ ?>
				<option value="NA">----- global -----</option>
				<?php
					}//end if
					$userqueryline=true;
					while($therecord=$this->db->fetchArray($thequery)){
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
	}//end function

	function showFieldSort(){

		//Grab query for all columns (for sort purposes)
		$querystatement="SELECT * FROM ".$this->maintable." LIMIT 1";
		$queryresult = $this->db->query($querystatement);
		if(!$queryresult) $error = new appError(500,"Cannot retrieve Table Information");
		$numfields = $this->db->numFields($queryresult);
		for ($i=0;$i<$numfields;$i++) $fieldlist[]=$this->db->fieldName($queryresult,$i);

		?>
		<select id="singlefield" name="singlefield" onchange="checkForCustom(this.value)">
			<?php
				foreach($fieldlist as $field){
					echo "<option value=\"".$field."\"";
					if($field=="id") echo "selected=\"selected\"";
					echo ">".$field."</option>\n";
				}
			?>
			<option value="**CUSTOM**" class="important">custom SQL</option>
		</select>

		<?php
	}

}//end class

?>