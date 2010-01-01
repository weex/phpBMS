<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
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
	require ("include/session.php");

	function isUnique($tablename,$column,$value,$excludeid,$db){
		
		$thereturn=false;
		
		$querystatement="SELECT count(id) AS thecount FROM ".$tablename." WHERE ".$column."=\"".$value."\" AND id!=".$excludeid;
		$queryresult=$db->query($querystatement);
		if($queryresult){
			$therecord=$db->fetchArray($queryresult);
			if($therecord["thecount"]==0)
				$thereturn=true;
		}
		
		return $thereturn;
	}
	
	
	$isunique=false;
	
	if(isset($_GET["tdid"]) && isset($_GET["c"]) && isset($_GET["val"]) && isset($_GET["xid"])) {
		$_GET["tdid"]=((int) $_GET["tdid"]);
		$_GET["xid"]=((int) $_GET["xid"]);
		
		$querystatement="SELECT maintable FROM tabledefs WHERE id=".$_GET["tdid"];
		$queryresult=$db->query($querystatement);
		if($queryresult)
			if($therecord=$db->fetchArray($queryresult))
				$isunique=isUnique($therecord["maintable"],$_GET["c"],$_GET["val"],$_GET["xid"],$db);		
	}
	
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>';
?>
<response>
  <isunique><?php echo ((int) $isunique) ?></isunique>
</response>