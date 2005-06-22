<?php	
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
							  modules.name
							  FROM tabledefs inner join modules on tabledefs.moduleid=modules.id
							  WHERE tabledefs.id=".$id;
			
			$queryresult=mysql_query($querystatement,$dblink);
			if(!$queryresult) reportError(1,mysql_error()." -- ".$querystatement);
			
			if (mysql_num_rows($queryresult)<1) reportError(1,"table definition not found. ".$queryresult);
			
			$therecord=mysql_fetch_array($queryresult);
			
			return $therecord;
		}//end function getTableDef
		
		
		
		//given a table id, go grab the column and column information fro the table
		function getTableColumns($id){
			global $dblink;
			
			$thecolumns=Array();
			$querystatement="SELECT name,`column`,align,sortorder,footerquery,wrap,size
								  FROM tablecolumns WHERE tabledefid=".$id." ORDER BY displayorder";
			$queryresult=mysql_query($querystatement,$dblink) ;
			if(!$queryresult) reportError(1,mysql_error()." -- ".$querystatement);
			while($therecord=mysql_fetch_array($queryresult)) $thecolumns[]=$therecord;
			return $thecolumns;
		}
						
function displayQueryHeader(){
	?>
	<input name="newsort" type="hidden" value=""><table cellspacing=0 cellpadding=0 border=0 class="querytable" id="queryresults"><tr>
	<script language="javascript">selIDs=new Array();</script>
	<?php
	$columncount=count($this->thecolumns);
	$i=1;

	foreach ($this->thecolumns as $therow){ ?>
<th nowrap class="queryheader" align="<?php echo $therow["align"]?>" <?php if($therow["size"]) echo "width=\"".$therow["size"]."\" "; if($i==$columncount) echo "style=\"border-right:0px;\"";?> >
	<input name="sortit<?php echo $i?>" type="hidden" value="<?php echo $therow["name"]?>">
	<a href="" onClick="doSort(<?php echo $i?>);return false;"><?php echo $therow["name"]?></a>
	<?php
		// If sorting on this column give the option to reverse the sort order.
		if ($this->querysortorder==$therow["column"] || $this->querysortorder==$therow["sortorder"]) 
	{?>&nbsp;<a href="" onClick="doDescSort();return false;"><img src="<?php echo $_SESSION["app_path"]?>common/image/down_arrow.gif" width=10 height=10 border=0></a><input name="desc" type="hidden" value="">
<?php }	elseif ($this->querysortorder==$therow["column"]." DESC" || $this->querysortorder==$therow["sortorder"]." DESC") 
{?> &nbsp;<a href="" onClick="doSort(<?php echo $i?>);return false;"><img src="<?php echo $_SESSION["app_path"]?>common/image/up_arrow.gif" width=10 height=10 border=0></a>
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
				?><tr class="qr<?php echo $rownum?>" id="r-<?php echo $therecord["id"]?>" <?php

				if ($this->options["select"]) {
					?> onClick="clickIt(this,event,'<?php echo $this->isselect?>')" <?php 
				}
				if ($this->options["edit"]) {
					?> onDblClick="editThis(this);"<?php 
				}
				?> ><?php 
				
				if ($rownum==1) $rownum++; else $rownum=1;
				
				foreach($this->thecolumns as $thecolumn){
					?><td align="<?php echo $thecolumn["align"]?>" <?php if(!$thecolumn["wrap"]) echo "nowrap"?>><?php echo ($therecord[$thecolumn["name"]]?$therecord[$thecolumn["name"]]:"&nbsp;")?></td><?php
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

			if ($this->thetabledef["type"]!="view")
				$this->ref=$this->thetabledef["maintable"];
			else
				$this->ref=$this->thetabledef["maintable"].$this->thetabledef["id"];

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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" >
<html>
<head>
<title>Choose</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript">
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
<?PHP echo "<script language=\"JavaScript\">sendInfo('".$this->fieldname."','".addslashes($value)."','".addslashes($display)."');</SCRIPT>";?>
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
			if(!$queryresult) reportError(100,"Error Retrieving Initial Rowset: ".mysql_error()."<br>".$querystatement);
			
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
			$querystatement="SELECT name,`option`,othercommand
								  FROM tableoptions WHERE tabledefid=".$id;
			$queryresult=mysql_query($querystatement,$dblink);
			if(!$queryresult) reportError(1,mysql_error()." -- ".$querystatement);
			
			while($therecord=mysql_fetch_array($queryresult)) {
			if($therecord["othercommand"])
				$options["othercommands"][$therecord["name"]]=$therecord["option"];
			else
				$options[$therecord["name"]]=$therecord["option"];
			}
			return $options;
		}//end getTableOptions

		function getTableQuickSearchOptions($id){
			global $dblink;
			
			$findoptions=Array();
			$querystatement="SELECT name,search
								  FROM tablefindoptions WHERE tabledefid=".$id." ORDER BY displayorder";
			$queryresult=mysql_query($querystatement,$dblink);
			if(!$queryresult) reportError(1,mysql_error()." -- ".$querystatement);
		
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
			if(!$queryresult) reportError(1,mysql_error()." -- ".$querystatement);
		
			while($therecord=mysql_fetch_array($queryresult)) $searchablefields[]=$therecord;
			
			return $searchablefields;
		}




		function displaySearch(){

		?>
<form name="search" id="searchform" method="post" action="<?PHP echo $_SERVER["PHP_SELF"]?>?id=<?php echo $this->thetabledef["id"]?>" onSubmit="setSelIDs(this);return true;">
<input name="theids" type="hidden" value="">
<input name="advancedsearch" type="hidden" value="">
<input name="advancedsort" type="hidden" value="">
<div class="box" style="margin:0px;margin-bottom:8px;">
	<?php if ($this->querytype!="" and $this->querytype!="search") 
			echo "<div><i>(currently showing ".$this->querytype.")</i></div>"
	?>
	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td nowrap valign=top>
				<div>find<br>
				<select name="find">
					<?PHP 											
						for($i=0;$i<count($this->findoptions);$i++) {
							?><option value="<?php echo $this->findoptions[$i]["name"]?>"<?php 
								if($this->querytype=="search" and $this->findoptions[$i]["name"]==$this->savedfindoptions) echo "selected";
							?>><?php echo $this->findoptions[$i]["name"]?></option><?php
						}
					?>
				</select>				  
				</div></td>
			<td nowrap valign=top>
				<div>
					where<br>
					<select name="startswithfield">
						<?PHP 
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
				</div>
			</td>
			<td width="100%" nowrap valign=top >
				<div>
					starts with<br>
					<input id="startswith" name="startswith" type="text" style="width:99%;" value="<?php if($this->querytype=="search" and isset($this->savedstartswith)) echo stripslashes($this->savedstartswith)?>" size="35" maxlength="128"><script language="javascript">setMainFocus()</script>
				</div>
			</td>
			<td align="left" valign="top" nowrap class="small">
				<div>
					<br>
					<input name="command" id="searchbutton" type="submit" class="Buttons" value="Search" style="width:90px;">
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="left" valign=middle nowrap>
				<div> 
					<select name="Selection">
						<option value="new" <?php if ($this->querytype!="search" or ($this->querytype=="search" and $this->savedselection=="new") ) echo "selected"?> >new result</option>
						<option value="add" <?php if ($this->querytype=="search" and $this->savedselection=="add")echo "selected"?>>add to result</option>
						<option value="remove" <?php if ($this->querytype=="search" and $this->savedselection=="remove")echo "checked"?>>remove from result</option>
						<option value="narrow" <?php if ($this->querytype=="search" and $this->savedselection=="narrow")echo "checked"?>>narrow result</option>
					</select>
				</div>
			</td>
			<td align="right" valign=top nowrap >
				<div><?PHP if($_SESSION["userinfo"]["accesslevel"]>90){?><input name="command" type="button" class="smallButtons" value="view SQL" style="margin-right:3px;" onClick="openWindow('viewsqlstatement.php','Count','resize=yes,status=yes,scrollbars=yes,width=700,height=350,modal=yes')"><?PHP }//end accesslevel?>
					<input name="command" type="button" class="smallButtons" id="advancedsortbutton" style="" onClick="openWindow('advancedsort.php?id=<?PHP echo $this->thetabledef["id"] ?>','AdvancedSearch','status=no,scrollbars=no,width=540,height=310,modal=yes')" value="advanced sort">					
					<input name="command" type="button" class="smallButtons" id="advanced" style="" onClick="openWindow('advancedsearch.php?id=<?PHP echo $this->thetabledef["id"] ?>','AdvancedSearch','status=no,scrollbars=no,width=540,height=310,modal=yes')" value="advanced search">                        
			</div></td>
			<td align="right" valign=top nowrap ><div><input name="command" type="submit" id="reset" class="smallButtons" value="Reset" style="width:90px;"></div></td>
		</tr>				
	</table>
</div>
<?PHP 				
		}//end function
		
		
function displayQueryButtons() { 


	if(!isset($this->tableoptions["new"])) $this->tableoptions["new"]=0;
	if(!isset($this->tableoptions["select"])) $this->tableoptions["select"]=0;
	if(!isset($this->tableoptions["edit"])) $this->tableoptions["edit"]=0;
	if(!isset($this->tableoptions["printex"])) $this->tableoptions["printex"]=0;
	if(!isset($this->tableoptions["othercommands"])) $this->tableoptions["othercommands"]=false;
	
	?>
	<table border=0 cellspacing=0 cellpadding=0><tr><td nowrap>
	<tr>
	<?php if($this->tableoptions["new"] or $this->tableoptions["edit"] or $this->thetabledef["deletebutton"]!="NA" ) {?>
	<td class=buttonSectionTitles nowrap valign=bottom>&nbsp;record</td>
	<?php }
	if($this->numrows){
		if($this->tableoptions["select"]){
			?>
			<td nowrap>&nbsp;</td>
			<td class=buttonSectionTitles nowrap valign=bottom>&nbsp;select</td>
			<td nowrap>&nbsp;</td>
			<td class=buttonSectionTitles nowrap valign=bottom>&nbsp;highlight</td>
			<?php 
		}
		if($this->tableoptions["printex"]){
			?>
			<td nowrap>&nbsp;</td>
			<td class=buttonSectionTitles nowrap valign=bottom>&nbsp;print/export</td>
			<?php
		}
		if($this->tableoptions["othercommands"]){
			?>
			<td nowrap>&nbsp;</td>
			<td class=buttonSectionTitles nowrap valign=bottom>&nbsp;commands</td>
			<?php 
		}

		?> <td nowrap>&nbsp;</td><?php
	}
		if($this->numrows){?>
<td align="right" rowspan=2 width="100%" nowrap class="small" valign="bottom"><?php
		
		if ($this->truecount<=$_SESSION["record_limit"]) 
			echo "records:&nbsp;".$this->numrows;
		else {
			
			if($this->recordoffset>0){
				?><a href="#" onClick="document.search.offset.value=<?php echo $this->recordoffset-$_SESSION["record_limit"] ?>;document.search.submit();">&lt;&nbsp;prev</a>&nbsp;<?php
			}
			if(($this->numrows+$this->recordoffset)<$this->truecount){
				?>&nbsp;<a href="#" onClick="document.search.offset.value=<?php echo $this->recordoffset+$_SESSION["record_limit"] ?>;document.search.submit();">&nbsp;next&nbsp;&gt;</a><?php
			}
			?><br><input name="offset" type="hidden" value="">records:
			  <select name="offsetselector" onChange="this.form.offset.value=this.value;this.form.submit();">
			  	<?php
					$displayedoffset=0;
					while($displayedoffset<$this->truecount){
						?><option value="<?php echo $displayedoffset?>" <?php if($displayedoffset==$this->recordoffset) echo "selected";?>><?php echo ($displayedoffset+1)?> - <?php if($displayedoffset+$_SESSION["record_limit"]<$this->truecount) echo ($displayedoffset+$_SESSION["record_limit"]); else echo $this->truecount;?></option><?php
						$displayedoffset+=$_SESSION["record_limit"];
					}
				?>
			  </select> of <?php echo $this->truecount;
		}
	?></td><?php }?>	
		</tr><tr><?php

	//crank the buttons out	
	if($this->tableoptions["new"] or $this->tableoptions["edit"] or $this->thetabledef["deletebutton"]!="NA" ){
		?><td nowrap class="buttonSection"><?php
		if ($this->tableoptions["new"]) {
			?> <input name="command" id="new" type="submit" value="new" class="Buttons" style="width:35px;"><?php
		}
		if($this->numrows) {
			if ($this->tableoptions["edit"]) {
				?><input name="command" id="edit" type="submit" value="edit" disabled="true" class="Buttons" style="width:35px;"><?php
			}
			if($this->thetabledef["deletebutton"] != "NA") {
				?><input name="command" id="delete" type="submit" value="<?php  echo $this->thetabledef["deletebutton"] ?>" disabled="true" class="Buttons"><?php
			}
			?></td><?php
		}
	}
	if($this->numrows) {
		if($this->tableoptions["select"]){?>
			<td nowrap>&nbsp;</td>
			<td nowrap class=buttonSection>
			<input name="na" id="All" type="button" value=" all " class="Buttons" onClick="selectRecords('All');" style="width:35px;"><input name="na" id="None" type="button" value="none" class="Buttons" onClick="selectRecords('None');" style="width:35px;">
			</td>
			
			<td nowrap>&nbsp;</td>
			<td nowrap class=buttonSection>
				<input name="command" id="keep" type="submit" value="keep" class="Buttons" disabled="true" style="width:35px;"><input name="command" id="omit" type="submit" value="omit" class="Buttons" disabled="true" style="width:35px;">
			</td>
			<?php
		}
		if($this->tableoptions["printex"]){?>
			<td nowrap>&nbsp;</td>
			<td nowrap class="buttonSection"><input name="command" type="submit" id="print" disabled="true" value="print" class="Buttons" style="width:65px;"></td>
			<?php
		}
		if($this->tableoptions["othercommands"]){?>			
			<td nowrap>&nbsp;</td>
			<td nowrap class="buttonSection">
				<select name="othercommands" disabled=true onChange="setSelIDs(this.form);this.form.submit();">
				<option value="" selected>choose...</option>
			<?php				
			foreach($this->tableoptions["othercommands"] as $key => $value){
				?><option value="<?php echo $key?>"><?php echo $value?></option><?php
			}
			?></select></td><?php
		}
		?><td nowrap>&nbsp;</td><?php }?></tr></table><?php
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
			echo $therecord[0];
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
	?><div class="recordbottom" style="margin:0px;margin-top:3px;">
	relate to records... <select name="relationship" onChange="setSelIDs(this.form);this.form.submit();"	disabled="true">
		<option value="" selected>choose...</option><?php 
		while($therecord = mysql_fetch_array($queryresult)){
		?>
			<option value="<?php echo $therecord["id"]?>"><?php echo $therecord["name"]?></option>
		<?php
	}
	?></select></div></form>
	<?php
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
			include($this->base."modules/".$this->thetabledef["name"]."/include/".$this->ref."_search_functions.php");
		}

		function issueQuery(){
			$querycolumns="";
			foreach ($this->thecolumns as $therow)
				$querycolumns.=", ".$therow["column"]." as \"".$therow["name"]."\"";
			$querycolumns=substr($querycolumns,2);
						
			$this->therecords=$this->thetabledef["querytable"]." ".$this->queryjoinclause." WHERE ".$this->querywhereclause." ORDER BY ".$this->querysortorder;
			$this->querystatement = "SELECT DISTINCT ".$querycolumns." FROM ".$this->therecords;

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