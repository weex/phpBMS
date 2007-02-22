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

	session_cache_limiter('private');
	require("../../../include/session.php");
	//turn debug borders on to troubleshoot PDF creation (1 or 0)
	$border_debug=0;
	
	require("../../../fpdf/fpdf.php");
	
	if($_SESSION["printing"]["sortorder"])
		$sortorder=$_SESSION["printing"]["sortorder"];
	else
		$sortorder=" ORDER BY concat(clients.lastname,clients.firstname,clients.company)";

	//Generate the Query
	$querystatement="SELECT if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as thename,clients.id 
						FROM clients ".$_SESSION["printing"]["whereclause"].$sortorder;
	$clientquery=mysql_query($querystatement,$dblink);
	if(!$clientquery) reportError(100,"Client Query Could not be executed");
	
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
	$pdf->Cell($tempwidth,.25,"Clients Notes Summary",$border_debug,1,"L");
	$pdf->SetFont("Arial","",12);
	$pdf->Cell($tempwidth,.18,"Date Created: ".dateToString(mktime()),$border_debug,1,"L");
	$pdf->SetLineWidth(.04);
	$pdf->Line($leftmargin,$pdf->GetY(),$paperwidth-$rightmargin,$pdf->GetY());
	
	$pdf->SetY($topmargin+.43+.1);
	
	while($clientrecord=mysql_fetch_array($clientquery)) {
		$querystatement="SELECT invoices.id FROM invoices INNER JOIN clients ON invoices.clientid=clients.id WHERE clients.id=".$clientrecord["id"];
		$invoicequery=mysql_query($querystatement,$dblink);
		if(!$invoicequery) reportError(100,"Invoice query could not be executed");
		
		$notewhereclause="( notes.attachedtabledefid=2 AND notes.attachedid=".$clientrecord["id"].")"; 
		if(mysql_num_rows($invoicequery)){
			$notewhereclause.="OR (notes.attachedtabledefid=3 AND (";
			while($invoicerecord=mysql_fetch_array($invoicequery))
				$notewhereclause.="notes.attachedid=".$invoicerecord["id"]." OR ";
			$notewhereclause=substr($notewhereclause,0,strlen($notewhereclause)-4)."))";
		}			
		$pdf->SetLineWidth(.01);		
		$pdf->SetFont("Arial","B",12);
		$pdf->Cell($tempwidth,.18,$clientrecord["thename"],$border_debug,1,"L");
		$pdf->SetLineWidth(.02);		
		$pdf->Line($leftmargin,$pdf->GetY(),$paperwidth-$rightmargin,$pdf->GetY());

		$pdf->SetY($pdf->GetY()+.05);		
		$pdf->SetLineWidth(.01);		

		$querystatement="SELECT users.firstname, users.lastname, notes.id, notes.creationdate,
							notes.modifieddate, users2.firstname as mfirstname ,users2.lastname as mlastname,
							notes.attachedtabledefid,notes.attachedid , notes.subject, notes.content 
							FROM (notes INNER JOIN users on notes.createdby=users.id) LEFT JOIN users as users2 on notes.modifiedby=users2.id
							WHERE ".$notewhereclause." ORDER BY  notes.modifieddate DESC";
		$notequery=mysql_query($querystatement,$dblink);
		if(!$notequery) reportError(100,"Note query could not be executed.".$querystatement);
		while($therecord=mysql_fetch_array($notequery)) {
			$pdf->SetFont("Arial","B",10);
			$pdf->SetX($leftmargin+.125,$pdf->GetY()+.04);
			$pdf->Cell($tempwidth-.375,.19,$therecord["subject"],$border_debug,1,"L");

			$pdf->SetFont("Arial","",9);
			$pdf->SetX($leftmargin+.125);
			$pdf->Cell($tempwidth-.375,.17,"ID: ".$therecord["id"],$border_debug,1,"L");
	
			$pdf->SetX($leftmargin+.125);
			$pdf->Cell($tempwidth-.375,.17,"Created: ".$therecord["firstname"]." ".$therecord["lastname"]." ".formatFromSQLDatetime($therecord["creationdate"]),$border_debug,1,"L");
			
			$pdf->SetX($leftmargin+.125);
			$pdf->Cell($tempwidth-.375,.17,"Modified: ".$therecord["mfirstname"]." ".$therecord["mlastname"]." ".formatFromSQLDatetime($therecord["modifieddate"]),$border_debug,1,"L");
			
			if($therecord["attachedtabledefid"]==3)	{
				$pdf->SetX($leftmargin+.125);
				$pdf->Cell($tempwidth-.375,.17,"Attached to Invoice: ".$therecord["attachedid"],$border_debug,1,"L");
			}
	
			$pdf->SetX($leftmargin+.125);
			$pdf->SetFont("Arial","",8);
			$pdf->MultiCell($tempwidth-.375,.14,$therecord["content"],1,1,"L");
			$pdf->SetY($pdf->GetY()+.25);
		}// end fetch_array while loop
	}
	
	$pdf->Output();
	exit();
?>