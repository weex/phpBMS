<?php
//=================================================
//Most Common Functions of the Application go here.
//=================================================

//Create Tabs
//===================================
function create_tabs($tabarray,$selected="none") {
	?>
	<TABLE cellspacing=0 cellpadding=0 border=0><TR>
	<?php
	foreach($tabarray as $theitem){
		if(!isset($theitem["disabled"])) $theitem["disabled"]=false;
		if(!isset($theitem["notify"])) $theitem["notify"]=false;
		
		if ($theitem["name"]==$selected) $theclass="tabselected"; else $theclass="tabs";
		
		?><TD class="tabbottoms">&nbsp;</TD><TD class="<?php echo $theclass?>" align="center" nowrap><?php

		if ($theitem["name"]==$selected || $theitem["disabled"]) echo $theitem["name"];
		else echo "<a href=\"".$theitem["href"]."\">".$theitem["name"]."</a>";
		if ($theitem["notify"]) {
		?>&nbsp;<img src="<?php echo $_SESSION["app_path"]?>common/image/notify.gif" alt="*" align="middle" width="10" height="10" border="0"><?php
		}
		?></td><?php 
	}
	?>
<TD width="100%" class="tabbottoms">&nbsp;</td></TR></TABLE>
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
	return $tempinfo["name"];
}
?>