<?php
	function showSavedSearches($selected){
		global $dblink;
		
		$querystatment="SELECT id,name,userid FROM usersearches WHERE tabledefid=2 and type=\"SCH\" and(userid=0 or userid=\"".$_SESSION["userinfo"]["id"]."\") order by userid";
		$thequery = mysql_query($querystatment,$dblink);

		$numrows=mysql_num_rows($thequery);
		?>
		<select name="savedsearches" <?php if ($numrows<1) echo "disabled" ?> style="width:100%;">
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
						?><option value="<?php echo $therecord["id"]?>" <?php if($therecord["id"]==$selected) echo "selected"?>><?php echo $therecord["name"]?></option><?php 
					}// end while
				}//end if
			?>
		</select>
		<?php
	}
	
	
	function showClientFields(){
		global $dblink;
		
		$querystatement="describe clients";		
		$queryresult=mysql_query($querystatement,$dblink);
		?><select name="choosefield" id="choosefield">
		<?php 
			while($therecord=mysql_fetch_array($queryresult))
				echo "<option value=\"".$therecord["Field"]."\">".$therecord["Field"]."</option>";
		?>
		</select><?php
	}
	
	
	function showSavedProjects(){
		global $dblink;

		global $dblink;
		
		$querystatement="SELECT id,name,userid FROM clientemailprojects WHERE userid=0 or userid=\"".$_SESSION["userinfo"]["id"]."\" order by userid";
		$thequery = mysql_query($querystatement,$dblink);
		if(!$thequery) reportError(300,$querystatement);

		$numrows=mysql_num_rows($thequery);
		?>
		<select name="savedprojects" <?php if ($numrows<1) echo "disabled" ?> style="width:100%;">
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
	}
	
	
	function saveProject($variables){
		global $dblink;
		
		$sqlstatement=	"INSERT INTO clientemailprojects (name,userid,emailto,emailfrom,subject,body) VALUES (";
		$sqlstatement.=	"\"".$variables["savename"]."\", ";
		$sqlstatement.=	$_SESSION["userinfo"]["id"].", ";
		if($variables["therecords"]=="savedsearch")
			$sqlstatement.=	"\"".$variables["savedsearches"]."\", ";
		else	
			$sqlstatement.=	"\"".$variables["therecords"]."\", ";
		if(!$variables["email"])
			$sqlstatement.=	"\"".$variables["ds-email"]."\", ";
		else
			$sqlstatement.=	"\"".$variables["email"]."\", ";
		$sqlstatement.=	"\"".$variables["subject"]."\", ";
		$sqlstatement.=	"\"".$variables["body"]."\") ";

		mysql_query($sqlstatement,$dblink);
		return mysql_insert_id($dblink);
	}
	
	function loadProject($id){
		global $dblink;
		
		$sqlstatement="SELECT id,name,emailto,emailfrom,subject,body FROM clientemailprojects WHERE id=".$id;
		$queryresult=mysql_query($sqlstatement,$dblink);
		return mysql_fetch_array($queryresult);
		
	}

	function deleteProject($id){
		global $dblink;
		
		$sqlstatement="DELETE FROM clientemailprojects WHERE id=".$id." and userid=".$_SESSION["userinfo"]["id"];
		$queryresult=mysql_query($sqlstatement,$dblink);
		
	}
?>