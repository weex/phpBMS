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
	require_once("include/session.php");
	
	function deleteList($listname){
		global $dblink;

		$querystatement="DELETE FROM choices WHERE listname=\"".$listname."\" ";
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) 
			reportError(100,"SQL Statement Could not be executed.");
		else
			echo "ok";
	}
	
	function addToList($listname,$value){
		global $dblink;

		$querystatement="INSERT INTO choices (listname,thevalue) VALUES(\"".$listname."\",\"".$value."\") ";
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$querystatement) 
			reportError(100,"SQL Statement Could not be executed.");
		else
			echo "ok";
	}

	function displayList($queryresult,$blankvalue){
		while($therecord=mysql_fetch_array($queryresult)){
			$display=$therecord["thevalue"];
			$theclass="";
			if($therecord["thevalue"]==""){
				$display="&lt;".$blankvalue."&gt;";
				$theclass=" class=\"choiceListBlank\" ";
			}
			?><option value="<?php echo $therecord["thevalue"]?>" <?php echo $theclass?>><?php echo $display?></option><?php
		}//end while
	
	}
	
	function displayBox($listname,$blankvalue,$listid){
		global $dblink;
		
		$blankvalue=str_replace("<","",$blankvalue);
		$blankvalue=str_replace(">","",$blankvalue);
		
		$querystatement="SELECT thevalue FROM choices WHERE listname=\"".$listname."\" ORDER BY thevalue;";
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$querystatement) reportError(100,"SQL Statement Could not be executed.");
?>
	<p id="MLListP">
		<select id="MLlist" name="MLList" size="12" onchange="updateML(this)">
			<?php displayList($queryresult,$blankvalue)?>
		</select>
	</p>
	<p id="MLAddDelP">
		<input type="button" id="MLDelete" name="MLDelete" value="delete" class="Buttons" disabled onclick="delML()" /><br/>
		<input type="button" id="MLInsert" name="MLInsert" value="insert" class="Buttons" onclick="insertML()"/>
	</p>
	<p id="MLAddTextP">
		<input name="MLaddedit" id="MLaddedit" type="text"/>
		<input name="MLblankvalue" id="MLblankvalue" type="hidden" value="<?php echo $blankvalue?>"/>
	</p>
	<p id="MLAddP">
		<input type="button" id="MLaddeditbutton" name="MLaddeditbutton" value="add" class="Buttons" onclick="addeditML('<?php echo $blankvalue?>')" />
	</p>
	<p id="MLStatus" class="small">&nbsp;</p>
	<div class="box" align="right">
		<input type="button" id="MLok" name="MLok" value="ok" class="Buttons" style="width:75px;" onclick="clickOK('<?php echo $_SESSION["app_path"]?>','<?php echo $listid?>','<?php echo $listname?>')"/>
		<input type="button" id="MLcancel" name="MLcancel" value="cancel" class="Buttons" style="width:75px;" onclick="closeBox('<?php echo $listid?>');"/>&nbsp;
	</div>
<?php	}//end function

	if(!isset($_GET["cm"])) 
		$_GET["cm"]="shw";
	
	if(!isset($_GET["ln"]))
		$_GET["ln"]="shippingmethod";
	if(!isset($_GET["bv"]))
		$_GET["bv"]="none";
	switch($_GET["cm"]){
		case "shw":
			displayBox($_GET["ln"],$_GET["bv"],$_GET["lid"]);
		break;
		case "del":
			deleteList($_GET["ln"]);
		break;
		case "add":
			addToList($_GET["ln"],$_GET["val"]);
		break;
	}
	
?>