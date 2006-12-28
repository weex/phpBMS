<?php	
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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
		
		//given a table id, go grab the table definition information for that table
		function getTableDef($id){
			global $dblink;
			$querystatement="SELECT tabledefs.id,maintable,querytable,tabledefs.displayname,addfile,editfile,deletebutton,type,
							  defaultwhereclause,defaultsortorder,defaultsearchtype,defaultcriteriafindoptions,defaultcriteriaselection,
							  modules.name,searchroleid,advsearchroleid,viewsqlroleid
							  FROM tabledefs inner join modules on tabledefs.moduleid=modules.id
							  WHERE tabledefs.id=".$id;
			
			$queryresult=mysql_query($querystatement,$dblink);
			if(!$queryresult) reportError(1,mysql_error($dblink)." -- ".$querystatement);
			
			if (mysql_num_rows($queryresult)<1) reportError(1,"table definition not found: ".$id);
			
			$therecord=mysql_fetch_array($queryresult);
			
			if(!hasRights($therecord["searchroleid"]))
				goURL($_SESSION["app_path"]."noaccess.php");
			
			return $therecord;
		}//end function getTableDef
		
		
		
		//given a table id, go grab the column and column information fro the table
		function getTableColumns($id){
			global $dblink;
			
			$thecolumns=Array();
			$querystatement="SELECT name,`column`,align,sortorder,footerquery,wrap,size,format
								  FROM tablecolumns WHERE tabledefid=".$id." ORDER BY displayorder";
			$queryresult=mysql_query($querystatement,$dblink) ;
			if(!$queryresult) reportError(1,mysql_error($dblink)." -- ".$querystatement);
			while($therecord=mysql_fetch_array($queryresult)) $thecolumns[]=$therecord;
			return $thecolumns;
		}
						
function displayQueryHeader(){
	?>
	<script language="JavaScript" type="text/javascript">selIDs=new Array();</script>
	<input name="newsort" type="hidden" value="" /><table cellspacing=0 cellpadding=0 border=0 class="querytable" id="queryresults"><tr>
	<?php
	$columncount=count($this->thecolumns);
	$i=1;

	foreach ($this->thecolumns as $therow){ ?>
<th nowrap align="<?php echo $therow["align"]?>" <?php if($therow["size"]) echo "width=\"".$therow["size"]."\" ";?> >
	<input name="sortit<?php echo $i?>" type="hidden" value="<?php echo $therow["name"]?>" />
	<a href="/" onclick="doSort(<?php echo $i?>);return false;"><?php echo $therow["name"]?></a>
	<?php
		// If sorting on this column give the option to reverse the sort order.
		if ($this->querysortorder==$therow["column"] || $this->querysortorder==$therow["sortorder"]) 
	{?>&nbsp;<a href="/" onclick="doDescSort();return false;"><img src="<?php echo $_SESSION["app_path"]?>common/image/down_arrow.gif" alt="dn" title="dn" width="10" height="10" border="0" /></a><input name="desc" type="hidden" value="" />
<?php }	elseif ($this->querysortorder==$therow["column"]." DESC" || $this->querysortorder==$therow["sortorder"]." DESC") 
{?> &nbsp;<a href="/" onclick="doSort(<?php echo $i?>);return false;"><img src="<?php echo $_SESSION["app_path"]?>common/image/up_arrow.gif" alt="up" title="up" width="10" height="10" border="0" /></a>
<?php }	?></th><?php
		$i++;
	}//end foreach
	?></tr><?php
	
}//end function

		//output a query
		function displayQueryResults() {
			if(!isset($this->options["new"])) $this->options["new"]=1;
			if(!isset($this->options["select"])) $this->options["select"]=1;
			if(!isset($this->options["edit"])) $this->options["edit"]=1;
			
			$rownum=1;
			mysql_data_seek($this->queryresult,0);
			while($therecord = mysql_fetch_array($this->queryresult)){
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
					?><td align="<?php echo $thecolumn["align"]?>" <?php if(!$thecolumn["wrap"]) echo "nowrap"?>><?php echo (($therecord[$thecolumn["name"]]!=="")?formatVariable($therecord[$thecolumn["name"]],$thecolumn["format"]):"&nbsp;")?></td><?php
				}
				?></tr><?php 
			}
		}//end function
		
		
		// display a no results page
		function displayNoResults(){
			$i=count($this->thecolumns);?>
			<tr><td colspan="<?php echo $i?>" align=center style="padding:0px;">
				<?php if(!$this->sqlerror) {?>
				<div class="norecords">No Records to Display</div>
				<?php } else {?>
				<div class="norecords">Invalid Search</div>				
				<?php } ?>
			</td></tr>
			</table>
			<?php
		}
		
		function initialize($id){
			$this->thetabledef=$this->getTableDef($id);
			$this->ref=$this->thetabledef["id"];
			
/*			if ($this->thetabledef["type"]!="view")
				$this->ref=$this->thetabledef["maintable"];
			else
				$this->ref=$this->thetabledef["maintable"].$this->thetabledef["id"];
*/
			//next we set the columns
			$this->thecolumns=$this->getTableColumns($id);

		}
		
		
		function issueQuery(){
			global $dblink;
						
			//save the query for total and display purposes
			$_SESSION["thequerystatement"] = $this->querystatement;
			//Add limit (settings)
			$_SESSION["thequerystatement"].=" limit ".$this->recordoffset.", ".$_SESSION["record_limit"].";";
			$this->queryresult = mysql_query($_SESSION["thequerystatement"],$dblink);
			if($this->queryresult){
				 $this->numrows=mysql_num_rows($this->queryresult);
				 if($this->numrows==$_SESSION["record_limit"] or $this->recordoffset!=0){
				    //if you max the record limit or are already offsetiing get the true count
					$truecountstatement="SELECT count(distinct ".$this->thetabledef["maintable"].".id) as thecount".strstr(substr($this->querystatement,0,strpos($this->querystatement," ORDER BY"))," FROM ");
					$truequeryresult=mysql_query($truecountstatement,$dblink); 
					if(!$truequeryresult) reportError(100,$truecountstatement." ".mysql_error($dblink));
					$truerecord=mysql_fetch_array($truequeryresult);
					$this->truecount=$truerecord["thecount"];
				 }
				 else $this->truecount=$this->numrows;
				$this->sqlerror="";
			}else{
				$this->sqlerror=mysql_error($dblink);
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
		
		function sendInfo($value,$display){?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Choose</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="JavaScript" type="text/javascript">
function sendInfo(name,thevalue,thedisplay){
	//stupid browser incompatibilities
		//netscape
		var theform=opener.document.forms['record'];
		theform[name].value=thevalue;
		theform["display"+name].value=thedisplay;
		if(theform[name].onchange) 
			theform[name].onchange();
		window.close();
}
</script>
</head>
<body>
<?php echo "<script language=\"JavaScript\">sendInfo('".$this->fieldname."','".addslashes($value)."','".addslashes($display)."');</SCRIPT>";?>
</body>
</html>	<?php
		}//end function
		
		function issueInitialQuery(){
			global $dblink;
			
			$querystatement="SELECT ".$this->valuefield." AS value, ".$this->displayfield." AS display FROM ".$this->thetabledef["maintable"]." WHERE ";
			$querystatement.="(".$this->displayfield." LIKE \"".$this->searchvalue."%\") ";
			if($this->whereclause)
				$querystatement.="AND (".$this->whereclause.")";
			
			$queryresult=mysql_query($querystatement,$dblink);
			if(!$queryresult) reportError(100,"Error Retrieving Initial Rowset: ".mysql_error($dblink)."<br>".$querystatement);
			
			return $queryresult;
		}
		
		function issueQuery(){
			$querycolumns="";
			foreach ($this->thecolumns as $therow)
				$querycolumns.=", ".$therow["column"]." as \"".$therow["name"]."\"";
			$querycolumns=substr($querycolumns,2);
						
			$this->querystatement = "SELECT DISTINCT ".$this->valuefield." AS value, ".$this->displayfield." AS display, ".$querycolumns." FROM ".$this->thetabledef["querytable"]." WHERE";
			$this->querystatement.="(".$this->displayfield." LIKE \"".$this->searchvalue."%\") ";
			if($this->whereclause)
				$this->querystatement.="AND (".$this->whereclause.")";
			$this->querystatement.=" ORDER BY ".$this->querysortorder;
			
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
			global $dblink;
		
			$options=Array();
			$querystatement="SELECT name,`option`,othercommand,roleid
								  FROM tableoptions WHERE tabledefid=".$id;
			$queryresult=mysql_query($querystatement,$dblink);
			if(!$queryresult) reportError(1,mysql_error($dblink)." -- ".$querystatement);
			
			while($therecord=mysql_fetch_array($queryresult)) {
				if($therecord["othercommand"]) {
					$options["othercommands"][$therecord["name"]]["displayname"]=$therecord["option"];
					$options["othercommands"][$therecord["name"]]["roleid"]=$therecord["roleid"];
				}else{
					$options[$therecord["name"]]["allowed"]=$therecord["option"];
					$options[$therecord["name"]]["roleid"]=$therecord["roleid"];
				}
			}
			return $options;
		}//end getTableOptions

		function getTableQuickSearchOptions($id){
			global $dblink;
			
			$findoptions=Array();
			$querystatement="SELECT name,search,roleid
								  FROM tablefindoptions WHERE tabledefid=".$id." ORDER BY displayorder";
			$queryresult=mysql_query($querystatement,$dblink);
			if(!$queryresult) reportError(1,mysql_error($dblink)." -- ".$querystatement);
		
			while($therecord=mysql_fetch_array($queryresult)){
				$therecord["search"]=$this->subout($therecord["search"]);
				$findoptions[]=$therecord;
			}
			
			return $findoptions;
		}
		
		function getTableSearchableFields($id){
			global $dblink;
		
			$searchablefields=Array();
			$querystatement="SELECT id,field,name,type
								  FROM tablesearchablefields WHERE tabledefid=".$id." ORDER BY displayorder";
			$queryresult=mysql_query($querystatement,$dblink);
			if(!$queryresult) reportError(1,mysql_error($dblink)." -- ".$querystatement);
		
			while($therecord=mysql_fetch_array($queryresult)) $searchablefields[]=$therecord;
			
			return $searchablefields;
		}

		function displaySearch(){

		?>
<form name="search" id="search" method="post" action="<?php echo $_SERVER["PHP_SELF"]?>?id=<?php echo $this->thetabledef["id"]?>" onSubmit="setSelIDs(this);return true;">
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
	<li id="basicSearchT" class="tabsSel"><a href="/" onClick="switchSearchTabs(this);return false">basic</a></li>
	<?php if(hasRights($this->thetabledef["advsearchroleid"])){?><li id="advancedSearchT"><a href="/" onClick="switchSearchTabs(this,'<?php echo $_SESSION["app_path"]?>');return false">advanced</a></li><?php } //end access ?>
	<li id="loadSearchT"><a href="/" onClick="switchSearchTabs(this,'<?php echo $_SESSION["app_path"]?>');return false">load search</a></li>
	<li id="saveSearchT"><a href="/" onClick="switchSearchTabs(this,'<?php echo $_SESSION["app_path"]?>');return false">save search</a></li>
	<li id="advancedSortT"><a href="/" onClick="switchSearchTabs(this,'<?php echo $_SESSION["app_path"]?>');return false">sorting</a></li>
</ul>
<div class="box" id="searchBox">
	<div id="basicSearchTab">
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td nowrap valign="top">
					<p>
						<label for="find">find</label><br />					
						<select name="find" id="find">
						<?php 											
							for($i=0;$i<count($this->findoptions);$i++) {
								if(hasRights($this->findoptions[$i]["roleid"])){
									?><option value="<?php echo $this->findoptions[$i]["name"]?>"<?php 
										if($this->querytype=="search" and $this->findoptions[$i]["name"]==$this->savedfindoptions) echo "selected";
									?>><?php echo $this->findoptions[$i]["name"]?></option><?php
								}
							}
						?>
						</select>
					</p>
				</td>
			<td nowrap valign=top>
				<p>
				<label for="startswithfield">where</label><br />
					<select name="startswithfield" id="startswithfield">
						<?php 
							for($i=0;$i<count($this->searchablefields);$i++) {
								echo "<option value=\"".$this->searchablefields[$i]["id"]."\" ";
									if(!isset($this->savedstartswithfield)){
										if($this->querytype!="search" and $i==0) echo "selected";				
									} else {							
										if($this->querytype=="search" and addslashes($this->searchablefields[$i]["id"])==$this->savedstartswithfield) echo "selected";
									}
								echo ">".$this->searchablefields[$i]["name"]."</option>\n";
							}
						?>
					</select>
				</p>
			</td>
			<td width="100%" nowrap valign="top" >
				<p><label for="startswith">starts with</label><br />
					<input id="startswith" name="startswith" type="text"  value="<?php if($this->querytype=="search" and isset($this->savedstartswith)) echo str_replace("\"","&quot;",stripslashes($this->savedstartswith))?>" size="35" maxlength="128" /><script language="JavaScript" type="text/javascript">setMainFocus()</script>
				</p>
			</td>
			<td align="left" valign="top" nowrap class="small">
				<p>
					<br />
					<input name="command" id="searchbutton" type="submit" class="Buttons" value="search"/>
				</p>
			</td>
		</tr>
		<tr>
			<td colspan="3" align="left" valign=middle nowrap>
			<p>
			<select name="Selection">
				<option value="new" <?php if ($this->querytype!="search" or ($this->querytype=="search" and $this->savedselection=="new") ) echo "selected"?> >new result</option>
				<option value="add" <?php if ($this->querytype=="search" and $this->savedselection=="add")echo "selected"?>>add to result</option>
				<option value="remove" <?php if ($this->querytype=="search" and $this->savedselection=="remove")echo "checked"?>>remove from result</option>
				<option value="narrow" <?php if ($this->querytype=="search" and $this->savedselection=="narrow")echo "checked"?>>narrow result</option>
			</select></p>
			<td align="left" valign=top nowrap ><p><input name="command" type="submit" id="reset" class="smallButtons" value="reset" accesskey="t" title="(alt+t)"/></p></td>
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
					<input id="saveSearchName" name="saveSearchName" type="text"  value="" size="35" maxlength="128" onKeyUp="enableSave(this)" />
				</p>
			</td>
			<td align="right">
				<p>
					<br />
					<input id="saveSearch" onClick="saveMySearch('<?php echo $_SESSION["app_path"] ?>')" disabled="true" type="button" class="Buttons" value="save search" />
				</p>
			</td>
		</tr>
	</table></div><div id="advancedSortTab" style="display:none;padding:0px;margin:0px;"></div></div><?php 				
	}//end function		
		
function displayQueryButtons() { 
	if(!isset($this->tableoptions["new"])){
		 $this->tableoptions["new"]["allowed"]=0;
		 $this->tableoptions["new"]["roleid"]=0;
	}
	if(!isset($this->tableoptions["select"])) {
		$this->tableoptions["select"]["allowed"]=0;
		$this->tableoptions["select"]["roleid"]=0;
	}
	if(!isset($this->tableoptions["edit"])){
		 $this->tableoptions["edit"]["allowed"]=0;
		 $this->tableoptions["edit"]["roleid"]=0;
	}
	if(!isset($this->tableoptions["printex"])) {
		$this->tableoptions["printex"]["allowed"]=0;
		$this->tableoptions["printex"]["roleid"]=0;
	}
	if(!isset($this->tableoptions["othercommands"])) $this->tableoptions["othercommands"]=false;
	if(hasRights($this->thetabledef["viewsqlroleid"])){?>
	<div id="sqlstatement">
	<fieldset>
		<legend>SQL Statement</legend>
		<div class="mono small" style="height:150px; overflow:auto;"><?php echo stripslashes(htmlspecialchars($this->querystatement))?></div>
	</fieldset><?php if($this->sqlerror) {?>
	<fieldset>
		<legend><span style="text-transform:capitalize">SQL</span> Error</legend>
		<div><?php echo $this->sqlerror?></div>
	</fieldset><?php }?>
	</div>
	<?php }
	if($this->numrows){
		?><input type="hidden" id="deleteCommand" name="deleteCommand" value="" /><div id="numCount" align="right" class="small"><?php
		if ($this->truecount<=$_SESSION["record_limit"]) 
			echo "<div>records:&nbsp;".$this->numrows."</div>";
		else {?>			
			<input name="offset" type="hidden" value=""><select name="offsetselector" onChange="this.form.offset.value=this.value;this.form.submit();">
			  	<?php
					$displayedoffset=0;
					while($displayedoffset<$this->truecount){
						?><option value="<?php echo $displayedoffset?>" <?php if($displayedoffset==$this->recordoffset) echo "selected";?>><?php echo ($displayedoffset+1)?>-<?php if($displayedoffset+$_SESSION["record_limit"]<$this->truecount) echo ($displayedoffset+$_SESSION["record_limit"]); else echo $this->truecount;?></option><?php
						$displayedoffset+=$_SESSION["record_limit"];
					}
				?>
			  </select> of <?php echo $this->truecount;
			if($this->recordoffset>0){
				?><button type="button" class="graphicButtons buttonRew" onclick="document.search.offset.value=<?php echo $this->recordoffset-$_SESSION["record_limit"] ?>;document.search.submit();"><span>prev.</span></button><?php
			}
			if(($this->numrows+$this->recordoffset)<$this->truecount){
				?><button type="button" class="graphicButtons buttonFF" onClick="document.search.offset.value=<?php echo $this->recordoffset+$_SESSION["record_limit"] ?>;document.search.submit();"><span>next</span></button><?php
			}
						  
		} ?></div><?php }?>	
	
		<div id="recordCommands">
		<?php 
		if ($this->tableoptions["new"]["allowed"] && hasRights($this->tableoptions["new"]["roleid"])) 
			{
		?><button type="button" accesskey="n" class="graphicButtons buttonNew" onClick="addRecord()" title="new (alt+n)"><span>new</span></button><?php 
			} 
			
		if($this->numrows) {
			if ($this->tableoptions["edit"]["allowed"] && hasRights($this->tableoptions["edit"]["roleid"])) {
				?><button id="edit" accesskey="e" type="button" disabled="true" class="graphicButtons buttonEditDisabled" onClick="editThis()" title="edit (alt+e)"><span>edit</span></button><?php
			}
		
			if($this->tableoptions["printex"]["allowed"] && hasRights($this->tableoptions["printex"]["roleid"])){
				?><button id="print" accesskey="p" type="submit" disabled="true" class="graphicButtons buttonPrintDisabled" name="doprint"  title="print (alt+p)"><span>print</span></button><?php
			}

			if($this->thetabledef["deletebutton"] == "delete") {				
				?><button id="delete" name="dodelete" accesskey="d" type="button" title="delete (alt+d)" disabled="true" onClick="confirmDelete('delete')" class="graphicButtons buttonDeleteDisabled"><span>delete</span></button><?php
			}
	
			if($this->tableoptions["othercommands"] || ($this->thetabledef["deletebutton"] != "delete" && $this->thetabledef["deletebutton"] != "NA") ){?>			
				<select id="othercommands" name="othercommands" disabled=true onChange="chooseOtherCommand(this)">
				<option value="" selected class="choiceListBlank">commands...</option>
				<?php if($this->thetabledef["deletebutton"] != "delete" && $this->thetabledef["deletebutton"] != "NA") {?>
					<option value="delete_record" class="important"><?php echo $this->thetabledef["deletebutton"]?></option>
				<?php } 
				if($this->tableoptions["othercommands"]){
					foreach($this->tableoptions["othercommands"] as $key => $value){
						if(hasRights($value["roleid"])){
							?><option value="<?php echo $key?>"><?php echo $value["displayname"]?></option><?php
						}
					}
				}
				?></select><?php
		}
		if($this->tableoptions["select"]["allowed"] && hasRights($this->tableoptions["select"]["roleid"])){?> <select id="searchSelection" onChange="perfromToSelection(this)">
				<option class="choiceListBlank" value="">selection...</option>
				<option value="">_____________</option>
				<option value="selectall" title="(alt+a)">select all</option>
				<option value="selectnone" title="(alt+x)">select none</option>
				<option value="">_____________</option>
				<option value="keepselected" title="(alt+k)">keep selected</option>
				<option value="omitselected" title="(alt+o)" >omit selected</option>
			</select><a href="/" onClick="changeSelection('selectall');return false;" accesskey="a" tabindex="0"></a><a href="/" onClick="changeSelection('selectnone');return false;" accesskey="x" tabindex="0"></a><a href="/" onClick="changeSelection('keepselected');return false;" accesskey="k" tabindex="0"></a><a href="/" onClick="changeSelection('omitselected');return false;" accesskey="o" tabindex="0"></a><?php } 
		
		}//end if numrows	
		if(hasRights($this->thetabledef["viewsqlroleid"])){?><button id="showSQLButton" type="button" class="graphicButtons buttonShowSQLDown"><span>Show SQL</span></button><?php }//end rights?>
		</div><script language="JavaScript" type="text/javascript">
	var addFile="<?php echo $_SESSION["app_path"].$this->thetabledef["addfile"]?>";
	var editFile="<?php echo $_SESSION["app_path"].$this->thetabledef["editfile"]?>";
	</script><?php	
}//end function
			



function displayQueryFooter(){
	global $dblink;
	?>
	<tr><?php
	foreach ($this->thecolumns as $therow){
	?>
		<td align="<?php echo $therow["align"]?>" class="queryfooter"><?php
		if($therow["footerquery"]){
			$querystatement="SELECT ".$therow["footerquery"]." FROM ".$this->therecords;
			$queryresult=mysql_query($querystatement);
			if(!$queryresult) reportError(502,"Footer Query Invalid");
			
			$therecord=mysql_fetch_array($queryresult);
			echo formatVariable($therecord[0],$therow["format"]);
		} else {echo "&nbsp;";}?></td><?php 
	}
	//keep this in here to close the total table
	?></tr></table><?php
}//end function

function displayRelationships(){
	// Get relationships
	$querystatement="SELECT
		 id, name 
		 FROM relationships
		 WHERE fromtableid=\"".$this->thetabledef["id"]."\" ORDER BY name";
	$queryresult = mysql_query($querystatement);	
	if (!$queryresult) reportError(1,"Error Retrieving Relationships");
	if (mysql_num_rows($queryresult)) {
		?><div class="small box" style="margin:0px;margin-top:3px;">
		relate selected records to <select id="relationship" name="relationship" onChange="setSelIDs(this.form);this.form.submit();"	disabled="true">
			<option value="" selected class="choiceListBlank">area...</option><?php 
			while($therecord = mysql_fetch_array($queryresult)){
			?><option value="<?php echo $therecord["id"]?>"><?php echo $therecord["name"]?></option><?php }
		?></select></div>
		<?php
	}  ?></form><?php
}//end function

		function initialize($id){
			parent::initialize($id);
			$this->tableoptions=$this->getTableOptions($id);			
			// now we need to populate the find (quick search) options
			$this->findoptions=$this->getTableQuickSearchOptions($id);
			
			// next we need to get a list of  searchable fields for the quick search drop down
			$this->searchablefields=$this->getTableSearchableFields($id);
			

			//check to see if critera has been saved to Session
			if(isset($_SESSION["tableparams"][$this->ref]))
				//grab the session
				$this->loadQueryParameters($_SESSION["tableparams"][$this->ref]);
			else{
				$this->loadQueryDefaults();
			}
				
											
			//load table specific functions
			if ($this->thetabledef["type"]!="view")
				@ include($this->base."modules/".$this->thetabledef["name"]."/include/".$this->thetabledef["maintable"]."_search_functions.php");
            else
				@ include($this->base."modules/".$this->thetabledef["name"]."/include/".$this->thetabledef["maintable"].$this->thetabledef["id"]."_search_functions.php");

		}

		function issueQuery(){
			$querycolumns="";
			foreach ($this->thecolumns as $therow)
				$querycolumns.=", ".$therow["column"]." as \"".$therow["name"]."\"";
			$querycolumns=substr($querycolumns,2);
						
			$this->therecords=$this->thetabledef["querytable"]." ".$this->queryjoinclause." WHERE ".$this->querywhereclause." ORDER BY ".$this->querysortorder;
			$this->querystatement = "SELECT DISTINCT ".$this->thetabledef["maintable"].".id as theid,".$querycolumns." FROM ".$this->therecords;

			parent::issueQuery();
		}//end function

		function loadQueryParameters($params){
		
			$this->querytype=$params["querytype"];
			$this->queryjoinclause=$params["queryjoinclause"];
			$this->querysortorder=$params["querysortorder"];
			$this->querywhereclause=$params["querywhereclause"];

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
			
			$this->loadQueryDefaults();
		}

		function buildSearch($params){
			// assemble Search Criteria		
			//=====================================================================================================
			//start with the find pull down
			foreach($this->findoptions as $checkoption){
				if($params["find"]==$checkoption["name"]) {
					$params["find"]=$checkoption["search"];
					//keep setting
					$this->savedfindoptions=$checkoption["name"];
				}
			}
			$find=$params["find"];
	
			//add start with & end with stuff
				if ($params["startswith"]){ 
					$params["startswith"]=addslashes($params["startswith"]);
					//Get the startswithfield info
					$i=0;
					while($this->searchablefields[$i]["id"]!=$params["startswithfield"]) $i++;
					
					if($this->searchablefields[$i]["type"]=="field")					
						$contains=$this->searchablefields[$i]["field"]." like \"".$params["startswith"]."%\"";
					else
						$contains=str_replace("{{value}}",$params["startswith"],$this->searchablefields[$i]["field"]);					
					$find= "(".$find.") and (".$contains.")";
				}
				
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
?>
