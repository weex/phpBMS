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
	class displayTable{
		var $isselect;
		var $thetabledef;
		var $ref;
		var $thecolumns;
		var $querystatement;
		var $numrows=0;
		var $recordoffset=0;
		var $queryresult;
		var $querysortorder="";
		var $base="";
		var $sqlerror="";
		var $showGroupings = true;

		var $db;

		function displayTable($db){
			$this->db=$db;
		}


		//given a table id, go grab the table definition information for that table
		function getTableDef($id){

			$querystatement="
				SELECT
					tabledefs.id,
					tabledefs.uuid,
					maintable,
					querytable,
					tabledefs.displayname,
					addfile,
					editfile,
					importfile,
					deletebutton,
					type,
					defaultwhereclause,
					defaultsortorder,
					defaultsearchtype,
					defaultcriteriafindoptions,
					defaultcriteriaselection,
					modules.name,
					searchroleid,
					advsearchroleid,
					viewsqlroleid
				FROM
					tabledefs inner join modules on tabledefs.moduleid = modules.uuid
				WHERE
					tabledefs.id=".$id."";

			$queryresult=$this->db->query($querystatement);

			if ($this->db->numRows($queryresult)<1) $error = new appError(1,"table definition not found: ".$id);

			$therecord=$this->db->fetchArray($queryresult);

			if(!hasRights($therecord["searchroleid"]))
				goURL(APP_PATH."noaccess.php");

			return $therecord;
		}//end function getTableDef


		/**
		 * retrieves groupings for a tabledefs
		 *
		 * @param string $id tabledef uuid
		 */
		function getTableGroupings($id){

			$groupings = array();

			$querystatement = "
				SELECT
					`field`,
					`name` AS displayname,
					`ascending`,
					`roleid`
				FROM
					tablegroupings
				WHERE
					tabledefid = '".$id."'
				ORDER BY
					displayorder";

			$queryresult = $this->db->query($querystatement);

			while($therecord = $this->db->fetchArray($queryresult)) {

				if(hasRights($therecord["roleid"],false))
					$groupings[] = $therecord;

			}//endwhile

			return $groupings;

		}//end function getTableGroupings


		/**
		 * Retrieves table definition's columns (to be displayed)
		 *
		 * @param string $id tabledefs uuid
		 */
		function getTableColumns($id){

			$thecolumns = array();

			$querystatement = "
				SELECT
					name,
					`column`,
					align,
					sortorder,
					footerquery,
					wrap,
					size,
					format,
					roleid
				FROM
					tablecolumns
				WHERE
					tabledefid = '".$id."'
				ORDER BY
					displayorder";

			$queryresult = $this->db->query($querystatement) ;

			while($therecord = $this->db->fetchArray($queryresult))
				if(hasRights($therecord["roleid"], false))
					$thecolumns[] = $therecord;

			return $thecolumns;

		}//end function getTableColumns


		function showResultHeader(){
			?>
			<tr>
			<?php
			$columncount=count($this->thecolumns);
			$i=1;

			foreach ($this->thecolumns as $therow){ ?>
		<th nowrap="nowrap" align="<?php echo $therow["align"]?>" <?php if($therow["size"]) echo "width=\"".$therow["size"]."\" ";?> >
			<input name="sortit<?php echo $i?>" type="hidden" value="<?php echo $therow["name"]?>" />
			<a href="/" onclick="doSort(<?php echo $i?>);return false;"><?php echo $therow["name"]?></a>
			<?php
				// If sorting on this column give the option to reverse the sort order.
				if ($this->querysortorder==$therow["column"] || $this->querysortorder==$therow["sortorder"])
			{?>&nbsp;<a href="/" onclick="doDescSort();return false;"><img src="<?php echo APP_PATH?>common/image/down_arrow.gif" alt="dn" title="dn" width="10" height="10" border="0" /></a><input name="desc" type="hidden" value="" />
		<?php }	elseif ($this->querysortorder==$therow["column"]." DESC" || $this->querysortorder==$therow["sortorder"]." DESC")
		{?> &nbsp;<a href="/" onclick="doSort(<?php echo $i?>);return false;"><img src="<?php echo APP_PATH?>common/image/up_arrow.gif" alt="up" title="up" width="10" height="10" border="0" /></a>
		<?php }	?></th><?php
				$i++;
			}//end foreach
			?></tr><?php

		}//end function

		//output a query
		function showResultRecords() {
			if(!isset($this->options["new"])) $this->options["new"]=1;
			if(!isset($this->options["select"])) $this->options["select"]=1;
			if(!isset($this->options["edit"])) $this->options["edit"]=1;

			$rownum=1;
			$this->db->seek($this->queryresult,0);

			//groupings
			if($this->showGroupings){
				for($i = 0; $i < count($this->thegroupings); $i++){
					$this->thegroupings[$i]["theValue"]="";
				}

			}

			while($therecord = $this->db->fetchArray($this->queryresult)){

				// more groupings
				if($this->showGroupings){
					for($i = 0; $i < count($this->thegroupings); $i++){
						if($this->thegroupings[$i]["theValue"] != $therecord["_group".($i+1)]){
							$this->thegroupings[$i]["theValue"] = $therecord["_group".($i+1)];
							?><tr class="queryGroup"><td colspan = "<?php echo count($this->thecolumns)?>" <?php if($i) echo 'style = "padding-left:'.($i*15).'px"'?>>
							<?php
								if($this->thegroupings[$i]["displayname"])
									echo htmlQuotes($this->thegroupings[$i]["displayname"].": ");
								echo $therecord["_group".($i+1)];
							 ?>
							</td></tr><?php

							$rownum = 1;

						}//endif
					}//endfor
				}//endif

				?><tr class="qr<?php echo $rownum?>" id="r-<?php echo $therecord["theid"]?>" <?php

				if ($this->options["select"]) {
					?> onclick="clickIt(this,event,'<?php echo $this->isselect?>')" <?php
				}
				if ($this->options["edit"]) {
					?> ondblclick="editThis(this);"<?php
				}
				?> ><?php

				if ($rownum==1) $rownum++; else $rownum=1;

				foreach($this->thecolumns as $thecolumn){
					?><td align="<?php echo $thecolumn["align"]?>" <?php if(!$thecolumn["wrap"]) echo "nowrap=\"nowrap\""?>><?php echo (($therecord[$thecolumn["name"]]!=="")?formatVariable($therecord[$thecolumn["name"]],$thecolumn["format"]):"&nbsp;")?></td><?php
				}
				?></tr><?php
			}//endwhile


		}//end function


		// display a no results page
		function showNoResults(){
			$i=count($this->thecolumns);?>
			<tr class="norecords"><td colspan="<?php echo $i?>">
				<?php if(!$this->sqlerror) {?>
					No Results Found
				<?php } else {?>
					Invalid Search
				<?php } ?>
			</td></tr>
			<?php
		}

		function initialize($id){

			$this->thetabledef = $this->getTableDef($id);

			$this->ref = $this->thetabledef["id"];
			$this->uuid = $this->thetabledef["uuid"];

			//next we set the columns
			$this->thecolumns=$this->getTableColumns($this->uuid);
			$this->thegroupings = $this->getTableGroupings($this->uuid);

		}//end function initialize


		function issueQuery(){

			//save the query for total and display purposes
			$_SESSION["thequerystatement"] = $this->querystatement;
			//Add limit (settings)
			$_SESSION["thequerystatement"].=" limit ".$this->recordoffset.", ".RECORD_LIMIT.";";


			$this->db->logError=false;
			$this->db->stopOnError=false;

			$this->queryresult = $this->db->query($_SESSION["thequerystatement"]);

			$this->db->logError=true;
			$this->db->stopOnError=true;

			if($this->queryresult){
				 $this->numrows=$this->db->numRows($this->queryresult);
				 if($this->numrows==RECORD_LIMIT or $this->recordoffset!=0){
				    //if you max the record limit or are already offsetiing get the true count
					$truecountstatement="SELECT count(distinct ".$this->thetabledef["maintable"].".id) as thecount".strstr(substr($this->querystatement,0,strpos($this->querystatement," ORDER BY"))," FROM ");
					$truequeryresult=$this->db->query($truecountstatement);

					$truerecord=$this->db->fetchArray($truequeryresult);
					$this->truecount=$truerecord["thecount"];
				 }
				 else $this->truecount=$this->numrows;
				$this->sqlerror="";
			}else{
				$this->sqlerror=$this->db->error;
				$this->numrows=0;
				$this->truecount=0;
			}
			$_SESSION["sqlerror"]=$this->sqlerror;
		}

		function getIDs($variables){
			$theids=array();
			foreach($variables as $key=>$value){
				if (substr($key,0,5)=="check") $theids[]=$value;
			}
			return $theids;
		}

		//===============
		//Query Functions
		//===============
		// replace variables
		// strings with entrys like " {{$ENTRY}} "
		// get everything in the {{ }} evaluated
		function subout($string){

			while(strpos($string,"{{")){
				$start=strpos($string,"{{");
				$startsubout=$start+2;
				$endsubout=strpos($string,"}}");
				$end=$endsubout+2;
				$temp="";
				eval(stripslashes("\$temp=".substr($string,$startsubout,$endsubout-$startsubout).";"));
				$string=substr($string,0,$start).$temp.substr($string,$end);
			}

			return $string;

		}//end function

	}//end class





	//=====================================================================================================================
	class displaySelectTable extends displayTable{
		var $isselect=true;
		var $querytype="select";
		var $valuefield;
		var $displayfield;
		var $whereclause;
		var $searchvalue;
		var $fieldname;

		function displaySelectTable($db){
			$this->db = $db;
		}

		function initialize($variables){
			parent::initialize($variables["tableid"]);

			$this->valuefield=stripslashes($variables["valuefield"]);
			$this->displayfield=stripslashes($variables["displayfield"]);
			$this->whereclause=stripslashes($variables["whereclause"]);
			$this->searchvalue=stripslashes($variables["value"]);
			$this->fieldname=stripslashes($variables["name"]);

			if(isset($_SESSION["tableparams"][$this->ref]))
				$this->querysortorder=$_SESSION["tableparams"][$this->ref]["querysortorder"];
			else
				$this->querysortorder=$this->thetabledef["defaultsortorder"];
		}


		function issueInitialQuery(){

			$querystatement="SELECT ".$this->valuefield." AS value, ".$this->displayfield." AS display FROM ".$this->thetabledef["maintable"]." WHERE ";
			$querystatement.="(".$this->displayfield." LIKE \"".$this->searchvalue."%\") ";

			if($this->whereclause)
				$querystatement.="AND (".$this->whereclause.")";

			$queryresult=$this->db->query($querystatement);

			return $queryresult;
		}


		function issueQuery(){
			$querycolumns = "";
			$tempSortOrder = "";

			//GROUPING SETUP
			if($this->showGroupings){
				$i =1 ;
				foreach ($this->thegroupings as $thegroup){
					$querycolumns .= ", ".$thegroup["field"]." as \"_group".$i."\" ";
					$tempSortOrder .= ", ".$thegroup["field"];
					if($thegroup["ascending"] == 0)
						$tempSortOrder.=" DESC";
					$i++;
				}
				if($i > 1){
					$tempSortOrder = substr($tempSortOrder,2).", ";
				}
			}

			foreach ($this->thecolumns as $therow)
				$querycolumns.=", ".$therow["column"]." as \"".$therow["name"]."\"";
			$querycolumns=substr($querycolumns,2);

			$this->querystatement = "SELECT DISTINCT ".$this->valuefield." AS value, ".$this->displayfield." AS display, ".$querycolumns." FROM ".$this->thetabledef["querytable"]." WHERE";
			$this->querystatement.="(".$this->displayfield." LIKE \"".$this->searchvalue."%\") ";
			if($this->whereclause)
				$this->querystatement.="AND (".$this->whereclause.")";

			$tempSortOrder.= $this->querysortorder;
			$this->querystatement.=" ORDER BY ".$tempSortOrder;

			$_SESSION["tableparams"][$this->ref]["querysortorder"]=$this->querysortorder;

			parent::issueQuery();

		}//end function
	}//end class



	//=====================================================================================================================
	class displaySearchTable extends displayTable{
		var $isselect=false;
		var $therecords="";

		var $querytype="search";

		var $queryjoinclause="";
		var $querywhereclause="";

		var $savedfindoptions="";
		var $savedselection="";
		var $savedstartswithfield="";
		var $savedstartswith="";
		var $savedendswith="";

		var $tableoptions;

		function getTableOptions($id){
			$options=Array();
			$querystatement="
				SELECT
					id,
					name,
					`option`,
					needselect,
					othercommand,
					roleid,
					displayorder
				FROM
					tableoptions
				WHERE
					tabledefid = ".$id."
				ORDER BY
					othercommand,
					displayorder,
					id";
			$queryresult=$this->db->query($querystatement);

			while($therecord=$this->db->fetchArray($queryresult)) {

				if($therecord["othercommand"]) {

					$options["othercommands"][] = array(
						"id" => $therecord["id"],
						"name" => $therecord["option"],
						"roleid" => $therecord["roleid"],
						"displayorder" => $therecord["displayorder"],
						"needselect" => $therecord["needselect"]
					);

				}else{

					$options[$therecord["name"]]["allowed"]=$therecord["option"];
					$options[$therecord["name"]]["roleid"]=$therecord["roleid"];
					$options[$therecord["name"]]["needselect"]=$therecord["needselect"];

				}//endif

			}//endwhile

			return $options;

		}//end getTableOptions


		/**
		 * Builds table definitions findoptions (find select box)
		 *
		 * @param string $id tabledefs UUID
		 */
		function getTableQuickSearchOptions($id){

			$findoptions = Array();
			$querystatement = "
				SELECT
					name,
					search,
					roleid
				FROM
					tablefindoptions
				WHERE
					tabledefid = '".$id."'
				ORDER BY
					displayorder";

			$queryresult = $this->db->query($querystatement);

			while($therecord = $this->db->fetchArray($queryresult)){

				$therecord["search"] = $this->subout($therecord["search"]);
				$findoptions[] = $therecord;

			}//endif

			return $findoptions;

		}//end function getTableQuickSearchOptions


		function getTableSearchableFields($id){

			$searchablefields=Array();
			$querystatement="SELECT id,field,name,type
								  FROM tablesearchablefields WHERE tabledefid=".$id." ORDER BY displayorder";
			$queryresult=$this->db->query($querystatement);

			while($therecord=$this->db->fetchArray($queryresult)) $searchablefields[]=$therecord;

			return $searchablefields;
		}

		function displaySearch(){

		?>
<form name="search" id="search" method="post" action="<?php echo $_SERVER["PHP_SELF"]?>?id=<?php echo $this->thetabledef["id"]?>" onsubmit="setSelIDs(this);return true;">
<input id="tabledefid" name="tabledefid" type="hidden" value="<?php echo $this->thetabledef["id"]?>" />
<input id="theids" name="theids" type="hidden" value="" />
<input id="advancedsearch" name="advancedsearch" type="hidden" value="" />
<input id="advancedsort" name="advancedsort" type="hidden" value="" />
<?php if ($this->querytype!="" and $this->querytype!="search") {
		$temptype=$this->querytype;
		if($temptype=="advanced search")
			$temptype="advanced or saved search";
		echo "<p><i>(currently showing ".$temptype.")</i></p>";
	}
?>
<ul class="tabs">
	<li id="basicSearchT" class="tabsSel"><a href="/" onclick="switchSearchTabs(this);return false">basic</a></li>
	<?php if(hasRights($this->thetabledef["advsearchroleid"])){?><li id="advancedSearchT"><a href="/" onclick="switchSearchTabs(this,'<?php echo APP_PATH?>');return false">advanced</a></li><?php } //end access ?>
	<li id="loadSearchT"><a href="/" onclick="switchSearchTabs(this,'<?php echo APP_PATH?>');return false">load search</a></li>
	<li id="saveSearchT"><a href="/" onclick="switchSearchTabs(this,'<?php echo APP_PATH?>');return false">save search</a></li>
	<li id="advancedSortT"><a href="/" onclick="switchSearchTabs(this,'<?php echo APP_PATH?>');return false">sorting</a></li>
</ul>
<div class="box" id="searchBox">
	<div id="basicSearchTab">
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td nowrap="nowrap" valign="top">
					<p>
						<label for="find">find</label><br />
						<select name="find" id="find">
						<?php
							for($i=0;$i<count($this->findoptions);$i++) {
								if(hasRights($this->findoptions[$i]["roleid"])){
									?><option value="<?php echo $this->findoptions[$i]["name"]?>"<?php
										if($this->querytype=="search" and $this->findoptions[$i]["name"]==$this->savedfindoptions) echo "selected=\"selected\"";
									?>><?php echo $this->findoptions[$i]["name"]?></option><?php
								}
							}
						?>
						</select>
					</p>
				</td>
			<td nowrap="nowrap" valign="top">
				<p>
				<label for="startswithfield">where</label><br />
					<select name="startswithfield" id="startswithfield">
						<?php
							for($i=0;$i<count($this->searchablefields);$i++) {
								echo "<option value=\"".$this->searchablefields[$i]["id"]."\" ";
									if(!isset($this->savedstartswithfield)){
										if($this->querytype!="search" and $i==0) echo "selected=\"selected\"";
									} else {
										if($this->querytype=="search" and addslashes($this->searchablefields[$i]["id"])==$this->savedstartswithfield) echo "selected=\"selected\"";
									}
								echo ">".$this->searchablefields[$i]["name"]."</option>\n";
							}
						?>
					</select>
				</p>
			</td>
			<td width="100%" nowrap="nowrap" valign="top" >
				<p><label for="startswith">starts with</label><br />
					<input id="startswith" name="startswith" type="text"  value="<?php if($this->querytype=="search" and isset($this->savedstartswith)) echo str_replace("\"","&quot;",stripslashes($this->savedstartswith))?>" size="35" maxlength="128" /><script language="JavaScript" type="text/javascript">setMainFocus()</script>
				</p>
			</td>
			<td align="left" valign="top" nowrap="nowrap" class="small">
				<p>
					<br />
					<input name="command" id="searchbutton" type="submit" class="Buttons" value="search"/>
				</p>
			</td>
		</tr>
		<tr>
			<td colspan="3" align="left" valign="middle" nowrap="nowrap">
			<p>
			<select name="Selection">
				<option value="new" <?php if ($this->querytype!="search" or ($this->querytype=="search" and $this->savedselection=="new") ) echo "selected=\"selected\""?> >new result</option>
				<option value="add" <?php if ($this->querytype=="search" and $this->savedselection=="add")echo "selected=\"selected\""?>>add to result</option>
				<option value="remove" <?php if ($this->querytype=="search" and $this->savedselection=="remove")echo "selected=\"selected\""?>>remove from result</option>
				<option value="narrow" <?php if ($this->querytype=="search" and $this->savedselection=="narrow")echo "selected=\"selected\""?>>narrow result</option>
			</select></p></td>
			<td align="left" valign="top" nowrap="nowrap"><p><input name="command" type="submit" id="reset" class="smallButtons" value="reset" accesskey="t" title="(access key+t)"/></p></td>
		</tr>
	</table>
</div><?php if(hasRights($this->thetabledef["advsearchroleid"])){?><div id="advancedSearchTab" style="display:none;"></div><?php } //end access ?>
<div id="loadSearchTab" style="display:none;padding:0px;margin:0px;"></div>
<div id="saveSearchTab" style="display:none;margin:0px;padding:0px;margin:0px;">
	<div id="saveSearchReults" style="display:none"></div>
	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td width="100%">
				<p><label for="saveSearchName">save current search as</label>
					<br />
					<input id="saveSearchName" name="saveSearchName" type="text"  value="" size="35" maxlength="128" onkeyup="enableSave(this)" />
				</p>
			</td>
			<td align="right">
				<p>
					<br />
					<input id="saveSearch" onclick="saveMySearch('<?php echo APP_PATH ?>')" disabled="disabled" type="button" class="Buttons" value="save search" />
				</p>
			</td>
		</tr>
	</table></div><div id="advancedSortTab" style="display:none;padding:0px;margin:0px;"></div></div><?php
	}//end function


		function displayQueryButtons() {

			global $phpbms;

			?><div id="resultInfoDiv"><?php

			if(!isset($this->tableoptions["new"])){
				 $this->tableoptions["new"]["allowed"]=0;
				 $this->tableoptions["new"]["roleid"]=0;
				 $this->tableoptions["new"]["needselect"]=0;
			}
			if(!isset($this->tableoptions["select"])) {
				$this->tableoptions["select"]["allowed"]=0;
				$this->tableoptions["select"]["roleid"]=0;
				$this->tableoptions["select"]["needselect"]=0;
			}
			if(!isset($this->tableoptions["edit"])){
				 $this->tableoptions["edit"]["allowed"]=0;
				 $this->tableoptions["edit"]["roleid"]=0;
				 $this->tableoptions["edit"]["needselect"]=0;
			}
			if(!isset($this->tableoptions["printex"])) {
				$this->tableoptions["printex"]["allowed"]=0;
				$this->tableoptions["printex"]["roleid"]=0;
				$this->tableoptions["printex"]["needselect"]=0;
			}
			if(!isset($this->tableoptions["import"])) {
				$this->tableoptions["import"]["allowed"]=0;
				$this->tableoptions["import"]["roleid"]=0;
				$this->tableoptions["import"]["needselect"]=0;
			}
			if(!isset($this->tableoptions["othercommands"])) $this->tableoptions["othercommands"]=false;

			// If they have rights to see the SQL statement, spit it out here.
			if(hasRights($this->thetabledef["viewsqlroleid"])) {

			?><div id="sqlstatement">
				<fieldset>
					<legend>SQL Statement</legend>
					<div id="theSqlText" class="mono small"><?php echo stripslashes(htmlQuotes($this->querystatement))?></div>
				</fieldset><?php

				if($this->sqlerror) {?>
				<fieldset>
					<legend><span style="text-transform:capitalize">SQL</span> Error</legend>
					<div><?php echo $this->sqlerror?></div>
				</fieldset><?php

				}?>
			</div>

			<?php }

			?><div id="commandSet"><?php

			if($this->numrows){
				?>
				<div id="numCount" align="right" class="small"><input type="hidden" id="deleteCommand" name="deleteCommand" value="" /><?php

				if ($this->truecount<=RECORD_LIMIT)
					echo "<div>records:&nbsp;".$this->numrows."</div>";
				else {

				?>
					<input name="offset" type="hidden" value="" /><select name="offsetselector" onchange="this.form.offset.value=this.value;this.form.submit();">
						<?php

							$displayedoffset=0;
							while($displayedoffset<$this->truecount){
								?><option value="<?php echo $displayedoffset?>" <?php if($displayedoffset==$this->recordoffset) echo "selected=\"selected\"";?>><?php echo ($displayedoffset+1)?>-<?php if($displayedoffset+RECORD_LIMIT<$this->truecount) echo ($displayedoffset+RECORD_LIMIT); else echo $this->truecount;?></option><?php
								$displayedoffset+=RECORD_LIMIT;
							}

						?>
					  </select> of <?php echo $this->truecount;
					if($this->recordoffset>0){
						?><button type="button" class="graphicButtons buttonRew" onclick="document.search.offset.value=<?php echo $this->recordoffset-RECORD_LIMIT ?>;document.search.submit();"><span>prev.</span></button><?php
					}
					if(($this->numrows+$this->recordoffset)<$this->truecount){
						?><button type="button" class="graphicButtons buttonFF" onclick="document.search.offset.value=<?php echo $this->recordoffset+RECORD_LIMIT ?>;document.search.submit();"><span>next</span></button><?php
					}

				}//end if ?></div><?php
			}//end if?>

				<ul id="recordCommands">
				<?php
					$showFirst = ' id="firstToolbarItem" ';

					if ($this->tableoptions["new"]["allowed"] && hasRights($this->tableoptions["new"]["roleid"])) {

					?><li <?php echo $showFirst?>>
						<a href="#" id="newRecord" class="newRecord" accesskey="n" title="new record (alt + n)" onclick="addRecord();return false;"><span>new</span></a>
					  </li><?php
					$showFirst = NULL;
				}

				if($this->numrows) {
					if ($this->tableoptions["edit"]["allowed"] && hasRights($this->tableoptions["edit"]["roleid"])) {
						?><li <?php echo $showFirst?>>
							<a href="#" id="editRecord" class="editRecordDisabled" accesskey="e" onclick="return editButton();" title="edit record (alt + e)"><span>edit</span></a>
						</li>
						<?php
						$showFirst = NULL;
					}//end if

					if($this->thetabledef["deletebutton"] == "delete") {
						?><li <?php echo $showFirst?>>
							<a href="#" id="deleteRecord" class="deleteRecordDisabled" accesskey="d" onclick="confirmDelete('delete');return false" title="delete record (alt + d)"><span>delete</span></a>
						</li>
						<?php
						$showFirst = NULL;
					}//end if

					if($this->tableoptions["printex"]["allowed"] && hasRights($this->tableoptions["printex"]["roleid"])){
						?><li <?php echo $showFirst?>>
							<a href="#" id="print" class="print" accesskey="p" onclick="doPrint();return false" title="print report (alt + p)"><span>print</span></a>
							<input type="hidden" id="doprint" name="doprint" value="no" />
						</li>
						<?php
						$showFirst = NULL;
					}//end if
				}//end if --numrows--
					if($this->tableoptions["othercommands"] || ($this->tableoptions["import"]["allowed"] && hasRights($this->tableoptions["import"]["roleid"])) || ($this->thetabledef["deletebutton"] != "delete" && $this->thetabledef["deletebutton"] != "NA") ){

						?><li <?php echo $showFirst?>>
							<a href="#" id="otherCommandButton" class="otherCommands" onclick="showDropDown('otherDropDown');return false" title="other commands"><span>other commands</span></a>
							<div id="otherDropDown" class="toolbarDropDowns" style="display:none">
								<ul>
									<?php
									if($this->thetabledef["deletebutton"] != "delete" && $this->thetabledef["deletebutton"] != "NA") {

										?>
										<li><a class="needselectDisabled" href="#" title="(alt + d)" onclick="chooseOtherCommand('-1','<?php echo $this->thetabledef["deletebutton"]?>',this); return false;"><strong><?php echo $this->thetabledef["deletebutton"]?></strong></a></li>
										<?php

										$displayOrder = -1;

									}else
										$displayOrder = 0;

									if($this->tableoptions["import"]["allowed"] && hasRights($this->tableoptions["import"]["roleid"])){

										$class = '';
										if($this->tableoptions["import"]["needselect"])
											$class = 'class="needselectDisabled"';
										?>
										<li><a <?php echo $class; ?> href="#" title="" onclick="chooseOtherCommand('-2','', this); return false;">import</a></li>
										<?php

										$displayOrder = -1;

									}else
										$displayOrder = ($displayOrder != -1)? 0 : (-1);

									if($this->tableoptions["othercommands"]){

										foreach($this->tableoptions["othercommands"] as $command){

											if(hasRights($command["roleid"])){

												$aclass="";
												$liclass="";
												if($command["displayorder"] != $displayOrder){

													$liclass = ' class="menuSep"';
													$displayOrder = $command["displayorder"];

												}
												if($command["needselect"])
														$aclass = ' class="needselectDisabled"';

												?>
												<li<?php echo $liclass?>><a<?php echo $aclass ?> href="#" onclick="chooseOtherCommand('<?php echo $command["id"] ?>','',this); return false;"><?php echo $command["name"]?></a></li>
												<?php

											}//end if

										}//endforeach

									}//end if
									?>
								</ul>
							</div><input id="othercommands" name="othercommands" type="hidden"/>
						</li>
						<?php
						$showFirst = NULL;
					}//end if

				if($this->numrows){
					if($this->tableoptions["select"]["allowed"] && hasRights($this->tableoptions["select"]["roleid"])){
						?><li <?php echo $showFirst?>>
							<a href="#" id="searchSelection" class="searchSelection" onclick="showDropDown('searchSelectionDropDown');return false" title="selection"><span>selection</span></a>
							<div id="searchSelectionDropDown" class="toolbarDropDowns" style="display:none">
							<ul>
								<li><a href="#" onclick="perfromToSelection('selectall');return false;" accesskey="a" title="select all (alt + a)">select all</a></li>
								<li><a href="#" onclick="perfromToSelection('selectnone');return false;" accesskey="x" title="select none (alt + x)">select none</a></li>
								<li class="menuSep"><a href="#" onclick="perfromToSelection('keepselected');return false;" accesskey="k" title="keep selected (alt + k)">show only selected records</a></li>
								<li><a href="#" onclick="perfromToSelection('omitselected');return false;" accesskey="o" title="omit selected (alt + o)">remove selected records from view</a></li>
							</ul>
							</div>
						</li>
						<?php
						$showFirst = NULL;
					}//end if

				}//end if numrows
					if(hasRights($this->thetabledef["viewsqlroleid"])){
						?>
						<li>
							<a href="#" id="showSQLButton" class="sqlUp" onclick="return false;" title="Show SQL Statement"><span>show SQL</span></a>
						</li>
						<?php }//end rights

				?>
				</ul>
				</div></div>
			<?php
			$phpbms->bottomJS[] = ' var addFile = "'.APP_PATH.$this->thetabledef["addfile"].'"';
			$phpbms->bottomJS[] = ' var editFile = "'.APP_PATH.$this->thetabledef["editfile"].'"';

			//for the import page, "" == the general page instead.
			$import = ($this->thetabledef["importfile"])?$this->thetabledef["importfile"]:"modules/base/general_import.php?id=".$this->thetabledef["id"];
			$phpbms->bottomJS[] = ' var importFile = "'.APP_PATH.$import.'"';

		}//end method



		function displayResultTable(){
			?><script language="JavaScript" type="text/javascript">selIDs=new Array();</script><input name="newsort" type="hidden" value="" />
			<div id="queryTableContainer"><table class="querytable" border="0" cellpadding="0" cellspacing="0">
				<thead>
					<?php $this->showResultHeader()?>
				</thead>
				<tfoot>
					<?php $this->showResultFooter()?>
				</tfoot>
				<tbody id="resultTbody">
					<?php
						if($this->numrows>0)
							$this->showResultRecords();
						else
							$this->showNoResults();
					?>
				</tbody>
			</table></div><?php

		}//end method



		function showResultFooter(){
			?><tr class="queryfooter"><?php
			foreach ($this->thecolumns as $therow){
				?><td align="<?php echo $therow["align"]?>"><?php
				if($therow["footerquery"]){
					$querystatement="SELECT ".$therow["footerquery"]." as thet FROM ".$this->therecords;
					$queryresult=$this->db->query($querystatement);

					$therecord=$this->db->fetchArray($queryresult);
					echo formatVariable($therecord["thet"],$therow["format"]);
				} else {echo "&nbsp;";}?></td><?php
			}//end foreach
			//keep this in here to close the total table
			?></tr><?php
		}//end function


		function displayRelationships(){
			// Get relationships
			$querystatement="SELECT
				 id, name
				 FROM relationships
				 WHERE fromtableid=\"".$this->thetabledef["id"]."\" ORDER BY name";
			$queryresult = $this->db->query($querystatement);
			if (!$queryresult) $error = new appError(1,"Error Retrieving Relationships");
			if ($this->db->numRows($queryresult)) {
				?><div class="small">
				show related records in <select id="relationship" name="relationship" onchange="setSelIDs(this.form);this.form.submit();"	disabled="disabled">
					<option value="" selected="selected" class="choiceListBlank">area...</option><?php
					while($therecord = $this->db->fetchArray($queryresult)){
					?><option value="<?php echo $therecord["id"]?>"><?php echo $therecord["name"]?></option><?php }
				?></select></div>
				<?php
			}  ?></form><?php
		}//end function

		function initialize($id){

			parent::initialize($id);

			$this->tableoptions=$this->getTableOptions($id);

			// now we need to populate the find (quick search) options
			$this->findoptions=$this->getTableQuickSearchOptions($this->uuid);

			// next we need to get a list of  searchable fields for the quick search drop down
			$this->searchablefields=$this->getTableSearchableFields($id);


			//check to see if critera has been saved to Session
			if(isset($_SESSION["tableparams"][$this->ref]))
				//grab the session
				$this->loadQueryParameters($_SESSION["tableparams"][$this->ref]);
			else{
				$this->loadQueryDefaults();
			}

		}

		function issueQuery(){
			$querycolumns="";
			$tempSortOrder = "";

			//GROUPING SETUP
			if($this->showGroupings){
				$i =1 ;
				foreach ($this->thegroupings as $thegroup){
					$querycolumns .= ", ".$thegroup["field"]." as \"_group".$i."\" ";
					$tempSortOrder .= ", ".$thegroup["field"];
					if($thegroup["ascending"] == 0)
						$tempSortOrder.=" DESC";
					$i++;
				}
				if($i > 1){
					$tempSortOrder = substr($tempSortOrder,2).", ";
				}
			}


			foreach ($this->thecolumns as $therow)
				$querycolumns.=", ".$therow["column"]." as \"".$therow["name"]."\"";
			$querycolumns=substr($querycolumns,2);

			$tempSortOrder .= $this->querysortorder;
			$this->therecords=$this->thetabledef["querytable"]." ".$this->queryjoinclause." WHERE ".$this->querywhereclause." ORDER BY ".$tempSortOrder;
			$this->querystatement = "SELECT DISTINCT ".$this->thetabledef["maintable"].".id as theid,".$querycolumns." FROM ".$this->therecords;

			parent::issueQuery();
		}//end function

		function loadQueryParameters($params){

			$this->querytype=$params["querytype"];
			$this->queryjoinclause=$params["queryjoinclause"];
			$this->querysortorder=$params["querysortorder"];
			$this->querywhereclause=$params["querywhereclause"];

			$this->showGroupings =  $params["showGroupings"];

			$this->savedfindoptions=$params["savedfindoptions"];
			$this->savedselection=$params["savedselection"];
			$this->savedstartswithfield=$params["savedstartswithfield"];
			$this->savedstartswith=$params["savedstartswith"];
			$this->savedendswith=$params["savedendswith"];
			$this->recordoffset=$params["recordoffset"];
			$this->sqlerror=$params["sqlerror"];

		}

		function saveQueryParameters(){

			$_SESSION["tableparams"][$this->ref]["querytype"]=$this->querytype;
			$_SESSION["tableparams"][$this->ref]["queryjoinclause"]=$this->queryjoinclause;
			$_SESSION["tableparams"][$this->ref]["querysortorder"]=$this->querysortorder;
			$_SESSION["tableparams"][$this->ref]["querywhereclause"]=$this->querywhereclause;

			$_SESSION["tableparams"][$this->ref]["showGroupings"]=$this->showGroupings;

			$_SESSION["tableparams"][$this->ref]["savedfindoptions"]=$this->savedfindoptions;
			$_SESSION["tableparams"][$this->ref]["savedselection"]=$this->savedselection;
			$_SESSION["tableparams"][$this->ref]["savedstartswithfield"]=$this->savedstartswithfield;
			$_SESSION["tableparams"][$this->ref]["savedstartswith"]=$this->savedstartswith;
			$_SESSION["tableparams"][$this->ref]["savedendswith"]=$this->savedendswith;
			$_SESSION["tableparams"][$this->ref]["recordoffset"]=$this->recordoffset;
			$_SESSION["tableparams"][$this->ref]["sqlerror"]=$this->sqlerror;

		}

		function loadQueryDefaults(){
			//load the defaults from the table definitions
			$this->querywhereclause=$this->subout($this->thetabledef["defaultwhereclause"]);
			$this->querytype=$this->thetabledef["defaultsearchtype"];
			$this->savedfindoptions=$this->thetabledef["defaultcriteriafindoptions"];
			$this->savedselection=$this->thetabledef["defaultcriteriaselection"];
			$this->querysortorder=$this->thetabledef["defaultsortorder"];
		}

		function resetQuery(){
			// reset query... this requires a call to the function that should be
			// defined in the same place the table paramaters are.
			//=====================================================================================================
			$this->querytype="search";
			$this->savedselection="";
			$this->savedstartswithfield="";
			$this->savedstartswith="";
			$this->savedendswith="";
			$this->queryjoinclause="";
			$this->showGroupings = true;

			$this->loadQueryDefaults();
		}

		function buildSearch($params){

			// assemble Search Criteria
			//=====================================================================================================
			//start with the find pull down
			foreach($this->findoptions as $checkoption){

				if(stripslashes($params["find"])==$checkoption["name"]) {

					$params["find"]=$checkoption["search"];
					//keep setting
					$this->savedfindoptions=$checkoption["name"];

				}//endif

			}//endforeach

			$find = $params["find"];

			//add start with & end with stuff
				if ($params["startswith"]){

					$params["startswith"]=addslashes($params["startswith"]);

					//Get the startswithfield info
					$i=0;
					while($this->searchablefields[$i]["id"]!=$params["startswithfield"])
						$i++;

					if($this->searchablefields[$i]["type"]=="field")
						$contains=$this->searchablefields[$i]["field"]." like \"".$params["startswith"]."%\"";
					else
						$contains=str_replace("{{value}}",$params["startswith"],$this->searchablefields[$i]["field"]);

					$find= "(".$find.") and (".$contains.")";

				}//endif

			//need to account for add/new/remove
			if(!isset($params["Selection"])) $params["Selection"]="new";
			switch($params["Selection"]){
				case "new":
					if(!isset($this->querytype)) $this->querytype="";
					if ($this->querytype!="search") {
						$this->queryjoinclause="";
					}
					$this->querywhereclause=$find;
				break;
				case "add":
					$this->querywhereclause="(".$this->querywhereclause.") or (".$find.")";
				break;
				case "remove":
					$this->querywhereclause="(".$this->querywhereclause.") and not (".$find.")";
				break;
				case "narrow":
					$this->querywhereclause="(".$this->querywhereclause.") and (".$find.")";
				break;
			}

			//keeping settings
			$this->querytype="search";
			$this->savedselection=$params["Selection"];
			$this->savedstartswithfield=$params["startswithfield"];
			$this->savedstartswith=$params["startswith"];

		}

	}//end class

	// SEARCH FUNCTIONS BASE CLASS ======================================================================
	class searchFunctions{

		var $db;
		var $tabledefid;
		var $idsArray = array();
		var $maintable;
		var $deletebutton;

		function searchFunctions($db,$tabledefid,$idsArray=array()){

			$this->db = $db;
			$this->tabledefid = (int) $tabledefid;
			$this->idsArray = $idsArray;

			$querystatement = "SELECT maintable,deletebutton FROM tabledefs WHERE id=".$this->tabledefid;
			$queryresult = $this->db->query($querystatement);
			$therecord = $this->db->fetchArray($queryresult);

			$this->maintable = $therecord["maintable"];
			$this->deletebutton = $therecord["deletebutton"];

		}//end method


		function delete_record(){

			$whereclause=$this->buildWhereClause();

			$endmessage="";
			switch($this->deletebutton){
				case "inactivate":
					$querystatement = "UPDATE `".$this->maintable."` SET ".$this->maintable.".inactive = 1, modifiedby = ".$_SESSION["userinfo"]["id"].", modifieddate = NOW() WHERE ".$whereclause;
					$endmessage=" marked inactive";
				break;
				default:
				case "delete":
					$querystatement = "DELETE FROM `".$this->maintable."` WHERE ".$whereclause;
					$endmessage=" deleted";
			}
			$queryresult = $this->db->query($querystatement);
			$message = $this->buildStatusMessage().$endmessage;

			return $message;

		}

		function buildWhereClause($fieldphrase = NULL ,$idsArray = NULL){
			if($fieldphrase === NULL)
				$fieldphrase = $this->maintable.".id";

			if($idsArray === NULL)
				$idsArray = $this->idsArray;

			$whereclause="";
			foreach($idsArray as $theid){
				$whereclause.=" OR ".$fieldphrase."=".$theid;
			}
			$whereclause=substr($whereclause,3);

			return $whereclause;
		}

		function buildStatusMessage($affected = NULL,$selected = NULL){
			if($affected === NULL)
				$affected = $this->db->affectedRows();

			if($selected === NULL)
				$selected = count($this->idsArray);

			switch($affected){
				case "0":
					$message="No records";
				break;
				case "1":
					$message="1 record";
				break;
				default:
					$message=$affected." records";
				break;
			}
			if($affected!=$selected)
				$message.=" (of ".$selected." selected)";
			return $message;
		}


	}//end class


//==============================================================================
class simpleTable extends displaySearchTable{

	var $uniqueName;

	function simpleTable($db, $id, $uniqueName){

		$this->db = $db;
		$this->initialize($id);
		$this->uniqueName = $uniqueName;

	}//end function simpleTable


	function show(){

		?>
		<table class="querytable simple" id="<?php echo $this->uniqueName?>" border="0" cellpadding="0" cellspacing="0">

			<thead><?php $this->showHeader()?></thead>

			<tfoot><?php $this->showResultFooter()?></tfoot>

			<tbody>
				<?php
					if($this->numrows>0)
						$this->showRecords();
					else
						$this->showNoResults();
				?>
				</tbody>
		</table>
		<?php


	}//end function show


	function showHeader(){

		?>
		<tr >
			<?php
				foreach ($this->thecolumns as $therow){

						?>
						<th nowrap="nowrap" align="<?php echo $therow["align"]?>" <?php if($therow["size"]) echo 'width="'.$therow["size"].'" ';?> >
							<?php echo formatVariable($therow["name"])?>
						</th>
						<?php

				}//end foreach
		?></tr><?php

	}//end function showHeader


	function showRecords(){

		$rownum = 1;

		$this->db->seek($this->queryresult,0);

		//groupings
		if($this->showGroupings)
			for($i = 0; $i < count($this->thegroupings); $i++)
				$this->thegroupings[$i]["theValue"]="";

		while($therecord = $this->db->fetchArray($this->queryresult)){

			// more groupings
			if($this->showGroupings){

				for($i = 0; $i < count($this->thegroupings); $i++){

					if($this->thegroupings[$i]["theValue"] != $therecord["_group".($i+1)]){

						$this->thegroupings[$i]["theValue"] = $therecord["_group".($i+1)];

						?><tr class="queryGroup"><td colspan = "<?php echo count($this->thecolumns)?>" <?php if($i) echo 'style = "padding-left:'.($i*15).'px"'?>>
						<?php
							if($this->thegroupings[$i]["displayname"])
								echo htmlQuotes($this->thegroupings[$i]["displayname"].": ");
							echo $therecord["_group".($i+1)];
						 ?>
						</td></tr><?php

						$rownum = 1;

					}//endif

				}//endfor

			}//endif

			?><tr id="<?php echo $this->uniqueName.":".$therecord["theid"];?>" class="qr<?php echo $rownum?>"><?php

				if ($rownum==1)
					$rownum++;
				else
				$rownum=1;

			foreach($this->thecolumns as $thecolumn){
				?><td align="<?php echo $thecolumn["align"]?>" <?php if(!$thecolumn["wrap"]) echo "nowrap=\"nowrap\""?>><?php echo (($therecord[$thecolumn["name"]]!=="")?formatVariable($therecord[$thecolumn["name"]],$thecolumn["format"]):"&nbsp;")?></td><?php
			}//endforeach

			?></tr><?php

		}//endwhile

	}//end function showRecords;

}//end class
?>
