<?php 
/*
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
<div id="menu" >
	<h1><a href="<?php echo $_SESSION["app_path"]?><?php echo $_SESSION["default_load_page"]?>" title="<?php echo htmlQuotes($_SESSION["application_name"]);?>"><span><?php echo $_SESSION["application_name"];?></span></a></h1>

	<div id="menuRighthandButtons">
		<a href="/click for information/"  name="toptop" id="loggedinuser" onClick="showUserInfo('<?php echo $_SESSION["app_path"]?>'); return false;"><?php echo trim($_SESSION["userinfo"]["firstname"]." ".$_SESSION["userinfo"]["lastname"])?></a>		
		<button name="menuLogout" type="button" onClick="document.location=('<?php echo $_SESSION["app_path"]?>logout.php')" title="log out" class="smallButtons">log out</button>
		<button name="menuHelp" type="button" onClick="showHelp('<?php echo $_SESSION["app_path"]?>')" title="Help" class="smallButtons">?</button>
	</div>
		
	<ul id="menuBar">
	<?php 	
		$submenustring="";
		while($menurecord=mysql_fetch_array($menus)){
			if($_SESSION["userinfo"]["accesslevel"]>=$menurecord["accesslevel"]){
				if($menurecord["link"]) {
					if(strpos($menurecord["link"],"http")!==0)
						$menurecord["link"]=$_SESSION["app_path"].$menurecord["link"];
					?><li><a href="<?php echo $menurecord["link"]?>"><?php echo $menurecord["name"]?></a></li><?php 
				}
				else { ?><li><a href=""  id="menu<?php echo $menurecord["id"]?>"  onClick="expandMenu(this);return false;"  onMouseOver="checkExpand(this)"><?php echo $menurecord["name"]; ?></a></li><ul class="submenuitems" style="display:none;" id="submenu<?php echo $menurecord["id"]?>"><?php 
					$submenustring.=$menurecord["id"].",";
					$subitemsquery=getSubItems($menurecord["id"]);
					if($subitemsquery){
						while($subrecord=mysql_fetch_array($subitemsquery)){
							if($_SESSION["userinfo"]["accesslevel"]>=$subrecord["accesslevel"]){
								if(strpos($subrecord["link"],"http")!==0)
									$subrecord["link"]=$_SESSION["app_path"].$subrecord["link"];
							?><li><a href="<?php echo $subrecord["link"]?>">&nbsp;<?php echo $subrecord["name"] ?></a></li><?php }//end if
						}//end while
					}//end if
					echo "</ul>";
				}//end if
			}//end if
		}//end while
		$submenustring=substr($submenustring,0,strlen($submenustring)-1);
	?></ul></div><script language="JavaScript">var subMenuArray="<?php echo $submenustring ?>".split(",");</script>
<?PHP if (isset($statusmessage)) {?>
<div id="statusmessage"><?PHP echo $statusmessage ?></div>
<?PHP } // end if ?>
	
