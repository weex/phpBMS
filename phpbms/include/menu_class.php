<?php
/*
 $Rev: 254 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-07 18:38:38 -0600 (Tue, 07 Aug 2007) $
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2007, Kreotek LLC                                  |
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

class topMenu{
	
	var $db;
	
	function topMenu($db){
		$this->db = $db;

		$querystatement = "SELECT id,name,link,roleid FROM menu WHERE parentid=0 ORDER BY displayorder";
		$this->menuresult = $this->db->query($querystatement);	

	}


	function getSubItems($parentid){
		$querystatement="SELECT id,name,link,roleid FROM menu WHERE parentid=".$parentid." ORDER BY displayorder";
		$queryresult=$this->db->query($querystatement);
	
		if ($this->db->numRows($queryresult)<1) return false; else return $queryresult;
	}

	
	function display(){
		
?>
<div id="menu">
	<h1><a href="<?php echo APP_PATH.DEFAULT_LOAD_PAGE ?>" title="<?php echo htmlQuotes(APPLICATION_NAME);?>" name="toptop"><span><?php echo APPLICATION_NAME;?></span></a></h1>

	<div id="menuRighthand"><?php echo htmlQuotes(trim($_SESSION["userinfo"]["firstname"]." ".$_SESSION["userinfo"]["lastname"]))?>	</div>
		
	<ul id="menuBar">
	<?php 	
		$submenustring="";
		
		while($menurecord = $this->db->fetchArray($this->menuresult)){

			if(hasRights($menurecord["roleid"])){
				if($menurecord["link"]) {
					if(strpos($menurecord["link"],"http")!==0 && strpos($menurecord["link"],"javascript")!==0)
						$menurecord["link"]=APP_PATH.$menurecord["link"];						
					?><li><a href="<?php echo $menurecord["link"]?>"><?php echo $menurecord["name"]?></a></li><?php 
				} else { 
				
				?><li><a href="#toptop" class="topMenus" id="menu<?php echo $menurecord["id"]?>"><?php echo $menurecord["name"]; ?></a></li><li class="submenusli"><ul class="submenuitems" id="submenu<?php echo $menurecord["id"]?>"><?php 
										
					$subitemsquery = $this->getSubItems($menurecord["id"]);
					
					if($subitemsquery){
						$sep=false;
						
						while($subrecord=$this->db->fetchArray($subitemsquery)){
							if($subrecord["name"]=="----")
								$sep=true;
							else{
								if(hasRights($subrecord["roleid"])){
									if(strpos($subrecord["link"],"http")!==0 && strpos($subrecord["link"],"javascript")!==0)
										$subrecord["link"]=APP_PATH.$subrecord["link"];
									if(strpos($subrecord["link"],"javascript")===0)
										$subrecord["link"]="#\" onclick=\"".str_replace("javascript:","",$subrecord["link"]);	
								?><li <?php if($sep) echo " class=\"menuSep\" "?>><a href="<?php echo $subrecord["link"]?>">&nbsp;<?php echo $subrecord["name"] ?></a></li><?php 
									$sep=false;
								}//end if
							}//end if
						}//end while
						
					}//end if
					?></ul></li><?php ;

				}//end if

			}//end if

		}//end while
	?></ul></div><?php

	}//end method
}// end class

?>