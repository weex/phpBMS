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

	// The label report requires the following variables to be set before creating the PDF
	//maxrows
	//maxcolumns
	//columnmargin
	//labelheight
	//labelwidth
	//top_start
	//left_start
	//border debug
	//reportquerystatement	
	if(!isset($border_debug)) $border_debug=0;
	
	//Also, it requires the definition of the function printLabel($pdf,$therecord,$thex,$they,$border_debug) which prints the actual 
	//contectens of the label
	
	if(isset($_POST["command"])) {
	
		if(!is_numeric($_POST["skiplabels"])) $_POST["skiplabels"]=0;
		if ($_POST["skiplabels"]>$maxrows*$maxcolumns) $_POST["skiplabels"]=0;
			
		
	
		//Generate the invoice Query
		$reportquerystatement.=$_SESSION["printing"]["whereclause"].$sortorder;
		$thequery=mysql_query($reportquerystatement,$dblink);
		if(!$thequery) die("No records, or invlaid SQL statement:<BR>".$reportquerystatement);
		//===================================================================================================
		// Generating PDF File.
		//===================================================================================================
		
		//define the documents and margins
		$pdf=new FPDF("P","in","Letter");
		$pdf->Open();
		$pdf->SetMargins(0,0);
	
		$pdf->AddPage();	
		
		$thex=$leftstart;
		$they=$topstart;
		$rowcount=1;
		$totalcount=1;
		$column=1;
		//this first while skips records

		while($totalcount<=$_POST["skiplabels"]){
			if($rowcount>$maxrows) {
			   $column++;
			   $they=$topstart;
			   $thex+=$labelwidth+$columnmargin;
			   $rowcount=1;
			}
			$they+=$labelheight;
			$rowcount++;
			$totalcount++;
		}
		
		while($therecord=mysql_fetch_array($thequery)) {	
			if($rowcount>$maxrows) {
			   $column++;
			   $they=$topstart;
			   $thex+=$labelwidth+$columnmargin;
			   $rowcount=1;
			}
			if($column>$maxcolumns){
				$pdf->AddPage();				
				$thex=$leftstart;
				$they=$topstart;
				$rowcount=1;
				$column=1;
			}
			$pdf=printLabel($pdf,$therecord,$thex,$they,$border_debug);
			
			$they+=$labelheight;
			$rowcount++;
		}// end fetch_array while loop
			
		$pdf->Output();
		exit();
	} else {
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Label Options</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("../head.php")?>
</head>
<body>

<div class="bodyline" style="width:550px;">
	<h1>Label Options</h1>
	
	<form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" name="print">
		<div class="box">
			skip first labels<br>
			<input name="skiplabels" value="0" size="3" maxlength="3">
		</div>
		<div align="right" class="box">
			<input name="command" type="submit" class="Buttons" id="print" value="print" style="width:75px;margin-right:3px;">
			<input name="cancel" type="button" class="Buttons" id="cancel" value="canel" style="width:75px;" onClick="window.close();">	 
		</div>
   </form>
</div>
</body>
</html>
<?php }//end if ?>