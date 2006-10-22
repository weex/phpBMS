<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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
	require("include/session.php");
	require("include/common_functions.php");

	function getAutofillXML($what,$displayfield,$xtrafield,$tabledefid,$thefield,$whereclause){
		global $dblink;
		
		$tblquerystatement="SELECT maintable FROM tabledefs WHERE id=".$tabledefid;
		$queryresult=mysql_query($tblquerystatement,$dblink);
		$therecord=mysql_fetch_array($queryresult);
		
		$querystatement="SELECT ".stripslashes($displayfield)." as display,
						".stripslashes($xtrafield)." as xtra ";
		if($thefield)
			$querystatement.=", ".$thefield." as thefield";			
		$querystatement.=" FROM ".$therecord["maintable"]." WHERE ";
		if($whereclause)
			$querystatement.=" (".stripslashes($whereclause).") AND ";
		$querystatement.=stripslashes($displayfield)." ";
		if($thefield)
				$querystatement.="=\"".$what."\"";
		else 
				$querystatement.="LIKE \"".$what."%\"";
		$querystatement.="ORDER BY ".stripslashes($displayfield);
		if($thefield)
			$querystatement.=" LIMIT 1";			
		else
			$querystatement.=" LIMIT 10";			
			
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult){
			echo $querystatement;
			$numrows=0;die();}
		else {
			$numrows=mysql_num_rows($queryresult);
		}
				
		header('Content-Type: text/xml');
		?>
<?php echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'; ?>
<response>
<numrec><?php echo $numrows ?></numrec>
	<?php if($numrows){
		while($therecord=mysql_fetch_array($queryresult)) {
	?><rec>
<fld>display</fld>
<val><?php echo xmlEncode($therecord["display"])?></val>
<fld>extra</fld>
<val><?php echo xmlEncode($therecord["xtra"])?></val><?php
	if(isset($therecord["thefield"])) {
	?><fld>thefield</fld><val><?php echo xmlEncode($therecord["thefield"])?></val><?php
	}
?></rec><?php } //end while
		}//end if
	?></response><?php 
	}//end function

	if(isset($_GET["l"])){
		$_GET=addSlashesToArray($_GET);
		if(!isset($_GET["gf"]))$_GET["gf"]="";
		if(!isset($_GET["wc"]))$_GET["wc"]="";
		getAutofillXML($_GET["l"],$_GET["fl"],$_GET["xt"],$_GET["tid"],$_GET["gf"],$_GET["wc"]);
	}
?>