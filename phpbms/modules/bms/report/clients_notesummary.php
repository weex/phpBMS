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
	$querystatement="
		SELECT
			if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) AS thename,
			clients.id,
			`clients`.`uuid`
		FROM
			`clients` ".$_SESSION["printing"]["whereclause"].$sortorder;
	$clientquery=$db->query($querystatement);
	if(!$clientquery) $error = new appError(100,"Client Query Could not be executed");

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

	while($clientrecord=$db->fetchArray($clientquery)) {
		$querystatement = "
			SELECT
				`invoices`.`id`,
				`invoices`.`uuid`
			FROM
				`invoices` INNER JOIN `clients` ON `invoices`.`clientid`=`clients`.`uuid` WHERE `clients`.`id`='".$clientrecord["id"]."'";
		$invoicequery=$db->query($querystatement);
		if(!$invoicequery) $error = new appError(100,"Invoice query could not be executed");

		$notewhereclause="( notes.attachedtabledefid='tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083' AND notes.attachedid='".$clientrecord["uuid"]."')";
		if($db->numRows($invoicequery)){
			$notewhereclause.="OR (notes.attachedtabledefid='tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883' AND (";
			while($invoicerecord=$db->fetchArray($invoicequery))
				$notewhereclause.="notes.attachedid='".$invoicerecord["uuid"]."' OR ";
			$notewhereclause=substr($notewhereclause,0,strlen($notewhereclause)-4)."))";
		}
		$pdf->SetLineWidth(.01);
		$pdf->SetFont("Arial","B",12);
		$pdf->Cell($tempwidth,.18,$clientrecord["thename"],$border_debug,1,"L");
		$pdf->SetLineWidth(.02);
		$pdf->Line($leftmargin,$pdf->GetY(),$paperwidth-$rightmargin,$pdf->GetY());

		$pdf->SetY($pdf->GetY()+.05);
		$pdf->SetLineWidth(.01);

		$querystatement = "
			SELECT
				`users`.`firstname`,
				`users`.`lastname`,
				`notes`.`id`,
				`notes`.`creationdate`,
				`notes`.`modifieddate`,
				`users2`.`firstname` AS `mfirstname`,
				`users2`.`lastname` AS `mlastname`,
				`notes`.`attachedtabledefid`,
				`notes`.`attachedid`,
				`notes`.`subject`,
				`notes`.`content`
			FROM
				(`notes` INNER JOIN `users` ON `notes`.`createdby`=`users`.`id`) LEFT JOIN `users` AS `users2` ON `notes`.`modifiedby`=`users2`.`id`
			WHERE
				".$notewhereclause." ORDER BY  notes.modifieddate DESC";

		$notequery=$db->query($querystatement);
		if(!$notequery) $error = new appError(100,"Note query could not be executed.".$querystatement);
		while($therecord=$db->fetchArray($notequery)) {
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

			if($therecord["attachedtabledefid"]=='tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883')	{
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