<?PHP
	if($_SESSION["printing"]["sortorder"])
		$sortorder=$_SESSION["printing"]["sortorder"];
	else
		$sortorder="  ORDER BY invoices.orderdate";

	$maxrows=10;
	$maxcolumns=3;
	$topstart=1/2;
	$leftstart=3/16;
	$columnmargin=1/8;
	$labelheight=1;
	$labelwidth=2+(5/8);
	
	$reportquerystatement="select clients.firstname,clients.lastname,clients.company,invoices.address1,invoices.address2,invoices.city,invoices.state,invoices.postalcode,invoices.country 
						FROM invoices INNER JOIN clients on invoices.clientid=clients.id ";
						
	$border_debug=0;

	function printLabel($pdf,$therecord,$thex,$they,$border_debug){
		//offset lef tby 1/8" and top by 1/16th
		$pdf->SetXY($thex+(1/8),$they+1/16);
		$pdf->SetFont("Arial","B",9);
		if($therecord["lastname"]){
			$thename=$therecord["lastname"].", ".$therecord["firstname"];
			if($therecord["company"]) $thename.="\n".$therecord["company"];
		} else {
			$thename=$therecord["company"];		
		}
		$pdf->MultiCell(2.25,.135,$thename,$border_debug,2,"L");
		$pdf->SetFont("Arial","",8);
		$pdf->SetX($thex+(1/8));
		$pdf->Cell(2.25,.12,$therecord["address1"],$border_debug,2,"L");
		if($therecord["address2"]) $pdf->Cell(2.25,.12,$therecord["address2"],$border_debug,2,"L");
		$pdf->Cell(2.25,.12,$therecord["city"].", ".$therecord["state"]." ".$therecord["postalcode"],$border_debug,2,"L");
		if($therecord["country"]) $pdf->Cell(2.25,.12,$therecord["country"],$border_debug,2,"L");
		
		return $pdf;
	}

	require_once("../../../include/session.php");
	require_once("../../../fpdf/fpdf.php");
	require("../../../report/general_labels.php");
?>