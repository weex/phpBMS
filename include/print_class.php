<?php
	class printer{
		var $tableid;
		var $theids;
		var $reports;
		var $maintable;
		var $openwindows="";
		var $savedSearches;
		var $savedSorts;
		
		function initialize($tableid,$theids){
			global $dblink;

			$this->tableid=$tableid;
			$this->theids=$theids;
			
			$querystatement="SELECT maintable FROM tabledefs 
							WHERE id=".$this->tableid.";";
			$queryresult=mysql_query($querystatement,$dblink);		
			if(!$queryresult) reportError(500,"Error retreving table info.");
			$therecord=mysql_fetch_array($queryresult);
			$this->maintable=$therecord["maintable"];

			$querystatement="SELECT id,name,reportfile,type,description,displayorder FROM reports 
							WHERE tabledefid=0 or tabledefid=".$this->tableid." and accesslevel <= ".$_SESSION["userinfo"]["accesslevel"]." ORDER BY tabledefid desc, displayorder desc,name";
			$queryresult=mysql_query($querystatement,$dblink);		
			if(!$queryresult) reportError(500,"Error retreving reports.");
			$this->reports=$queryresult;
			
			$this->savedSearches=$this->getSaved($_SESSION["userinfo"]["id"],"SCH");
			$this->savedSorts=$this->getSaved($_SESSION["userinfo"]["id"],"SRT");
		}
		
		function saveVariables(){
			$_SESSION["printing"]["tableid"]=$this->tableid;
			$_SESSION["printing"]["maintable"]=$this->maintable;
			$_SESSION["printing"]["theids"]=$this->theids;
		}
		

		function getSaved($userid,$type){
			global $dblink;
			
			$querystring="SELECT id,name,userid FROM usersearches WHERE tabledefid=".$this->tableid." and type=\"".$type."\" and((userid=0 and accesslevel<=".$_SESSION["userinfo"]["accesslevel"].") or userid=\"".$userid."\") order by userid,name";
			$thequery = mysql_query($querystring,$dblink);
			return $thequery;
		}//end function


		function  donePrinting($backurl){
			if(!$backurl)
				header("Location: search.php?id=".$this->tableid);
			else
				header("Location: ".$backurl);
		}
		
		function showJavaScriptArray(){
			mysql_data_seek($this->reports,0);

			echo "<script language=\"JavaScript\">\n";
			while($therecord=mysql_fetch_array($this->reports)){
				echo "theReport[theReport.length]=new Array(".$therecord["id"].",\"".$therecord["reportfile"]."\",\"".$therecord["name"]."\",\"".$therecord["type"]."\",\"".addcslashes(addslashes($therecord["description"]),"\n,\r")."\");\n";
			 }	 
			echo "</script>\n";
			
		}
		
		function displayReportList(){
			mysql_data_seek($this->reports,0);
		?>
	   <select name="choosereport[]" id="choosereport" size="12" multiple style="width:205px;" onChange="switchReport(this)">
	    <?PHP
		 $diplayorder=-1;
	   	 while($therecord=mysql_fetch_array($this->reports)){
		 	if ($displayorder!=$therecord["displayorder"]){
				if($displayorder>0)
				 	echo "<OPTION value=\"\">----------------------------------------------------------------</option>\n";
				$displayorder=$therecord["displayorder"];
			}
		 	echo "<OPTION value=\"".$therecord["id"]."\">".$therecord["name"]."</option>\n";
		 }
	   ?>
	   </select>
	   <script>var thechoice=getObjectFromID("choosereport");thechoice.focus();thechoice.options[0].selected=true;</script>
		<?php
		}

	function showSaved($thequery,$selectname){
		$numrows=mysql_num_rows($thequery);
		?>
		<select name="<?php echo $selectname?>" <?php if ($numrows<1) echo "disabled" ?> style="width:100%;">
			<?php if($numrows<1) {?>
				<option value="NA">None Saved</option>
			<?php 
				} else {
					$numglobal=0;
					while($therecord=mysql_fetch_array($thequery))
						if($therecord["userid"]<1) $numglobal++;
					mysql_data_seek($thequery,0);				
			?>			
				<?php if($numglobal>0){ ?>
				<option value="NA">----- global -----</option>
				<?PHP
					}//end if
					$userqueryline=true;
					while($therecord=mysql_fetch_array($thequery)){
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
		global $dblink;

		//Grab query for all columns (for sort purposes)
		$querystatement="SELECT * FROM ".$this->maintable." LIMIT 1";
		$queryresult = mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(500,"Cannot retrieve Table Information");
		$numfields = mysql_num_fields($queryresult);
		for ($i=0;$i<$numfields;$i++) $fieldlist[]=mysql_field_name($queryresult,$i);

		?>
		<select name="singlefield" onChange="checkForCustom(this.value)">
			<?php 
				foreach($fieldlist as $field){
					echo "<option value=\"".$field."\"";
					if($field=="id") echo "selected";
					echo ">".$field."</option>\n";
				}
			?>
			<option value="**CUSTOM**" class="important">custom SQL</option>
		</select>
		
		<?php		
	}

}//end class

?>