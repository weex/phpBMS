<?PHP
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
	if(!$thequery) die("No records, or invlaid SQL statement:<BR>".$querystatement);
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

	if($border_debug==1){
		$pdf->Output();
	}
	else {
		//write the frickin thing! Need to write to a temp file and then you know...
		chdir("../../../report");
		$file=basename(tempnam(getcwd(),'tmp'));
		rename($file,$file.'.pdf');
		$file.='.pdf';
	
		// write to file and then output
		$pdf->Output($file);
		echo "<HTML><SCRIPT>document.location='../../../report/".$file."';</SCRIPT></HTML>";
	}
?>