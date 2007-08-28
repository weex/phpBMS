<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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

	//set mysql encoding to latin1 (fpdf doesn't like utf)
	$db->setEncoding("latin1");

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
		$thequery=$db->query($reportquerystatement);
		if(!$thequery) die("No records, or invlaid SQL statement:<br />".$reportquerystatement);
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
		
		while($therecord=$db->fetchArray($thequery)) {	
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
	
	$pageTitle = "Label Options";
	$phpbms->showMenu = false;
	$phpbms->cssIncludes[] = "pages/historyreports.css";
	include("header.php");
	
?>

<form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" name="print_form">
<div class="bodyline" id="reportOptions">
	<h1 id="topTitle"><span>Label Options</span></h1>
	
		<p>
			skip first labels<br />
			<input name="skiplabels" value="0" size="3" maxlength="3" />
		</p>
		<p align="right">
			<input name="command" type="submit" class="Buttons" id="print" value="print" />
			<input name="cancel" type="button" class="Buttons" id="cancel" value="canel" onclick="window.close();" />
		</p>
</div>
</form>
<?php include("footer.php"); }//end if ?>