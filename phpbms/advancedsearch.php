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

	function showSearch($tabledefid,$basepath){
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
					<div>match <select id="ASanyall" onChange="updateAS()">
						<option value="and" selected="selected">all</option>
						<option value="or">any</option>
					</select> of the following rules:</div>
					<div id="theASCs" style="margin:0px;padding:0px;">
						<div id="ASC1">
							<select id="ASC1field" onChange="updateAS()">
								<?php 
									foreach($fieldlist as $field){
										echo "<option value=\"".$field."\" >".$field."</option>\n";}?>
							</select>
							<select id="ASC1operator" onChange="updateAS()">
								 <option value="=" selected="selected">=</option>
								 <option value="!=">!=</option>
								 <option value=">">&gt;</option>
								 <option value="<">&lt;</option>
								 <option value=">=">&gt;=</option>
								 <option value="<=">&lt;=</option>
								 <option value="like">like</option>
								 <option value="not like">not like</option>
							</select>
							<input type="text" id="ASC1text" size="30" maxlength="255" onKeyUp="updateAS()" value="" />
							<button type="button" id="ASC1minus" class="invisibleButtons" onClick="removeLineAS(this)"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-minus-disabled.png" align="middle" alt="-" width="16" height="16" border="0" /></button>
							<button type="button" id="ASC1plus" class="invisibleButtons" onClick="addlineAS()"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-plus.png" align="middle" alt="+" width="16" height="16" border="0" /></button>
						</div>
					</div>
					<div>
						sql where clause<br/>
						<textarea id="ASSQL" style="width:99%" cols="90" rows="3" onKeyUp="ASEnableSave(this)"></textarea>		
					</div>
				</td>
				<td valign="top">
					<div align="right" style="margin-top:10px;"><br />
						<input id="ASsearchbutton" type="button" onClick="performAdvancedSearch(this)" class="Buttons" disabled="true" value="search" style="width:90px;" accesskey="" />		
					</div>
				</td>
			</tr>
		</table><?php		
	}


	if(isset($_GET["cmd"])){
		switch($_GET["cmd"]){
			case "show":
				showSearch($_GET["tid"],$_GET["base"]);
			break;
		}//end switch
	}
?>
