<?php
	function get_saved_searches($userid,$tabledefid){
		global $dblink;
		
		$querystring="select id,name,userid from usersearches where tabledefid=".$tabledefid." and type=\"SCH\" and(userid=0 or userid=\"".$userid."\") order by userid, name";
		$thequery = mysql_query($querystring,$dblink) or die (mysql_error()." -- ".$querystring);
		return $thequery;
	}//end function
	
	function display_saved_search_list($thequery){
		$numrows=mysql_num_rows($thequery);
		?>
		<select name="loadsearch" <?php if ($numrows<1) echo "disabled" ?> style="width:330px;">
			<?php if($numrows<1) {?>
				<option value="NA">No Saved Searches</option>
			<?php 
				} else {
					$numglobal=0;
					while($therecord=mysql_fetch_array($thequery))
						if($therecord["userid"]<1) $numglobal++;
					mysql_data_seek($thequery,0);				
			?>			
				<?php if($numglobal>0){ ?>
				<option value="NA">----- global searches -----</option>
				<?PHP
					}//end if
					$userqueryline=true;
					while($therecord=mysql_fetch_array($thequery)){
						if ($therecord["userid"]> 0 and $userqueryline) {
							$userqueryline=false;						
							?><option value="NA">----- user searches ------</option><?php 
						}
						?><option value="<?php echo $therecord["id"]?>"><?php echo $therecord["name"]?></option><?php 
					}// end while
				}//end if
			?>
		</select>
		<?php
	}//end function
	
	function save_search($userid,$tabledefid,$savename,$constructedquery){
		global $dblink;
		
		$thequery="insert into usersearches (userid,tabledefid,name,type,sqlclause) values (";
		$thequery.=$userid.", ";
		$thequery.="\"".$tabledefid."\", ";
		$thequery.="\"".$savename."\", ";
		$thequery.="\"SCH\", ";		
		$thequery.="\"".$constructedquery."\")";
		$query = mysql_query($thequery,$dblink) or die (mysql_error());
	}//enf function
	
	function delete_search($userid,$loadsearch){
		if ($loadsearch != "NA"){
			$thequery="DELETE FROM usersearches where id=".$loadsearch." and userid=".$userid;		
			$query = mysql_query($thequery) or die (mysql_error());
		}
	}// enf function
	
	function load_search($savedsearchid){
		if ($savedsearchid != "NA"){
			$thequery="SELECT sqlclause FROM usersearches WHERE id=".$savedsearchid;		
			$query = mysql_query($thequery) or die (mysql_error());
			$therecord = mysql_fetch_array($query);
			return $therecord["sqlclause"];
		} else return "";
	}// end function
?>