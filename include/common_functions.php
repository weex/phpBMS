<?php
//=================================================
//Most Common Functions of the Application go here.
//=================================================

//Create Tabs
//===================================
function create_tabs($tabarray,$selected="none") {
	?>
	<table cellspacing=0 cellpadding=0 border=0><tr>
	<?php
	foreach($tabarray as $theitem){
		if(!isset($theitem["disabled"])) $theitem["disabled"]=false;
		if(!isset($theitem["notify"])) $theitem["notify"]=false;
		
		if ($theitem["name"]==$selected) $theclass="tabselected"; else $theclass="tabs";
		
		?><td class="tabbottoms">&nbsp;</td><td class="<?php echo $theclass?>" align="center" nowrap><?php

		if ($theitem["name"]==$selected || $theitem["disabled"]) echo $theitem["name"];
		else echo "<a href=\"".$theitem["href"]."\">".$theitem["name"]."</a>";
		if ($theitem["notify"]) {
		?><img src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-note.png" alt="*" align="absmiddle" width="16" height="16" border="0"><?php
		}
		?></td><?php 
	}
	?>
<td width="100%" class="tabbottoms">&nbsp;</td></tr></table>
	<?php
}

// Clean our temp PDF report files
//====================================================================
function clean_pdf_reports($dir,$sectime=3600)
{
    //Delete temporary files
    $t=time();
    $h=opendir($dir);
    while($file=readdir($h))
    {
        if(substr($file,0,3)=='tmp' and substr($file,-4)=='.pdf')
        {
            $path=$dir.'/'.$file;
            if($t-filemtime($path)>$sectime)
                @unlink($path);
        }
    }
    closedir($h);
}

//=====================================================================
function getUserName($id=0){
	global $dblink;
	if($id=="") $id=0;
	$thequerystatment="select concat(firstname,\" \",lastname) as name from users where id=".$id;
	$thequery = mysql_query($thequerystatment,$dblink);
	$tempinfo = mysql_fetch_array($thequery);
	return trim($tempinfo["name"]);
}

// This Function prepares a date for insertion/updaing into a SQL statement
// It will add the quotes for you.
//=====================================================================
function formatToSQLDate($thedate,$allownull=true){
	if($thedate=="" || $thedate=="0/0/0000"){
		if ($allownull)
			$tempdate="NULL";
		else
			$tempdate="\"0000-00-00\"";
	} else{
		$thedate="/".ereg_replace(",.","/",$thedate);
		$temparray=explode("/",$thedate);
		$tempdate="\"".$temparray[3]."-".$temparray[1]."-".$temparray[2]."\"";
	}//end if
	return $tempdate;
}//end function

function formatToSQLTime($thetime,$allownull=true){
	if($thetime=="" || $thetime=="0:00"){
		if ($allownull)
			$temptime="NULL";
		else
			$temptime="\"00:00:00\"";
	} else{
		$temptime="\"".$thetime."\"";
	}	
	return $temptime;
}

function dateFromSQLDate($sqlDate){
	$thedate="";
	$temparray=explode("-",$sqlDate);
	if(count($temparray)>1)
		$thedate=mktime(0,0,0,$temparray[1],$temparray[2],$temparray[0]);
	return $thedate;
}
?>