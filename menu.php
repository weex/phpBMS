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
	$nummenus=mysql_num_rows($menus);

?><script language="JavaScript" src="<?php echo $_SESSION["app_path"]?>common/javascript/menu.js" type="text/javascript" ></script>
<script language="JavaScript" src="<?php echo $_SESSION["app_path"]?>common/javascript/common.js" type="text/javascript" ></script>
<script language="JavaScript" >
	var spinner=new Image;spinner.src=+"<?php echo $_SESSION["app_path"] ?>common/image/spinner.gif";
	var upArrow=new Image;upArrow.src="<?php echo $_SESSION["app_path"] ?>common/image/up_arrow.gif";
        var downArrow=new Image;downArrow.src="<?php echo $_SESSION["app_path"] ?>common/image/down_arrow.gif";
</script>
<div id="menu">
	<div id="menuLogout" class="small">
		<a href="/click for information/"  name="toptop" id="loggedinuser" onClick="showUserInfo('<?php echo $_SESSION["app_path"]?>'); return false;"><?php echo trim($_SESSION["userinfo"]["firstname"]." ".$_SESSION["userinfo"]["lastname"])?></a> 	
		<input id="lo" name="lo" type="button" class="smallButtons" value="log out" style="margin-left:3px;margin-right:3px;" onClick="document.location=('<?php echo $_SESSION["app_path"]?>logout.php')">
		<input id="help" name="help" type="button" class="smallButtons" value="?" style="" onClick="showHelp('<?php echo $_SESSION["app_path"]?>')">
	</div>
	<div id="menuAppname"><a href="<?php echo $_SESSION["app_path"]?><?php echo $_SESSION["default_load_page"]?>"><?php echo $_SESSION["application_name"];?></a></div>
	<div id="menuBar" style="clear:both;">
	<?php 	
		$submenustring="";
		while($menurecord=mysql_fetch_array($menus)){
			if($_SESSION["userinfo"]["accesslevel"]>=$menurecord["accesslevel"]){
				if($menurecord["link"]) {
					if(strpos($menurecord["link"],"http")!==0)
						$menurecord["link"]=$_SESSION["app_path"].$menurecord["link"];
					?><a href="<?php echo $menurecord["link"]?>"><?php echo $menurecord["name"]?></a><?php 
				}
				else { ?><a href=""  id="menu<?php echo $menurecord["id"]?>"  onClick="expandMenu(this);return false;"  onMouseOver="checkExpand(this)"><?php echo $menurecord["name"]; ?>&nbsp;<img src="<?php echo $_SESSION["app_path"]?>common/image/down_arrow.gif" id="menuImage<?php echo $menurecord["id"]?>" width=10 height=10 border="0" ></a><div class="submenuitems" style="display:none;" id="submenu<?php echo $menurecord["id"]?>"><?php 
					$submenustring.=$menurecord["id"].",";
					$subitemsquery=getSubItems($menurecord["id"]);
					if($subitemsquery){
						while($subrecord=mysql_fetch_array($subitemsquery)){
							if($_SESSION["userinfo"]["accesslevel"]>=$subrecord["accesslevel"]){
								if(strpos($subrecord["link"],"http")!==0)
									$subrecord["link"]=$_SESSION["app_path"].$subrecord["link"];
							?><a href="<?php echo $subrecord["link"]?>">&nbsp;<?php echo $subrecord["name"] ?></a><?php }//end if
						}//end while
					}//end if
					echo "</div>";
				}//end if
			}//end if
		}//end while
		$submenustring=substr($submenustring,0,strlen($submenustring)-1);
	?></div></div><script language="JavaScript">var subMenuArray="<?php echo $submenustring ?>".split(",");</script>
<?PHP if (isset($statusmessage)) {?>
<div id="statusmessage"><?PHP echo $statusmessage ?></div>
<?PHP } // end if ?>
	
