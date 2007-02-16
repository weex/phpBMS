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

	require("../../../include/session.php");
	//turn debug borders on to troubleshoot PDF creation (1 or 0)
	$border_debug=0;
	
	require("../../../fpdf/fpdf.php");
	
	if($_SESSION["printing"]["sortorder"])
		$sortorder=$_SESSION["printing"]["sortorder"];
	else
		$sortorder=" ORDER BY notes.creationdate DESC";

	//Generate the notes Query
	$querystatement="SELECT users.firstname, users.lastname, notes.id, date_Format(notes.creationdate,\"%c/%e/%Y %T\") as thecreationdate,
						notes.subject,notes.content 
						FROM notes INNER JOIN users on notes.createdby=users.id ".$_SESSION["printing"]["whereclause"].$sortorder;
	$thequery=mysql_query($querystatement,$dblink);
	if(!$thequery) die("No records, or invlaid SQL statement:<br />".$querystatement);
	//===================================================================================================
	// Generating PDF File.
	//===================================================================================================
	
	$leftmargin=.5;
	$rightmargin=.5;
	$topmargin=.75;
	$paperwidth=8.5;
	$paperlength=11;
	
	//define the documents and margins
	$pdf=new FPDF("P","in","Letter");
	$pdf->SetMargins($leftmargin,$topmargin,$rightmargin);
	$pdf->Open();
	
	$pdf->AddPage();
	
	$tempwidth=$paperwidth-$leftmargin-$rightmargin;
	$tempheight=.25;
	$pdf->SetXY($leftmargin,$topmargin);
	
	//Report Title
	$pdf->SetFont("Arial","B",16);
	$pdf->Cell($tempwidth,.25,"Notes Summary",$border_debug,1,"L");
	$pdf->SetFont("Arial","B",12);
	$pdf->Cell($tempwidth,.18,"Date Created: ".date("m/d/Y"),$border_debug,1,"L");
	$pdf->SetLineWidth(.04);
	$pdf->Line($leftmargin,$pdf->GetY(),$paperwidth-$rightmargin,$pdf->GetY());
	
	$pdf->SetY($topmargin+.43+.1);
	$pdf->SetLineWidth(.01);
	while($therecord=mysql_fetch_array($thequery)) {
		$pdf->SetFont("Arial","",9);
		$pdf->SetX($leftmargin+.125);
		$pdf->Cell($tempwidth-.5,.17,"ID: ".$therecord["id"],$border_debug,1,"L");

		$pdf->SetX($leftmargin+.125);
		$pdf->Cell($tempwidth-.5,.17,"Created: ".$therecord["firstname"]." ".$therecord["lastname"]." ".$therecord["thecreationdate"],$border_debug,1,"L");

		$pdf->SetFont("Arial","B",11);
		$pdf->SetXY($leftmargin+.25,$pdf->GetY()+.04);
		$pdf->Cell($tempwidth-.5,.19,$therecord["subject"],$border_debug,1,"L");
		$pdf->Line($leftmargin+.25,$pdf->GetY(),$paperwidth-$rightmargin-.25,$pdf->GetY());

		$pdf->SetX($leftmargin+.25);
		$pdf->SetFont("Arial","",8);
		$pdf->MultiCell($tempwidth-.5,.14,$therecord["content"],$border_debug,1,"L");
		$pdf->Line($leftmargin+.25,$pdf->GetY(),$paperwidth-$rightmargin-.25,$pdf->GetY());
		$pdf->SetY($pdf->GetY()+.25);
	}// end fetch_array while loop

	$pdf->Output();
	exit();
?>