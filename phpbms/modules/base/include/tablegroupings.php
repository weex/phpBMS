<?php
/*
 $Rev: 267 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-14 13:08:27 -0600 (Tue, 14 Aug 2007) $
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

class groupings{

	var $db;
	var $tabledefid;
	var $tabledefuuid;

	/**
	 * initialize group object
	 *
	 * @param object $db database object
	 * @param int $tabledefid tabledef id
	 */
	function groupings($db, $tabledefid){

		$this->db = $db;
		$this->tabledefid = (int) $tabledefid;

		$querystatement = "
			SELECT
				uuid
			FROM
				tabledefs
			WHERE
				id = ".$this->tabledefid;

		$queryresult = $this->db->query($querystatement);

		$therecord = $this->db->fetchArray($queryresult);

		$this->tabledefuuid = $therecord["uuid"];

	}//end fucntion tabledefid


	function processForm($command,$post, $get){

		$therecord = $this->getDefaults();
		$therecord["action"] = "add record";

		switch($command){
			case "edit":
				$therecord = $this->getRecords($get["selid"]);
				$therecord["action"] = "edit record";
				break;

			case "delete":
				$therecord["statusMessage"] = $this->delete($get["selid"]);
				break;

			case "add record":
				$therecord["statusMessage"] = $this->add(addSlashesToArray($post));
				break;

			case "edit record":
				$therecord["statusMessage"] = $this->update(addSlashesToArray($post));
				break;

			case "moveup":
				$therecord["statusMessage"] = $this->move($get["selid"],"up");
				break;

			case "movedown":
				$therecord["statusMessage"] = $this->move($get["selid"],"down");
				break;

		}//endswitch

		return $therecord;

	}//end method processFrom

	/**
	 * get grouping record (or records)
	 *
	 * @param integer $id
	 */
	function getRecords($id = NULL){

		$querystatement = "
			SELECT
				*
			FROM
				tablegroupings
			WHERE
				tabledefid = '".$this->tabledefuuid."'";

		if($id != NULL)
			$querystatement .= "
				AND id =".((int) $id);

		$querystatement .= "
			ORDER BY
				displayorder";

		$queryresult = $this->db->query($querystatement);

		if($id != NULL)
			return  $this->db->fetchArray($queryresult);
		else
			return $queryresult;

	}//end function getRecords


	function getDefaults(){

		$therecord["id"] = NULL;
		$therecord["displayorder"] = 0;
		$therecord["name"] = "";
		$therecord["field"] = "";
		$therecord["roleid"] = "";
		$therecord["ascending"] = 1;

		return $therecord;

	}//end method


	function delete($id){

		$querystatement="SELECT displayorder FROM tablegroupings WHERE id=".((int) $id);
		$theresult=$this->db->query($querystatement);
		$therecord=$this->db->fetchArray($theresult);

		$querystatement="UPDATE tablegroupings SET displayorder=displayorder-1
							WHERE tabledefid=".$this->tabledefid." AND displayorder>".$therecord["displayorder"];
		$theresult=$this->db->query($querystatement);

		$querystatement="DELETE FROM tablegroupings WHERE id=".((int) $id);
		$theresult=$this->db->query($querystatement);

		return "Record deleted";

	}//end method


	function add($variables){

		$maxOrder = $this->getMaxOrder();


		$querystatement = "INSERT INTO tablegroupings (tabledefid, name, `field`, displayorder, `ascending`, roleid) VALUES (";
		$querystatement .= "'".$this->tabledefuuid."', ";
		$querystatement .= "'".$variables["name"]."', ";
		$querystatement .= "'".$variables["field"]."', ";
		$querystatement .= (((int)$maxOrder) +1 ).", ";

		if(isset($variables["ascending"])) $querystatement .= "1, "; else $querystatement .= "0, ";

		$querystatement .= "'".$variables["roleid"]."') ";

		$this->db->query($querystatement);

		return "record added";

	}//end method add


	function update($variables){

		$querystatement = "UPDATE tablegroupings SET ";
		$querystatement .= "`name` = '".$variables["name"]."', ";
		$querystatement .= "`field` = '".$variables["field"]."', ";

		$querystatement .= "`ascending` =";
		if(isset($variables["ascending"])) $querystatement .= "1, "; else $querystatement .= "0, ";

		$querystatement .= "roleid = '".$variables["roleid"]."' ";
		$querystatement .= "WHERE id = ".((int) $variables["id"]);

		$this->db->query($querystatement);

		return "record updated";

	}//end function update


	function getMaxOrder(){

		$querystatement="select max(displayorder) as themax FROM tablegroupings WHERE tabledefid=".$this->tabledefid;
		$thequery=$this->db->query($querystatement);
		$maxrecord=$this->db->fetchArray($thequery);

		return($maxrecord["themax"]);

	}//end function getMaxOrder


	function move($id,$direction = "down"){

		if($direction=="down") $increment="1"; else $increment="-1";

		$querystatement="select displayorder FROM tablegroupings WHERE id=".((int) $id);
		$thequery=$this->db->query($querystatement);
		$therecord=$this->db->fetchArray($thequery);

		$maxOrder = $this->getMaxOrder();

		if(!(($direction=="down" and $therecord["displayorder"] == $maxOrder) or ($direction=="up" and $therecord["displayorder"]=="0"))){
			$querystatement="UPDATE tablegroupings set displayorder=".$therecord["displayorder"]." WHERE displayorder=".($increment+$therecord["displayorder"])." AND tabledefid=".$this->tabledefid;
			$thequery=$this->db->query($querystatement);

			$querystatement="UPDATE tablegroupings set displayorder=displayorder+".$increment." WHERE id=".((int) $id);
			$thequery=$this->db->query($querystatement);
		}// end if

		"position moved";

	}


	function showRecords($queryresult){
		global $phpbms;
?>
	<div class="fauxP">
   <table border="0" cellpadding="0" cellspacing="0" class="querytable">
	<tr>
	 <th nowrap="nowrap" class="queryheader">move</th>
	 <th align="left" nowrap="nowrap" class="queryheader" width="100%">name/field</th>
	 <th align="left" nowrap="nowrap" class="queryheader">ascending</th>
	 <th align="left" nowrap="nowrap" class="queryheader">access</th>
	 <th nowrap="nowrap" class="queryheader">&nbsp;</th>
	</tr>
	<?php
		$topdisplayorder=-1;
		$row=1;

		while($therecord = $this->db->fetchArray($queryresult)){
			$topdisplayorder=$therecord["displayorder"];
			if($row==1) $row=2; else $row=1;
	?>
	<tr class="qr<?php echo $row?> noselects">
		<td nowrap="nowrap"valign="top">
		 	<button type="button" class="graphicButtons buttonUp" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=moveup&amp;selid=".$therecord["id"]?>';"><span>Move Up</span></button>
		 	<button type="button" class="graphicButtons buttonDown" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=movedown&amp;selid=".$therecord["id"]?>';"><span>Move Down</span></button>
			<?php echo $therecord["displayorder"];?>
		</td>

		<td valign="top"><?php
			if($therecord["name"])
				echo "<strong>".$therecord["name"]."</strong><br />";

			echo htmlQuotes($therecord["field"])?>
		</td>

		<td align="center" nowrap="nowrap"valign="top"><?php echo booleanFormat($therecord["ascending"])?></td>

		<td valign="top"><?php $phpbms->displayRights($therecord["roleid"])?></td>

		<td nowrap="nowrap"valign="top">
			 <button id="edit<?php echo $therecord["id"]?>" name="doedit" type="button" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=edit&amp;selid=".$therecord["id"]?>';" class="graphicButtons buttonEdit"><span>edit</span></button>
			 <button id="delete<?php echo $therecord["id"]?>" name="dodelete" type="button" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=delete&amp;selid=".$therecord["id"]?>';" class="graphicButtons buttonDelete"><span>delete</span></button>
		</td>
	</tr>
	<?php } ?>
	<tr class="queryfooter">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	</table></div>

<?php
	}//end method showRecords
}//end class
