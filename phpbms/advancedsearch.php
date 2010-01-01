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

	function showSearch($tabledefid,$basepath,$db){
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
		?>
		<p align="right" style="float:right">
			<input id="ASsearchbutton" type="button" onclick="performAdvancedSearch(this)" class="Buttons" disabled="disabled" value="search" />		
		</p>

		<p>match <select id="ASanyall" onchange="updateAS()">
			<option value="and" selected="selected">all</option>
			<option value="or">any</option>
		</select> of the following rules:</p>
		<div id="theASCs">
			<div id="ASC1">
				<select id="ASC1field" onchange="updateAS()">
					<?php 
						foreach($fieldlist as $field){
							echo "<option value=\"".$field."\" >".$field."</option>\n";}?>
				</select>
				<select id="ASC1operator" onchange="updateAS()">
					 <option value="=" selected="selected">=</option>
					 <option value="!=">!=</option>
					 <option value=">">&gt;</option>
					 <option value="<">&lt;</option>
					 <option value=">=">&gt;=</option>
					 <option value="<=">&lt;=</option>
					 <option value="like">like</option>
					 <option value="not like">not like</option>
				</select>
				<input type="text" id="ASC1text" size="30" maxlength="255" onkeyup="updateAS()" value="" />
				<button type="button" id="ASC1minus" class="graphicButtons buttonMinusDisabled" onclick="removeLineAS(this)"><span>-</span></button>
				<button type="button" id="ASC1plus" class="graphicButtons buttonPlus" onclick="addlineAS()"><span>+</span></button>
			</div>
		</div>
		<p>
			sql where clause<br/>
			<textarea id="ASSQL" style="width:99%" cols="90" rows="3" onkeyup="ASEnableSave(this)"></textarea>		
		</p><?php		
	}


	if(isset($_GET["cmd"])){
		switch($_GET["cmd"]){
			case "show":
				showSearch($_GET["tid"],$_GET["base"],$db);
			break;
		}//end switch
	}
?>
