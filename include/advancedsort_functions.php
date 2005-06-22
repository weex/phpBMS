<?php
	function get_saved_sorts($userid,$tabledefid){
		global $dblink;
		
		$querystring="select id,name,userid from usersearches where tabledefid=".$tabledefid." and type=\"SRT\" and(userid=0 or userid=\"".$userid."\") order by userid, name";
		$thequery = mysql_query($querystring,$dblink) or die (mysql_error()." -- ".$querystring);
		return $thequery;
	}//end function
	
	function display_saved_sort_list($thequery){
		$numrows=mysql_num_rows($thequery);
		?>
		<select name="loadsearch" <?php if ($numrows<1) echo "disabled" ?> style="width:330px;">
			<?php if($numrows<1) {?>
				<option value="NA">No Saved Sorts</option>
			<?php 
				} else {
					$numglobal=0;
					while($therecord=mysql_fetch_array($thequery))
						if($therecord["userid"]<1) $numglobal++;
					mysql_data_seek($thequery,0);				
			?>			
				<?php if($numglobal>0){ ?>
				<option value="NA">----- global sorts -----</option>
				<?PHP
					}//end if
					$userqueryline=true;
					while($therecord=mysql_fetch_array($thequery)){
						if ($therecord["userid"]> 0 and $userqueryline) {
							$userqueryline=false;						
							?><option value="NA">----- user sorts ------</option><?php 
						}
						?><option value="<?php echo $therecord["id"]?>"><?php echo $therecord["name"]?></option><?php 
					}// end while
				}//end if
			?>
		</select>
		<?php
	}//end function
	
	function save_sort($userid,$tabledefid,$savename,$constructedquery){
		global $dblink;
		
		$thequery="insert into usersearches (userid,tabledefid,name,type,sqlclause) values (";
		$thequery.=$userid.", ";
		$thequery.="\"".$tabledefid."\", ";
		$thequery.="\"".$savename."\", ";
		$thequery.="\"SRT\", ";		
		$thequery.="\"".$constructedquery."\")";
		$query = mysql_query($thequery,$dblink) or die (mysql_error());
	}//enf function
	
	function delete_sort($userid,$loadsort){
		if ($loadsort != "NA"){
			$thequery="DELETE FROM usersearches where id=".$loadsort." and userid=".$userid;		
			$query = mysql_query($thequery) or die (mysql_error());
		}
	}// enf function
	
	function load_sort($savedsortid){
		if ($savedsortid != "NA"){
			$thequery="SELECT sqlclause FROM usersearches WHERE id=".$savedsortid;		
			$query = mysql_query($thequery) or die (mysql_error());
			$therecord = mysql_fetch_array($query);
			return $therecord["sqlclause"];
		} else return "";
	}// end function
?>