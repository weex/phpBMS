<?php 

function getMenu(){
	global $dblink;
	
	$querystatement="SELECT id,name,link,accesslevel FROM menu WHERE parentid=0 ORDER BY displayorder";
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(1,"Error Retrieving Menu");
	
	return $queryresult;
}

function getSubItems($parentid){
	global $dblink;

	$querystatement="SELECT id,name,link,accesslevel FROM menu WHERE parentid=".$parentid." ORDER BY displayorder";
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(1,"Error Retrieving Menu");

	if (mysql_num_rows($queryresult)<1) return false; else return $queryresult;
}

	$menus=getMenu();

?>
<script language="JavaScript" src="<?php echo $_SESSION["app_path"]?>common/javascript/menu.js" type="text/javascript" ></script>
<script language="JavaScript" src="<?php echo $_SESSION["app_path"]?>common/javascript/common.js" type="text/javascript" ></script>
<script language="JavaScript" >var spinner=new Image;spinner.src=+"<?php echo $_SESSION["app_path"] ?>common/image/spinner.gif";</script>
<table border="0" cellpadding="0" cellspacing=0 id="menu" width="100%"><tr><td valign="top" align=right  nowrap colspan=<?php echo mysql_num_rows($menus)?>>
	<div id="appname"><a href="<?php echo $_SESSION["app_path"]?><?php echo $_SESSION["default_load_page"]?>"><?php echo $_SESSION["application_name"];?></a></div>
	<div class=small>
	<a href="/click for information/" id="loggedinuser" onClick="showUserInfo('<?php echo $_SESSION["app_path"]?>'); return false;"><?php echo trim($_SESSION["userinfo"]["firstname"]." ".$_SESSION["userinfo"]["lastname"])?></a> 	
	<input name="lo" type="button" class="smallButtons" value="log out" style="margin-left:3px;margin-right:3px;" onClick="document.location=('<?php echo $_SESSION["app_path"]?>logout.php')">
	</div>
</td></tr>
<tr><?php 	
	while($menurecord=mysql_fetch_array($menus)){
		if($_SESSION["userinfo"]["accesslevel"]>$menurecord["accesslevel"]){
			echo "<td valign=top nowrap width=\"".round(100/mysql_num_rows($menus))."%\">";
			if($menurecord["link"]) {
				echo "<div class=mainmenuitems><a href=\"".$_SESSION["app_path"].$menurecord["link"]."\" class=exp1>".$menurecord["name"]."<img src=\"".$_SESSION["app_path"]."common/image/spacer.gif\" width=1 height=1 border=\"0\" ></a></div>";
			}
			else {
				echo "<div class=mainmenuitems onClick=\"expand(".$menurecord["id"]."); return false;\"><a href=\"\" class=exp1>".$menurecord["name"]."<img src=\"".$_SESSION["app_path"]."common/image/left_arrow.gif\" id=\"right".$menurecord["id"]."\" width=10 height=10 border=\"0\" ></a></div><div class=submenuitems id=sub".$menurecord["id"].">";
				$subitemsquery=getSubItems($menurecord["id"]);
				if($subitemsquery){
					while($subrecord=mysql_fetch_array($subitemsquery)){
						if($_SESSION["userinfo"]["accesslevel"]>$subrecord["accesslevel"])
						echo "<a href=\"".$_SESSION["app_path"].$subrecord["link"]."\" class=exp2>&nbsp;".$subrecord["name"]."<img src=\"".$_SESSION["app_path"]."common/image/spacer.gif\" width=1 height=1 border=\"0\" ></a>";
					}//end while
				}//end if
				echo "</div>";
			echo "</td>";					
			}//end if
		}//end if
	}//end while
?></tr>
</table>